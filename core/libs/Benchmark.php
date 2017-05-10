<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');



class Benchmark extends Core{
    function __construct() {
        parent::__construct();
        $this->start = core::$coreConfig['startTime'];
    }
    
    public function start(){
        $this->start = microtime();
    }
    
    public function time(){
        return microtime(true) - $this->start;
    }
}
