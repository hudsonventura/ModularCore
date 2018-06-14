<?php
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


//Configurações basicas
$coreConfig['timezone'] = 'America/Cuiaba';
$coreConfig['environment'] = 'DEV'; //MUST BE 'PRD' TO HIDE ERRORS. To other values, the errors will be show. 
$coreConfig['console_show'] = true;


//Configurações do roteamento
$coreConfig['default_module'] = 'acesso';
$coreConfig['default_controller'] = 'login';
$coreConfig['default_function'] = 'index';





//DEFAULT ENCRYPTION
$coreConfig['encryption_algorithm'] = 'BF-ECB';
$coreConfig['encryption_key'] = 'amaggi@2015';


//Configuração dos bancos de dados
$coreConfig['databases']['default_database'] = 'dev';


$dev = array();
$dev['vendor'] = 'pgsql';
$dev['host'] = 'hudsonventura.no-ip.org';
$dev['schema'] = 'public';
$dev['dbname'] = 'DentalClin'; //OU OWNER/ DONO
$dev['port'] = '5432';
$dev['user'] = 'fatosistemas';
$dev['pass'] = '#fatosistemas@2018#';
$coreConfig['databases']['dev'] = $dev;

$prd = array();
$prd['vendor'] = 'pgsql';
$prd['host'] = '40.114.74.171';
$prd['schema'] = 'public';
$prd['dbname'] = 'DentalClin'; //OU OWNER/ DONO
$prd['port'] = '5432';
$prd['user'] = 'fatosistemas';
$prd['pass'] = '#fatosistemas@2018#';
$coreConfig['databases']['prd'] = $dev;









//ACTIVE DIRECTORY
$coreConfig['ActiveDirectory'] = array();
$coreConfig['ActiveDirectory']['Maggi Corp']['host'] = '172.12.12.172';
$coreConfig['ActiveDirectory']['Maggi Corp']['domain'] = 'maggi.corp';
$coreConfig['ActiveDirectory']['Maggi Corp']['admin_user'] = 'suporte.unlockuser';
$coreConfig['ActiveDirectory']['Maggi Corp']['admin_pass'] = 'w42@$809';



//SESSION
$coreConfig['session_name'] = 'modularcore.com';
$coreConfig['session_expire_time'] = '120'; // in minutes




//EMAIL
$coreConfig['email_smtp_host'] = 'webmail.grupomaggi.com.br';
$coreConfig['email_smtp_port'] = '25';
$coreConfig['email_from_addr'] = '';
$coreConfig['email_from_name'] = 'Sistema de Monitoramento de Links';
$coreConfig['email_login'] = 'suporte.unlockuser';
$coreConfig['email_pass'] = 'w42@$809';