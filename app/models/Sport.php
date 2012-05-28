<?
class Sport extends RESTful_Model {

  protected $_name = 'sports';
  protected $_primary = 'id';
  protected $_dependentTables = array( 'tips_mobile' );
  protected $accessible_attributes = array( 'id', 'subsport', 'sport', 'sportid', 'tipable', 'category', 'crank', 'menu_cat' );
  
  protected $_referenceMap    = array(
      'Tip' => array(
      'columns'           => array('subsport'),
      'refTableClass'     => 'tips_mobile',
      'refColumns'        => array('sport')
        ),
    );
  
  public function all( $params = array() ) {
    return $this->cacheFetchAll( $this->prepare( $params ) );
  }
  
  public function one( $id ) {
    
    $select = $this->prepare( array( 'sportid' => $id ) );
    $select->where( 'sportid = ?', $id )->group('sportid');
    
    return $this->cacheFetchAll( $select );
  }
  
  public function categories() {
    $select = $this->select()->from( $this->_name, array('sport', 'rank', 'sportid') )->where( 'tipable', true )->group( 'sport' )->order( 'sport', 'ASC' )->order( 'rank', 'ASC' );
    # echo $select;
    
    return $this->cacheFetchAll( $select );
  }
  
  public function categories_with_tips() {
    $select = $this->select()
                    ->from( $this->_name, array( 'sport', 'rank', 'sportid' ) )
                    ->join( 'tips_mobile', 'tips_mobile.sport = sports.subsport', array() )
                    ->where( 'tipable', true )
                    ->group( 'sport' )
                    ->order( 'sport', 'ASC' )->order( 'rank', 'ASC' )->where( 'event_start >= NOW()' );
    #echo $select;
    return $this->cacheFetchAll( $select );
  }
  
  public function subsports_with_tips( $sport ) {

    $select = $this->select()
                    ->from( $this->_name, array( 'subsport', 'subrank', 'id' ) )
                    ->join( 'tips_mobile', 'tips_mobile.sport = sports.subsport', array() )
                    ->where( 'tipable', true )->where( 'sports.sport = ?', $sport )
                    ->group( 'sports.id' )
                    ->order( 'subsport', 'ASC' )->order( 'subrank', 'ASC' )->where( 'event_start >= NOW()' );
    return $this->cacheFetchAll( $select );
  }

  public function menu_categories( $sport ) {
    $select = $this ->select()
                    ->distinct()
                    ->from( $this->_name, 'menu_cat' )
                    ->join( 'tips_mobile', 'tips_mobile.sport = sports.subsport', array() )
                    ->order( 'sports.crank', 'ASC' )
                    ->where( 'sports.sport = ?', $sport );
    return $this->cacheFetchAll( $select );
  }

  public function menu_leagues( $sport, $menu_cat ) {

    if ( $sport == 'horseracing' || $sport == 'horse racing' || $sport == 'horse_racing' ) {
      return $this->menu_leagues_hr_exception( $sport, $menu_cat );
    } else {
      $select = $this->select()
                      ->distinct()
                      ->from( $this->_name, array( 'subsport' ) )
                      ->join( 'tips_mobile', 'tips_mobile.sport = sports.subsport', array() )
                      ->where( 'sports.menu_cat = ?', $menu_cat )
                      ->group( 'sports.id' )
                      ->order( 'subsport', 'DESC' );
                      
      return $this->cacheFetchAll( $select );
    }

  }
  
  private function menu_leagues_hr_exception( $sport, $menu_cat ) {
  
    $select = $this->select()->setIntegrityCheck(false)
                    ->distinct()
                    ->from( 'tips_mobile', array( 'eventname' ) )
                    ->group( 'eventname' )
                    ->where( 'sport = ?', array( 'Horse Racing' ) )
                    ->order( 'eventname', 'ASC' );
                    
    if ( $menu_cat != 'all' ) {
      $select->where( 'eventname like ?', array( '%' . $menu_cat . '%' ) );
    }
                    
    $courses = $this->cacheFetchAll( $select )->toArray(); 
    $course_names = array();
    foreach ( $courses as $course ) {
      if ( preg_match( '/([0-9]*):([0-9]*) (.*)/', $course['eventname'], $matches ) ) {
        if ( count( $matches ) == 4 ) {
          $course_names[] = $matches[3];
        }
      }
    }
    
    if ( !empty( $course_names ) ) {
      $course_names = array_values( array_unique( $course_names ) );
      sort( $course_names );
      
      $courses = array();
      foreach ( $course_names as $course_name ) {
        $courses[] = array( 'subsport' => $course_name );
      }

      return $courses;
    } 
  }

  protected function prepare( $params = array(), $selectable_attributes = array() ) {
    
    if ( empty( $selectable_attributes ) ) $this->selectable_attributes = $this->accessible_attributes;
    
    $select = $this->select()->from( $this->getTableName(), $this->selectable_attributes );
    
    if ( !empty( $params ) ) $params = $this->accessibleParams( $params, $this->accessible_attributes, $this->accessible_filters );
    if ( !empty( $params ) ) $select = $this->applyParams( $params, $select );
    
    # add default query parts if not manually set
    if ( ! (bool) $select->getPart( Zend_Db_Select::ORDER ) )   $select->order( array( 'sport ASC', 'subsport ASC' ) );
    # if ( ! (bool) $select->getPart( Zend_Db_Select::HAVING ) )  $select->having( '`tips_per_event` >= 10' );
    # if ( ! (bool) $select->getPart( Zend_Db_Select::LIMIT_COUNT ) || ! (bool) $select->getPart( Zend_Db_Select::LIMIT_OFFSET ) ) $select->limitPage(0, 50);
    
    # echo $select;
    
    return $select;
  }

}