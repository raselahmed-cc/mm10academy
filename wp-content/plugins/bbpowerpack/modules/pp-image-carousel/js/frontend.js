;(function ($) {

    PPImageCarousel = function (settings) {
        this.id = settings.id;
        this.nodeClass = '.fl-node-' + settings.id;
		this.wrapperClass = this.nodeClass + ' .pp-image-carousel';
		this.elements = '';
        this.slidesPerView = settings.slidesPerView;
        this.slidesRows = settings.slidesRows;
        this.slidesToScroll = settings.slidesToScroll;
		this.settings = settings;
		this.swipers = {};

        if (this._isSlideshow()) {
            this.slidesPerView = settings.slideshow_slidesPerView;
		}
		
		if ( typeof Swiper === 'undefined' ) {
			$(window).on('load', function() {
				if ( typeof Swiper === 'undefined' ) {
					return;
				} else {
					this._init();
				}
			}.bind( this ) );
		} else {
			this._init();
		}
    };

    PPImageCarousel.prototype = {
        id: '',
        nodeClass: '',
        wrapperClass: '',
        elements: '',
        slidesPerView: {},
        slidesRows: {},
        slidesToScroll: {},
        settings: {},
        swipers: {},

        _init: function () {
            this.elements = {
                mainSwiper: this.nodeClass + ' .pp-image-carousel'
            };

            this.elements.swiperSlide = $(this.elements.mainSwiper).find('.swiper-slide:not(.swiper-slide-duplicate)');
            this.elements.thumbSwiper = this.nodeClass + ' .pp-thumbnails-swiper';

            if (1 >= this._getSlidesCount()) {
                return;
            }

            var swiperOptions = this._getSwiperOptions();

			$(this.nodeClass).trigger( 'pp_carousel_before_init', [swiperOptions] );

            this.swipers.main = new Swiper(this.elements.mainSwiper, swiperOptions.main);

			// Manual pause the autoplay on mouse hover.
			if ( this.settings.pause_on_interaction && this.settings.autoplay_delay !== false ) {
				var self = this;
				$( this.swipers.main.el ).on( 'mouseenter', function() {
					self.swipers.main.autoplay.stop();
				} ).on( 'mouseleave', function() {
					self.swipers.main.autoplay.start();
				} );
			}

            if (this._isSlideshow() && 1 < this._getSlidesCount()) {
                this.swipers.main.controller.control = this.swipers.thumbs = new Swiper(this.elements.thumbSwiper, swiperOptions.thumbs);
                this.swipers.thumbs.controller.control = this.swipers.main;
            }
		},

		_getEffect: function() {
			return this.settings.effect;
		},

        _getSlidesCount: function () {
            return this.elements.swiperSlide.length;
        },

        _getInitialSlide: function () {
            return this.settings.initialSlide;
        },

        _getSpaceBetween: function () {
            var space = this.settings.spaceBetween.desktop,
                space = parseInt(space);

            if ( isNaN( space ) ) {
                space = 20;
            }

            return space;
        },

		_getSpaceBetweenLarge: function () {
            var space = this.settings.spaceBetween.large,
                space = parseInt(space);

            if ( isNaN(space) ) {
                space = this._getSpaceBetween();
            }

            return space;
        },

        _getSpaceBetweenTablet: function () {
            var space = this.settings.spaceBetween.tablet,
                space = parseInt(space);

            if ( isNaN(space) ) {
                space = this._getSpaceBetweenLarge();
            }

            return space;
        },

        _getSpaceBetweenMobile: function () {
            var space = this.settings.spaceBetween.mobile,
                space = parseInt(space);

            if ( isNaN(space) ) {
                space = this._getSpaceBetweenTablet();
            }

            return space;
        },

        _getSlidesPerView: function () {
			if ( this._isSlideshow() ) {
				return 1;
			}

			var slidesPerView = this.slidesPerView.desktop;

            return Math.min(this._getSlidesCount(), +slidesPerView);
        },

		_getSlidesPerViewLarge: function () {
			if ( this._isSlideshow() ) {
				return 1;
			}

			var slidesPerView = this.slidesPerView.large;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getSlidesPerView();
			}

			if (!slidesPerView && 'coverflow' === this.settings.type) {
				return Math.min(this._getSlidesCount(), 3);
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getSlidesPerViewTablet: function () {
			if ( this._isSlideshow() ) {
				return 1;
			}

			var slidesPerView = this.slidesPerView.tablet;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getSlidesPerViewLarge();
			}

			if (!slidesPerView && 'coverflow' === this.settings.type) {
				return Math.min(this._getSlidesCount(), 3);
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getSlidesPerViewMobile: function () {
			if ( this._isSlideshow() ) {
				return 1;
			}

			var slidesPerView = this.slidesPerView.mobile;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getSlidesPerViewTablet();
			}

			if (!slidesPerView && 'coverflow' === this.settings.type) {
				return Math.min(this._getSlidesCount(), 3);
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
		},

		_getThumbsSlidesPerView: function () {
			var slidesPerView = this.slidesPerView.desktop;

            return Math.min(this._getSlidesCount(), +slidesPerView);
        },

		_getThumbsSlidesPerViewLarge: function () {
			var slidesPerView = this.slidesPerView.large;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getThumbsSlidesPerView();
			}

			if (!slidesPerView && 'coverflow' === this.settings.type) {
				return Math.min(this._getSlidesCount(), 3);
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getThumbsSlidesPerViewTablet: function () {
			var slidesPerView = this.slidesPerView.tablet;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getThumbsSlidesPerViewLarge();
			}

			if (!slidesPerView && 'coverflow' === this.settings.type) {
				return Math.min(this._getSlidesCount(), 3);
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
        },

        _getThumbsSlidesPerViewMobile: function () {
			var slidesPerView = this.slidesPerView.mobile;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getSlidesPerViewTablet();
			}

			if (!slidesPerView && 'coverflow' === this.settings.type) {
				return Math.min(this._getSlidesCount(), 3);
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
		},

		_getSlidesRows: function () {
			return this.slidesRows.desktop || 1;
        },

		_getSlidesRowsLarge: function () {
			return this.slidesRows.large || this._getSlidesRows();
        },

        _getSlidesRowsTablet: function () {
			return this.slidesRows.tablet || this._getSlidesRowsLarge();
        },

        _getSlidesRowsMobile: function () {
			return this.slidesRows.mobile || this._getSlidesRowsTablet();
		},
		
		_getSlidesToScroll: function(device) {
			if ( ! this._isSlideshow() && 'slide' === this._getEffect() ) {
				var slides = this.slidesToScroll[device];

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
			// Since in Swiper >5.0 the breakpoint system is min-width based.
			var minBreakpoint = 0;
            var large_breakpoint = this.settings.breakpoint.large,
            	medium_breakpoint = this.settings.breakpoint.medium,
				responsive_breakpoint = this.settings.breakpoint.responsive;
				nodeClass = this.nodeClass;

			var pagination = $(nodeClass).find('.swiper-pagination');
			var isAutoSlides = $(nodeClass).hasClass('pp-slides-auto');
			var captions = pagination.data( 'captions' );

			var addAriaLabels = function() {
				var count = 0;
				setTimeout(function() {
					pagination.find( '.swiper-pagination-bullet' ).each(function() {
						var label = captions[ count ];
						if ( '' !== label ) {
							$(this).attr( 'aria-label', label );
						}
						if ( $(this).hasClass( 'swiper-pagination-bullet-active' ) ) {
							$(this).attr( 'aria-current', 'true' );
						} else {
							$(this).attr( 'aria-current', 'false' );
						}
						count++;
					});
				}, 250);
			};

			var addSlideCount = function( $scope ) {
				var $slides = $($scope.el).find('.swiper-slide:not(.swiper-slide-duplicate)');
				var total = $slides.length;
				$($scope.slides).each(function() {
					var index = $(this).data('swiper-slide-index');
					var current = index + 1;
					if ( $(this).find('.pp-caption').find( '.pp-slides-count' ).length == 0 ) {
						$(this).find('.pp-caption').append( '<span class="pp-slides-count" aria-hidden="true">' + current + ' / ' + total + '</span>' );
					}
				});
			};

            var options = {
				direction: this.settings.direction,
				keyboard: {
					enabled: true,
					onlyInViewport: true,
				},
				navigation: {
					prevEl: nodeClass + ' .swiper-button-prev',
					nextEl: nodeClass + ' .swiper-button-next'
				},
				pagination: {
					el: nodeClass + ' .swiper-pagination',
					type: this.settings.pagination,
					clickable: true,
					renderBullet: function( index, className ) {
						var pagination = $(nodeClass).find('.swiper-pagination');
						var captions = pagination.data( 'captions' );

						return '<button class="' + className + '" aria-label="' + captions[index] + '" tabindex="0" role="button"></button>';
					},
					dynamicBullets: this.settings.dynamic_bullets
				},
				a11y: {
					enabled: false
				},
				grabCursor: true,
                effect: this._getEffect(),
                initialSlide: this._getInitialSlide(),
                slidesPerView: this._getSlidesPerView(),
                slidesPerGroup: this._getSlidesToScrollDesktop(),
                spaceBetween: this._getSpaceBetween(),
                loop: 'undefined' !== typeof this.settings.loop ? this.settings.loop : true,
                speed: this.settings.speed,
				breakpoints: {},
				on: {
					init: addAriaLabels,
					slideChange: addAriaLabels,
					afterInit: addSlideCount,
					update: addSlideCount
				}
			};

			if ( this.settings.mousewheel ) {
				options.mousewheel = this.settings.mousewheel;
			}

			if ( window.innerWidth > large_breakpoint && this._getSlidesCount() <= this._getSlidesPerView() && ! isAutoSlides ) {
				options.pagination = false;
			}
			if ( window.innerWidth <= large_breakpoint && window.innerWidth > medium_breakpoint && this._getSlidesCount() <= this._getSlidesPerViewLarge() && ! isAutoSlides ) {
				options.pagination = false;
			}
			if ( window.innerWidth <= medium_breakpoint && window.innerWidth > responsive_breakpoint && this._getSlidesCount() <= this._getSlidesPerViewTablet() && ! isAutoSlides ) {
				options.pagination = false;
			}
			if ( window.innerWidth >= responsive_breakpoint && this._getSlidesCount() <= this._getSlidesPerViewMobile() && ! isAutoSlides ) {
				options.pagination = false;
			}

			if ( ! this.settings.isBuilderActive && this.settings.lazy_load ) {
				options.lazy = {
					loadPrevNext: true,
        			loadPrevNextAmount: 1
				}
			}

			if ( this._isSlideshow() ) {
				options.loopedSlides = this._getSlidesCount();

				if ( 'fade' === this._getEffect() ) {
					options.fadeEffect = {
						crossFade: true
					};
				}
			}
			
			if ( ! this.settings.isBuilderActive && this.settings.autoplay_delay !== false ) {
				options.autoplay = {
					delay: this.settings.autoplay_delay,
					reverseDirection: this.settings.reverseDirection,
					disableOnInteraction: this.settings.pause_on_interaction,
					stopOnLastSlide: 'undefined' !== typeof this.settings.stopOnLastSlide ? this.settings.stopOnLastSlide : false,
				};
			}
			
			if ( ! this._isSlideshow() ) {
				if ( 'coverflow' === this.settings.type || 'slide' === this._getEffect() ) {
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
						slidesPerGroup: this._getSlidesToScrollDesktop(),
						spaceBetween: this._getSpaceBetween()
					};
				}

				if ( 'horizontal' === this.settings.direction && ( 'coverflow' === this.settings.type || 'slide' === this._getEffect() ) ) {
					if ( this._getSlidesRows() > 1 ) {
						options.grid = {
							enabled: true,
							rows: this._getSlidesRows()
						};
					}
					// Small device
					if ( this._getSlidesRowsMobile() >= 1 ) {
						options.breakpoints[minBreakpoint].grid = {
							enabled: true,
							rows: this._getSlidesRowsMobile()
						};
					}
					// Medium device
					if ( this._getSlidesRowsTablet() >= 1 ) {
						options.breakpoints[responsive_breakpoint + 1].grid = {
							enabled: true,
							rows: this._getSlidesRowsTablet()
						};
					}
					// Large device
					if ( this._getSlidesRowsLarge() >= 1 ) {
						options.breakpoints[medium_breakpoint + 1].grid = {
							enabled: true,
							rows: this._getSlidesRowsLarge()
						};
					}
					// Desktop
					if ( this._getSlidesRows() >= 1 ) {
						options.breakpoints[large_breakpoint + 1].grid = {
							enabled: true,
							rows: this._getSlidesRows()
						};
					}
				}
			}

			if ( 'carousel' === this.settings.type ) {
				options.centeredSlides = this.settings.centered_slides;

				// override slidesPerView and slidesPerGroup if the class pp-slides-auto is set
				if ( $(this.nodeClass).hasClass('pp-slides-auto') ) {
					options.slidesPerView = 'auto';
					options.slidesPerGroup = 1;

					// Small device
					options.breakpoints[minBreakpoint] = {
						slidesPerView: options.slidesPerView,
						slidesPerGroup: options.slidesPerGroup
					};
					// Medium device
					options.breakpoints[responsive_breakpoint + 1] = {
						slidesPerView: options.slidesPerView,
						slidesPerGroup: options.slidesPerGroup
					};
					// Large device
					options.breakpoints[medium_breakpoint + 1] = {
						slidesPerView: options.slidesPerView,
						slidesPerGroup: options.slidesPerGroup
					};
					// Desktop device
					options.breakpoints[large_breakpoint + 1] = {
						slidesPerView: options.slidesPerView,
						slidesPerGroup: options.slidesPerGroup
					};
				}
			}

			if ( 'coverflow' === this.settings.type ) {
                options.effect = 'coverflow';
				options.centeredSlides = true;

				if ( window.innerWidth > large_breakpoint && this._getSlidesPerView() <= 2 ) {
					options.centeredSlides = false;
				}
				if ( window.innerWidth <= large_breakpoint && window.innerWidth > medium_breakpoint && this._getSlidesPerViewLarge() <= 2 ) {
					options.centeredSlides = false;
				}
				if ( window.innerWidth <= medium_breakpoint && window.innerWidth > responsive_breakpoint && this._getSlidesPerViewTablet() <= 2 ) {
					options.centeredSlides = false;
				}
				if ( window.innerWidth >= responsive_breakpoint && this._getSlidesPerViewMobile() <= 2 ) {
					options.centeredSlides = false;
				}
            }

            if (this._isSlideshow()) {
                options.slidesPerView = 1;

                delete options.pagination;
                delete options.breakpoints;
            }

            var thumbsSliderOptions = {
                slidesPerView: this._getThumbsSlidesPerView(),
                initialSlide: this._getInitialSlide(),
                centeredSlides: true,
                slideToClickedSlide: true,
                spaceBetween: this._getSpaceBetween(),
                loop: 'undefined' !== typeof this.settings.loop ? this.settings.loop : true,
                loopedSlides: this._getSlidesCount(),
                speed: this.settings.speed,
                onSlideChangeEnd: function (swiper) {
                    swiper.fixLoop();
                },
                breakpoints: {}
            };

			// Small device - when window width is > 0px && < responsive_breakpoint + 1
			thumbsSliderOptions.breakpoints[minBreakpoint] = {
                slidesPerView: this._getThumbsSlidesPerViewMobile(),
                spaceBetween: this._getSpaceBetweenMobile()
            };

			// Medium device - when window width is >= responsive_breakpoint + 1 && < medium_breakpoint + 1
			thumbsSliderOptions.breakpoints[responsive_breakpoint + 1] = {
                slidesPerView: this._getThumbsSlidesPerViewTablet(),
                spaceBetween: this._getSpaceBetweenTablet()
            };

			// Large device - when window width is >= medium_breakpoint + 1 && < large_breakpoint + 1
			thumbsSliderOptions.breakpoints[medium_breakpoint + 1] = {
                slidesPerView: this._getThumbsSlidesPerViewLarge(),
                spaceBetween: this._getSpaceBetweenLarge()
            };

			// Desktop - when window width is >= large_breakpoint + 1
			thumbsSliderOptions.breakpoints[large_breakpoint + 1] = {
                slidesPerView: this._getThumbsSlidesPerView(),
                spaceBetween: this._getSpaceBetween()
            };

			if ( ! this.settings.isBuilderActive && this.settings.lazy_load ) {
				thumbsSliderOptions.lazy = {
					loadPrevNext: true,
        			loadPrevNextAmount: 1
				}
			}

            return {
                main: options,
                thumbs: thumbsSliderOptions
            };
        },

        _isSlideshow: function () {
            return 'slideshow' === this.settings.type;
        },
    };

})(jQuery);