; (function ($) {

	PPOffcanvasContent = function (settings) {
		this.id 				= settings.id;
		this.node 				= $('.fl-node-' + this.id);
		this.wrap 				= this.node.find('.pp-offcanvas-content-wrap');
		this.content 			= this.node.find('.pp-offcanvas-content');
		this.button 			= this.node.find('.pp-offcanvas-toggle');
		this.direction			= settings.direction,
		this.contentTransition	= settings.contentTransition,
		this.closeButton		= settings.closeButton,
		this.escClose			= settings.escClose,
		this.closeButton		= settings.closeButton,
		this.bodyClickClose		= settings.bodyClickClose,
		this.toggleSource		= settings.toggleSource,
		this.toggle_class		= settings.toggle_class,
		this.toggle_id			= settings.toggle_id,
		this.innerWrapper		= settings.innerWrapper,
		this.size				= settings.size,
		this.duration			= 500,
		this.isBuilderActive 	= settings.isBuilderActive,
		this._active = false;
		this._previous = false;

		this._destroy();
		this._init();
	};

	PPOffcanvasContent.prototype = {
		animations: [
			'slide',
			'slide-along',
			'reveal',
			'push',
		],

		_active: false,
		_previous: false,

		_init: function () {
			if (!this.wrap.length) {
				return;
			}

			if ( this.isBuilderActive ) {
				return;
			}

			$('html').addClass('pp-offcanvas-content-widget');

			if ( this.innerWrapper ) {
				if ($('.pp-offcanvas-container').length === 0) {
					$('body').wrapInner('<div class="pp-offcanvas-container" />');
					this.content.insertBefore('.pp-offcanvas-container');
				}
			} else {
				$('body').addClass('pp-offcanvas-container');
				//this.content.prependTo( 'body' );
			}

			if (this.wrap.find('.pp-offcanvas-content').length > 0) {
				if ($('.pp-offcanvas-container > .pp-offcanvas-content-' + this.id).length > 0) {
					$('.pp-offcanvas-container > .pp-offcanvas-content-' + this.id).remove();
				}
				if ($('body > .pp-offcanvas-content-' + this.id).length > 0) {
					$('body > .pp-offcanvas-content-' + this.id).remove();
				}
				$('body').prepend(this.wrap.find('.pp-offcanvas-content'));
			}

			this._setSize();
			this._bindEvents();

			$(document).trigger( 'pp_offcanvas_after_init', [ $('.pp-offcanvas-content-' + this.id) ] );
		},

		_setSize: function() {
			if ( '' !== this.size ) {
				return;
			}
			if ( 'top' !== this.direction || 'bottom' !== this.direction ) {
				return;
			}
			var offCanvasContent = $('.pp-offcanvas-content-' + this.id),
				offCanvasBody = offCanvasContent.find( '.pp-offcanvas-body' );

			offCanvasContent.css( {
				'height': offCanvasBody.outerHeight() + 'px',
				'max-height': ( window.innerHeight ) + 'px'
			} );
		},

		_destroy: function () {
			this._close();

			this.animations.forEach(function (animation) {
				if ($('html').hasClass('pp-offcanvas-content-' + animation)) {
					$('html').removeClass('pp-offcanvas-content-' + animation)
				}
			});
		},
		
		_getTrigger: function () {
			var trigger = false;

			if (this.toggleSource == 'id' && this.toggle_id != '') {
				var toggleId = this.toggle_id.replace('#', '');
				trigger = '#' + toggleId;
			} else if (this.toggleSource == 'class' && this.toggle_class != '') {
				var toggleClass = this.toggle_class.replace('#', '');
				trigger = '.' + toggleClass;
			} else {
				trigger = '.fl-node-' + this.id + ' .pp-offcanvas-toggle';
			}

			return trigger;
		},

		_bindEvents: function () {
			var self = this;
			var trigger = this._getTrigger();
			var scrollPos = $(window).scrollTop();

			if (trigger) {
				$('body').on( 'click', trigger, this._toggleContent.bind( this ) );
			}

			this._onHashChange();

			$(window).on('hashchange', function(e) {
				e.preventDefault();
				window['pp_offcanvas_' + self.id]._onHashChange();
			});

			$('body').on( 'click keyup', '.pp-offcanvas-content .pp-offcanvas-close', function(e) {
				if (e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined) {
					this._close();
				}
			}.bind( this ) );

			$('body').on( 'click keyup', '.pp-offcanvas-' + this.id + '-close', function(e) {
				e.preventDefault();
				window['pp_offcanvas_' + self.id]._close();
			} );

			// Close the off-canvas panel on clicking on inner links start with hash.
			$('body').on( 'click', '.pp-offcanvas-content .pp-offcanvas-body a[href*="#"]:not([href="#"])', this._close.bind( this ) );

			$('body').on( 'click', 'a[href*="#"]:not([href="#"])', function(e) {
				var hash = '#' + $(this).attr('href').split('#')[1];

				if ( $(hash).length > 0 && $(hash).hasClass( 'fl-node-' + self.id ) ) {
					if ( ! $('html').hasClass('pp-offcanvas-content-open') ) {
						self._show();
					}
				}
			} );

			if (this.escClose === 'yes') {
				this._closeESC();
			}
			if (this.bodyClickClose === 'yes') {
				this._closeClick();
			}

			$(window).on( 'resize', this._setSize.bind( this ) );
		},

		_onHashChange: function() {
			var hash = location.hash.split('/')[0].replace('!', '');
			var self = this;

			if ( $(hash).length > 0 && $(hash).hasClass( 'fl-node-' + this.id ) ) {
				setTimeout(function() {
					if ( ! $('html').hasClass('pp-offcanvas-content-open') ) {
						self._show();
					}
				}, 500);
			}
		},

		_toggleContent: function (e) {
			e.preventDefault();

			if (!$('html').hasClass('pp-offcanvas-content-open')) {
				this._show();
			} else {
				this._close();
			}
		},

		_show: function () {
			$(document).trigger( 'pp_offcanvas_before_reveal', [ $('.pp-offcanvas-content-' + this.id) ] );

			this._previous = this._active;
			var self = this;

			// init animation class.
			$('html').addClass('pp-offcanvas-content-' + this.contentTransition);
			$('html').addClass('pp-offcanvas-content-' + this.direction);
			$('html').addClass('pp-offcanvas-content-open');
			$('html').addClass('pp-offcanvas-content-' + this.id + '-open');
			$('html').addClass('pp-offcanvas-content-reset');

			setTimeout(function() {
				$('.pp-offcanvas-content-' + self.id).addClass('pp-offcanvas-content-visible').attr('tabindex', '0');
				$('.pp-offcanvas-content-' + self.id).trigger( 'focus' );
			}, 250);

			this.button.addClass('pp-is-active');

			this._active = {
				id: this.id,
				contentTransition: this.contentTransition,
				direction: this.direction,
				$button: this.button
			};

			$(document).trigger( 'pp_offcanvas_after_reveal', [ $('.pp-offcanvas-content-' + this.id) ] );
		},

		_close: function () {
			$(document).trigger( 'pp_offcanvas_before_close', [ $('.pp-offcanvas-content-' + this.id) ] );

			var hash = location.hash.split('/')[0].replace('!', '');
			var self = this;

			$('html').removeClass('pp-offcanvas-content-open');
			$('html').removeClass('pp-offcanvas-content-' + this.id + '-open');
			setTimeout( function () {
				$('html').removeClass('pp-offcanvas-content-reset');
				$('html').removeClass('pp-offcanvas-content-' + this.contentTransition);
				$('html').removeClass('pp-offcanvas-content-' + this.direction);
				$('.pp-offcanvas-content-' + this.id).removeClass('pp-offcanvas-content-visible');
				$('.pp-offcanvas-content-' + this.id).trigger('blur');

				if ( $(hash).length > 0 && $(hash).hasClass( 'fl-node-' + this.id ) ) {
					if ( ! $('html').hasClass('pp-offcanvas-content-open') ) {
						var scrollPos = $(window).scrollTop();
						location.href = location.href.split('#')[0] + '#';
						window.scrollTo(0, scrollPos);
					}
				}
			}.bind( this ), 500);

			setTimeout( function() {
				$('.pp-offcanvas-content-' + self.id).addClass('pp-offcanvas-content-visible').attr( 'tabindex', '-1' );
			}, 250 );

			this.button.removeClass('pp-is-active');
			this._active = false;

			$(document).trigger( 'pp_offcanvas_after_close', [ $('.pp-offcanvas-content-' + this.id) ] );
		},

		_closeESC: function () {
			var self = this;

			if ('no' === self.escClose) {
				return;
			}

			// menu close on ESC key
			$(document).on('keydown', function (e) {
				if (e.keyCode === 27) { // ESC
					self._close();
				}
			});
		},

		_closeClick: function () {
			var self = this;

			if (this.toggleSource == 'id' && this.toggle_id != '') {
				$trigger = '#' + this.toggle_id;
			} else if (this.toggleSource == 'class' && this.toggle_class != '') {
				$trigger = '.' + this.toggle_class;
			} else {
				$trigger = '.pp-offcanvas-toggle';
			}

			$(document).on('click', function (e) {
				if ( $(e.target).is('.pp-offcanvas-content') || 
					$(e.target).parents('.pp-offcanvas-content').length > 0 || 
					$(e.target).is('.pp-offcanvas-toggle') || 
					$(e.target).parents('.pp-offcanvas-toggle').length > 0 || 
					$(e.target).is($trigger) || 
					$(e.target).parents($trigger).length > 0 || 
					! $(e.target).is('.pp-offcanvas-container') ) {
					return;
				} else {
					self._close();
				}
			});
		}
	};
}) (jQuery);
