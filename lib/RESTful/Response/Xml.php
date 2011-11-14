<?
class RESTful_Response_Xml extends RESTful_Response {

	public static function toString( $obj, $root_elem = '', $default_elem = '', $root_attribs = array(), $cache = true ) {
	
		$xml_serializer = new XML_Serializer();
		
		$options = array(
				XML_SERIALIZER_OPTION_XML_DECL_ENABLED 		=> true,
				XML_SERIALIZER_OPTION_DEFAULT_TAG			=> ( $default_elem ? $default_elem : 'element' ),
				XML_SERIALIZER_OPTION_DOCTYPE_ENABLED		=> true,
				XML_SERIALIZER_OPTION_ROOT_NAME				=> ( $root_elem ? $root_elem : 'RESTful_Response_Xml' ),
				XML_SERIALIZER_OPTION_ROOT_ATTRIBS			=> $root_attribs,
				XML_SERIALIZER_OPTION_CDATA_SECTIONS		=> false,
				# XML_SERIALIZER_OPTION_TAGMAP 				=> array('selection' => 'outcome', 'eventname' => 'event', 'sport' => 'league', 'event_start' => 'event_date')
			);
			
		$xml_serializer->setOptions( $options );
		$xml_serializer->serialize( $obj );
		
		return $xml_serializer->getSerializedData();
		
	}
	
	public static function autorender( $object, $xml_options = array(), $cache = false ) {
	
		if ( $cache ) {
			$result = RESTful_Cache::load( md5( RESTful_Application::getRequest()->getUrl() ) );
			if ( $result !== false ) return $result;
		}
			
		$result = RESTful_Response_Xml::toString( $object, $xml_options['root'], $xml_options['elem'], $xml_options['root_options'] );
		
		if( $cache ) RESTful_Cache::save( $result, md5( RESTful_Application::getRequest()->getUrl() ) );
		return $result;
	}

}