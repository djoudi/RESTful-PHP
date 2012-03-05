<?
class Admin_Bookies_RESTful_Controller extends Bookies_RESTful_Controller {
	
	public function index() {
	
		$this->bookies = $this->Bookie->mobileBookies( $this->params );
		$this->respond();
	
	}
	
	public function update() {
		$this->answer( $this->Bookie->update( array( 'live_mobile' => ( $_POST['live_mobile'] == "true" ? 1 : 0 ) ), "bookie_id = " . $this->params['id'] ) ); # validate!
	}
	
	public function sort() {
		
		$order = $_POST['bookie']; # validate!
		$this->answer( $this->Bookie->sort( $order ) ? 'OK' : 'KO' );
		
	}
	
	protected function respond( $response_val = null ) {
		
		if ( $this->hasFormat( 'xml' ) || $this->hasFormat( 'json' ) ) $metaData = $this->metaData( $response_val );

		if ( $this->hasFormat( 'xml' ) ) {
			
			$metaData = array_merge( $metaData, $this->params );
			$this->autorender( $response_val->toArray(), array( 'root' => 'bookies', 'elem' => 'bookie', 'root_options' => ( (bool) $metaData ? $metaData : null ) ) );
		
		} elseif ( $this->hasFormat( 'json' ) ) {
			
			$this->autorender( is_null( $response_val ) ? array() : $response_val->toArray(), $metaData );
			
		} else $this->render();
	
	}
	
	protected function metaData( $response_val ) {
		
		return array( 
			
				'generated_at' 		=> date('Y-m-d H:i:s'), 
				'generated_in' 		=> RESTful_Profiler::timeElapsed( 'init' ) . ' sec', 
				'bookies_count' 	=> count( $response_val ),
				'cached'			=> $this->Tip->wasLastQueryCached() !== false ? 'YES' : 'NO',
				'type'				=> 'bookies'
				
			);
		
	}
	
	public function before() {
	
		if ( !RESTful_Auth::authenticate( $this->Bookie->db() ) ) {
			RESTful_Application::run( 'authentications/add.html' );
			exit;
		}
		
		$this->layout()->setLayout('admin');
	}
	
}