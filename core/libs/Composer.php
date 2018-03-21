<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');



class Composer extends Core{
    function __construct() {
        parent::__construct();
        
    }

    public function autoload($module = null){
        if(!$module){

            $autoload = MODULEFOLDER."vendor/autoload.php";
            if (file_exists($autoload)) {
                require_once $autoload;
            }

            $autoload = BASEFOLDER."modules/vendor/autoload.php";
            if (file_exists($autoload)) {
                require_once $autoload;
            }
            return true;
        }

        if ($module == 'modules') {
            $autoload = BASEFOLDER."modules/vendor/autoload.php";
            if (file_exists($autoload)) {
                require_once $autoload;
            }
            return true;
        }else{
            $autoload = BASEFOLDER."modules/$module/vendor/autoload.php";
            if (file_exists($autoload)) {
                require_once $autoload;
            }
        }


        
    }
    

}
