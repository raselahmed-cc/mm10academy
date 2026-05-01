( function( $ ) {

	FLBuilder.registerModuleHelper( 'pp-subscribe-form', {

		rules: {
			btn_text: {
				required: true
			},
			btn_font_size: {
				required: true,
				number: true
			},
			btn_padding: {
				required: true,
				number: true
			},
			btn_border_radius: {
				required: true,
				number: true
			},
			service: {
				required: true
			}
		},

		init: function()
		{
			var form      = $( '.fl-builder-settings' ),
				action    = form.find( 'select[name=success_action]' );

			// CSS class fix in settings form.
			$('.pp-field-css-class').val('pp_subscribe_' + form.data('node'));

			this._actionChanged();

			action.on( 'change', this._actionChanged );

			this._typeChanged();
			$('.fl-builder-settings select[name="box_type"]').on('change', this._typeChanged);

			// Toggle reCAPTCHA display
            this._toggleReCaptcha();
			// Toggle hCaptcha display
            this._toggleHCaptcha();

			$('input[name=recaptcha_toggle]').on( 'change', this._toggleReCaptcha.bind( this ) );
            $('input[name=hcaptcha_toggle]').on( 'change', this._toggleHCaptcha.bind( this ) );
            $('select[name=recaptcha_validate_type]').on( 'change', this._toggleReCaptcha.bind( this ) );
            $('input[name=recaptcha_theme]').on( 'change', this._toggleReCaptcha.bind( this ) );

            // Render reCAPTCHA after layout rendered via AJAX
            if (window.onLoadPPReCaptcha) {
                $(FLBuilder._contentClass).on('fl-builder.layout-rendered', onLoadPPReCaptcha);
            }
			// Render hCaptcha after layout rendered via AJAX
            if (window.onLoadPPHCaptcha) {
                $(FLBuilder._contentClass).on('fl-builder.layout-rendered', onLoadPPHCaptcha);
            }
		},

		submit: function()
		{
			var form       = $( '.fl-builder-settings' ),
				service    = form.find( '.fl-builder-service-select' ),
				serviceVal = service.val(),
				account    = form.find( '.fl-builder-service-account-select' ),
				list       = form.find( '.fl-builder-service-list-select' );

			if ( 0 === account.length ) {
				FLBuilder.alert( FLBuilderStrings.subscriptionModuleConnectError );
				return false;
			}
			else if ( '' == account.val() || 'add_new_account' == account.val() ) {
				FLBuilder.alert( FLBuilderStrings.subscriptionModuleAccountError );
				return false;
			}
			else if ( ( 0 === list.length || '' == list.val() ) && 'email-address' != serviceVal && 'sendy' != serviceVal ) {

				if ( 'drip' == serviceVal || 'hatchbuck' == serviceVal ) {
					FLBuilder.alert( FLBuilderStrings.subscriptionModuleTagsError );
				}
				else {
					FLBuilder.alert( FLBuilderStrings.subscriptionModuleListError );
				}

				return false;
			}

			return true;
		},

		_actionChanged: function()
		{
			var form      = $( '.fl-builder-settings' ),
				action    = form.find( 'select[name=success_action]' ).val(),
				url       = form.find( 'input[name=success_url]' );

			url.rules('remove');

			if ( 'redirect' == action ) {
				url.rules( 'add', { required: true } );
			}
		},

		_typeChanged: function()
		{
			var selector = '#fl-builder-settings-section-form_bg_setting, #fl-builder-settings-section-form_box_shadow, #fl-field-box_border_radius, #fl-field-form_border_radius';
			if ( $('.fl-builder-settings select[name="box_type"]').val() === 'welcome_gate' ) {
				$( selector ).hide();
			} else {
				$( selector ).show();
			}
		},

		/**
		 * Custom preview method for reCAPTCHA settings
		 *
		 * @param  object event  The event type of where this method been called
		 */
		 _toggleReCaptcha: function (event) {
            var form = $('.fl-builder-settings'),
                nodeId = form.attr('data-node'),
                toggle = form.find('input[name=recaptcha_toggle]'),
                captchaKey = '',
                captType = form.find('select[name=recaptcha_validate_type]').val(),
                theme = form.find('input[name=recaptcha_theme]').val(),
                reCaptcha = $('.fl-node-' + nodeId).find('.pp-grecaptcha'),
                reCaptchaId = nodeId + '-pp-grecaptcha',
                target = typeof event !== 'undefined' ? $(event.currentTarget) : null,
                inputEvent = target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_site_key',
                selectEvent = target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_toggle',
                typeEvent = target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_validate_type',
                themeEvent = target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_theme',
                scriptTag = $('<script>'),
				isRender = false;
				
			if ( 'undefined' !== typeof pp_recaptcha ) {
				captchaKey = pp_recaptcha.site_key;
			}

            if ('show' === toggle.val() && captchaKey.length) {

				// Add library if not exists
				if (0 === $('script#g-recaptcha-api').length) {
					scriptTag
						.attr('src', 'https://www.google.com/recaptcha/api.js?onload=onLoadPPReCaptcha&render=explicit')
						.attr('type', 'text/javascript')
						.attr('id', 'g-recaptcha-api')
						.attr('async', 'async')
						.attr('defer', 'defer')
						.appendTo('body');
				}

                // reCAPTCHA is not yet exists
                if (0 === reCaptcha.length) {
                    isRender = true;
                }
                // If reCAPTCHA element exists, then reset reCAPTCHA if existing key does not matched with the input value
                else if ((inputEvent || selectEvent || typeEvent || themeEvent) && (reCaptcha.data('sitekey') != captchaKey || reCaptcha.data('validate') != captType || reCaptcha.data('theme') != theme)
                ) {
                    reCaptcha.parent().remove();
                    isRender = true;
                }
                else {
                    reCaptcha.parent().show();
                }

                if (isRender) {
                    this._renderReCaptcha(nodeId, reCaptchaId, captchaKey, captType, theme);
				}
            }
            else if ('show' === toggle.val() && captchaKey.length === 0 && reCaptcha.length > 0) {
                reCaptcha.parent().remove();
            }
            else if ('hide' === toggle.val() && reCaptcha.length > 0) {
                reCaptcha.parent().hide();
            }
        },

		_toggleHCaptcha: function(event) {
			var form = $('.fl-builder-settings'),
				nodeId = form.attr('data-node'),
				toggle = form.find('input[name=hcaptcha_toggle]'),
				captchaKey = '',
				hCaptcha = $('.fl-node-' + nodeId).find('.h-captcha'),
				hCaptchaId = nodeId + '-pp-hcaptcha',
				target = typeof event !== 'undefined' ? $(event.currentTarget) : null,
				selectEvent = target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'hcaptcha_toggle',
				scriptTag = $('<script>'),
				isRender = false;

			if ( 'undefined' !== typeof pp_hcaptcha ) {
				captchaKey = pp_hcaptcha.site_key;
			}

			if ('show' === toggle.val() && captchaKey.length) {
				// Add API script if not exists
				if (0 === $('script#h-captcha-api').length) {
					scriptTag
						.attr('src', 'https://hcaptcha.com/1/api.js?onload=onLoadPPHCaptcha&render=explicit&recaptchacompat=off')
						.attr('type', 'text/javascript')
						.attr('id', 'h-captcha-api')
						.attr('async', 'async')
						.attr('defer', 'defer')
						.appendTo('body');
				}

                // hCaptcha is not yet exists
                if (0 === hCaptcha.length) {
                    isRender = true;
                }
                // If hCaptcha element exists, then reset hCaptcha if existing key does not matched with the input value
                else if (selectEvent && hCaptcha.data('sitekey') != captchaKey ) {
                    hCaptcha.parent().remove();
                    isRender = true;
                }
                else {
                    hCaptcha.parent().show();
                }

                if (isRender) {
                    this._renderHCaptcha(nodeId, hCaptchaId, captchaKey);
				}
            }
            else if ('show' === toggle.val() && captchaKey.length === 0 && hCaptcha.length > 0) {
                hCaptcha.parent().remove();
            }
            else if ('hide' === toggle.val() && hCaptcha.length > 0) {
                hCaptcha.parent().hide();
            }
		},

		/**
		 * Render Google reCAPTCHA
		 *
		 * @param  string nodeId  		The current node ID
		 * @param  string reCaptchaId  	The element ID to render reCAPTCHA
		 * @param  string reCaptchaKey  The reCAPTCHA Key
		 * @param  string reCaptType  	Checkbox or invisible
		 * @param  string theme         Light or dark
		 */
        _renderReCaptcha: function (nodeId, reCaptchaId, reCaptchaKey, reCaptType, theme) {
            var captchaField = $('<div class="pp-form-field pp-recaptcha">'),
                captchaElement = $('<div id="' + reCaptchaId + '" class="pp-grecaptcha">'),
				form 		   = $( '.fl-node-'+ nodeId ).find( '.pp-subscribe-form' ),
                widgetID;

            captchaElement.attr('data-sitekey', reCaptchaKey);
            captchaElement.attr('data-validate', reCaptType);
            captchaElement.attr('data-theme', theme);
			captchaField.html( captchaElement );

			if ( form.hasClass('pp-subscribe-form-stacked') ) {
				captchaField.insertBefore( form.find('.pp-form-button') );
			}
			else if ( form.hasClass('pp-subscribe-form-inline') ) {
				captchaField.insertAfter( form.find('.pp-form-button') );
			}

            widgetID = grecaptcha.render(reCaptchaId, {
                sitekey: reCaptchaKey,
                size: reCaptType,
                theme: theme
            });
            captchaElement.attr('data-widgetid', widgetID);
        },

		/**
		 * Render hCaptcha
		 *
		 * @param  string nodeId  		The current node ID
		 * @param  string hCaptchaId  	The element ID to render hCaptcha
		 * @param  string hCaptchaKey  The hCaptcha Key
		 * @since 2.x
		 */
		 _renderHCaptcha: function (nodeId, hCaptchaId, hCaptchaKey) {
            var captchaField   = $('<div class="pp-form-field pp-hcaptcha">'),
                captchaElement = $('<div id="' + hCaptchaId + '" class="h-captcha">'),
				form 		   = $( '.fl-node-'+ nodeId ).find( '.pp-subscribe-form' ),
                widgetID;

            captchaElement.attr('data-sitekey', hCaptchaKey);
			captchaField.html( captchaElement );

            if ( form.hasClass('pp-subscribe-form-stacked') ) {
				captchaField.insertBefore( form.find('.pp-form-button') );
			}
			else if ( form.hasClass('pp-subscribe-form-inline') ) {
				captchaField.insertAfter( form.find('.pp-form-button') );
			}

            widgetID = hcaptcha.render(hCaptchaId, {
                sitekey: hCaptchaKey
            });
            captchaElement.attr('data-hcaptcha-widget-id', widgetID);
        }

	});

})(jQuery);
