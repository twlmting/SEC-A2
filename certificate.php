<?php 
// 	$privateKey = openssl_pkey_new(array('private_key_bits' => 2048));
// 	$details = openssl_pkey_get_details($privateKey);
// 	$publicKey = $details['key'];
	
// 	echo $publicKey;
	
/* Create the private and public key */
$res = openssl_pkey_new();

/* Extract the private key from $res to $privKey */
openssl_pkey_export($res, $privKey);

/* Extract the public key from $res to $pubKey */
$pubKey = openssl_pkey_get_details($res);
$pubKey = $pubKey["key"];

echo $pubKey;
?>

