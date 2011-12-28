<?php
class ValueChecker {

	private $db;
	
	private $events_list;
	private $outcomes_list;
	
	private $vc_sports_mappings;

	public function __construct() {
		$this->db = RESTful_Loader::loadDbConfig( 'valuechecker', true );
		RESTful_Loader::loadConfig( 'valuechecker' );
	}
	
	public function getEventId( $event ) {
		
		$event_hash = hash( 'sha256', $event['eventname'] . $event['event_start'] );
		
		if ( ! isset( $this->events_list[ $event_hash ] ) ) {
			
			# this should not be retested every time!!!
			$event = $this->map_sports( $event ); if ( is_null( $event ) ) return null; 
			
			$event_id = $this->queryEvent( $event );
			if ( isset( $event_id[0]['id'] ) ) $this->events_list[ $event_hash ] = $event_id;
			else return null;
		}
		
		return $this->events_list[ $event_hash ][0]['id'];
	}
	
	public function getOutcomeId( $event, $vc_event_id ) {
	
		$outcome_hash = hash( 'sha256', $event['eventname'] . $event['event_start'] . $event['marketid'] . $event['selection'] );
		
		if ( ! isset( $this->outcomes_list[ $outcome_hash ] ) ) {
			
			# this should not be retested every time!!!
			$event = $this->map_sports( $event ); if ( is_null( $event ) ) continue; 
		
			$outcome_id = $this->queryOutcome( $event, $vc_event_id );
			if ( isset( $outcome_id[0]['id'] ) ) $this->outcomes_list[ $outcome_hash ] = $outcome_id;
			else return null;
		}
		
		return $this->outcomes_list[ $outcome_hash ][0]['id'];
	
	}
	
	public function getBestOdds( $event, $outcome_id, $mobile_bookies_names ) {
		
		$bookie_names = quote_bookie_names( $mobile_bookies_names );
		$bookie_names = implode( ',', $bookie_names );
		
		# this should not be retested every time!!!
		$event = $this->map_sports( $event ); if ( is_null( $event ) ) continue; 
		
		$odds = $this->queryBestOdds( $event, $outcome_id, $bookie_names );
		
		if ( isset( $odds[0] ) ) $odds = $odds[0];
		else return null; 
		
		if ( $odds['provider_type'] == 2 ) $odds['odds_decimal_value'] = number_format( ( $odds['odds_decimal_value'] / 100 * ( 100 - $odds['default_commission'] ) ), 2 ); # exchange
		
		return $odds;
	}
	
	private function queryEvent( $event ) {
		
		$event_datetime = explode(' ', $event['event_start']);
		
		$select = $this->db->select()
										->from( array( 'i' => 'instance_data_' . $event['sport'] . '_events'), 'id' )
										->where( 'event_name = ?', $event['eventname'] )
										->where( 'event_date = ?', $event_datetime[0] )
										->where( 'event_time = ?', $event_datetime[1] );
		
		# echo $select->__toString();
		return $this->db->query($select)->fetchAll();
	}
	
	private function queryOutcome( $event, $vc_event_id ) {
		
		$select = $this->db->select()
										->from( array( 'i' => 'instance_data_' . $event['sport'] . '_outcomes'), 'id' )
										->where( 'event_id = ?', $vc_event_id )
										->where( 'market_id = ?', $event['marketid'] )
										->where( 'outcome_name = ?', $event['selection'] );
		
		# echo $select->__toString();
		return $this->db->query($select)->fetchAll();
	}
	
	private function queryBestOdds( $event, $outcome_id, $bookie_names ) {
		
		$select = $this->db->select()
										->from( 'instance_data_' . $event['sport'] . '_odds' )
										->columns( 'instance_data_providers.provider_name')->columns( 'instance_data_providers.provider_type' )->columns( 'instance_data_providers.default_commission' )
										->join( 'instance_data_providers', 	'instance_data_providers.id = provider_id', array() )
										->where( 'provider_name IN (' . $bookie_names . ')' )
										->where( 'outcome_id = ?', $outcome_id )
										->order( 'odds_decimal_value DESC' )->order( 'FIELD( provider_name, ' . $bookie_names . ' ) ASC' )
										->limit(1);
		
		# echo $select->__toString();
		return $this->db->query($select)->fetchAll();
	
	}
	
	private function map_sports( $event ) {
		
		$vc_sports_mappings = RESTful_Config::$vc_sports_mappings;
		
		if ( in_array( $event['sport'], $vc_sports_mappings['non_vc_sports'] ) ) return null;
			
		if ( in_array( $event['sport'], $vc_sports_mappings['auto_mapped_sports'] ) ) $event['sport'] = str_ireplace( ' ', '', $event['subsport'] );
		
		if ( array_key_exists( $event['sport'], $vc_sports_mappings['non_mapped_sports'] ) ) $event['sport'] = $vc_sports_mappings['non_mapped_sports'][ $event['sport'] ];
		elseif ( array_key_exists( $event['subsport'], $vc_sports_mappings['non_mapped_sports'] ) ) $event['sport'] = $vc_sports_mappings['non_mapped_sports'][ $event['subsport'] ];

		return $event; 
	}

}

function quote_bookie_names( $bookies ) {
	
	$result = array();
	foreach ( $bookies as $bookie ) $result[] = '"' . $bookie . '"';
	
	return $result;
	
}