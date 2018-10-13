<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');



class Console extends Core{
    function __construct() {
        parent::__construct();
    }

    public function write($string){
		
		if(core::$coreConfig['console_show'] === true){
			$log = '';
			$logArray = '';
			if($string <> ''){

				if(!is_array($string) && !is_object($string)){
					$log .= $string;
				}else{

					if(is_array($string)){
						$log .= 'Array';
						$logArray .= '\\nArray Begin --- \\n';
						$logArray .= $this->recursiveArray($string);
						$logArray .= '\\n\\nArray End --- \\n \\n';
					}
					
					if(is_object($string)){
						$log .= 'Object';
						$logArray .= '\\nObject Begin --- \\n';
						$logArray .= 'This is not compatible with objects yet.'; //TODO: Do console be compatible with objects
						$logArray .= '\\n\\nObject End --- \\n \\n';
					}

				}
			}else{
				$log .= "null";
			}
			echo "<script>console.log('Console.log: --##> $log <##-- $logArray ".str_replace('\\', '\\\\', debug_backtrace()[0] ["file"])." at line ".debug_backtrace()[0] ["line"]."\\n\\n');</script>";
			
		}
		

	}

	private function recursiveArray($array){

		$return = '';
		

		foreach($array as $key => $item){
			$return .= $key.' => ';
			if(is_array($item)){
					$return .= 'Array\n\\t'.$this->recursiveArray($item);
			}else{
				//if(is_string($item) || is_numeric($item)){
					$return .= $item.'\n';
				//}else{
				//	return $return.'strange value';
				//}
			}
		}
		return $return;
	}




}
