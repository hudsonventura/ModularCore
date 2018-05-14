<?php
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');

class Start extends ModularCore\Controller{
	
	function __construct() {
		parent::__construct();
	}	
	
	function index(){
		$this->loadView('start');
	}
	
}