<?
class Market extends RESTful_Model {

	protected $_name = 'markets';
	protected $_dependentTables = array( 'tips_summary' );
	protected $accessible_attributes = array( 'marketid', 'name', 'shortname', 'sport', 'live', 'ew', 'marketname', 'tipable' );
	
	public function all( $params = array() ) {
		return $this->cacheFetchAll( $this->prepare( $params ) );
	}
	
	public function one( $id ) {
		$select = $this->prepare( array( 'id' => $id ) );
		$select->where( 'marketid = ?', $id );
		
		return $this->cacheFetchAll( $select );
	}
	
	protected function prepare( $params = array() ) {
		
		$this->selectable_attributes = $this->accessible_attributes;
		
		$select = $this->select()->from( $this->getTableName(), $this->selectable_attributes );
		
		if ( !empty( $params ) ) $params = $this->accessibleParams( $params, $this->accessible_attributes, $this->accessible_filters );
		if ( !empty( $params ) ) $select = $this->applyParams( $params, $select );
		
		# add default query parts if not manually set
		if ( ! (bool) $select->getPart( Zend_Db_Select::ORDER ) ) 	$select->order( array( 'sport ASC', 'name ASC', 'marketname ASC' ) );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::HAVING ) ) 	$select->having( '`tips_per_event` >= 10' );
		# if ( ! (bool) $select->getPart( Zend_Db_Select::LIMIT_COUNT ) || ! (bool) $select->getPart( Zend_Db_Select::LIMIT_OFFSET ) ) $select->limitPage(0, 50);
		
		# echo $select->__toString();
		
		return $select;
	}

}