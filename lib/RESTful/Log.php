<?
class RESTful_Log extends RESTful_Application {
  
  public static $app_log;
  
  public static function init() {
    RESTful_Log::getInstance();
  }
  
  public static function getInstance() {
    
    if ( RESTful_Log::$app_log instanceof Zend_Log ) return RESTful_Log::$app_log;
    
    $writer = new Zend_Log_Writer_Stream( 'log/' . date( 'Y-m-d ') . '.php' );
    return RESTful_Log::$app_log = new Zend_Log( $writer );
  }
  
}