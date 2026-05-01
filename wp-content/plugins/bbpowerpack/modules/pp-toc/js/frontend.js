(function ($) {

	PPToCModule = function (settings) {
		this.settings = settings;
		this.id = settings.id;
		this.nodeClass = '.fl-node-' + settings.id;
		this.anchorId = settings.anchorId;
		this.headData = settings.headData;
		this.additionalOffset = settings.additionalOffset;
		this.container = settings.container;
		this.exclude = settings.exclude;
		this.collapsableToc = settings.collapsableToc;
		this.collapseOn = settings.collapseOn;
		this.hierarchicalView = settings.hierarchicalView;
		this.stickyOn = settings.stickyOn;
		this.stickyType = settings.stickyType;
		this.stickyFixedOffset = settings.stickyFixedOffset;
		this.stickyBuilderOff = settings.stickyBuilderOff;
		this.scrollTop = settings.scrollTop;
		this.scrollTo = settings.scrollTo;
		this.scrollAlignment = settings.scrollAlignment;
		this.breakpoints = settings.breakpoints;
		this.listIcon = settings.listIcon;
		this.listStyle = settings.listStyle;
		this.elementIds = {};

		if ( $( this.nodeClass ).length === 0 ) {
			return;
		}

		this.init();
	}

	PPToCModule.prototype = {
		settings: {},
		node: "",
		nodeClass: "",
		elementIds: {},

		init: function () {
			$( document ).trigger( 'pp_toc_before_init', [ this ] );

			var nodeId = this.id;
			var selectedHead = this.headData;
			var collapsableToc = this.collapsableToc;
			var hierarchicalView = this.hierarchicalView;
			var listIcon = this.listIcon;
			var listStyle = this.listStyle;

			// Checks for container value
			if (0 === $( this.container ).length || 'body' === this.container) {
				this.container = $( 'body' ).find( '.fl-builder-content:not(header):not(footer)' );
			}

			// Check if the style type is icon or else the value of the icon will be reset.
			if ('icon' !== listStyle) {
				listIcon = '';
			}

			// Add a class to the specific heading that is excluded by the user.
			this.excludeHeadings();

			this.insertAnchors();

			// For nested/hierarchical view.
			if ('yes' === hierarchicalView) {
				this.nestedView({
					content: this.container,
					headings: selectedHead,
					icon: listIcon,
				});
			} else {
				// simple population of the List Items.
				this.flatView();
			}

			if ('icon' === listStyle) {
				$( '.pp-toc-list-wrapper li > span' ).each(function () {
					$( this ).next( 'a' ).addBack().wrapAll( '<div class="pp-toc-listicon-wrapper"/>' );
				});
			}

			if ('yes' === collapsableToc) {
				$( '.fl-node-' + nodeId + ' .pp-toc-header' ).on( 'click', this.toggleToC.bind( this ) );

				this.collapseOnDevices();
			} else {
				this.expandToC();
			}

			// Init sticky mode.
			this.stickyTocInit();

			// Scroll to Top.
			this.scrollTopInit();

			this.smoothScroll();

			this.resizeToCBody();

			$( window ).on( 'resize', this.resizeToCBody.bind( this ) );

			$( document ).trigger( 'pp_toc_after_init', [ this ] );
		},

		getAutoId: function( index ) {
			return 'pp-toc-' + this.id + '-anchor-' + index;
		},

		getTextId: function( $element ) {
			//var regex = /\s+|"|'|#|\.|*|\^|{|}|/g;
			var id = $($element).text()
				.toLowerCase()
				.trim()
				.normalize('NFD') // Decompose accented characters into base character and diacritical mark
				.replace(/[\u0300-\u036f]/g, '') // Remove diacritical marks
				.replace(/\W+/g, '-') // Replace non-alphabetic characters with hyphens
				.replace(/^-+|-+$/g, ''); // Remove leading and trailing hyphens

			return id;
		},

		getId: function( $element, index ) {
			if ( 'text' === this.anchorId ) {
				var id = this.getTextId( $element );
				if ( $( '#' + id ).length > 1 ) {
					id = id + '-' + index;
				}
				return id;
			}

			return this.getAutoId( index );
		},

		insertAnchors: function() {
			var self = this;
			$( this.container ).find( this.headData ).not( '.pp-toc-exclude-element' ).before(function (index) {
				var id = self.getId( $(this), index );
				self.elementIds[index] = id; // Store the ID to build ToC.
				return (
					'<span id="' + id + '" data-element-index="' + index + '"></span>'
				);
			});
		},

		nestedView: function (options) {
			var self = this;
			var list = $( this.nodeClass + ' .pp-toc-list-wrapper' ),
				stack = [list], // The upside-down stack keeps track of list elements
				currentLevel = 0,
				headingSelectors;

			options = $.extend(
				{
					content: 'body',
					headings: 'h1,h2,h3,h4,h5,h6'
				},
				options
			);

			headingSelectors = options.headings.split( "," );

			var $headings = $( options.content ).find( options.headings ).not( '.pp-toc-exclude-element' );

			if ( $headings.length === 0 ) {
				$( this.nodeClass + ' .pp-toc-container' ).addClass( 'is-empty' );
				return;
			} else {
				$( this.nodeClass + ' .pp-toc-container' ).addClass( 'pp-toc-initialized' );
			}

			$headings.each(function (index) {
				// What level is the current heading?
				var elem = $( this ),
					level = $.map(headingSelectors, function (selector, index) {
						return elem.is( selector ) ? index : undefined;
					})[0];

				if (level > currentLevel) {
					// If the heading is at a deeper level than where we are, start a new nested
					// list, but only if we already have some list items in the parent. If we do
					// not, that means that we're skipping levels, so we can just add new list items
					// at the current level.
					// In the upside-down stack, unshift = push, and stack[0] = the top.
					var parentItem = stack[0].children( "li:last" )[0];
					if (parentItem) {
						stack.unshift( $( "<" + list.prop('tagName').toLowerCase() + "/>" ).appendTo( parentItem ) );
					}
				} else {
					// Truncate the stack to the current level by chopping off the 'top' of the
					// stack. We also need to preserve at least one element in the stack - that is
					// the containing element.
					stack.splice( 0, Math.min( currentLevel - level, Math.max( stack.length - 1, 0 ) ) );
				}

				var id = self.elementIds[index];

				// Add the list item
				$( "<li/>" ).appendTo( stack[0] ).append(
					options.icon && $( '<span/>' ).addClass( options.icon ), $( "<a/>" ).text( elem.text().trim() ).attr( "href", "#" + id )
				);

				currentLevel = level;
			});
		},

		// Non-nested function initiation
		flatView: function () {
			var listIcon = this.listIcon;
			var listStyle = this.listStyle;
			var $headings = $( this.container ).find( this.headData ).not( '.pp-toc-exclude-element' );
			var self = this;

			if ( $headings.length === 0 ) {
				$( this.nodeClass + ' .pp-toc-container' ).addClass( 'is-empty' );
				return;
			} else {
				$( this.nodeClass + ' .pp-toc-container' ).addClass( 'pp-toc-initialized' );
			}

			$headings.each(function (index) {

				var anchor = '<a href="#' + self.getId( $(this), index ) + '">' + $( this ).text().trim() + '</a>';

				if ('icon' !== listStyle) {
					var li = '<li>' + anchor + '</li>';
				} else {
					var li = '<li><span class="' + listIcon + '"></span>' + anchor + '</li>';
				}

				$( li ).appendTo( '.pp-toc-list-wrapper' );

			});
		},

		toggleToC: function () {
			if ($( this.nodeClass + ' .pp-toc-header' ).hasClass( 'pp-toc-collapsed' )) {
				this.expandToC();
			} else {
				this.collapseToC();
			}
		},

		collapseToC: function () {
			$( this.nodeClass + ' .pp-toc-header' ).addClass( 'pp-toc-collapsed' );
			$( this.nodeClass + ' .header-icon-expand' ).addClass( 'active' );
			$( this.nodeClass + ' .header-icon-collapse' ).removeClass( 'active' );
			$( this.nodeClass + ' .pp-toc-body' ).slideUp( 500, this.resizeToCBody.bind( this ) );

			var self = this;
			setTimeout(function () {
				$( self.nodeClass + ' .pp-toc-container-placeholder' ).css( 'height', 'auto' );
			}, 500);
		},

		expandToC: function () {
			$( this.nodeClass + ' .pp-toc-header' ).removeClass( 'pp-toc-collapsed' );
			$( this.nodeClass + ' .header-icon-expand' ).removeClass( 'active' );
			$( this.nodeClass + ' .header-icon-collapse' ).addClass( 'active' );
			$( this.nodeClass + ' .pp-toc-body' ).slideDown( 500, this.resizeToCBody.bind( this ) );
		},

		collapseOnDevices: function () {
			if ( this.matchDevice( this.collapseOn ) ) {
				this.collapseToC();
			} else {
				this.expandToC();
			}
		},

		matchDevice: function (value) {
			var match = false;

			if ('all' === value) {
				match = true;
			} else if ('xl' === value) {
				match = window.innerWidth > this.breakpoints.large;
			} else if ('xl-desktop' === value) {
				match = window.innerWidth > this.breakpoints.tablet;
			} else if ('xl-desktop-medium' === value) {
				match = window.innerWidth >= this.breakpoints.tablet;
			} else if ('large' === value) {
				match = window.innerWidth > this.breakpoints.tablet && window.innerWidth < this.breakpoints.large;
			} else if ('large-medium' === value) {
				match = window.innerWidth <= this.breakpoints.large && window.innerWidth > this.breakpoints.mobile;
			} else if ('medium' === value) {
				match = window.innerWidth > this.breakpoints.mobile && window.innerWidth <= this.breakpoints.tablet;
			} else if ('medium-responsive' === value) {
				match = window.innerWidth <= this.breakpoints.tablet;
			} else if ('responsive' === value) {
				match = window.innerWidth <= this.breakpoints.mobile;
			}

			return match;
		},

		resizeToCBody: function () {
			var self = this,
				winHeight = $( window ).height(),
				$tocBody = $( self.nodeClass + ' .pp-toc-body' ),
				$tocHeader = $( self.nodeClass + ' .pp-toc-header' ),
				height = winHeight - ($tocHeader.length > 0 ? $tocHeader.outerHeight() : 20);

			if ( $( self.nodeClass + ' .pp-toc-is-sticky' ).length > 0 ) {
				height = winHeight - $tocBody[0].getBoundingClientRect().top;
			}

			$tocBody.css( 'overflow-y', 'auto' );

			setTimeout(function () {
				if (winHeight < $tocBody.outerHeight()) {
					$tocBody.css( 'height', height + 'px' );
				} else {
					$tocBody.css( 'height', 'auto' );
				}
			}, 1000);
		},

		excludeHeadings: function () {
			$( this.container ).find( this.headData ).each(function () {
				if ('' === $( this ).text().trim()) {
					$( this ).addClass( 'pp-toc-exclude-element' );
				}
			});

			if ('' !== this.exclude) {
				$( this.exclude ).find( 'h1,h2,h3,h4,h5,h6' ).each(function () {
					$( this ).addClass( 'pp-toc-exclude-element' );
				});
			}
		},

		stickyTocInit: function () {
			if ( 'yes' === this.stickyBuilderOff && $('body').hasClass('fl-builder-edit') ) {
				$( this.nodeClass + ' .pp-toc-container' ).removeClass( 'pp-toc-is-sticky' );
				return;
			}
			if ( ! this.matchDevice( this.stickyOn )) {
				$( this.nodeClass + ' .pp-toc-container' ).removeClass( 'pp-toc-is-sticky' );
				return;
			} else {
				$( this.nodeClass + ' .pp-toc-container' ).addClass( 'pp-toc-is-sticky' );
			}

			var self = this,
				nodeId = this.id,
				isBuilder = $('body').hasClass( 'fl-builder-edit' ) && $('.fl-builder-bar').length > 0,
				isAdminBar = $( 'body' ).hasClass( 'admin-bar' ),
				isFixed = 'fixed' === this.stickyType,
				stickyOffset = $( this.nodeClass + ' .pp-toc-container' ).offset().top,
				stickyOffsetCustom = stickyOffset + $( self.nodeClass + ' .pp-toc-container' ).height(),
				stickyWidth = $( self.nodeClass + ' .pp-toc-container' ).outerWidth(),
				stickyHeight = $( self.nodeClass + ' .pp-toc-container' ).outerHeight(),
				placeholder = $( '<div />', { class: 'pp-toc-container-placeholder', css: { width: stickyWidth } } ),
				hideTo = self.settings.hideTo;

			if ( '' !== hideTo ) {
				hideTo = '.' + hideTo.replace( '.', '' );
			}

			$( self.nodeClass + ' .pp-toc-container' ).wrap( placeholder );

			$( window ).on('scroll', function () {
				if ( ! $( self.nodeClass + ' .pp-toc-container' ).hasClass( 'pp-toc-is-sticky' )) {
					return;
				}

				var scrollPos = $( window ).scrollTop() + (isFixed ? self.stickyFixedOffset : 0);
				var stickyOffsetFixed = stickyOffset;

				stickyHeight = $( self.nodeClass + ' .pp-toc-container' ).outerHeight();
				$( self.nodeClass + ' .pp-toc-container-placeholder' ).height( stickyHeight );

				if ( isBuilder ) {
					scrollPos += 45;
					stickyOffsetFixed += 45;
				} else if (isAdminBar) {
					scrollPos += 32;
					//stickyOffsetFixed -= 32;
				}

				if ( '' !== hideTo && $( hideTo ).length > 0 ) {
					var lastRowOffset = ( $( hideTo ).offset().top - ( $( self.nodeClass + ' .pp-toc-container' ).offset().top + $( self.nodeClass + ' .pp-toc-container-placeholder' ).height() ) );

					if ( lastRowOffset <= 25 ) {
						$( self.nodeClass + ' .pp-toc-container' ).addClass( 'pp-toc--stop' );
					} else {
						$( self.nodeClass + ' .pp-toc-container' ).removeClass( 'pp-toc--stop' );
					}
				}

				if (isFixed) {

					if (scrollPos >= stickyOffsetFixed) {
						$( self.nodeClass + ' .pp-toc-container' ).removeClass( 'pp-toc-sticky-custom' );
						$( self.nodeClass + ' .pp-toc-container' ).addClass( 'pp-toc-sticky-fixed' );
						$( self.nodeClass + ' .pp-toc-sticky-fixed' ).width( stickyWidth );
					} else {
						$( self.nodeClass + ' .pp-toc-container' ).removeClass( 'pp-toc-sticky-fixed' );
						$( self.nodeClass + ' .pp-toc-container' ).css( 'height', 'auto' );
						//$( self.nodeClass + ' .pp-toc-container' ).css( 'overflow', 'visible' );
					}
				} else {
					stickyOffsetCustom = stickyOffset + $( self.nodeClass + ' .pp-toc-container' ).height();
					if (scrollPos >= stickyOffsetCustom) {
						$( self.nodeClass + ' .pp-toc-container' ).removeClass( 'pp-toc-sticky-fixed' );
						$( self.nodeClass + ' .pp-toc-container' ).addClass( 'pp-toc-sticky-custom' );
						$( self.nodeClass + ' .pp-toc-sticky-custom' ).width( stickyWidth );

						if ($( self.nodeClass + ' .pp-toc-container' ).height() > window.innerHeight) {
							$( self.nodeClass + ' .pp-toc-container' ).css( 'height', window.innerHeight + 'px' );
						}
					} else {
						$( self.nodeClass + ' .pp-toc-container' ).removeClass( 'pp-toc-sticky-custom' );
						$( self.nodeClass + ' .pp-toc-container' ).css( 'height', 'auto' );
						//$( self.nodeClass + ' .pp-toc-container' ).css( 'overflow', 'visible' );
					}
				}

			});

		},

		scrollTopInit: function () {
			if ( ! this.matchDevice( this.scrollTop )) {
				return;
			}

			$( this.nodeClass + ' .pp-toc-scroll-top-container' ).fadeIn();

			var isWindow = 'window' === this.scrollTo;
			var nodeId = this.id;
			var scrollOffset = $( this.nodeClass + ' .pp-toc-container' ).offset().top;
			var scrollAlignment = this.scrollAlignment;

			if ('right' === scrollAlignment) {
				$( this.nodeClass + ' .pp-toc-scroll-top-container' ).removeClass( 'pp-toc-scroll-align-left' );
				$( this.nodeClass + ' .pp-toc-scroll-top-container' ).addClass( 'pp-toc-scroll-align-right' );
			} else {
				$( this.nodeClass + ' .pp-toc-scroll-top-container' ).removeClass( 'pp-toc-scroll-align-right' );
				$( this.nodeClass + ' .pp-toc-scroll-top-container' ).addClass( 'pp-toc-scroll-align-left' );
			}

			// Fade In and Out as per the selected scroll type
			$( window ).on( 'scroll', function () {
				if (isWindow) {
					if ($( this ).scrollTop() > 1) {
						$( '.fl-node-' + nodeId + ' .pp-toc-scroll-top-container' ).fadeIn();
					} else {
						$( '.fl-node-' + nodeId + ' .pp-toc-scroll-top-container' ).fadeOut();
					}
				} else {
					if ($( this ).scrollTop() > scrollOffset) {
						$( '.fl-node-' + nodeId + ' .pp-toc-scroll-top-container' ).fadeIn();
					} else {
						$( '.fl-node-' + nodeId + ' .pp-toc-scroll-top-container' ).fadeOut();
					}
				}
			});

			// Click Event for Scroll Till Top or ToC.
			$( this.nodeClass + ' .pp-toc-scroll-top-container' ).on('click', function () {
				if (isWindow) {
					$( 'html, body' ).animate( { scrollTop: 0 }, 800 );
				} else {
					$( 'html, body' ).animate( { scrollTop: scrollOffset }, 800 );
				}
			});
		},

		smoothScroll: function () {
			var extraOffset = $( 'body' ).hasClass( 'admin-bar' ) ? 32 : 0;
			if ( '' !== this.additionalOffset.desktop && ! isNaN( this.additionalOffset.desktop ) ) {
				extraOffset += this.additionalOffset.desktop;
			}
			if ( window.innerWidth <= this.breakpoints.tablet && '' !== this.additionalOffset.tablet ) {
				extraOffset += this.additionalOffset.tablet;
			}
			if ( window.innerWidth <= this.breakpoints.mobile && '' !== this.additionalOffset.mobile ) {
				extraOffset += this.additionalOffset.mobile;
			}
			var offset = 0;
			var hash = '';
			$( '.fl-node-' + this.id + ' a' ).on('click', function (e) {
				hash = $( this ).attr( 'href' ).replace( '#', '' );
				if (hash !== '' && $( '#' + hash ).length > 0) {
					e.preventDefault();
					offset = Math.round( $( '#' + hash ).offset().top - extraOffset );
					$( 'html, body' ).animate({
						scrollTop: offset
						}, 800, function () {
							window.location.hash = hash;
							window.scrollTo(0, offset);
						}
					);
				}
			});
			$(window).on('hashchange', function(e) {
				if ( window.location.hash === '#' + hash ) {
					e.preventDefault();
					e.stopPropagation();
					window.scrollTo(0, offset);
				}
			});
		},
	}

})(jQuery);
