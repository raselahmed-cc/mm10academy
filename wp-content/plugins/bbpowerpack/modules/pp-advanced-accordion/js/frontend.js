(function($) {

	PPAccordion = function( settings ) {
		this.id 		= settings.id;
		this.settings 	= settings;
		this.nodeClass  = '.fl-node-' + settings.id;
		this.accordion	= $( this.nodeClass + ' > .fl-module-content > .pp-accordion' );
		this.clicked 	     = false;
		this.nestedToggle    = false;
		this.defaultOpened   = false;
		this.offsetTop       = settings.scrollOffsetTop;
		this.isBuilderActive = settings.isBuilderActive;

		this._init();
	};

	PPAccordion.prototype = {

		settings	: {},
		nodeClass   : '',
		clicked		: false,
		offsetTop   : 120,

		_init: function() {
			if ( this.accordion.hasClass( 'pp-accordion-initialized' ) ) {
				return;
			}

			var button = this.accordion.find( '> .pp-accordion-item > .pp-accordion-button' );

			//button.css('height', button.outerHeight() + 'px');
			button.off('click').on('click', this._buttonClick.bind( this ) );
			button.on('keypress', this._buttonClick.bind( this ) );
			button.on('mouseup', this._mouseEvent.bind( this ) );
			button.on('focus', this._focusIn.bind( this ) );
			button.on('focusout', this._focusOut.bind( this ) );

			this._responsiveCollapse();

			this._hashChange();

			$(window).on('hashchange', this._hashChange.bind( this ));
			this.accordion.addClass('pp-accordion-initialized');
		},

		_hashChange: function() {
			var scrollPos = $(window).scrollTop();
			$(window).on('scroll', function() {
				scrollPos = $(window).scrollTop();
			});
			var hash = location.hash.split('/')[0].replace('!', '');
			if( hash && $(hash).length > 0 ) {
				var self = this;
				var element = $(hash + '.pp-accordion-item');
				if ( element && element.length > 0 ) {
					$('html, body').animate({
						scrollTop: element.offset().top - self.offsetTop
					}, 0, function() {
						location.href = '#';
						// Fix scroll after hash change.
						window.scrollTo(0, scrollPos);
						// Open accordion item.
						setTimeout(function() {
							if ( ! element.hasClass('pp-accordion-item-active') ) {
								element.find('> .pp-accordion-button').trigger('click');
							}
						}, 100);
						// Nested accordion logic.
						var parentModules = element.parents('.fl-module');
						var elementNodeId = element.closest('.fl-module').data('node');
						parentModules.each(function() {
							if ( $(this).data('node') !== elementNodeId ) {
								var parentNodeId = $(this).data('node');
								if ( 'undefined' !== typeof window['pp_accordion_' + parentNodeId] ) {
									var parentItem = $(this).find('.fl-node-' + elementNodeId).parents('.pp-accordion-item');
									if ( ! parentItem.hasClass('pp-accordion-item-active') ) {
										parentItem.find('> .pp-accordion-button').trigger('click');
										self.nestedToggle = true;
										setTimeout(function() {
											window.scrollTo(0, element.offset().top - self.offsetTop);
										}, 800);
									}
								}
							}
						});
					});
				}
			}
		},

		_mouseEvent: function() {
			this.clicked = true;
		},

		_buttonClick: function( e ) {
			e.preventDefault();
			e.stopPropagation();

			var button      = $( e.target ).closest('.pp-accordion-button'),
				accordion   = button.closest('.pp-accordion'),
				item	    = button.closest('.pp-accordion-item'),
				allContent  = accordion.find('> .pp-accordion-item > .pp-accordion-content'),
				content     = button.siblings('.pp-accordion-content'),
				self		= this;
			
			// Click or keyboard (enter or spacebar) input?
			if ( ! this._validClick(e) ) {
				return;
			}

			// Prevent scrolling when the spacebar is pressed
			e.preventDefault();

			if ( accordion.hasClass('pp-accordion-collapse') ) {
				accordion.find( '> .pp-accordion-item-active .pp-accordion-button' ).attr('aria-expanded', 'false');
				accordion.find( '> .pp-accordion-item-active .pp-accordion-content' ).attr('aria-hidden', 'true');
				accordion.find( '> .pp-accordion-item-active' ).removeClass( 'pp-accordion-item-active' );
				button.attr('aria-expanded', 'false');
				allContent.slideUp('normal');
			}

			if ( content.is(':hidden') ) {
				button.attr('aria-expanded', 'true');
				item.addClass( 'pp-accordion-item-active' );
				if ( this.defaultOpened ) {
					var speed = 0;
				} else {
					var speed = 'normal';
				}
				content.slideDown(speed, function() {
					self._slideDownComplete(this);
					$(this).attr( 'aria-hidden', false );
				});
			}
			else {
				button.attr('aria-expanded', 'false');
				item.removeClass( 'pp-accordion-item-active' );
				content.slideUp('normal', function() {
					self._slideUpComplete(this);
					$(this).attr( 'aria-hidden', true );
				});
			}
		},

		_focusIn: function( e ) {
			var button = $( e.target ).closest('.pp-accordion-button');

			button.attr('aria-selected', 'true');
		},

		_focusOut: function( e ) {
			var button = $( e.target ).closest('.pp-accordion-button');

			button.attr('aria-selected', 'false');
		},

		_slideUpComplete: function(target) {
			var content 	= $( target ),
				accordion 	= content.closest( '.pp-accordion' );

			accordion.trigger( 'fl-builder.pp-accordion-toggle-complete' );
		},

		_slideDownComplete: function(target) {
			var content 	= $( target ),
				accordion 	= content.closest( '.pp-accordion' ),
				item 		= content.parent(),
				win  		= $( window );

			// Gallery module support.
			FLBuilderLayout.refreshGalleries( content );

			// Grid layout support (uses Masonry)
			FLBuilderLayout.refreshGridLayout( content );

			// Post Carousel support (uses BxSlider)
			FLBuilderLayout.reloadSlider( content );

			// WP audio shortcode support
			FLBuilderLayout.resizeAudio( content );

			// Prevent row slideshow from getting stopped
			// when an item is set to expand by default.
			if ( ! this.defaultOpened ) {
				// Slideshow module support.
				FLBuilderLayout.resizeSlideshow();
			} else {
				this.defaultOpened = false;
			}

			// Content Grid module support.
			if ( 'undefined' !== typeof $.fn.isotope ) {
				var highestBox = 0;
				var contentHeight = 0;

	            content.find('.pp-equal-height .pp-content-post').css('height', '').each(function(){
	                if($(this).height() > highestBox) {
	                	highestBox = $(this).height();
	                	contentHeight = $(this).find('.pp-content-post-data').outerHeight();
	                }
	            });

				content.find('.pp-equal-height .pp-content-post').height(highestBox);
				content.find('.pp-content-post-grid').isotope('layout');
			}

			if ( ! this.nestedToggle ) {
				if ( item.offset().top < win.scrollTop() + 100 ) {
					if ( ! this.isBuilderActive && ( ! this.clicked || this.settings.scrollAnimation ) ) {
						$( 'html, body' ).animate({
							scrollTop: item.offset().top - this.offsetTop
						}, 500, 'swing');
					}
				}
			}

			this.clicked = false;
			this.nestedToggle = false;

			accordion.trigger( 'fl-builder.pp-accordion-toggle-complete' );
			$(document).trigger( 'pp-accordion-toggle-complete', [ accordion, item ] );
		},

		_responsiveCollapse: function() {
			if ( this.settings.responsiveCollapse && window.innerWidth <= 768 ) {
				this.accordion.find( '> .pp-accordion-item' ).removeClass('pp-accordion-item-active').find('> .pp-accordion-content').hide();
				return;
			}
		},

		_validClick: function(e) {
			return (e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined) ? true : false;
		}
	};

})(jQuery);
