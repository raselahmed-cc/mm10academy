( function( $ ) {
	window.onLoadPPReCaptcha = function () {
		var reCaptchaFields = $('.pp-grecaptcha'),
			widgetID;

		if (reCaptchaFields.length > 0) {
			reCaptchaFields.each(function (i) {
				var self = $(this),
					attrWidget = self.attr('data-widgetid'),
					newID = $(this).attr('id') + '-' + i;

				// Avoid re-rendering as it's throwing API error
				if ((typeof attrWidget !== typeof undefined && attrWidget !== false)) {
					return;
				}
				else {
					// Increment ID to avoid conflict with the same form.
					self.attr('id', newID);

					widgetID = grecaptcha.render(newID, {
						sitekey: self.data('sitekey'),
						theme: self.data('theme'),
						size: self.data('validate'),
						callback: function (response) {
							if (response != '') {
								self.attr('data-pp-grecaptcha-response', response);

								// Re-submitting the form after a successful invisible validation.
								if ('invisible' == self.data('validate')) {
									self.closest('.fl-module').find('.pp-form-button').trigger('click');
								}
							}
						}
					});

					self.attr('data-widgetid', widgetID);
				}
			});
		}
	};

	window.onLoadPPHCaptcha = function() {
		var hCaptchaFields = $('.pp-hcaptcha .h-captcha'),
			widgetID;

		if (hCaptchaFields.length > 0) {
			hCaptchaFields.each(function (i) {
				var self = $(this),
					frame = $(this).find('iframe'),
					attrWidget = frame.attr('data-hcaptcha-widget-id'),
					newID = $(this).attr('id') + '-' + i;

				// Avoid re-rendering as it's throwing API error
				if ((typeof attrWidget !== typeof undefined && attrWidget !== false)) {
					return;
				}
				else {
					// Increment ID to avoid conflict with the same form.
					self.attr('id', newID);

					widgetID = hcaptcha.render(newID, {
						sitekey: self.data('sitekey'),
						callback: function (response) {
							if (response != '') {
								self.attr('data-pp-hcaptcha-response', response);
							}
						}
					});

					self.attr('data-hcaptcha-widget-id', widgetID);
				}
			});
		}
	};

	PPSubscribeForm = function( settings )
	{
		this.settings	= settings;
		this.nodeClass	= '.fl-node-' + settings.id;
		this.form 		= $( this.nodeClass + ' .pp-subscribe-form' );
		this.wrap 		= this.form.find( '.pp-subscribe-form-inner' );
		this.button		= this.form.find( 'a.fl-button' );
		this._init();
	};

	PPSubscribeForm.prototype = {

		settings	: {},
		nodeClass	: '',
		form		: null,
		button		: null,

		_init: function()
		{
			this.button.on( 'click', this._submitForm.bind( this ) );
			this.form.find( 'input[type="email"]' ).on( 'keypress', this._onEnterKey.bind( this ) );
		},

		_submitForm: function( e )
		{
			var postId      	= this.form.closest( '.fl-builder-content' ).data( 'post-id' ),
				templateId		= this.form.data( 'template-id' ),
				templateNodeId	= this.form.data( 'template-node-id' ),
				nodeId      	= this.form.closest( '.fl-module' ).data( 'node' ),
				buttonText  	= this.button.find( '.fl-button-text' ).text(),
				waitText    	= this.button.closest( '.pp-form-button' ).data( 'wait-text' ),
				name        	= this.form.find( 'input[name=pp-subscribe-form-name]' ),
				email       	= this.form.find( 'input[name=pp-subscribe-form-email]' ),
				acceptance   	= this.form.find( 'input[name=pp-subscribe-form-acceptance]'),
				reCaptchaField 	= this.form.find('.pp-grecaptcha'),
				reCaptchaValue 	= reCaptchaField.data('pp-grecaptcha-response'),
				hCaptchaField 	= this.form.find('.h-captcha'),
				hCaptchaValue 	= hCaptchaField.find('iframe').data('hcaptcha-response'),
				re          	= /\S+@\S+\.\S+/,
				valid       	= true;

			e.preventDefault();

			if ( this.button.hasClass( 'pp-form-button-disabled' ) ) {
				return; // Already submitting
			}
			if ( name.length > 0 ) {
				if ( name.val() == '' ) {
					name.parent().addClass( 'pp-form-error' );
					valid = false;
				} else {
					name.parent().removeClass( 'pp-form-error' );
				}
			}
			if ( '' == email.val() || ! re.test( email.val() ) ) {
				email.parent().addClass( 'pp-form-error' );
				valid = false;
			} else {
				email.parent().removeClass( 'pp-form-error' );
			}

			if ( acceptance.length ) {
				if ( ! acceptance.is(':checked') ) {
					valid = false;
					acceptance.parent().addClass( 'pp-form-error' );
				}
				else {
					acceptance.parent().removeClass( 'pp-form-error' );
				}
			}

			// validate if reCAPTCHA is enabled and checked
			if (reCaptchaField.length > 0) {
				if ('undefined' === typeof reCaptchaValue || reCaptchaValue === false) {
					valid = false;
					if ('normal' == reCaptchaField.data('validate')) {
						reCaptchaField.parent().addClass('pp-form-error');
					} else if ('invisible' == reCaptchaField.data('validate')) {

						// Invoke the reCAPTCHA check.
						grecaptcha.execute(reCaptchaField.data('widgetid'));
					}
				} else {
					reCaptchaField.parent().removeClass('pp-form-error');
				}
			}

			// validate if hCaptcha is enabled and checked
			if (hCaptchaField.length > 0) {
				if ('undefined' === typeof hCaptchaValue || hCaptchaValue === false) {
					valid = false;
					hCaptchaField.parent().addClass('pp-form-error');
				} else {
					hCaptchaField.parent().removeClass('pp-form-error');
				}
			}

			var ajaxData = {
				name       : name.val(),
				email      : email.val(),
				acceptance : acceptance.is(':checked') ? '1' : '0',
			};

			this.form.trigger( 'pp_form_before_process', [ajaxData, valid] );

			if ( valid ) {

				this.form.find( '> .pp-form-error-message' ).hide();
				this.button.find( '.fl-button-text' ).text( waitText );
				this.button.data( 'original-text', buttonText );
				this.button.addClass( 'pp-form-button-disabled' );

				ajaxData.action      = 'pp_subscribe_form_submit';
				ajaxData.post_id     = postId;
				ajaxData.node_id     = nodeId;
				ajaxData.template_id = templateId;
				ajaxData.template_node_id = templateNodeId;

				if (reCaptchaValue) {
					ajaxData.recaptcha_response = reCaptchaValue;
				}
				if (hCaptchaValue) {
					ajaxData.hcaptcha_response = hCaptchaValue;
				}

				$.post( bb_powerpack.getAjaxUrl(), ajaxData, this._submitFormComplete.bind( this ) );
			}
		},

		_submitFormComplete: function( response )
		{
			var data        = JSON.parse( response ),
				buttonText  = this.button.data( 'original-text' );

			if ( data.error ) {

				if ( data.error ) {
					this.wrap.find( '> .pp-form-error-message' ).text( data.error );
				}
				if ( typeof data.errorInfo !== 'undefined' ) {
					console.log( 'Subscribe Form:', data.errorInfo );
				}

				this.wrap.find( '> .pp-form-error-message' ).show();
				this.button.removeClass( 'pp-form-button-disabled' );
				this.button.find( '.fl-button-text' ).text( buttonText );
			}
			else if ( 'message' == data.action ) {
				this.form.find( '> *' ).hide();
				this.form.append( '<div class="pp-form-success-message">' + data.message + '</div>' );
			}
			else if ( 'redirect' == data.action ) {
				window.location.href = data.url;
			}
		},

		_onEnterKey: function( e )
		{
			if (e.which == 13) {
		    	this.button.trigger( 'click' );
		  	}
		}
	}

})( jQuery );
