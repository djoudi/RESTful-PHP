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
	
	public function byGeo() {
	  
	  $this->tips = $this->Tip->byGeo( $this->params );
	  if ( isset( $this->params['no_response'] ) || ! isset( $this->params['lat'] ) || ! isset( $this->params['long'] ) ) $this->tips = array();
	  
		$this->respond( $this->tips );
	  
	}
	
	public function bygeo_event() {
		
		$response_data = $this->Tip->bygeo_event();
		if ( isset( $this->params['no_response'] ) || ! isset( $this->params['lat'] ) || ! isset( $this->params['long'] ) ) $response_data = array(); # stub for testing
		
		$this->respond( $response_data );
	}

	public function CommentsByHash() {
		
		$this->tip = $this->Tip->comments( $this->params );
		$this->respond( $this->tip, 'comments' );
		
	}

	public function TipstersByHash() {
		
		$this->tip = $this->Tip->tipsters( $this->params );
		$this->respond( $this->tip, 'tipsters' );
		
	}

	public function menu_events() {
		$sport = str_replace('_', ' ', $this->params['sport'] ) ;
		$category = str_replace('_', ' ', $this->params['menu_cat'] ) ;
		$league = str_replace('_', ' ', $this->params['menu_league'] ) ;
		$this->respond( $this->Tip->menu_events( $sport, $category, $league) );
	}

	public function menu_tips() {
		$sport = str_replace('_', ' ', $this->params['sport'] ) ;
		$category = str_replace('_', ' ', $this->params['menu_cat'] ) ;
		$league = str_replace('_', ' ', $this->params['menu_league'] ) ;
		$event = str_replace('_', ' ', $this->params['menu_event'] );
		$this->respond( $this->Tip->menu_tips( $sport, $category, $league, $event) );
	}
	
	protected function respond( $response_val, $data_name = 'tips' ) {
		
		if ( $this->hasFormat( 'xml' ) || $this->hasFormat( 'json' ) ) 
			$metaData = $this->metaData( $response_val, $data_name );

		if ( $this->hasFormat( 'xml' ) ) {
			
			$metaData = array_merge( $metaData, $this->params );
			$this->autorender( $response_val->toArray(), array( 'root' => 'tips', 'elem' => 'tip', 'root_options' => ( (bool) $metaData ? $metaData : null ) ) );
		
		} elseif ( $this->hasFormat( 'json' ) ) {
			
			$this->autorender( is_array( $response_val ) ? $response_val : $response_val->toArray(), $metaData );
			
		} else $this->render();
	
	}
	
	protected function metaData( $response_val, $data_name = 'tips') {
		
		return array( 
			
				'generated_at' 	=> date('Y-m-d H:i:s'), 
				'generated_in' 	=> RESTful_Profiler::timeElapsed( 'init' ) . ' sec', 
				'tips_count' 		=> count( $response_val ),
				'cached'				=> $this->Tip->wasLastQueryCached() !== false ? 'YES' : 'NO',
				'type'					=> $data_name
				
			);
		
	}
	
}