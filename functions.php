<?php

function validateRegistrationText($field) {
    if($field == "") {
        echo "This field is required!";
        return false;
    }
    else {
        return true;
    }
}

// function validateRegistrationNumber($field) {
// 	$regexp = "/^[0-9-]+$/";
	
// 	if ($field == "") {
//     	echo "This field is required!";
//     	return false;
//     }
//     if(!preg_match($regexp, $field)) {
//         echo "Numeric Only!";
//         return false;
//     }
//     else{
//         return true;
//     }
// }

function validateRegistrationUser($field) {
	$pfile = fopen("data/users.txt", "a+");
	rewind($pfile);
	
	while(!feof($pfile)) {
		$line = fgets($pfile);
		$tmp = explode(':', $line);
		if ($tmp[0] == $field) {
			$errorText = "Username Exists!";
			break;
		}
	}
	
	fclose($pfile);
	
	if ($field == "") {
		echo "This field is required!";
		return false;
	}
	elseif ($errorText != "") {
		echo "Username Exists!";
		return false;
	}
	else{
		return true;
	}
}

function validateRegistrationCC($field) {
	$regexp = "/^[0-9-]+$/";

	if ($field == "") {
		echo "This field is required!";
		return false;
	}
	elseif(strlen($field) != 16) {
		echo "Length is incorrect!";
		return false;
	}
	if(!preg_match($regexp, $field)) {
		echo "Numeric Only!";
		return false;
	}
	else{
		return true;
	}
}

function registerUser($username, $password, $name) {
	$errorText = '';

	$pfile = fopen("data/users.txt", "a+");
	rewind($pfile);

	while(!feof($pfile)) {
		$line = fgets($pfile);
		$tmp = explode(':', $line);
		if ($tmp[0] == $username) {
			$errorText = "You have already registered!";
			break;
		}
	}

	if ($errorText == '') {
		$encryptPass = md5($password);

		fwrite($pfile, "$username:$encryptPass:$name\n");
		
		//create a new file when an user has registered
		$cerFile = $username . ".txt";
		$directory = "data/certificates/";
		$fp = fopen($directory . $cerFile, 'w');
		chmod($directory . $cerFile, 0707);
	} 
	fclose($pfile);
	fclose($fp);
	return $errorText;
}

function logUserLogin($username, $date, $ip) {
	$pfile = fopen("data/log_login.txt", "a+");
	rewind($pfile);
	
	$string = "$date,$username,$ip\n";
	
	fwrite($pfile, $string);
	
	fclose($pfile);
}

function createNewCertificate($username, $commonName, $org, $orgUnit, $city, $state, $country) {
	$cerFile = $username . ".txt";
	$directory = "data/certificates/";
	$fp = fopen($directory . $cerFile, 'w+') or die("File Cannot Open");

	fwrite($fp, "$commonName:$org:$orgUnit:$city:$state:$country\n");

	fclose($fp);
}

function loadUserCertificate($user) {
	$cerFile = $user . ".txt";
	$directory = "data/certificates/";
	$fp = fopen($directory . $cerFile, 'r') or die("File Cannot Open");
	
	$line = fgetss($fp);
	$linearray = explode(":",$line);

	echo "<tr>";
	echo "<td class='historyTitle'>Common Name</td>";
	echo "<td><input type='text' value='$linearray[0]' readonly='readonly' /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='historyTitle'>Organization</td>";
	echo "<td><input type='text' value='$linearray[1]' readonly='readonly' /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='historyTitle'>Organization Unit</td>";
	echo "<td><input type='text' value='$linearray[2]' readonly='readonly' /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='historyTitle'>City</td>";
	echo "<td><input type='text' value='$linearray[3]' readonly='readonly' /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='historyTitle'>State</td>";
	echo "<td><input type='text' value='$linearray[4]' readonly='readonly' /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='historyTitle'>Country</td>";
	echo "<td><input type='text' value='$linearray[5]' readonly='readonly' /></td>";
	echo "</tr>";
	
	fclose($fp);
}
	
	

function loadUserFailedTransactions($user) {
	$fp = fopen("data/log_trans_failed.txt",'r');
	$match = false;
	
	while(!feof($fp)) {
		$line = fgetss($fp);
		$linearray = explode(",",$line);
	
		if((strnatcasecmp($linearray[0], $user) == 0)) {
			$match = true;
			break;
		}
	}
	
	if($match == true) {
		$fp = fopen("data/log_trans_failed.txt",'r');
		echo "<tr>";
		echo "<td colspan='4' class='historyTitle'>Declined Transactions</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='historyTitle'>Date</td>";
		echo "<td class='historyTitle'>Amount</td>";
		echo "<td class='historyTitle'>Status</td>";
		echo "<td class='historyTitle'>Error Type</td>";
		echo "<td class='historyTitle'>IP</td>";
		echo "</tr>";
		
		while(!feof($fp)) {
			$line = fgetss($fp);
			$linearray = explode(",",$line);
		
			if((strnatcasecmp($linearray[0], $user) == 0)) {
				echo "<tr>";
				echo "<td><input type='text' value='$linearray[1]' readonly='readonly' /></td>";
				echo "<td><input type='text' value='$linearray[2]' readonly='readonly' /></td>";
				echo "<td><input type='text' value='$linearray[3]' readonly='readonly' /></td>";
				echo "<td><input type='text' value='$linearray[4]' readonly='readonly' /></td>";
				echo "<td><input type='text' value='$linearray[5]' readonly='readonly' /></td>";
				echo "</tr>";
			}
		}
	}
	fclose($fp);
}

function logTransactionApproved($resultUsername, $date, $transID, $resultStatus, $resultAmount, $ip) {
	$pfile = fopen("data/log_trans_approved.txt", "a+");
	rewind($pfile);
	
	$string = "$resultUsername,$date,$transID,$resultStatus,$resultAmount,$ip\n";
	
	fwrite($pfile, $string);
	
	fclose($pfile);
}

function logTransactionFailed($username, $date, $resultAmount, $resultStatus, $error, $ip) {
	$pfile = fopen("data/log_trans_failed.txt", "a+");
	rewind($pfile);

	$string = "$username,$date,$resultAmount,$resultStatus,$error,$ip\n";

	fwrite($pfile, $string);

	fclose($pfile);
}

function loadUsers() {
	$fp = fopen("data/users.txt",'r');
	
	while(!feof($fp)) {
		$line = fgetss($fp);
		$linearray = explode(":",$line);
		
		echo "<tr>";
		echo "<td><input type='text' value='$linearray[0]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[1]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[2]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[3]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[4]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[5]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[6]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[7]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[8]' readonly='readonly' /></td>";
		echo "</tr>";
	}
	fclose($fp);
}

function loadUserLogins() {
	$fp = fopen("data/log_login.txt",'r');

	while(!feof($fp)) {
		$line = fgetss($fp);
		$linearray = explode(",",$line);

		echo "<tr>";
		echo "<td><input type='text' value='$linearray[0]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[1]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[2]' readonly='readonly' /></td>";
		echo "</tr>";
	}
	fclose($fp);
}

function loadApprovedTransactions() {
	$fp = fopen("data/log_trans_approved.txt",'r');
	
	while(!feof($fp)) {
		$line = fgetss($fp);
		$linearray = explode(",",$line);
	
		echo "<tr>";
		echo "<td><input type='text' value='$linearray[0]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[1]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[2]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[3]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[4]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[5]' readonly='readonly' /></td>";	
		echo "</tr>";
	}
	fclose($fp);
}

function loadFailedTransactions() {
	$fp = fopen("data/log_trans_failed.txt",'r');

	while(!feof($fp)) {
		$line = fgetss($fp);
		$linearray = explode(",",$line);

		echo "<tr>";
		echo "<td><input type='text' value='$linearray[0]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[1]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[2]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[3]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[4]' readonly='readonly' /></td>";
		echo "<td><input type='text' value='$linearray[5]' readonly='readonly' /></td>";
		echo "</tr>";
	}
	fclose($fp);
}









