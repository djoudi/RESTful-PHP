<?
class RESTful_Request extends RESTful_Application {

	private $url;

	private $method;
	private $content_type;
	private $format;
	
	private $controller;
	private $action;
	
	public static $mime_types;
	
	protected function __construct( Net_URL2 $url ) {
	
		$this->method = strtoupper( $_SERVER['REQUEST_METHOD'] );
		$this->content_type = /* isset( $_SERVER['CONTENT_TYPE'] ) ? strtolower( $_SERVER['CONTENT_TYPE'] ) : */ RESTful_Request::contentType( $url );
		
		$this->url = $url;
		$this->url->normalize();
		
		$this->format = RESTful_Request::getFormatFor( $this->content_type );
	}
	
	protected function contentType( Net_URL2 $url ) {
		if ( preg_match('/(\.)(.*)$/', $url->getPath(), $matches) ) {
			$url->setPath( str_ireplace( $matches[0], '', $url->getPath() ) );
			return RESTful_Request::$mime_types[$matches[2]];
		}
		
		return DEFAULT_MIME_TYPE;
	}
	
	protected function getMethod() {
		return $this->method;
	}
	
	public function getUrl() {
		return $this->url;
	}

  public function getController() {
    return $this->controller;
  }

  public function getAction() {
    return $this->action;
  }

	protected function setUrl( Net_URL2 $url ) {
		$this->url = $url;
	}
	
	public function getContentType() {
		return $this->content_type;
	}
	
	public function getFormat() {
		return $this->format;
	}
	
	public function getQueryData() {
		return $this->url->getQuery();
	}
	
	public static function getFormatFor( $content_type ) {
		return trim( array_search( $content_type, RESTful_Request::$mime_types ) );
	}
	
	public static function getContentTypeFor( $format ) {
		if ( array_key_exists( $format, RESTful_Request::$mime_types ) ) return RESTful_Request::$mime_types[$format];
		else return DEFAULT_MIME_TYPE;
	}
	
	protected function parse( $throwException = true ) {
		
		$resource_def = RESTful_Route::uriPathToResourceDef( $this->getUrl()->getPath(), $this->getMethod() ); 
		
		if ( RESTful_Route::isRoutedTo( $resource_def ) ) {
		
			$this->controller = RESTful_Route::getControllerFor( $resource_def );
			$this->action = RESTful_Route::getActionFor( $resource_def );
			
			return true;
			
		} else {
		
			if ( DEBUG ) Zend_Debug::dump( RESTful_Route::getRoutes(), __CLASS__ );
			
			if ( $throwException ) {
				$exception_message = "Undefined route for " . $resource_def;
				if ( stripos( $resource_def, '_' ) !== false ) {
					$pieces = explode( '/', $this->getUrl()->getPath() );
					$exception_message .= ' or undefined action ' . $pieces[count($pieces) -1];
					
					unset( $pieces[count($pieces) -1] );
					$exception_message .= ' for resource ' . implode( '_', $pieces );
				}
				throw new RESTful_Exception( $exception_message );
			}
			
			return false;
		}
	}
	
	public function __destruct() { parent::__destruct(); }
	
}

RESTful_Request::$mime_types = array(
	'txt' 	=> 'text/plain',
	'html' 	=> 'text/html',
	'htm' 	=> 'text/html',
	'php' 	=> 'text/html',
	'css' 	=> 'text/css',
	'js' 	=> 'application/javascript',
	'json' 	=> 'application/json',
	'xml' 	=> 'application/xml',
	'xml '	=> 'text/xml',
);