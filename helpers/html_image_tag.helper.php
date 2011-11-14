<?
function html_image_tag( $params ) {

	$src = '';
	$alt = '';
	$output = '';
	
	if ( is_array( $params ) ) {
		if ( isset( $params['src'] ) && !empty( $params['src'] ) ) $src = $params['src'];
		else return null;
		
		if ( isset( $params['alt'] ) && !empty( $params['alt'] ) ) $alt = htmlspecialchars( $params['alt'] );
		else $alt = '';
	} else {
		if ( empty( $params ) ) return null;
		
		$src = $params;
		$alt = '';
	}
	
	$output = "<img src='{$src}' alt='{$alt}'";
	
	if ( is_array( $params ) ) {
		foreach ( $params as $key => $val ) {
			if ( $key != 'src' && $key != 'alt' ) $output .= " {$key}='{$val}'";
		}
	}
	
	$output .= ' />';
	return $output;
}