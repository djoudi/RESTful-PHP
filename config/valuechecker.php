<?
RESTful_Config::$vc_sports_mappings = array(
	'non_vc_sports' => array( 'darts', 'dogs', 'gaelic', 'rowing' ), # don't use them -- not available in vc
	'auto_mapped_sports' => array( 'rugby', 'us sports' ), # use subsport instead of sport
	'non_mapped_sports' => array( 'nhl' => 'icehockey', 'nfl' => 'americanfootball', 'horse racing' => 'horseracing' ) # use indicated mapping
);