<?php

namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');



class Cache extends Core{

	function __construct() {
		parent::__construct();
	}

	public function getTimestamp($name){
		$file = 'modules/'.core::$coreConfig['coreModule'].'/models/coreCache_'.$name.'.cache';
		if(!file_exists($file)){
			return false;
		}

		$location = 'sqlite:modules/'.core::$coreConfig['coreModule'].'/models/coreCache_'.$name.'.cache';
		$cache = new \PDO($location);

		$sql = "SELECT * FROM TIME;";
		$result = $cache->query($sql);

		if($result){
			$return = array();
			while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
				return $row ['TIMESTAMP'];
			}
		}else{
			$return = null;
		}

		$cache = null;
		return $return;
	}

	public function save($name, $array){
		if(count($array) > 0)
		{
			$location = 'sqlite:modules/'.core::$coreConfig['coreModule'].'/models/coreCache_'.$name.'.cache';
			$cache = new \PDO($location);


			//TABLE TIME
			$timestamp = time();
			$sql = "DROP TABLE TIME;";
			$cache->exec($sql);
			$sql = "CREATE TABLE TIME (TIMESTAMP)";
			$cache->exec($sql);
			$sql = "INSERT INTO TIME ('TIMESTAMP') VALUES ('$timestamp')";
			$cache->exec($sql);






			//DELETE PREVIOUS TABLE
			$sql = "DROP TABLE CACHE;";
			$cache->exec($sql);


			//CREATE TABLE
			$sql = "CREATE TABLE CACHE (";
			foreach($array[0] as $key => $value)
			{
				$sql.= "'$key' TEXT, ";
			}
			$sql = substr($sql, 0, strlen($sql) -2);
			$sql.= ")";
			$cache->exec($sql);


			//INSERT THE NEWS VALUES
			foreach($array as $row)
			{
				$sql = "INSERT INTO CACHE (";
				foreach($row as $key => $value)
				{
					$sql.= "'$key', ";
				}
				$sql = substr($sql, 0, strlen($sql) -2);
				$sql .= ") VALUES (";
				foreach($row as $key => $value)
				{
					$sql.= "'$value', ";
				}
				$sql = substr($sql, 0, strlen($sql) -2);
				$sql.= ")";
				$cache->exec($sql);
			}
		}else{
			return false;
		}

		$cache = null;
	}

	public function delete($name){
		$file = 'modules/'.core::$coreConfig['coreModule'].'/models/coreCache_'.$name.'.cache';
		if(!file_exists($file)){
			return false;
		}
		unlink($file);
		unset($file);
	}


	public function deleteAll(){
		$path = 'modules/'.core::$coreConfig['coreModule'].'/models/';
		$dir = dir($path);
		while($file = $dir -> read()){
			if(strpos($file, 'coreCache_') === false)
			{

			}else{
				$file = 'modules/'.core::$coreConfig['coreModule'].'/models/'.$file;
				unlink($file);
				unset($file);
			}
		}
		$dir -> close();
	}


	public function load($name, $WHERE = null){
		$file = 'modules/'.core::$coreConfig['coreModule'].'/models/coreCache_'.$name.'.cache';
		if(!file_exists($file)){
			return false;
		}

		if($WHERE == null)
		{
			$WHERE = '1=1';
		}

		$location = 'sqlite:modules/'.core::$coreConfig['coreModule'].'/models/coreCache_'.$name.'.cache';
		$cache = new \PDO($location);

		$sql = "SELECT * FROM CACHE WHERE $WHERE;";
		//var_dump($sql); die();
		$result = $cache->query($sql);

		if($result){
			$return = array();
			while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
				array_push($return, $row);
			}
		}else{
			$return = false;
		}

		$cache = null;
		return $return;
	}




}