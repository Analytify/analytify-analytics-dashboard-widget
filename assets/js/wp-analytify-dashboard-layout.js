jQuery( document ).ready( function ( $ ) {

	$( document ).on( 'click', '#analytify-dashboard-addon-warning .ui-sortable-handle', function ( e ) {
		e.preventDefault();
		$( '#analytify-dashboard-addon-warning' ).removeClass( 'closed' );
	});

	$( '#analytify-dashboard-addon-warning' ).removeClass( 'closed' );

});
