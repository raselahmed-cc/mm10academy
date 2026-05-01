;(function($) {

	PPModalBox = function(settings) {
		this.id 			= settings.id;
		this.settings 		= settings;
		this.type			= settings.type;
		this.cookieKey		= 'pp_modal_' + this.id;
		this.cookieTime 	= settings.display_after;
		this.cookieValue 	= settings.cookie_value ? settings.cookie_value : this.cookieTime;
		this.triggerType 	= settings.trigger_type;
		this.layout			= settings.layout;
		this.wrap 			= $('#modal-' + this.id);
		this.container 		= this.wrap.find('.pp-modal-container');
		this.element 		= this.wrap.find('.pp-modal');
		this.isPreviewing 	= settings.previewing;
		this.isVisible		= settings.visible;
		this.eventClose		= false;

		this.initCookieApi();
		this.init();
	};

	PPModalBox.prototype = {
		id			: '',
		settings	: {},
		type		: '',
		cookieKey	: '',
		cookieTime	: 0,
		triggerType	: '',
		layout		: '',
		wrap		: '',
		element		: '',
		prevFocusedEl: '',
		isActive	: false,
		isPreviewing: false,
		isVisible	: false,
		eventClose  : false,

		init: function()
		{
			if ( parseInt( this.cookieTime ) === 0 || this.cookieTime < 0 || this.cookieTime === '' ) {
				this.removeCookie();
			}
			if ( ( 'exit_intent' === this.triggerType || 'auto' === this.triggerType ) && this.getCookie() && ! this.isPreviewing ) {
				return;
			}
			if ( ! this.isPreviewing && 'undefined' !== typeof this.isVisible && ! this.isVisible ) {
				return;
			}
			if ( this.isActive ) {
				return;
			}

			this.setResponsive();
			this.bindEvents();
			this.show();
		},

		setResponsive: function()
		{
			if ( window.innerWidth <= this.settings.breakpoint ) {
                this.element.removeClass('layout-standard').addClass('layout-fullscreen');
            }
            if ( window.innerWidth < this.element.width() ) {
				this.element.css('width', window.innerWidth + 'px');
            }
		},

		bindEvents: function()
		{
			var self = this;

			$( this.element ).on( 'beforeload', this.beforeLoad.bind( this ) );

			$(document).on( 'keyup', function(e) {
				// close modal box on Esc key press.
                if ( self.settings.esc_exit && 27 == e.which && self.isActive && $('form[data-type="pp-modal-box"]').length === 0 ) {
					self.eventClose = true;
                    self.hide();
                }
			}).on( 'keydown', function(e) {
				// trap focus inside the modal element.
				if ( (e.key === 'Tab' || e.keyCode === 9) && self.isActive ) {
					if ( e.shiftKey && $(document.activeElement).is( self.element ) ) {
						e.preventDefault();
					}
					if ( $(document.activeElement).closest( self.element ).length === 0 ) {
						self.element.trigger('focus');
						e.preventDefault();
					}
				}
			} );

			// close modal box by clicking on outside of modal box element in document.
			$(document).on( 'click', function(e) {
                if ( self.settings.click_exit && $(e.target).parents('.pp-modal').length === 0 && self.isActive && ! self.isPreviewing && ! self.element.is(e.target) && self.element.has(e.target).length === 0 && ! $(e.target).hasClass('modal-' + self.id) && $(e.target).parents('.modal-' + self.id).length === 0 && e.which ) {
					self.eventClose = true;
                    self.hide();
                }
			});
			
			// close modal box by clicking on the close button.
            $(self.wrap).find('.pp-modal-close, .pp-modal-close-custom').on('keypress click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				if (e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined) {
					self.eventClose = true;
					self.hide();
				}
			});

			// close the modal box on clicking on inner links start with hash.
			$('body').on( 'click', '#modal-' + self.id + ' .pp-modal-content a[href*="#"]:not([href="#"])', function() {
				if ( $('.fl-node-' + self.id).hasClass('anchor-click-no-event') ) {
					return;
				}
				self.eventClose = true;
                self.hide();
			} );

			$(window).on( 'resize', this.setResponsive.bind( this ) );
			$(window).on( 'resize', this.setPosition.bind( this ) );
		},

		setPosition: function()
		{
			if ( 'fullscreen' !== this.layout ) {
                if ( typeof this.settings.height === 'undefined' ) {

                    this.wrap.addClass('pp-modal-height-auto');
                    var modalHeight = this.element.outerHeight();
                    this.wrap.removeClass('pp-modal-height-auto');

                    if ( 'photo' === this.type ) {
                        this.element.find( '.pp-modal-content-inner img' ).css( 'max-width', '100%' );
                    }

                    var topPos = ( window.innerHeight - modalHeight ) / 2;
                    if ( topPos < 0 ) {
                        topPos = 0;
                    }
                    this.element.css( 'top', topPos + 'px' );
                } else {
                    var topPos = ( window.innerHeight - this.settings.height ) / 2;
					if ( topPos < 0 ) {
                        topPos = 0;
                    }
                    this.element.css( 'top', topPos + 'px' );
                }
			}
		},

		beforeLoad: function() {
			if ( this.settings.clickedElement ) {
				var clickedElement = this.settings.clickedElement;
				var postId = clickedElement.attr( 'data-pp-modal-post' ) || clickedElement.closest( '[data-pp-modal-post]' ).attr( 'data-pp-modal-post' ) || clickedElement.parents( '.pp-content-post' ).attr( 'data-id' );
				var self = this;

				if ( 'undefined' === typeof postId || '' === postId ) {
					return;
				}

				if ( 'html' === self.settings.type && '' !== self.settings.content ) {
					self.element.find( '.pp-modal-content-inner' ).html('<div style="text-align: center;"><img src="' + self.settings.loaderImg + '" /></div>');
					self.setPosition();
					$.ajax({
						url: bb_powerpack.getAjaxUrl(),
						type: 'post',
						data: {
							action: 'pp_modal_dynamic_content',
							content: self.settings.content,
							postId: postId
						},
						success: function( response ) {
							if ( ! response.success ) {
								return;
							}

							self.element.find( '.pp-modal-content-inner' ).html( response.data );
							self.setPosition();
						}
					});
				}
			}
		},

		show: function()
		{
			if ( this.element.length === 0 ) {
				return;
			}

			this.setPosition();

			var self = this;

			if ( 'photo' === this.type ) {
				this.element.find( '.pp-modal-content-inner img' ).css( 'max-width', '100%' );
			}

			this.prevFocusedEl = $( document.activeElement );

			//this.element.find( '.pp-modal-content' ).scrollTop(0);
			
			setTimeout( function() {
				self.element.trigger('beforeload');

				if ( ! self.isPreviewing ) {
					setTimeout(function() {
						self.element.attr( 'tabindex', '0' ).trigger( 'focus' );
					}, 100);
				}

				$('html').addClass( 'pp-modal-active-' + self.id );

				self.wrap.addClass( 'pp-modal-active' );

				self.container
					.removeClass( self.settings.animation_load + ' animated' )
					.addClass( 'modal-visible' )
					.addClass( self.settings.animation_load + ' animated' );

				if ( ! $('body').hasClass('wp-admin') ) {
					self.container.one( 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
						self.eventClose = false;
						$(this).removeClass( self.settings.animation_load + ' animated' );
						self.setup();
					} );
				} else {
					self.setup();
				}

				self.isActive = true;
				
                if ( 'exit_intent' === self.triggerType || 'auto' === self.triggerType ){
                    if ( ! self.isPreviewing ) {
                        self.setCookie();
                    }
				}
				
                self.restruct();
				self.setPosition();

				self.element.trigger('afterload');
				$(document).trigger( 'pp_modal_box_rendered', [self.element] );
				
            }, self.settings.auto_load ? parseFloat(self.settings.delay) * 1000 : 0);
		},

		setup: function()
		{
			if ( this.element.find('.pp-modal-iframe').length > 0 ) {
				var original_src = this.element.find('.pp-modal-iframe').attr('src');
				var src = this.element.find('.pp-modal-iframe').data('url');
				if ( original_src === undefined || original_src === '' ) {
					this.element.find('.pp-modal-iframe').attr( 'src', src );
				}
			}

			var iframeAndSource = this.element.find('iframe, source');
			if ( iframeAndSource.length > 0 && iframeAndSource.closest( '.fl-module' ).length === 0 ) {
				var src = '';
				var m_src = iframeAndSource.attr('src');
				
				if ( m_src === undefined || m_src === '' ) {
					src = iframeAndSource.data('url');
				} else {
					src = iframeAndSource.attr('src');
				}

				if ( src ) {
					if ( ( src.search('youtube') !== -1 || src.search('vimeo') !== -1) && src.search('autoplay=1') == -1 ) {
						if ( typeof src.split('?')[1] === 'string' ) {
							src = src + '&autoplay=1&rel=0';
						} else {
							src = src + '?autoplay=1&rel=0';
						}
					}
					iframeAndSource.attr('src', src);
				}
			}

			if ( this.element.find('video').length ) {
				this.element.find('video')[0].play();
			}
		},

		reset: function()
		{
            var iframeAndSource = this.element.find('iframe, source');
			if ( iframeAndSource.length > 0 && iframeAndSource.closest( '.fl-module' ).length === 0 ) {
				var src = iframeAndSource.attr('src');
				if ( '' !== src ) {
					iframeAndSource.attr('data-url', src).attr('src', '');
				}
            }
			if ( this.element.find('video').length > 0 ) {
				this.element.find('video')[0].pause();
			}
			if ( this.element.find('mux-player').length > 0 ) {
				this.element.find('mux-player')[0].pause();
			}

			this.element.attr( 'tabindex', '-1' );
			var self = this;
			
			setTimeout(function() {
				if ( self.settings.clickedElement ) {
					var $clickedElement = self.settings.clickedElement;
					if ( $clickedElement.closest( '.modal-' + self.id ).length ) {
						$clickedElement.closest( '.modal-' + self.id ).trigger( 'focus' );
					} else if ( $clickedElement.closest( '#trigger-' + self.id ).length ) {
						$clickedElement.closest( '#trigger-' + self.id ).trigger( 'focus' );
					} else if ( '' !== self.settings.customTrigger ) {
						if ( $clickedElement.is( self.settings.customTrigger ) ) {
							$clickedElement.trigger( 'focus' );
						} else if ( $clickedElement.closest( self.settings.customTrigger ).length ) {
							$clickedElement.closest( self.settings.customTrigger ).trigger( 'focus' );
						}
					}
				}
			}, 100);
        },

		restruct: function()
		{
			var mH = 0, hH = 0, cH = 0, eq = 0;
			var self = this;

            setTimeout( function() {
                if ( self.isActive ) {
                    if ( 'fullscreen' === self.layout ) {
                        var marginTop 		= parseInt( self.element.css('margin-top') );
                        var marginBottom 	= parseInt( self.element.css('margin-bottom') );
                        var modalHeight 	= $(window).height() - (marginTop + marginBottom);
						
						self.element.css( 'height', modalHeight + 'px' );
                    }
                    eq = 6;
                    mH = self.element.outerHeight(); // Modal height.
                    hH = self.element.find('.pp-modal-header').outerHeight(); // Header height.

                    if ( self.settings.auto_height && 'fullscreen' !== self.layout ) {
                        return;
					}
					
					var cP = parseInt( self.element.find('.pp-modal-content').css('padding') ); // Content padding.
					
					self.element.find('.pp-modal-content').css( 'height', mH - (hH + eq) + 'px' );
					
                    if ( ! self.settings.auto_height && self.element.find('.pp-modal-header').length === 0) {
                        self.element.find('.pp-modal-content').css('height', mH + 'px');
                    }
				   
					// Adjust iframe height.
                    if ( 'url' === self.type && self.element.find( '.pp-modal-video-embed' ).length === 0 ) {
                        self.element.find('.pp-modal-iframe').css('height', self.element.find('.pp-modal-content-inner').outerHeight() + 'px');
                    }
                    if ( 'video' === self.type ) {
                        self.element.find('iframe').css({'height':'100%', 'width':'100%'});
                    }
                }
            }, self.settings.auto_load ? parseFloat(self.settings.delay) * 1000 : 1);
		},

		hide: function()
		{
			var self = this;

			this.element.trigger('beforeclose');

            this.container
                .removeClass( self.settings.animation_exit + ' animated' )
				.addClass( self.settings.animation_exit + ' animated' );
				
			if ( ! $('body').hasClass('wp-admin') ) {
				this.container.one( 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
					if ( self.eventClose ) {
						self.close();
					}
				});
			} else {
				self.close();
			}
				
            if ( window.location.hash ) {
				var hashVal = window.location.hash.replace('/', '');
                if ( '#modal-' + self.id === hashVal ) {
                    var scrollTop = self.settings.scrollTop || $(window).scrollTop();
                    window.location.href = window.location.href.split('#')[0] + '#';
                    $(window).scrollTop(scrollTop);
                }
			}

			if ( ! this.isPreviewing ) {
				this.element.attr( 'tabindex', '-1' ).trigger( 'blur' );
				if ( this.prevFocusedEl && this.prevFocusedEl.length > 0 ) {
					this.prevFocusedEl.trigger( 'focus' );
				}
			}
			
			this.element.trigger('afterclose');
			$(document).trigger( 'pp_modal_box_after_close', [this.element, this] );
		},

		close: function()
		{
			this.container.removeClass( this.settings.animation_exit + ' animated' ).removeClass('modal-visible');
			this.container.find('.pp-modal-content').removeAttr('style');
			this.wrap.removeClass( 'pp-modal-active' );

			$('html').removeClass( 'pp-modal-active-' + this.id );

			this.isActive = false;
			this.eventClose = false;
			this.reset();
		},

		initCookieApi: function() {
			if ( 'undefined' === typeof $.cookie ) {
				/*!
				 * jQuery Cookie Plugin v1.4.1
				 * https://github.com/carhartl/jquery-cookie
				 *
				 * Copyright 2013 Klaus Hartl
				 * Released under the MIT license
				*/
				!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a){function b(a){return h.raw?a:encodeURIComponent(a)}function c(a){return h.raw?a:decodeURIComponent(a)}function d(a){return b(h.json?JSON.stringify(a):String(a))}function e(a){0===a.indexOf('"')&&(a=a.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return a=decodeURIComponent(a.replace(g," ")),h.json?JSON.parse(a):a}catch(b){}}function f(b,c){var d=h.raw?b:e(b);return a.isFunction(c)?c(d):d}var g=/\+/g,h=a.cookie=function(e,g,i){if(void 0!==g&&!a.isFunction(g)){if(i=a.extend({},h.defaults,i),"number"==typeof i.expires){var j=i.expires,k=i.expires=new Date;k.setTime(+k+864e5*j)}return document.cookie=[b(e),"=",d(g),i.expires?"; expires="+i.expires.toUTCString():"",i.path?"; path="+i.path:"",i.domain?"; domain="+i.domain:"",i.secure?"; secure":""].join("")}for(var l=e?void 0:{},m=document.cookie?document.cookie.split("; "):[],n=0,o=m.length;o>n;n++){var p=m[n].split("="),q=c(p.shift()),r=p.join("=");if(e&&e===q){l=f(r,g);break}e||void 0===(r=f(r))||(l[q]=r)}return l};h.defaults={},a.removeCookie=function(b,c){return void 0===a.cookie(b)?!1:(a.cookie(b,"",a.extend({},c,{expires:-1})),!a.cookie(b))}});
			}
		},

		setCookie: function()
		{
			if ( parseInt( this.cookieTime ) > 0 ) {
				return $.cookie( this.cookieKey, this.cookieValue, {expires: this.cookieTime, path: '/'} );
			} else {
				this.removeCookie();
			}
		},

		getCookie: function()
		{
			// Reset cookie when module settings change.
			if ( this.cookieValue != $.cookie( this.cookieKey ) ) {
				this.removeCookie();
			}
			return $.cookie( this.cookieKey );
		},

		removeCookie: function()
		{
			$.cookie( this.cookieKey, 0, {expires: 0, path: '/'} );
		}
	};
})(jQuery);
