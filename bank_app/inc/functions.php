<?php
// Prevent direct url access to this file
if(count(get_included_files()) === 1) {
	exit;
}

require_once("config.php");
require_once("aes_crypto.php");
require_once('phpseclib1.0.16/Crypt/RSA.php');
require('Signature.php');


function get_http_headers()
{
	$headers = [];
	try {
		foreach($_SERVER as $name => $value) {
		if($name != 'HTTP_MOD_REWRITE' && (substr($name, 0, 5) == 'HTTP_' || $name == 'CONTENT_LENGTH' || $name == 'CONTENT_TYPE')) {
			$name = str_replace(' ', '-', strtolower(str_replace('_', ' ', str_replace('HTTP_', '', $name))));
			if($name == 'Content-Type') $name = 'Content-type';
			$headers[$name] = $value;
			}
		}
	} catch (Exception $e) {
		return $headers;
	}
	return $headers;
}


function decrypt_rsa($private_key, $ciphertext)
{
	$rsa = new Crypt_RSA();
	$rsa->loadKey($private_key); // private key
	$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
	return $rsa->decrypt($ciphertext);
}


function Sha256($msg) {
	return hash('sha256', $msg);
}


function unwrap($text)
{
	$txt = str_replace("\r\n", '', $text);
	$txt = str_replace("\n", '', $text);
	return $txt;
}


function load_keypair($private_key)
{
	# http://phpseclib.sourceforge.net/rsa/examples.html
	$rsa = new Crypt_RSA();
	$rsa->loadKey($private_key);

	$privatekey = $rsa->getPrivateKey();
	$publickey = $rsa->getPublicKey();

	$pair = array();
	$pair['public_key'] = $publickey;
	$pair['private_key'] = $privatekey;
	return $pair;

}

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



function gen_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

		// 16 bits for "time_mid"
		mt_rand( 0, 0xffff ),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand( 0, 0x0fff ) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand( 0, 0x3fff ) | 0x8000,

		// 48 bits for "node"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}

?>
