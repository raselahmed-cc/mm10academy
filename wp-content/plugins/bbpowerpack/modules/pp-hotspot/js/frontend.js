; (function ($) {

	PPHotspot = function (settings) {
		this.id 				= settings.id;
		this.markerLength 		= settings.markerLength;
		this.node 				= $('.fl-node-' + this.id);
		this.content 			= this.node.find('.pp-hotspot-content');
		this.markerElem 		= this.node.find('.pp-hotspot-marker');
		this.customClass 		= 'pp-tooltip-wrap-' + this.id;
		this.tooltipEnable 		= settings.tooltipEnable;
		this.enableCloseIcon    = settings.enableCloseIcon;
		this.escToClose 		= settings.escToClose;
		this.clickToClose 		= settings.clickToClose;
		this.tooltipPosition 	= settings.tooltipPosition;
		this.tooltipTrigger 	= settings.tooltipTrigger;
		this.tooltipArrow 		= settings.tooltipArrow;
		this.tooltipDistance 	= settings.tooltipDistance !== '' ? parseInt(settings.tooltipDistance) : 30;
		this.tooltipAnimation	= settings.tooltipAnimation;
		this.tooltipWidth 		= settings.tooltipWidth !== '' ? parseInt(settings.tooltipWidth) : 300;
		this.animationDuration 	= settings.animationDuration !== '' ? parseInt(settings.animationDuration) : 350;
		this.tourEnable 		= settings.tourEnable;
		this.tourRepeat 		= settings.tourRepeat;
		this.tourAutoplay 		= settings.tourAutoplay;
		this.tooltipInterval 	= settings.tooltipInterval !== '' ? parseInt(settings.tooltipInterval) : 2000;
		this.launchTourOn 		= settings.launchTourOn;
		this.nonActiveMarker 	= settings.nonActiveMarker;
		this.viewport 			= settings.viewport;
		this.isBuilderActive 	= settings.isBuilderActive;
		this.overlayId 			= this.node.find('.pp-hotspot-overlay');
		this.buttonId 			= this.node.find('.pp-hotspot-overlay-button');
		this.tooltipZindex 		= settings.tooltipZindex !== '' ? parseInt(settings.tooltipZindex) : 99;
		this.adminTitlePreview 	= settings.adminTitlePreview;
		this.tooltipPreview 	= settings.tooltipPreview;
		this.hotspotInterval 	= [];
		this.hoverFlag 			= '';
		this.scrolling 			= false;
		this.hideMaxWidth       = settings.maxWidth;
		this.hideMinWidth       = settings.minWidth;
		this.breakpoints        = settings.breakpoints;
		this.tourVisibility     = settings.tourVisibility;
		this.winWidth           = $( window ).width();

		this._init();
	};

	PPHotspot.prototype = {
		id:	 				'',
		markerLength:	 	'',
		node:	 			'',
		content:	 		'',
		markerElem:	 		'',
		customClass:	 	'',
		tooltipEnable:	 	'',
		enableCloseIcon:    '',
		escToClose:         '',
		clickToClose:       '',
		tooltipPosition:	'',
		tooltipTrigger:	 	'',
		tooltipArrow:	 	'',
		tooltipDistance:	'',
		tooltipAnimation:	'',
		tooltipWidth:	 	'',
		animationDuration:	'',
		tourEnable:	 		'',
		tourRepeat:	 		'',
		tourAutoplay:	 	'',
		tooltipInterval:	'',
		launchTourOn:	 	'',
		nonActiveMarker:	'',
		viewport:	 		'',
		isBuilderActive:	'',
		overlayId:	 		'',
		buttonId:	 		'',
		tooltipZindex:	 	'',
		adminTitlePreview:	'',
		tooltipPreview:	 	'',
		hotspotInterval:	[],
		hoverFlag:	 		'',
		scrolling:	 		false,
		
		_init: function () {
			var self = this;

			clearInterval(this.hotspotInterval[this.id]);

			if ( $(window).width() < 768 || this._isTouch() ) {
				this._initTooltip(this.markerElem, 'click');
			} else{ 
				this._initTooltip(this.markerElem, this.tooltipTrigger);
			}

			// Start of hotspot functionality.
			if ( 'yes' == this.tourEnable && 'yes' == this.tooltipEnable ) {
				//if ( this.winWidth > this.hideMaxWidth || this.winWidth < this.hideMinWidth || 'none' == this.hideMaxWidth || 'none' == this.hideMinWidth ) {
				if ( this._matchDevice( this.tourVisibility ) ) {
					this._initButtonOverlay();
				} else {
					this.node.find('.pp-hotspot-overlay').hide();
					this.node.find('.pp-tour').hide();
					clearInterval(this.hotspotInterval[this.id]);
				}
			} else {
				clearInterval(this.hotspotInterval[this.id]);
			}
			// Hide Marker Title
			if (this.isBuilderActive) {
				// Enable Admin Title Preview in editor mode
				if ('yes' == this.adminTitlePreview ) {
					this.node.find('span.pp-marker-title').show();
				}else{
					this.node.find('span.pp-marker-title').addClass('sr-only');
				}
				// Enable Tooltip Preview in editor mode
				if ( 'yes' == this.tooltipPreview && 'yes' == this.tooltipEnable ) {
					this.node.find('.pp-hotspot-marker.pp-marker-1').trigger('click');
					this.node.find('.pp-hotspot-marker').addClass('open');
				}
			} else {
				this.node.find('span.pp-marker-title').addClass('sr-only');
			}
			
			// Stop propagation to prevent hiding when clicking on body
			this.node.find('.pp-hotspot').on('click touch', function (event) {
				if ( 'no' === self.clickToClose ) {
					event.stopPropagation();
				}
			});
		},

		_initTooltip: function (selector, triggerValue) {
			if ( 'yes' !== this.tooltipEnable ) {
				return;
			}
			if ( ! $.fn.tooltipster ) {
				console.log( 'Tooltipster library was not initiated.' );
				return;
			}
			var self = this;
			var _id = $(selector).data( 'tooltop-content' );
			_id = _id ? _id.replace( '#', '' ) : '';

			$(selector).tooltipster({
				theme: 				['tooltipster-noir', 'tooltipster-noir-customized'],
				maxWidth: 			self.tooltipWidth,
				trigger: 			triggerValue,
				side: 				self.tooltipPosition,
				arrow: 				self.tooltipArrow,
				distance: 			self.tooltipDistance,
				delay: 				300,
				interactive: 		true,
				ppclass: 			self.customClass + ' ' + _id,
				animation: 			self.tooltipAnimation,
				animationDuration:	self.animationDuration,
				zIndex: 			self.tooltipZindex,
				functionReady: function (origin, tooltip) {
					//functionality to close previous other tooltips.
					var $all_tooltipser = $(self.node).find( '*' ).filter(function () {
						return $(this).data('tooltipsterNs');
					});
					if ( typeof $all_tooltipser !== 'undefined' ){
						$all_tooltipser.not(tooltip.origin).tooltipster('hide');
					}

					// Click on Close Icon to Close 
					if ( 'yes' == self.enableCloseIcon ) {
						$( '.pp-tooltip-content-' + self.id ).find( '.pp-tooltip-close' ).off('click').on('click', function (e) {
							e.preventDefault();
							$all_tooltipser.tooltipster('hide');
						});
					}

					// ESC key to Close 
					if ( 'yes' == self.escToClose ) {
						var	prefix = 'pp-hotspot';
						// Key Bindings
						$( document ).on( 'keydown.' + prefix, function (e) {
							var key    = e.keyCode;
							if ( key == 27) {
								e.preventDefault();
								$all_tooltipser.tooltipster('hide');
							}
						});
					}
					return false;
				}
			});
		},

		// Disable prev & next nav for 1st & last tooltip.
		_initTooltipNav: function () {
			var self = this;
			if ('no' == self.tourRepeat) {
				self.node.find(".pp-prev[data-tooltipid='1']").addClass("inactive");
				self.node.find(".pp-next[data-tooltipid='" + self.markerLength + "']").addClass("inactive");
			}
		},

		_initTourPlay: function () {
			if ( ! $.fn.tooltipster ) {
				console.log( 'Tooltipster library was not initiated.' );
				return;
			}
			var self = this;
			clearInterval(self.hotspotInterval[self.id]);

			// Open previous tooltip on trigger
			self.node.find('.pp-prev').off('click').on('click', function (e) {
				clearInterval(self.hotspotInterval[self.id]);

				var sid = $(this).data('tooltipid');

				if (sid <= self.markerLength) {
					self.node.find('.pp-marker-' + sid).trigger('click');

					if ('yes' == self.tourRepeat) {
						if (sid == 1) {
							sid = parseInt(self.markerLength) + 1;
						}
					} else {
						if (sid == 1) {
							self._initButtonOverlay();
							$(self.overlayId).show();
						}
					}
					sid = sid - 1;
					self.node.find('.pp-marker-' + sid).trigger('click');
				}
				if ('yes' == self.tourAutoplay) {
					self._initSectionInterval();
				}
			});

			// Open next tooltip on trigger
			self.node.find('.pp-next').off('click').on('click', function (e) {
				clearInterval(self.hotspotInterval[self.id]);
				var sid = $(this).data('tooltipid');

				if (sid <= self.markerLength) {
					self.node.find('.pp-marker-' + sid).trigger('click');
					if ('yes' == self.tourRepeat) {
						if (sid == self.markerLength) {
							sid = 0;
						}
					} else {
						if (sid == self.markerLength) {
							self._initButtonOverlay();
							$(self.overlayId).show();
						}
					}
					sid = sid + 1;
					self.node.find('.pp-marker-' + sid).trigger('click');
				}
				if ('yes' == self.tourAutoplay) {
					self._initSectionInterval();
				}
			});

			// Close tooltip on End Tour trigger
			self.node.find('.pp-tour-end').off('click').on('click', function (e) {
				clearInterval(self.hotspotInterval[self.id]);
				e.preventDefault();

				self.node.find('.pp-hotspot-marker.open').tooltipster('close');
				self.node.find('.pp-hotspot-marker.open').removeClass('open');

				if ('yes' == self.tourAutoplay && 'on_scroll' == self.launchTourOn) {
					self.node.find('.pp-hotspot-marker').css("pointer-events", "none");
				} else {
					self._initButtonOverlay();
					$(self.overlayId).show();
				}
			});

			// Add & remove open class for tooltip.
			self.node.find('.pp-hotspot-marker').off('click').on('click', function (e) {
				if (!$(this).hasClass('open')) {
					self.node.find('.pp-hotspot-marker').tooltipster('close');
					self.node.find('.pp-hotspot-marker').removeClass('open');
					$(this).tooltipster('open');
					$(this).addClass('open');

					if ('yes' == self.tourAutoplay) {
						$(this).css("pointer-events", "visible");
						self.node.find('.pp-hotspot-marker.open').hover(function () {
							self.hoverFlag = true;
						}, function () {
							self.hoverFlag = false;
						});
					}
				} else {
					$(this).tooltipster('close');
					$(this).removeClass('open');
					if ('yes' == self.tourAutoplay) {
						$(this).css("pointer-events", "none");
					}
				}
			});

			//Initialy open first tooltip by default.
			if ('yes' == self.tourAutoplay) {
				self.node.find('.pp-hotspot-marker').css("pointer-events", "none");
				self._initTooltipNav();
				self.node.find('.pp-hotspot-marker.pp-marker-1').trigger('click');
				self._initSectionInterval();
			} else if ('no' == self.tourAutoplay) {
				self._initTooltipNav();
				self.node.find('.pp-hotspot-marker.pp-marker-1').trigger('click');
			}
		},

		_initSectionInterval: function () {
			var self = this;
			self.hotspotInterval[self.id] = setInterval(function () {
				sid = self.node.find('.pp-hotspot-marker' + '.open').data('pptour');
				if (!self.hoverFlag) {
					self.node.find('.pp-hotspot-marker' + '.open').trigger('click');

					if ('yes' == self.tourRepeat) {
						if (!this.isBuilderActive) {

							if (sid == self.markerLength) {
								sid = 1;
							} else {
								sid = sid + 1;
							}
							self.node.find('.pp-marker-' + sid).trigger('click');

							$(window).on('scroll', function () {
								if (!self.scrolling) {
									self.scrolling = true;
									(!window.requestAnimationFrame) ? setTimeout(self._updateSections.bind( self ), 300) : window.requestAnimationFrame( self._updateSections.bind( self ) );
								}
							});

						} else {
							if (sid < self.markerLength) {
								sid = sid + 1;
								self.node.find('.pp-marker-' + sid).trigger('click');
							}
							else if (sid == self.markerLength) {
								clearInterval(self.hotspotInterval[self.id]);
								self._initButtonOverlay();
								$(self.overlayId).show();
							}
						}

					} else if ('no' == self.tourRepeat) {
						if (sid < self.markerLength) {
							sid = sid + 1;
							self.node.find('.pp-marker-' + sid).trigger('click');
						} else if (sid == self.markerLength) {
							clearInterval(self.hotspotInterval[self.id]);

							if ( 'on_scroll' == self.launchTourOn ) {
								self.node.find('.pp-hotspot-marker').tooltipster('close');
								self.node.find('.pp-hotspot-marker').removeClass('open');
							} else {	
								self._initButtonOverlay();
								$(self.overlayId).show();
							}
						}
					}
				}
			}, self.tooltipInterval);
		},

		_updateSections() {
			var halfWindowHeight = $(window).height() / 2,
				scrollTop = $(window).scrollTop(),
				section = this.content;

			if (!(section.offset().top - halfWindowHeight < scrollTop) && (section.offset().top + section.height() - halfWindowHeight > scrollTop)) {

			} else {
				this.node.find('.pp-hotspot-marker.open').tooltipster('close');
				this.node.find('.pp-hotspot-marker.open').removeClass('open');
				clearInterval(this.hotspotInterval[this.id]);
				this._initButtonOverlay();
				$(this.overlayId).show();
			}
			this.scrolling = false;
		},

		// Add button overlay when tour ends.
		_initButtonOverlay: function () {
			var self = this;
			if ('yes' == self.tourEnable) {
				if ('button_click' == self.launchTourOn) {
					// if ( 'yes' == self.tourAutoplay ) {
					if (!this.isBuilderActive) {
						$(self.buttonId).off().on('click', function (e) {
							$(self.overlayId).hide();
							self._initTourPlay();
						});
					}
					// }
				} else if ('on_scroll' == self.launchTourOn && 'yes' == self.tourAutoplay) {
					if (!this.isBuilderActive) {

						if (typeof jQuery.fn.waypoint !== 'undefined') {
							self.content.waypoint({
								offset: self.viewport + '%',
								handler: function (direction) {
									self._initTourPlay();
								}
							});
						}
					}
				} else {
					self._initTourPlay();
				}
			}
		},

		_isTouch: function() {
			var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
			var mq = function(query) {
			  return window.matchMedia(query).matches;
			}
		  
			if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
			  return true;
			}
		  
			// include the 'heartz' as a way to have a non matching MQ to help terminate the join
			// https://git.io/vznFH
			var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
			return mq(query);
		},

		_matchDevice: function (value) {
			var match = false;

			if ('all' === value) {
				match = true;
			} else if ('large' === value) {
				match = window.innerWidth > this.breakpoints.tablet;
			} else if ('large-medium' === value) {
				match = window.innerWidth > this.breakpoints.mobile;
			} else if ('large-responsive' === value) {
				match = window.innerWidth < this.breakpoints.mobile || window.innerWidth > this.breakpoints.tablet;
			} else if ('medium' === value) {
				match = window.innerWidth > this.breakpoints.mobile && window.innerWidth <= this.breakpoints.tablet;
			} else if ('medium-responsive' === value) {
				match = window.innerWidth <= this.breakpoints.tablet;
			} else if ('responsive' === value) {
				match = window.innerWidth <= this.breakpoints.mobile;
			}

			return match;
		},
	};

})(jQuery);
