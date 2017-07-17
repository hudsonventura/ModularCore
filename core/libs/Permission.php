<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


class Permission extends Core{

	public $required;


	function __construct() {
		parent::__construct();
	}

	function verify($permissions = null){

		$access = $this->recursive($permissions);

		if(!$access){
			//if(!$this->required){
				echo '<h1>ACCESS DENIED!</h1></br>Acesso negado!';
				die();
			//}
		}



	}

	function recursive($permissions){
		if(!is_array($permissions)){
			if(is_array($this->required)){

				foreach($this->required as $require){
					if(strpos($permissions, $require)){
						return true;
					}
				}
			}else{


					if($this->required > '' && strpos($permissions, $this->required)){
						return true;
					}else{
						//return false;
					}
			}
			foreach($this->required as $require){
				if(strpos($permissions, $require)){
					return true;
				}else{
					//return false;
				}
			}
		}else{
			foreach($permissions as $permission){
				$access = $this->recursive($permission);
				if($access == true){
					return true;
				}
			}
		}
		return false;
	}

}