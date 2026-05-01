(function($) {

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
									self.closest('.fl-module').find('.pp-submit-button').trigger('click');
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

	PPContactForm = function( settings )
	{
		this.settings	= settings;
		this.nodeClass	= '.fl-node-' + settings.id;
		this._init();
	};

	PPContactForm.prototype = {

		settings	: {},
		nodeClass	: '',

		_init: function()
		{
			var self = this;

			$( this.nodeClass + ' .pp-contact-form' ).on( 'submit', this._submit.bind( this ) );
			$( this.nodeClass + ' .pp-contact-form .pp-submit-button' ).on( 'click', function(e) {
				e.preventDefault();
				$( self.nodeClass + ' .pp-contact-form' ).trigger( 'submit' );
			} );
		},

		_submit: function( e )
		{
			var theForm	  		= $(this.nodeClass + ' .pp-contact-form'),
				submit	  		= $(this.nodeClass + ' .fl-button'),
				name	  		= $(this.nodeClass + ' .pp-name input'),
				email			= $(this.nodeClass + ' .pp-email input'),
				phone			= $(this.nodeClass + ' .pp-phone input'),
				subject	  		= $(this.nodeClass + ' .pp-subject input'),
				message	  		= $(this.nodeClass + ' .pp-message textarea'),
				checkbox		= $(this.nodeClass + ' .pp-checkbox input'),
				reCaptchaField 	= $(this.nodeClass + ' .pp-grecaptcha'),
				reCaptchaValue 	= reCaptchaField.data('pp-grecaptcha-response'),
				hCaptchaField 	= $(this.nodeClass + ' .h-captcha'),
				hCaptchaValue 	= hCaptchaField.find('iframe').data('hcaptcha-response'),
				ajaxData		= null,
				ajaxurl	  		= bb_powerpack.getAjaxUrl(),
				email_regex 	= /\S+@\S+\.\S+/,
				isValid	  		= true,
				postId      	= theForm.closest( '.fl-builder-content' ).data( 'post-id' ),
				layoutId      	= theForm.find( 'input[name=fl-layout-id]' ).val(),
				templateId		= theForm.data( 'template-id' ),
				templateNodeId	= theForm.data( 'template-node-id' ),
				nodeId      	= theForm.closest( '.fl-module' ).data( 'node' );

			e.preventDefault();

			// End if button is disabled (sent already)
			if (submit.hasClass('pp-disabled')) {
				return;
			}

			// validate the name
			if(name.length) {
				if (name.val() === '' && name.parents('.pp-input-required').length) {
					isValid = false;
					name.parent().addClass('pp-error');
				}
				else if (name.parent().hasClass('pp-error')) {
					name.parent().removeClass('pp-error');
				}
			}

			// validate the email
			if(email.length) {
				if ((email.val() === '' || !email_regex.test(email.val())) && email.parents('.pp-input-required').length) {
					isValid = false;
					email.parent().addClass('pp-error');
				}
				else if (email.parent().hasClass('pp-error')) {
					email.parent().removeClass('pp-error');
				}
			}

			// validate the subject..just make sure it's there
			if(subject.length) {
				if (subject.val() === '' && subject.parents('.pp-input-required').length) {
					isValid = false;
					subject.parent().addClass('pp-error');
				}
				else if (subject.parent().hasClass('pp-error')) {
					subject.parent().removeClass('pp-error');
				}
			}

			// validate the phone..just make sure it's there
			if(phone.length) {
				if (phone.val() === '' && phone.parents('.pp-input-required').length) {
					isValid = false;
					phone.parent().addClass('pp-error');
				}
				else if (phone.parent().hasClass('pp-error')) {
					phone.parent().removeClass('pp-error');
				}
			}

			// validate the message..just make sure it's there
			if (message.length > 0) {
				if (message.val() === '' && message.parents('.pp-input-required').length) {
					isValid = false;
					message.parent().addClass('pp-error');
				}
				else if (message.parent().hasClass('pp-error')) {
					message.parent().removeClass('pp-error');
				}
			}

			// validate the checkbox
			if (checkbox.length > 0) {
				if (!checkbox.is(':checked') && checkbox.parents('.pp-input-required').length) {
					isValid = false;
					checkbox.parent().addClass('pp-error');
				}
				else if (checkbox.parent().hasClass('pp-error')) {
					checkbox.parent().removeClass('pp-error');
				}
			}

			// validate if reCAPTCHA is enabled and checked
			if (reCaptchaField.length > 0) {
				if ('undefined' === typeof reCaptchaValue || reCaptchaValue === false) {
					isValid = false;
					if ('normal' == reCaptchaField.data('validate')) {
						reCaptchaField.parent().addClass('pp-error');
					} else if ('invisible' == reCaptchaField.data('validate')) {

						// Invoke the reCAPTCHA check.
						grecaptcha.execute(reCaptchaField.data('widgetid'));
					}
				} else {
					reCaptchaField.parent().removeClass('pp-error');
				}
			}

			// validate if hCaptcha is enabled and checked
			if (hCaptchaField.length > 0) {
				if ('undefined' === typeof hCaptchaValue || hCaptchaValue === false) {
					isValid = false;
					hCaptchaField.parent().addClass('pp-error');
				} else {
					hCaptchaField.parent().removeClass('pp-error');
				}
			}

			// end if we're invalid, otherwise go on..
			if (!isValid) {
				return false;
			}
			else {

				// disable send button
				submit.addClass('pp-disabled');

				ajaxData = {
					action: 'pp_send_email',
					name: name.val(),
					subject: subject.val(),
					email: email.val(),
					phone: phone.val(),
					message: message.val(),
					post_id: postId,
					layout_id: layoutId,
					template_id: templateId,
					template_node_id: templateNodeId,
					node_id: nodeId
				}

				if (reCaptchaValue) {
					ajaxData.recaptcha_response = reCaptchaValue;
				}
				if (hCaptchaValue) {
					ajaxData.hcaptcha_response = hCaptchaValue;
				}

				// post the form data
				$.post(ajaxurl, ajaxData, this._submitComplete.bind( this ) );
			}
		},

		_submitComplete: function( response )
		{
			var urlField 	= $( this.nodeClass + ' .pp-success-url' ),
				noMessage 	= $( this.nodeClass + ' .pp-success-none' );

			// On success show the success message
			if (typeof response.error !== 'undefined' && response.error === false) {

				$( this.nodeClass + ' .pp-send-error' ).fadeOut();

				if ( urlField.length > 0 ) {
					window.location.href = urlField.val();
				}
				else if ( noMessage.length > 0 ) {
					noMessage.fadeIn();
				}
				else {
					$( this.nodeClass + ' .pp-contact-form' ).hide();
					$( this.nodeClass + ' .pp-success-msg' ).fadeIn();
				}

				$(document).trigger( 'pp_contact_form_submit_success', [ $( this.nodeClass ) ] );
			}
			// On failure show fail message and re-enable the send button
			else {
				$(this.nodeClass + ' .fl-button').removeClass('pp-disabled');
				if ( typeof response.message !== 'undefined' ) {
					$(this.nodeClass + ' .pp-send-error').html(response.message);
				}
				if ( typeof response.errorInfo !== 'undefined' ) {
					console.log( 'Contact Form: ', response.errorInfo );
				}
				$(this.nodeClass + ' .pp-send-error').fadeIn();

				return false;
			}

			$(document).trigger( 'pp_contact_form_after_submit', [ $( this.nodeClass ), response ] );
		}
	};

})(jQuery);
