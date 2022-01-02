<?php

function Base64UrlDecode($x)
{
   return base64_decode(str_replace(array('_','-'), array('/','+'), $x));
}
function Base64UrlEncode($x)
{
   return str_replace(array('/','+'), array('_','-'), base64_encode($x));
}


function encrypt_aes256($clear_text, $key, $iv) {
    $iv = str_pad($iv, 16, "\0");
    $encrypt_text = openssl_encrypt($clear_text, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
    $data = Base64UrlEncode($encrypt_text);
    return $data;
}

function decrypt_aes256($data, $key, $iv) {
    $iv = str_pad($iv, 16, "\0");
    $encrypt_text = Base64UrlDecode($data);
    $clear_text = openssl_decrypt($encrypt_text, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
    return $clear_text;
}


function decrypt_file($filePath, $saveLocation, $key, $iv) {
    $fileBuffer = file_get_contents($filePath);
    $iv = str_pad($iv, 16, "\0");
    $cipher = base64_decode($fileBuffer);
    $plain = openssl_decrypt($cipher, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
    file_put_contents($saveLocation, $plain);
}

function encrypt_file($filePath, $saveLocation, $key, $iv) {
    $fileBuffer = file_get_contents($filePath);
    $iv = str_pad($iv, 16, "\0");
    $cipher = openssl_encrypt($fileBuffer, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
    $cipher64 = base64_encode($cipher);
    file_put_contents($saveLocation, $cipher64);
}

?>