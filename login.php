
<?php
	/**
	Permissions
		1: User
		2: Manager
*/

//print("hi there!");
	if ($_GET['action'] == "md5") {
		print_r(md5($_GET['value']));
		die();
	}
	if ($_GET['action'] == "sign_in") {
		session_start();
		if (isset($_SESSION['it_user'])) {
			session_unset();
			session_destroy();
			session_start();
		}
		include("config.php");
		$mysqli = mysqlInit();
		$query = "SELECT * FROM users WHERE username = '" . $mysqli->real_escape_string($_POST['username']) . "' AND password = '" . $mysqli->real_escape_string(md5($_POST['password'])) . "'";
	
		if (!$result = mysqli_query($mysqli, $query)) {
        	        die("Query Error (" . $query . "): " . mysqli_error($mysqli));
	        }

        	$row = mysqli_fetch_assoc($result);
		
		

		if ($row['access'] == "1" || $row['access'] == "2") {
		
			$_SESSION['it_user'] = $_POST['username'];
			$_SESSION['access'] = intval($row['access']);
			$_SESSION['expire'] = (time()+120);
			//print_r("TIME: " . time());
			//print_r("EXPIRE: " . $_SESSION['expire']);
			$m = "Signed in";
			header("Location: http://localhost/itrap/index.php");
		}
		else {
			$m = "Invalid username or password.";
		}
	}
	if ($_GET['action'] == "sign_out") {
		session_start();
		session_unset();
		session_destroy();
		$m = "Successfully signed out.";
	}
	if ($_GET['action'] == "expire") {
		$m = "Expired after 120 seconds no activity.  Please sign in again.";
		session_start();
		session_unset();
		session_destroy();
	}

?>
<html>
<body>
<center>
<?php
	if (isset($m)) {
		print("<font color='red'><b>" . $m . "</b></font>");
	}
?>

<form action="login.php?action=sign_in" method="post">
<table>
<tr>
	<td width="25%"></td>
	<td>Username:</td>
	<td><input type="text" name="username" width="80px" height="15px"/></td>
</tr>
<tr>
	<td width="25%"></td>
	<td>Password:</td>
	<td><input type="password" name="password" /></td>
</tr>
</table>

<input type="Submit" value="Sign In" />
</center>
</form>
</body>
</html>
