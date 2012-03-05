<?
# routes
# RESTful_Route::map( '/', 'tips' ); #default - site root
# RESTful_Route::map( 'tips/horseracing', 'tips_horseracing' ); # example 1
# RESTful_Route::map( '@tips/(?P<id>\d+)@', 'tips#show' ); # example 2
# RESTful_Route::mapResource( 'tips_football', array( ':only' => array( 'index', 'show' ) ) ); 
# RESTful_Route::map( 'tips/(?P<sport>\w+)', 'tips#bysport' );
# RESTful_Route::map( 'tips/(?P<sport>\w+)/(?P<id>\d+)', 'tips#bysport' );

RESTful_Route::map( ':root', 'tips#hot' ); #default - site root

RESTful_Route::map( 'tips/hot', 'tips#hot' );
RESTful_Route::map( 'tips/hot/(?P<id>\d+)', 'tips#hot' );
RESTful_Route::map( 'tips/(?P<hash>\w+)/comments', 'tips#CommentsByHash' );
RESTful_Route::map( 'tips/(?P<hash>\w+)/tipsters', 'tips#TipstersByHash' );

RESTful_Route::mapResource( 'tips', array( ':only' => array( 'index', 'show' ) ) ); 

RESTful_Route::map( 'tips/(?P<sport>\w+)', 'tips#bysport' );
RESTful_Route::map( 'tips/(?P<sport>\w+)/(?P<id>\d+)', 'tips#bysport' );

RESTful_Route::map( 'sports/categories', 'sports#categories' );
RESTful_Route::map( 'sports/categories_with_tips', 'sports#categories_with_tips' );
RESTful_Route::map( 'sports/(?P<sport>\w+)/leagues_with_tips', 'sports#subsports_with_tips' );

# subpages routes
RESTful_Route::map( 'sports/(?P<sport>\w+)/menu_categories', 'sports#menu_categories' );
RESTful_Route::map( 'sports/(?P<sport>\w+)/(?P<menu_cat>\w+)/menu_leagues', 'sports#menu_leagues' );
RESTful_Route::map( 'tips/(?P<menu_league>\w+)/menu_events', 'tips#menu_events' );

RESTful_Route::mapResource( 'sports', array( ':only' => array( 'index', 'show' ) ) ); 

RESTful_Route::mapResource( 'markets', array( ':only' => array( 'index', 'show' ) ) ); 
RESTful_Route::mapResource( 'events', array( ':only' => array( 'index', 'show' ) ) ); 

RESTful_Route::mapResource( 'admin/bookies' ); 
RESTful_Route::map( 'admin/bookies/sort', 'admin/bookies#sort' );

RESTful_Route::mapResource( 'authentications' );
RESTful_Route::map( 'authentications/log_off', 'authentications#destroy' );

RESTful_Route::map( 'odds/import', 'odds#import' );
RESTful_Route::map( 'odds/best', 'odds#best' );