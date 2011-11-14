<?
$pdoParams = array( PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;' );

$default = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> '46.38.172.135',
    'username' 				=> 'adrian',
    'password' 				=> 'Schw3pp3s!@#',
    'dbname'   				=> 'betting_mobile',
    'driver_options' 		=> $pdoParams,
));

$default = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> '127.0.0.1',
    'username' 				=> 'root',
    'password' 				=> 'schw3pp3s',
    'dbname'   				=> 'betting_mobile',
    'driver_options' 		=> $pdoParams,
));