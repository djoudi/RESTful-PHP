<?
class RESTful_Cache extends RESTful_Application {

	private static $cache;

	public static function getInstance() { # lazy loading / on demand
	
		if ( RESTful_Cache::$cache instanceof Zend_Cache_Core ) return RESTful_Cache::$cache;
		
		return RESTful_Cache::$cache = Zend_Cache::factory('Core', 'File', array( 'lifetime' => 20, 'automatic_serialization' => true ), array( 'cache_dir' => sys_get_temp_dir() ) );
		
	}
	
	public static function load( $cache_id ) {
		
		$registry = Zend_Registry::getInstance();
  	if ( ! isset( $registry[$cache_id] ) ) {
			$registry[$cache_id] = array();
			$registry[$cache_id]['update_time'] = 0;
		}
  	
  	return RESTful_Cache::getInstance()->load( $cache_id );
	
	}
	
	public static function save( $result, $cache_id ) {
		
		$registry = Zend_Registry::getInstance();
		$registry[$cache_id]['update_time'] = time(); # store cache time on registry
		
		return RESTful_Cache::getInstance()->save( $result, $cache_id );
	
	}
	
	public static function getCacheId( $object, $salt = null ) {
	
  	$object_id = is_object( $object ) ? $object->__toString() : serialize( $object );	
  	return hash_hmac( 'sha256', $object_id, $salt );
	
	}

}