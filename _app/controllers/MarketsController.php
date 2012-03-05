<?
class Markets_RESTful_Controller extends RESTful_Controller {
	
	public function index() {
	
		$this->markets = $this->Market->all( $this->params );
		$this->respond( $this->markets );
	
	}
	
	public function show() {
		$this->market = $this->Market->one( $this->params['id'] );
		$this->respond( $this->market );
	
	}
	
	protected function respond( $response_val ) {
	
		if ( $this->hasFormat( 'xml' ) ) {
		
			$xml_root_options = array( 
			
				'generated_at' 	=> date('Y-m-d H:i:s'), 
				'generated_in' 	=> RESTful_Profiler::timeElapsed( 'init' ) . ' sec', 
				'sports_count' 	=> count( $response_val ),
				'cached'		=> $this->Market->wasLastQueryCached() !== false ? 'YES' : 'NO',
				
			);
			
			$xml_root_options = array_merge( $xml_root_options, $this->params );
			$this->autorender( $response_val->toArray(), array( 'root' => 'markets', 'elem' => 'market', 'root_options' => ( (bool) $xml_root_options ? $xml_root_options : null ) ) );
		
		} else $this->render();
	
	}
	
}