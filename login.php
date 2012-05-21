<?php 
  session_start();
  require("functions.php");
  $validForm = true;
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
	<title>SEC A2 | Login</title>
  </head>
  
  <body>
  	<h3>User Login</h3>
  	<div>
  	  <div id="login">
  	  <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  	  	<table border="0">
  	  	  <tr>
  	  	    <td class="title">Username:</td>
  	  	    <td class="input"><input type="text" name="username" value="<?php echo $_POST["username"] ?>"></input></td>
  	  	    <td class="error">
  	  	    <?php 
  	  	      if(isset($_POST["login"]) && $_POST["username"] == "") {
  	  	      	echo "Username Required!";
  	  	      	$validForm = false;
  	  	      }
  	  	    ?>
			</td>
  	  	  </tr>
  	  	  <tr>
  	  	    <td class="title">Password:</td>
  	  	    <td class="input"><input type="password" name="password" value="<?php echo $_POST["password"] ?>"></input></td>
  	  	    <td class="error">
  	  	    <?php 
  	  	      if(isset($_POST["login"]) && $_POST["password"] == "") {
  	  	      	echo "Password Required!";
  	  	      	$validForm = false;
  	  	      }
  	  	    ?>
			</td>
  	  	  </tr>
  	  	  <tr>
  	  	    <td colspan="3" align="center">
  	  	      <input type="submit" name="login" value="Login"></input>
  	  	      <a href="register.php"><input type="button" value="New User" class="registerButton"></input></a>
  	  	    </td>
  	  	  </tr>
  	    </table>
  	  </form>
  	  </div>
  	  
  	  <div id="loginSuccess">
  	    <table border="0" width="450px">
  	      <tr>
  	        <td align="center" class="loginError">
  	          <?php 
  	          	if(isset($_POST["login"]) && $validForm == true && (strnatcasecmp($_POST['username'], 'admin') == 0) && (strnatcasecmp($_POST['password'], 'admin') == 0)) {
  	          	  session_register('username');
  	          	  $_SESSION['name'] = "Administrator";
  	          	  $_SESSION['username'] = "admin";
  	          		
  	          	  echo "<br />Login successfully!<br /><br />";
  	          	  echo "<a href='main.php'><input type='button' value='OK' /></a>";
  	          	}
		  	    elseif(isset($_POST["login"]) && $validForm == true) {
		  	      $fp = fopen("data/users.txt","r");
		  	      if ($fp == null) {
		  	    	echo "<br />Invalid login details";
		  	      }
		  	      else {
		  	    	$match = false;
		  	    	while (!feof($fp)) {
		  	    	  $line = fgetss($fp);
		  	    	  if (!(($line == "") || ($line == null))) {
		  	    	    $linearray = explode(":",$line);
		  	    	    $encryptPass = md5($_POST['password']);
		  	    	    if((strnatcasecmp($linearray[0], $_POST['username']) == 0) && (strcmp($linearray[1], $encryptPass) == 0)) {
		  	    	      $match = true;
		  	    	      break;
		  	    	    }
		  	    	  }
		  	        }
		  	    	if ($match == true) {
		  	    	  session_register('username');
		  	    	  $_SESSION['name'] = $linearray[2];
		  	    	  $_SESSION['username'] = $linearray[0];
		  	    	  $date = date("Y-m-d g:i:s a");
		  	    	  $ip = $_SERVER['REMOTE_ADDR'];
		  	    	  $username = $linearray[0];
		  	    	  
		  	    	  logUserLogin($username, $date, $ip);
		  	    	  
		  	    	  echo "<br />Login successfully!<br /><br />";
		  	    	  echo "<a href='main.php'><input type='button' value='OK' /></a>";
		  	    	}
		  	    	else {
		  	    	  echo "<br />Incorrect login details!";
		  	    	}
		  	    	fclose($fp);
		  	      }
		  	    }
  	  		  ?>
  	        </td>
  	      </tr>
  	    </table>
  	  </div>
  	</div>
  </body>
</html>