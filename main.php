<?php 
  require("functions.php");
  session_start();
  $allowed_referer = array("http://yallara.cs.rmit.edu.au/~s3318730/SEC_A2/login.php", "http://yallara.cs.rmit.edu.au/~s3318730/SEC_A2/main.php");
  $referal = $_SERVER['HTTP_REFERER'];
  if (in_array($referal, $allowed_referer)) {
  
  
  // display the page for normal user login
  function displayForm() {
  	$username = $_SESSION['username'];
  	
  	$fp = fopen("data/users.txt","r");
  	
  	while (!feof($fp)) {
  		$line = fgetss($fp);
  		if (!(($line == "") || ($line == null))) {
  			$linearray = explode(":",$line);
  			if((strnatcasecmp($linearray[1], $username) == 0)) {
  				$password = $linearray[2];
  				$address = $linearray[3];
  				$state = $linearray[4];
  				$zip = $linearray[5];
  				$cc = $linearray[6];
  				$exp = $linearray[7];
  				$cvv = $linearray[8];
  			}
  		}
  	}
  	
  	$hidden = "XXXX-XXXX-XXXX-";
  	$lastFour = substr($cc, -4);
  	$displayCC = $hidden . $lastFour;
  	
  	echo "<h3>Welcome " . $_SESSION['name'] . "</h3>";
  	echo "<div id='register'>";
  	echo "<form action='transaction.php' method='POST'>";
  	  echo "<table>";
	  	echo "<tr><td colspan='2' align='center'>Your Details:</td></tr>";
	  	echo "<tr>";
	  	  echo "<td class='title'>Username:</td>";
	  	  echo "<td class='input'><input type='text' name='username' value='$username' readonly='readonly'></input></td>";
	  	echo "</tr>";
	  	echo "<tr>";
	  	  echo "<td class='title'>Password:</td>";
	  	  echo "<td class='input'><input type='password' name='password' value='$password' readonly='readonly'></input></td>";
	  	echo "</tr>";
	  	echo "<tr>";
	  	  echo "<td class='title'>Address:</td>";
	  	  echo "<td class='input'><input type='text' name='address' value='$address' readonly='readonly'></input></td>";
	  	echo "</tr>";
	  	echo "<tr>";
	  	  echo "<td class='title'>State:</td>";
	  	  echo "<td class='input'><input type='text' name='state' value='$state' readonly='readonly'></input></td>";
	  	echo "</tr>";
	  	echo "<tr>";
	  	  echo "<td class='title'>Zip:</td>";
	  	  echo "<td class='input'><input type='text' name='zip' value='$zip' readonly='readonly'></input></td>";
	  	echo "</tr>";
	  	echo "<tr>";
	  	  echo "<td class='title'>Credit Card Number:</td>";
	  	  echo "<td class='input'>
	  	  		  <input type='text' name='displaycc' value='$displayCC' readonly='readonly'></input>
	  	  		  <input type='hidden' name='cc' value='$cc'></input>
	  	  		</td>";
	  	echo "</tr>";
	  	echo "<tr>";
	  	  echo "<td class='title'>Expiry Date:</td>";
	  	  echo "<td class='input'><input type='text' name='exp' value='$exp' readonly='readonly'></input></td>";
	  	echo "</tr>";
	  	echo "<tr>";
	  	  echo "<td class='title'>CVV:</td>";
	  	  echo "<td class='input'><input type='text' name='cvv' value='$cvv' readonly='readonly'></input></td>";
	  	echo "</tr>";
	  	echo "<tr>";
	  	  echo "<td colspan='2' align='center'>";
	  		echo "<input type='submit' value='Go to Transaction' name='transaction'></input>";
	  		echo "<a href='login.php'><input type='button' value='Logout' name='logout'></input></a>";
	  		if(isset($_POST["logout"])) {
	  		  session_destroy();
	  		}
	  	  echo "</td>";
	  	echo "</tr>";
  	  echo "</table>";
  	echo "</form>";
  	echo "</div>";
  	
  	// display user recent logins
  	echo "<div id='loginHistory'>";
  	  echo "<table border='0'>";
  		echo "<tr>";
  	 	  echo "<td colspan='2' align='center'>Recent Logins</td>";
  		echo "</tr>";
  		echo "<tr>";
  		  echo "<td>Date</td>";
  		  echo "<td>IP</td>";
  		echo "</tr>";
  	
	  	$user = $_SESSION['username'];
	  	$fp = fopen("data/log_login.txt",'r');
	  	
	  	while(!feof($fp)) {
	  		$line = fgetss($fp);
	  		$linearray = explode(",",$line);
	  	
	  		if((strnatcasecmp($linearray[1], $user) == 0)) {
	  			echo "<tr>";
	  			echo "<td><input type='text' value='$linearray[0]' readonly='readonly' /></td>";
	  			echo "<td><input type='text' value='$linearray[2]' readonly='readonly' /></td>";
	  			echo "</tr>";
	  		}
	  	}
	  	fclose($fp);
	  	
	  echo "</table>";
  	echo "</div>";
  	
  	// display user recent activities
  	echo "<div id='transHistory'>";
  	  echo "<table border='0'>";
  		echo "<tr>";
  	 	  echo "<td colspan='4' align='center' class='historyTitle'>Recent Transactions</td>";
  		echo "</tr>";
  		loadUserApprovedTransactions($user);
  		loadUserFailedTransactions($user);
  	  echo "</table>";
  	echo "</div>";
  }
  
  // display the page for Admin login
  function displayAdmin() {
  	$username = $_SESSION['username'];
  	 
  	echo "<h3>Welcome " . $_SESSION['name'] . "</h3>";
  	echo "<div id='adminPage'>";
  	echo "<form action='main.php' method='POST'>";
  	  echo "<table border='0'>";
  		echo "<tr><td colspan='2' align='center'>Select to view:</td></tr>";
  		echo "<tr>";
  		  echo "<td>Display Users:</td>";
  		  echo "<td><input type='submit' name='displayUsers' value='Display'></input></td>";
  		echo "</tr>";
  		echo "<tr>";
  		echo "<td>Display User Logins:</td>";
  		echo "<td><input type='submit' name='displayUserLogins' value='Display'></input></td>";
  		echo "</tr>";
  		echo "<tr>";
  		  echo "<td>Display Approved Transactions:</td>";
  		  echo "<td><input type='submit' name='displayApprovedTrans' value='Display'></input></td>";
  		echo "</tr>";
  		echo "<tr>";
  		  echo "<td>Display Failed Transactions:</td>";
  		  echo "<td><input type='submit' name='displayFailedTrans' value='Display'></input></td>";
  		echo "</tr>";
  		echo "<tr>";
  		  echo "<td colspan='2' align='center'>";
  			echo "<a href='login.php'><input type='button' value='Logout' name='logout'></input></a>";
  	
  	  		if(isset($_POST["logout"])) {
  			  session_destroy();
  	  		}
  	  		
  	  	  echo "</td>";
  		echo "</tr>";
  	  echo "</table>";
  	echo "</form>";
  	echo "</div>";
  	
  	if(isset($_POST['displayUsers'])) {
  	  echo "<hr />";
  	  echo "<table>";
  	  	echo "<tr>";
  	  	  echo "<td>Name</td>";
  	  	  echo "<td>Username</td>";
  	  	  echo "<td>Password</td>";
  	  	  echo "<td>Address</td>";
  	  	  echo "<td>State</td>";
  	  	  echo "<td>Zip</td>";
  	  	  echo "<td>Credit Card Number</td>";
  	  	  echo "<td>Expiry Date</td>";
  	  	  echo "<td>CVV</td>";
  	  	echo "</tr>";
  	  	loadUsers();
  	  echo "</table>";
  	}
  	elseif(isset($_POST['displayUserLogins'])) {
  		echo "<hr />";
  		echo "<table>";
  		echo "<tr>";
  		echo "<td>Username</td>";
  		echo "<td>Date</td>";
  		echo "<td>IP</td>";
  		echo "</tr>";
  		loadUserLogins();
  		echo "</table>";
  	}
  	elseif(isset($_POST['displayApprovedTrans'])) {
  		echo "<hr />";
  		echo "<table>";
  		echo "<tr>";
  	  	  echo "<td>Username</td>";
  	  	  echo "<td>Date</td>";
  	  	  echo "<td>Transaction ID</td>";
  	  	  echo "<td>Result</td>";
  	  	  echo "<td>Amount</td>";
  	  	  echo "<td>IP</td>";
  		echo "</tr>";
  		loadApprovedTransactions();
  	  echo "</table>";
  	}
  	elseif(isset($_POST['displayFailedTrans'])) {
  		echo "<hr />";
  		echo "<table>";
  		echo "<tr>";
  		  echo "<td>Username</td>";
  	  	  echo "<td>Date</td>";
  	  	  echo "<td>Amount</td>";
  	  	  echo "<td>Result</td>";
  	  	  echo "<td>Error Type</td>";
  	  	  echo "<td>IP</td>";
  		echo "</tr>";
  		loadFailedTransactions();
  	  echo "</table>";
  	}
  }
?>

<!DOCTYPE html 
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
	<meta name="description" content="Website of Certificate Management" />
	<meta name="author" content="Ming Ting, Chen" />
	<link rel="stylesheet" type="text/css" href="template.css" />
	<title>SEC A2 | Main</title>
  </head>
  
  <body>
	<div>
  	  	<?php 
		if(isset($_SESSION['name']) && $_SESSION['username'] !== "admin") {
		  displayForm();
		}
		
	  	elseif($_SESSION['username'] == "admin") {
	  	  displayAdmin();
	  	}
	  	?>
	</div>
	<hr />
  	<?php
  	  if(isset($_POST["transaction"])) {
  	  	$_SESSION['displaycc'] = $_POST['displaycc'];
  	  	$_SESSION['cc'] = $_POST['cc'];
  	  	$_SESSION['exp'] = $_POST['exp'];
  	  	$_SESSION['cvv'] = $_POST['cvv'];
  	  }
  	?>
  	<?php
  	}
  	else{
  		header('Location: http://yallara.cs.rmit.edu.au/~s3318730/SEC_A2/login.php');
  	}
  	?>
  </body>
</html>