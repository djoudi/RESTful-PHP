<?
class Sports_RESTful_Controller extends RESTful_Controller {
  
  public function index() {
  
    $this->sports = $this->Sport->all( $this->params );
    $this->respond( $this->sports );
  
  }
  
  public function show() {
    $this->respond( $this->Sport->one( $this->params['id'] ) );
  }
  
  public function categories() {
    $this->respond( $this->Sport->categories() );
  }
  
  public function categories_with_tips() {
    $this->respond( $this->Sport->categories_with_tips() );
  }
  
  public function subsports_with_tips() {
    $sport = strtolower(str_replace('_', ' ', $this->params['sport'] ));
    if($sport == "horse racing")
      $this->respond( $this->Tip->subsports_with_tips( $sport ) );
    else 
      $this->respond( $this->Sport->subsports_with_tips( $sport ) );
  }

  
  public function menu_categories() {
    $sport = str_replace('_', ' ', $this->params['sport']);
    $this->respond( $this->Sport->menu_categories( $sport ) );
  }

  public function menu_leagues() {
    $this->respond( $this->Sport->menu_leagues( 
      str_replace('__nbsp__', ' ', $this->params['sport'] ),
      str_replace('__nbsp__', ' ', $this->params['menu_cat'] ) ) );
  }
  
  protected function respond( $response_val, $all_label = 'all' ) {
    
    if ( $this->hasFormat( 'xml' ) || $this->hasFormat( 'json' ) ) $metaData = $this->metaData( $response_val );
    
    if ( !is_array( $response_val ) ) $response_array = $response_val->toArray();
    else $response_array = $response_val;

    // check for the 0 menu_call which is an equivalent for all
    if( array_key_exists( '0', $response_array ) )
      if( array_key_exists( 'menu_cat', $response_array[0] ) )
        if( $response_array[0]['menu_cat'] == '0' )
          $response_array[0]['menu_cat'] = $all_label;

    // check for the 0 menu_call which is an equivalent for all
    if( count( $response_array ) == 0 ) {
      $response_array[0]['subsport'] = 'all';
      $metaData['sports_count'] = 1;
    }

    if ( $this->hasFormat( 'xml' ) ) {
      
      $metaData = array_merge( $metaData, $this->params );
      $this->autorender( $response_array, array( 'root' => 'sports', 'elem' => 'sport', 'root_options' => ( (bool) $metaData ? $metaData : null ) ) );
    
    } elseif ( $this->hasFormat( 'json' ) ) $this->autorender( $response_array, $metaData );
    
    else $this->render();
  
  }
  
  protected function metaData( $response_val ) {
    
    return array( 
      
        'generated_at'  => date('Y-m-d H:i:s'), 
        'generated_in'  => RESTful_Profiler::timeElapsed( 'init' ) . ' sec', 
        'sports_count'  => count( $response_val ),
        'cached'        => $this->Sport->wasLastQueryCached() !== false ? 'YES' : 'NO',
        'type'          => 'sports',
        
      );
    
  }
  
}