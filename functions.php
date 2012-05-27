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
		
		//create a new file for key pair
		$keyFile = $username . ".txt";
		$directory = "data/keypairs/";
		$fp1 = fopen($directory . $keyFile, 'w');
		chmod($directory . $keyFile, 0707);
	} 
	fclose($pfile);
	fclose($fp1);
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

function displayAllCertificates() {
// 	if ($handle = opendir('data/certificates/')) {
		$directory = "data/certificates/";
		$files1 = scandir($directory);
		$cerFile = $files1 . ".txt";
		
		
		echo $files1;
		
		$fp = fopen($directory . $cerFile, 'r');
		
		$line = fgetss($fp);
		$linearray = explode(":",$line);
		
		$com = $linearray[0];
		$org = $linearray[1];
		$orgUnit = $linearray[2];
		$city = $linearray[3];
		$state = $linearray[4];
		$country = $linearray[5];
		
		fclose($fp);
		
// 		while($handle != null) {
// 			echo "<table>";
// 			echo "<tr>";
// 			echo "<td>" . $com . "</td>";
// 			echo "</tr>";
// 			echo "<tr>";
// 			echo "<td>" . $org . "</td>";
// 			echo "</tr>";
// 			echo "<tr>";
// 			echo "<td>" . $orgUnit . "</td>";
// 			echo "</tr>";
// 			echo "<tr>";
// 			echo "<td>" . $city . "</td>";
// 			echo "</tr>";
// 			echo "<tr>";
// 			echo "<td>" . $state . "</td>";
// 			echo "</tr>";
// 			echo "<tr>";
// 			echo "<td>" . $country . "</td>";
// 			echo "</tr>";
// 		}
// 	}
	
// 	closedir($handle);
}

function createNewCertificate($username, $commonName, $org, $orgUnit, $city, $state, $country) {
	$cerFile = $username . ".txt";
	$directory = "data/certificates/";
	$fp = fopen($directory . $cerFile, 'w+') or die("File Cannot Open");

	fwrite($fp, "$commonName:$org:$orgUnit:$city:$state:$country\n");

	fclose($fp);
}

function createKeyPair($username) {
	if($username != "") {
		$res = openssl_pkey_new();
		
		/* Extract the private key from $res to $privKey */
		openssl_pkey_export($res, $privKey);
		
		/* Extract the public key from $res to $pubKey */
		$pubKey = openssl_pkey_get_details($res);
		
		$pubKey = $pubKey["key"];
		
		$file = "data/keypairs/" . $username . ".txt";
		file_put_contents($file, $pubKey);
		
		echo $file . "<br />";
		echo $pubKey . "<br />";
		echo "Key Created! <br />";
	}
	else {
		echo "Error";
	}
}

function signCertificate($username, $commonName, $org, $orgUnit, $city, $state, $country, $secret) {
	$dn = array("commonName" => $commonName, "org" => $org, "orgUnit" => $orgUnit, 
				"city" => $city, "state" => $state, "country" => $country);
	
	$privkeypass = $secret;
	$numberofdays = 365;
	
	$privkey = openssl_pkey_new(array('private_key_bits' => 1024,'private_key_type' => OPENSSL_KEYTYPE_RSA));
	$csr = openssl_csr_new($dn, $privkey);
	$sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);
	openssl_x509_export($sscert, $publickey);
	openssl_pkey_export($privkey, $privatekey, $privkeypass);
	openssl_csr_export($csr, $csrStr);
	
// 	$file = "data/keypairs/" . $username . ".txt";
// 	file_put_contents($file, $privkey);
	
// 	$fp = fopen("../PKI/private/$userid.key","w");
// 	fwrite($fp,$privatekey);
// 	fclose($fp);
	$file = "data/certificates/" . $username . ".txt";
	file_put_contents($file, $publickey);
}

function loadUserCertificate($user) {
	$cerFile = $user . ".txt";
	$directory = "data/certificates/";
	$fp = fopen($directory . $cerFile, 'r') or die("File Cannot Open");

	$line = fgetss($fp);
	$linearray = explode(":",$line);

	$_POST['commonName'] = $linearray[0];
	$_POST['org'] = $linearray[1];
	$_POST['orgUnit'] = $linearray[2];
	$_POST['city'] = $linearray[3];
	$_POST['state'] = $linearray[4];
	$_POST['country'] = $linearray[5];

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









