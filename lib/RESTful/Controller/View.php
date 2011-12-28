<?
class RESTful_Controller_View extends RESTful_Controller {
	
	private $_view;
	private $_rest_controller;
	
	public function getView() {
	
		if ( ! is_null( $this->_view ) ) return $this->_view;
		
		$trace = debug_backtrace();
		if ( isset( $trace[4]['function'] ) ) {
			
			$this->setView( $trace[4]['function'] ); #no default, it is specifically set within controller
			
			return $this->getView();
		}
		
		return null;
	}
	
	protected function setView( $view ) {
		$this->_view = $view;
	}
	
	protected function render( $format, $return ) {
		
		extract( RESTful_Application::$instance_variables );
		
		ob_start();
		RESTful_Loader::loadView( $this, $format );
		$output = ob_get_contents(); ob_end_clean();
		
		if ( $return ) return $output;
		
		RESTful_Response::sendResponseHeaders( $format );
		RESTful_Response::setContent( RESTful_Response::getContent() . $output );
	}
	
	function __construct() {}

}