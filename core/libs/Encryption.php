<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


class Encryption extends Core{


	function __construct(){
		parent::__construct();
	}

	public function encrypt($string, $key = null){
		if(!$key){
			$key = core::$coreConfig['encryption_key'];
		}
		$encrypt = mcrypt_encrypt(MCRYPT_BLOWFISH, strrev($key), $string, MCRYPT_MODE_ECB);
		$encrypt2 = mcrypt_encrypt(MCRYPT_BLOWFISH, strrev(MD5($key)), $encrypt, MCRYPT_MODE_ECB);
		return base64_encode($encrypt2);
	}

	public function decrypt($string, $key = null){
		if(!$key){
			$key = core::$coreConfig['encryption_key'];
		}
		$base64 = base64_decode($string);
		$encrypt2 = mcrypt_decrypt(MCRYPT_BLOWFISH, strrev(MD5($key)), $base64, MCRYPT_MODE_ECB);
		return mcrypt_decrypt(MCRYPT_BLOWFISH, strrev($key), $encrypt2, MCRYPT_MODE_ECB);
	}
}


