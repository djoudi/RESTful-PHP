<?
abstract class RESTful_Application {

	private static $request;
	
	public static $instance_variables = array(); # array emulating ruby @
	public static $loadedModels;
	public static $_url;
	
	protected static $db_conf = array(); # db connections configurations 
	
	public static function init() {
		
		# sleep( rand(0, 10) );
	
		require_once 'RESTful/Loader.php';
		RESTful_Loader::init(); 
		
		RESTful_Profiler::init();
		RESTful_Exception::init();
		RESTful_Registry::init();
		RESTful_Log::init();
		
		if ( DEBUG ) RESTful_Response::sendAccessControlHeaders(); # allow Ajax cross-domain requests
		
		RESTful_Application::$_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		if ( substr( $_SERVER['REQUEST_URI'], -1 ) == '/' ) $_SERVER['REQUEST_URI'] = substr( $_SERVER['REQUEST_URI'], 0, ( strlen( $_SERVER['REQUEST_URI'] ) -1 ) );
		if ($_SERVER["SERVER_PORT"] != "80") RESTful_Application::$_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		else RESTful_Application::$_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		
	}
	
	public static function run( $url = null ) {
	
		if ( is_null( $url ) ) $url = Net_URL2::getRequestedURL();
		else {
			$url_ = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
			if ($_SERVER["SERVER_PORT"] != "80") $url = $url_ . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/' . $url;
			else $url = $url_ . $_SERVER["SERVER_NAME"] . '/' . $url;
		}
		
		RESTful_Log::$app_log->info( $url );
		
		$request = new RESTful_Request( new Net_URL2( $url ) );
		RESTful_Application::setRequest( $request );
		
		RESTful_Loader::loadRoutes();
		RESTful_Loader::loadHelpers();
		
		if ( $request->parse() ) {
			
			$controller = RESTful_Controller::factory( $request->getController(), new RESTful_Controller_View(), new RESTful_Controller_Layout() ); 
			$action = $request->getAction();

      RESTful_Application::$instance_variables['params'] = array_merge( RESTful_Route::$regex_maps, $_REQUEST );
			RESTful_Application::execute( $controller, $action, $request );
		
			# all done - close
			RESTful_Application::finish();			
		} 
	}
	
	protected static function execute( $controller, $action, $request = null ) {
		
		if ( is_null( $request ) ) $request = RESTful_Application::getRequest();
		
		try {
				
			RESTful_Response::setTitle( ucfirst( trim( str_replace( '/', ' ', $request->getUrl()->getPath() ) ) ) );
			
			$controller->view()->setView( $action ); # set corresponding view file
			$controller->$action( RESTful_Application::$instance_variables['params'] ); # execute controller action
				
			RESTful_Response::renderLayout( $controller->layout()->getLayout() );
								
		} catch( Exception $e ) {	
		
			RESTful_Exception::handleException( $e );	
			
		}
		
	}
	
	private static function finish() {
	
		RESTful_Registry::finish();
	
	}
	
	public function getFormat() {
	
		return RESTful_Application::getRequest()->getFormat();
		
	}
	
	public function hasFormat( $format ) {
	
		return RESTful_Application::getRequest()->getFormat() == $format;
		
	}
	
	public static function getRequest() {
	
		return RESTful_Application::$request;
		
	}
	
	private static function setRequest( $req ) {
	
		RESTful_Application::$request = $req;
	
	}
	
	public function controllerName() {
		return RESTful_Application::$instance_variables['controller_name'];
	}
	
	function __call($method, $params) {
	
		throw new RESTful_Exception( "Method ::{$method} is not defined." );
		
	}
	
	function __set( $name, $value ) {
	
		RESTful_Application::$instance_variables[$name] = $value;
		
	}
	
	function __get( $var_name ) {
		
		if ( RESTful_Loader::loadModel( $var_name ) ) { # model file exists - we can instantiate it
			
			if ( isset( RESTful_Application::$loadedModels[$var_name] ) ) return RESTful_Application::$loadedModels[$var_name]; # only one model object for each model class - zend models correspond to tables
			
			RESTful_Application::$loadedModels[$var_name] = RESTful_Model::factory( $var_name );
			return RESTful_Application::$loadedModels[$var_name];
			
		}
		
		# is it trying to load a __set property on instance_variables?
		if ( isset( RESTful_Application::$instance_variables[$var_name] ) ) {
			return RESTful_Application::$instance_variables[$var_name];
		}
		
	}
	
	function __destruct() {
	
		restore_error_handler();
		restore_exception_handler();
		
	}
	
	function __toString() {
		return get_class( $this );
	}

} RESTful_Application::init();

function __autoload( $class_name ) {
	RESTful_Loader::autoLoad( $class_name );
}