<?
# setup env
define( 'APPLICATION_ENV', apache_getenv('APPLICATION_ENV') );
define( 'BASE_URL', "http://" . $_SERVER['HTTP_HOST'] . "/" );
define( 'BASE_PATH', realpath( dirname( __FILE__ ) . '/..' ) . "/" );

$_SERVER['PHP_SELF'] = str_ireplace( '/index.php', '', $_SERVER['PHP_SELF'] );

# error reporting
error_reporting( APPLICATION_ENV != 'production' ? E_ALL : E_NONE );
define( 'DEBUG', APPLICATION_ENV != 'production' ? true : false );


# setup helpers : comma separated 
define( 'APP_HELPERS', '' ); # generic app helpers - loaded all the time
define( 'HTML_APP_HELPERS', 'html_image_tag, html_a_tag' ); # html output helpers - loaded just for HTML output <- lazy loaded


# setup includes
set_include_path( BASE_PATH . ':' . BASE_PATH . "lib:" . ini_get( "include_path" ) ); # app libs
set_include_path( BASE_PATH . 'lib/PEAR:' . ini_get( "include_path" ) );	# local pear


# some defaults
#define( 'DEFAULT_MIME_TYPE', 'text/html' );
define( 'DEFAULT_MIME_TYPE', 'application/json' );
#define( 'DEFAULT_MIME_TYPE', 'application/xml' ); 