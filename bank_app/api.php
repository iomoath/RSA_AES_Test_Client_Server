<?php
require_once("inc/functions.php");

if(!isset($_SERVER['HTTP_CLIENTID']) || !isset($_POST['data']))
{
	// Handle invalid requests
	exit('0');
}

# Decrypt the RSA message, and get the client UUID
$client_uuid = $_SERVER['HTTP_CLIENTID'];
$client_uuid = Base64UrlDecode($client_uuid);
$client_uuid  = decrypt_rsa(RSA_PRIVATE_KEY, $client_uuid);

if($client_uuid === NULL)
{
	// Handle invalid requests
	exit('1');
}

# Verify the client UUID, match with server session DB
$session_info = file_get_contents('sessions_db.json');
$session_info = json_decode($session_info, true);

if($session_info['clientId'] !== $client_uuid)
{
	// Invalid session ID
	exit('2');
}

# Decrypt the body
$data = decrypt_aes256($_POST['data'], $session_info['key'], $session_info['iv']);

if($data === NULL)
{
	// Decryption failed or empty body
	exit('3');
}

# Prepare the response
$data = array(
	'message' => 'Well received!',
	'result' => 200
);

# Sign the information
$data_signed = Signature::sign_JSON($data, RSA_PRIVATE_KEY);
$response_data_json = json_encode($data_signed);
$response_data_json = encrypt_aes256($response_data_json, $session_info['key'], $session_info['iv']);
header("Content-Type: text/plain; charset=UTF-8");
exit($response_data_json);
