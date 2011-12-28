<?
class Sport extends RESTful_Model {

	protected $_name = 'sports';
	protected $_primary = 'id';
	protected $_dependentTables = array( 'tips_summary' );
	protected $accessible_attributes = array( 'id', 'subsport', 'sport', 'sportid', 'tipable', 'category' );
	
	protected $_referenceMap    = array(
      'Tip' => array(
			'columns'           => array('subsport'),
			'refTableClass'     => 'tips_summary',
			'refColumns'        => array('sport')
        ),
    );
	
	public function all( $params = array() ) {
		return $this->cacheFetchAll( $this->prepare( $params ) );
	}
	
	public function one( $id ) {
		
		$select = $this->prepare( array( 'sportid' => $id ) );
		$select->where( 'sportid = ?', $id )->group('sportid');
		
		return $this->cacheFetchAll( $select );
	}
	
	public function categories() {
		$select = $this->select()->from( $this->_name, array('sport', 'rank', 'sportid') )->where( 'tipable', true )->group( 'sport' )->order( 'sport', 'ASC' )->order( 'rank', 'ASC' );
		# echo $select;
		
		return $this->cacheFetchAll( $select );
	}
	
	public function categories_with_tips() {
		$select = $this->select()
										->from( $this->_name, array( 'sport', 'rank', 'sportid' ) )
										->join( 'tips_summary', 'tips_summary.sport = sports.subsport', array() )
										->where( 'tipable', true )
										->group( 'sport' )
										->order( 'sport', 'ASC' )->order( 'rank', 'ASC' )->where( 'event_start >= NOW()' );
		#echo $select;
		return $this->cacheFetchAll( $select );
	}
	
	public function subsports_with_tips( $sport_id ) {
		
		$select = $this->select()
										->from( $this->_name, array( 'subsport', 'subrank', 'id' ) )
										->join( 'tips_summary', 'tips_summary.sport = sports.subsport', array() )
										->where( 'tipable', true )->where( 'sports.sport = ?', $sport_id )
										->group( 'sports.id' )
										->order( 'subsport', 'ASC' )->order( 'subrank', 'ASC' )->where( 'event_start >= NOW()' );
										
		return $this->cacheFetchAll( $select );
	}
	
	protected function prepare( $params = array(), $selectable_attributes = array() ) {
		
		if ( empty( $selectable_attributes ) ) $this->selectable_attributes = $this->accessible_attributes;
		
		$select = $this->select()->from( $this->getTableName(), $this->selectable_attributes );
		
		if ( !empty( $params ) ) $params = $this->accessibleParams( $params, $this->accessible_attributes, $this->accessible_filters );
		if ( !empty( $params ) ) $select = $this->applyParams( $params, $select );
		
		# add default query parts if not manually set
		if ( ! (bool) $select->getPart( Zend_Db_Select::ORDER ) ) 	$select->order( array( 'sport ASC', 'subsport ASC' ) );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::HAVING ) ) 	$select->having( '`tips_per_event` >= 10' );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::LIMIT_COUNT ) || ! (bool) $select->getPart( Zend_Db_Select::LIMIT_OFFSET ) ) $select->limitPage(0, 50);
		
		# echo $select;
		
		return $select;
	}

}