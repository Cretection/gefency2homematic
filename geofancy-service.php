<?php
	/**
	*	Author: Jonathan Starck
	*	Date: 2015-03-13
	*	Copyright:
	*	Desc:
	*	This Script conects my Geofency-App on iOS (Geofancy) with my CCU2 at home,
	*	because presence management over WLAN is on iOS not realy a option.
	*	This Script depense on a Idea and Script "Geofency2Homematic" from philipp_cgn on 
	*	https://bitbucket.org/philipp_cgn/geofency2homematic/wiki/Home
	*	But this does not work for me because I'm using SSL + HTTP-Auth with Digest
	**/
	
	/**
	* Personal Geofency Class
	*/
	class geo2ccu {
		protected $datetime		= NULL;		// Date + Time to log it in SQLite
		protected $device		= NULL;		// iPhone 6 Example: FEAC6F55-610C-492A-83CB-379C8D156A6E
		protected $latitude		= NULL;		// Latitude Example: 49.6361898452347
		protected $longitude	= NULL;		// Longitude Example: 8.36146609325408
		protected $timestamp	= NULL;		// Timestamp Example: 1425391924.027712
		protected $trigger		= NULL;		// Trigger: exit / enter / test
		protected $database		= NULL;
		
		/**
		* Construct the class variables
		*/
		function __construct($config){
			$this->setVars($config);
			$this->setVars($_POST);
			$this->setVars($_GET);
			
			if (isset($this->device)) {
				$this->datetime = date('Y-m-d H:i:s');
				$this->database = new PDO("sqlite:$this->dbFile");
				$this->createDatabase();
				$this->showId();
				$this->verifyDevice($this->device);
			} else {
				echo "No parameters available!";
				exit();
			}
		}
		/**
		* Transform Array to variables
		**/
		protected function setVars($param){
			if (!empty($param)) {
				foreach($param as $key => $value) {
					$this->$key = $value;
				}
			}
		}
		protected function showId() {
			if ($this->showDeviceIDonly) {
				$this->updateDatabase("Show ID only");
				echo "Your DeviceID: " . $this->deviceId;
				exit();
			}
		}
		/**
		* Verify allowed devices
		**/
		protected function verifyDevice($device){
			if (in_array($this->device, $this->devices)) {
				$this->actions();
			} else {
				$this->updateDatabase("Device not allowed");
				echo "Device not allowed";
				exit();
			}
		}
		protected function actions(){
			$urlXmlApi = $this->url2ccu.'config/xmlapi/';
			switch($this->trigger){
				case "enter":
					$this->updateDatabase(NULL);
					foreach ($this->actionsArrive as $key => $value) {
						if ($key == $this->device){
							echo $urlXmlApi.$value;
							$this->cURL($urlXmlApi.$value);
						}
					}
					break;
				case "exit":
					$this->updateDatabase(NULL);
					foreach ($this->actionsLeave as $key => $value) {
						if ($key == $this->device){
							echo $urlXmlApi.$value;
							$this->cURL($urlXmlApi.$value);
						}
					}
					break;
				case "test":
					$this->updateDatabase(NULL);
					echo "Test work!";
					break;
				default:
					$this->updateDatabase("Trigger forbidden");
					echo "Trigger forbidden!";
					exit();
					break;
			}
		}
		/**
		* Send change Request over cURL
		**/
		protected function cURL($url){
			$ch = curl_init($url);
			curl_setopt_array($ch, $this->curlOptions);
			if(curl_exec($ch) === false){
				$this->updateDatabase(curl_error($ch));
				echo 'Curl-Fehler: ' . curl_error($ch);
				exit();
			}else{
				curl_exec($ch);
				return true;
			}
			curl_close($ch);
		}
		/**
		*
		**/
		protected function createDatabase() {
			$this->database->exec("CREATE TABLE IF NOT EXISTS log ('index'	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 'datetime' TEXT NOT NULL,'device' TEXT NOT NULL,'latitude' TEXT, 'longitude'	TEXT, 'timestamp' TEXT, 'trigger'	TEXT,'error' TEXT);");
		}
		/**
		*
		**/
		protected function updateDatabase($error) {
			$sql = ('INSERT INTO log (datetime, device, latitude, longitude, timestamp, trigger, error) VALUES (:datetime, :device, :latitude, :longitude, :timestamp, :trigger, :error)');
			$q = $this->database->prepare($sql);
			$a = array (':datetime'=>$this->datetime,
		                ':device'=>$this->device,
		                ':latitude'=>$this->latitude,
		                ':longitude'=>$this->longitude,
		                ':timestamp'=>$this->timestamp,
		                ':trigger'=>$this->trigger,
		                ':error'=>$error);
			if ($q->execute($a)) {
				return true;
		    }else{
			    echo "Database Error!";
			    exit();
		    }
		}
	}
	
	$config = require 'config.php';
	
	$geo2ccu = new geo2ccu($config);
?>