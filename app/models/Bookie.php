<?
class Bookie extends RESTful_Model {

	protected $_name = 'bookies';
	protected $_primary = 'id'; # only for models using views
	protected $accessible_attributes = array( 'providerid', 'bookmaker', 'logo', 'murl', '(bookies_mobile.live_mobile)' );
	protected $mobile_scope = 'live = "True" AND murl <> ""';
	
	public function mobileBookies() {
		
		$select = $this->select()
								->from( 'bookies', $this->accessible_attributes )
								->joinLeft( 'bookies_mobile', 'bookies_mobile.bookie_id = bookies.providerid', array() )
								->where( $this->mobile_scope )->where( 'live_mobile', 1 )
								->order( 'order', 'ASC' )->order( 'sortorder', 'ASC' );
										
		# echo $select;
		return $this->cacheFetchAll( $select );
	
	}
	
	public function sort( $sortOrder ) {
		
		if ( !is_array( $sortOrder ) || empty( $sortOrder ) ) return false;
		
		# get all data to persist live status
		$select = $this->select()->from( 'bookies', array( 'providerid', '(bookies_mobile.live_mobile)' ) )->joinLeft( 'bookies_mobile', 'bookies_mobile.bookie_id = bookies.providerid', array() )->where( $this->mobile_scope );
		$mobileBookies = $this->cacheFetchAll( $select )->toArray();
		
		# now truncate the table
		$this->db()->query( 'TRUNCATE TABLE `bookies_mobile`' );
		
		# now mix the sorting data with the live data and insert
		foreach( $sortOrder as $order => $bookie_id ) {
			foreach( $mobileBookies as $index => $bookie_data ) {
				if ( in_array( $bookie_id, $bookie_data ) ) {
					# insert data
					$this->db()->insert( 'bookies_mobile', array( 'bookie_id' => $bookie_id, 'order' => $order, 'live_mobile' => is_null( $bookie_data['live_mobile'] ) ? 1 : $bookie_data['live_mobile'] ) );
					
					# remove item from array
					unset( $mobileBookies[$index] );
				}
			}
		}
		
		return true;
	}
	
	public function update( $params, $condition ) {
		$this->db()->update( 'bookies_mobile', $params, $condition );
		return 'OK';
	}
	
}