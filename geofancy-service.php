<?php
	/**
	*	Author: Jonathan Starck
	*	Date: 2015-03-13
	*	Copyright: 
	*	Desc:
	*	This Script conects my Geofency-App on iOS (Geofancy) with my CCU2 at home,
	*	because presence management over WLAN is on iOS not realy a option.
	*	This Script depense on a Idea and Script "Geofancy2Homematic" from ----------
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
		
		/**
		* Construct the class variables
		*/
		function __construct($config){
			$this->setVars($config);
			$this->setVars($_POST);
			$this->setVars($_GET);
			$this->datetime = date('Y-m-d H:i:s');
			
			if (isset($this->device)) {
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
				echo "Device not allowed";
				exit();
			}
		}
		protected function actions(){
			$urlXmlApi = $this->url2ccu.'config/xmlapi/';
			switch($this->trigger){
				case "enter":
					foreach ($this->actionsArrive as $key => $value) {
						if ($key == $this->device){
							echo $urlXmlApi.$this->$value;
							$this->cURL($urlXmlApi.$this->$value);
						}
					}
					break;
				case "exit":
					foreach ($this->actionsLeave as $key => $value) {
						if ($key == $this->device){
							echo $urlXmlApi.$this->$value;
							$this->cURL($urlXmlApi.$this->$value);
						}
					}
					break;
				case "test":
					echo "Test work!";
					break;
			}
		}
		
		protected function cURL($url){
			$ch = curl_init($url);
			curl_setopt_array($ch, $this->curlOptions);
			if(curl_exec($ch) === false){
				echo 'Curl-Fehler: ' . curl_error($ch);
				exit();
			}else{
				curl_exec($ch);
				echo "Tasks performed!";
				return true;
			}
			curl_close($ch);
		}
	}
	$config = require 'config.php';
	
	$geo2ccu = new geo2ccu($config);
?>
