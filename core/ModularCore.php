<?php
namespace ModularCore;


if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');

/*Starts the meter of BENCHMARK LIB*/
$coreStartTime = microtime(true);


/* Array config */
$coreConfig = array();
$coreConfig ['startTime'] = $coreStartTime;



/*DEFINE AS CONSTANTES RAIS DO SISTEMA */
define('CORE', __DIR__.'/');
if(strpos(CORE, '\\') === false){
	//server linux
	$tmp = explode('/', __DIR__);
	array_pop($tmp);
	define('BASE', implode('/',$tmp));
	define('BASEFOLDER', implode('/',$tmp).'/');
}else{
	//server windows
	$tmp = explode('\\', __DIR__);
	array_pop($tmp);
	define('BASE', implode('\\',$tmp));
	define('BASEFOLDER', implode('\\',$tmp).'\\');
	
}



/* VARRE O DIRETORIO DE MODULOS*/
$path ='modules/';
$dir = dir($path);
$modules = array();
while($file = $dir -> read()){
	if($file<> '.' && $file<> '..'){
		if(is_dir($path.$file)){
			array_push($modules, $file);
		}
	}
} //DISPONIBILIZA OS MODULOS ENCONTRADOS NO ARRAY CONFIG
$coreConfig['modules'] = $modules;







/* DEFINE A VAR COREVARS E SEUS VALORES E REDIRECIONA PARA O MODULO CORRETO */
if(isset($_GET['CoreVars'])){
	$CoreVars = $_GET['CoreVars'];
	$CoreVars = explode('/', trim($_GET['CoreVars']));
	if($CoreVars[0] == ''){
		array_pop($CoreVars);
	}
	if(!isset($CoreVars[1])){

		require_once(BASE.'/modules/config.php');

		$location = 'Location: '.$CoreVars[0].'/'.$coreConfig['default_controller'];
		header($location);
		die("<script>Console.Log('Redirecting to $location')</script>");
	}

		/* PROCURA O ARQUIVO CONFIG.PHP */
		$file = BASE.'/modules/'.$CoreVars[1].'/config.php';
		require_once(BASE.'/modules/config.php');
		if(file_exists($file)){
			require_once($file);
		}



}else{
	/* DEFINE THE  coreModule */
	require_once(BASE.'/modules/config.php'); //TODO: criar uma excessão caso o arquivo de configuração nao esteja acessivel
	$coreModule = $coreConfig['default_controller'];
	if(isset($CoreVars) && $CoreVars[0] != null)
		$coreModule = @$CoreVars[0];
	else
		$coreModule = 'default';

	$coreConfig['coreModule'] = $coreModule;

	/* PROCURA O ARQUIVO CONFIG.PHP */
	//echo $coreModule;
	$file = BASE.'\modules\\config.php';

	if(file_exists($file)){
		require_once($file);
	}
	$location = 'Location: '.$coreConfig['default_module'].'/'.$coreConfig['default_controller'];
	header($location);
	die("<script>Console.Log('Redirecting to $location')</script>");
}


/* DEFINE THE  coreModule */
$coreModule = 'default';
if(isset($CoreVars) && @$CoreVars[0] != null){
	$coreModule = @$CoreVars[0];
}
$coreConfig['coreModule'] = $coreModule;






/* PROCURA O ARQUIVO CONFIG.PHP */
$file = BASE.'/modules/'.$coreModule.'/config.php';
if(file_exists($file)){
	require_once($file);
}
else
{
	require_once(BASE.'/modules/config.php');
}








/* SET PUBLICDIR FROM DEFAULT MODULE */
$dir = explode('/', $_SERVER['PHP_SELF']);
array_pop($dir);
array_shift($dir);
$PUBLICDEFAULTDIR = '/'.implode('/', $dir).'/modules/default/views/';



/*Corrige caso o sistema esteja no diretorio raiz*/
if(substr($PUBLICDEFAULTDIR, 0, 2) == '//'){
	$PUBLICDEFAULTDIR = substr($PUBLICDEFAULTDIR, 1);
}



$baseSystemAddress = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'] ;

$PUBLICDEFAULTDIR = $baseSystemAddress.$PUBLICDEFAULTDIR;
define('DEFAULTVIEWDIR', $PUBLICDEFAULTDIR);
define('BASESYSTEMADDRESS', $baseSystemAddress);




/*Corrige caso o sistema esteja no diretorio raiz*/
array_pop($dir);array_pop($dir);
//define('PUBLICMODULEDIR', implode('/', $dir).'/'.$coreModule.'/public/');



/* SET THE BASE SYSTEM DIR AND THE BASE MODULE DIR */
$url1 = @explode("/", $_SERVER['REDIRECT_URL']);
$url2 = @explode("/", $_SERVER['SCRIPT_NAME']);
$baseDir = '';
for($i=0; $i<= count($url1)-1; $i++){
	if(in_array($url1[$i], $url2)){
		if($url1[$i] > '' && $url1[$i]!= 'default'){
			$baseDir.= '/'.$url1[$i];
		}
	}
}







/*LOAD THE BASE OF SYSTEM CORE*/
include(CORE.'Core.php');






$mod = implode('/',$tmp);
define('MODULEFOLDER', $mod.'/modules/'.$coreModule);
define('ATUALMODULE', $coreModule);

define('DEFAULTFOLDER', $mod.'/modules/default/');


//define('MODULEPUBLIC', MODULEFOLDER.'\\public\\');
//define('MODULEVIEW', MODULEFOLDER.'\\'.$coreConfig['views']);
//define('DEFAULTPUBLIC', DEFAULTFOLDER.'public\\');
//define('DEFAULTVIEW', DEFAULTFOLDER.$coreConfig['views'].'\\');


/* SET THE ENVIRONMENT OPTIONS BY THE CONFIG FILE */
/* SET THE TIME ZONE BY THE CONFIG FILE */
date_default_timezone_set($coreConfig['timezone']);

/* DISABLE THE ERRORS IF THE SYSYEM IS IN PRODUCTION*/
if($coreConfig['environment'] == 'PRD'){
	error_reporting(0);
}else{
	error_reporting(E_ALL);
}


$baseDir = substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF'])-9);
define('BASEDIR', $baseDir);

//GERA O MODULE
if(isset($_GET['CoreVars'])){
$params = explode('/',$_GET['CoreVars']);
	if($params){
		if(file_exists(BASEFOLDER.'modules/'.$params[0])){
			$coreModule = $CoreVars[0];
		}else{
			$coreModule = $coreConfig['default_controller'];
		}	
	}
	
}
else
	$coreModule = '';


//GERA O CONTROLLER
if(isset($params[1]) && $params[1] != null){
	$coreController = $params[1];
}else{
	$location = 'Location: '.BASEDIR.$coreModule.'/'.$coreConfig['default_controller'];
	header($location);
	die("<script>Console.Log('Redirecting to $location')</script>");
	$coreController = $coreConfig['default_controller'];
}

define('MODULEDIR', $baseDir.$coreModule);
define('CONTROLLERDIR', $baseDir.$coreModule.'/'.$coreController);
define('ATUALCONTROLLER', $coreController);


//GERA O CORE FUNCTION
if(isset($params[2])){
	$coreFunction = $params[2];
}else{
	$coreFunction = $coreConfig['default_function'];
}

if($coreModule == 'core' || $coreModule == 'modules'){
	//header('Location: '.$coreConfig['default_module']);
	//die("<script>Console.Log('Redirecting to $location')</script>");
}

/*  SISTEMA DE ROTEAMENTO */
if($params[0]){ // se algum controller for especificado

	if($coreModule){

			//ELIMINA OS DADOS DO PARAMS NAO USADOS (MODULO, CONTROLLER E FUNCTION)
			try{
				@array_shift($params);
				@array_shift($params);
				@array_shift($params);
			}catch(Exception $e){

			}

			//ESCOLHE UM ARQUIVO PARA DAR UM REQUIRE
			$file = null; //zera a variavel
			$file = BASE.'/modules/'.$coreModule.'/controllers/'.$coreController.'.php';
			if(!file_exists($file)){
				$tmpDir = BASE.'/modules/'.$coreModule.'/controllers';
				$file = $tmpDir.'/'.$coreConfig['default_controller'].'.php';
			}

			//PROCURA O ARQUIVO 404 CASO O ARQUIVO CONTROLLER DESEJADO NAO EXISTA
			if(!file_exists($file)){
				$file = BASE.'/modules/'.$coreConfig ["default_module"].'/controllers/error404.php';


				if(file_exists($file)){
					require $file;
					define('ATUALFUNCTION', 'index');
					$error404 = new \Error404();
					$error404->index();
				}else{
					$file = BASE.'/core/errors/404.php';
					require $file;
				}


			}else{

				require_once($file);
				//define('CONTROLLERDIR', MODULEDIR.'/'.strtolower($coreModule));
				//CRIA CONSTANTE ASSETS PARA SER UTLIZADO NAS REFERENCIAS DEPENDENCIAS


				//Apache
				define('ASSETS', BASEDIR.'modules/'.$coreModule.'/views/assets/');




				$class = $coreController;
				if(class_exists($class) === true){
					//ESCOLHE E CHAMA A FUNÇÃO
					if(!method_exists($class, $coreFunction)){
						if(!method_exists($class, $coreConfig['default_function'])){
							//var_dump($file);
							$coreFunction = 'functionNotFound';
						}else{
							$coreFunction = $coreConfig['default_function'];
						}
					}



					define('ATUALFUNCTION', $coreFunction);
					define('FUNCTIONDIR', $baseDir.$coreModule.'/'.$coreController.'/'.$coreFunction);
				}else{
					include ('errors/404.php');
					die();
				}
				$get = $_GET;
				unset($get['CoreVars']);
				foreach ($get as $key => $value) {
					if ($value == '') {
						$get[$key] = null;
					}
				}

				$controller = new $class();
				$controller->$coreFunction($get); //die();
			}
	}

	/*WRITE THE TIME PROCESS OF PAGE TO CONSOLE */
	//$coreBenchstart = microtime() - $coreBenchstart;
	//$console = $console.'<br /><br /><br />------------<br />Generated page in '.number_format($coreBenchstart, 2).'s'.'<br />------------<br />';

}
