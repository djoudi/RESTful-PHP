<?
class Odds_RESTful_Controller extends RESTful_Controller {
	
	public function import() {
		
		$this->Odd->import();
		
	}
	
	public function best( $sport = null ) {
		
		$session = date( 'Y-m-d H:i:s', time() );
		mail( NOTIFY_THEM, 'starting to process best odds for tips', 'yep' );
		
		$event_mappings = array();
		$outcome_mappings = array();
		
		$mobile_bookies = $this->Bookie->mobileBookies()->toArray();
		$mobile_bookies_names = array_map( 'map_bookies_names', $mobile_bookies );
		
		$events = $this->Tip->eventsWithTips( $sport );
		
		$this->Tip->refreshTable();
		
		$counter = 0;
		foreach ( $events as $event ) {
			
			echo '<pre>' . print_r( $event->toArray(), true ) . '</pre>';
			echo ++ $counter . '<br/>'; 
			
			$event = $event->toArray();
			$vc_event = array();
			
			$event_id = $this->ValueChecker->getEvent( $event, &$vc_event );
			echo 'event_id: ' . $event_id . '<br/>';
			if ( ! $event_id ) {
				echo '<span style="color: red">event not found!</span>';
				echo '<pre> event: ' . print_r( $event, true ) . '</pre>';
				echo '<pre> vc_event: ' . print_r( $vc_event, true ) . '</pre>';
				
				continue;
			}
			
			
			echo '<hr/> so now we have ';
			echo '<pre>' . print_r( $event, true ) . '</pre>';
			echo '<pre>' . print_r( $vc_event, true ) . '</pre>';
			echo '<hr/>';
			if ( ! empty( $vc_event ) ) $this->ValueChecker->parseHomeAwayTeams( &$event, $vc_event ); # got used to ruby - really like to change param objects in outer methods instead of returning them
			
			$outcome_id = $this->ValueChecker->getOutcomeId( $event, $event_id );
			echo 'outcome_id: ' . $outcome_id . '<br/>';
			if ( ! $outcome_id ) {
				echo '<span style="color: red">outcome not found!</span>';
				echo '<pre> event: ' . print_r( $event, true ) . '</pre>';
				echo '<pre> vc_event: ' . print_r( $vc_event, true ) . '</pre>';
				
				continue; 
			}
			
			$best_odds = $this->ValueChecker->getBestOdds( $event, $outcome_id, $mobile_bookies_names );
			echo 'odds: ' . $best_odds . '<br/>';
			if ( ! $best_odds ) {
				echo '<span style="color: red">odds not found!</span>';
				echo '<pre>' . print_r( $event, true ) . '</pre>';
				
				continue;
			}
			
			echo '<hr/>';
			
			if ( ! $this->Tip->updateBestOdds( $event, $best_odds ) ) {
				mail( NOTIFY_THEM, 'odds update has failed', print_r( $event, true ) . ' --- ' . print_r( $best_odds, true ) );
			}
		}
		
		echo 'OK'; 
		mail( NOTIFY_THEM, 'ending process best odds for tips', date( 'Y-m-d H:i:s', time() ) );
	}
	
}

function map_bookies_ids( $bookie_row ) {
	
	return $bookie_row['providerid'];
	
}

function map_bookies_names( $bookie_row ) {
	
	return $bookie_row['bookmaker']; 
	
}