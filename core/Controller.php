<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');

require_once(CORE.'View.php');

abstract class Controller extends Core{

	protected $coreView = array();
	//protected $core = array();
	protected $models;


	function __construct() {
		$origin = explode('/',debug_backtrace()[1]['file']);
		array_pop($origin);
		if(end($origin) == 'views'){
			echo '<h2>ERROR 403 - FORBIDDEN</h2> You can only instantiate a CONTROLLER or MODEL from a file in the CONTROLLER directory in the file <b>'.debug_backtrace()[1] ['file'].'</b> in the line <b>'.debug_backtrace()[1] ['line'].'</b>';
			die();
		}

		
		if (isset($GLOBALS['twigOptions'])) { //declaration that the dev will work this twig
			$this->twigOptions = $GLOBALS['twigOptions'];
			require_once BASEFOLDER.'core/vendor/autoload.php';
			$this->twigLoader = new \Twig_Loader_Filesystem(array(DEFAULTFOLDER.'views/', MODULEFOLDER .'/views/'));
			$this->twig = new \Twig_Environment($this->twigLoader, $this->twigOptions);
			$this->twig->addExtension(new \Twig_Extension_Debug());
		}
		

		parent::__construct();

		$core['module'] = $GLOBALS['coreModule'];
		$core['controller'] = $GLOBALS['coreController'];
		$core['function'] = $GLOBALS['coreFunction'];
		$this->core = $core;
		$this->coreView['core'] = $core;

		//INICIALIZA O OBJETO MODELS
		$this->models = new \StdClass();


		$coreLinks = array();
		$coreLinks['base'] = 		BASEDIR;
		$coreLinks['module'] = 		$coreLinks['base'].$core['module'];
		$coreLinks['controller'] = 	$coreLinks['module'].'/'.ATUALCONTROLLER;
		$coreLinks['function'] = 	$coreLinks['controller'].'/'.ATUALFUNCTION;

		$this->coreView['links'] = $coreLinks;



	}

	public function functionNotFound(){
		$file = BASE.'/modules/'.$this->core['module'].'/controllers/404-2.php';
		if(!file_exists($file)){
			$file = BASE.'/core/errors/404-2.php';
		}
		require $file;
	}


	public function loadView($string){

		$file = BASE.'/modules/'.$this->core['module'].'/views/'.$string.'.php';
		$fileTwig = $string.'.html';
		if(!file_exists($file)){
			$file = BASE.'/modules/default/views/'.$string.'.php';
		}
		if(file_exists($file)){

			//LIMPA TODO O CONTEUDO DA TELA DO USUARIO ANTES DE CARREGAR A VIEW.
			if(core::$coreConfig['environment'] == 'PRD'){
				ob_clean();
			}

			$view = new View($file, $this->coreView);
			$this->coreView = array(); //limpa o array coreView, pois ele estará disponivel na view como $this->viewVars
			$view->show($this);
			echo core::$console;

			die();
		}

		try{
			//LIMPA TODO O CONTEUDO DA TELA DO USUARIO ANTES DE CARREGAR A VIEW.
			if(core::$coreConfig['environment'] == 'PRD'){
				ob_clean();
			}

			$view = new View($fileTwig, $this->coreView, $this->twig);
			$this->coreView = array(); //limpa o array coreView, pois ele estará disponivel na view como $this->viewVars
			$view->show($this);
			echo core::$console;

			die();
		}catch(Exception $e){
			echo '<h2>ERROR 404 - LOST VIEW</h2> Fail when open the VIEW <b>'.$string.'</b> in the file <b>'.debug_backtrace()[0] ["file"].'</b> in the line <b>'.debug_backtrace()[0] ["line"].'</b>';
			die();
		}

	}

	public function getView($string){
		$file = BASE.'/modules/'.$this->core['module'].'/views/'.$string.'.php';
		if(!file_exists($file)){
			$file = BASE.'/modules/default/views/'.$string.'.php';
		}
		if(file_exists($file)){

			//LIMPA TODO O CONTEUDO DA TELA DO USUARIO ANTES DE CARREGAR A VIEW.
			if(core::$coreConfig['environment'] == 'PRD'){
				ob_clean();
			}

			$view = new View($file, $this->coreView);
			$this->coreView = array(); //limpa o array coreView, pois ele estará disponivel na view como $this->viewVars

			return $view->get($this);
		}else{
			echo '<h2>ERROR 404 - LOST VIEW</h2> Fail when open the VIEW <b>'.$string.'</b> in the file <b>'.debug_backtrace()[0] ["file"].'</b> in the line <b>'.debug_backtrace()[0] ["line"].'</b>';
			die();
		}
	}



	public function __destruct(){
		//unset($this);
	}

	

	protected function redirect($location, $time = null){
		if(substr($location, 0, 4) == 'http'){
			$location = "$location";
		}else{
			$location = BASEDIR.$location;
		}
		//header($location);

		if(!$time){
			$time = 0;
		}

		die("<meta http-equiv='refresh' content='$time;url=$location'>");
	}

	protected function refresh($time = null){
		if(!$time){
			$time = 0;
		}

		die("<meta http-equiv='refresh' content='$time'>");
	}

	protected function close(){
		die("<script>window.close();</script>");
	}
}



