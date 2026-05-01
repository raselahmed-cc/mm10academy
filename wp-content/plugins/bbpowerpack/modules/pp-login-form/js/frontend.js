;(function($) {
	window.onLoadPPReCaptcha = function () {
		var reCaptchaFields = $('.pp-grecaptcha'),
			widgetID;

		if (reCaptchaFields.length > 0) {
			reCaptchaFields.each(function (i) {
				var self = $(this),
					attrWidget = self.attr('data-widgetid'),
					newID = $(this).attr('id'); // + '-' + i;
				// Avoid re-rendering as it's throwing API error
				if ((typeof attrWidget !== typeof undefined && attrWidget !== false)) {
					return;
				}
				else {
					// Increment ID to avoid conflict with the same form.
					self.attr('id', newID);

					widgetID = grecaptcha.render(newID, {
						sitekey	: self.data('sitekey'),
						theme	: self.data('theme'),
						size	: self.data('validate'),
						callback: function (response) {
							if ( response != '' ) {
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
		var hCaptchaFields = $('.h-captcha'),
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

	window.onLoadGoogleSignIn = function( response ) {
		$(document).trigger( 'pp_lf_google_signin', [ response ] );
	};

	PPLoginForm = function( settings ) {
		this.id			= settings.id;
		this.node 		= $('.fl-node-' + this.id);
		this.messages	= settings.messages;
		this.settings 	= settings;

		this._init();
	};

	PPLoginForm.prototype = {
		settings: {},
		isGoogleLoginClicked: false,

		_init: function() {
			if ( this.settings.facebook_login ) {
				this._initFacebookLogin();
			}
			if ( this.settings.google_login ) {
				this._initGoogleLogin();
			}

			if ( this.node.find( '.pp-lf-toggle-pw' ).length ) {
				this._initPasswordToggle();
			}

			if ( this.node.find( '#pp-form-' + this.id ).length > 0 ) {
				this.node.find( '#pp-form-' + this.id ).on( 'submit', this._loginFormSubmit.bind( this ) );
			}

			if ( this.node.find( '.pp-login-form--lost-pass' ).length > 0 ) {
				this.node.find( '.pp-login-form--lost-pass' ).on( 'submit', this._lostPassFormSubmit.bind( this ) );
			}

			if ( this.node.find( '.pp-login-form--reset-pass' ).length > 0 ) {
				this.node.find( '.pp-login-form--reset-pass' ).on( 'submit', this._resetPassFormSubmit.bind( this ) );
			}
		},

		_initFacebookLogin: function() {
			if ( '' === this.settings.facebook_app_id ) {
				return;
			}
			if ( this.node.find( '.pp-fb-login-button' ).length > 0 ) {
				this._initFacebookSDK();
			
				this.node.find( '.pp-fb-login-button' ).on( 'click', this._facebookLoginClick.bind( this ) );
			}
		},

		_initFacebookSDK: function() {
			var self = this;

			if ( $( '#fb-root' ).length === 0 ) {
				$('body').prepend('<div id="fb-root"></div>');
			}
			// Load the SDK asynchronously.
			var d = document, s = 'script', id = 'facebook-jssdk';
			var js, fjs = d.getElementsByTagName(s)[0];
			
			if (d.getElementById(id)) return;
			
			js = d.createElement(s); js.id = id;
			js.src = this.settings.facebook_sdk_url;
			fjs.parentNode.insertBefore(js, fjs);

			window.fbAsyncInit = function() {
			    // Init.
			    FB.init({
			      appId      : self.settings.facebook_app_id, // App ID.
			      cookie     : true,  // Enable cookies to allow the server to access the session.
			      xfbml      : true,  // Parse social plugins on this webpage.
			      version    : 'v2.12' // Use this Graph API version for this call.
				});
			};
		},

		_facebookLoginClick: function() {
			var self = this,
				theForm = this.node.find( '.pp-login-form' ),
				redirect = theForm.find( 'input[name="redirect_to"]' );

			var args = {
				action: 'pp_lf_process_social_login',
				provider: 'facebook',
				page_url: self.settings.page_url,
				nonce: self._getNonce(),
			};

			if ( redirect.length > 0 && '' !== redirect.val() ) {
				args['redirect'] = redirect.val();
			}

			this._disableForm();

			FB.login( function( response ) {
				if ( 'connected' === response.status ) {
					FB.api( '/me', { fields: 'id, email, name, first_name, last_name' }, function( response ) {
						var authResponse = FB.getAuthResponse();
						args['user_data'] = response;
						args['auth_response'] = authResponse;
						self._ajax( args, function( response ) {
							if ( ! response.success ) {
								if ( 'undefined' !== typeof response.data.code ) {
									theForm.find( '.pp-lf-error' ).remove();
									$('<span class="pp-lf-error">').appendTo( theForm ).html( response.data.message );
								} else {
									console.error( response.data );
								}
								self._enableForm();
							} else {
								if ( response.data.redirect_url ) {
									window.location.href = response.data.redirect_url;
								} else {
									window.location.reload();
								}
							}
						} );
					} );
				} else {
					if ( response.authResponse ) {
						console.error( 'PP Login Form: Unable to connect Facebook account.' );
					}
					self._enableForm();
				}
			}, {
				scope: 'email',
				return_scopes: true
			} );
		},

		_initGoogleLogin: function() {
			if ( '' === this.settings.google_client_id ) {
				return;
			}
			if ( this.node.find( '#g_id_onload' ).length > 0 ) {
				$(document).on( 'pp_lf_google_signin', this._initGoogleApi.bind( this ) );

				this.node.find( '.pp-google-login-button' ).on( 'click', this._googleLoginClick.bind( this ) );
			}
		},

		_initGoogleApi: function( e, googleSignInResponse ) {
			var self = this,
				theForm = this.node.find( '.pp-login-form' ),
				redirect = theForm.find( 'input[name="redirect_to"]' );

			if ( '' === self.settings.google_client_id ) {
				return;
			}

			var args = {
				action: 'pp_lf_process_social_login',
				provider: 'google',
				page_url: self.settings.page_url,
				hash: googleSignInResponse.credential,
				nonce: self._getNonce(),
			};

			if ( redirect.length > 0 && '' !== redirect.val() ) {
				args['redirect'] = redirect.val();
			}

			self._ajax( args, function( response ) {
				if ( ! response.success ) {
					if ( 'undefined' !== typeof response.data.code ) {
						theForm.find( '.pp-lf-error' ).remove();
						$('<span class="pp-lf-error">').appendTo( theForm ).html( response.data.message );
					} else {
						console.error( response.data );
					}
					self._enableForm();
				} else {
					if ( response.data.redirect_url ) {
						var hostUrl = location.protocol + '//' + location.host;
						var redirectUrl = '';

						if ( '' === response.data.redirect_url.split( hostUrl )[0] ) {
							redirectUrl = response.data.redirect_url.split( hostUrl )[1];
						} else {
							redirectUrl = response.data.redirect_url.split( hostUrl )[0];
						}

						if ( redirectUrl === location.href.split( hostUrl )[1] ) {
							window.location.reload();
						} else {
							window.location.href = response.data.redirect_url;
						}
					} else {
						window.location.reload();
					}
				}

				self.isGoogleLoginClicked = false;
			} );
		},

		_googleLoginClick: function() {
			this.isGoogleLoginClicked = true;
			this._disableForm();
		},

		_initPasswordToggle: function() {
			this.node.find( '.pp-lf-toggle-pw' ).on( 'click', function(e) {
				e.preventDefault();

				if ( $(this).hasClass( 'pwd-toggled' ) ) {
					$(this).removeClass( 'pwd-toggled' );
					$(this).parent().find('[type="text"]').attr('type', 'password');
					$(this).attr( 'aria-label', 'Show password' );
				} else {
					$(this).addClass( 'pwd-toggled' );
					$(this).parent().find('[type="password"]').attr('type', 'text');
					$(this).attr( 'aria-label', 'Hide password' );
				}
			} );
		},

		_loginFormSubmit: function(e) {
			e.preventDefault();

			var theForm 		= $(e.target),
				username 		= theForm.find( 'input[name="log"]' ),
				password 		= theForm.find( 'input[name="pwd"]' ),
				remember 		= theForm.find( 'input[name="rememberme"]' ),
				redirect 		= theForm.find( 'input[name="redirect_to"]' ),
				reauth 			= theForm.find( 'input[name="reauth"]' ),
				reCaptchaField 	= theForm.find( '.pp-grecaptcha' ),
				reCaptchaValue 	= reCaptchaField.attr( 'data-pp-grecaptcha-response' ),
				hCaptchaField 	= theForm.find( '.pp-hcaptcha' ),
				hCaptchaValue 	= hCaptchaField.find('iframe').attr('data-hcaptcha-response'),
				self 			= this;
		
			username.parents('.pp-login-form-field').find( '.pp-lf-error' ).remove();
			password.parents('.pp-login-form-field').find( '.pp-lf-error' ).remove();
			reCaptchaField.parent().find( '.pp-lf-error' ).remove();
			hCaptchaField.parent().find( '.pp-lf-error' ).remove();

			// Validate username.
			if ( '' === username.val().trim() ) {
				$('<span class="pp-lf-error">').insertAfter( username.parent() ).html( this.messages.empty_username );
				return;
			}

			// Validate password.
			if ( '' === password.val() ) {
				$('<span class="pp-lf-error">').insertAfter( password.parent() ).html( this.messages.empty_password );
				return;
			}

			// Validate reCAPTCHA.
			if ( reCaptchaField.length > 0 ) {
				if ( 'undefined' === typeof reCaptchaValue || reCaptchaValue === false ) {
					if ( 'normal' == reCaptchaField.data( 'validate' ) ) {
						$('<span class="pp-lf-error">').insertAfter( reCaptchaField ).html( this.messages.empty_recaptcha );
						return;
					} else if ( 'invisible' == reCaptchaField.data( 'validate' ) ) {
						// Invoke the reCAPTCHA check.
						if ( 'undefined' !== typeof reCaptchaField.data( 'action' ) ) {
							// V3
							grecaptcha.execute( reCaptchaField.data( 'widgetid' ), {action: reCaptchaField.data( 'action' )} );
						}
						else {
							// V2
							grecaptcha.execute( reCaptchaField.data( 'widgetid' ) );
						}
					}
				}
			}

			// Validate if hCaptcha is enabled and checked
			if ( hCaptchaField.length > 0 ) { 
				if ( 'undefined' === typeof hCaptchaValue || hCaptchaValue === false || hCaptchaValue === '' ) {
					$('<span class="pp-lf-error">').insertAfter( hCaptchaField ).html( this.messages.empty_recaptcha );
					return;
				}
			}

			var formData = new FormData( theForm[0] );

			formData.append( 'action', 'pp_lf_process_login' );
			formData.append( 'page_url', this.settings.page_url );
			formData.append( 'username', username.val() );
			formData.append( 'password', password.val() );

			if ( redirect.length > 0 && '' !== redirect.val() ) {
				formData.append( 'redirect', redirect.val() );
			}

			if ( reauth.length > 0 && '' !== reauth.val() ) {
				formData.append( 'reauth', 1 );
			}

			if ( remember.length > 0 && remember.is(':checked') ) {
				formData.append( 'remember', '1' );
			}

			if ( reCaptchaField.length > 0 ) {
				formData.append( 'recaptcha', true );
				formData.append( 'recaptcha_validate', reCaptchaField.data( 'validate' ) );
				if ( reCaptchaField.data( 'invisible' ) ) {
					formData.append( 'recaptcha_invisible', true );
				}
			}
			if ( reCaptchaValue ) {
				formData.append( 'recaptcha_response', reCaptchaValue );
			}

			if ( hCaptchaField.length > 0 ) {
				formData.append( 'hcaptcha', true );
			}
			if ( 'undefined' !== typeof hCaptchaValue || hCaptchaValue !== false ) {
				formData.append( 'hcaptcha_response', hCaptchaValue );
			}

			// Wordfence 2FA.
			if ( self.node.find( 'input[name="wfls-token"]' ).length > 0 ) {
				formData.append( 'wfls-token', self.node.find( 'input[name="wfls-token"]' ).val().toString() );
			}

			this._disableForm();

			this._ajax( formData, function( response ) {
				if ( ! response.success ) {
					self._enableForm();
					theForm.find( '.pp-lf-error' ).remove();
					$('<span class="pp-lf-error">').appendTo( theForm ).html( response.data );
				} else {
					if ( response.data.wordfence_2fa ) {
						self._enableForm();

						// Remove captcha fields as we already have verified.
						self.node.find( '.pp-field-type-recaptcha' ).remove();
						self.node.find( '.pp-field-type-hcaptcha' ).remove();

						self.node.find( '.pp-login-form-wrap' ).addClass( 'wordfence-2fa' );
						$('<p>' + response.data.field_desc + '</p>').insertBefore( self.node.find( '.pp-login-form-fields' ) );
						var authField = '<div class="pp-login-form-field pp-field-group wordfence_2fa">\
							<label for="wfls-token">'+ response.data.field_label +'</label>\
							<div class="pp-field-inner">\
								<input type="text" name="wfls-token" id="wfls-token" class="pp-login-form--input" value="" size="6" autocomplete="one-time-code">\
							</div>\
						</div>';
						$( authField ).insertBefore(
							self.node.find( '.pp-field-type-submit' )
						);
						return;
					}
					var reload = function() {
						if ( 'URLSearchParams' in window ) {
							var query = new URLSearchParams( location.search );
							query.delete( 'reset_success' );

							window.location.href = '' !== query.toString() ? window.location.pathname + '?' + query.toString() : window.location.pathname;
						} else {
							window.location.reload();
						}
					};

					if ( response.data.redirect_url ) {
						var hostUrl = location.protocol + '//' + location.host;
						var redirectUrl = '';

						if ( '' === response.data.redirect_url.split( hostUrl )[0] ) {
							redirectUrl = response.data.redirect_url.split( hostUrl )[1];
						} else {
							redirectUrl = response.data.redirect_url.split( hostUrl )[0];
						}

						if ( redirectUrl === location.href.split( hostUrl )[1] ) {
							reload();
						} else {
							window.location.href = response.data.redirect_url;
						}
					} else {
						reload();
					}
				}
			} );
		},

		_lostPassFormSubmit: function(e) {
			e.preventDefault();

			var theForm = $(e.target),
				username = theForm.find( 'input[name="user_login"]' ),
				reCaptchaField 	= theForm.find( '.pp-grecaptcha' ),
				reCaptchaValue 	= reCaptchaField.attr( 'data-pp-grecaptcha-response' ),
				hCaptchaField 	= theForm.find( '.pp-hcaptcha' ),
				hCaptchaValue 	= hCaptchaField.find('iframe').attr('data-hcaptcha-response'),
				self = this;

			username.parent().find( '.pp-lf-error' ).remove();
			reCaptchaField.parent().find( '.pp-lf-error' ).remove();
			hCaptchaField.parent().find( '.pp-lf-error' ).remove();

			if ( '' === username.val().trim() ) {
				$('<span class="pp-lf-error">').insertAfter( username ).html( this.messages.empty_username );
				return;
			}

			// Validate reCAPTCHA.
			if ( reCaptchaField.length > 0 ) {
				if ( 'undefined' === typeof reCaptchaValue || reCaptchaValue === false ) {
					if ( 'normal' == reCaptchaField.data( 'validate' ) ) {
						$('<span class="pp-lf-error">').insertAfter( reCaptchaField ).html( this.messages.empty_recaptcha );
						return;
					} else if ( 'invisible' == reCaptchaField.data( 'validate' ) ) {
						// Invoke the reCAPTCHA check.
						if ( 'undefined' !== typeof reCaptchaField.data( 'action' ) ) {
							// V3
							grecaptcha.execute( reCaptchaField.data( 'widgetid' ), {action: reCaptchaField.data( 'action' )} );
						}
						else {
							// V2
							grecaptcha.execute( reCaptchaField.data( 'widgetid' ) );
						}
					}
				}
			}

			// Validate if hCaptcha is enabled and checked
			if ( hCaptchaField.length > 0 ) { 
				if ( 'undefined' === typeof hCaptchaValue || hCaptchaValue === false || hCaptchaValue === '' ) {
					$('<span class="pp-lf-error">').insertAfter( hCaptchaField ).html( this.messages.empty_recaptcha );
					return;
				}
			}

			var formData = new FormData( theForm[0] );

			formData.append( 'action', 'pp_lf_process_lost_pass' );
			formData.append( 'page_url', this.settings.page_url );

			if ( reCaptchaField.length > 0 ) {
				formData.append( 'recaptcha', true );
				formData.append( 'recaptcha_validate', reCaptchaField.data( 'validate' ) );
				if ( reCaptchaField.data( 'invisible' ) ) {
					formData.append( 'recaptcha_invisible', true );
				}
			}
			if ( reCaptchaValue ) {
				formData.append( 'recaptcha_response', reCaptchaValue );
			}

			if ( hCaptchaField.length > 0 ) {
				formData.append( 'hcaptcha', true );
			}
			if ( 'undefined' !== typeof hCaptchaValue || hCaptchaValue !== false ) {
				formData.append( 'hcaptcha_response', hCaptchaValue );
			}

			this._disableForm();

			this._ajax( formData, function( response ) {
				self._enableForm();
				if ( ! response.success ) {
					username.parent().find( '.pp-lf-error' ).remove();
					$('<span class="pp-lf-error">').insertAfter( username ).html( response.data );
				} else {
					$('<p class="pp-lf-success">').insertAfter( theForm ).html( self.messages.email_sent );
					theForm.hide();
				}
			} );
		},

		_resetPassFormSubmit: function(e) {
			e.preventDefault();

			var theForm = $(e.target),
				password_1 = theForm.find( 'input[name="password_1"]' ),
				password_2 = theForm.find( 'input[name="password_2"]' ),
				self	= this;

			password_1.parent().find( '.pp-lf-error' ).remove();
			password_2.parent().find( '.pp-lf-error' ).remove();

			if ( '' === password_1.val() ) {
				$('<span class="pp-lf-error">').insertAfter( password_1 ).html( this.messages.empty_password_1 );
				return;
			}

			if ( '' === password_2.val() ) {
				$('<span class="pp-lf-error">').insertAfter( password_2 ).html( this.messages.empty_password_2 );
				return;
			}

			var formData = new FormData( theForm[0] );

			formData.append( 'action', 'pp_lf_process_reset_pass' );
			formData.append( 'page_url', this.settings.page_url );

			this._disableForm();

			this._ajax( formData, function( response ) {
				self._enableForm();
				if ( ! response.success ) {
					theForm.find( '.pp-lf-error' ).remove();
					$('<span class="pp-lf-error">').appendTo( theForm ).html( response.data );
				} else {
					if ( 'URLSearchParams' in window ) {
						var query = new URLSearchParams( location.search );
						query.delete( 'reset_pass' );
						query.delete( 'key' );
						query.delete( 'id' );
						query.append( 'reset_success', '1' );

						window.location.href = window.location.pathname + '?' + query.toString();
					} else {
						$('<p class="pp-lf-success">').insertAfter( theForm ).html( self.messages.reset_success );
						theForm.hide();
					}
				}
			} );
		},

		_enableForm: function() {
			this.node.find( '.pp-login-form-wrap' ).removeClass( 'pp-event-disabled' );
		},

		_disableForm: function() {
			this.node.find( '.pp-login-form-wrap' ).addClass( 'pp-event-disabled' );
		},

		_getNonce: function() {
			return this.node.find( '.pp-login-form input[name="pp-lf-login-nonce"]' ).val();
		},

		_ajax: function( data, callback ) {
			var ajaxArgs = {
				type: 'POST',
				url: bb_powerpack.getAjaxUrl(),
				data: data,
				dataType: 'json',
				success: function( response ) {
					if ( 'function' === typeof callback ) {
						callback( response );
					}
				}
			};

			if ( 'undefined' === typeof data.provider ) {
				ajaxArgs.processData = false,
				ajaxArgs.contentType = false;
			}

			$.ajax( ajaxArgs ).fail( function( jqXhr ) {
				if ( 503 === jqXhr.status ) {
					if ( null !== jqXhr.responseText.match( /<!DOCTYPE|<!doctype/ ) ) {
						document.write( jqXhr.responseText );
					}
				}
			} );
		},
	};

})(jQuery);