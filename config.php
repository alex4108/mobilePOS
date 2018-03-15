<?php

$_HOST = "http://localhost/itrap";

if ($_SERVER['REMOTE_ADDR'] != "localhost" && $_SERVER['REMOTE_ADDR'] != "127.0.0.1") {
	//echo("hacking attempt");
	//writeToLog(2, "Hacking attempt! Unauthorized access from: " . $_SERVER['REMOTE_ADDR'] . " | Host: " . $_SERVER['REMOTE_HOST'] . "");
	//die();
}
function mysqlInit() { // MySQL connection
	
// Modify these for database connection
		$db['location'] = "localhost";
		$db['user'] = "root";
		$db['pass'] = "";
		$db['database'] = "itrap";
/* DO NOT TOUCH ANYTHING PAST THIS LINE OR YOU WILL BREAK THE SCRIPT! */
	
	$mysqli = new mysqli($db['location'], $db['user'], $db['pass'], $db['database']);
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		//exit();
	}
	else
		return $mysqli;

	$mysqli = null;
}
function loadSettings() { // Script settings
	
	// Load website settings
	$mysqli = mysqlInit();
	$query = "SELECT * FROM settings";

	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}

	while ($row = mysqli_fetch_assoc($result)) {
		$settings[$row['setting']] = $row['value'];
		//echo("Setting: " . $row['setting'] . " | Value: " . $row['value'] . " | Variable: settings[" . $row['setting'] . "] = " . $settings[$row['setting']] . "<br />");
	}
	//die();
	return $settings;
}
function sendSMS($msg, $number = null) { // Send SMS (only for ($settings['biteSMS']))
	//global $settings;
	//if ($number == null) 
		//$number = $settings['logNumber'];
		
	//$exec = 'sudo /Applications/biteSMS.app/biteSMS -send -carrier "' . $number . '" "' . $msg . ' "';
	//echo $exec;
	//exec($exec);
	//writeToLog(0, "Sent SMS to " . $number . " | " . $msg . "");
	
}
/*
	Write To Log
	
	@param string flag Type of log (cash, inventory, transaction, data mod, sign in, sign out)
	@param string data Array of data consistent with flag
	
	@return boolean True on success, False on failure
	
*/
function writeToLog($flag, $data) {
	require("sessionCheck.php");
	global $settings;
	$mysqli = mysqlInit();
	
	$data = serialize($data);
	
	$query = "INSERT INTO log (flag, data, timestamp, admin) VALUES ('" . $flag . "', '" . $data . "', '" . time() ."', '" . $_SESSION['it_user'] . "')";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	return true;
}
/*
	Get Log	
	
	@param string flag
	@param string startStamp
	@param string endStamp
	
	@return array Log data
*/
function getLog($flag, $startStamp, $endStamp) {
	global $settings;
	$mysqli = mysqlInit();
	$query = $mysqli->query("SELECT id,flag,data FROM log WHERE flag = '".$flag."' AND timestamp >= ".$startStamp." AND timestamp <= ".$endStamp." LIMIT 10000 ");
	$logData = array();
	while($row = mysqli_fetch_assoc($query)) {
		$logData[$row['id']] = unserialize($row['data']);
	}
	//print_r($logData);
	//die();
	return $logData;
}
function nameToID($name) {
	$mysqli = mysqlInit();
	
	$query = "SELECT id FROM customers WHERE userid = '" . $mysqli->real_escape_string($name) . "'";
	if (!$result = mysqli_query($mysqli, $query)) {
			die("Query Error (" . $query . "): " . mysqli_error($mysqli));
		}
	while ($row = mysqli_fetch_assoc($result)) {
		return $row['id'];
	}
	
}

function idToName($id) {
	$mysqli = mysqlInit();
		
		$query = "SELECT userid FROM customers WHERE id = '" . $mysqli->real_escape_string($id) . "'";
		if (!$result = mysqli_query($mysqli, $query)) {
				die("Query Error (" . $query . "): " . mysqli_error($mysqli));
			}
		while ($row = mysqli_fetch_assoc($result)) {
			return $row['userid'];
		}
		
}
?>
