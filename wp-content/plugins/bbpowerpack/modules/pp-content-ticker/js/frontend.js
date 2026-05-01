;(function($) {
	PPContentTicker = function( settings ) {
		this.id = settings.id;
		this.nodeClass = '.fl-node-' + this.id;
		this.wrapperClass = this.nodeClass + ' .pp-content-ticker';
		this.slider = {};

		this.sliderOptions = {
			direction: settings.direction,
			loop: settings.loop,
			effect: settings.effect,
			speed: settings.speed,
			grabCursor: settings.grabCursor,
			slidesPerView: 1,
			autoHeight: false,
			autoplay: settings.autoplay,
			fadeEffect: {
				crossFade: true
			},
			navigation: {
				nextEl: this.nodeClass + ' .swiper-button-next',
				prevEl: this.nodeClass + ' .swiper-button-prev',
			}
		};

		if ( 'cube' === this.sliderOptions.effect ) {
			this.sliderOptions.direction = 'vertical';
			this.sliderOptions.cubeEffect = {
				shadow: false,
				slideShadows: false
			};
		}

		this.init();
	};

	PPContentTicker.prototype = {
		init: function() {
			if ( 'undefined' === typeof Swiper ) {
				return;
			}
			if ( this.slider instanceof Swiper ) {
				this.slider.destroy();
			}
			if ( 'vertical' === this.sliderOptions.direction ) {
				this.setHeight();
			}

			this.slider = new Swiper( this.wrapperClass, this.sliderOptions );
		},

		setHeight: function() {
			var height = 0;
			$( this.wrapperClass + ' .pp-content-ticker-item' ).each(function () {
				if ( $(this).height() > height) {
					height = $(this).height();
				}
			});
			$( this.wrapperClass ).css( 'height', height + 'px' );
		},
	}; 
})(jQuery);