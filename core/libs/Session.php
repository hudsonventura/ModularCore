<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');

class Session extends Core{

	private $temp = array();

	function __construct(){
		parent::__construct();
	
	if(!isset($_SESSION)){
		session_start(core::$coreConfig['session_name']);
	}
	
	if(count($_SESSION)<=1){
		$this->setData('session_expire_time', time());
	}else{
		if((core::$coreConfig['session_expire_time']*60)-(time() - $this->getData('session_expire_time')) <= 0){
			if(!isset($_SESSION)) {
				session_start(core::$coreConfig['session_name']);
			}else{
				header ('Location:'.MODULEDIR.'/'.core::$coreConfig['controller_default']);
			}
			
			$this->cleanSession();
		}else{
			$this->setData('session_expire_time', time());
		}
	}

	
	//consoleWrite($this->cleanTemp());
	//consoleWrite($this->getData());
    }
    
    public function getData($string = null){
	if(isset($_SESSION[$string]))
	    return $_SESSION[$string];
	else
	    return false;
    }
    
    public function get(){
	return $_SESSION;
    }
    
    public function set($array){
	$_SESSION = $array;
	return true;
    }
    
    public function setData($string, $value){
	if(isset($string) && isset($value)){
	    $_SESSION[$string] = $value;
	    //consoleWrite('Adding session '.$string.' => '.$value);
	    return true;
	}else{
	    return false;
	}
        
    }
    
    public function cleanData($string = null){
        if(isset($_SESSION[$string]))
		session_unset($_SESSION[$string]);
	else
		session_unset($_SESSION);
    }
    
	public function cleanSession(){
		session_destroy();
		return true;
	}
    
    public function setTemp($string, $value){
        $_SESSION['temp_'.$string] = $value;
        return true;
    }
    
    public function getTemp($string){
		if(isset($_SESSION['temp_'.$string])){
		    $return = $_SESSION['temp_'.$string];
		    unset($_SESSION['temp_'.$string]);
		    return $return;
		}
		else{
		    return false;
		}
    }
    
    public function cleanTemp(){
	$array[0] = 'Clean session temp items:';
        if(isset($_SESSION['temp_life_time'])){
            foreach($_SESSION as $key => $value){
                if(strpos($key, 'temp_')===0){
		    consoleWrite('Clean temp session '.$key);
                    $this->temp = array_merge($this->temp, array($key => $value));
                    unset($_SESSION[$key]);
                }
            }
        }
	return array_merge($array, $this->temp);
    }
}
