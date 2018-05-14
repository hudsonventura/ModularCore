<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


class Encryption extends Core{

	private $cipher = 'BF-ECB';

	function __construct(){
		parent::__construct();
	}

	public function encrypt($string, $key = null, $algorithm = null){
		if(!$key) $key = core::$coreConfig['encryption_key'];

		if(!$algorithm) $algorithm = core::$coreConfig['encryption_algorithm'];


		$encrypt = openssl_encrypt($string, $algorithm, strrev($key));
		$encrypt2 = openssl_encrypt($encrypt, $algorithm,strrev(MD5($key)));
		return base64_encode($encrypt2);
	}

	public function decrypt($string, $key = null, $algorithm = null){
		
		if(!$key) $key = core::$coreConfig['encryption_key'];
		

		if(!$algorithm) $algorithm = core::$coreConfig['encryption_algorithm'];
		

		$base64 = base64_decode($string);
		$encrypt2 = openssl_decrypt($base64, $algorithm, strrev(MD5($key)));
		return openssl_decrypt($encrypt2, $algorithm, strrev($key));	
	}
}
