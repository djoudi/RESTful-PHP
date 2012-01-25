<?
class Tips_RESTful_Controller extends RESTful_Controller {
	
	public function index() {
		$this->hot();
	}
	
	public function hot() {
		
		$this->hot_tips = $this->Tip->hot( $this->params );
		$this->respond( $this->hot_tips );
		
	}
	
	public function show() {
		
		$this->tip = $this->Tip->cacheFind( $this->params['id'] );
		$this->respond( $this->tip );
		
	}
	
	public function bySport() {
		
		$this->tips = $this->Tip->bySport( $this->params );
		$this->respond( $this->tips );
		
	}

	public function menu_events() {
		$this->respond( $this->Tip->menu_events( str_replace('_', ' ', $this->params['menu_league'] ) ) );
	}	
	
	public function jump() {
		echo 'bzzz print';
	}
	
	protected function respond( $response_val ) {
		
		
		if ( $this->hasFormat( 'xml' ) || $this->hasFormat( 'json' ) ) $metaData = $this->metaData( $response_val );

		if ( $this->hasFormat( 'xml' ) ) {
			
			$metaData = array_merge( $metaData, $this->params );
			$this->autorender( $response_val->toArray(), array( 'root' => 'tips', 'elem' => 'tip', 'root_options' => ( (bool) $metaData ? $metaData : null ) ) );
		
		} elseif ( $this->hasFormat( 'json' ) ) {
			
			$this->autorender( is_array( $response_val ) ? $response_val : $response_val->toArray(), $metaData );
			
		} else $this->render();
	
	}
	
	protected function metaData( $response_val ) {
		
		return array( 
			
				'generated_at' 	=> date('Y-m-d H:i:s'), 
				'generated_in' 	=> RESTful_Profiler::timeElapsed( 'init' ) . ' sec', 
				'tips_count' 		=> count( $response_val ),
				'cached'				=> $this->Tip->wasLastQueryCached() !== false ? 'YES' : 'NO',
				'type'					=> 'tips'
				
			);
		
	}
	
}