<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');



class Console extends Core{
    function __construct() {
        parent::__construct();
    }
    
    public function write($string){
		if(core::$coreConfig['environment'] <> 'PRD'){
			core::$console .= "<script>";
			if($string <> ''){
				
				if(!is_array($string) && !is_object($string)){
					core::$console .= "console.log('$string');";
				}else{
					if(is_array($string)){
						foreach($string as $item){
							$this->writePart($item);
						}
					}
					if(is_object($string)){
						core::$console .= 'console.log("'; 
						print_r($string); //TODO: echo de um objeto
						core::$console .= '");';
					}
					
				}
			}else{
				core::$console .= "console.log('null');";
			}
			core::$console .= "console.log('ModularCore Console >> ".str_replace('\\', '\\\\', debug_backtrace()[0] ["file"])." at line ".debug_backtrace()[0] ["line"]."\\n\\n');</script>";
		}
		
    }
	
	 public function writePart($string){
		if($string <> ''){
			core::$console .= "console.log('$string');";
		}else{
			core::$console .= "console.log('null');";
		}
    }

}
