<?
class RESTful_Loader_Rc extends RESTful_loader {

	public static function load( $class_name ) {
	
		$path = str_ireplace( RC, '', $class_name );
		
		$path_pieces = explode( DIRECTORY_SEPARATOR, str_ireplace( NSS, DIRECTORY_SEPARATOR, $path ) );
		$normalized_path_pieces = array();
		
		for( $iterator = new ArrayIterator( $path_pieces ); $iterator->valid(); $iterator->next() ) {
			$normalized_path_pieces[] = ucfirst( strtolower( $iterator->current() ) );
		}
		
		$path = implode( DIRECTORY_SEPARATOR, $normalized_path_pieces );
			
		require_once( C_PATH . $path . C . EXT );
	
	}

}