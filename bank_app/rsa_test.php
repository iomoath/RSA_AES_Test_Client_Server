<?php
error_log(E_ALL);

require_once("inc/config.php");
require_once("inc/aes_crypto.php");
require_once('inc/phpseclib1.0.16/Crypt/RSA.php');
require('inc/Signature.php');


function generate_rsa_keypair()
{
	# http://phpseclib.sourceforge.net/rsa/examples.html
	$rsa = new Crypt_RSA();
	extract($rsa->createKey(2048)); // == $rsa->createKey(1024) where 1024 is the key size

	$pair = array();
	$pair['public_key'] = $publickey;
	$pair['private_key'] = $privatekey;
	return $pair;
}


function get_public_key_in_xml_format($private_key)
{
	$rsa = new Crypt_RSA();
	$rsa->loadKey($private_key);
	$publickey = $rsa->getPublicKey(CRYPT_RSA_PUBLIC_FORMAT_XML);
	return $publickey;
}


$rsa_keys = generate_rsa_keypair();

echo $rsa_keys['public_key'];
echo "\r\n";
echo "\r\n";
echo get_public_key_in_xml_format($rsa_keys['public_key']);
echo "\r\n";
echo "\r\n";
echo $rsa_keys['private_key'];