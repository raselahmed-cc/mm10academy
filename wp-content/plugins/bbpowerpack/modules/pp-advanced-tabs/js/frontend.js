(function ($) {

	PPAdvancedTabs = function (settings) {
		this.settings = settings;
		this.nodeClass = '.fl-node-' + settings.id;
		this._init();
	};

	PPAdvancedTabs.prototype = {

		settings: {},
		nodeClass: '',

		_init: function () {
			$( this.nodeClass + ' .pp-tabs-labels .pp-tabs-label' ).on( 'click keyup', this._labelClick.bind( this ) );
			$( this.nodeClass + ' .pp-tabs-panels .pp-tabs-label' ).on( 'click', this._responsiveLabelClick.bind( this ) );

			$( this.nodeClass + ' .pp-tabs-labels .pp-tabs-label.pp-tab-active' ).attr( 'tabindex', '0' );

			this._responsiveCollapsed();

			this._bindEvents();
		},

		_bindEvents: function() {
			var layout = this.settings.layout,
				tabs = $( this.nodeClass + ' .pp-tabs-labels .pp-tabs-label' );

			// Enable arrow navigation between tabs in the tab list
  			var tabFocus = 0;

			$( this.nodeClass + ' .pp-tabs-labels' ).on( 'keydown', function(e) {
				var keyCode = e.keyCode || e.which;

				if ( 'vertical' === layout ) {
					if ( 38 === keyCode || 40 === keyCode ) {
						e.preventDefault();
						tabs[tabFocus].setAttribute('tabindex', -1);
						// Move down.
						if ( 40 === keyCode ) {
							tabFocus++;
							// If we're at the end, go to the start.
							if (tabFocus >= tabs.length) {
								tabFocus = 0;
							}
						} else if ( 38 === keyCode ) {
							// Move up.
							tabFocus--;
							// If we're at the start, move to the end.
							if (tabFocus < 0) {
								tabFocus = tabs.length - 1;
							}
						}
					}
				} else {
					if ( 37 === keyCode || 39 === keyCode ) {
						e.preventDefault();
						tabs[tabFocus].setAttribute('tabindex', -1);
						// Move right.
						if ( 39 === keyCode ) {
							tabFocus++;
							// If we're at the end, go to the start.
							if (tabFocus >= tabs.length) {
								tabFocus = 0;
							}
						} else if ( 37 === keyCode ) {
							// Move left.
							tabFocus--;
							// If we're at the start, move to the end.
							if (tabFocus < 0) {
								tabFocus = tabs.length - 1;
							}
						}
					}
				}
				tabs[tabFocus].setAttribute('tabindex', 0);
				tabs[tabFocus].focus();
			});
			  
			if ($( this.nodeClass + ' .pp-tabs-vertical' ).length > 0) {
				this._resize();
				$( window ).off( 'resize' + this.nodeClass );
				$( window ).on( 'resize' + this.nodeClass, this._resize.bind( this ) );
			}

			this._hashChange();

			$( window ).on( 'hashchange', this._hashChange.bind( this ) );
		},

		_hashChange: function () {
			var hash = location.hash.split('/')[0].replace('!', '');
			if (hash && $( hash ).length > 0) {
				var element = $( hash + '.pp-tabs-label' );
				if (element && element.length > 0) {
					var header = $( '.fl-theme-builder-header-sticky' );
					var offset = header.length > 0 ? header.height() + 32 : 120;
					location.href = '#';
					$( 'html, body' ).animate({
						scrollTop: element.parents( '.pp-tabs' ).offset().top - offset
						}, 50, function () {
							if ( ! element.hasClass( 'pp-tab-active' )) {
								element.trigger( 'click' );
							}
						});
				}
			}
		},

		_labelClick: function (e) {
			var label = $( e.target ).closest( '.pp-tabs-label' ),
				index = label.data( 'index' ),
				wrap = label.closest( '.pp-tabs' );
				// allIcons = wrap.find('.pp-tabs-label .fa'),
				// icon = wrap.find('.pp-tabs-label[data-index="' + index + '"] .fa');
			// Toggle the responsive icons.
			// allIcons.addClass('fa-plus');
			// icon.removeClass('fa-plus');
			var showContent = 'click' === e.type || ('keyup' === e.type && (13 === e.keyCode || 13 === e.which))
			if ( ! showContent) {
				return;
			}

			label.siblings().attr( 'aria-selected', false ).attr( 'tabindex', '-1' );
			label.attr( 'aria-selected', true ).attr( 'tabindex', '0' ).trigger( 'focus' );

			if (wrap.hasClass( 'pp-tabs-vertical' ) && this.settings.scrollAnimate) {
				var header = $( '.fl-theme-builder-header-sticky' );
				var offset = header.length > 0 ? header.height() + 32 : 120;
				$( 'html, body' ).animate({
					scrollTop: wrap.offset().top - offset
				}, 500);
			}

			// Toggle the tabs.
			wrap.find( '.pp-tabs-labels:first > .pp-tab-active' ).removeClass( 'pp-tab-active' );
			wrap.find( '.pp-tabs-panels:first > .pp-tabs-panel > .pp-tab-active' ).removeClass( 'pp-tab-active' );
			wrap.find( '.pp-tabs-panels:first > .pp-tabs-panel > .pp-tabs-label' ).removeClass( 'pp-tab-active' );

			wrap.find( '.pp-tabs-labels:first > .pp-tabs-label[data-index="' + index + '"]' ).addClass( 'pp-tab-active' );
			wrap.find( '.pp-tabs-panels:first > .pp-tabs-panel > .pp-tabs-panel-content[data-index="' + index + '"]' ).addClass( 'pp-tab-active' );
			wrap.find( '.pp-tabs-panels:first > .pp-tabs-panel > .pp-tabs-label[data-index="' + index + '"]' ).addClass( 'pp-tab-active' );

			// Gallery module support.
			FLBuilderLayout.refreshGalleries( wrap.find( '.pp-tabs-panel-content[data-index="' + index + '"]' ) );

			// Grid layout support (uses Masonry)
			FLBuilderLayout.refreshGridLayout( wrap.find('.pp-tabs-panel-content[data-index="' + index + '"]') );

			// Post Carousel support (uses BxSlider)
			FLBuilderLayout.reloadSlider( wrap.find('.pp-tabs-panel-content[data-index="' + index + '"]') );

			// WP audio shortcode support
			FLBuilderLayout.resizeAudio( wrap.find('.pp-tabs-panel-content[data-index="' + index + '"]') );

			// Slideshow module support.
			FLBuilderLayout.resizeSlideshow();

			$( document ).trigger( 'pp-tabs-switched', [wrap.find( '.pp-tabs-panel-content[data-index="' + index + '"]' )] );
		},

		_responsiveLabelClick: function (e) {
			var label = $( e.target ).closest( '.pp-tabs-label' ),
				wrap = label.closest( '.pp-tabs' ),
				index = label.data( 'index' ),
				content = label.siblings( '.pp-tabs-panel-content' ),
				activeContent = wrap.find( '.pp-tabs-panel-content.pp-tab-active' ),
				activeIndex = activeContent.data( 'index' ),
				allIcons = wrap.find( '.pp-tabs-label .fa' ),
				icon = label.find( '.fa' );

			// Should we proceed?
			if (index == activeIndex) {
				activeContent.slideUp( 'normal' );
				activeContent.removeClass( 'pp-tab-active' );
				$( this.nodeClass + ' .pp-tabs-panels .pp-tabs-label' ).removeClass( 'pp-tab-active' );
				wrap.removeClass( 'pp-tabs-animation' );
				return;
			}
			if (wrap.hasClass( 'pp-tabs-animation' )) {
				return;
			}

			// Toggle the icons.
			// allIcons.addClass('fa-plus');
			// icon.removeClass('fa-plus');
			// Run the animations.
			wrap.addClass( 'pp-tabs-animation' );
			activeContent.slideUp( 'normal' );

			content.slideDown('normal', function () {

				wrap.find( '.pp-tab-active' ).removeClass( 'pp-tab-active' );
				wrap.find( '.pp-tabs-label[data-index="' + index + '"]' ).addClass( 'pp-tab-active' );
				content.addClass( 'pp-tab-active' );
				wrap.removeClass( 'pp-tabs-animation' );

				// Gallery module support.
				FLBuilderLayout.refreshGalleries( content );

				// WP audio shortcode support
				FLBuilderLayout.resizeAudio( wrap.find('.pp-tabs-panel-content[data-index="' + index + '"]') );

				// Slideshow module support.
				FLBuilderLayout.resizeSlideshow();

				// Content Grid module support.
				if ('undefined' !== typeof $.fn.isotope) {
					content.find( '.pp-content-post-grid' ).isotope( 'layout' );
				}

				if (label.offset().top < $( window ).scrollTop() + 100 && ! wrap.hasClass('pp-tabs-no-scroll')) {
					$( 'html, body' ).animate( { scrollTop: label.offset().top - 100 }, 500, 'swing' );
				}

				$( document ).trigger( 'pp-tabs-switched', [content] );
			});
		},

		_resize: function () {
			$( this.nodeClass + ' .pp-tabs-vertical' ).each( this._resizeVertical.bind( this ) );
		},

		_resizeVertical: function (e) {
			var wrap = $( this.nodeClass + ' .pp-tabs-vertical' ),
				labels = wrap.find( '.pp-tabs-labels' ),
				panels = wrap.find( '.pp-tabs-panels' );

			panels.css( 'min-height', labels.height() + 'px' );
		},

		_responsiveCollapsed: function () {
			if ($( window ).innerWidth() < 769 && ! this.settings.isBuilderActive) {
				if (this.settings.responsiveClosed) {
					$( this.nodeClass + ' .pp-tabs-panels .pp-tabs-label.pp-tab-active' ).trigger( 'click' );
				}
				$( this.nodeClass + ' .pp-tabs-panels' ).css( 'visibility', 'visible' );
			}
		}
	};

})(jQuery);
