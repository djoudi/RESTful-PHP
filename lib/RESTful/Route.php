<?
abstract class RESTful_Route extends RESTful_Application {
	
	private static $routes;
	private static $restful_actions;
	
	public static $regex_maps = array();
	
	public static function map( $uri_path, $controller_action ) {
		if ( empty( $uri_path ) ) $uri_path = '@' . ':root' . '@'; # root
		if ( empty( $controller_action ) || stripos($controller_action, '#') === 0 ) throw new RESTful_Exception( "Invalid route {$uri_path} <=> {$controller_action}" );
		
		RESTful_Route::addRoute( $uri_path, $controller_action );
	}
	
	public static function mapResource( $resource_name, $conditions = array() ) {
	
		if ( empty( $resource_name ) ) throw new RESTful_Exception( "Invalid RESTful resource {$resource_name}" );
		if ( stripos( $resource_name, '_' ) !== false ) $resource_name = str_replace( '_', '/', $resource_name );
	
		$available_restful_actions = RESTful_Route::getRestfulActions();
		
		if( isset( $conditions[':only'] ) && is_array( $conditions[':only'] ) ) {
			$available_restful_actions = array_intersect( $available_restful_actions, $conditions[':only'] );
		} elseif( isset( $conditions[':except'] ) && is_array( $conditions[':except'] ) ) {
			$available_restful_actions = array_diff( $available_restful_actions, $conditions[':except'] );
		}
		
		for( $iterator = new ArrayIterator( $available_restful_actions ); $iterator->valid(); $iterator->next() ) {
			
			$method = $iterator->current();
			$action = '';
			
			switch( $method ) {
			
				case 'index':
					$action = $resource_name;
					break;
			
				case 'show':
					$action = '@' . $resource_name . '/(?P<id>\d+)@';
					break;
					
				case 'edit':
					$action = '@' . $resource_name . '/(?P<id>\d+)/edit@';
					break;
					
				case 'add':
					$action = '@' . $resource_name . '/add@';
					break;
					
				case 'create':
					$action = '@' . $resource_name . '/create@';
					break;
					
				case 'update':
					$action = '@' . $resource_name . '/(?P<id>\d+)/update@';
					break;
					
				case 'destroy':
					$action = '@' . $resource_name . '/(?P<id>\d+)/destroy@';
					break;
					
				default:
					$action = '@' . $resource_name . '/' . $method . '@';
					break;
			}
			
			if ( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' && $method == 'add' )  $method = 'create';
			if ( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' && $method == 'edit' )  $method = 'update';
			
			if ( $method == 'index' ) RESTful_Route::addRoute( '@' . $action . '/index@', $resource_name . '#' . $method );
			RESTful_Route::addRoute( $action, $resource_name . '#' . $method );
		}
	}
	
	public static function getRoutes() {
		return RESTful_Route::$routes;
	}
	
	private static function addRoute( $uri_path, $controller_action ) {
		if ( stripos( $controller_action, '#' ) === false ) $controller_action .= '#index';
		
		RESTful_Route::$routes[$uri_path] = $controller_action;
	}
	
	public static function uriPathToResourceDef( $uri_path, $request_method = 'GET' ) {
		if( stripos( $uri_path, '/' ) === 0 ) $uri_path = substr( $uri_path, 1 ); # remove first /
		if ( empty( $uri_path ) || $uri_path == '/' ) $uri_path = ':root'; # root
		
		foreach( RESTful_Route::$routes as $route_def => $mapping ) {
			$original_route_def = $route_def;
			if( stripos( $route_def, '@' ) === false ) $route_def = '@' . $route_def . '@'; # add regex delimiter
			if ( preg_match( $route_def, $uri_path, $matches ) ) { 
				if ( $matches[0] == $uri_path ) {
					if ( RESTful_Route::isHash( $matches ) ) {
						$keys = array_keys( $matches );
						foreach( $keys as $key ) if ( is_numeric( $key ) ) unset( $matches[$key] );
						RESTful_Route::$regex_maps = $matches; # store the named paths in the global params array
					}
					
					return $original_route_def; # good match
				}
			} 
		}
				
		return null; # nothing found
	}
	
	public static function isRoutedTo( $uri_path ) {
		if( isset( RESTful_Route::$routes[$uri_path] ) ) return RESTful_Route::$routes[$uri_path];
		
		return null;
	}
	
	public static function getControllerFor( $uri_path ) {
		if ( ! RESTful_Route::isRoutedTo( $uri_path ) ) throw new RESTful_Exception( "Controller not found. No route defined for {$uri_path}" );
		
		$controller_action = RESTful_Route::getControllerActionFor( $uri_path );
		if ( stripos( $controller_action, '#' ) === false ) return $controller_action;
		else {
			$controller_action = explode('#', $controller_action);
			
			return $controller_action[0];
		}
	}
	
	public static function getActionFor( $uri_path ) {
		if ( ! RESTful_Route::isRoutedTo( $uri_path ) ) throw new RESTful_Exception( "Action not found. No route defined for {$uri_path}" );
		
		$controller_action = RESTful_Route::getControllerActionFor( $uri_path );
		if ( stripos( $controller_action, '#' ) === false ) return 'index';
		else {
			$controller_action = explode('#', $controller_action);
			if ( isset( $controller_action[1] ) && ! empty( $controller_action[1] ) ) return $controller_action[1];
			return 'index';
		}
	}
	
	public static function getControllerActionFor( $uri_path ) {
		if ( ! RESTful_Route::isRoutedTo( $uri_path ) ) throw new RESTful_Exception( "ControllerAction not found. No route defined for {$uri_path}" );
		
		return RESTful_Route::$routes[$uri_path];
	}
	
	public static function getRestfulActions() {
		if ( empty( RESTful_Route::$restful_actions ) ) RESTful_Route::$restful_actions = array( 'index', 'show', 'add', 'create', 'edit', 'update', 'destroy' );
		
		return RESTful_Route::$restful_actions;
	}
	
	public static function isHash($arr) {
	    return array_keys( $arr ) !== range( 0, count($arr) - 1 );
	}

	
	public function __destruct() { parent::__destruct(); }
	
}