;(function ($) {

    PPReviewsCarousel = function (settings) {
        this.id = settings.id;
        this.nodeClass = '.fl-node-' + settings.id;
		this.wrapperClass = this.nodeClass + ' .pp-reviews-swiper';
		this.elements = '';
        this.slidesPerView = settings.slidesPerView;
        this.slidesToScroll = settings.slidesToScroll;
		this.settings = settings;
		this.swipers = {};

		if ( typeof Swiper === 'undefined' ) {
			$(window).on( 'load', function() {
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

	PPReviewsCarousel.prototype = {
        id: '',
        nodeClass: '',
        wrapperClass: '',
        elements: '',
        slidesPerView: {},
        slidesToScroll: {},
        settings: {},
        swipers: {},

        _init: function () {
            this.elements = {
				mainSwiper: this.nodeClass + ' .pp-reviews-swiper'
            };

            this.elements.swiperSlide = $(this.elements.mainSwiper).find('.swiper-slide:not(.swiper-slide-duplicate)');

            if (1 >= this._getSlidesCount()) {
                return;
            }

            var swiperOptions = this._getSwiperOptions();

            this.swipers.main = new Swiper(this.elements.mainSwiper, swiperOptions.main);
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
			var slidesPerView = this.slidesPerView.desktop;

            return Math.min(this._getSlidesCount(), +slidesPerView);
        },

		_getSlidesPerViewLarge: function () {
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
			var slidesPerView = this.slidesPerView.mobile;

			if (slidesPerView === '' || slidesPerView === 0) {
				slidesPerView = this._getSlidesPerViewTablet();
			}

			if (!slidesPerView && 'coverflow' === this.settings.type) {
				return Math.min(this._getSlidesCount(), 3);
			}

			return Math.min(this._getSlidesCount(), +slidesPerView);
		},

		_getSlidesToScroll: function(device) {
			if ( 'slide' === this._getEffect() ) {
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
			var minBreakpoint = 0;
            var large_breakpoint = this.settings.breakpoint.large,
            	medium_breakpoint = this.settings.breakpoint.medium,
                responsive_breakpoint = this.settings.breakpoint.responsive;

            var options = {
				navigation: {
					prevEl: this.nodeClass + ' .pp-swiper-button-prev',
					nextEl: this.nodeClass + ' .pp-swiper-button-next'
				},
				pagination: {
					el: this.nodeClass + ' .swiper-pagination',
					type: this.settings.pagination,
					clickable: true
				},
				grabCursor: true,
                effect: this._getEffect(),
                initialSlide: this._getInitialSlide(),
                slidesPerView: this._getSlidesPerView(),
                slidesPerGroup: this._getSlidesToScrollDesktop(),
                spaceBetween: this._getSpaceBetween(),
                loop: this.settings.loop,
                loopedSlides: this._getSlidesCount(),
                speed: this.settings.speed,
				autoHeight: this.settings.autoHeight,
                breakpoints: {},
			};

			if ( window.innerWidth > large_breakpoint && this._getSlidesCount() <= this._getSlidesPerView() ) {
				options.pagination = false;
			}
			if ( window.innerWidth <= large_breakpoint && window.innerWidth > medium_breakpoint && this._getSlidesCount() <= this._getSlidesPerViewLarge() ) {
				options.pagination = false;
			}
			if ( window.innerWidth <= medium_breakpoint && window.innerWidth > responsive_breakpoint && this._getSlidesCount() <= this._getSlidesPerViewTablet() ) {
				options.pagination = false;
			}
			if ( window.innerWidth >= responsive_breakpoint && this._getSlidesCount() <= this._getSlidesPerViewMobile() ) {
				options.pagination = false;
			}

			if ( ! this.settings.isBuilderActive && this.settings.autoplay_speed !== false ) {
				options.autoplay = {
					delay: this.settings.autoplay_speed,
					disableOnInteraction: this.settings.pause_on_interaction
				};
			}
			if ('cube' !== this._getEffect()) {
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
			}

            if ('coverflow' === this.settings.type) {
                options.effect = 'coverflow';
            }

            return {
                main: options
            };
        },
    };

})(jQuery);