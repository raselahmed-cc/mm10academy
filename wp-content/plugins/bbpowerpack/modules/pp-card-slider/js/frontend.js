;(function($) {
	PPCardSlider = function( settings ) {
		this.id = settings.id;
		this.nodeClass = '.fl-node-' + this.id;
		this.wrapperClass = this.nodeClass + ' .pp-card-slider';
		this.slider = {};
		this.responsive = settings.responsive;

		var self = this;

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
			pagination: settings.pagination,
			keyboard: settings.keyboard,
			on: {
				init: function() {
					$(self.wrapperClass).addClass( 'pp-card-slider-initialized' );
				}
			}
		};

		this.init();
	};

	PPCardSlider.prototype = {
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
			if ( window.innerWidth < this.responsive ) {
				//this.sliderOptions.direction = 'vertical';
			}

			this.slider = new Swiper( this.wrapperClass, this.sliderOptions );
		},

		setHeight: function() {
			var height = 0;
			$( this.wrapperClass + ' .pp-card-slider-item' ).each(function () {
				if ( $(this).height() > height) {
					height = $(this).height();
				}
			});
			$( this.wrapperClass ).css( 'height', (height + 70) + 'px' );
		},
	};
})(jQuery);