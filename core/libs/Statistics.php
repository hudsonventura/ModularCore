<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


class Statistics extends Core{
	
	function __construct() {
		parent::__construct();
		
		$table = 'lib_statistics';
		$sqlite = new sqlite($table);
		$this->sqlite = $sqlite;
		$data = $sqlite->select();
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
			$this->language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}else{
			$this->user_agent = 'UPDATING';
			$this->language = 'UPDATING';
		}
			
			
		if(!$data){
			$stmt = "CREATE TABLE IF NOT EXISTS $table (
						id INTEGER NOT NULL,
						ip TEXT(15) NOT NULL,
						user_agent TEXT(100) NOT NULL,
						language TEXT(100) NOT NULL,
						first_access TEXT(19) NOT NULL,
						last_access TEXT(19) NOT NULL,
						PRIMARY KEY ('id')
					);";
			$sqlite->exec($stmt);
		}
		
		$data = $sqlite->selectWhere('ip = "'.$_SERVER['REMOTE_ADDR'].'"');
		if(!$data){
			$this->insert();
		}else{
			
			
			
			if($data['user_agent']!= $this->user_agent){
				$this->insert();
			}else{
				$this->update($data['id']);
			}
		}

	}
	
	private function insert(){
		$array = array();
		$array['ip'] = $_SERVER['REMOTE_ADDR'];
		$array['user_agent'] = $this->user_agent;
		$array['language'] = $this->language;
		$array['first_access'] = date('Y-m-d H:i:s');
		$array['last_access'] = date('Y-m-d H:i:s');
		$this->sqlite->insert($array);
	}
	
	private function update($id){
		$array = array();
		$array['last_access'] = date('Y-m-d H:i:s');
		$this->sqlite->update($array, $id);
	}
}