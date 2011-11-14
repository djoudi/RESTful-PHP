<?
class RESTful_Response_Json extends RESTful_Response {

	public static function toString( $object ) {
		
		/* +TODO
		$sj = new Services_JSON();
		return $sj->encode( $object );
		*/
		
		return json_encode( $object );
		
	}

	public static function autorender( $object, $metaData = array(), $cache = true ) {
	
		if ( ! RESTful_Application::getRequest()->getFormat() == 'json' ) return false;
		
		if ( $cache ) {
			$result = RESTful_Cache::load( md5( RESTful_Application::getRequest()->getUrl() ) );
			if ( $result !== false ) return $result;
		}
		
		$object = array( 'metadata' => $metaData, $metaData['type'] => $object );
		$result = RESTful_Response_Json::toString( $object );
		
		if( $cache ) RESTful_Cache::save( $result, md5( RESTful_Application::getRequest()->getUrl() ) );
		return $result;
		
	}

}