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
	
	public function getEvent( $event, &$vc_event = array() ) {
			
		# this should not be retested every time!!!
		$event = $this->map_sports( $event ); if ( is_null( $event ) ) return null; 
		
		$vc_event = $this->queryEvent( $event );
		if ( isset( $vc_event[0]['id'] ) ) {
			$vc_event = $vc_event[0];
		}
		else return null;
			
		return $vc_event['id'];
	}
	
	public function getOutcomeId( &$event, $vc_event_id ) {
	
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
		
		if ( $event['sport'] == 'horseracing' ) $event['eventname'] = $this->processHrEventName( $event['eventname'] );
		
		$select = $this->db->select()
										->from( array( 'i' => 'instance_data_' . $event['sport'] . '_events'), '*' )
										->where( ' ( event_name = ? OR TRIM( REPLACE( REPLACE( REPLACE( `event_name`, " - ", " " ), ")", "" ), "(", "" ) ) = ? ) ', trim( $event['eventname'] ) )
										->where( 'event_date = ?', $event_datetime[0] );
		if ( $event['sport'] == 'horseracing' ) $select->where( 'event_time = ?', $event_datetime[1] );
		
		echo $select->__toString();
		return $this->db->query($select)->fetchAll();
	}
	
	private function processHrEventName( $event_name ) {
		preg_match( '/([0-9]*):([0-9]*) (.*)/', $event_name, $matches ); 
		if ( isset( $matches[3] ) ) return $matches[3];
		else return $event_name;
	}
	
	private function queryOutcome( &$event, $vc_event_id ) {
		
		echo '<pre>' . print_r( $event, true ) . '</pre>';
		
		if ( $event['sport'] == 'basketball' || $event['sport'] == 'icehockey' ) $event['selection'] = $this->processOutcomeNamesWithHandicaps( $event['selection'] );
		$event['selection'] = trim( str_replace( array( '  ', "'" ), array( ' ', '' ), $event['selection'] ) );
		
		$select = $this->db->select()
										->from( array( 'i' => 'instance_data_' . $event['sport'] . '_outcomes'), 'id' )
										->where( 'event_id = ?', $vc_event_id )
										->where( 'market_id = ?', $event['marketid'] )
										->where( '( outcome_name = \'' . $event['selection'] . '\' OR outcome_name = \'' . ( isset( $event[ $event['selection'] ] ) ? $event[ $event['selection'] ] : '-NA-' ) . '\' )' );
		
		echo $select->__toString() . '<br/>';
		return $this->db->query($select)->fetchAll();
	}
	
	private function processOutcomeNamesWithHandicaps( $outcome_name ) {
		preg_match( '/([A-Za-z ]*)/', $outcome_name, $matches ); 
		if ( isset( $matches[0] ) ) return trim( $matches[0] );
		else return $outcome_name;
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
	
	public function parseHomeAwayTeams( &$event, $vc_event ) {
		
		if ( stripos( $vc_event['event_name'], ' v ' ) ) {
			$teams = explode( ' v ', $vc_event['event_name'] );
			$event['team_home_name'] = trim( $teams[0] );
			$event['team_away_name'] = trim( $teams[1] );
			
			$event['team_home_id'] = $vc_event['team_home_id'];
			$event['team_away_id'] = $vc_event['team_away_id'];
			
			$event[ trim( $teams[0] ) ] = 'Home Win';
			$event[ trim( $teams[1] ) ] = 'Away Win';
			
			return true;
		}
		
		if ( stripos( $vc_event['event_name'], ' @ ' ) ) {
			$teams = explode( ' @ ', $vc_event['event_name'] );
			$teams = array_reverse( $teams );
			$event['team_home_name'] = trim( $teams[0] );
			$event['team_away_name'] = trim( $teams[1] );
			
			$event['team_home_id'] = $vc_event['team_home_id'];
			$event['team_away_id'] = $vc_event['team_away_id'];
			
			$event[ trim( $teams[0] ) ] = 'Home Win';
			$event[ trim( $teams[1] ) ] = 'Away Win';
			
			return true;
		}
		
		return false; 
	}

}

function quote_bookie_names( $bookies ) {
	
	$result = array();
	foreach ( $bookies as $bookie ) $result[] = '"' . $bookie . '"';
	
	return $result;
	
}