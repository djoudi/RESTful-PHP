<?
class Events_RESTful_Controller extends RESTful_Controller {
	
	public function index() {
	
		$this->events = $this->Event->all( $this->params );
		$this->respond( $this->events );
	
	}
	
	public function show() {
		$this->event = $this->Event->one( $this->params['id'] );
		$this->respond( $this->event );
	
	}
	
	protected function respond( $response_val ) {
	
		if ( $this->hasFormat( 'xml' ) ) {
		
			$xml_root_options = array( 
			
				'generated_at' 	=> date('Y-m-d H:i:s'), 
				'generated_in' 	=> RESTful_Profiler::timeElapsed( 'init' ) . ' sec', 
				'sports_count' 	=> count( $response_val ),
				'cached'		=> $this->Event->wasLastQueryCached() !== false ? 'YES' : 'NO',
				
			);
			
			$xml_root_options = array_merge( $xml_root_options, $this->params );
			$this->autorender( $response_val->toArray(), array( 'root' => 'events', 'elem' => 'event', 'root_options' => ( (bool) $xml_root_options ? $xml_root_options : null ) ), $this->Event->wasLastQueryCached() !== false );
		
		} else $this->render();
	
	}
	
}