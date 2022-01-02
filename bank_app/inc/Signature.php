<?php
class Signature
{

	public static function create_signature($message, $privateKey)
	{
		$rsa = new Crypt_RSA();
		$rsa->loadKey($privateKey); # private key
		$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
		$rsa->setHash('sha256');

		$signature = $rsa->sign($message);
		$signature = Base64UrlEncode($signature);
		return $signature;

	}

	function verify_signature($public_key, $plaintext, $signature)
	{
		$signature = Base64UrlDecode($signature);
		$rsa = new Crypt_RSA();
		$rsa->loadKey($public_key); // public key
		$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
		$rsa->setHash('sha256');
		return $rsa->verify($plaintext, $signature)? true : false;
	}

	public static function sign_JSON($jsonToSign, $privateKey)
	{
		if(gettype($jsonToSign) != 'string')
			$jsonToSign = json_encode($jsonToSign);

		$signature = self::create_signature($jsonToSign, $privateKey);
		return array('data' => $jsonToSign, 'signature' => $signature);
	}

	public static function verify_JSON_signature($jsonObject, $publicKey)
	{
		if(gettype($jsonObject->data) == 'string')
			throw new Exception('Value $jsonObject->data must be a String, is a ' . gettype($jsonObject->data));

		return self::verify_signature($jsonObject->data, $publicKey, $jsonObject->signature);
	}
}

?>