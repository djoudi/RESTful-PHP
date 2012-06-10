<?
# routes
# RESTful_Route::map( '/', 'tips' ); #default - site root
# RESTful_Route::map( 'tips/horseracing', 'tips_horseracing' ); # example 1
# RESTful_Route::map( '@tips/(?P<id>\d+)@', 'tips#show' ); # example 2
# RESTful_Route::mapResource( 'tips_football', array( ':only' => array( 'index', 'show' ) ) ); 
# RESTful_Route::map( 'tips/(?P<sport>\w+)', 'tips#bysport' );
# RESTful_Route::map( 'tips/(?P<sport>\w+)/(?P<id>\d+)', 'tips#bysport' );