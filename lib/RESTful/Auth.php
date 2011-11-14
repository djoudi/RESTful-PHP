<?
class RESTful_Auth extends RESTful_Application {
	
	private static $zend_auth_adapter;
	private static $auth_result;
	
	public static function init( $zend_adapter ) {

		RESTful_Auth::$zend_auth_adapter = new Zend_Auth_Adapter_DbTable( $zend_adapter );
		RESTful_Auth::$zend_auth_adapter
						->setTableName( AUTH_USERS_TABLE )
						->setIdentityColumn( AUTH_USERS_COLUMN )
						->setCredentialColumn( AUTH_PASS_COLUMN );
	}
	
	public static function auth( $username, $password ) {
	
		RESTful_Auth::$zend_auth_adapter->setIdentity( $username )->setCredential( $password );
		try {
			RESTful_Auth::$auth_result = Zend_Auth::getInstance()->authenticate( RESTful_Auth::$zend_auth_adapter );
			if ( RESTful_Auth::$auth_result->isValid() ) return true;
		} catch ( Exception $e ) {
			return false;
		}
	}
	
	public static function getAuthResult() {
		
		RESTful_Auth::$auth_result = Zend_Auth::getInstance();
		if ( RESTful_Auth::$auth_result->hasIdentity() ) {
    		// Identity exists; get it
			# $identity = RESTful_Auth::$auth_result->getIdentity();
			return true;
		}
		
		return false;
	}
	
	public static function authenticate( $zend_adapter, $username = null, $password = null ) {
		
		if ( !is_null( $username ) && !is_null( $password ) ) { // new auth
			RESTful_Loader::loadConfig( 'auth' );
			RESTful_Auth::init( $zend_adapter );
			RESTful_Auth::auth( $username, $password );
		}
		
		return RESTful_Auth::getAuthResult();
	}
	
	public static function logOff() {
		Zend_Auth::getInstance()->clearIdentity();
	}
	
}