<?
class RESTful_Controller_Layout extends RESTful_Controller {

	private $_layout = 'application';
	
	public function getLayout() {
		return $this->_layout;
	}
	
	protected function setLayout( $layout ) {
		$this->_layout = $layout;
	}
	
	protected function noLayout() {
		$this->setLayout( null );
	}
	
	protected function output( $output, $format = null ) {
		$this->noLayout();
		
		RESTful_Response::sendResponseHeaders( $format );
		RESTful_Response::setContent( RESTful_Response::getContent() . $output );
		RESTful_Response::yeld();
	}
	
	function __construct() {}
	
}