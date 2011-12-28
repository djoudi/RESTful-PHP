<?
class Odds_RESTful_Controller extends RESTful_Controller {
	
	public function import() {
		
		$this->Odd->import();
		
	}
	
	public function best( $sport = null ) {
		
		$event_mappings = array();
		
		$events = $this->Tip->eventsWithTips( $sport );
		foreach ( $events as $event ) {
		
			
		
		}
	
	}
	
	
}