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

  public function byGeo( $params = array() ) {
  
    if ( ! isset( $params['lat'] ) ) $params['lat'] = 0;
    if ( ! isset( $params['long'] ) ) $params['long'] = 0;

    $event = $this->Event->allByGeo( $params['lat'], $params['long'] );
    if ( !empty( $event ) ) $event = $this->Tip->bygeo_event( $event[0]['eventname'], $event[0]['eventdate'] );
    if ( empty( $event ) ) $event = array();

    $this->respond( $event, 'tips', 'tip' );
  }

  protected function respond( $response_val, $data_name = 'events', $data_type = 'event' ) {

    if ( $this->hasFormat( 'xml' ) || $this->hasFormat( 'json' ) )
      $metaData = $this->metaData( $response_val, $data_name );

    if ( $this->hasFormat( 'xml' ) ) {

      $metaData = array_merge( $metaData, $this->params );
      $this->autorender( $response_val->toArray(), array( 'root' => $data_name, 'elem' => $data_type, 'root_options' => ( (bool) $metaData ? $metaData : null ) ) );

    } elseif ( $this->hasFormat( 'json' ) ) {

      $this->autorender( is_array( $response_val ) ? $response_val : $response_val->toArray(), $metaData );

    } else $this->render();

  }

  protected function metaData( $response_val, $data_name = 'tips') {

    return array(

      'generated_at' 	          => date('Y-m-d H:i:s'),
      'generated_in' 	          => RESTful_Profiler::timeElapsed( 'init' ) . ' sec',
      $data_name . '_count' 		=> count( $response_val ),
      'cached'				          => $this->Tip->wasLastQueryCached() !== false ? 'YES' : 'NO',
      'type'					          => $data_name

    );

  }

  /*
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
  */
	
}