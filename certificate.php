<?php 
	$privateKey = openssl_pkey_new(array('private_key_bits' => 2048));
	$details = openssl_pkey_get_details($privateKey);
	$publicKey = $details['key'];
	
	echo $publicKey;
	

?>
