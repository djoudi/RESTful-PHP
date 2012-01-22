<?
class Sports_RESTful_Controller extends RESTful_Controller {
	
	public function index() {
	
		$this->sports = $this->Sport->all( $this->params );
		$this->respond( $this->sports );
	
	}
	
	public function show() {
		$this->respond( $this->Sport->one( $this->params['id'] ) );
	}
	
	public function categories() {
		$this->respond( $this->Sport->categories() );
	}
	
	public function categories_with_tips() {
		$this->respond( $this->Sport->categories_with_tips() );
	}
	
	public function subsports_with_tips() {
		$this->respond( $this->Sport->subsports_with_tips( str_replace('__nbsp__', ' ', $this->params['sport'] ) ) );
	}

	public function cat_subsports_with_tips() {
		$sport_id = $this->Sport->get_sport_by_name($this->params['sport']);
		$this->respond( $this->Sport->menu_cat_subsports_with_tips( 
										str_replace('__nbsp__', ' ', $this->params['sport'] ),
										$this->params['menu_cat'] ) );
    }

	
	protected function respond( $response_val ) {
		
		if ( $this->hasFormat( 'xml' ) || $this->hasFormat( 'json' ) ) $metaData = $this->metaData( $response_val );
	
		if ( $this->hasFormat( 'xml' ) ) {
			
			$metaData = array_merge( $metaData, $this->params );
			$this->autorender( $response_val->toArray(), array( 'root' => 'sports', 'elem' => 'sport', 'root_options' => ( (bool) $metaData ? $metaData : null ) ) );
		
		} elseif ( $this->hasFormat( 'json' ) ) $this->autorender( $response_val->toArray(), $metaData );
		
		else $this->render();
	
	}
	
	protected function metaData( $response_val ) {
		
		return array( 
			
				'generated_at' 	=> date('Y-m-d H:i:s'), 
				'generated_in' 	=> RESTful_Profiler::timeElapsed( 'init' ) . ' sec', 
				'sports_count' 	=> count( $response_val ),
				'cached'				=> $this->Sport->wasLastQueryCached() !== false ? 'YES' : 'NO',
				'type'					=> 'sports',
				
			);
		
	}
	
}