<?
abstract class RESTful_Controller extends RESTful_Application {

	protected $params;
	
	private $restful_view;
	private $restful_layout;
	
	protected $_view_path;

	public static function factory( $controller_class, $view, $layout ) {
		$controller_class = str_replace('/', '_', $controller_class);
		$class_name = ucfirst( $controller_class . '_RESTful_Controller' ); 
		
		return new $class_name( $view, $layout );
	}
	
	protected function answer( $content, $response_type = null ) {
		if ( !is_null( $response_type ) ) RESTful_Response::sendResponseHeaders( RESTful_Request::$mime_types['txt'] );
		die( $content );
	}
	
	protected function render( $format = null, $return = false ) {
		
		$this->getView()->render( $format ? $format : RESTful_Application::getRequest()->getFormat(), $return );
	}
	
	protected function autorender( $object, $xml_options = array(), $cached = false ) {
		$this->output( RESTful_Response::autorender( $object, $xml_options, $cached ) );
	}
	
	protected function output( $output, $format = null ) {
		$this->layout()->output( $output, $format );
	}
	
	protected function view() {
		return $this->getView();
	}
	
	protected function getView() {
		return $this->restful_view;
	}
	
	protected function setView( RESTful_Controller_View $view ) {
		$this->restful_view = $view;
	}
	
	protected function layout() {
		return $this->restful_layout;
	}
	
	protected function setLayout( RESTful_Controller_Layout $layout ) {
		$this->restful_layout = $layout;
	}
	
	function __construct( RESTful_Controller_View $view, RESTful_Controller_Layout $layout ) {
		
		$this->restful_view = $view;
		$this->restful_layout = $layout;
		
		$this->params = array_merge( RESTful_Application::getRequest()->getUrl()->getQueryVariables(), RESTful_Route::$regex_maps );
		$this->controller_name = get_class( $this );
		
		if ( method_exists( $this, 'before' ) ) $this->before();
	}
	
}