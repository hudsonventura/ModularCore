<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');



class Console extends Core{
    function __construct() {
        parent::__construct();
    }

    public function write($string){
		if(core::$coreConfig['console_show'] === true){
			core::$console .= "<script>";
			if($string <> ''){

				if(!is_array($string) && !is_object($string)){
					core::$console .= "console.log('$string');";
				}else{
					if(is_array($string)){
						$this->writePart('Array Begin --- \\n');
						$this->recursiveArray($string);
						$this->writePart('Array End --- \\n \\n');
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

	private function recursiveArray($array){

		foreach($array as $key => $item){
			if(is_array($item)){
				$this->writePart('\\t'.$key.' => ');
				$this->recursiveArray($item);
			}else{
				if(is_string($item) || is_numeric($item)){
					$string = str_replace(" ","_",preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($item))));
					$this->writePart($string);
				}else{
					$this->writePart('strange value');
				}
			}
		}
		$this->writePart('\\n');
	}


	 private function writePart($string){
		if($string <> ''){
			core::$console .= "console.log('$string');";
		}else{
			core::$console .= "console.log('null');";
		}
	}

}
