<?php 
  require("functions.php");
  session_start();
?>
<!DOCTYPE html 
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
	<meta name="description" content="Website to Payment Gateway" />
	<meta name="author" content="Ming Ting, Chen" />
	<link rel="stylesheet" type="text/css" href="template.css" />
	<title>SEC A2 | Create New Certificate</title>
  </head>
  
  <body>
  	<h3>Transaction - <strong><?php echo $_SESSION['name']; ?></strong></h3>
  	<div>
  	  <div id="register">
	  	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
	  	  <table border="0">
	  	  	<tr>
	  	  	  <td class="title" align="center" colspan="2">New Certificate Details:</td>
	  	  	</tr>
	  	  	<tr>
	  	  	  <td class="title">Credit Card Number:</td>
	  	  	  <td class="input">
	  	  	  	<input type="text" name="displaycc" value="<?php echo $_POST['displaycc']; ?>" readonly="readonly"></input>
	  	  	  	<input type="hidden" name="cc" value="<?php echo $_POST['cc']; ?>" />
	  	  	  </td>
	  	  	</tr>
	  	  	<tr>
	  	  	  <td class="title">Expiry Date:</td>
	  	  	  <td class="input"><input type="text" name="exp" value="<?php echo $_POST['exp']; ?>" readonly="readonly"></input></td>
	  	  	</tr>
	  	  	<tr>
	  	  	  <td class="title">CVV:</td>
	  	  	  <td class="input"><input type="text" name="cvv" value="<?php echo $_POST['cvv']; ?>" readonly="readonly"></input></td>
	  	  	</tr>
	  	  	<tr>
	  	  	  <td class="title">Demo:</td>
	  	  	  <td class="input"><input type="text" name="demo" value="y" readonly="readonly"></input></td>
	  	  	</tr>
	  	  	<tr>
	  	  	  <td class="title">Action:</td>
	  	  	  <td class="input"><input type="text" name="action" value="sale" readonly="readonly"></input></td>
	  	  	</tr>
	  	  	<tr>
	  	  	  <td class="title">Media:</td>
	  	  	  <td class="input"><input type="text" name="media" value="cc" readonly="readonly"></input></td>
	  	  	</tr>
	  	  	<tr>
	  	  	  <td class="title">Amount:</td>
	  	  	  <td class="input"><input type="text" name="amount"></input></td>
	  	  	  <!--  
	  	  	  <td>
	  	  	  	<input type="hidden" name="custid" value="s3318730"></input>
	  	  	  	<input type="hidden" name="password" value="8776e39c09"></input>
	  	  	  </td>
	  	  	  -->
	  	  	</tr>
	  	  	<tr>
	  	  	  <td colspan="2" align="center">
	  	  	    <a href="main.php"><input type="button" value="Back" name="back"></input></a>
	  	  	  	<input type="submit" name="create" value="Create" class="registerButton"></input>
	  	  	  	<a href="login.php"><input type="button" value="Logout" name="logout"></input></a>
	  	  	  	<?php 
	  	  	  	  if(isset($_POST["logout"])) {
	  	  	  	  	session_destroy();
	  	  	  	  }
	  	  	  	?>
	  	  	  </td>
	  	  	</tr>
	  	  </table>
	  	</form>
	  </div>
	</div>
	<hr />
	<div>
  	<?php
  	  if(isset($_POST["create"])) {
  	  	$params['name'] = $_SESSION['name'];
  	  	$params['custid'] = $_POST['custid'];
	 	$params['password'] = $_POST['password'];
	 	$params['demo'] = $_POST['demo'];
	    $params['action'] = $_POST['action'];
	    $params['media'] = $_POST['media'];
	    $params['cc'] = $_POST['cc'];
	    $params['exp'] = $_POST['exp'];
	    $params['amount'] = $_POST['amount'];
	    
  	  	$submit_url = "http://goanna.cs.rmit.edu.au/~ronvs/TCLinkGateway/process.php";
  	  	
  	  	// Now submit the web form and catch any output from the submission
  	  	if(!($snoopy->submit($submit_url, $params)))
  	  		die("Failed fetching document: ".$snoopy->error."\n");
  	  	
	    $result = $snoopy->results;
	    
	    //  Output will be 'serialized'. Use line below to undo this.
	    $result = unserialize($snoopy->results);
	    
	    $resultUsername = $_SESSION['username'];
	    $date = date("Y-m-d g:i:s a");
	    $resultAmount = $_POST['amount'];
	    $resultStatus = $result['status'];
	    $ip = $_SERVER['REMOTE_ADDR'];
	    
	    if($result['status'] == 'approved') {
	    	$transID = $result['transid'];
	    	logTransactionApproved($resultUsername, $date, $transID, $resultStatus, $resultAmount, $ip);
	    	
	    	echo "<table>";
	    	echo "<tr><td class='success'>Transaction was successful!</td></tr>";
	    	echo "<tr><td class='success'>Your Transaction ID: " . $transID . "</td></tr>";
	    	echo "</table>";
	    }
	    elseif($result['status'] == 'decline') {
	    	$error = $result['declinetype'];
	    	logTransactionFailed($resultUsername, $date, $resultAmount, $resultStatus, $error, $ip);
	    	
	    	echo "<table>";
	    	echo "<tr><td class='transError'>Transaction declined!</td></tr>";
	    	echo "<tr><td class='transError'>Reason: " . $error . "</td></tr>";
	    	echo "</table>";
	    }
  	  	elseif($result['status'] == 'baddata') {
	    	$error = $result['offenders'];
	    	logTransactionFailed($resultUsername, $date, $resultAmount, $resultStatus, $error, $ip);
	    	
	    	echo "<table>";
	    	echo "<tr><td class='transError'>Improperly formatted data!</td></tr>";
	    	echo "<tr><td class='transError'>Reason: " . $error . "</td></tr>";
	    	echo "</table>";
	    }
	    else {
	    	$error = $result['errortype'];
	    	logTransactionFailed($resultUsername, $date, $resultAmount, $resultStatus, $error, $ip);
	    	
	    	echo "<table>";
	    	echo "<tr><td class='transError'>An error occurred!</td></tr>";
	    	echo "<tr><td class='transError'>Reason: " . $error . "</td></tr>";
	    	echo "</table>";
	    }
  	  }
  	?>
  	</div>
  </body>
</html>
