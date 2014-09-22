<?php
/* 
*** TODO ***
Main links don't work! WTF?
*/
/* *** CONFIGURATION ***
Please modify the config.php file!
* DO NOT TOUCH THIS FILE! *
*/

// Required functions

global $settings;
require('config.php');
$settings = loadSettings();

// Basic security
if ($_SERVER['REMOTE_ADDR'] != "localhost" && $_SERVER['REMOTE_ADDR'] != "127.0.0.1") {
	//echo("hacking attempt");
	//writeToLog(2, "Hacking attempt! Unauthorized access from: " . $_SERVER['REMOTE_ADDR'] . " | Host: " . $_SERVER['REMOTE_HOST'] . "");
	//die();
	//echo("hallo!");
}


/* Settings:
$settings['logNumber'] | Type: String | Desc: Phone number to send log information to (if biteSMS == true)
$settings['biteSMS'] | Type: boolean | Desc: determines if biteSMS is installed
$settings['creditTrigger'] | Type: int | Desc: Trigger point for credits
$settings['creditAmt'] | Type: int | Desc: amount of credits to give when trigger is reached
$settings['textRedemption'] | Type: boolean | Desc: Send text message on redemption log event
$settings['textTransaction'] | Type: boolean | Desc: Send text message on transaction log event
$settings['textMessage'] | Type: boolean | Desc: Send text message on message log event
$settings['textMinor'] | Type: boolean | Desc: Send text message on minor log event
$settings['textMajor'] | Type: boolean | Desc: Send text message on major log event
*/
if ($_POST['action'] == "addCust") {
	
	// Works 100%
	
	$mysqli = mysqlInit();
	
	$addCust['id'] = mysqli_real_escape_string($mysqli, $_POST['customer_id']);
	$addCust['name'] = mysqli_real_escape_string($mysqli, $_POST['customer_name']);
	$addCust['phone'] = mysqli_real_escape_string($mysqli, $_POST['customer_phone']);
	$addCust['notes'] = mysqli_real_escape_string($mysqli, $_POST['customer_notes']);
	$addCust['notifications'] = mysqli_real_escape_string($mysqli, $_POST['customer_notifications']);
	$addCust['boolean'] = false;
	
	// Check existing user
	if ($addCust['id'] !== "") {
		$query = "SELECT * FROM customers WHERE userid='" . $addCust['id'] . "'";
		if (!$result = mysqli_query($mysqli, $query)) {
			die("Query Error (" . $query . "): " . mysqli_error($mysqli));
		}
		
		

		// Check for rows = 0
		if (mysqli_num_rows($result) != 0) {
			$row = mysqli_fetch_assoc($result);
			$addCust['boolean'] = false;
			$addCust['result'] = '<font color="red">Username already exists!</font>
			<br />Check for duplicate user!<br />
			Username: ' . $row['userid'] . '<br />
			Phone: ' . $row['phone'] . '<br />
			';
		}
		else {
					
			if ($addCust['notifications'] != "1")
				$addCust['notifications'] = "0";

			$query = "INSERT INTO customers (userid, name, phone, notifications, balance, credit, notes) VALUES (
			'".$addCust['id']."', 
			'".$addCust['name']."', 
			'".$addCust['phone']."', 
			'".$addCust['notifications']."', 
			0.00, 
			0.00, 
			'".$addCust['notes']."')
			";
			if (!mysqli_query($mysqli, $query)) {
				die("Query Error (" . $query . "): " . mysqli_error($mysqli));
			}
			else {
				writeToLog(0, "Customer added to database", $addCust['id']);
				$addCust['result'] = '
				<font color="red">Customer added!</font>
				<br />
				';
				$addCust['boolean'] = true;
			}
		}
	}
	else {
	// No username, just go ahead and add them
	$query = "INSERT INTO customers (userid, name, phone, notifications, balance, credit, notes) VALUES (
		'".$addCust['id']."', 
		'".$addCust['name']."', 
		'".$addCust['phone']."', 
		'".$addCust['notifications']."', 
		0.00, 
		0.00, 
		'".$addCust['notes']."')
		";
		if (!mysqli_query($mysqli, $query)) {
			die("Query Error (" . $query . "): " . mysqli_error($mysqli));
		}
		else {
			writeToLog(0, "Customer added to database (" . $addCust['name'] ."", $addCust['id']);
			$addCust['result'] = '
			<font color="red">Customer added!</font>
			<br />
			';
			$addCust['boolean'] = true;
		}
	}
}
elseif ($_POST['action'] == "lookupCust") {
	// Works 100%
	$mysqli = mysqlInit();
	// Search based on given values
	
	$lookupCust['userid'] = mysqli_real_escape_string($mysqli, $_POST['userid']);
	$lookupCust['name'] = mysqli_real_escape_string($mysqli, $_POST['name']);
	$lookupCust['phone'] =  mysqli_real_escape_string($mysqli, $_POST['phone']);
	$lookupCust['balance'] =  mysqli_real_escape_string($mysqli, $_POST['balance']);
	$lookupCust['credit'] =  mysqli_real_escape_string($mysqli, $_POST['credit']);
	$lookupCust['notes'] = mysqli_real_escape_string($mysqli, $_POST['notes']);
	
	$search = Array('userid', 'name', 'phone', 'balance', 'credit', 'notes');

	$where = "";

	foreach ($search as $col)
	{
		if ($_POST[$col] == "")
			continue;

		if ($where != "")
			$where .= " OR ";

		$where .= $col ." LIKE '%".mysqli_real_escape_string($mysqli, trim($_POST[$col]))."%'"; //   matches anywhere
	}

	$query = "SELECT * FROM customers WHERE {$where}";
	// 	echo $query;
	//die();
    
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	// Echo result as table
	
	
	/* show tables */
	$lookupCust['result'] = "<table border=\"1\" width=\"95%\"><tr><td>Username</td><td>Name</td><td>Phone</td><td>Notifications</td><td>Balance</td><td>Credit</td><td>Notes</td><td></td><td></td></tr>";
	while($row = mysqli_fetch_assoc($result)) {
		switch($row['notifications']) {
			case "0": $row['notifications'] = '<font color="red">Disabled</font>'; break;
			case "1": $row['notifications'] = '<font color="green">Enabled</font>'; break; 
			default: $row['notifications'] = "unset (ERROR?)"; writeToLog(0, "Notifications unset on user " . $row['id'] . "", $row['name']); break;
		}
		$lookupCust['result'] .= "<tr><td>".$row['userid']."</td><td>".$row['name']."</td><td><a href=\"tel:" . $row['phone'] ."\">".$row['phone']."</a></td><td>". $row['notifications'] . "<td>$".$row['balance']."</td><td>$".$row['credit']."</td><td>".$row['notes']."</td><td><a href=\"?id=" . $row['id'] . "#_editCust\" target=\"_window\">Edit</a></td><td><a href=\"index.php?id=" . $row['id'] . "#_transac\" target=\"_window\">Transaction</a></td></tr>";
	}
	$lookupCust['result'] .="</table>";
}
elseif ($_POST['action'] == "editCust") {
	$mysqli = mysqlInit();
	
	// Take in variables
	
	$editCust['id'] = mysqli_real_escape_string($mysqli, $_POST['id']);
	$editCust['userid'] = mysqli_real_escape_string($mysqli, $_POST['userid']);
	$editCust['name'] = mysqli_real_escape_string($mysqli, $_POST['name']);
	$editCust['phone'] =  mysqli_real_escape_string($mysqli, $_POST['phone']);
	$editCust['notifications'] =  mysqli_real_escape_string($mysqli, $_POST['notifications']);
	$editCust['balance'] =  mysqli_real_escape_string($mysqli, $_POST['balance']);
	$editCust['credit'] =  mysqli_real_escape_string($mysqli, $_POST['credit']);
	$editCust['notes'] = mysqli_real_escape_string($mysqli, $_POST['notes']);
	
	if ($editCust['notifications'] != "1")
		$editCust['notifications'] = "0";
	
	// Update row
	$query = "UPDATE customers SET userid = '".$editCust['userid']."', name = '".$editCust['name']."', phone = '".$editCust['phone']."', notifications = '".$editCust['notifications']."', balance = '".$editCust['balance']."', credit = '".$editCust['credit']."', notes = '".$editCust['notes']."' WHERE id = '".$editCust['id']."' ";
	//echo $query;
	//die();
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	// Write to log
		writeToLog(0, "User Edit Complete.  New values: UserID: " . $editCust['userid'] . "| Name: " . $editCust['name'] . " | Phone: ".$editCust['phone']." | Notifications: ".$editCust['notifications']." | Balance: ".$editCust['balance']." | Credit: ".$editCust['credit']." | Notes: ". $editCust['notes'], $editCust['userid']);
				
	// Display: "Success"
	$lookupCust['result'] = "Customer successfully updated!";
}
elseif ($_POST['action'] == "viewLog") {
	$mysqli = mysqlInit();
	// Search based on given values
	$viewLog['affectedUser'] = mysqli_real_escape_string($mysqli, $_POST['affectedUser']);
	
	// affectedUser is $row['userid'] -> We need to convert it to $row['id']!
	$viewLog['affectedUser'] = nameToId($viewLog['affectedUser']);
	if ($_POST['affectedUser'] !== "") { // If affected user present
		$affectedUser = "AND affectedUser = '" . $viewLog['affectedUser'] . "' ";
		$affectedUserNA = "WHERE affectedUser = '" . $viewLog['affectedUser'] . "' ";
    }
	else { // If no affected user present
		$affectedUser = '';
		$affectedUserNA = '';
	}
	
	
	switch($_POST['priority']) {
		case "-2": $query = "SELECT * FROM log WHERE (priority = '-2') {$affectedUser} ORDER BY id DESC"; $viewLog['prioritySelected']['-2'] = true; break;
		case "-1": $query = "SELECT * FROM log WHERE (priority = '-1') {$affectedUser} ORDER BY id DESC"; $viewLog['prioritySelected']['-1'] = true; break;
		case "0": $query = "SELECT * FROM log WHERE (priority = '0') {$affectedUser} ORDER BY id DESC"; $viewLog['prioritySelected']['0'] = true; break;
		case "1": $query = "SELECT * FROM log WHERE (priority = '1') {$affectedUser} ORDER BY id DESC"; $viewLog['prioritySelected']['1'] = true; break;
		case "2": $query = "SELECT * FROM log WHERE (priority = '2') {$affectedUser} ORDER BY id DESC"; $viewLog['prioritySelected']['22'] = true; break;
		case "3": $query = "SELECT * FROM log WHERE (priority = '1' OR priority = '2') {$affectedUser} ORDER BY id DESC"; $viewLog['prioritySelected']['3'] = true; break;
		case "na": $query = "SELECT * FROM log {$affectedUserNA} ORDER BY id DESC"; $viewLog['prioritySelected']['na'] = true; break;
		default: $query = "switch on line 141 failed"; writeToLog(0, "Switch on Line 141 failed.  Invalid priority"); break;
	}

	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$viewLog['result'] = '<table border="1"><tr><td width="5%">Event ID</td><td wdith="10%">Priority</td><td width="10%">Timestamp</td><td width="50%">Message</td><td width="25%">Affected User</td></tr>';
	 
	while ($row = mysqli_fetch_assoc($result)) {
		switch ($row['priority']) {
			case "-2": $viewLog['priority'] = "Credit Redemption"; break;
			case "-1": $viewLog['priority'] = "Transaction"; break;
			case "0": $viewLog['priority'] = "Message"; break;
			case "1": $viewLog['priority'] = "Minor Threat"; break;
			case "2": $viewLog['priority'] = "Major Threat"; break;
			default: $viewLog['priority'] = "Unset"; writeToLog(0, "Unset priority on event ID " . $row['id']); break;
		}
		// Convert $row['affectedUser'] (id) to Username
		$row['affectedUser'] = idToName($row['affectedUser']);
		$viewLog['result'] .= "<tr><td>" . $row['id'] . "</td><td>" . $viewLog['priority'] . "</td><td>" . date('d-m-Y g:i a', $row['timestamp']) . "</td><td>" . $row['message'] . "</td><td>" . $row['affectedUser'] . "</td></tr>";
	}
	$viewLog['result'] .= "</table>";
	
}
elseif ($_POST['action'] == "transac") {
	$mysqli = mysqlInit();
	$transac['username'] =  mysqli_real_escape_string($mysqli, $_POST['username']);
	$transac['amount'] =  mysqli_real_escape_string($mysqli, $_POST['amount']);

	// Select user
	$query = "SELECT * FROM customers WHERE userid = '".$transac['username']."'";
	
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	if (mysqli_num_rows($result) != 1) {
		$transac['result'] = '<font color="red">Customer '.$transac['username'].' does not exist!</font><br />
		<a class="redButton" href="index.php#addCust">New Customer</a>
		';
	}
	else {
		$row = mysqli_fetch_assoc($result);
		$transac['boolean'] = false;
		$transac['newBalance'] = $row['balance'] + $transac['amount'];
		$oldMultiple = floor($row['balance']/$settings['creditTrigger']); //Previous multiple of 150
		$newMultiple = floor($transac['newBalance']/$settings['creditTrigger']); //Current multiple of 150
		$difference=$newMultiple-$oldMultiple; //Are they different?

		if ($difference>0) {
			// +20 to credit for each multiple of $150 that was passed
			$transac['newCredit'] = $row['credit'] + $settings['creditAmt'] * $difference;
			$query = "UPDATE customers SET credit='" . $transac['newCredit'] . "' WHERE userid = '" . $transac['username'] . "'";
			if (!mysqli_query($mysqli, $query)) {
				die("Query Error (" . $query . "): " . mysqli_error($mysqli));
			}
		}
		else 
			$transac['newCredit'] = $row['credit'];
		
		$query = "UPDATE customers SET balance='" . $transac['newBalance'] . "' WHERE userid = '" . $transac['username'] . "'";
			if (!mysqli_query($mysqli, $query)) {
				die("Query Error (" . $query . "): " . mysqli_error($mysqli));
			}
		
		
		
		writeToLog(-1, "Transaction.  Prevous Balance: " . $row['balance'] . " | New Balance: " . $transac['newBalance'] . " | Available Credit: " . $transac['newCredit'], $transac['username']);
		// Update user's balance
		$transac['boolean'] = true;
		$transac['result'] = '<font color="red">Transaction Complete!</font><br />
		Customer: ' . $transac['username'] . '<br />
		Balance: $' . $transac['newBalance'] . '<br />
		Credit: $' . $transac['newCredit'] . '<br />
		';
		// Check user's notifications from DB
		$transac['notifications'] = $row['notifications'];
		$transac['id'] = $row['id'];
		if ($transac['notifications']) {
			$transac['nextCredit'] = 150 - ($transac['newBalance'] - (150*$newMultiple));
			//echo $transac['nextCredit'];
			//die();
			$transac['phone'] = $row['phone'];
			$msg = "Transaction Approved!\nAmount: " . $transac['amount'] . "\nCurrent Bal: " . $transac['newBalance'] . "\nAvailable Credit: " . $transac['newCredit'] . "\nNext credit in: " . $transac['nextCredit'] . "";
			sendSMS("" . $msg, $transac['phone']);
		}
	}
}
elseif ($_POST['action'] == "redeem") {
	// Works 100%
	$mysqli = mysqlInit();
	
	$redeem['username'] =  mysqli_real_escape_string($mysqli, $_POST['username']);
	$redeem['amount'] =  mysqli_real_escape_string($mysqli, $_POST['amount']);
	
	// Select user
	$query = "SELECT * FROM customers WHERE userid = '".$redeem['username']."'";
	
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	if (mysqli_num_rows($result) != 1) {
		$row = mysqli_fetch_assoc($result);
		$redeem['result'] = '<font color="red">Customer '.$redeem['username'].' does not exist!</font><br />
		<a class="redButton" href="index.php#addCust">New Customer</a>
		';
	}
	else {
		
		$row = mysqli_fetch_assoc($result);
		
		$redeem['phone'] = $row['phone'];
		$redeem['newCredit'] = $row['credit'] - $redeem['amount'];
		if ($redeem['newCredit'] >= 0) {
		
			$query = "UPDATE customers SET credit='" . $redeem['newCredit'] . "' WHERE userid = '" . $redeem['username'] . "'";
			if (!mysqli_query($mysqli, $query)) {
				die("Query Error (" . $query . "): " . mysqli_error($mysqli));
			}
			// Update user's balance
			$redeem['result'] = '<font color="red">Redemption Complete!</font><br />
			Customer: ' . $redeem['username'] . '<br />
			Available Credit: $' . $redeem['newCredit'] . '<br />
			';
			$redeem['notifications'] = $row['notifications'];
			if ($redeem['notifications']) {
				$msg = "Credit Redeemed!\nAmt Redeemed: " . $redeem['amount'] . "\nAvailable Credit: " . $redeem['newCredit'] . "";
				sendSMS("" . $msg, $redeem['phone']);
			}
			writeToLog(-2, "Credit Redeemed.  Previous: " . $row['credit'] . " | Redeemed: " . $redeem['amount'] . " | New Balance: " . $redeem['newCredit'], $redeem['username']);
			// Post transaction and modify available credit
		}
		else {
			$redeem['result'] = '<font color="red">Failure! Not enough available credit!</font><br />
			Customer: ' . $redeem['username'] . ' <br />
			Available Credit: ' . $row['credit'] . '<br />
			';
		}
		
		
		
	}
}
elseif ($_POST['action'] == "settings") {
	$mysqli = mysqlInit();
	
	// Take inputs
		
	$settingForm['logNumber'] =  mysqli_real_escape_string($mysqli, $_POST['logNumber']);
	$settingForm['biteSMS'] =  mysqli_real_escape_string($mysqli, $_POST['biteSMS']);
	$settingForm['creditTrigger'] =  mysqli_real_escape_string($mysqli, $_POST['creditTrigger']);
	$settingForm['creditAmt'] =  mysqli_real_escape_string($mysqli, $_POST['creditAmt']);
	$settingForm['textRedemption'] =  mysqli_real_escape_string($mysqli, $_POST['textRedemption']);
	$settingForm['textTransaction'] =  mysqli_real_escape_string($mysqli, $_POST['textTransaction']);
	$settingForm['textMessage'] =  mysqli_real_escape_string($mysqli, $_POST['textMessage']);
	$settingForm['textMinor'] =  mysqli_real_escape_string($mysqli, $_POST['textMinor']);
	$settingForm['textMajor'] =  mysqli_real_escape_string($mysqli, $_POST['textMajor']);

	if ($settingForm['textRedemption'] != "1") 
		$settingForm['textRedemption'] = "0";
	
	if ($settingForm['textTransaction'] != "1") 
		$settingForm['textTransaction'] = "0";
	
	if ($settingForm['textMessage'] != "1") 
		$settingForm['textMessage'] = "0";
	
	if ($settingForm['textMinor'] != "1") 
		$settingForm['textMinor'] = "0";
	
	if ($settingForm['textMajor'] != "1") 
		$settingForm['textMajor'] = "0";
	
	
	// Update each row-by-row
	
	$query = "UPDATE settings SET value = '" . $settingForm['logNumber'] . "' WHERE setting = 'logNumber'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$query = "UPDATE settings SET value = '" . $settingForm['biteSMS'] . "' WHERE setting = 'biteSMS'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$query = "UPDATE settings SET value = '" . $settingForm['creditTrigger'] . "' WHERE setting = 'creditTrigger'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$query = "UPDATE settings SET value = '" . $settingForm['creditAmt'] . "' WHERE setting = 'creditAmt'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$query = "UPDATE settings SET value = '" . $settingForm['textRedemption'] . "' WHERE setting = 'textRedemption'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$query = "UPDATE settings SET value = '" . $settingForm['textTransaction'] . "' WHERE setting = 'textTransaction'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$query = "UPDATE settings SET value = '" . $settingForm['textMessage'] . "' WHERE setting = 'textMessage'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$query = "UPDATE settings SET value = '" . $settingForm['textMinor'] . "' WHERE setting = 'textMinor'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	$query = "UPDATE settings SET value = '" . $settingForm['textMajor'] . "' WHERE setting = 'textMajor'";
	if (!$result = mysqli_query($mysqli, $query)) {
		die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	}
	
	// Disp results
	$settingForm['result'] = "Settings successfully updated!";
	// Reload settings
	$settings = null;
	global $settings;
	$settings = loadSettings();

}
?>
<!DOCTYPE html>
<html>
<head>
   <title>Home</title>
   <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
   <link rel="stylesheet" href="iui/iui.css" type="text/css" />
   <link rel="stylesheet" href="iui/t/default/default-theme.css" type="text/css"/>
   <link rel="apple-touch-icon" href="icon.png">
   <meta name="apple-mobile-web-app-capable" content="yes">
   <link rel="apple-touch-startup-image" href="startup.png">
   <script type="application/x-javascript" src="iui/iui.js"></script>
</head>

<body>
   <div class="toolbar">
      <h1 id="pageTitle"></h1>
      <a id="backButton" class="button" href="#"></a>
   </div>
   <ul id="screen1" title="Home" selected="true"> 
      <li><a href="#addCust">Add Customer</a></li>
	  <li><a href="#lookupCust">Customer Lookup</a></li>
	  <li><a href="#transac">New Transaction</a></li>
	  <li><a href="#redeem">Redeem Credit</a></li>
	  <li><a href="#viewLog">View Log</a></li>
	  <li><a href="#settings">Settings</a></li>
	  <?php if (isset($mainScreen['result'])) 
		echo '<li>' . $mainScreen['result'] .'</li>'
	  ?>
   </ul>
   
	<form id="addCust" title="Add Customer" class="panel" name="addCust" action="index.php#_addCust" method="post">
		<fieldset>
			<div class="row">
				<label>Username</label>
				<input type="text" name="customer_id" <?php if (isset($addCust['id'])) echo 'value="'.$addCust['id'] .'"' ?> placeholder="Customer's Username">
			</div>
			<div class="row">
				<label>Name</label>
				<input type="text" name="customer_name" <?php if (isset($addCust['name'])) echo 'value="'.$addCust['name'] .'"' ?> placeholder="Customer's Name">
			</div>
			<div class="row">
				<label>Phone</label>
				<input type="text" name="customer_phone" <?php if (isset($addCust['phone'])) echo 'value="'.$addCust['phone'] .'"' ?> placeholder="Customer's Phone #">
			</div>
			<div class="row">
				<label>Notifications</label>
				<input type="checkbox" name="customer_notifications" value="1" <?php if ($addCust['notifications']) echo("checked"); ?> placeholder="">
			</div>
			<div class="row">
				<label>Notes</label>
				<input type="text" name="customer_notes" <?php if (isset($addCust['notes'])) echo 'value="'.$addCust['notes'] .'"' ?> placeholder="Customer's Notes">
			</div>
			<input type="hidden" name="action" value="addCust" />
			<br />
			<?php 
			if (isset($addCust['result']))
				echo '<div class="row">
						<p align="center">' . $addCust['result'] . '</p>
					  </div>';
			?>
		</fieldset>
		
		<a class="grayButton" href="javascript:addCust.submit()">Submit</a><br />
		<?php
			if ($addCust['boolean'] == true) {
				$mysqli = mysqlInit();
				$query = "SELECT id FROM customers WHERE userid = '" . $mysqli->real_escape_string($addCust['id']) . "'";
				if (!$result = mysqli_query($mysqli, $query)) {
					die("Query Error (" . $query . "): " . mysqli_error($mysqli));
				}
				
				$row = mysqli_fetch_assoc($result);
				
					echo('<a class="redButton" href="index.php?id=' . $row['id'] . '#_transac" target="_window">New Transaction</a>');
			}
		?>	
	</form>
	
	<form id="lookupCust" title="Customer Lookup" class="panel" name="lookupCust" action="index.php#_lookupCust" method="post">
		<fieldset>
			<div class="row">
				<p align="center">To search customer database, enter any of their information and hit submit.</p>
			</div>
			<div class="row">
				<label>Username</label>
				<input type="text" name="userid" <?php if (isset($lookupCust['userid'])) echo 'value="'.$lookupCust['userid'] .'"' ?> placeholder="Customer's Username">
			</div>
			<div class="row">
				<label>Name</label>
				<input type="text" name="name" <?php if (isset($lookupCust['name'])) echo 'value="'.$lookupCust['name'] .'"' ?>  placeholder="Customer's Name">
			</div>
			<div class="row">
				<label>Phone</label>
				<input type="text" name="phone" <?php if (isset($lookupCust['phone'])) echo 'value="'.$lookupCust['phone'] .'"' ?>  placeholder="Customer's Phone #">
			</div>
			<div class="row">
				<label>Balance</label>
				<input type="text" name="balance" <?php if (isset($lookupCust['balance'])) echo 'value="'.$lookupCust['balance'] .'"' ?> placeholder="Amount customer has spent">
			</div>
			<div class="row">
				<label>Credit</label>
				<input type="text" name="credit" <?php if (isset($lookupCust['credit'])) echo 'value="'.$lookupCust['credit'] .'"' ?> placeholder="Customer's available credit">
			</div>
			<div class="row">
				<label>Notes</label>
				<input type="text" name="notes" <?php if (isset($lookupCust['notes'])) echo 'value="'.$lookupCust['notes'] .'"' ?> placeholder="Customer's Notes">
			</div>
			<input type="hidden" value="lookupCust" name="action" />
			<br />
			<?php 
			if (isset($lookupCust['result']))
				echo '<div class="row">
						<p align="center">' . $lookupCust['result'] . '</p>
					  </div>';
			?>
		</fieldset>
		<a class="grayButton" href="javascript:lookupCust.submit()">Submit</a><br />
		
	</form>

	<form id="editCust" title="Customer Edit" class="panel" name="editCust" action="index.php#_lookupCust" method="post">
		<fieldset>
			<?php
				if (isset($_GET['id'])) {
					//echo "Get ID Set!";
					//die();
					$mysqli = mysqlInit();
					
					// Select user based on ID
	
					$query = "SELECT * FROM customers WHERE id = '" . $mysqli->real_escape_string($_GET['id']) . "'";
					
					if (!$result = mysqli_query($mysqli, $query)) {
						die("Query Error (" . $query . "): " . mysqli_error($mysqli));
					}
					// Echo result as table
					
					
					/* show tables */
					while($row = mysqli_fetch_assoc($result)) {
						$editCust['id'] = $row['id'];
						$editCust['userid'] = $row['userid'];
						$editCust['name'] = $row['name'];
						$editCust['phone'] = $row['phone'];
						$editCust['notifications'] = $row['notifications'];
						$editCust['balance'] = $row['balance'];
						$editCust['credit'] = $row['credit'];
						$editCust['notes'] = $row['notes'];
						
					}
					writeToLog(0, "Loaded user edit.  Current values: UserID: " . $editCust['userid'] . "| Name: " . $editCust['name'] . " | Phone: ".$editCust['phone']." | Notifications: ".$editCust['notifications']." | Balance: ".$editCust['balance']." | Credit: ".$editCust['credit']." | Notes: ". $editCust['notes'], $editCust['userid']);
					echo('<input type="hidden" name="id" value="' . $editCust['id'] .'" />');
				}
				else {
					// DO NOT UNCOMMENT!
					//writeToLog(2, "Hacks! Edit page accessed without GET ID string!!!  IP Address: " . $_SERVER['REMOTE_ADDR'] . " | Host: " . $_SERVER['REMOTE_HOST'] . "");
					//die();
				}
			?>
			<div class="row">
				<label>Username</label>
				<input type="text" name="userid" <?php if (isset($editCust['userid'])) echo 'value="'.$editCust['userid'] .'"' ?> placeholder="Customer's Username">
			</div>
			<div class="row">
				<label>Name</label>
				<input type="text" name="name" <?php if (isset($editCust['name'])) echo 'value="'.$editCust['name'] .'"' ?>  placeholder="Customer's Name">
			</div>
			<div class="row">
				<label>Phone</label>
				<input type="text" name="phone" <?php if (isset($editCust['phone'])) echo 'value="'.$editCust['phone'] .'"' ?>  placeholder="Customer's Phone #">
			</div>
			<div class="row">
				<label>Notifications</label>
				<input type="checkbox" name="notifications" value="1" <?php if ($editCust['notifications']) echo("checked"); ?> placeholder=""> 
			</div>
			<div class="row">
				<label>Balance</label>
				<input type="text" name="balance" <?php if (isset($editCust['balance'])) echo 'value="'.$editCust['balance'] .'"' ?> placeholder="Amount customer has spent">
			</div>
			<div class="row">
				<label>Credit</label>
				<input type="text" name="credit" <?php if (isset($editCust['credit'])) echo 'value="'.$editCust['credit'] .'"' ?> placeholder="Customer's available credit">
			</div>
			<div class="row">
				<label>Notes</label>
				<input type="text" name="notes" <?php if (isset($editCust['notes'])) echo 'value="'.$editCust['notes'] .'"' ?> placeholder="Customer's Notes">
			</div>
			<input type="hidden" value="editCust" name="action" />
			<br />
			<?php 
			if (isset($editCust['result']))
				echo '<div class="row">
						<p align="center">' . $editCust['result'] . '</p>
					  </div>';
			?>
		</fieldset>
		<a class="grayButton" href="javascript:editCust.submit()">Submit</a><br />
		
	</form>
	
	<form id="transac" title="New Transaction" class="panel" name="transac" action="index.php#_transac" method="post">
		<fieldset>
			<?php
				// Check for $_GET['id']
				if (isset($_GET['id'])) {
						// If set, load username into space
						
						$mysqli = mysqlInit();
						if (!is_numeric($_GET['id']) && isset($_GET['id'])) {
							writeToLog(1, "Hacks! Non-Nfumeric GET ID String on Transaction!!!  IP Address: " . $_SERVER['REMOTE_ADDR'] . " | Host: " . $_SERVER['REMOTE_HOST'] . "");
							die();
						}
						$query = "SELECT userid FROM customers WHERE id = '" . $mysqli->real_escape_string($_GET['id']) . "'";
						if (!$result = mysqli_query($mysqli, $query)) {
							die("Query Error (" . $query . "): " . mysqli_error($mysqli));
						}
						$row = mysqli_fetch_assoc($result);
							$transacB['username'] = $row['userid'];
							$transacB['id'] = $row['id'];
							//print_r($transacB);
							//die();
						
				}
				
				// If not, oh well
			?>
			 <div class="row">
				<label>Username</label>
				<input type="text" name="username" <?php if (isset($transacB['username'])) echo('value="' . $transacB['username'] . '"'); ?> placeholder="Customer's Username">
			</div>
			<div class="row">
				<label>Amount</label>
				<input type="text" name="amount" placeholder="Amount received">
			</div>
			<input type="hidden" name="action" value="transac">
			<?php 
			if (isset($transac['result']))
				echo '<div class="row">
						<p align="center">' . $transac['result'] . '</p>
					  </div>';
			?>
		</fieldset>
		<a class="grayButton" href="javascript:transac.submit()">Submit</a><br />
		<a class="grayButton" href="index.php#lookupCust">Customer Lookup</a>
		<?php
			if ($transac['boolean'] == true && $transac['newCredit'] > 0) 
				echo('<a class="redButton" href="index.php?id=' . $transac['id'] . '#_redeem" target="_window">Redeem Credit</a>');
		?>	
	</form>
	
	<form id="redeem" title="Redeem Credit" class="panel" name="redeem" action="index.php#_redeem" method="post">
			<?php
				// Check for $_GET['id']
				if ($_GET['id'] !== "" || $_GET['id'] != null) {
						// If set, load username into space
						
						$mysqli = mysqlInit();
						if (!is_numeric($_GET['id']) && isset($_GET['id'])) {
							writeToLog(1, "Hacks! Non-Nfumeric GET ID String on Redeem!!!  IP Address: " . $_SERVER['REMOTE_ADDR'] . " | Host: " . $_SERVER['REMOTE_HOST'] . "");
							//die();
						}
						$query = "SELECT userid FROM customers WHERE id = '" . $mysqli->real_escape_string($_GET['id']) . "'";
						if (!$result = mysqli_query($mysqli, $query)) {
							die("Query Error (" . $query . "): " . mysqli_error($mysqli));
						}
						while($row = mysqli_fetch_assoc($result)) {
							$redeemB['username'] = $row['userid'];
						}
				}
				
				// If not, oh well
			?>
		<fieldset>
			<div class="row">
				<label>Username</label>
				<input type="text" name="username"  <?php if (isset($redeemB['username'])) echo('value="' . $redeemB['username'] . '"'); ?> placeholder="Customer's Username">
			</div>
			<div class="row">
				<label>Credit</label>
				<input type="text" name="amount" placeholder="Credit to spend">
			</div>
			<input type="hidden" name="action" value="redeem" />
			<?php 
			if (isset($redeem['result']))
				echo '<div class="row">
						<p align="center">' . $redeem['result'] . '</p>
					  </div>';
			?>
		</fieldset>
		<a class="grayButton" href="javascript:redeem.submit()">Submit</a><br />
		<a class="grayButton" href="index.php#lookupCust">Customer Lookup</a>
		
	</form>
	
	<form id="viewLog" title="View Log" class="panel" name="viewLog" action="index.php#_viewLog" method="post">
	<fieldset>
			<div class="row">
				<label>Affected User</label>
				<input type="text" name="affectedUser" value="<?php if(isset($_POST['affectedUser'])) echo $_POST['affectedUser']; ?>" placeholder="Customer's username">
			</div>
			<div class="row">
				<label>Priority</label>
				<select name="priority">
					<option value="na" <?php if ($viewLog['prioritySelected']['na'] == true) echo("selected=selected"); ?> >All priorities</option>
					<option value="-2" <?php if ($viewLog['prioritySelected']['-2'] == true) echo("selected=selected"); ?> >Credit Redemptions</option>
					<option value="-1" <?php if ($viewLog['prioritySelected']['-1'] == true) echo("selected=selected"); ?> >Transactions</option>
					<option value="0" <?php if ($viewLog['prioritySelected']['0'] == true) echo("selected=selected"); ?> >Message</option>
					<option value="1" <?php if ($viewLog['prioritySelected']['1'] == true) echo("selected=selected"); ?> >Minor threat</option>
					<option value="2" <?php if ($viewLog['prioritySelected']['2'] == true) echo("selected=selected"); ?> >Major threat</option>
					<option value="3" <?php if ($viewLog['prioritySelected']['3'] == true) echo("selected=selected"); ?> >All threats</option>
				</select>
			</div>
			<input type="hidden" name="action" value="viewLog" />
			<?php 
			if (isset($viewLog['result']))
				echo '<div class="row">
						<p align="center">' . $viewLog['result'] . '</p>
					  </div>';
			?>
		</fieldset>
		<a class="grayButton" href="javascript:viewLog.submit()">Submit</a>
	</form>
	
	
	
<?php
/*
 Settings:
$settings['logNumber'] | Type: String | Desc: Phone number to send log information to (if biteSMS == true)
$settings['biteSMS'] | Type: boolean | Desc: determines if biteSMS is installed
$settings['creditTrigger'] | Type: int | Desc: Trigger point for credits
$settings['creditAmt'] | Type: int | Desc: amount of credits to give when trigger is reached
$settings['textRedemption'] | Type: boolean | Desc: Send text message on redemption log event
$settings['textTransaction'] | Type: boolean | Desc: Send text message on transaction log event
$settings['textMessage'] | Type: boolean | Desc: Send text message on message log event
$settings['textMinor'] | Type: boolean | Desc: Send text message on minor log event
$settings['textMajor'] | Type: boolean | Desc: Send text message on major log event
*/
?>
	<form id="settings" title="Settings" class="panel" name="settings" action="index.php#_settings" method="post">
	<fieldset>
			
			<div class="row">
				<label>Trigger</label>
				<input type="text" name="creditTrigger" value="<?php echo $settings['creditTrigger']; ?>" placeholder="Balance to trigger credits at">
			</div>
			<div class="row">
				<label>Amt</label>
				<input type="text" name="creditAmt" value="<?php echo $settings['creditAmt']; ?>" placeholder="Amount of credits to issue when trigger is hit">
			</div>
						
			<div class="row">
				<label>Log Phone</label>
				<input type="text" name="logNumber" value="<?php echo $settings['logNumber']; ?>" placeholder="Phone number to send logging alerts to">
			</div>
			<div class="row">
				<label>biteSMS installed?</label>
				<input type="checkbox" name="biteSMS" value="1" <?php if ($settings['biteSMS']) echo("checked"); ?>> <br>
			</div>

			
			
			<div class="row">
				<label>Redemption notifications</label>
				<input type="checkbox" name="textRedemption" value="1" <?php if ($settings['textRedemption']) echo("checked"); ?>> <br>
			</div>

			
			<div class="row">
				<label>Transaction notifications</label>
				<input type="checkbox" name="textTransaction" value="1" <?php if ($settings['textTransaction']) echo("checked"); ?>> <br>
			</div>

			
			<div class="row">
				<label>Message notifications</label>
				<input type="checkbox" name="textMessage" value="1" <?php if ($settings['textMessage']) echo("checked"); ?>> <br>
			</div>

			
			<div class="row">
				<label>Minor Threat notifications</label>
				<input type="checkbox" name="textMinor" value="1" <?php if ($settings['textMinor']) echo("checked"); ?>> <br>
			</div>

			
			<div class="row">
				<label>Major Threat notifications</label>
				<input type="checkbox" name="textMajor" value="1" <?php if ($settings['textMajor']) echo("checked"); ?>> <br>
			</div>

			
			<input type="hidden" name="action" value="settings" />
			<?php 
			if (isset($settingForm['result']))
				echo '<div class="row">
						<p align="center">' . $settingForm['result'] . '</p>
					  </div>';
			?>
		</fieldset>
		<a class="grayButton" href="javascript:settings.submit()">Submit</a>
	</form>
	
</body>
</html>