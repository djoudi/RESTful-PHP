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
		
		foreach ( $events as $event ) {
			
			$event = $event->toArray();
			
			$event_id = $this->ValueChecker->getEventId( $event );
			if ( ! $event_id ) continue;
			
			$outcome_id = $this->ValueChecker->getOutcomeId( $event, $event_id );
			if ( ! $outcome_id ) continue;
			
			$best_odds = $this->ValueChecker->getBestOdds( $event, $outcome_id, $mobile_bookies_names );
			if ( ! $best_odds ) continue;
			
			$this->Tip->updateBestOdds( $event, $best_odds );
		}
	}
	
}

function map_bookies_ids( $bookie_row ) {
	
	return $bookie_row['providerid'];
	
}

function map_bookies_names( $bookie_row ) {
	
	return $bookie_row['bookmaker']; 
	
}