(function($) {

	PPFAQModule = function( settings )
	{
		this.settings  = settings;
		this.nodeClass = '.fl-node-' + settings.id;
		this._init();
	};

	PPFAQModule.prototype = {

		settings  : {},
		nodeClass : '',
		clicked   : false,

		_init: function()
		{
			//$( this.nodeClass + ' .pp-faq-button' ).css( 'height', $( this.nodeClass + ' .pp-faq-button' ).outerHeight() + 'px' );
			var button = $( this.nodeClass + ' .pp-faq-button' );
			button.off( 'click' ).on( 'click', this._buttonClick.bind( this ) );
			button.on( 'keypress', this._buttonClick.bind( this ) );
			button.on( 'focus', this._focusIn.bind( this ) );
			button.on( 'focusout', this._focusOut.bind( this ) );

			this._openDefaultItem();

			this._hashChange();

			$( window ).on( 'hashchange', this._hashChange.bind( this ) );
		},

		_hashChange: function()
		{
			var hash = location.hash.split('/')[0].replace('!', '');
			if ( hash && $(hash).length > 0 ) {
				var element = $( hash + '.pp-faq-item' );
				if ( element && element.length > 0 ) {
					location.href = '#';
					$( 'html, body' ).animate({
						scrollTop: element.offset().top - 120
					}, 0, function() {
						if ( ! element.hasClass( 'pp-faq-item-active' ) ) {
							element.find( '.pp-faq-button' ).trigger( 'click' );
						}
					});
				}
			}
		},

		_buttonClick: function( e )
		{
			// Click or keyboard (enter or spacebar) input?
			if ( ! this._validClick(e) ) {
				return;
			}

			// Prevent scrolling when the spacebar is pressed
			e.preventDefault();
			e.stopPropagation();

			var button     = $( e.target ).closest( '.pp-faq-button' ),
				faq        = button.closest( '.pp-faq' ),
				item	   = button.closest( '.pp-faq-item' ),
				allContent = faq.find( '.pp-faq-content' ),
				content    = button.siblings( '.pp-faq-content' ),
				self       = this;

			// if ( 1 == this.settings.defaultItem ) {
			// 	allContent = faq.find('.pp-faq-item:not(:first-child) .pp-faq-content');
			// }

			if ( faq.hasClass( 'pp-faq-collapse' ) && allContent.length ) {
				faq.find( '.pp-faq-item-active .pp-faq-button' ).attr('aria-expanded', 'false');
				faq.find( '.pp-faq-item-active .pp-faq-content' ).attr('aria-hidden', 'true');
				faq.find( '.pp-faq-item-active' ).removeClass( 'pp-faq-item-active' );
				button.attr('aria-expanded', 'false');
				allContent.slideUp( 'normal' );
			}

			// if ( this.settings.responsiveCollapse && window.innerWidth <= 768 && ! this.clicked ) {
			// 	this.clicked = true;
			// 	return;
			// }

			if ( content.is( ':hidden' ) ) {
				button.attr('aria-expanded', 'true');
				item.addClass( 'pp-faq-item-active' );
				content.slideDown( 'normal', function() {
					self._slideDownComplete( this );
					$(this).attr( 'aria-hidden', 'false' );
				});
			}
			else {
				button.attr('aria-expanded', 'false');
				item.removeClass( 'pp-faq-item-active' );
				content.slideUp( 'normal', function() {
					self._slideUpComplete( this );
					$(this).attr( 'aria-hidden', 'true' );
				});
			}
		},

		_slideUpComplete: function(target)
		{
			var content = $( target ),
				faq     = content.closest( '.pp-faq' );

			faq.trigger( 'fl-builder.pp-faq-toggle-complete' );
		},

		_slideDownComplete: function(target)
		{
			var content = $( target ),
				faq     = content.closest( '.pp-faq' ),
				item    = content.parent(),
				win     = $( window );

			// Gallery module support.
			FLBuilderLayout.refreshGalleries( content );

			// Content Grid module support.
			if ( 'undefined' !== typeof $.fn.isotope ) {
				content.find( '.pp-content-post-grid.pp-masonry-active' ).isotope( 'layout' );

				var highestBox    = 0;
				var contentHeight = 0;

	            content.find( '.pp-equal-height .pp-content-post' ).css( 'height', '' ).each(function(){
	                if ( $(this).height() > highestBox ) {
	                	highestBox    = $( this ).height();
	                	contentHeight = $( this ).find( '.pp-content-post-data' ).outerHeight();
	                }
	            });

	            $( this.nodeClass ).find( '.pp-equal-height .pp-content-post' ).height(highestBox);
			}

			if ( item.offset().top < win.scrollTop() + 100 ) {
				if ( ! this.clicked ) {
					$( 'html, body' ).animate({
						scrollTop: item.offset().top - 100
					}, 500, 'swing' );
				}
			}

			this.clicked = false;

			faq.trigger( 'fl-builder.pp-faq-toggle-complete' );
			$( document ).trigger( 'pp-faq-toggle-complete', [ faq ] );
		},

		_openDefaultItem: function()
		{
			if ( this.settings.responsiveCollapse && window.innerWidth <= 768 ) {
				return;
			}

			if ( 'all' == this.settings.defaultItem ) {
				$( this.nodeClass + ' .pp-faq-item' ).each(function() {
					$(this).addClass('pp-faq-item-active').find( '.pp-faq-content' ).show();
				});
			} else {
				var itemIndex = $.isNumeric( this.settings.defaultItem ) ? ( this.settings.defaultItem - 1 ) : null;

				if ( itemIndex !== null ) {
					this.clicked = true;
					$( this.nodeClass + ' .pp-faq-item' ).eq( itemIndex ).addClass('pp-faq-item-active').find( '.pp-faq-content' ).show();
				}
			}
		},

		_focusIn: function( e )
		{
			var button = $( e.target ).closest('.pp-faq-button');

			button.attr('aria-selected', 'true');
		},

		_focusOut: function( e )
		{
			var button = $( e.target ).closest('.pp-faq-button');

			button.attr('aria-selected', 'false');
		},

		_validClick: function(e)
		{
			return (e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined) ? true : false;
		}
	};

})(jQuery);
