<?
class RESTful_Profiler extends RESTful_Application {
	
	private static $timers = array();
	
	public static function init() {
		RESTful_Profiler::$timers['init_time'] = microtime();
	}
	
	public static function timeElapsed( $since = 'init', $current_time = null ) {
	
		$since .= '_time';
		if ( is_null( $current_time ) ) $current_time = microtime();
		
		if ( ! isset( RESTful_Profiler::$timers[$since] ) ) return null;
		
		return ( $current_time - RESTful_Profiler::$timers[$since] );
		
	}
	
	public static function setTimer( $timer_name ) {
		
		RESTful_Profiler::$timers[$timer_name] = microtime();
		
	}
	
	public static function getTimer( $timer_name ) {
		
		if ( ! isset( RESTful_Profiler::$timers[$timer_name] ) ) return null;
		
		return RESTful_Profiler::$timers[$timer_name];
		
	}
	
	function __get( $name ) {
	
		return RESTful_Profiler::getTimer( $name );
	
	}
	
	function __set( $name, $value ) {
	
		return RESTful_Profiler::setTimer( $name, $value );
	
	}

}