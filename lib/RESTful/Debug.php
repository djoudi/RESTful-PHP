<?
abstract class RESTful_Debug {

	public static function dump( $var, $label = null, $echo = true, $css_class = null ) {

		if ( empty( $css_class ) ) return Zend_Debug::dump( $var, $label, $echo );
		
		$output = Zend_Debug::dump( $var, $label, false );
		$output = str_ireplace( '<pre>', "<pre class='{$css_class}'>", $output );
		if ( $echo ) echo $output;
		
		return $output;
	}

}