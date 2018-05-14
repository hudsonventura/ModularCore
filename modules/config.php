<?php
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


//Configurações basicas
$coreConfig['timezone'] = 'America/Cuiaba';
$coreConfig['environment'] = 'DEV'; //MUST BE 'PRD' TO HIDE ERRORS. To other values, the errors will be show. 
$coreConfig['console_show'] = true;


//Configurações do roteamento
$coreConfig['default_module'] = 'home';
$coreConfig['default_controller'] = 'start';
$coreConfig['default_function'] = 'index';





//DEFAULT ENCRYPTION
$coreConfig['encryption_algorithm'] = 'BF-ECB';
$coreConfig['encryption_key'] = 'amaggi@2015';


//Configuração dos bancos de dados
$coreConfig['databases']['default_database'] = 'postgres';

//PRD PRODUCAO
$cscPRD = array();
$cscPRD['vendor'] = 'pgsql';
$cscPRD['host'] = '172.12.12.59';
$cscPRD['schema'] = 'public';
$cscPRD['dbname'] = 'noname'; //OU OWNER/ DONO
$cscPRD['port'] = '5432';
$cscPRD['user'] = 'csc';
$cscPRD['pass'] = 'csc@2015';
$coreConfig['databases']['cscPRD'] = $cscPRD;









//ACTIVE DIRECTORY
$coreConfig['ActiveDirectory'] = array();
$coreConfig['ActiveDirectory']['Maggi Corp']['host'] = '172.12.12.172';
$coreConfig['ActiveDirectory']['Maggi Corp']['domain'] = 'maggi.corp';
$coreConfig['ActiveDirectory']['Maggi Corp']['admin_user'] = 'suporte.unlockuser';
$coreConfig['ActiveDirectory']['Maggi Corp']['admin_pass'] = 'w42@$809';



//SESSION
$coreConfig['session_name'] = 'MVCore-Session';
$coreConfig['session_expire_time'] = '60'; // in minutes




//EMAIL
$coreConfig['email_smtp_host'] = 'webmail.grupomaggi.com.br';
$coreConfig['email_smtp_port'] = '25';
$coreConfig['email_from_addr'] = '';
$coreConfig['email_from_name'] = 'Sistema de Monitoramento de Links';
$coreConfig['email_login'] = 'suporte.unlockuser';
$coreConfig['email_pass'] = 'w42@$809';