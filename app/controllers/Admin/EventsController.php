<?
class Admin_Events_RESTful_Controller extends Events_RESTful_Controller {

  public function index( $params = array() ) {

    if ( $params['search_event'] ) {
      $this->events = $this->Event->findAllLikeEventName( $params['search_event'] )->toArray();
    }

    $this->respond();

  }

  public function edit( $params = array() ) {

    if ( isset( $params['id'] ) ) {
      $event = $this->Tip->fetchRow( $this->Tip->select()->where( 'id = ?', $params['id'] ) )->toArray();

      if ( isset( $event[0] ) ) {
        $event = $event[0];
        $geo = $this->Event->getGeoData( $event['eventname'], $event['event_start'] );
        if ( isset( $geo[0] ) ) $event['geo'] = $geo[0];
      }

      $this->event = $event;
    }
    $this->respond();
  }

  public function update( $params = array() ) {

    $event = $this->Tip->fetchRow( $this->Tip->select()->where( 'id = ?', $params['id'] ) )->toArray();
    
    if ( $event['sport'] == 'Horse Racing' && preg_match( '/([0-9]*):([0-9]*) (.*)/', $event['eventname'], $matches ) ) {
      if ( count( $matches ) == 4 ) {
        # $event['eventname'] = '%' . $matches[3];
        $event['event_start'] = null;
      }
    }

    $this->Event->update( $params, $event );

    //+TODO: refactor in redirect_to(...)
    header("Location: " . BASE_URL . "admin/events?search_event=" . $event['eventname'] );

  }

  protected function respond( $response_val = null ) {

    if ( $this->hasFormat( 'xml' ) || $this->hasFormat( 'json' ) ) $metaData = $this->metaData( $response_val );

    if ( $this->hasFormat( 'xml' ) ) {

      $metaData = array_merge( $metaData, $this->params );
      $this->autorender( $response_val->toArray(), array( 'root' => 'events', 'elem' => 'event', 'root_options' => ( (bool) $metaData ? $metaData : null ) ) );

    } elseif ( $this->hasFormat( 'json' ) ) {

      $this->autorender( is_null( $response_val ) ? array() : $response_val->toArray(), $metaData );

    } else $this->render();

  }

  protected function metaData( $response_val ) {

    return array(

      'generated_at' 		=> date('Y-m-d H:i:s'),
      'generated_in' 		=> RESTful_Profiler::timeElapsed( 'init' ) . ' sec',
      'events_count' 	  => count( $response_val ),
      'cached'			    => $this->Event->wasLastQueryCached() !== false ? 'YES' : 'NO',
      'type'				    => 'bookies'

    );

  }

  public function before() {

    if ( !RESTful_Auth::authenticate( $this->Event->db() ) ) {
      RESTful_Application::run( 'authentications/add.html' );
      exit;
    }

    $this->layout()->setLayout('admin');
  }

}