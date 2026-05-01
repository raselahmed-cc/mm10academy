;( function ($) {
	PPFileDownload = function( settings ) {
		this.id = settings.id;
		this.node = $( '.fl-node-' + this.id );

		this.init();
	};

	PPFileDownload.prototype = {
		init: function() {
			if ( this.isMultiple() ) {
				var dropdown = this.node.find( '.pp-files-dropdown select' );
				var button = this.node.find( '.pp-button' );

				if ( '' === dropdown.val() ) {
					button.addClass( 'disabled' );
				} else {
					button.attr( 'href', dropdown.val() );
					button.attr( 'download', dropdown.find( 'option:selected' ).attr( 'data-filename' ) );
				}

				$( dropdown ).on( 'change', function() {
					if ( '' === $( this ).val() ) {
						button.addClass( 'disabled' );
					} else {
						button.attr( 'href', $( this ).val() );
						button.attr( 'download', $( this ).find( 'option:selected' ).attr( 'data-filename' ) );
					}
				} );
			}
		},

		isMultiple: function() {
			return this.node.find( '.pp-files-dropdown' ).length > 0;
		},
	};
} )( jQuery );