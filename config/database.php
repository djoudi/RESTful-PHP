<?
$pdoParams = array( PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;' );

RESTful_Application::$db_conf['default'] = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> '10.176.80.146',
    'username' 				=> 'adrian',
    'password' 				=> 'Schw3pp3s!@#',
    'dbname'   				=> 'betting_mobile',
    'driver_options' 		=> $pdoParams,
));

/*
RESTful_Application::$db_conf['default'] = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> 'localhost',
    'username' 				=> 'root',
    'password' 				=> 'root',
    'dbname'   				=> 'betting_mobile',
    'driver_options' 		=> $pdoParams,
));
*/

RESTful_Application::$db_conf['valuechecker'] = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> '192.168.100.208',
    'username' 				=> 'vc_masterdb_user',
    'password' 				=> 'wdG201kh49DuuSe',
    'dbname'   				=> 'odds101',
    'driver_options' 		=> $pdoParams,
));

/*
RESTful_Application::$db_conf['valuechecker'] = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> '94.236.99.181',
    'username' 				=> 'adrian',
    'password' 				=> 'Schw3pp3s!@#',
    'dbname'   				=> 'odds101',
    'driver_options' 		=> $pdoParams,
));
*/