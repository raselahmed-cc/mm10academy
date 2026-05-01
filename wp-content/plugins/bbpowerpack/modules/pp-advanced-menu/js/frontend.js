(function($) {

	PPAdvancedMenu = function( settings ) {

		// set params
		this.settingsId 		 = settings.id;
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .pp-advanced-menu';
		this.type				 = settings.type;
		this.mobileToggle		 = settings.mobile;
		this.mobileBelowRow		 = 'below' === settings.menuPosition;
		this.breakpoints         = settings.breakpoints;
		this.mobileBreakpoint	 = settings.mobileBreakpoint;
		this.mediaBreakpoint	 = settings.mediaBreakpoint;
		this.mobileMenuType	 	 = settings.mobileMenuType;
		this.offCanvasDirection	 = settings.offCanvasDirection;
		this.postId 			 = bb_powerpack.post_id;
		this.isBuilderActive	 = settings.isBuilderActive;
		this.currentBrowserWidth = window.innerWidth;
		this.fullScreenMenu 	= null;
		this.offCanvasMenu 		= null;
		this.$submenus 			= null;

		// initialize the menu
		$( this._initMenu.bind( this ) );

		// check if viewport is resizing
		$( window ).on( 'resize', function( e ) {

			var width = window.innerWidth;

			// if screen width is resized, reload the menu
		    if( width != this.currentBrowserWidth ) {

				this._initMenu();
 				this._clickOrHover();
		    	this.currentBrowserWidth = width;
			}

		}.bind( this ) );

		$( 'body' ).on( 'click', function( e ) {
			if ( 'undefined' !== typeof FLBuilderConfig ){
				return;
			}

			if ( $( this.wrapperClass ).find( '.pp-advanced-menu-mobile-toggle' ).hasClass( 'pp-active' ) && ( 'expanded' !== this.mobileToggle ) ) {
				if ( $( e.target ).parents('.fl-module-pp-advanced-menu').length > 0 ) {
					return;
				}
				if ( $( e.target ).is( 'input' ) && $( e.target ).parents('.pp-advanced-menu').length > 0 ) {
					return;
				}
				if ( $( e.target ).hasClass( 'pp-menu-close-btn' ) ) {
					return;
				}
				$( this.wrapperClass ).find( '.pp-advanced-menu-mobile-toggle' ).trigger( 'click' );
			}

			$( this.wrapperClass ).find( '.pp-has-submenu' ).removeClass( 'focus pp-active' );
			$( this.wrapperClass ).find( '.pp-has-submenu .sub-menu' ).removeClass( 'focus' );
			$( this.wrapperClass ).find( '.pp-has-submenu-container' ).removeClass( 'focus' );
			$( this.wrapperClass ).find( '.pp-menu-toggle' ).attr( 'aria-expanded', false );

		}.bind( this ) );

		// Esc key to close the submenu.
		$( document ).on( 'keyup', function( e ) {
			if ( 27 == e.which ) {
				$( this.wrapperClass ).find( '.pp-has-submenu' ).removeClass( 'focus pp-active' );
				$( this.wrapperClass ).find( '.pp-has-submenu .sub-menu' ).removeClass( 'focus' );
				$( this.wrapperClass ).find( '.pp-has-submenu-container' ).removeClass( 'focus' );
				$( this.wrapperClass ).find( '.pp-menu-toggle' ).attr( 'aria-expanded', false );
				if ( $( this.wrapperClass ).find( '.pp-menu-toggle.focus' ).is( ':visible' ) ) {
					$( this.wrapperClass ).find( '.pp-menu-toggle.focus' ).delay(100).trigger( 'focus' );
				}
			}
		}.bind( this ) );

	};

	PPAdvancedMenu.prototype = {
		nodeClass               : '',
		wrapperClass            : '',
		type 	                : '',
		breakpoints 			: {},
		$submenus				: null,
		fullScreenMenu			: null,
		offCanvasMenu			: null,

		/**
		 * Check if the screen size fits a mobile viewport.
		 *
		 * @since  1.6.1
		 * @return bool
		 */
		_isMobile: function() {
			return window.innerWidth <= this.breakpoints.small ? true : false;
		},

		/**
		 * Check if the screen size fits a medium viewport.
		 *
		 * @since  1.10.5
		 * @return bool
		 */
		_isMedium: function() {
			return window.innerWidth <= this.breakpoints.medium ? true : false;
		},

		/**
		 * Check if the screen size fits a large viewport.
		 *
		 * @since  1.10.5
		 * @return bool
		 */
		_isLarge: function() {
			return window.innerWidth <= this.breakpoints.large ? true : false;
		},

		/**
		 * Check if the screen size fits a custom viewport.
		 *
		 * @since  1.10.5
		 * @return bool
		 */
		_isCustom: function() {
			return window.innerWidth <= this.breakpoints.custom ? true : false;
		},

		_isTouch: function() {
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
		},

		/**
		 * Check if the menu should toggle for the current viewport base on the selected breakpoint
		 *
		 * @see 	this._isMobile()
		 * @see 	this._isMedium()
		 * @since  	1.10.5
		 * @return bool
		 */
		_isMenuToggle: function() {
			if ( ( 'always' == this.mobileBreakpoint
				|| ( this._isMobile() && 'mobile' == this.mobileBreakpoint )
				|| ( this._isMedium() && 'medium-mobile' == this.mobileBreakpoint )
				|| ( this._isCustom() && 'custom' == this.mobileBreakpoint )
				) && ( $( this.nodeClass ).find( '.pp-advanced-menu-mobile-toggle' ).is(':visible') || 'expanded' == this.mobileToggle ) ) {
				return true;
			}

			return false;
		},

		_bindSettingsFormEvents: function()
		{
			// $('body').on( 'change', '.fl-builder-settings select[name="offcanvas_direction"]', function() {
			// 	$('html').removeClass('pp-off-canvas-menu-open');
			// } );
		},

		/**
		 * Initialize the toggle logic for the menu.
		 *
		 * @see    this._isMobile()
		 * @see    this._menuOnCLick()
		 * @see    this._clickOrHover()
		 * @see    this._submenuOnRight()
		 * @see    this._submenuRowZindexFix()
		 * @see    this._toggleForMobile()
		 * @since  1.6.1
		 * @return void
		 */
		_initMenu: function() {
			if ( this.mobileToggle != 'expanded' ) {
				this._initOffCanvas();
				this._initFullScreen();
			}

			this._setupSubmenu();
			this._menuOnHover();
			this._menuOnFocus();
			this._submenuOnClick();

			if ( $( this.nodeClass ).length && this.type == 'horizontal' ) {
				this._initMegaMenus();
			}

			if ( this.type == 'horizontal' || this.type == 'vertical' ) {
				var self = this;
				$( this.wrapperClass ).find('.pp-has-submenu-container').on( 'click', function( e ) {
					if ( self.mobileMenuType !== 'off-canvas' && self.mobileMenuType !== 'full-screen' ) {
						if ( self._isTouch() ) {
							if ( ! $(this).hasClass('first-click') ) {
								e.preventDefault();
								$(this).addClass('first-click');
							}
						}
					}
				} );

				// Keyboard nav support for submenu toggle.
				$( this.wrapperClass ).find( 'li.pp-has-submenu a' ).on( 'keypress', function(e) {
					if ( $(e.target).hasClass( 'pp-menu-toggle' ) && ! $( this.nodeClass ).find( '.pp-advanced-menu-mobile-toggle' ).is( ':visible' ) ) {
						if (e.which == 1 || e.which == 13 || e.which == undefined) {
							e.stopPropagation();
							e.preventDefault();
							$(e.target).parents('li.pp-has-submenu').toggleClass( 'pp-active' );

							if ( $(e.target).parents('li.pp-has-submenu').hasClass( 'pp-active' ) ) {
								$(e.target).attr( 'aria-expanded', true );
							} else {
								$(e.target).attr( 'aria-expanded', false );
							}
						}
					}
				}.bind( this ) );
			}

			if ( this._isMenuToggle() || this.type == 'accordion' ) {
				$( this.wrapperClass ).off( 'mouseenter mouseleave' );
				this._menuOnClick();
				this._clickOrHover();
			} else {
				$( this.wrapperClass ).off( 'click' );
				this._submenuOnRight();
				this._submenuRowZindexFix();
			}

			if ( this.mobileToggle != 'expanded' ) {
				this._toggleForMobile();
			}

			$(this.wrapperClass).find('li:not(.menu-item-has-children)').on('click', 'a', function (e) {
				if ( $(e.target).closest( '.pp-menu-search-item' ).length > 0 ) {
					return;
				}

				$(this.nodeClass).find('.pp-advanced-menu').removeClass('menu-open');
				$(this.nodeClass).find('.pp-advanced-menu').addClass('menu-close');
				$('html').removeClass('pp-off-canvas-menu-open');
				$('html').removeClass('pp-full-screen-menu-open');

			}.bind( this ) );

			if ( $( this.wrapperClass ).find( '.pp-menu-search-item' ).length ) {
				this._toggleMenuSearch();
			}

			if ( $( this.wrapperClass ).find( '.pp-menu-cart-item').length ) {
				this._wooUpdateParams();
			}
		},

		/**
		 * Initializes submenu dropdowns.
		 *
		 * @since 2.33.x
		 * @return void
		 */
		_setupSubmenu: function() {
			$( this.wrapperClass + ' ul.sub-menu' ).each( function(){
				$( this ).closest( 'li' ).attr( 'aria-haspopup', 'true' );
			});
		},

		_menuOnHover: function() {
			$( this.wrapperClass ).on( 'mouseenter', 'li.menu-item', function() {
				$(this).addClass( 'hover' );
			} ).on( 'mouseleave', 'li.menu-item', function() {
				$(this).removeClass( 'hover' );
			} );
		},

		/**
		 * Adds a focus class to menu elements similar to be used similar to CSS :hover psuedo event
		 *
		 * @since  1.9.0
		 * @return void
		 */
		_menuOnFocus: function() {
			$( this.nodeClass ).off('focus').on( 'focus', 'a', function( e ) {
				var $menuItem	= $( e.target ).parents( '.menu-item' ).first(),
					$parents	= $( e.target ).parentsUntil( this.wrapperClass );

				$('.pp-advanced-menu .focus:not(.pp-menu-toggle)').removeClass('focus');

				$menuItem.addClass('focus');
				$parents.addClass('focus');

			}.bind( this ) ).on( 'focusout', 'a', function( e ) {
				if ( $('.pp-advanced-menu .focus').hasClass('pp-has-submenu') ) {
					$( e.target ).parentsUntil( this.wrapperClass ).find('.pp-has-submenu-container').removeClass('first-click');
				}
			}.bind( this ) );

			$( this.nodeClass ).off('focus', '.pp-menu-toggle').on( 'focus', '.pp-menu-toggle', function( e ) {
				$(e.target).addClass( 'focus' );
			}.bind( this ) ).off('blur', '.pp-menu-toggle').on( 'blur', '.pp-menu-toggle', function( e ) {
				if ( $(e.target).parents( 'li.pp-has-submenu.pp-active' ).length === 0 || $(e.target).parents( 'li.pp-has-submenu.pp-active' ).parent( 'ul.menu' ).length === 0 ) {
					$(e.target).removeClass( 'focus' );
				}
			}.bind( this ) );
		},

		/**
		 * Logic for submenu toggling on accordions or mobile menus (vertical, horizontal)
		 *
		 * @since  1.6.1
		 * @return void
		 */
		_menuOnClick: function() {
			var self = this;
			var $mainItem = '';

			$( this.wrapperClass ).off().on( 'click.pp-advanced-menu keyup', '.pp-has-submenu-container', function( e ) {
				var isValidEvent = e.which == 1 || e.which == 13 || e.which == undefined; // click or enter key press.
				if ( ! isValidEvent ) {
					return;
				}
				if ( e.which == 13 && ! $(e.target).hasClass( 'pp-menu-toggle' ) ) { // prevent event bubbling.
					return;
				}
				if ( self._isTouch() ) {
					if ( ! $(this).hasClass('first-click') ) {
						e.preventDefault();
						$(this).addClass('first-click');
					}
				}

				e.stopPropagation();

				var isMainEl = $(e.target).parents('.menu-item').parent().parent().hasClass('pp-advanced-menu');

				if (isMainEl && $mainItem === '') {
					$mainItem = $(e.target).parents('.menu-item');
				}

				var $link			= $( e.target ).parents( '.pp-has-submenu' ).first(),
					$subMenu 		= $link.children( '.sub-menu' ).first(),
					$href	 		= $link.children('.pp-has-submenu-container').first().find('> a').attr('href'),
					$subMenuParents = $( e.target ).parents( '.sub-menu' ),
					$activeParent 	= $( e.target ).closest( '.pp-has-submenu.pp-active' );

					if ( $activeParent.length > 0 ) {
						$activeParent.find( '.pp-menu-toggle' ).first().attr('aria-expanded', true);
					} else {
						$activeParent.find( '.pp-menu-toggle' ).first().attr('aria-expanded', false);
					}

				if( !$subMenu.is(':visible') || $(e.target).hasClass('pp-menu-toggle')
					|| ($subMenu.is(':visible') && (typeof $href === 'undefined' || $href == '#')) ) {
					if ( ! $(this.wrapperClass).hasClass('pp-advanced-menu-accordion-collapse') ) {
						e.preventDefault();
					}
					if ( $(e.target).hasClass('pp-menu-toggle') ) {
						e.stopPropagation();
						e.preventDefault();
					}
				}
				else {
					e.stopPropagation();
					window.location.href = $href;
					return;
				}

				if ($(this.wrapperClass).hasClass('pp-advanced-menu-accordion-collapse')) {

					if ( $link.parents('.menu-item').length && !$link.parents('.menu-item').hasClass('pp-active') ) {
						$('.menu .pp-active', this.wrapperClass).not($link).removeClass('pp-active');
					}
					else if ($link.parents('.menu-item').hasClass('pp-active') && $link.parent('.sub-menu').length) {
						$('.menu .pp-active', this.wrapperClass).not($link).not($activeParent).removeClass('pp-active');
					}

					$('.sub-menu', this.wrapperClass).not($subMenu).not($subMenuParents).slideUp('normal');
				}

				// Parent menu toggle icon was being reversed on collapsing its submenu,
				// so here is the workaround to fix this behavior.
				if ($(self.wrapperClass).find('.sub-menu:visible').length > 0) {
					$(self.wrapperClass).find('.sub-menu:visible').parent().addClass('pp-active');
				}
				$subMenu.slideToggle(400, function() {
					// Reset previously opened sub-menu toggle icon.
					$(e.target).parents('.pp-has-submenu-container').parent().parent().find('> .menu-item.pp-active').removeClass('pp-active');
					
					if ($mainItem !== '') {
						$mainItem.parent().find('.menu-item.pp-active').removeClass('pp-active');
						$(self.wrapperClass).find('.sub-menu').parent().removeClass('pp-active');

						if ($(self.wrapperClass).find('.sub-menu:visible').length > 0) {
							$(self.wrapperClass).find('.sub-menu:visible').parent().addClass('pp-active');
						} else {
							$link.toggleClass('pp-active');
							$mainItem.removeClass('pp-active');
						}
					} else {
						$link.toggleClass('pp-active');
					}
					if (!$subMenu.is(':visible')) {
						$subMenu.parent().removeClass('pp-active');
						$subMenu.parent().find('> .pp-has-submenu-container .pp-menu-toggle').attr('aria-expanded', false);
					} else {
						$subMenu.parent().find('> .pp-has-submenu-container .pp-menu-toggle').attr('aria-expanded', true);
					}
				});

			}.bind( this ) );

		},

		/**
		 * Logic for submenu items click event
		 *
		 * @since  1.3.1
		 * @return void
		 */
		_submenuOnClick: function(){
			$( 'body' ).off( 'click', this.wrapperClass + ' .sub-menu a' ).on( 'click', this.wrapperClass + ' .sub-menu a', function( e ){
				if ( $( e.target ).parent().hasClass('focus') ) {
					$( e.target ).parentsUntil( this.wrapperClass ).removeClass( 'focus' );
				}
			}.bind( this ) );
		},

		/**
		 * Changes general styling and behavior of menus based on mobile / desktop viewport.
		 *
		 * @see    this._isMobile()
		 * @since  1.6.1
		 * @return void
		 */
		_clickOrHover: function() {
			this.$submenus = this.$submenus || $( this.wrapperClass ).find( '.sub-menu' );
			var $wrapper   = $( this.wrapperClass ),
				$menu      = $wrapper.find( '.menu' );
				$li        = $wrapper.find( '.pp-has-submenu' );

			if( this._isMenuToggle() ) {
				$li.each( function() {
					if( !$(this).hasClass('pp-active') ) {
						$(this).find( '.sub-menu' ).fadeOut();
					}
				} );
			} else {
				$li.each( function() {
					if( !$(this).hasClass('pp-active') ) {
						$(this).find( '.sub-menu' ).css( {
							'display' : '',
							'opacity' : ''
						} );
					}
				} );
			}
		},

		/**
		 * Logic to prevent submenus to go outside viewport boundaries.
		 *
		 * @since  1.6.1
		 * @return void
		 */
		_submenuOnRight: function() {

			$( this.wrapperClass )
				.on( 'mouseenter focus', '.pp-has-submenu', function( e ) {

					if( $ ( e.currentTarget ).find('.sub-menu').length === 0 ) {
						return;
					}

					var $link           = $( e.currentTarget ),
						$parent         = $link.parent(),
						$subMenu        = $link.find( '.sub-menu' ),
						subMenuWidth    = $subMenu.width(),
						subMenuPos      = 0,
						winWidth        = window.innerWidth;

					if( $link.closest( '.pp-menu-submenu-right' ).length !== 0) {

						$link.addClass( 'pp-menu-submenu-right' );

					} else if( $( 'body' ).hasClass( 'rtl' ) ) {

						subMenuPos = $parent.is( '.sub-menu' ) ?
									 $parent.offset().left - subMenuWidth:
									 $link.offset().left - subMenuWidth;

						if( subMenuPos <= 0 ) {
							$link.addClass( 'pp-menu-submenu-right' );
						}

					} else {

						subMenuPos = $parent.is( '.sub-menu' ) ?
									 $parent.offset().left + $parent.width() + subMenuWidth :
									 $link.offset().left + subMenuWidth;

						if( subMenuPos > winWidth ) {
							$link.addClass('pp-menu-submenu-right');
						}
					}
				}.bind( this ) )
				.on( 'mouseleave', '.pp-has-submenu', function( e ) {
					$( e.currentTarget ).removeClass( 'pp-menu-submenu-right' );
				}.bind( this ) );

		},

		/**
		 * Logic to prevent submenus to go behind the next overlay row.
		 *
		 * @since  2.2
		 * @return void
		 */
		_submenuRowZindexFix: function (e) {

			$(this.wrapperClass)
				.on('mouseenter', 'ul.menu > .pp-has-submenu', function (e) {

					if ($(e.currentTarget).find('.sub-menu').length === 0) {
						return;
					}

					$(this.nodeClass)
						.closest('.fl-row')
						.find('.fl-row-content')
						.css('z-index', '10');

				}.bind( this ) )
				.on('mouseleave', 'ul.menu > .pp-has-submenu', function (e) {

					$(this.nodeClass)
						.closest('.fl-row')
						.find('.fl-row-content')
						.css('z-index', '');

				}.bind( this ) );
		},

		/**
		 * Logic for the mobile menu button.
		 *
		 * @since  1.6.1
		 * @return void
		 */
		_toggleForMobile: function() {

			var $wrapper = null,
				$menu    = null,
				self 	 = this;

			if ( this._isMenuToggle() ) {
				$wrapper = $(this.wrapperClass);
				if ( this._isMobileBelowRowEnabled() ) {
					this._placeMobileMenuBelowRow();
					$menu = $(this.nodeClass + '-clone');
					$menu.find('ul.menu').show();
				} else {
					$menu = $wrapper.find('.menu');
				}

				if( !$wrapper.find( '.pp-advanced-menu-mobile-toggle' ).hasClass( 'pp-active' ) && this.mobileMenuType === 'default' ) {
					$menu.css({ display: 'none' });
				}

				$wrapper.on( 'click', '.pp-advanced-menu-mobile-toggle', function( e ) {
					$(this).toggleClass( 'pp-active' );
					if ( $(this).hasClass('pp-active') ) {
						$(this).attr('aria-expanded', 'true');
						$menu.addClass( 'pp-menu-visible' );
					} else {
						$(this).attr('aria-expanded', 'false');
						$menu.removeClass( 'pp-menu-visible' );
					}
					$menu.slideToggle();
					e.stopPropagation();
				} );

				$menu.on( 'click', '.menu-item > a[href*="#"]', function(e) {
					var $href = $(this).attr('href'),
						$targetID = '';

					if ( $href !== '#' ) {
						$targetID = $href.split('#')[1];

						if ( $('body').find('#'+  $targetID).length > 0 ) {
							e.preventDefault();
							$( this ).toggleClass( 'pp-active' );
							setTimeout(function() {
								$('html, body').animate({
									scrollTop: $('#'+ $targetID).offset().top
								}, 1000, function() {
									window.location.hash = $targetID;
								});
							}, 500);

							if ( ! self._isMenuToggle() ) {
								$menu.slideToggle();
							}
						}
					}
				});
			}
			else {

				if ( this._isMobileBelowRowEnabled() ) {
					this._removeMenuFromBelowRow();
				}

				$wrapper = $( this.wrapperClass ),
				$menu    = $wrapper.children( '.menu' );
				$wrapper.find( '.pp-advanced-menu-mobile-toggle' ).removeClass( 'pp-active' );
				$menu.css({ display: '' });
			}
		},

		/**
		 * Init any mega menus that exist.
		 *
		 * @see 	this._isMenuToggle()
		 * @since  	1.10.4
		 * @return void
		 */
		_initMegaMenus: function() {

			var module     = $( this.nodeClass ),
				rowContent = module.closest( '.fl-row-content' ),
				rowWidth   = rowContent.width(),
				megas      = module.find( '.mega-menu' ),
				disabled   = module.find( '.mega-menu-disabled' ),
				isToggle   = this._isMenuToggle();

			if ( isToggle ) {
				megas.removeClass( 'mega-menu' ).addClass( 'mega-menu-disabled' );
				module.find( 'li.mega-menu-disabled > ul.sub-menu' ).css( 'width', '' );
				rowContent.css( 'position', '' );
			} else {
				disabled.removeClass( 'mega-menu-disabled' ).addClass( 'mega-menu' );
				module.find( 'li.mega-menu > ul.sub-menu' ).css( 'width', rowWidth + 'px' );
				rowContent.css( 'position', 'relative' );
			}
		},

		/**
		 * Init off-canvas menu.
		 *
		 * @since  	1.2.8
		 * @return void
		 */
		_initOffCanvas: function() {
			if ( this.mobileMenuType !== 'off-canvas' ) {
				return;
			}
			$('html').addClass('pp-off-canvas-menu-module');
			$('html').addClass('pp-off-canvas-menu-' + this.offCanvasDirection);

			if ( $(this.nodeClass).find( '#pp-menu-' + this.settingsId ).length === 0 ) {
				return;
			}

			var menuHTML = $(this.nodeClass).find( '#pp-menu-' + this.settingsId ).html();

			if ( null === this.offCanvasMenu ) {
				this.offCanvasMenu = $('<div id="pp-advanced-menu-off-canvas-'+this.settingsId+'" class="fl-node-'+this.settingsId+' pp-menu-off-canvas" />').html( menuHTML );
			}
			if ( $('#pp-advanced-menu-off-canvas-'+this.settingsId).length === 0 && null !== this.offCanvasMenu && ! this.isBuilderActive ) {
				this.offCanvasMenu.appendTo( 'body' );
			}

			$(this.nodeClass).trigger('pp-advanced-menu-off-canvas-init', [this.offCanvasMenu]);

			this._toggleMenu();
		},

		/**
		 * Init full-screen overlay menu.
		 *
		 * @since  	1.2.8
		 * @return void
		 */
		_initFullScreen: function() {
			if ( this.mobileMenuType !== 'full-screen' ) {
				return;
			}

			$('html').addClass('pp-full-screen-menu-module');

			if ( $(this.nodeClass).find( '#pp-menu-' + this.settingsId ).length === 0 ) {
				return;
			}

			var menuHTML = $(this.nodeClass).find( '#pp-menu-' + this.settingsId ).html();

			if ( null === this.fullScreenMenu ) {
				this.fullScreenMenu = $('<div id="pp-advanced-menu-full-screen-'+this.settingsId+'" class="fl-node-'+this.settingsId+' pp-menu-full-screen" />').html( menuHTML );
			}
			if ( $('#pp-advanced-menu-full-screen-'+this.settingsId).length === 0 && null !== this.fullScreenMenu && ! this.isBuilderActive ) {
				this.fullScreenMenu.appendTo( 'body' );
			}

			$(this.nodeClass).trigger('pp-advanced-menu-full-screen-init', [this.fullScreenMenu]);

			this._toggleMenu();
		},

		/**
		 * Trigger the toggle event for off-canvas
		 * and full-screen overlay menus.
		 *
		 * @since  	1.2.8
		 * @return void
		 */
		_toggleMenu: function() {
			var self = this;
			var singleInstance = true;
			if( self.mobileMenuType === 'full-screen' ) {
				var winHeight = window.innerHeight;
				$(self.nodeClass).find('.pp-menu-overlay').css('height', winHeight + 'px');
				$(window).on('resize', function() {
					winHeight = window.innerHeight;
					$(self.nodeClass).find('.pp-menu-overlay').css('height', winHeight + 'px');
				});
			}
			// Toggle Click
			$(self.nodeClass).find('.pp-advanced-menu-mobile-toggle' ).off('click').on( 'click', function() {
				if ( singleInstance ) {
					if ( $('.pp-advanced-menu.menu-open').length > 0 ) {
						$('.pp-advanced-menu').removeClass('menu-open');
						$('html').removeClass('pp-full-screen-menu-open');
					}
				}
				if( $(self.nodeClass).find('.pp-advanced-menu').hasClass('menu-open') ) {
					$(self.nodeClass).find('.pp-advanced-menu').removeClass('menu-open');
					$(self.nodeClass).find('.pp-advanced-menu').addClass('menu-close');
					$('html').removeClass('pp-off-canvas-menu-open');
					$('html').removeClass('pp-full-screen-menu-open');
				} else {
					$(self.nodeClass).find('.pp-advanced-menu').addClass('menu-open');
					if( self.mobileMenuType === 'off-canvas' ) {
						$('html').addClass('pp-off-canvas-menu-open');
						self.offCanvasMenu.find('nav').attr('tabindex', '0').trigger( 'focus' );
						$(this).attr( 'tabindex', '-1' ).trigger( 'blur' );
					}
					if( self.mobileMenuType === 'full-screen' ) {
						$('html').addClass('pp-full-screen-menu-open');
						self.fullScreenMenu.find('nav').attr('tabindex', '0').trigger( 'focus' );
						$(this).attr( 'tabindex', '-1' ).trigger( 'blur' );
					}
				}

				if ( '0' == $(this).attr('tabindex') ) {
					$(this).attr('aria-expanded', 'false');
				} else {
					$(this).attr('aria-expanded', 'true');
				}
			} );

			// Keyboard navigation.
			$(self.nodeClass).find('.pp-advanced-menu-mobile-toggle').on('keyup', function(e) {
				if ( 13 === e.keyCode || 13 === e.which ) {
					$(this).trigger('click');
				}
			});

			// Close button click
			$(self.nodeClass).find('.pp-advanced-menu .pp-menu-close-btn, .pp-clear').on( 'click', function() {
				$(self.nodeClass).find('.pp-advanced-menu').removeClass('menu-open');
				$(self.nodeClass).find('.pp-advanced-menu').addClass('menu-close');
				$('html').removeClass('pp-off-canvas-menu-open');
				$('html').removeClass('pp-full-screen-menu-open');

				if ( $(self.nodeClass).find('.pp-advanced-menu-mobile-toggle' ).is( ':visible' ) ) {
					setTimeout(function() {
						$(self.nodeClass).find('.pp-advanced-menu-mobile-toggle' )
							.attr( 'tabindex', 0 )
							.attr( 'aria-expanded', 'false' )
							.trigger( 'focus' );
					}, 120);
				}

				if ( self.offCanvasMenu && self.offCanvasMenu.length > 0 ) {
					self.offCanvasMenu.find('nav').attr('tabindex', '-1').trigger( 'blur' );
				}
				if ( self.fullScreenMenu && self.fullScreenMenu.length > 0 ) {
					self.fullScreenMenu.find('nav').attr('tabindex', '-1').trigger( 'blur' );
				}
			} );

			if ( this.isBuilderActive ) {
				setTimeout(function() {
					if ( $('.fl-builder-settings[data-node="'+self.settingsId+'"]').length > 0 ) {
						$('.pp-advanced-menu').removeClass('menu-open');
						$(self.nodeClass).find('.pp-advanced-menu-mobile-toggle').trigger('click');
					}
				}, 600);

				FLBuilder.addHook('settings-form-init', function() {
					if ( ! $('.fl-builder-settings[data-node="'+self.settingsId+'"]').length > 0 ) {
						return;
					}
					if ( ! $(self.nodeClass).find('.pp-advanced-menu').hasClass('menu-open') ) {
						$('.pp-advanced-menu').removeClass('menu-open');
						$(self.nodeClass).find('.pp-advanced-menu-mobile-toggle').trigger('click');
					}
				});

				if ( $('html').hasClass('pp-full-screen-menu-open') && ! $(self.nodeClass).find('.pp-advanced-menu').hasClass('menu-open') ) {
					$('html').removeClass('pp-full-screen-menu-open');
				}
				if ( $('html').hasClass('pp-off-canvas-menu-open') && ! $(self.nodeClass).find('.pp-advanced-menu').hasClass('menu-open') ) {
					$('html').removeClass('pp-off-canvas-menu-open');
				}
			}
		},

		/**
		 * Check to see if Below Row should be enabled.
		 *
		 * @since  	2.8.0
		 * @return boolean
		 */
		_isMobileBelowRowEnabled: function() {
			if (this.mobileMenuType === 'default') {
				return this.mobileBelowRow && $( this.nodeClass ).closest( '.fl-col' ).length;
			}
			return false;
		},

		/**
		 * Logic for putting the mobile menu below the menu's
		 * column so it spans the full width of the page.
		 *
		 * @since  2.2
		 * @return void
		 */
		_placeMobileMenuBelowRow: function () {

			if ($(this.nodeClass + '-clone').length) {
				return;
			}
			// if ( $('html').hasClass( 'fl-builder-edit' ) ) {
			// 	return;
			// }

			var module = $(this.nodeClass),
				clone = null,
				row = module.closest('.fl-row-content');

			if ( module.length < 1 ) {
				return;
			}

			clone = ( module.length > 1 ) ? $( module[0] ).clone() : module.clone();
			module.find('.pp-menu-nav').remove();
			clone.addClass((this.nodeClass + '-clone').replace('.', ''));
			clone.addClass('pp-menu-mobile-clone');
			clone.find('.pp-advanced-menu-mobile').remove();
			row.append(clone);

			// Removes animation when enabled.
			if ( module.hasClass( 'fl-animation' ) ) {
				clone.removeClass( 'fl-animation' );
			}

			this._menuOnClick();
		},

		/**
		 * Logic for removing the mobile menu from below the menu's
		 * column and putting it back in the main wrapper.
		 *
		 * @since  2.2
		 * @return void
		 */
		_removeMenuFromBelowRow: function () {

			if (!$(this.nodeClass + '-clone').length) {
				return;
			}

			var module = $(this.nodeClass),
				clone = $(this.nodeClass + '-clone'),
				menu = clone.find('.pp-menu-nav');

			module.find('.pp-advanced-menu-mobile').after(menu);
			module.find('.pp-advanced-menu-mobile-toggle').attr( 'aria-expanded', false );
			clone.remove();
		},

		_toggleMenuSearch: function() {
			var items =  $( this.wrapperClass ).find( '.pp-menu-search-item' ),
				self  = this;

			items.each( function() {
				var item = $(this);
				var button = item.find( '> a' ),
					form = item.find( '.pp-search-form' ),
					input = item.find('.pp-search-form__input');

				button.on( 'click', function(e) {
					e.preventDefault();
					item.toggleClass( 'pp-search-active' );
					if ( item.hasClass( 'pp-search-active' ) ) {
						setTimeout( function() {
							input.focus();
							self._focusMenuSearch( input );
						}, 100 );
					}

					$('body').on( 'click.pp-menu-search', self._hideMenuSearch.bind( self ) );
				} );

				input.on( 'focus', function() {
					form.addClass( 'pp-search-form--focus' );
				} ).on( 'blur', function() {
					form.removeClass( 'pp-search-form--focus' );
				} );
			} );
		},

		_hideMenuSearch: function(e) {
			if (e !== undefined) {
				if ($(e.target).closest('.pp-menu-search-item').length > 0) {
					return;
				}
			}

			$( this.wrapperClass ).find( '.pp-menu-search-item' ).removeClass( 'pp-search-active' );
			$('body').off('click.pp-menu-search');
		},

		_focusMenuSearch: function( $el ) {
			// If this function exists... (IE 9+)
			if ( $el[0].setSelectionRange ) {
				// Double the length because Opera is inconsistent about whether a carriage return is one character or two.
				var len = $el.val().length * 2;

				// Timeout seems to be required for Blink
				setTimeout(function() {
					$el[0].setSelectionRange( len, len );
				}, 1);
			} else {
				// As a fallback, replace the contents with itself
				// Doesn't work in Chrome, but Chrome supports setSelectionRange
				$el.val( $el.val() );
			}
		},

		/**
		 * Adds menu node and post ID to WooCommerce ajax URL requests.
		 *
		 * @since  2.20
		 * @return void
		 */
		 _wooUpdateParams: function() {
			if ( 'undefined' !== typeof wc_cart_fragments_params ) {
				wc_cart_fragments_params.wc_ajax_url += '&pp-advanced-menu-node='+ this.settingsId +'&post-id='+ this.postId;
			}
			if ( 'undefined' !== typeof wc_add_to_cart_params ) {
				wc_add_to_cart_params.wc_ajax_url += '&pp-advanced-menu-node='+ this.settingsId +'&post-id='+ this.postId;
			}
		},

		_validClick: function(e) {
			return (e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined) ? true : false;
		}

	};

})(jQuery);
