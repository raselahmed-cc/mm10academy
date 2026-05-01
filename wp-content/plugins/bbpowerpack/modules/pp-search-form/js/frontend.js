;(function($) {

	PPSearchForm = function(settings) {
		this.id 	= settings.id;
		this.node 	= $('.fl-node-' + this.id);
		this.form	= this.node.find('.pp-search-form');
		this.selectors = {
			input: '.fl-node-' + this.id + ' .pp-search-form__input',
			toggle: '.fl-node-' + this.id + ' .pp-search-form__toggle',
			container: '.fl-node-' + this.id + ' .pp-search-form__container',
		};

		this._init();
	};

	PPSearchForm.prototype = {
		id  : '',
		node: '',
		form: '',

		_init: function() {
			$( this.selectors.input ).on( 'focus', function() {
				this.form.addClass('pp-search-form--focus');
			}.bind( this ) ).on( 'blur', function() {
				this.form.removeClass('pp-search-form--focus');
			}.bind( this ) );

			$( this.selectors.toggle ).on( 'click', function() {
				$( this.selectors.container ).addClass('pp-search-form--lightbox').find('.pp-search-form__input').trigger( 'focus' );
				this._focus( this.form );
			}.bind( this ) );

			this.form.find('.pp-search-form--lightbox-close').on( 'click', function() {
				$( this.selectors.container ).removeClass('pp-search-form--lightbox');
			}.bind( this ) );

			// close modal box on Esc key press.
			$(document).on( 'keyup', function(e) {
                if ( 27 == e.which && this.form.find('.pp-search-form--lightbox').length > 0 ) {
                    $( this.selectors.container ).removeClass('pp-search-form--lightbox');
                }
			}.bind( this ) );
		},

		_focus: function( form ) {
			var $el = form.find('.pp-search-form__input');

			// If this function exists... (IE 9+)
			if ( $el[0].setSelectionRange ) {
				// Double the length because Opera is inconsistent about whether a carriage return is one character or two.
				var len = $el.val().length * 2;

				// Timeout seems to be required for Blink
				setTimeout(function() {
					$el[0].setSelectionRange( len, len );
				}, 1);
			} else {
				// As a fallback, replace the contents with itself
				// Doesn't work in Chrome, but Chrome supports setSelectionRange
				$el.val( $el.val() );
			}
		}
	};

})(jQuery);