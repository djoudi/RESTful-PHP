<?
class RESTful_Response extends RESTful_Application {

	public static $content, $title;

	public static function renderLayout( $layout ) {
	
		if ( is_null( $layout ) ) return;
		
		RESTful_Loader::loadLayout( $layout );
		
	}
	
	public static function yeld() {
	
		echo RESTful_Response::getContent();
		
	}
	
	public static function setContent( $content ) {
	
		RESTful_Response::$content = $content;
	
	}
	
	public static function getContent() {
	
		return RESTful_Response::$content;
	
	}
	
	public static function setTitle( $title ) {
	
		RESTful_Response::$title = $title;
	
	}
	
	public static function title() {
	
		return RESTful_Response::$title;
		
	}
	
	public static function sendResponseHeaders( $format = null ) { 
	
		$content_type = RESTful_Request::getContentTypeFor( $format );
	
		header( "Vary: Accept" );
		header( "Content-Type: " . $content_type . "; charset=utf-8" );
		
	}
	
	public static function sendAccessControlHeaders() {
		
		header("Access-Control-Allow-Origin: *");
		
	}
	
	public static function autorender( $object, $xml_options, $cached ) {
		
		# $class_name = 'RESTful_Response_' . RESTful_Application::getRequest()->getFormat(); 
		
		# return $class_name::autorender( $object, $xml_options );
		
		# just for php version online
		return RESTful_Response_Json::autorender( $object, $xml_options );
		
	}
	
	public static function redirectTo( $url, $header = null ) {
		# if ( !is_null( $header ) ) header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: $url" );
	}

}