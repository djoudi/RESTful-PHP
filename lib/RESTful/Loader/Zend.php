<?
class RESTful_Loader_Zend extends RESTful_loader {

	public static function load( $class_name ) {
	
		require_once( str_ireplace( NSS, DIRECTORY_SEPARATOR, $class_name ) . EXT );
	
	}

}