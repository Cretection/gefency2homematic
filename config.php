<?php
	return array(
		// Set the link to your homematic ccu1 or ccu2. Example: http://homematic-ccu2/ 
		// or user the IP Address http://192.168.0.22/ (with slash at the end)
		url2ccu => "http://192.168.1.2",
		
		// Show DeviceID only
		// If you don't know your DeviceID - Default: false
		showDeviceIDonly => false,
		
		// Array with Device IDs. 
		// Ids seperated by comma ("ID1", "ID2", "ID3")
		devices => array("ID1", "ID2"),
		
		// Name of database file, please place the file outside the www-root folder but reachable for the webserver.
		// Use a long filename ex: nzzXTzSbUezCXkE5dTE2.sqlite
		dbFile => "nzzXTzSbUezCXkE5dTE2.sqlite",
		
		// Actions at arrive
		actionsArrive => array(
			'ID1' => 'statechange.cgi?ise_id=950&new_value=1',
		),
		
		// Actions at leave
		actionsLeave => array(
			'ID1' => 'statechange.cgi?ise_id=950&new_value=0',
		),
		
		// Customize cURL
		// Customize the cURL Options. This is my example.
		curlOptions => array(
			// When SSL is used:
			//CURLOPT_SSLVERSION => 3,
			//CURLOPT_CAINFO => 'pathto/server.pem',
			//CURLOPT_SSL_VERIFYHOST => false,
			//CURLOPT_SSL_VERIFYPEER => false,
			// When HTTP-Auth is used
			//CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
			//CURLOPT_USERPWD => 'Username:Passwort',
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_RETURNTRANSFER => true,
		),
	);
?>