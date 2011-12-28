<?
class Odd extends RESTful_Model {

	protected $_name = 'tips_mobile';
	protected $accessible_attributes = array( '(sports.sport)', '(tips_mobile.sport) AS subsport', 'eventname', 'marketid', 'TRIM(selection) AS selection' );
	
	public function import() {
		
		$select = $this->select()
								->from( 'tips_mobile', $this->accessible_attributes )
								->join( 'sports', 'tips_mobile.sport = sports.subsport', array() )
								->where( 'event_start >= NOW()' );
														
		# echo $select;
		$markets_and_outcomes_per_sport = $this->cacheFetchAll( $select );
		RESTful_Debug::dump( $markets_and_outcomes_per_sport );
		
	}
	
	public function best() {
		
		
		
	}
	
}