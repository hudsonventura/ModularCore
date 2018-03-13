<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');

//require_once(CORE.'Controller.php');


abstract class Model extends Core{


	function __construct() {
		parent::__construct();
		
	}
	
	public function vars(&$controller){
		$this->params = &$controller->params;
		$this->libs = &$controller->libs;
	}
	
	
	protected function loadDb($db = null, $name = null){
		if($db == null){
			$db = core::$coreConfig['databases']['default_database'];
		}
		if($name == null){
			$name = core::$coreConfig['databases']['default_database'];
		}
		
		if(!class_exists('DataBase')){
			try{
				include_once CORE.'libs/DataBase.php';
			}catch(Exception $e){
				echo 'The DataBase.php is not in lib folder. Please make sure that your instalation is ok';
				die();
			}
			
		}
		if(!isset($this->db)){
			$this->db = new \StdClass();
		}
		$this->db->$name = new DataBase($db);
		
	}



}