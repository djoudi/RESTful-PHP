<?
$pdoParams = array( PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;' );

RESTful_Application::$db_conf['default'] = Zend_Db::factory('Pdo_Mysql', array(
    'host'     				=> 'your_host_here',
    'username' 				=> 'username',
    'password' 				=> 'password',
    'dbname'   				=> 'db_name',
    'driver_options' 		=> $pdoParams,
));