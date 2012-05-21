<?php 
//
//      Gateway Test Routine
//
//      This is the "Hello World" of online payment processing.
//
//      Save this file as "gwtest.php" in your yallara web site
//      Fill in the values below with your authentications details
//      as per the TCLink access page where you got this file.
//
//      Fill in associative array 'params'.
 	$params['custid'] = 's3318730';
 	$params['password'] = '8776e39c09';
 	$params['demo'] = 'y';

    $params['action'] = 'sale';
    $params['media'] = 'cc';
    $params['cc'] = '4444111144441111';
    $params['exp'] = '0412';
    $params['amount'] = '100';
    $params['name'] = 'Gary Chen';

// 	Print out the parameters in a formatted way
// 	print_r($params);


    // include a Third party Object which allows you to send HTML forms
    // from PHP (Google: "php snoopy" for a copy of it)
    // store the class file in the same place as this file and
    // ensure access ("chmod o+r gwtest.php snoopy.class.php")

    include "Snoopy.class.php";
    $snoopy = new Snoopy;
    $submit_url = "http://goanna.cs.rmit.edu.au/~ronvs/TCLinkGateway/process.php";

    // Now submit the web form and catch any output from the submission
    if(!($snoopy->submit($submit_url, $params)))
		die("Failed fetching document: ".$snoopy->error."\n");

    // Get the output, store in $results
    $result=$snoopy->results;

//  Output will be 'serialized'. Use line below to undo this.
 	$result=unserialize($snoopy->results);

	print_r($result);
?>