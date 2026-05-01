;(function($) {
	$('.fl-node-<?php echo $id; ?> .pp-hover-card').each( function() {
		$( this ).on( 'focus', function() {
			$( this ).addClass( 'focus' );
		} ).on( 'blur', function(e) {
			if ( $( e.relatedTarget ).closest( this ).length > 0 ) {
				return;
			}
			$( this ).removeClass( 'focus' );
		} );
	});

	$( document ).on( 'click focus keyup', function(e) {
		if ( $(e.target).closest( '.fl-node-<?php echo $id; ?> .pp-hover-card' ).length > 0 ) {
        	return;
		}
		$('.fl-node-<?php echo $id; ?> .pp-hover-card').removeClass('focus');
	} );
})(jQuery);