<?
abstract class RESTful_Model extends Zend_Db_Table_Abstract {
	
	public static $db_adapter; # the connection
	
	# private $zend_db_table; # the actual table used for queries
	# private $registry_name; # the object's id in the registry
	
	protected $accessible_attributes; # can be used for DB operations - can include composed values: COUNT(id) AS "acc_attr"
	protected $selectable_attributes; # can be used for db selects - can't include composed values
	protected $accessible_filters = array ( 'order', 'rorder', 'paginate', 'having', 'custom_filter' );
	
	protected $_view_name; # when using views
	protected $_base_name; # when using views store _name here
	
	protected $last_query_cached;
	
	public static function factory( $class_name, $table_name = null, $adapter_name = 'default' ) {
	
		RESTful_Loader::loadDbConfig( $adapter_name );
		RESTful_Loader::loadModel( $class_name );
		Zend_Db_Table::setDefaultAdapter( RESTful_Model::$db_adapter );
		
		return new $class_name();
	}
	
	public function init() {}
  	
	protected function getTableName() {
  		return $this->_name;
 	}
  
	protected function getBaseTableName() {
		return $this->_base_name;
	}
	
	public function wasLastQueryCached() {
		return $this->last_query_cached;
	}
	
	public function cache( $param, $zend_db_method = null ) {
	
		if ( is_null( $zend_db_method ) ) {
			$zend_db_method = $param; 
			$param = null;
		}
		
		$cache_id = RESTful_Cache::getCacheId( $param, $zend_db_method . $this->id() );
		$result = RESTful_Cache::load( $cache_id );
		
		if ( $result === false ) { 
			
			$result = $this->$zend_db_method( $param );
			RESTful_Cache::save( $result, $cache_id );
			
			$this->last_query_cached = false;
			
		} else $this->last_query_cached = true;
		
		return $result;
	}
	
	public function id() {
	
		$conn = $this->db()->getConfig();
		
		return hash_hmac( 'sha256', $conn['host'] . '~' . $conn['dbname'] . '~' . implode( '~', $conn['driver_options'] ) . '~' . $conn['charset'] . '~' . implode( '~', $conn['options'] ) . '~' . $this->getTableName(), get_class( $this ) );
		
	}
	
	protected function accessibleParams( $params, $attributes, $filters ) {
		$valid_params = array_flip( array_intersect( array_keys( $params ), array_merge( $attributes, $filters ) ) );
		return array_intersect_key( $params, $valid_params );
	}
	
	protected function applyParams( $params, Zend_DB_Select $select ) {
	
		foreach ( $params as $key => $value ) {
			if ( in_array( $key, $this->accessible_attributes ) ) $select->where( "{$key} = ?", $value );
			if ( in_array( $key, $this->accessible_filters ) ) $select = $this->applyFilters( $key, $value, $select );
		}
		
		return $select;
	}
	
	protected function applyFilters( $filter, $value, Zend_DB_Select $select ) {
	
		if ( $filter == 'paginate' ) { # paginate=2-10
			if ( stripos( $value, '-' ) ) $value = explode( '-', $value );
			$select->limitPage( intval( $value[0] ), intval( $value[1] ) );
		} elseif ( $filter == 'order' && in_array( $value, $this->accessible_attributes ) ) { # order asc
			$select->order( array( "{$value} ASC" ) );
		} elseif ( $filter == 'rorder' && in_array( $value, $this->accessible_attributes ) ) { # order asc
			$select->order( array( "{$value} DESC" ) );
		} elseif ( $filter == 'having' ) { # order asc
			# $havings = explode( '+', $value );
			if( is_array( $value ) ) {
				foreach( $value as $having ) {
					$select->having( $this->processQueryData( $having ) );
				}
			} else {
				$select->having( $this->processQueryData( $value ) );
			}
		} elseif ( stripos( $filter, 'custom_filter' ) !== false ) {
			$this->$filter( $value, $select );
		}
		
		return $select;
	}
	
	private function processQueryData( $having ) {
		$having = str_replace( 'gt', '>', $having );
		$having = str_replace( 'lt', '<', $having );
		$having = str_replace( 'eq', '=', $having );
		
		return str_replace( array( '(', ')' ), ' ', $having );
	}
	
	protected function _setupTableName() {
		parent::_setupTableName();
	}
	
	public function db() {
		return RESTful_Model::$db_adapter;	
	}
	
	function __call( $method_name, $params = array() ) {
	
		if ( stripos( $method_name, 'cache' ) === 0 ) { # auto cache overloading
			
			$rm = new ReflectionMethod( 'RESTful_Model', 'cache' );
			
			$zend_db_method = str_ireplace( 'cache', '', $method_name ); $zend_db_method{0} = strtolower( $zend_db_method{0} ); # no lcfirst if php < 5.3 :(
			$params[] =  $zend_db_method;
			
			return $rm->invokeArgs( $this, $params );
		}
		
		# add 'By' - findBy FetchAllBy ...
		
	}

}