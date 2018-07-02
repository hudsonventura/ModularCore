<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');

require_once(CORE.'Controller.php');
require_once(CORE.'Model.php');


abstract class Core {

	public static $coreConfig;
	protected $params;
	protected $libs;
	protected static $console;

	function __construct() {
		core::$coreConfig = &$GLOBALS['coreConfig'];
		$this->params = $GLOBALS['params'];
		$this->libs = new \StdClass();
	}



	protected function loadLib($lib, $name = null){

		if($name == null){
			$name = $lib;
		}
		//BUSCA BIBLIOTECAS NA PASTA CORE/LIBS
		$libCore = CORE.'libs/'.$lib.'.php';
		if(file_exists($libCore)){
			$class = 'ModularCore\\'.$lib;
		}else{
			//BUSCA BIBLIOTECAS NA PASTA MODULES/modulousuario/LIBS
			$libCore = MODULEFOLDER.'/libs/'.$lib.'.php';
			if(file_exists($libCore)){
				$class = $lib;
			}else{
				include 'errors/400.php';
				die();
				echo '<h2>ERROR 404 - LOST LIBRARY</h2> Fail when open the LIB <b>'.$lib.'</b> in the file <b>'.debug_backtrace()[0] ["file"].'</b> in the line <b>'.debug_backtrace()[0] ["line"].'</b>';

			}

		}
		require_once($libCore);

		if(class_exists($class)){
			$this->libs->$name = new $class;
		}else{
			echo '<h2>ERROR 403 - FORBIDDEN</h2> We can\'t found the class <b>'.$class.'</b> in the file <b>'.debug_backtrace()[0] ['file'].'</b> in the line <b>'.debug_backtrace()[0] ['line'].'</b>';

			die();
		}
	}

	public function array_msort($array, $cols)
	{
		$colarr = array();
		foreach ($cols as $col => $order) {
			$colarr[$col] = array();
			foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
		}
		$eval = 'array_multisort(';
		foreach ($cols as $col => $order) {
			$eval .= '$colarr[\''.$col.'\'],'.$order.',';
		}
		$eval = substr($eval,0,-1).');';
		eval($eval);
		$ret = array();
		foreach ($colarr as $col => $arr) {
			foreach ($arr as $k => $v) {
				$k = substr($k,1);
				if (!isset($ret[$k])) $ret[$k] = $array[$k];
				$ret[$k][$col] = $array[$k][$col];
			}
		}
		return $ret;
	}

	public function removeSpecialChar($string){
		$what = array( 'á','à','ã','ä','â','é','è','ë','ê','í','ì','ï','ó','ò','ô','ö','õ','ú','ù','û','ü','Á','À','É','Í','Ó','Ú','ñ','ç','Ç','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','�','�' );
		$by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','E','I','O','U','n','c','C',' ',' ',' ',' ',' ',' ',' ',' ','$','%',' ',' ',' ',' ',' ',' ','_','_','_','_' );
		return str_replace($what, $by, $string);
	}

	function onlyAlphaNumeric($string){
		$what = array(' ','-','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º','_','.');
		$by   = array('','','','','','','','','','','','','','','','','','','','','','','','','');
		return trim(str_replace($what, $by, $this->removeSpecialChar($string)));
	}
	

	
	protected function loadModel($model, $name = null){

		if($name == null){
			$name = $model;
		}

		//BUSCA MODELS NA PASTA MODULES/modulousuario/MODELS
		$modelCore = MODULEFOLDER.'/models/'.$model.'.php';
		if(file_exists($modelCore)){
			$class = $model;
		}else{

			//BUSCA MODELS NA PASTA MODULES/DEFAULT/MODELS
			$modelCore = DEFAULTFOLDER.'models/'.$model.'.php';
			if(file_exists($modelCore)){
				$class = $model;
			}else{
				//include 'errors/402.php';
				echo '<h2>ERROR 402 - LOST MODEL</h2> Fail when open the MODEL <b>'.$model.'</b> in the file <b>'.debug_backtrace()[0] ["file"].'</b> in the line <b>'.debug_backtrace()[0] ["line"].'</b>';
				die();
			}

		}


		require_once($modelCore);
		if(class_exists($class)){
			if(!isset($this->models)){
				$this->models = new \stdClass();
			}
			
			$this->models->$name = new $class($name);
			$this->models->$name->vars($this);
		}else{
			//include 'errors/402.php';
			echo '<h2>ERROR 403 - FORBIDDEN</h2> We can\'t found the class <b>'.$class.'</b> in the file <b>'.debug_backtrace()[0] ['file'].'</b> in the line <b>'.debug_backtrace()[0] ['line'].'</b>';
			die();
		}
	}



}

