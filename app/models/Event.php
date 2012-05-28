<?
class Event extends RESTful_Model {

	protected $_name = 'events';
  protected $_primary = 'eventid';
	protected $_dependentTables = array( 'tips_summary' );
	protected $accessible_attributes = array( 'eventid', 'eventname', 'sport', 'startdate', 'enddate', 'starttime' );
	
	public function all( $params = array() ) {
		return $this->cacheFetchAll( $this->prepare( $params ) );
	}
	
	public function one( $id ) {
		$select = $this->prepare( array( 'id' => $id ) );
		$select->where( 'eventid = ?', $id );
		
		return $this->cacheFetchAll( $select );
	}

  public function findAllLikeEventName( $event_name ) {

    $select = $this->select()->from( 'tips_mobile', array( 'tips_mobile.*', '(events_geodata.latitude)', '(events_geodata.longitude)' ) );

    $select->where( 'tips_mobile.eventname LIKE ?', '%' . $event_name . '%' );
    $select->where( 'tips_mobile.event_start > NOW()' );
    $select->joinLeft( 'events_geodata', 	'tips_mobile.eventname = events_geodata.eventname', array() );
    $select->order( 'tips_mobile.sport' )->order( 'eventname' );
    $select->group( 'eventname' )->group('event_start');

    $select->setIntegrityCheck(false);

    //echo $select->__toString();
    return $this->cacheFetchAll( $select );
  }

  public function getGeoData( $event_name, $event_date ) {
    $stmt = $this->db()->query( 'SELECT * FROM events_geodata WHERE eventname = ? AND eventdate = ?', $event_name, $event_date );
    return $stmt->fetchAll();
  }

  public function allByGeo( $lat, $long ) {

    // haversine formula - 6371 = KM // 3959 = miles
    $sql = "SELECT id, eventname, eventdate, ( 6371 * acos( cos( radians( $lat ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( $long ) ) + sin( radians( $lat ) ) * sin( radians( latitude ) ) ) ) AS distance FROM events_geodata HAVING distance < 25 ORDER BY distance LIMIT 1";
    #echo $sql;
    $stmt = $this->db()->query( $sql );

    return $stmt->fetchAll();
  }

  public function update( $params = array(), $event = array() ) {

    if ( isset( $params['latitude'] ) || isset( $params['longitude'] ) ) {

      $geo_data = array();

      if ( isset( $params['latitude'] ) ) {
        $geo_data['latitude'] = $params['latitude'];
        unset( $params['latitude'] );
      }
      if ( isset( $params['longitude'] ) ) {
        $geo_data['longitude'] = $params['longitude'];
        unset( $params['longitude'] );
      }

      if ( ! empty( $geo_data ) ) $this->updateGeoData( $geo_data, $event );

    }

  }

  private function updateGeoData( $geo_data = array(), $event = array() ) {

    $stmt = $this->db()->query( 'SELECT * FROM events_geodata WHERE eventname = ? AND ( eventdate = ? OR eventdate IS NULL )', array( $event['eventname'], $event['event_start'] ) );
    $row = $stmt->fetchAll();

    if ( empty( $row ) ) $this->db()->query( 'INSERT INTO events_geodata SET latitude = ?, longitude = ?, eventname = ?, eventdate = ?', array( $geo_data['latitude'], $geo_data['longitude'], $event['eventname'], $event['event_start'] ) );
    else {
      $this->db()->query( 'UPDATE events_geodata SET latitude = ?, longitude = ? WHERE id = ?', array( $geo_data['latitude'], $geo_data['longitude'], $row['id'] ) );
    }
  }

	protected function prepare( $params = array() ) {
		
		$this->selectable_attributes = $this->accessible_attributes;
		
		$select = $this->select()->from( $this->getTableName(), $this->selectable_attributes );
		
		if ( !empty( $params ) ) $params = $this->accessibleParams( $params, $this->accessible_attributes, $this->accessible_filters );
		if ( !empty( $params ) ) $select = $this->applyParams( $params, $select );
		
		# add default query parts if not manually set
		if ( ! (bool) $select->getPart( Zend_Db_Select::ORDER ) ) 	$select->order( array( 'sport ASC', 'eventname ASC', 'startdate ASC' ) );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::HAVING ) ) 	$select->having( '`tips_per_event` >= 10' );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::LIMIT_COUNT ) || ! (bool) $select->getPart( Zend_Db_Select::LIMIT_OFFSET ) ) $select->limitPage(0, 50);
		
		# echo $select->__toString();
		
		return $select;
	}

}