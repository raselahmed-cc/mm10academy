/**
 * UABB SVG Animator – Frontend JavaScript
 *
 * Handles stroke-drawing animation for SVG icons using CSS transitions
 * and the IntersectionObserver API. Animation settings are read from
 * data attributes written by the PHP template.
 *
 * @package UABB SVG Animator Module
 */

/* global jQuery */

( function( $ ) {
	'use strict';

	/**
	 * UABBSvgAnimator
	 *
	 * @param {HTMLElement} element – The .uabb-svg-animator wrapper element.
	 * @constructor
	 */
	function UABBSvgAnimator( element ) {
		this.element     = element;
		this.$element    = $( element );
		this.isAnimated  = false;
		this.isAnimating = false;
		this.isInViewport = false;
		this.observer    = null;
		this.paths       = [];
		this.loopTimeout = null;
		this.currentLoop = 0;

		this._init();
	}

	UABBSvgAnimator.prototype = {

		// ---- Init -------------------------------------------------------

		_init: function() {
			var $el = this.$element;

			this.settings = {
				animationType    : $el.data( 'animation-type' )     || 'sync',
				animationTrigger : $el.data( 'animation-trigger' )  || 'viewport',
				animationDuration: Math.max( 0.1, parseFloat( $el.data( 'animation-duration' ) ) || 3 ),
				animationDelay   : Math.max( 0,   parseFloat( $el.data( 'animation-delay' ) )    || 0 ),
				pathTiming       : $el.data( 'path-timing' )        || 'ease-out',
				autoStart        : $el.data( 'auto-start' )         || 'yes',
				replayOnClick    : $el.data( 'replay-on-click' )    || 'no',
				looping          : $el.data( 'looping' )            || 'none',
				loopCount        : parseInt( $el.data( 'loop-count' ), 10 ) || 1,
				direction        : $el.data( 'direction' )          || 'forward',
				fillMode         : $el.data( 'fill-mode' )          || 'none',
				fillColor        : $el.data( 'fill-color' )         || '',
				fillDuration     : Math.max( 0, parseFloat( $el.data( 'fill-duration' ) ) || 1 ),
				strokeColor      : $el.data( 'stroke-color' )       || '',
				strokeWidth      : $el.data( 'stroke-width' )       || '1px',
				staggerDelay     : parseInt( $el.data( 'stagger-delay' ), 10 ) || 100,
				lazyLoad         : $el.data( 'lazy-load' )          || 'no',
			};

			// Respect OS-level "reduce motion" preference (WCAG 2.1 SC 2.3.3).
			this.prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

			this._findSvgElements();

			if ( this.prefersReducedMotion ) {
				this._skipToEnd();
				return;
			}

			this._setupTrigger();
			this._setupReplayClick();
		},

		// ---- SVG Discovery ----------------------------------------------

		_findSvgElements: function() {
			var self    = this;
			var $svg    = this.$element.find( 'svg' );
			var selectors = [ 'path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'ellipse' ];

			this.paths = [];

			if ( ! $svg.length ) {
				return;
			}

			$.each( selectors, function( i, sel ) {
				$svg.find( sel ).each( function() {
					self._preparePath( this );
				} );
			} );
		},

		_preparePath: function( pathElement ) {
			var $path = $( pathElement );
			var pathLength;

			try {
				if ( typeof pathElement.getTotalLength === 'function' ) {
					pathLength = pathElement.getTotalLength();
				} else {
					pathLength = this._estimatePathLength( pathElement );
				}

				if ( pathLength > 0 ) {
					this.paths.push( {
						element : pathElement,
						$element: $path,
						length  : pathLength,
					} );

					// Apply stroke via inline style so it overrides any stroke rules
					// already present in the SVG file (internal <style> blocks or
					// presentation attributes on individual elements).
					if ( this.settings.strokeColor ) {
						$path.css( 'stroke', this.settings.strokeColor );
					}
					if ( this.settings.strokeWidth ) {
						$path.css( 'stroke-width', this.settings.strokeWidth );
					}

					$path.css( {
						'stroke-dasharray' : pathLength + ' ' + pathLength,
						'stroke-dashoffset': pathLength,
					} );

					this._applyInitialFill( $path );
					$path.addClass( 'uabb-svg-path' );
				}
			} catch ( e ) {
				// Silently skip unmeasurable paths.
			}
		},

		/**
		 * Apply the correct initial fill state to a path element.
		 *
		 * We set fill color and opacity via JS (inline style) so that SVG
		 * presentation attributes on the original SVG (e.g. fill="black") do not
		 * override the user's chosen fill color. Inline styles via $.css() always
		 * win over SVG presentation attributes.
		 */
		_applyInitialFill: function( $path ) {
			var color    = this.settings.fillColor;
			var fillMode = this.settings.fillMode;

			switch ( fillMode ) {
				case 'before':
				case 'always':
					// Visible from the start: set color and full opacity.
					if ( color ) {
						$path.css( 'fill', color );
					}
					$path.css( 'fill-opacity', '1' );
					break;

				case 'after':
					// Revealed after stroke animation: set color but hide it.
					if ( color ) {
						$path.css( 'fill', color );
					}
					$path.css( 'fill-opacity', '0' );
					break;

				case 'none':
				default:
					// No fill.
					$path.css( { 'fill': 'none', 'fill-opacity': '1' } );
					break;
			}
		},

		_estimatePathLength: function( el ) {
			var tag = el.tagName.toLowerCase();
			var r, rx, ry, w, h, x1, y1, x2, y2, pts;

			switch ( tag ) {
				case 'circle':
					r = parseFloat( el.getAttribute( 'r' ) ) || 0;
					return 2 * Math.PI * r;

				case 'ellipse':
					rx = parseFloat( el.getAttribute( 'rx' ) ) || 0;
					ry = parseFloat( el.getAttribute( 'ry' ) ) || 0;
					return Math.PI * ( 3 * ( rx + ry ) - Math.sqrt( ( 3 * rx + ry ) * ( rx + 3 * ry ) ) );

				case 'rect':
					w = parseFloat( el.getAttribute( 'width' ) )  || 0;
					h = parseFloat( el.getAttribute( 'height' ) ) || 0;
					return 2 * ( w + h );

				case 'line':
					x1 = parseFloat( el.getAttribute( 'x1' ) ) || 0;
					y1 = parseFloat( el.getAttribute( 'y1' ) ) || 0;
					x2 = parseFloat( el.getAttribute( 'x2' ) ) || 0;
					y2 = parseFloat( el.getAttribute( 'y2' ) ) || 0;
					return Math.sqrt( Math.pow( x2 - x1, 2 ) + Math.pow( y2 - y1, 2 ) );

				case 'polyline':
				case 'polygon':
					pts = el.getAttribute( 'points' );
					return pts ? this._polylineLength( pts ) : 0;

				default:
					return 0;
			}
		},

		_polylineLength: function( pointsStr ) {
			var points = pointsStr.trim().split( /[\s,]+/ );
			var total  = 0;
			var i, x1, y1, x2, y2;

			for ( i = 0; i < points.length - 2; i += 2 ) {
				x1 = parseFloat( points[ i ] );
				y1 = parseFloat( points[ i + 1 ] );
				x2 = parseFloat( points[ i + 2 ] );
				y2 = parseFloat( points[ i + 3 ] );
				if ( ! isNaN( x1 ) && ! isNaN( y1 ) && ! isNaN( x2 ) && ! isNaN( y2 ) ) {
					total += Math.sqrt( Math.pow( x2 - x1, 2 ) + Math.pow( y2 - y1, 2 ) );
				}
			}

			return total;
		},

		// ---- Trigger Setup ----------------------------------------------

		_setupTrigger: function() {
			// Always set up viewport observer – needed for looping & lazy-load.
			this._setupViewportObserver();

			if ( 'no' === this.settings.autoStart ) {
				return;
			}

			switch ( this.settings.animationTrigger ) {
				case 'auto':
					this._delayedStart( 0 );
					break;
				case 'viewport':
					// handled by observer.
					break;
				case 'hover':
					this._setupHoverTrigger();
					break;
				case 'click':
					this._setupClickTrigger();
					break;
				case 'delay':
					this._delayedStart( this.settings.animationDelay );
					break;
				default:
					// default falls back to viewport.
			}
		},

		_delayedStart: function( delaySec ) {
			var self = this;
			if ( delaySec > 0 ) {
				setTimeout( function() {
					self.startAnimation();
				}, delaySec * 1000 );
			} else {
				this.startAnimation();
			}
		},

		_setupViewportObserver: function() {
			var self = this;

			if ( ! window.IntersectionObserver ) {
				// Fallback for old browsers: animate immediately.
				this.isInViewport = true;
				this.startAnimation();
				return;
			}

			this.observer = new IntersectionObserver(
				function( entries ) {
					entries.forEach( function( entry ) {
						if ( entry.isIntersecting ) {
							self.$element.addClass( 'uabb-svg-in-view' );
							self.isInViewport = true;

							if (
								! self.isAnimated &&
								! self.isAnimating &&
								'viewport' === self.settings.animationTrigger
							) {
								setTimeout( function() {
									self.startAnimation();
								}, 100 );
							}
						} else {
							self.$element.removeClass( 'uabb-svg-in-view' );
							self.isInViewport = false;
							if ( self.loopTimeout ) {
								clearTimeout( self.loopTimeout );
								self.loopTimeout = null;
							}
						}
					} );
				},
				{ root: null, rootMargin: '0px', threshold: 0.1 }
			);

			this.observer.observe( this.element );
		},

		_setupHoverTrigger: function() {
			var self = this;
			this.$element.find( '.uabb-svg-container' ).on( 'mouseenter.uabb-svg', function() {
				if ( ! self.isAnimated && ! self.isAnimating ) {
					self.startAnimation();
				}
			} );
		},

		_setupClickTrigger: function() {
			var self = this;
			this.$element.find( '.uabb-svg-container' ).on( 'click.uabb-svg', function( e ) {
				if ( ! self.$element.closest( 'a' ).length ) {
					e.preventDefault();
				}
				if ( ! self.isAnimated && ! self.isAnimating ) {
					self.startAnimation();
				} else if ( 'yes' === self.settings.replayOnClick ) {
					self.replayAnimation();
				}
			} );
		},

		_setupReplayClick: function() {
			if ( 'yes' !== this.settings.replayOnClick ) {
				return;
			}
			if ( 'click' === this.settings.animationTrigger ) {
				return; // Already handled in _setupClickTrigger.
			}

			var self = this;
			this.$element.find( '.uabb-svg-container' ).on( 'click.uabb-svg-replay', function( e ) {
				if ( ! self.$element.closest( 'a' ).length ) {
					e.preventDefault();
				}
				self.replayAnimation();
			} );
		},

		// ---- Animation --------------------------------------------------

		startAnimation: function() {
			if ( this.isAnimating || ! this.paths.length ) {
				return;
			}

			this.isAnimating = true;
			this.$element.addClass( 'uabb-svg-animating' );

			if ( 'backward' === this.settings.direction ) {
				this.paths = this.paths.reverse();
			}

			switch ( this.settings.animationType ) {
				case 'delayed':
					this._animateDelayed();
					break;
				case 'one-by-one':
					this._animateOneByOne();
					break;
				case 'sync':
				default:
					this._animateSync();
			}
		},

		_animateSync: function() {
			var self          = this;
			var duration      = this.settings.animationDuration * 1000;
			var completed     = 0;
			var total         = this.paths.length;

			this.paths.forEach( function( pathInfo ) {
				self._animatePath( pathInfo, 0, duration, function() {
					completed++;
					if ( completed === total ) {
						self._onComplete();
					}
				} );
			} );
		},

		_animateDelayed: function() {
			var self          = this;
			var duration      = this.settings.animationDuration * 1000;
			var stagger       = this.settings.staggerDelay;
			var completed     = 0;
			var total         = this.paths.length;

			this.paths.forEach( function( pathInfo, index ) {
				self._animatePath( pathInfo, index * stagger, duration, function() {
					completed++;
					if ( completed === total ) {
						self._onComplete();
					}
				} );
			} );
		},

		_animateOneByOne: function() {
			var self         = this;
			var totalDur     = this.settings.animationDuration * 1000;
			var pathDur      = totalDur / Math.max( this.paths.length, 1 );
			var stagger      = this.settings.staggerDelay;
			var currentIndex = 0;

			var next = function() {
				if ( currentIndex >= self.paths.length ) {
					self._onComplete();
					return;
				}
				var pathInfo = self.paths[ currentIndex ];
				self._animatePath( pathInfo, stagger * currentIndex, pathDur, function() {
					currentIndex++;
					next();
				} );
			};

			next();
		},

		_animatePath: function( pathInfo, delay, duration, onComplete ) {
			var $path  = pathInfo.$element;
			var timing = this.settings.pathTiming;

			setTimeout( function() {
				$path.css( {
					'transition'        : 'stroke-dashoffset ' + duration + 'ms ' + timing,
					'stroke-dashoffset' : '0',
				} );

				var done = false;
				var finish = function() {
					if ( done ) { return; }
					done = true;
					$path.off( 'transitionend.uabb-svg' );
					if ( typeof onComplete === 'function' ) {
						onComplete();
					}
				};

				$path.on( 'transitionend.uabb-svg', finish );
				// Safety fallback: ensure completion even if transitionend misfires.
				setTimeout( finish, duration + 100 );
			}, delay );
		},

		/**
		 * Skip animation and show SVG in its final state immediately.
		 * Used when prefers-reduced-motion is enabled.
		 */
		_skipToEnd: function() {
			this.isAnimated = true;
			this.$element.addClass( 'uabb-svg-animated' );

			this.paths.forEach( function( p ) {
				p.$element.css( 'stroke-dashoffset', '0' );
			} );

			this._handleFill();
		},

		_onComplete: function() {
			var self = this;

			this.isAnimating = false;
			this.isAnimated  = true;
			this.$element.removeClass( 'uabb-svg-animating' ).addClass( 'uabb-svg-animated' );

			// Clear stroke transitions.
			this.paths.forEach( function( p ) {
				p.$element.css( 'transition', '' );
			} );

			this._handleFill();
			this._handleLooping();
		},

		/**
		 * Animate fill opacity after the stroke animation completes.
		 *
		 * Only runs for 'after' and 'always' fill modes. The fill color was
		 * already applied in _applyInitialFill; here we transition fill-opacity
		 * from 0 → 1.
		 */
		_handleFill: function() {
			var fillMode = this.settings.fillMode;
			if ( 'after' !== fillMode && 'always' !== fillMode ) {
				return;
			}

			var fillDur = this.settings.fillDuration * 1000;
			this.paths.forEach( function( p ) {
				p.$element.css( {
					'transition'  : 'fill-opacity ' + fillDur + 'ms ease',
					'fill-opacity': '1',
				} );
			} );
		},

		_handleLooping: function() {
			if ( ! this.isInViewport ) {
				return;
			}

			if ( 'infinite' === this.settings.looping ) {
				this._scheduleLoop();
			} else if ( 'count' === this.settings.looping ) {
				this.currentLoop = ( this.currentLoop || 0 ) + 1;
				if ( this.currentLoop < this.settings.loopCount ) {
					this._scheduleLoop();
				} else {
					this.currentLoop = 0;
				}
			}
		},

		_scheduleLoop: function() {
			var self = this;
			this.loopTimeout = setTimeout( function() {
				if ( self.isInViewport && ! self.isAnimating ) {
					self.replayAnimation();
				}
			}, 500 );
		},

		replayAnimation: function() {
			if ( this.isAnimating ) {
				return;
			}

			this.isAnimated = false;
			this.$element.removeClass( 'uabb-svg-animated' );

			var self     = this;
			var fillMode = this.settings.fillMode;
			// Only hide fill on replay for 'none' and 'after' modes.
			// 'before'/'always' should keep fill visible throughout.
			var replayOpacity = ( 'none' === fillMode || 'after' === fillMode ) ? '0' : '1';

			this.paths.forEach( function( p ) {
				p.$element.css( {
					'stroke-dashoffset': p.length,
					'transition'       : '',
					'fill-opacity'     : replayOpacity,
				} );
			} );

			setTimeout( function() {
				self.startAnimation();
			}, 100 );
		},

		// ---- Destroy ----------------------------------------------------

		destroy: function() {
			if ( this.observer ) {
				this.observer.disconnect();
				this.observer = null;
			}
			if ( this.loopTimeout ) {
				clearTimeout( this.loopTimeout );
				this.loopTimeout = null;
			}

			this.$element.find( '.uabb-svg-container' ).off( '.uabb-svg .uabb-svg-replay' );
			this.$element.removeClass( 'uabb-svg-animating uabb-svg-animated uabb-svg-in-view' );

			this.paths.forEach( function( p ) {
				p.$element.removeClass( 'uabb-svg-path' ).css( {
					'stroke'           : '',
					'stroke-width'     : '',
					'stroke-dasharray' : '',
					'stroke-dashoffset': '',
					'transition'       : '',
					'fill'             : '',
					'fill-opacity'     : '',
				} );
			} );

			this.paths = [];
		},
	};

	// Expose globally for the per-node initializer.
	window.UABBSvgAnimator = UABBSvgAnimator;

}( jQuery ) );
