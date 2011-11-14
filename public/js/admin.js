var _ = {}; // global object

$(function() {
	// add sorting functionality
	$( "#bookies" ).sortable( { 
		items: 	"li:not(.header)", 
		stop: 	function(event, ui) { _.ajaxSubmitSorting( $(this).sortable("serialize") ); }
	} );
	$( "#bookies" ).disableSelection();
	
	// add enable / disable functionality
	$( '.live_bookie' ).click( function(){
		_.ajaxSubmitLiveStatus( $(this).attr('data-bookieid'), $(this).is(':checked') );
	});
});

_.ajaxSubmitSorting = function( serializedSort ) {
	_.showStatus("saving");
	$.ajax({
		type: "POST",
		url: "bookies/sort",
		data: serializedSort,
		success: function( msg ){
			if ( msg == 'OK' ) _.showStatus( 'success' );
			else _.showStatus( 'error' );
		}
	});
}

_.ajaxSubmitLiveStatus = function( bookie_id, checked ) {
	_.showStatus("saving");
	$.ajax({
		type: "POST",
		url: "bookies/" + bookie_id + "/update",
		data: 'live_mobile=' + checked,
		success: function( msg ){
			if ( msg == 'OK' ) _.showStatus( 'success' );
			else _.showStatus( 'error' );
		}
	});
}

_.showStatus = function( status ) {
	$('.help').slideUp();
	$('#status').html( '&nbsp;&raquo;&nbsp;' + status ).addClass( status ).slideDown();
	
	if ( status === 'success' ) setTimeout( "_.hideStatus()", 3000 );
	else if ( status === 'error' ) {
		if ( console != undefined && console != null ) console.log( msg );
		else alert( msg );
	}
}

_.hideStatus = function() {
	$('#status').html('').removeClass( status ).slideUp();
	$('.help').slideDown();
}