<?
class RESTful_Registry extends RESTful_Application {

	public static function init() {
		
		define( 'REGISTRY_ENTRY_NAME', 'RESTful_Application_Registry_Data' );
		
		RESTful_Registry::loadRegistry();
	
	}
	
	public static function finish() {
	
		RESTful_Registry::saveRegistry();
	
	}

	public static function loadRegistry() {
	
		$cache = RESTful_Cache::getInstance();
		
		if ( ( $reg_data = $cache->load( REGISTRY_ENTRY_NAME ) ) !== false ) {
		
			$registry = Zend_Registry::getInstance();
			$registry = $reg_data;
			
			return true;
		}
		
		return false;
	}
	
	public static function saveRegistry() {
	
		$cache = RESTful_Cache::getInstance();
		
		$registry = Zend_Registry::getInstance();
		
		$cache->save( $registry, REGISTRY_ENTRY_NAME );
		
	}

}