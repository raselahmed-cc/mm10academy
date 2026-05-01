;(function($) {
	PPTable = function( settings ) {
		this.id       = settings.id;
		this.settings = settings;

		this.init();
	};

	PPTable.prototype = {
		init: function() {
			$('.fl-node-' + this.id + ' table.pp-table-content tbody tr:nth-child(odd)').addClass('odd');
			$('.fl-node-' + this.id + ' table.pp-table-content tbody tr:nth-child(even)').addClass('even');

			$('.fl-node-' + this.id + ' table.pp-table-content').attr('data-tablesaw-mode', this.settings.mode);

			if( this.settings.mode == 'swipe' && this.settings.breakpoint > 0 && window.innerWidth >= this.settings.breakpoint ) {
				$('.fl-node-' + this.id + ' table.pp-table-content').removeAttr('data-tablesaw-mode');
			}

			setTimeout( function() {
				$( document ).trigger( 'enhance.tablesaw' );
			}, 500 );

			$( document ).on( 'pp-tabs-switched', function(e, selector) {
				if ( selector.find('.pp-table-content').length > 0 ) {
					$( window ).trigger( 'resize' );
				}
			} );
		},

		isTouch: function() {
			var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
			var mq = function(query) {
				return window.matchMedia(query).matches;
			}
			
			if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
				return true;
			}
			
			// include the 'heartz' as a way to have a non matching MQ to help terminate the join
			// https://git.io/vznFH
			var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
			return mq(query);
		}
	};
})(jQuery);