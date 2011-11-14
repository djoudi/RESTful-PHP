<?
function html_a_tag( $value, $params ) {

	$href = '';
	$title = '';
	$output = '';
	
	if ( is_array( $params ) ) {
		if ( isset( $params['href'] ) && !empty( $params['href'] ) ) $href = $params['href'];
		else return null;
		
		if ( isset( $params['title'] ) && !empty( $params['title'] ) ) $title = htmlspecialchars( $params['title'] );
		else $title = '';
	} else {
		if ( empty( $params ) ) return null;
		
		$href = $params;
		$title = '';
	}
	
	$output = "<a href='{$href}' title='{$title}'";
	
	if ( is_array( $params ) ) {
		foreach ( $params as $key => $val ) {
			if ( $key != 'href' && $key != 'title' ) $output .= " {$key}='{$val}'";
		}
	}
	
	$output .= ">{$value}</a>";
	return $output;
}