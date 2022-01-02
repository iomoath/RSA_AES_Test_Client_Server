<?php

error_log(E_ALL);
require_once("inc/functions.php");


# Coming from the client, expecting 'AES params: key, iv'
$headers = array('query' => 'T-avsY8hHzqNtVITALtTMp3Yy1rDzq9rxMIBi4oxIfeyzzNS3Fl7Mw8wc6tnL4wcEOF3_8nHyFyVlspM_U9Mms1JYpkMP27ZJoUUmbWpE0TZtAp68qCoskCA0ksvDQ692VFTrlfNyOXFfRA4oU2yWHrWBakQY8rpTu1XqGRii7GXqXC5d9RemEol3GlhxiKlU1rO2_vweezGsA5edM5FuiEoR_IyduxBe0lM2kqg8pXlM1rz06UDxcmtaGwp9uRyuM-TWNTWcR774Hz80BO4gOCcDMC3HABOOFTQfcZWkjwjxkNfK8KisSLmqYgshfy9DZM9nucxSGvl3B7iG6d3IA==');


$query_data = $headers['query'];
$query_data = Base64UrlDecode($query_data);
$query_data  = decrypt_rsa(RSA_PRIVATE_KEY, $query_data);


$aes_keys = json_decode($query_data, true);
$client_uuid = 'c21120ff-3a3e-44ad-8b4b-6680711320ac';

echo "Key: " . $aes_keys['key'] . "<br>";
echo "IV: " . $aes_keys['iv'] . "<br>";
echo "CLIENT UUID: " . $client_uuid;


$session_info = array('key' => $aes_keys['key'], 'iv' => $aes_keys['iv'], 'clientId' => $client_uuid);
$session_info = json_encode($session_info);
file_put_contents('sessions_db.json', $session_info);



