<?php
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
		$db['database'] = "mobilepos";
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
	global $settings;
	if ($number == null) 
		$number = $settings['logNumber'];
		
	$exec = 'sudo /Applications/biteSMS.app/biteSMS -send -carrier "' . $number . '" "' . $msg . ' "';
	//echo $exec;
	exec($exec);
	writeToLog(0, "Sent SMS to " . $number . " | " . $msg . "");
	
}
function writeToLog($priority, $msg, $affectedUser = null) { // Log writer
	global $settings;
	$mysqli = mysqlInit();
	/* Priority levels:
	-2: Redemption
	-1: Transaction
	0: Message
	1: Minor threat
	2: Major threat
	*/
	// Send a text message on certain types of log notifications
	//echo("Settings: " . $settings['textMajor'] . " and " . $settings['biteSMS'] . "");
	switch ($priority) {
		case -2: ($settings['textRedemption'] && $settings['biteSMS']) ? sendSMS("(Redemption) " . $msg) : print(""); break;
		case -1: ($settings['textTransaction'] && $settings['biteSMS']) ? sendSMS("(Transaction) " . $msg) : print(""); break;
		case 0: ($settings['textMessage'] && $settings['biteSMS']) ? sendSMS("(Message) " . $msg) : print(""); break;
		case 1: ($settings['textMinor'] && $settings['biteSMS']) ? sendSMS("(Minor Threat) " . $msg) : print(""); break;
		case 2: ($settings['textMajor'] && $settings['biteSMS']) ? sendSMS("(Major Threat) " . $msg) : print(""); break;
	}
		
		
	if ($affectedUser == null) {
		$query = "INSERT INTO log (priority, message, timestamp) VALUES ('" . $priority . "', '" . $msg . "', '" . time() . "')";
		if (!$result = mysqli_query($mysqli, $query)) {
			die("Query Error (" . $query . "): " . mysqli_error($mysqli));
		}
	}
	else {
		$query = "INSERT INTO log (priority, message,affectedUser, timestamp) VALUES ('" . $priority . "', '" . $msg . "', '" . $affectedUser ."', '" . time() . "')";
		if (!$result = mysqli_query($mysqli, $query)) {
			die("Query Error (" . $query . "): " . mysqli_error($mysqli));
		}
	}
	
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