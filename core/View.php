<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');

class View extends Core{

	private $viewVars;
	private $file;

	function __construct($file, $coreView) {
		parent::__construct();
		$this->file = $file;
		$this->viewVars = $coreView;
	}


	public function show(&$controller){

		if(count($this->viewVars)>0){
			foreach($this->viewVars as $key => $value){
				$$key = $value;
			}
		}

		unset($GLOBALS['controller']);
		unset($GLOBALS['model']);
		$this->viewVars = array();


		$viewVars = $this->viewVars;
		include $this->file;
	}

	public function get(&$controller){

		if(count($this->viewVars)>0){
			foreach($this->viewVars as $key => $value){
				$$key = $value;
			}
		}

		unset($GLOBALS['controller']);
		unset($GLOBALS['model']);
		$this->viewVars = array();


		$viewVars = $this->viewVars;
		include $this->file;
		$content = ob_get_clean();
		echo $content;
		return $content;
	}
}