;(function( $ ) {

	PPVideoGallery = function( settings ) {
		this.id				= settings.id;
		this.layout 		= settings.layout;
		this.aspectRatio 	= settings.aspectRatioLightbox;
		this.filters		= settings.filters;
		this.carousel		= settings.carousel;
		this.isBuilderActive = settings.isBuilderActive;
		this.nodeClass 		= '.fl-node-' + this.id;
		this.itemClass		= this.nodeClass + ' .pp-video-gallery-item';
		this.elements		= {};
		this.swiper			= {};
		this.lightboxWrap	= '<div class="pp-aspect-ratio-'+this.aspectRatio+'"></div>';
		this.lightboxData	= [];
		this.activeIndex	= false;
		this.filtersObj 	= '';

		this._init();
	};

	PPVideoGallery.prototype = {
		_init: function() {
			this.elements = {
				node: $(this.nodeClass),
				wrap: $(this.nodeClass).find('.pp-video-gallery-items'),
				pagination: $(this.nodeClass).find('.pp-video-gallery-pagination'),
				items: '',
			};

			this._initVideo();
			this._bindEvents();

			if ( 'gallery' === this.layout ) {
				// Initialize the gallery.
				this._initGallery();
				this._initPagination();
			} else {
				// Initialize the carousel.
				if ( typeof Swiper === 'undefined' ) {
					$(window).on('load', function() {
						if ( typeof Swiper === 'undefined' ) {
							return;
						} else {
							this._initCarousel();
						}
					}.bind( this ) );
				} else {
					this._initCarousel();
				}
			}
		},

		_initVideo: function() {
			var self = this;
			
			this.lightboxData = [];

			this._getItems();

			this.elements.items.each( function() {
				var $item = $(this);

				if ( $(this).find( '.pp-video-image-overlay' ).length > 0 ) {
					//$(this).find( '.pp-video-image-overlay' ).off('click');
					self._stopIframeAutoplay($item);
					
					if ( $item.find('.pp-video-lightbox-content').length > 0 ) {
						var data = {
							src: $(self.lightboxWrap).html( $item.find('.pp-video-lightbox-content').html() ),
							type: 'html',
							opts: {
								slideClass: 'pp-video-slide-' + $item.index()
							}
						};
		
						self.lightboxData.push( data );
					}
				} else {
					var videoFrame = $item.find('.pp-video-iframe');
					videoFrame.attr( 'src', videoFrame.data( 'src' ) );
				}
			} );
		},

		_initGallery: function() {
			if ( this.filters ) {
				this._initFilters();
				this._initResponsiveFilters();

				$(window).on( 'resize', this._initResponsiveFilters.bind( this ) );
			}
		},

		_initCarousel: function() {
			var self = this;

			this.elements.swiper = this.nodeClass + ' .swiper-container';

			this.elements.swiperSlide = $(this.elements.swiper).find('.swiper-slide');
			
			if (1 >= this._getSlidesCount()) {
                return;
			}
			
			this.swiper = new Swiper(this.elements.swiper, this._getSwiperOptions());

			$(document).on('pp-tabs-switched', function(e, selector) {
				if ( selector.find('.fl-node-' + self.id).length > 0 ) {
					self.swiper.update();
				}
			});
			$(document).on('fl-builder.pp-accordion-toggle-complete', function(e) {
				if ( $(e.target).find('.fl-node-' + self.id).length > 0 ) {
					self.swiper.update();
				}
			});
		},

		_bindEvents: function() {
			this.elements.wrap.find('.pp-video-image-overlay').off('click keyup');
			this.elements.wrap.on( 'click keyup', '.pp-video-image-overlay', function(e) {
				// Click or keyboard (enter or spacebar) input?
				if ( ! this._validClick(e) ) {
					return;
				}

				e.preventDefault();
				e.stopPropagation();

				var $item = $(e.target).parents('.pp-video-gallery-item');

				this.currentItem = $item;
				
				this.activeIndex = $item.index();

				if ( $item.find('.pp-video-lightbox-content').length > 0 ) {
					this._initLightbox($item);
				} else {
					this._inlinePlay($item);
				}
			}.bind( this ) );

			this._onHashChange();

			$(window).on( 'hashchange', this._onHashChange.bind( this ) );
		},

		_initLightbox: function($item) {
			$item.find('.pp-video-play-icon').attr( 'tabindex', '-1' );
			//$.fancybox.open($('<div class="'+wrapperClasses+'"></div>').html( $item.find('.pp-video-lightbox-content').html() ), options);
			$.fancybox.open( this.lightboxData, this._getLightboxOptions($item), this.activeIndex );

			$(document).on('keyup', function(e) {
				if ( e.keyCode === 27 ) {
					$.fancybox.close();
				}
			});
		},

		_onHashChange: function() {
			var hash = location.hash,
				hashFragments = hash.split( '-' );

			if ( hashFragments.length < 3 ) {
				return;
			}
			if ( '#video' !== hashFragments[0] ) {
				return;
			}
			if ( this.id !== hashFragments[1] ) {
				return;
			}

			var $item = $('.fl-node-' + this.id).find( '.pp-video-gallery-item[data-index="' + hashFragments[2] + '"]' );

			if ( $item.length === 0 ) {
				return;
			}

			if ( $item.find('.pp-video-lightbox-content').length === 0 ) {
				return;
			}

			$.fancybox.open( this.lightboxData, this._getLightboxOptions($item), hashFragments[2] );
		},

		_getLightboxOptions: function($item) {
			var self = this;
			var id = this.id;
			var options = {
				modal			: false,
				baseClass		: 'pp-video-gallery-fancybox fancybox-' + id,
				buttons			: [
					'close'
				],
				wheel			: false,
				defaultType		: 'html',
				animationEffect	: 'fade',
				touch			: false,
				afterLoad		: function(current, previous) {
					$('.fancybox-' + id).find('.fancybox-bg').addClass('fancybox-' + id + '-overlay');
					setTimeout(function() {
						$('.fancybox-' + id).trigger('focus');
					}, 1200);

					$('.fancybox-' + id).on('click', '.fancybox-content', function(e) {
						if ( $(this).hasClass( 'fancybox-content' ) ) {
							$.fancybox.close();
						}
					});
				},
				afterClose		: function() {
					if ( $item.find('.pp-video-play-icon').length > 0 ) {
						$item.find('.pp-video-play-icon').attr( 'tabindex', '0' );
						$item.find('.pp-video-play-icon')[0].focus();
					}
					//self._initVideo();
				},
				iframe: {
					preload: false
				},
				keys : {
					close  : [27],
				}
			};

			return options;
		},

		_inlinePlay: function($item) {
			// Pause/reset all other videos in the grid/carousel
			$item.siblings().each( function() {
				var $iframe = $(this).find( '.pp-video-iframe' );
				if ( $iframe.length ) {
					$iframe.attr( 'data-src', $iframe.attr('src') ).removeAttr( 'src' );
				}
				if ( $(this).find( '.pp-video-player' ).length > 0 ) {
					$(this).find( '.pp-video-player' )[0].pause();
	
					return;
				}
				$(this).parent().find('.pp-video-image-overlay').show();
			} );

			// Begin play video.
			$item.find( '.pp-video-image-overlay' ).fadeOut(800);

			if ( $item.find( '.pp-video-player' ).length > 0 ) {
				$item.find( '.pp-video-player' )[0].play();

				return;
			}

			if ( $item.find( '.pp-video-iframe' ).length > 0 ) {
				var lazyLoad = $item.find( '.pp-video-iframe' ).data( 'src' );

				if ( lazyLoad ) {
					$item.find( '.pp-video-iframe' ).attr( 'src', lazyLoad );
				}

				var iframeSrc = $item.find( '.pp-video-iframe' )[0].src;
				iframeSrc = iframeSrc.replace('autoplay=0', '');

				var src = iframeSrc.split('#');
				iframeSrc = src[0] + '&autoplay=1';

				if ( 'undefined' !== typeof src[1] ) {
					iframeSrc += '#' + src[1];
				}
				$item.find( '.pp-video-iframe' )[0].src = iframeSrc;
			}
		},

		_initFilters: function() {
			var filterData = {
				itemSelector: '.pp-video-gallery-item',
				percentPosition: true,
				transitionDuration: '0.4s',
				isOriginLeft: ! $('body').hasClass( 'rtl' ),
			};
			filterData = $.extend( {}, filterData, {
				layoutMode: 'fitRows',
				fitRows: {
					gutter: '.pp-video-gallery--spacer'
				},
			} );

			var filters = this.filtersObj = this.elements.wrap.isotope( filterData );
			var filtersWrap = this.elements.node.find( '.pp-video-gallery-filters' );
			var self = this;

			this._filterByHash();

			$(window).on('hashchange', this._filterByHash.bind( this ) );

			this.elements.wrap.imagesLoaded( function() {
				filtersWrap.on('click', '.pp-video-gallery-filter', function() {
					var filterVal = $(this).attr('data-filter');
                    filters.isotope({ filter: filterVal }, function() {
						$(document).trigger( 'pp_video_gallery_filter_complete', [self] );
					});

					filtersWrap.find('.pp-video-gallery-filter').removeClass('pp-filter--active');
					$(this).addClass('pp-filter--active');
				});
			}.bind( this ) );
		},

		_filterByHash: function() {
			var filtersWrap = this.elements.node.find( '.pp-video-gallery-filters' );

			if ( '' !== location.hash ) {
				var filter = location.hash.replace('#', '.pp-filter-');
				var filterItem = filtersWrap.find( 'li[data-filter="' + filter + '"]' );
				if ( filterItem.length > 0 ) {
					setTimeout(function() {
						filterItem.trigger( 'click' );
					}, 100);
				}
			}
		},

		_initResponsiveFilters: function() {
			$( this.nodeClass + ' .pp-video-gallery-filters .pp-video-gallery-filter' ).removeAttr('style');
			$('body').undelegate(this.nodeClass + ' .pp-video-gallery-filters .pp-filter--active', 'click', this._bindResponsiveFilters );
			
			if ( window.innerWidth <= 768 ) {
				$('body').on('click', this.nodeClass + ' .pp-video-gallery-filters .pp-filter--active', this._bindResponsiveFilters );
			}
		},

		_bindResponsiveFilters: function(e) {
			setTimeout(function() {
				if ( $(e.target).siblings().is(':visible') ) {
					$(e.target).siblings().hide();
				} else {
					$(e.target).siblings().show();
				}
			}, 250);
		},

		_initPagination: function() {
			if ( ! this.elements.pagination.length ) {
				return;
			}

			var $items   = $( $(this.nodeClass).find( 'script.pp-video-gallery-pagination-items' ).html() );
			var perPage  = this.elements.pagination.data( 'per-page' );
			var offset   = 0;
			var rendered = perPage;
			var self     = this;

			this.elements.pagination.find('a').on( 'click', function(e) {
 				e.preventDefault();

				for( var i = offset; i < rendered; i++ ) {
					self.elements.wrap.isotope( 'insert', $items[ i ] );
					offset += 1;
				}

				rendered += offset;

				if ( $items.length < offset ) {
					offset = $items.length;
				}

				if ( $items.length == offset ) {
					self.elements.pagination.remove();
				}
			} );
		},

		_stopIframeAutoplay: function($item) {
			if ( $item.find( '.pp-video-iframe' ).length > 0 ) {
				var src = $item.find( '.pp-video-iframe' )[0].src;
				if ( 'undefined' !== typeof src && '' !== src ) {
					src = src.replace('&autoplay=1', '');
					src = src.replace('autoplay=1', '');

					$item.find( '.pp-video-iframe' )[0].src = src;
				}
			}
		},

		_getItems: function() {
			this.elements.items = $(this.itemClass);
			return this.elements.items;
		},

		_isSlideshow: function() {
			return false;
		},

		_getEffect: function() {
			return this.carousel.effect;
		},

        _getSlidesCount: function () {
            return this.elements.swiperSlide.length;
        },

        _getInitialSlide: function () {
            return this.carousel.initialSlide;
        },

        _getSpaceBetween: function () {
            var space = this.carousel.spaceBetween.desktop,
                space = parseInt(space);

            if ( isNaN( space ) ) {
                space = 20;
            }

            return space;
        },

		_getSpaceBetweenLarge: function () {
            var space = this.carousel.spaceBetween.large,
                space = parseInt(space);

            if ( isNaN( space ) ) {
                space = this._getSpaceBetween();
            }

            return space;
        },

        _getSpaceBetweenTablet: function () {
            var space = this.carousel.spaceBetween.tablet,
                space = parseInt(space);

            if ( isNaN( space ) ) {
                space = this._getSpaceBetweenLarge();
            }

            return space;
        },

        _getSpaceBetweenMobile: function () {
            var space = this.carousel.spaceBetween.mobile,
                space = parseInt(space);

            if ( isNaN( space ) ) {
                space = this._getSpaceBetweenTablet();
            }

            return space;
        },

        _getSlidesPerView: function () {
			if ( this._isSlideshow() ) {
				return 1;
			}

			var slidesPerView = this.carousel.slidesPerView.desktop;

            return Math.min(this._getSlidesCount(), +slidesPerView);
        },

		_getSlidesPerViewLarge: function () {
			if ( this._isSlideshow() ) {
				return 1;
			}

			var slidesPerView = this.carousel.slidesPerView.large;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getSlidesPerView();
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getSlidesPerViewTablet: function () {
			if ( this._isSlideshow() ) {
				return 1;
			}

			var slidesPerView = this.carousel.slidesPerView.tablet;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getSlidesPerViewLarge();
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getSlidesPerViewMobile: function () {
			if ( this._isSlideshow() ) {
				return 1;
			}

			var slidesPerView = this.carousel.slidesPerView.mobile;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getSlidesPerViewTablet();
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
		},
		
		_getSlidesToScroll: function(device) {
			if ( ! this._isSlideshow() && 'slide' === this._getEffect() ) {
				var slides = this.carousel.slidesToScroll[device];

				return Math.min( this._getSlidesCount(), +slides || 1 );
			}

			return 1;
		},

		_getSlidesToScrollDesktop: function() {
			return this._getSlidesToScroll( 'desktop' );
		},

		_getSlidesToScrollLarge: function() {
			return this._getSlidesToScroll( 'large' );
		},

		_getSlidesToScrollTablet: function() {
			return this._getSlidesToScroll( 'tablet' );
		},

		_getSlidesToScrollMobile: function() {
			return this._getSlidesToScroll( 'mobile' );
		},

        _getSwiperOptions: function () {
			var minBreakpoint = 0;
            var large_breakpoint      = this.carousel.breakpoint.large,
            	medium_breakpoint     = this.carousel.breakpoint.medium,
				responsive_breakpoint = this.carousel.breakpoint.responsive;

            var options = {
				navigation: {
					prevEl: this.nodeClass + ' .pp-video-carousel-nav-prev',
					nextEl: this.nodeClass + ' .pp-video-carousel-nav-next'
				},
				pagination: {
					el: this.nodeClass + ' .swiper-pagination',
					type: this.carousel.pagination,
					clickable: true
				},
				grabCursor: true,
                effect: this._getEffect(),
                initialSlide: this._getInitialSlide(),
                slidesPerView: this._getSlidesPerView(),
                slidesPerGroup: this._getSlidesToScrollDesktop(),
                spaceBetween: this._getSpaceBetween(),
                loop: this.carousel.loop,
                loopedSlides: this._getSlidesCount(),
				centeredSlides: this.carousel.centeredSlides,
				speed: this.carousel.speed,
                breakpoints: {}
			};

			if ( ! this.isBuilderActive && this.carousel.autoplay ) {
				options.autoplay = this.carousel.autoplay;
			}

			// Small device
			options.breakpoints[minBreakpoint] = {
				slidesPerView: this._getSlidesPerViewMobile(),
				slidesPerGroup: this._getSlidesToScrollMobile(),
				spaceBetween: this._getSpaceBetweenMobile()
			};
			// Medium device
			options.breakpoints[responsive_breakpoint + 1] = {
				slidesPerView: this._getSlidesPerViewTablet(),
				slidesPerGroup: this._getSlidesToScrollTablet(),
				spaceBetween: this._getSpaceBetweenTablet()
			};
			// Large device
			options.breakpoints[medium_breakpoint + 1] = {
				slidesPerView: this._getSlidesPerViewLarge(),
				slidesPerGroup: this._getSlidesToScrollLarge(),
				spaceBetween: this._getSpaceBetweenLarge()
			};
			// Desktop device
			options.breakpoints[large_breakpoint + 1] = {
				slidesPerView: this._getSlidesPerView(),
				slidesPerGroup: this._getSlidesToScroll(),
				spaceBetween: this._getSpaceBetween()
			};

			options.on = {
				init: this._initVideo.bind(this),
				//imagesReady: this._onResize.bind(this),
				//resize: this._onResize.bind(this)
			};

            return options;
		},
		
		_onResize: function() {
			var element = $(this.elements.swiper);
			element.css( 'height', 'auto' );

			var height = element.height() + element.find('.swiper-pagination').height();
			element.height( (height + 22) );
		},

		_validClick: function(e) {
			return (e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined) ? true : false;
		}
	};

})(jQuery);