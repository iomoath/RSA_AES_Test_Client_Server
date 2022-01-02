<?php

require_once("inc/functions.php");


if(!isset($_SERVER['HTTP_QUERY']))
{
	// Handle invalid requests
	exit('0');
}

# Decrypt the RSA message
$query_data = $_SERVER['HTTP_QUERY'];

$query_data = Base64UrlDecode($query_data);
$query_data  = decrypt_rsa(RSA_PRIVATE_KEY, $query_data);
if($query_data === NULL)
{
	// Handle invalid requests
	exit('1');
}

# Generate a client unique identifier
$client_uuid = gen_uuid();

# Store the session keys towards the UUID locally. For this demo app, we will just use a simple JSON file as our DB!
$aes_keys = json_decode($query_data, true);
$session_info = array('key' => $aes_keys['key'], 'iv' => $aes_keys['iv'], 'clientId' => $client_uuid);
$session_info = json_encode($session_info);
file_put_contents('sessions_db.json', $session_info);


# Prepare the response
$data = array(
	'clientId' => $client_uuid,
	'message' => 'OK',
	'result' => 200
);

# Sign the information
$data_signed = Signature::sign_JSON($data, RSA_PRIVATE_KEY);

$response_data_json = json_encode($data_signed);
$response_data_json = encrypt_aes256($response_data_json, $aes_keys['key'], $aes_keys['iv']);
header("Content-Type: text/plain; charset=UTF-8");

# Respond and exit
exit($response_data_json);


