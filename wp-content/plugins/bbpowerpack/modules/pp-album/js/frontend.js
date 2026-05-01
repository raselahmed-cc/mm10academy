;(function($) {

	PPAlbum = function (settings) {
		this.id 				= settings.id;
		this.node 				= $('.fl-node-' + this.id);
		this.nodeCount 			= this.node.length;
		this.lightboxLoop		= settings.lightboxLoop;
		this.lightboxArrows		= settings.lightboxArrows;
		this.slidesCounter		= settings.slidesCounter;
		this.keyboardNav		= settings.keyboardNav;
		this.toolbar			= settings.toolbar;
		this.toolbarButtons		= settings.toolbarButtons;
		this.thumbsAutoStart	= 'yes' == settings.thumbsAutoStart ? true : false;
		this.thumbsPosition		= settings.thumbsPosition;
		this.lightboxAnimation	= settings.lightboxAnimation;
		this.transitionEffect	= settings.transitionEffect;
		this.transitionDuration = settings.transitionDuration;
		this.lightboxBgColor	= settings.lightboxBgColor;
		this.lightboxbgOpacity	= settings.lightboxbgOpacity;
		this.thumbsBgColor		= settings.thumbsBgColor;
		this.album 				= this.node.find('.pp-album');
		this.fancyboxThumbs 	= this.album.data('fancybox-class');
		this.fancyboxAxis 		= this.album.data('fancybox-axis');
		this.lightboxTrigger 	= this.node.find('.pp--album-' + this.id );
		this.lightboxSelector 	= this.node.find('.pp-album-' + this.id );

		if ( window.innerWidth <= 768 ) {
			// Show thumbs at bottom on small screen.
			this.fancyboxAxis = 'x';
		}

		this._init();
	};

	PPAlbum.prototype = {
		_init: function () {
			var self = this;

			self.lightboxTrigger.on('click', function() {
				// Re-assign all properties if the instance is in a post loop.
				if ( self.nodeCount > 1 ) {
					self.node             = $(this).closest( '.fl-module' );
					self.lightboxTrigger  = self.node.find('.pp--album-' + self.id );
					self.lightboxSelector = self.node.find('.pp-album-' + self.id );
				}

				self.lightboxSelector.fancybox({
					loop: 				self.lightboxLoop,
					arrows:				self.lightboxArrows,
					infobar: 			self.slidesCounter,
					keyboard: 			self.keyboardNav,
					toolbar: 			self.toolbar,
					buttons: 			self.toolbarButtons.split(","),
					animationEffect:	self.lightboxAnimation,
					transitionEffect:	self.transitionEffect,
					transitionDuration: self.transitionDuration,
					baseClass: 			self.fancyboxThumbs + ' ' + 'pp-fancybox-' + self.id,
					thumbs: {
						autoStart: 		self.thumbsAutoStart,
						axis: 			self.fancyboxAxis
					},
				});

				self.node.find('.pp-album-1.pp-album-' + self.id).trigger('click');

				// if ( $('body').find('.fancybox-container').hasClass( 'pp-fancybox-' + self.id ) ) {
				// 	return;
				// } else {
				// 	$('body').find('.fancybox-container').addClass( 'pp-fancybox-' + self.id );
				// }

			});
		}

	};

})(jQuery);
