<?
echo 'setting up an array of 2200 elements<br/>';
$outcome_ids = range( 33948726, 33948726 + 2200 );

echo 'connecting to DB<br/>';
$link = mysql_connect( '94.236.99.177', 'adrian', 'Schw3pp3s!@#' );
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'connected successfully<br/>';

mysql_select_db( 'odds101' );

echo 'setting up version 1 - select in loop:<br/>';

$st = microtime(true);
echo 'start time: ' . $st . '<br/>';

foreach ( $outcome_ids as $oid ) {
	mysql_query( 'SELECT SQL_NO_CACHE * FROM `instance_data_football_outcomes` WHERE `id` = ' . $oid );
}

$et = microtime(true);
echo 'end time: ' . $et . '<br/>';

echo 'setting up version 2 - select outside loop:<br/>';

$st2 = microtime(true);
echo 'start time: ' . $st2 . '<br/>';

mysql_query( 'SELECT SQL_NO_CACHE * FROM `instance_data_football_outcomes` WHERE `id` IN (' . implode( ', ', $outcome_ids ) . ')' );

$et2 = microtime(true);
echo 'end time: ' . $et2 . '<br/>';

echo 'setting up version 3 - select outside loop, with loop concatenation:<br/>';

$in = '';
$st3 = microtime(true);
echo 'start time: ' . $st3 . '<br/>';

foreach ( $outcome_ids as $oid ) {
	$in .= $oid . ', ';
}
$in[strlen($in)] = '';

mysql_query( 'SELECT SQL_NO_CACHE * FROM `instance_data_football_outcomes` WHERE `id` IN (' . $in . ')' );

$et3 = microtime(true);
echo 'end time: ' . $et3 . '<br/>';

echo '<hr/>';
echo 'Results: ';
echo 'v1: ' . ( ($et - $st) ) . '<br/>';
echo 'v2: ' . ( ($et2 - $st2) ) . '<br/>';
echo 'v3: ' . ( ($et3 - $st3) ) . '<br/>';