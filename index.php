<?php
/* *** CONFIGURATION ***
Please modify the config.php file!
* DO NOT TOUCH THIS FILE! *
*/


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


// Required functions

global $settings;

require( 'config.php' );

$settings = loadSettings();
$settings['config_location'] = '/var/www/html/config.php';
require( 'sessionCheck.php' );

/**
	Generate Log Data.  Returns an array containing timestamp, active user, and other session variables
	
	@return array Array of default log data
*/
function getLogData() {
	return array('timestamp' => time(), 'admin' => $_SESSION['it_user']);
} 

/**
	Update Inventory
	$id int product id
	$amt double amount of product to deduct
*/
function updateInventory($id, $amt) {

	$mysqli = mysqlInit();
	$result = $mysqli->query("SELECT amount FROM inventory WHERE `id`='$id'");
	$row = mysqli_fetch_assoc($result);
	$newAmt = $row['amount'] - $amt;
	if ($newAmt < 0) {
		return False;
	}
	$mysqli->query("UPDATE iTrap.inventory SET `amount`='$newAmt', `amt_sold`=`amt_sold` + '$amt' WHERE `id`='$id'");
	
	return True;
}
if (array_key_exists('action', $_GET)) {
	if ($_GET['action'] == "closeTransac" && array_key_exists('id', $_GET)) {
		$mysqli = mysqlInit();
		$query = $mysqli->query("SELECT * FROM log WHERE flag = 'transac' AND id = " . mysqli_real_escape_string($mysqli, $_GET['id']));
		$row = mysqli_fetch_assoc($query);
			$data = unserialize($row['data']);
			// Mark transac as closed
			$data['closed'] = True;		
			// Drop cash to admin
			
			$mysqli->query("UPDATE cash SET amount = amount + " . $data['amount'] . " WHERE location = '" . $data['admin'] . "'");
		
			// Log transac is closed
			$row['data'] = serialize($data);
			$query = "UPDATE log SET data = '" . $row['data'] . "' WHERE id = " . mysqli_real_escape_string($mysqli, $_GET['id']);
			$mysqli->query($query);
			header("Location: http://localhost/itrap/index.php#_transacList");
				
	}
	if ($_GET['action'] == "recvInventory") {
		$mysqli = mysqlInit();
		$parts = array();
		$recvInventory = array();
		foreach($_POST as $k => $v) {
			$parts = explode('_', $k);
			
			$recvInventory[$parts[0]][$parts[1]] = $_POST[$parts[0] . '_' . $parts[1]];	
		}
		print_r($recvInventory);
		foreach($recvInventory as $k => $v) {
			// Remove cash from store
			$mysqli->query('UPDATE cash SET amount = amount - ' . $v['cost'] . ' WHERE location = "store"');
			// Add inventory to ID	
			$mysqli->query('UPDATE inventory SET amount = amount + ' . $v['amt'] . ' WHERE id = ' . $k . '');
			// Add log
			$logData = getLogData();
			$logData['flag'] = "recv_inventory";
			$logData['inventory_id'] = $k;
			$logData['inventory_added'] = $v['amt'];
			$logData['inventory_cost'] = $v['cost'];
			writeToLog('inventory', $logData);
		}
		header('Location: http://localhost/itrap/index.php#reportInventory');
	}
}
//die();
if (array_key_exists('action', $_POST)) {

	if ($_POST['action'] == "addCust") {	
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
					$logData = getLogData();
					$logData['type'] = "customer";
					$logData['action'] = "new";
					$logData['data'] = array(
	 					'id' => $addCust['id'],
						'name' => $addCust['name'],
						'phone' => $addCust['phone'],
						'notes' => $addCust['notes'],
					);
					writeToLog('data', $logData);
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
				$logData = getLogData();
					$logData['type'] = "customer";
					$logData['action'] = "new";
					$logData['data'] = array(
	 					'id' => $addCust['id'],
						'name' => $addCust['name'],
						'phone' => $addCust['phone'],
						'notes' => $addCust['notes'],
					);
					writeToLog('data', $logData);
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
		$logData = getLogData();
				$logData['type'] = "customer";
				$logData['action'] = "edit";
				$logData['data'] = array(
					'id' => $addCust['id'],
					'name' => $addCust['name'],
					'phone' => $addCust['phone'],
					'notes' => $addCust['notes'],
				);
				writeToLog('data', $logData);		
		// Display: "Success"
		$lookupCust['result'] = "Customer successfully updated!";
	}
	elseif ($_POST['action'] == "transac") {
		$mysqli = mysqlInit();
		$transac['username'] =  mysqli_real_escape_string($mysqli, $_POST['username']);
		$transac['amount'] =  mysqli_real_escape_string($mysqli, $_POST['amount']);
		$transac['product_id'] = mysqli_real_escape_string($mysqli, $_POST['product_id']);
		$transac['product_amt'] = mysqli_real_escape_string($mysqli, $_POST['product_amt']);		
		if (array_key_exists('close_now', $_POST)) {
			$transac['close_now'] = True;
		}
		else {
			$transac['close_now'] = False;
		}
		if (array_key_exists('update_avg', $_POST)) {
			$transac['update_avg'] = mysqli_real_escape_string($mysqli, $_POST['update_avg']);
		}
		
		if (array_key_exists('point_override', $_POST) && $_POST['point_override'] != null) {
			$transac['point_override'] = mysqli_real_escape_string($mysqli, $_POST['point_override']);
		}
		else {
			$transac['point_override'] = False;
		}
		
		if (array_key_exists('give_points', $_POST)) {
			$transac['give_points'] = True;
		}
		else {
			$transac['give_points'] = False;
		}
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

		elseif (!updateInventory($transac['product_id'], $transac['product_amt'])) {
			$transac['result'] = '<font color="red">Product does not have enough inventory to complete transaction!</font>';
		}	
		else {
			$row = mysqli_fetch_assoc($result);
			if ($_SESSION['access'] >= 2 && $transac['point_override'] != False && $transac['give_points'] == True) {
				$creditEarned = $transac['point_override'];				
				$transac['newCredit'] = $row['credit'] + $transac['point_override'];
				$query = "UPDATE customers SET credit='" . $transac['newCredit'] . "' WHERE userid = '" . $transac['username'] . "'";
				if (!mysqli_query($mysqli, $query)) {
					die("Query Error (" . $query . "): " . mysqli_error($mysqli));
				}
			}
	 		elseif ($transac['give_points'] == True) {
				/**
					$transac['newBalance'] = $row['balance'] + $transac['amount'];
					$oldMultiple = floor($row['balance']/$settings['creditTrigger']); //Previous multiple of 150
					$newMultiple = floor($transac['newBalance']/$settings['creditTrigger']); //Current multiple of 150
					$difference=$newMultiple-$oldMultiple; //Are they different?
				*/
				$getProduct = $mysqli->query("SELECT points FROM inventory WHERE id = " . $transac['product_id']);
				$product = mysqli_fetch_assoc($getProduct);
				if ($product['points'] == 1) {
					
					if ($transac['product_amt'] > 6.99) {
						$creditEarned = 1;
					}

					elseif ($transac['product_amt'] > 4.99) {
						$creditEarned = 2;
					}
					elseif ($transac['product_amt'] > 2.99) {
						$creditEarned = 3;
					}
					elseif ($transac['product_amt'] > 1.99) {
						$creditEarned = 2;
					}
					elseif ($transac['product_amt'] > 0.99) {
						$creditEarned = 1;
					}
				}
				else {
					$creditEarned = 0;	
				}
				$transac['newCredit'] = $row['credit'] + $creditEarned;
				
				$query = "UPDATE customers SET credit='" . $transac['newCredit'] . "' WHERE userid = '" . $transac['username'] . "'";
				if (!mysqli_query($mysqli, $query)) {
					die("Query Error (" . $query . "): " . mysqli_error($mysqli));
				}
			}
			if ($transac['close_now']) {	
				// Update cash on user
					$user = $_SESSION['it_user'];
					$query = $mysqli->query("SELECT * FROM cash WHERE `location`='" . $user . "'");
					$row2 = mysqli_fetch_assoc($query);
					$newCash = $row2['amount'] + $transac['amount'];
					$mysqli->query("UPDATE cash SET `amount`=" . $newCash . " WHERE `location`='" . $user . "'");
			}
			
				// Check: Avg?
					if ($transac['update_avg'] == 'checked' || $_SESSION['access'] == 1) {
						$query = $mysqli->query("SELECT * FROM inventory WHERE id = '" . $transac['product_id'] . "'");		
						$row_avg = mysqli_fetch_assoc($query);
						$transac['product_oldavg'] = $row_avg['avgprice'];
						$transac['product_newavg'] = ($transac['product_oldavg'] / $row_avg['amt_sold']) + ($transac['amount'] / $transac['product_amt']); 					
						$str = "UPDATE inventory SET avgprice = " . $transac['product_newavg'] . " WHERE id = " . $transac['product_id'] . "";
						$mysqli->query($str);			
					}
			// Build log
				$logData = getLogData();
				$logData['customer'] = $transac['username'];
				$logData['amount'] = $transac['amount'];
				$logData['inventory'] = array(
					'id' => $transac['product_id'],
					'amount' => $transac['product_amt'],
					'oldavg' => $transac['product_oldavg'],
					'newavg' => $transac['product_newavg']
				);
				$logData['points'] = $creditEarned;
				$logData['closed'] = $transac['close_now'];

			writeToLog('transac', $logData);	
			$transac['result'] = '<font color="red">Transaction Complete!</font><br />
			Customer: ' . $transac['username'] . '<br />
			Credit: ' . $transac['newCredit'] . '/10<br />
			';
			// Check user's notifications from DB
			/*$transac['notifications'] = $row['notifications'];
			$transac['id'] = $row['id'];
		
			if ($transac['notifications']) {
				$transac['nextCredit'] = 150 - ($transac['newBalance'] - (150*$newMultiple));
				//echo $transac['nextCredit'];
				//die();
				$transac['phone'] = $row['phone'];
				$msg = "Transaction Approved!\nAmount: " . $transac['amount'] . "\nCurrent Bal: " . $transac['newBalance'] . "\nAvailable Credit: " . $transac['newCredit'] . "\nNext credit in: " . $transac['nextCredit'] . "";
				sendSMS("" . $msg, $transac['phone']);
			}
			*/
		}
	}
	elseif ($_POST['action'] == "redeem") {
		// Works 100%
		$mysqli = mysqlInit();
	
		$redeem['username'] =  mysqli_real_escape_string($mysqli, $_POST['username']);
		$redeem['amount'] =  mysqli_real_escape_string($mysqli, $_POST['amount']);
		$redeem['product_id'] = mysqli_real_escape_string($mysqli, $_POST['product_id']);
		$redeem['product_amt'] = mysqli_real_escape_string($mysqli, $_POST['product_amt']);
		// Select user
		$query = "SELECT * FROM customers WHERE userid = '".$redeem['username']."'";
	
		if (!$result = mysqli_query($mysqli, $query)) {
			die("Query Error (" . $query . "): " . mysqli_error($mysqli));
		}
		function withinTolerance($redeem) {
			if ( 
				($redeem['amount'] - ($redeem['product_amt'] * 10) >= 1)
				 ||
				($redeem['amount'] - ($redeem['product_amt'] * 10) <= -1)
			)
				return False;
			else
				return True;
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
				if (withinTolerance($redeem)) {
					updateInventory($redeem['product_id'], $redeem['product_amt']);
					$query = "UPDATE customers SET credit='" . $redeem['newCredit'] . "' WHERE userid = '" . $redeem['username'] . "'";
					if (!mysqli_query($mysqli, $query)) {
						die("Query Error (" . $query . "): " . mysqli_error($mysqli));
					}
				
					$redeem['result'] = '<font color="red">Redemption Complete!</font><br />
					Customer: ' . $redeem['username'] . '<br />
					Available Credit: ' . $redeem['newCredit'] . '<br />
					';
					/*$redeem['notifications'] = $row['notifications'];
					if ($redeem['notifications']) {
						$msg = "Credit Redeemed!\nAmt Redeemed: " . $redeem['amount'] . "\nAvailable Credit: " . $redeem['newCredit'] . "";
						sendSMS("" . $msg, $redeem['phone']);
					}*/
					
					$logData = getLogData();
					$logData['customer'] = $redeem['username'];
					$logData['amount'] = $redeem['amount'];
					$logData['inventory'] = array(
							'id' => $redeem['product_id'],
							'amount' => $redeem['product_amt']
					);
					writeToLog('redeem', $logData);
				}
				else {
					$redeem['result'] = '<font color="red">Failure! Product amount is outside of tolerance range!</font><br />
					Customer: ' . $redeem['username'] . ' <br />
					Available Credit: ' . $row['credit'] . '<br />';
				}
			}
			else {
				$redeem['result'] = '<font color="red">Failure! Insufficient credit!</font><br />
				Customer: ' . $redeem['username'] . ' <br />
				Available Credit: ' . $row['credit'] . '<br />
				';
			}
		
		
		
		}
	}
	elseif ($_POST['action'] == "voidTransac") {
		$mysqli = mysqlInit();
		if (array_key_exists('invReturn', $_POST))
			$void['invReturned'] = mysqli_real_escape_string($mysqli, $_POST['invReturn']);
		if (array_key_exists('cashReturn', $_POST))
			$void['cashReturned'] = mysqli_real_escape_string($mysqli, $_POST['cashReturn']);
		if (array_key_exists('cashDropped', $_POST))
			$void['cashDropped'] = mysqli_real_escape_string($mysqli, $_POST['cashDropped']);
		$void['orderID']  = mysqli_real_escape_string($mysqli, $_POST['orderID']);
		
		if (array_key_exists('invReturned', $void)) {
			switch($void['invReturned']) {
				case "checked": $void['invReturned'] = True; break;
				default: $void['invReturned'] = False; break;
			}
		}
		if (array_key_exists('cashReturned', $void)){
			switch($void['cashReturned']) {
				case "checked": $void['cashReturned'] = True; break;
				default: $void['cashReturned'] = False; break;
			}
		}
		if (array_key_exists('cashDropped', $void)) {
			switch($void['cashDropped']) {
				case "checked": $void['cashDropped'] = True; break;
				default: $void['cashDropped'] = False; break;
			}
		}	
		// Get transaction data
		$getTransaction = $mysqli->query("SELECT * FROM log WHERE id = " . $void['orderID']);
		$data = mysqli_fetch_assoc($getTransaction);
		$data = unserialize($data['data']);
		// Reverse inventory
		if ($void['invReturned']) {
			$sales = 0;
			$amt = 0;
			$allTransactions = $mysqli->query("SELECT * FROM log WHERE flag = 'transac'");
			while ($allTransData = mysqli_fetch_assoc($allTransactions)) {
				$readableData = unserialize($allTransData['data']);
				
				if ($readableData['inventory']['id'] = $data['inventory']['id']) {
					$sales += $readableData['amount'];
					$amt += $readableData['inventory']['amount'];
				}
			}
			$avgPrice = $sales / $amt;
			$mysqli->query("UPDATE inventory SET amount = amount + " . $data['inventory']['amount'] . ", amt_sold = amt_sold - " . $data['inventory']['amount'] . ", avgprice = " . $avgPrice . " WHERE id = " . $data['inventory']['id']);
		}
		// Reverse Cash
		if ($void['cashReturned']) {
			if (array_key_exists('cashDropped', $void) && $void['cashDropped']) {
				$mysqli->query("UPDATE cash SET amount = amount - " . $data['amount'] . " WHERE location = 'store'");
			}	
			else {
				$mysqli->query("UPDATE cash SET amount = amount - " . $data['amount'] . " WHERE location = '" . $data['admin'] . "'");
			}
		}
		$data['void'] = true;
		$data['void_detail'] = array(
			'timestamp' => time(),
			'admin' => $_SESSION['it_user'],
			);
		if (array_key_exists('invReturned', $void)) {
			$logData['void_detail']['invReturned'] = True;
		}
		if (array_key_exists('cashReturned', $void)){
			$logData['void_detail']['cashReturned'] = True;
		}
		if (array_key_exists('cashDropped', $void)) {
			$logData['void_detail']['cashDropped'] = True;
		}
		$logData = serialize($data);
		$mysqli->query("UPDATE log SET data = '" . $logData . "' WHERE id = " . $void['orderID']);
		
		$void['result'] = "Order voided";	
	}
	elseif ($_POST['action'] == "settings") {
		if ($_SESSION['access'] < 2) {
			die('Insufficient Access');
		}
		
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

	elseif ($_POST['action'] == "update_inventory") {
		if ($_SESSION['access'] < 2) {
			die('Insufficient Access');
		}
		$mysqli = mysqlInit();
		$products = array();
		foreach($_POST as $key => $entry) {
			if (substr($key, 0, 2) == "i_") {
				 $id = substr($key, 2, 1);
				 $part = substr($key, 4);
				 $products[$id][$part] = $entry;
			}
		}
		// Post to SQL
		foreach($products as $id => $product) {
			foreach($product as $part => $entry) {
				// Check: Does product already exist?
					// True: Update

					// False: Create
				$query = "SELECT * FROM `inventory` WHERE `id` = ".$id."";
				if (!$result = mysqli_query($mysqli, $query)) {
					die("Query Error (" . $query . "): " . mysqli_error($mysqli));
				}
				$row2 = mysqli_fetch_assoc($result);
			
				$mysqli->query("UPDATE `inventory` SET `".$part."`='".$entry."' WHERE `id`='" . $id . "'");
				//print_r($part . " " . $entry);
			
				if ($part == "amount") {
					$logData = getLogData();
					$logData['flag'] = "post_inventory";
					$logData['inventory_id'] = $id;
					$logData['inventory_old'] = $row2['amount'];
					$logData['inventory_new'] = $entry;
					writeToLog('inventory', $logData);
				}
			}
		}	
	}
	elseif ($_POST['action'] == "addProduct") {
		if ($_SESSION['access'] < 2) {
			die('Insufficient Access');
		}	
		$mysqli = mysqlInit();

		$product_name = mysqli_real_escape_string($mysqli, $_POST['product_name']);
		if (array_key_exists('product_points', $_POST)) {
			switch($_POST['product_points']) {
				case "checked": $product_points = 1; break;
				default: $product_points = 0; break;
			}
		}
		else {
			$product_points = 0;
		}
		$query = "INSERT INTO inventory (name, amount, avgprice, amt_sold, points) VALUES ('". $product_name ."', 0, 0, 0, " . $product_points  .")";
		
		$mysqli->query($query);
			
		$addProd['result'] = "Success";
		header("Location: http://localhost/itrap/index.php#inventory");
	}
	elseif ($_POST['action'] == "cashdrop") {
		if ($_SESSION['access'] < 2) {
			die('Insufficient Access');
		}	
		$mysqli = mysqlInit();

		$source = mysqli_real_escape_string($mysqli, $_POST['source']);
		$dest = mysqli_real_escape_string($mysqli, $_POST['dest']);
		$amt = mysqli_real_escape_string($mysqli, $_POST['amt']);
	
		$query = $mysqli->query("SELECT * FROM cash");
		$amounts = array();
		while ($row = mysqli_fetch_assoc($query)) {
			$amounts[$row['location']] = $row['amount'];
		}
		$amountsNew[$source] = $amounts[$source] - $amt;
		$amountsNew[$dest] = $amounts[$dest] + $amt;
		$mysqli->query("UPDATE cash SET `amount`=" . $amountsNew[$source] . " WHERE `location`='" . $source . "'");
		$mysqli->query("UPDATE cash SET `amount`=" . $amountsNew[$dest] . " WHERE `location`='" . $dest . "'");
		$m = "Drop Saved Successfully.";
		$logData = getLogData();
		$logData['type'] = "drop";
		$logData['source'] = $source;
		$logData['dest'] = $dest;
		$logData['sourceCashOrig'] = $amounts[$source];
		$logData['sourceCashNew'] = $amountsNew[$source];
		$logData['destCashOrig'] = $amounts[$dest];
		$logData['destCashNew'] = $amountsNew[$dest];
		writeToLog('cash', $logData);
	
	}
	elseif ($_POST['action'] == "cashmgmt") {
		if ($_SESSION['access'] < 2) {
			die('Insufficient Access');
		}
		$mysqli = mysqlInit();
	
		$direction = mysqli_real_escape_string($mysqli, $_POST['direction']);
		$amount = mysqli_real_escape_string($mysqli, $_POST['amount']);
		$note = mysqli_real_escape_string($mysqli, $_POST['note']);
	
		$query = $mysqli->query("SELECT * FROM cash WHERE `location`='store'");
		$row = mysqli_fetch_assoc($query);
	
		if ($direction == "in") {
			$newAmt = $amount + $row['amount'];
		}
		else {
			$newAmt = $row['amount'] - $amount;
		}
		$mysqli->query("UPDATE cash SET `amount`=" . $newAmt . " WHERE `location`='store'");
	
		$logData = getLogData();
		$logData['type'] = "mgmt";
		$logData['location'] = "store";
		$logData['direction'] = $direction;
		$logData['cashOrig'] = $row['amount'];
		$logData['cashNew'] = $newAmt;
		$logData['note'] = $note;
		writeToLog('cash', $logData);
	
	
	}
	elseif($_POST['action'] == "calcLabor") {
		$mysqli = mysqlInit();
		$calcLabor['startDate'] = mysqli_real_escape_string($mysqli, $_POST['startDate']);
		$calcLabor['endDate'] = mysqli_real_escape_string($mysqli, $_POST['endDate']);
		$calcLabor['savings'] = mysqli_real_escape_string($mysqli, $_POST['reup']);

		// explode Dates
		$startDate = explode('/', $calcLabor['startDate']);
		$endDate = explode('/', $calcLabor['endDate']);
		
		$startDate = mktime(0, 0, 0, $month = $startDate[0], $day = $startDate[1], $year = $startDate[2]);
		$endDate = mktime(0, 0, 0, $month = $endDate[0], $day = $endDate[1], $year = $endDate[2]);
		
		$total = 0.00;
		$alexTotal = 0.00;
		$pmbTotal = 0.00;

		$totalTrans = 0;
		$alexTrans = 0;
		$pmbTrans = 0;

		$transactions = $mysqli->query('SELECT * FROM log WHERE flag = "transac"');

		while ($row = mysqli_fetch_assoc($transactions)) {
			$data = unserialize($row['data']);
			if ($data['timestamp'] >= $startDate && $data['timestamp'] <= $endDate && (!array_key_exists('closed', $data) || $data['closed'])) {
			
				if ($data['admin'] == "alex") {
					$alexTotal += $data['amount'];
				
				}
				elseif ($data['admin'] == "pmb") {
					$pmbTotal += $data['amount'];
				
				}
				$total += $data['amount'];
			}
		}
		$alexPct = number_format($alexTotal / $total*100, 2, '.', '');
		$willPct = number_format($pmbTotal / $total*100, 2, '.', '');
		$calcLabor['result'] = 
		'Alex %: ' . $alexPct . '<br />
		Pmb %: ' . $willPct . ' <br />
		';		
		
		// Calculate Actual Pay - Reup
		$storeSavings = $total * .2;
		$totalSavings = $calcLabor['savings'] + $storeSavings;

		$totalSales = $total * .8;
		$calcLabor['result'] .= 
		'<br />
		Total Sales $' . number_format($total, 2, '.', ',') . '<br />
		Deduction: $' . number_format($calcLabor['savings'], 2, '.', ',') . '<br />
		Additional 20% Store Savings: $' . number_format($total*.2, 2, '.', ',') . '<br />
		<br />
		Total Sales after Savings & Deduction: $' . number_format($total -= $totalSavings, 2, '.', ',') . '<br />
		Alex Net Pay: $' . number_format($alexPct*$total/100, 2, '.', ',') . '<br />
		Pmb Net Pay: $' . number_format($willPct*$total/100, 2, '.', ',') . '
		';
	}
}

/**
	HTML PAGE PRELOAD
	Run all queries and generate all global data at initial page load, as to speed up service times.
*/
$mysqli = mysqlInit();

$cash = array();	
$query = $mysqli->query("SELECT * FROM cash");
while ($row = mysqli_fetch_assoc($query)) {
	$cash[$row['location']] = $row['amount'];
}

$invenQuery = $mysqli->query("SELECT * FROM inventory");
$products = array();
while($row = mysqli_fetch_assoc($invenQuery)) {				
	$products[$row['id']] = $row;
	$products[$row['id']]['proj_amt'] = number_format((float)doubleval($row['amount']) * doubleval($row['avgprice']), 2, '.', '');
}

$transactionQuery = $mysqli->query("SELECT * FROM log WHERE flag = 'transac' ORDER BY id DESC");
$transactions = array();
while ($row = mysqli_fetch_assoc($transactionQuery)) {
	$transactions[$row['id']] = unserialize($row['data']);
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
	  <li><a href="#posting">Posting Menu</a></li>
	  <li><a href="#reports">Reporting Menu</a></li>
	  <li><a href="#custs">Customer Management</a></li>
	  <li><a href="login.php?action=sign_out" target="_parent">Sign Out</a></li>
	  <?php if (isset($mainScreen['result'])) 
		echo '<li>' . $mainScreen['result'] .'</li>'
	  ?>
   </ul>
   
   <ul id="custs" title="Customer Management">
	<li><a href="#addCust">Add Customer</a></li>
	<li><a href="#lookupCust">Search Customers</a></li>
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
					//writeToLog(0, "Loaded user edit.  Current values: UserID: " . $editCust['userid'] . "| Name: " . $editCust['name'] . " | Phone: ".$editCust['phone']." | Notifications: ".$editCust['notifications']." | Balance: ".$editCust['balance']." | Credit: ".$editCust['credit']." | Notes: ". $editCust['notes'], $editCust['userid']);
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
<ul id="posting" title="Posting Menu">
      <li><a href="#transac">Order Entry</a></li>
	  <li><a href="#redeem">Redeem Credit</a></li>
	  <li><a href="#transacList">Browse Orders</a></li>
	  <?php
		if ($_SESSION['access'] >= 2) {
			echo('
			  <li><a href="#inventory">Post Inventory</a></li>
			  <li><a href="#recvInventory">Receive Inventory</a></li>
			  <li><a href="#cashmgmt">Cash Management</a></li>
			  <li><a href="#cashdrop">Post Cash Drop</a></li>
			  <li><a href="#addProduct">Add Product</a></li>
			');
		}
	?>
	</ul>	
	<form id="addProduct" title="Add Product" class="panel" name="addProduct" action="index.php#_inventory" method="post">
		<center>
		<div id="row">
			<label>Product Name</label>
			<input type="text" name="product_name" />
		</div>
		<div id="row">
			<label>Give Points?</label>
			<input type="checkbox" name="product_points" value="checked" checked>
		</div>
		<?php
			if (isset($addProd)) {
				if (array_key_exists('result', $addProd)) {
					echo($addProd['result']);
				}
			}
		?>
		<a class="grayButton" href="javascript:addProduct.submit()">Submit</a><br />
		<input type="hidden" name="action" value="addProduct" /> 
		</center>
	</form>
	<form id="transac" title="New Transaction" class="panel" name="transac" action="index.php#_transac" method="post">
		<fieldset>
			<?php
				
				$mysqli = mysqlInit();
				// Check for $_GET['id']
				if (isset($_GET['id'])) {
						// If set, load username into space
						
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
							//print_r($transacB);
							//die();
						
				}
				$result = $mysqli->query("SELECT id,name,amount FROM inventory");
				while ($row = mysqli_fetch_assoc($result)) {
					if (doubleval($row['amount']) > 0.00) {
						$transacProducts[$row['id']] = $row['name'];
					}				
				}
				$productOptions = "";
				foreach ($transacProducts as $product => $id) {
					$productOptions .= '<option value="' . $product . '">' . $id . '</option>';
				}
				// If not, oh well
			?>
			 <div class="row">
				<label>Username</label>
				<input type="text" name="username" <?php if (isset($transacB['username'])) echo('value="' . $transacB['username'] . '"'); ?> placeholder="Customer's Username">
			</div>
			<div class="row">
				<label>Total</label>
				<input type="text" name="amount" placeholder="Cash Received">
			</div>
			<div class="row">
				<label>Product</label>
				<select name="product_id">
					<?php echo $productOptions; ?>
			</select>
			</div>
			<div class="row">
				<label>Prod Amt</label>
				<input type="text" name="product_amt" placeholder="Amount sold">
			</div>
			<div class="row">
				<label>Give Points?</label>
				<input type="checkbox" value="checked" name="give_points" checked>
			</div>
			<div class="row">
				<label>Close now?</label>
				<input type="checkbox" value="checked" name="close_now" checked>
			</div>
			<?php
				if ($_SESSION['access'] == 2) {
					echo('
						<div class="row">
							<label>Update Product Average?</label>
							<input type="checkbox" value="checked" name="update_avg" checked>
						</div>
						<div class="row">
							<label>Pt Override</label>
							<input type="text" value="" name="point_override" checked>
						</div>
					');
				}
			?>
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
			
			if (isset($transac) && in_array('newCredit', $transac) && $transac['newCredit'] > 10) 
				echo('<a class="redButton" href="index.php?id=' . $transac['id'] . '#_redeem" target="_window">Redeem Credit</a>');
		?>	
	</form>
	
	<form id="redeem" title="Redeem Credit" class="panel" name="redeem" action="index.php#_redeem" method="post">
		<p>
			<b>Credits can only be redeemed at rate of 1 credit per .1 unit (10 credits per unit)</b>
		</p>	<?php
				// Check for $_GET['id']
				if (array_key_exists('id', $_GET) && ($_GET['id'] !== "" || $_GET['id'] != null)) {
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
			<div class="row">
				<label>Product</label>
				<select name="product_id">
					<?php echo $productOptions; ?>
			</select>
			</div>
			<div class="row">
				<label>Prod Amt</label>
				<input type="text" name="product_amt" placeholder="Amount sold">
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

	<div id="transacList" title="Browse orders">
		<table border="1">
			<tr>
				<td>Transaction ID</td>
				<td>Date / Time</td>
				<td>Customer</td>
				<td>Admin</td>
				<td>Cash Amount</td>
				<td>Product Type</td>
				<td>Product Amt</td>
				<td></td>
				<td></td>

			</tr>
			<?php
			
			
			foreach($transactions as $k => $v) {
				$query = $mysqli->query('SELECT * FROM inventory WHERE id = ' . $v['inventory']['id']);
				$row = mysqli_fetch_assoc($query);
				if ((!array_key_exists('closed', $v) || $v['closed'] == 1) && !isset($v['void'])) {
					$closed = "<b>Closed</b>";
					$void = "<a target='_self' href='index.php?id=" . $k . "#_voidTransac'>Post Void</a>";
				}
				elseif (isset($v['void'])) {
					$closed = "";
					$void = "<b>Void</b>";
				}
				else {
					$closed = "<a target='_self' href='index.php?action=closeTransac&id=" . $k . "'>Close Transaction</a>";
					$void = '';			
				}
				print_r('
				<tr>
					<td>' . $k . '</td>
					<td>' . date('Y-m-d H:i:s', $v['timestamp']) . '</td>
					<td>' . $v['customer'] . '</td>
					<td>' . $v['admin'] . '</td>
					<td>' . $v['amount'] . '</td>
					<td>' . $row['name'] . '</td>
					<td>' . $v['inventory']['amount'] . '</td>
					<td>' . $closed . '</td>
					<td>' . $void . '</td>
				</tr>
				');
			}								
		?>
		</table>

	</div>
	
	<form id="voidTransac" title="Void Transaction" class="" action="index.php#_transacList" method="POST">
		
		<div class="row">
			<label>Inventory returned to us?</label>
			<input type="checkbox" value="checked" name="invReturn" checked>
		</div>
		<div class="row">
			<label>Cash returned to customer?</label>
			<input type="checkbox" value="checked" name="cashReturn" checked>
		</div>
				<div class="row">
			<label>Cash dropped to store already?</label>
			<input type="checkbox" value="checked" name="cashDropped" checked>
		</div>
		<input type="hidden" name="orderID" value="<?php echo $_GET['id']; ?>" />
		<input type="hidden" name="action" value="voidTransac" />
			<?php 
			if (isset($void['result']))
				echo '<div class="row">
						<p align="center">' . $void['result'] . '</p>
					  </div>';
			?>
		</fieldset>
		<a class="grayButton" href="javascript:voidTransac.submit()">Post Void</a><br />
			
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


	<form id="inventory" title="Inventory" class="panel" name="inventory" action="index.php#_inventory" method="post">
		<?php 
			if ($_SESSION['access'] >= 2) {
				echo('<fieldset>
		<center>
		<table width="100%" border="1">
		<tr>
			<td width="50%">Product</td>
			<td width="50%">Amount</td>
		</tr>');

			$mysqli = mysqlInit();
			$query = $mysqli->query('SELECT * FROM inventory');
			while ($row = mysqli_fetch_assoc($query)) {
				echo('<tr><td><input type="text" name="i_' . $row["id"] . '_name" value="' . $row["name"] . '" /></td><td><input type="text" name="i_' . $row["id"] . '_amount" value="' . $row["amount"] . '" /></td></tr>');
			}
		echo('
		</table>
		</center>
		</fieldset>
		<input type="hidden" name="action" value="update_inventory" />
			
		<a class="grayButton" href="javascript:inventory.submit()">Submit</a>');
			}
		?>
		
	</form>
	
	
	<form id="cashmgmt" title="Cash Management" class="panel" name="cash" action="index.php#_cashmgmt" method="post">
		<?php 
			if ($_SESSION['access'] >= 2) {
				echo('<fieldset>
		<center>
			Store Cash: ' . $cash['store'] . ' <br />
			Alex\'s Cash: ' . $cash['alex'] . '<br />
			Pmb\'s Cash: ' . $cash['pmb'] . '<br />
			<br />
			<br />
			
			
			<table>
				<tr>
					<td>
						<input type="radio" name="direction" value="in">Cash paid in</input>
					</td>
					<td>
						<input type="radio" name="direction" value="out">Cash paid out</input>
					</td>
				</tr>
				<tr>
					<td>Amount: </td><td><input type="text" name="amount"></td>
				</tr>
				<tr>
					<td>Note: </td><td><input type="text" name="note"</td>
				</tr>
			</table>
	
		</center>
		</fieldset>
		<input type="hidden" name="action" value="cashmgmt" />
		<a class="grayButton" href="javascript:cashmgmt.submit()">Update</a>
		');
			}
		?>
		
	</form>
	<form id="cashdrop" title="Cash Drop" class="panel" name="cashdrop" action="index.php#_cashdrop" method="post">
		<?php 
			if ($_SESSION['access'] >= 2) {
		echo('<fieldset>
		<center>
		');
			if (isset($m)) {
				echo('<font color="red"><b>' . $m . '</b></font>');
			}
			$cashOptions = "";
			foreach($cash as $k => $v) {	
				$cashOptions .= '<option value="' . $k . '">' . $k . ' ( $' . $v . ' )</option>';
			}
		echo('
		<table>
		<tr>
			<td>Source:</td> 
			<td><select name="source">
			' . $cashOptions . '
			</select></td>
		</tr>
		<tr>
		<td>Destination:</td>
			
			<td><select name="dest">
			' . $cashOptions . '
			</select></td>
		</tr>
		<tr>
			<td>Amount:</td> <td><input type="text" name="amt" /></td>
		</tr>
		</table>
		</center>
		</fieldset>
		<input type="hidden" name="action" value="cashdrop" />
		<a class="grayButton" href="javascript:cashdrop.submit()">Post Drop</a>
		');
	}
?>
		
		
	</form>
	<form id="recvInventory" class="page" title="Receive Inventory" action="index.php?action=recvInventory" method="post">
		<?php 
			if ($_SESSION['access'] >= 2) {
				echo('

				<table border="1">
					<tr>
					<td>Product ID</td>
					<td>Product Name</td>
					<td>Amount Received</td>
					<td>Cost</td>
					</tr>
				');
				foreach($products as $k => $v) {
					echo("<tr>");				
					echo("<td>". $k . "</td>");
					echo("<td>". $v['name'] . "</td>");
					echo("<td><input type='text' name='".$k."_amt' /></td>");
					echo("<td><input type='text' name='".$k."_cost' /></td>");
					echo("</tr>");
				}
				echo('</table>');
				echo('<a class="redButton" href="javascript:recvInventory.submit()">Post Shipment</a>');
			}
		?>		
	</form>

	
	 <ul id="reports" class="page" title="Reporting"> 
     
	  <li><a href="#reportKey">Key Indicator</a></li>
	  <li><a href="#reportInventory">Inventory Summary Report</a></li>
          <li><a href="#laborCalc">Labor Cost Calculation</a></li>
	 
   </ul>

	<?php
		
	?>
   <div id="reportInventory">
	<table border="1">
		<tr>
			<td>Item #</td>
			<td>Product Name</td>
			<td>Product Amount</td>
			<td>Avg. Price</td>
			<td>Amount Sold</td>
			<td>Proj. Amt</td>
			<td>Give Points</td>
		</tr>
	<?php
			$invReport = array();	
			$invReport['amount'] = 0.00;
			$invReport['avgprice'] = 0.00;
			$invReport['amt_sold'] = 0.00;
			$invReport['proj_amt'] = 0.00;

			foreach($products as $product) {
				echo('<tr>');
				$invReport['amount'] += $product['amount'];
				$invReport['avgprice'] += $product['avgprice'];
				$invReport['amt_sold'] += $product['amt_sold'];
				$invReport['proj_amt'] += $product['proj_amt'];
				
				
				switch($product['points']) {
					case 0: $points = "False"; break;
					case 1: $points = "True"; break;
				}
				echo('
					<td>' . $product['id'] . '</td>
					<td>' . $product['name'] . '</td>
					<td>' . $product['amount'] . '</td>
					<td>$' . number_format((float)$product['avgprice'], 2, '.', '') . '</td>
					<td>' . $product['amt_sold'] . '</td>
					<td>$' . number_format((float)$product['proj_amt'], 2, '.', '') . '</td>
					<td>' . $points .'</td>
				');
				echo('</tr>');
				
			}
			
			$invReport['avgprice'] = $invReport['avgprice'] / count($products);
			echo('
				<tr>
				<td></td>
				<td>TOTAL</td>
				<td>' . $invReport['amount'] . '</td>
				<td>$' . number_format((float)$invReport['avgprice'], 2, '.', '') . '</td>
				<td>' . $invReport['amt_sold'] . '</td>
				<td>$' . number_format((float)$invReport['proj_amt'], 2, '.', '') . '</td>
				<td></td>
				</tr>
			');
	?>
	</table>
  </div>
  <div id="reportKey">
	
	<center>

	<p>&nbsp;</p>
<table border="1" width="100%" height="100%">
<tbody>
<tr>
<td>Day Sales:</td>
<td><?php
					$dailyLog = getLog('transac', strtotime('today midnight'), strtotime('today 23:59:59'));
					$dailySales = 0.00;
					//print_r($logData);
					foreach($dailyLog as $log) {
						foreach($log as $k => $v) {
							if ($k == "amount") {
								$dailySales += $v;
							}
						}
					}
					echo "$".number_format($dailySales, 2);			
				?></td>
<td>WTD Sales</td>
<td><?php
					$today = date('l');
					
					$date = new DateTime('U');
					$date->setTimeZone(new DateTimeZone('America/Chicago'));						
					$date->setTime(00, 00, 00);
					$date->setDate(date('Y'), date('m'), date('d'));
					if ($today == "Sunday") {			

						$wtdEnd = $date->sub(new DateInterval('P6D'))->format('U');					
						$wtdEnd = $date->add(new DateInterval('P1D'))->format('U');

					}
					elseif ($today == "Monday") {
						$wtdStart = $date->format('U');
						$wtdEnd = $date->add(new DateInterval('P7D'))->format('U');
					}
					elseif ($today == "Tuesday") {
						
						$wtdStart = $date->sub(new DateInterval('P1D'))->format('U');
						$wtdEnd = $date->add(new DateInterval('P6D'))->format('U');
					}
					elseif ($today == "Wednesday") {

						$wtdStart = $date->sub(new DateInterval('P2D'))->format('U');
						$wtdEnd = $date->add(new DateInterval('P5D'))->format('U');
					}
					elseif ($today == "Thursday") {

						$wtdStart = $date->sub(new DateInterval('P3D'))->format('U');
						$wtdEnd = $date->add(new DateInterval('P4D'))->format('U');
					}
					elseif ($today == "Friday") {

						$wtdStart = $date->sub(new DateInterval('P4D'))->format('U');
						$wtdEnd = $date->add(new DateInterval('P3D'))->format('U');
					}
					elseif ($today == "Saturday") {

						$wtdStart = $date->sub(new DateInterval('P5D'))->format('U');
						$wtdEnd = $date->add(new DateInterval('P4D'))->format('U');
					}
					$wtdLogData = getLog('transac', $wtdStart, $wtdEnd);
					$wtdSales = 0.00;
					//print_r($logData);
					foreach($wtdLogData as $log) {
						foreach($log as $k => $v) {
							if ($k == "amount") {
								$wtdSales += $v;
							}
						}
					}
					echo "$".number_format($wtdSales, 2);			
				?></td>
<?php 
?>
<td>WTD Labor</td>
<td><?php
	$wtdLabor = ($wtdSales - 850) * .8;
	echo($wtdLabor);
?></td>
</tr>
<tr>
<td>&nbsp;&nbsp; Alex Sales</td>
<td><?php
	$alexSales = 0;
	foreach($dailyLog as $log) {
		if ($log['admin'] == 'alex') {
			$alexSales += $log['amount'];
		}
	}
	print_r('$'.number_format($alexSales, 2));
?></td>
<td>&nbsp; WTD Alex</td>
<td><?php
	$alexWtdSales = 0;
	foreach($wtdLogData as $log) {
		if ($log['admin'] == 'alex') {
			$alexWtdSales += $log['amount'];
		}
	}
	print_r('$'.number_format($alexWtdSales, 2));
?></td>
<td>&nbsp;&nbsp; Alex Labor</td>
<td><?php echo number_format($alexWtdSales / $wtdSales*100, 2, '.', '');?>%</td>
</tr>

<tr>
<td>&nbsp;&nbsp; Pmb Sales</td>
<td><?php
	$pmbSales = 0;
	foreach($dailyLog as $log) {
		if ($log['admin'] == 'pmb') {
			$pmbSales += $log['amount'];
		}
	}
	print_r('$'.number_format($pmbSales, 2));
?></td>
<td>&nbsp; WTD Pmb</td>
<td><?php
	$pmbWtdSales = 0;
	foreach($wtdLogData as $log) {
		if ($log['admin'] == 'pmb') {
			$pmbWtdSales += $log['amount'];
		}
	}
	print_r('$'.number_format($pmbWtdSales, 2));
?></td>
<td>&nbsp;&nbsp; Pmb Labor</td>
<td><?php echo number_format($pmbWtdSales / $wtdSales*100, 2, '.', '');?>%</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Proj. Cash</td>
<td><?php
					
					$projcash = 0.00;		
					foreach($products as $product) {
						$projcash += $product['proj_amt'];
					}
					foreach($cash as $c) {
						$projcash += $c;
					}
		
					$openCash = 0.00;
					foreach($transactions as $t) {
						if (array_key_exists('closed', $t) && !$t['closed']) {
							$openCash += $t['amount'];
						}
					}
					echo "$".number_format($projcash + $openCash, 2);
				?></td>
<td>Open Transactions</td>
<td>
<?php
$openTransactions = 0;

foreach($transactions as $t) {
	if (array_key_exists('closed', $t) && $t['closed'] == False)
		$openTransactions++;
}
echo $openTransactions;
?>
</td>
<td>All Time Alex Pay</td>
<td><?php
	$allCashLog = $mysqli->query("SELECT * FROM log WHERE flag = 'cash'");
	$alexPay = 0.00;
	$pmbPay = 0.00;
	while ($row = mysqli_fetch_assoc($allCashLog)) {
		$data = unserialize($row['data']);
		if ($data['type'] == 'mgmt' && $data['note'] == 'payroll_alex') {
			$alexPay += ($data['cashOrig'] - $data['cashNew']);
		}	
		elseif ($data['type'] == 'mgmt' && ($data['note'] == 'payroll_will' || $data['note'] == 'payroll_pmb')) {
			$pmbPay += ($data['cashOrig'] - $data['cashNew']);
		}
	}
echo '$'.number_format($alexPay, 2);
?> </td>
</tr>
<tr>
<td>&nbsp; Open Cash</td>
<td><?php echo "$".number_format($openCash, 2); ?></td>
<td>Void Transactions (WTD)</td>
<td>
<?php
$voidOrders = 0;
   foreach($wtdLogData as $w) {
	if (array_key_exists('void', $w)) {
		$voidOrders++;
	}
}
echo $voidOrders++;
?></td>
<td>All Time Pmb Pay</td>
<td>
<?php
	echo '$'.number_format($pmbPay, 2);
?>
</td>
</tr>
<tr>
<td>&nbsp; Store Cash</td>
<td><?php echo "$".number_format($cash['store'], 2); ?></td>
<td>Frequent Customers (WTD)</td>
<td>
<?php
$customerData = array();
foreach($wtdLogData as $k => $v) {
	if (!array_key_exists($v['customer'], $customerData)) {
		$customerData[ $v['customer'] ] = 1;
	}
	else {
		$customerData[ $v['customer'] ]++;
	}
}
$frequentCustomers = array();
$i = 0;
foreach($customerData as $k => $v) {
	$i++;
	echo($k);
	if ($i < 3) {
		echo(', ');
	}
	else {
		break;
	}
}
	
?>
</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp; Alex Cash</td>
<td><?php echo "$".number_format($cash['alex'], 2); ?></td>
<td>Highest Credit Customers</td>
<td><?php
	$query = $mysqli->query('SELECT * FROM `customers` ORDER BY `credit` DESC LIMIT 3');
	$maxCustomer = "";
	$i = 0;
	while($row = mysqli_fetch_assoc($query)) {
		$i++;
		$maxCustomer .= $row['userid'];
		if ($i < 3) {
			$maxCustomer .= ', ';
		}		
	}		
echo $maxCustomer;
?></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp; Pmb Cash</td>
<td><?php echo "$".number_format($cash['pmb'], 2); ?></td>
<td>Lowest Credit Customers</td>
<td><?php
	$query = $mysqli->query('SELECT * FROM `customers` ORDER BY `credit` ASC LIMIT 3');
	$maxCustomer = "";
	$i = 0;
	while($row = mysqli_fetch_assoc($query)) {
		$i++;
		$maxCustomer .= $row['userid'];
		if ($i < 3) {
			$maxCustomer .= ', ';
		}		
	}		
echo $maxCustomer;
?></td>
<td></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>
	</center>
   </div>
   <form id="laborCalc" title="Calculate Labor" class="panel" action="index.php#_laborCalc" method="post">
	<center>
		<p>
			Pay Period starts every Monday<br />
			Pay Period ends every Sunday<br />
			Pay Period pays every Monday<br />
			IE: Start 7/5, End 7/11, Paid 7/12<br />
			Deduction should be the expected reup cost ($850)<br />
		</p>
		<table border="1">
		<tr>
			<td>Pay Period Start Date (mm/dd/yyyy):</td><td><input type="text" name="startDate" /></td>
		</tr>
		<tr>
			<td>Pay Period End Date (mm/dd/yyyy):</td><td><input type="text" name="endDate" /></td>
		</tr>
		<tr>
			<td>Savings Deduction</td>
			<td><input type="text" name="reup" /></td>
		</tr>
		</table>
		<?php if (isset($calcLabor['result'])) {
			print_r($calcLabor['result']);
		}
		?>
		<input type="hidden" name="action" value="calcLabor" />
		<a class="redButton" href="javascript:laborCalc.submit()">Calculate</a>
	</center>
   </form>
</body>
</html>
