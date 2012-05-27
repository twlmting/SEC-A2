<?php 
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
	<link rel="stylesheet" type="text/css" href="templates.css" />
	<title>SEC A2 | Registration</title>
  </head>
  
  <body>
  	<h3>Registration</h3>
  	<div>
  	  <div id="register">
  	  <?php if ((!isset($_POST['registerSubmit'])) || ($validForm != ''))  {?>
  		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  	  	  <table border="0">
  	  		<tr>
  	  	  	  <td class="title">Name:</td>
  	  	  	  <td class="input"><input type="text" name="name" value="<?php echo $_POST["name"] ?>"></input></td>
  	  	  	  <td class="error"><?php
	                  if(isset($_POST["name"])) {
	                  	if(!validateRegistrationText($_POST["name"])) {
	                      $validForm = false;
	                    }
	                  } ?>
			  </td>
  	  		</tr>
  	  		<tr>
  	  	  	  <td class="title">Username:</td>
  	  	  	  <td class="input"><input type="text" name="username" value="<?php echo $_POST["username"] ?>"></input></td>
  	  	  	  <td class="error"><?php
	                  if(isset($_POST["username"])) {
	                  	if(!validateRegistrationUser($_POST["username"])) {
	                      $validForm = false;
	                    }
	                  } ?>
			  </td>
  	  		</tr>
  	  		<tr>
	  	  	  <td class="title">Password:</td>
	  	  	  <td class="input"><input type="password" name="password" value="<?php echo $_POST["password"] ?>"></input></td>
	  	  	  <td class="error"><?php
	                  if(isset($_POST["password"])) {
	                  	if(!validateRegistrationText($_POST["password"])) {
	                      $validForm = false;
	                    }
	                  } ?>
			  </td>
	  	  	</tr>
	  	  	<tr>
	  	  	  <td colspan="3" align="center">
	  	  	  	<input type="submit" name="registerSubmit" value="Register"></input>
	  	  	  	<a href="login.php"><input type="button" value="Back"></input></a>
	  	  	  </td>
	  	  	</tr>
  	  	  </table>
  		</form>
		
	  <?php }
		if(isset($_POST['registerSubmit']) && ($validForm == true)) {
			$name = isset($_POST['name']) ? $_POST['name'] : '';
			$username = isset($_POST['username']) ? $_POST['username'] : '';
			$password = isset($_POST['password']) ? $_POST['password'] : '';
			
			$error = registerUser($username, $password, $name);
	  ?>
	  <div id="registraionCompleted">
	  	<h4>Registration Completed!</h4>
	  	
	  	<table border="1">
	      <tr>
			<td align="center">
			<?php 
			  if($validForm == true && $_POST['errorText'] == '') {
				$username = $_POST['username'];
				$errorText = $_POST['errorText'];
											
				echo "Congratulation!! You are registered successfully!<br /><br />";
				echo "Your username is: $username <br /><br />";
				echo "<a href='login.php'><input type='button' value='OK' /></a>";
			  }
			  else {
				echo "$errorText<br /><br />";
				echo "<a href='login.php'><input type='button' value='Back' /></a>";
			  }
			?>
			</td>
		  </tr>
		</table>
	  </div>
	  <?php } ?>
	  </div>
	</div>
  </body>
</html>
