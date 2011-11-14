<?
abstract class RESTful_Loader extends RESTful_Application {

	public static function init() {
	
		define( 'EXT', '.php' );
		define( 'NSS', '_' );
		
		define( 'V_PATH', 'app/views/' );
		define( 'VL_PATH', 'app/views/layouts/' );
		define( 'C_PATH', 'app/controllers/' );
		define( 'M_PATH', 'app/models/' );
		define( 'CF_PATH', 'config/' );
	
		define( 'ZEND', 'Zend' );
		define( 'REST', 'RESTful' );
		define( 'RC', '_RESTful_Controller' );
		define( 'C', 'Controller' );
		
		define( 'DB_CONF', 'config/database' );
	}

	public static function loadRoutes() {
	
		require_once BASE_PATH . 'config/routes.php';
		
	}
	
	public static function loadLayout( $layout ) {
	
		require_once( BASE_PATH . VL_PATH . $layout . '.' . RESTful_Application::getRequest()->getFormat() . EXT );
	
	}
	
	public static function loadView( RESTful_Controller_View $controller_view, $format = null ) {
	
		if ( is_null( $format ) ) $format = RESTful_Application::getRequest()->getFormat();
		
		extract( RESTful_Application::$instance_variables );
		$path = BASE_PATH . V_PATH . strtolower( str_ireplace( array( RC, NSS ), array( '', DIRECTORY_SEPARATOR ), $controller_view->controllerName() ) ) . DIRECTORY_SEPARATOR . $controller_view->getView() . '.' . $format . EXT;
		if ( file_exists( $path ) ) require_once( $path );
		else throw new RESTful_Exception( 'View file not found: ' . $path );
		
	}
	
	public static function loadModel( $var_name ) {
	
		# by convention, model names are uppercase - other variables are lowercase
		if ( $var_name == ucfirst( $var_name ) ) { # uppercase
			
			# is it trying to load a model that was already instantiated?
			if ( isset( RESTful_Application::$loadedModels[$var_name] ) ) return true; # only one model object for each model class - zend models correspond to tables
			
			# can we instantiate it now?
			$path = str_replace('_', '/', $var_name);
			if ( file_exists( BASE_PATH . M_PATH . $path . EXT ) ) return require_once( BASE_PATH . M_PATH . $path . EXT );
			else throw new RESTful_Exception( 'Model file can not be found: ' . $var_name );
			
			return true;
			
		} else return null;
	}
	
	public static function loadConfig( $file ) {
		require_once BASE_PATH . CF_PATH . $file . EXT;
	}
	
	public static function loadDbConfig( $adapter_name ) {
	
		require_once BASE_PATH . DB_CONF . EXT;
		
		RESTful_Model::$db_adapter = $$adapter_name;
		
	}
	
	public static function getNamespace( $class_name ) {
	
		if ( stripos( $class_name, ZEND ) === 0 ) return ZEND;
		elseif ( stripos($class_name, REST) === 0 ) return REST;
		elseif ( stripos($class_name, RC) !== false ) return RC;
	
	}
	
	public static function loadHelpers( $request_type = null ) {
		
		RESTful_Loader::loadAppHelpers();
		
		if ( is_null( $request_type ) ) $request_type = RESTful_Application::getRequest()->getFormat();
		
		if ( defined( strtoupper( $request_type ) . '_APP_HELPERS' ) ) $HELPERS_SET = constant( strtoupper( $request_type ) . '_APP_HELPERS' );
		else return;
		
		if ( empty( $HELPERS_SET ) ) return;
		RESTful_Loader::parseHelpers( explode( ',', $HELPERS_SET ) );

	}
	
	public static function loadAppHelpers() {
		RESTful_Loader::parseHelpers( explode( ',', APP_HELPERS ) );
	}
	
	public static function loadHelper( $helper ) {
		RESTful_Loader::loadHelperFile( $helper );
	}
	
	private function parseHelpers( $helpers ) {
		if ( empty($helpers) ) return;
		
		foreach ( $helpers as $helper ) {
			$helper = trim( $helper );
			RESTful_Loader::loadHelperFile( $helper );
		}
	}
	
	private function loadHelperFile( $helper ) {
		if ( !empty( $helper ) ) require_once( "helpers/{$helper}.helper.php" );
	}
	
	public static function autoLoad( $class_name ) {
	
		switch ( RESTful_Loader::getNamespace( $class_name ) ) {
		
			case ZEND:
				RESTful_Loader_Zend::load( $class_name );
				break;
				
			case RC:
				RESTful_Loader_Rc::load( $class_name );
				break;
			
			case REST:
			default:
				require_once( str_ireplace( NSS, DIRECTORY_SEPARATOR, $class_name ) . EXT );
				break;
		
		}
	
	}
	
	public abstract static function load( $class_name );

}