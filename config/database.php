<?
$pdoParams = array( PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;' );

RESTful_Application::$db_conf['default'] = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> '46.38.172.135',
    'username' 				=> 'adrian',
    'password' 				=> 'Schw3pp3s!@#',
    'dbname'   				=> 'betting_mobile',
    'driver_options' 		=> $pdoParams,
));

RESTful_Application::$db_conf['default'] = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> '127.0.0.1',
    'username' 				=> 'root',
    'password' 				=> 'root',
    'dbname'   				=> 'betting_mobile',
    'driver_options' 		=> $pdoParams,
));

RESTful_Application::$db_conf['valuechecker'] = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> '94.236.99.181',
    'username' 				=> 'adrian',
    'password' 				=> 'Schw3pp3s!@#',
    'dbname'   				=> 'odds101',
    'driver_options' 		=> $pdoParams,
));