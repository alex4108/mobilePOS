<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

//print_r("EXPIRE TIME: " . $_SESSION['expire'] . "<br />");
//print_r("CURRENT TIME: " . time() . "<br />");
if (time() <  $_SESSION['expire']) {
	//print("Session Valid.  New Session Expire Time: " . (time()+120) . "<br />");
}
else {
	//print("Session Expired.");
}
//die();


if ($_SESSION['it_user'] == null) { // No Session 
	header("Location: http://localhost/itrap/login.php");
	die();
}
if ($_SESSION['expire'] < time()) { // Expired session
	header("Location: http://localhost/itrap/login.php?action=expire");
	die();
}

if ($_SESSION['expire'] > time()) { // Valid session
	$_SESSION['expire'] = (time()+120);
}
//print_r("Session expires at " . date("h:i:s", $_SESSION['expire']));

