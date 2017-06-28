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
		$libCore = CORE.'libs\\'.$lib.'.php';
		if(file_exists($libCore)){
			$class = 'ModularCore\\'.$lib;
		}else{
			//BUSCA BIBLIOTECAS NA PASTA MODULES/modulousuario/LIBS
			$libCore = MODULEFOLDER.'\\libs\\'.$lib.'.php';
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
			//echo '<h2>ERROR 403 - FORBIDDEN</h2> We can\'t found the class <b>'.$class.'</b> in the file <b>'.debug_backtrace()[0] ['file'].'</b> in the line <b>'.debug_backtrace()[0] ['line'].'</b>';

			//die();
		}
	}



}