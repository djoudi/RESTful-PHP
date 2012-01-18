<?
class Tip extends RESTful_Model {
	
	protected $_name = 'tips_mobile';
	protected $_primary = 'id'; # only for models using views
	# protected $_view_name = 'current_tips'; # only for models using views
	
	protected $_referenceMap    = array(
      'Sport' => array(
			'columns'           => array('sport'),
			'refTableClass'     => 'Sport',
			'refColumns'        => array('subsport')
        ),
      'Market' => array(
			'columns'           => array('marketid'),
			'refTableClass'     => 'Market',
			'refColumns'        => array('marketid')
        ),
    );
	
	protected $accessible_attributes = array( 'id', 'TRIM( selection ) AS selection', 'eventname', 'marketid', '(sports.sport)', 'odds', 'bookmaker', 'event_start', 'win_tips', 'ew_tips', 'lay_tips', 'nap_tips', '(c.win_tips_count), (sports.sportid) AS sportid, (sports.id) AS subsport_id' );
	
	public function init() {
		# only for models using views
		# $this->_base_name = $this->_name;
		# $this->_name = $this->_view_name;
		# parent::_setupTableName();
	}
	
	public function hot( $params = array() ) {
	
		$accessible_attributes = array( 'odds' => '(odds)', 
										'sportname' => '(sports.sport)', 
										'event_start_unix_timestamp' => 'UNIX_TIMESTAMP(event_start)', 
										'odds_rounded' => 'ROUND( odds, 2 )',
										'market_name' => '(markets.name)',
										'confidence' => 'ROUND( win_tips * 100 / win_tips_count )',
										'bookie_id' => '(bookies.id)' );
		
		$this->selectable_attributes = array_merge( $this->accessible_attributes, $accessible_attributes );
		$this->accessible_attributes = array_merge( array_keys( $accessible_attributes ), $this->accessible_attributes );
		
		$select_confidence = $this->select()
									->from( $this->_name, array( 'eventname', 'marketid', new Zend_Db_Expr( 'SUM(win_tips) AS win_tips_count' ) ) )
									->where( 'event_start >= NOW()' )
									->group( 'eventname' )->group( 'marketid' )
									->order( 'eventname', 'ASC' )->order( 'marketid', 'ASC' );
		
		$select = $this->select()
									->from( $this->getTableName(), $this->selectable_attributes )
									->join( 'sports', 	$this->_name . '.sport = sports.subsport', array() )
									->join( 'markets', 	$this->_name . '.marketid = markets.marketid', array() )
									->join( 'bookies', 	$this->_name . '.bookmaker = bookies.bookmaker', array() )
									->join( array( 'c' => $select_confidence ), $this->_name . '.eventname = c.eventname AND ' . $this->_name . '.marketid = c.marketid', array() )
									->where( 'event_start >= NOW()' );
		
		if ( !empty( $params ) ) $params = $this->accessibleParams( $params, $this->accessible_attributes, $this->accessible_filters );
		if ( !empty( $params ) ) $select = $this->applyParams( $params, $select );
		
		# add default query parts if not manually set
		if ( ! (bool) $select->getPart( Zend_Db_Select::ORDER ) ) 	$select->order( array( 'win_tips DESC', 'odds DESC' ) );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::HAVING ) ) 	$select->having( '`tips_per_event` >= 10' );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::LIMIT_COUNT ) || ! (bool) $select->getPart( Zend_Db_Select::LIMIT_OFFSET ) ) $select->limitPage(0, 50);
		
		#echo $select;
		$result = $this->cacheFetchAll( $select );
		$unique_tips = array();
		$stored = array();
		foreach ( $result->toArray() as $tip ) {
			if ( isset( $stored[ md5( $tip['eventname'] . $tip['marketid'] ) ] ) ) continue;
			else {
				$unique_tips[] = $tip;
				$stored[ md5( $tip['eventname'] . $tip['marketid'] ) ] = true;
			}
		}
		
		# return $this->cacheFetchAll( $select );
		return $unique_tips;
	}
	
	public function bySport( $params = array() ) {
		
		$accessible_attributes = array( 'odds' => '(odds + 1)' );
		
		$this->selectable_attributes = array_merge( $this->accessible_attributes, $accessible_attributes );
		$this->accessible_attributes = array_merge( array_keys( $accessible_attributes ), $this->accessible_attributes );
		
		$select = $this->select()
									->from( $this->getTableName(), $this->selectable_attributes )
									->join( 'sports', $this->_name . '.sport = sports.subsport', array() )
									->where( 'sports.sport = ?', $params['sport'] );
		
		unset( $params['sport'] );
		
		if ( !empty( $params ) ) $params = $this->accessibleParams( $params, $this->accessible_attributes, $this->accessible_filters );
		if ( !empty( $params ) ) $select = $this->applyParams( $params, $select );
		
		# add default query parts if not manually set
		if ( ! (bool) $select->getPart( Zend_Db_Select::ORDER ) ) 	$select->order( array( 'win_tips DESC', 'odds DESC' ) );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::HAVING ) ) 	$select->having( '`tips_per_event` >= 10' );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::LIMIT_COUNT ) || ! (bool) $select->getPart( Zend_Db_Select::LIMIT_OFFSET ) ) $select->limitPage(0, 50);
		
		# echo $select->__toString();
		return $this->cacheFetchAll( $select );
		
	}
	
	public function eventsWithTips( $sport = null ) {
		
		$accessible_attributes = array( 'id', 'TRIM( selection ) AS selection', 'eventname', 'marketid', 'event_start', 'LOWER( ( sports.sport ) ) AS sport', 'LOWER( ( sports.subsport ) ) AS subsport' );
		
		$this->selectable_attributes = $this->accessible_attributes = $accessible_attributes;
		$this->accessible_attributes = array_merge( array_keys( $accessible_attributes ), $this->accessible_attributes );
		
		$select = $this->select()
									->from( $this->_name, $this->selectable_attributes )
									->join( 'sports', 	$this->_name . '.sport = sports.subsport', array() )
									->where( 'event_start >= NOW()' )
									->where( $sport ? 'sport = "' . $sport . '"' : 1 )
									->group( 'eventname' )->group( 'marketid' )->group( 'selection' )
									->order( 'win_tips DESC' )->order( 'marketid ASC' )->order( 'eventname ASC' ); 
		
		echo 'getting events list from tips <br/>';
		echo $select->__toString();
		return $this->cacheFetchAll( $select );
		
	}
	
	public function updateBestOdds( $event, $odds ) {
		
		$row = $this->fetchRow( $this->select()->where( 'id = ?', $event['id'] ) );
		
		if ( ! method_exists( $row, 'save' ) ) return false; 
		
		$row->odds = $odds['odds_decimal_value'];
		$row->bookmaker = $odds['provider_name'];
		
		$row->save();
		
		return true; 
	}
	
	protected function custom_filter( $value, Zend_DB_Select $select ) {
		$select->where('selection LIKE ? OR ' . $this->_name . '.eventname LIKE ?', '%' . $value . '%');
	}
	
	public function refreshTable( $table_name = 'tips_mobile' ) {
		echo 'refreshing table <br/>';
		
		$this->db()->query( "TRUNCATE TABLE `tips_mobile`" );
		$this->db()->query( "INSERT INTO `tips_mobile` SELECT * FROM `betting`.`tips_mobile`" );
	}

}