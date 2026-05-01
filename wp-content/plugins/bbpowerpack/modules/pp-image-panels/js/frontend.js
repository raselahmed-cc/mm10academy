;(function($) {
	PPImagePanels = function(settings) {
		this.id = settings.id;
		this.settings = settings;
		this.$node = $('.fl-node-' + this.id);
		this.$panels = this.$node.find('.pp-image-panels-wrap .pp-panel');

		if ( this.$panels.length <= 1 ) {
			return;
		}

		this.init();
		this.initLightbox();
		$(window).on('resize', this.init.bind(this));
	};

	PPImagePanels.prototype = {
		init: function() {
			if ( window.innerWidth < 768 ) {
				return;
			}

			var self = this;
			var expandWidth = this.getExpandWidth();
			var expandPanel = this.getExpandPanelIndex();

			// Set the width of each panel based on the number of panels and expand width.
			this.$panels.css('width', (100 / this.$panels.length) + '%');

			this.$panels.on( 'mouseenter', function() {
				$(this)
					.css('width', (expandWidth) + '%')
					.removeClass('pp-panel-inactive')
					.addClass('pp-panel-active');
				$(this).siblings()
					.css('width', (100 - expandWidth) / (self.$panels.length - 1) + '%')
					.removeClass('pp-panel-active')
					.addClass('pp-panel-inactive');
			} ).on( 'mouseleave', function() {
				$(this)
					.css('width', (100 / self.$panels.length) + '%')
					.removeClass('pp-panel-active')
					.addClass('pp-panel-inactive');
				$(this).siblings()
					.css('width', (100 / self.$panels.length) + '%')
					.addClass('pp-panel-inactive');
			} );

			if ( '' !== expandPanel ) {
				// Expand the custom panel on page load.
				var $panel = this.$node.find('.pp-image-panels-wrap .pp-panel').eq(expandPanel);
				$panel
					.removeClass('pp-panel-inactive')
					.addClass('pp-panel-active')
					.css('width', (expandWidth) + '%');
				$panel.siblings()
					.css('width', (100 - expandWidth) / (self.$panels.length - 1) + '%')
					.addClass('pp-panel-inactive');

				// Expand custom panel again when mouse leave the wrapper.
				this.$node.find('.pp-image-panels-wrap').on( 'mouseleave', function() {
					$panel
						.removeClass('pp-panel-inactive')
						.addClass('pp-panel-active')
						.css('width', (expandWidth) + '%');
					$panel.siblings()
						.css('width', (100 - expandWidth) / (self.$panels.length - 1) + '%')
						.removeClass('pp-panel-active')
						.addClass('pp-panel-inactive');
				});
			}
		},

		initLightbox: function() {
			if ( this.$node.find('a.pp-panel-has-lightbox').length > 0 ) {
				this.$node.find('a.pp-panel-has-lightbox').magnificPopup({
					type: 'image',
					closeOnContentClick: true,
					closeBtnInside: false
				});
			}
		},

		getExpandWidth: function() {
			var expandWidth = this.settings.expandWidth || 0;
			expandWidth  = ( expandWidth > 100 ) ? 100 : expandWidth;
			if ( expandWidth === 0 && 2 === this.$panels.length ) {
				expandWidth = 70;
			} else if ( expandWidth === 0 && this.$panels.length > 2 ) {
				expandWidth = 40;
			}
			return expandWidth;
		},

		getExpandPanelIndex: function() {
			var expandPanel = ( this.settings.expandPanel && this.settings.expandPanel > 0 ) ? this.settings.expandPanel - 1 : '';
			if ( '' !== expandPanel && ( expandPanel < 0 || expandPanel >= this.$panels.length ) ) {
				expandPanel = 0;
			}
			return expandPanel;
		}
	};
})(jQuery);