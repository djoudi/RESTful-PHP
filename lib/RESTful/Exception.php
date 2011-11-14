<?
class RESTful_Exception extends Zend_Exception {
	
	public static function init() {
	
		# set_error_handler( 'RESTful_Exception::handleError' );
		# set_exception_handler( 'RESTful_Exception::handleException' );
	
	}
	
	public static function handleException( $e ) {
	
		if ( DEBUG ) RESTful_Debug::dump( $e, __CLASS__, true, 'error' );
		die();
		
	}
	
	public static function handleError( $errno, $errstr, $errfile, $errline ) {
	
		if ( DEBUG ) RESTful_Debug::dump( $errno . ': ' . $errstr . ' in ' . $errfile . ' at ' . $errline, 'Error ', true, 'error' );
		die();
		
	}
	
}