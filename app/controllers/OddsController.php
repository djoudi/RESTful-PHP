<?
class Odds_RESTful_Controller extends RESTful_Controller {
	
	public function import() {
		
		$this->Odd->import();
		
	}
	
	public function best( $sport = null ) {
		
		$event_mappings = array();
		$outcome_mappings = array();
		
		$mobile_bookies = $this->Bookie->mobileBookies()->toArray();
		$mobile_bookies_names = array_map( 'map_bookies_names', $mobile_bookies );
		
		$events = $this->Tip->eventsWithTips( $sport );
		
		if ( $events->count() > 1000 ) $this->Tip->refreshTable();
		else mail( NOTIFY_THEM, 'tips update has failed', 'just ' . $events->count() . ' tips' );
		
		$counter = 0;
		foreach ( $events as $event ) {
			
			echo '<pre>' . print_r( $event->toArray(), true ) . '</pre>';
			echo ++ $counter . '<br/>'; 
			
			$event = $event->toArray();
			
			$event_id = $this->ValueChecker->getEventId( $event );
			echo 'event_id: ' . $event_id . '<br/>';
			if ( ! $event_id ) continue;
			
			$outcome_id = $this->ValueChecker->getOutcomeId( $event, $event_id );
			echo 'outcome_id: ' . $outcome_id . '<br/>';
			if ( ! $outcome_id ) continue;
			
			$best_odds = $this->ValueChecker->getBestOdds( $event, $outcome_id, $mobile_bookies_names );
			echo 'odds: ' . $best_odds . '<br/>';
			if ( ! $best_odds ) continue;
			
			echo '<hr/>';
			
			if ( ! $this->Tip->updateBestOdds( $event, $best_odds ) ) mail( NOTIFY_THEM, 'odds update has failed', print_r( $event->toArray(), true ) . ' --- ' . print_r( $best_odds, true ) );
		}
		
		echo 'OK';
	}
	
}

function map_bookies_ids( $bookie_row ) {
	
	return $bookie_row['providerid'];
	
}

function map_bookies_names( $bookie_row ) {
	
	return $bookie_row['bookmaker']; 
	
}