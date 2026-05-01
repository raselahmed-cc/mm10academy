(function($){

	FLBuilder.registerModuleHelper('pp-login-form', {
		init: function() {
            // Toggle reCAPTCHA display
            $('input[name=enable_recaptcha]').on( 'change', this._toggleReCaptcha.bind( this ) );
            $('select[name=recaptcha_validate_type]').on( 'change', this._toggleReCaptcha.bind( this ) );
            $('input[name=recaptcha_theme]').on( 'change', this._toggleReCaptcha.bind( this ) );

			// Toggle hCaptcha display
			$('input[name=enable_hcaptcha]').on( 'change', this._toggleHCaptcha.bind( this ) );

            // Render reCAPTCHA after layout rendered via AJAX
            if (window.onLoadPPReCaptcha) {
                $(FLBuilder._contentClass).on('fl-builder.layout-rendered', onLoadPPReCaptcha);
            }

			// Render hCaptcha after layout rendered via AJAX
            if (window.onLoadPPHCaptcha) {
                $(FLBuilder._contentClass).on('fl-builder.layout-rendered', onLoadPPHCaptcha);
            }
		},

        /**
		 * Custom preview method for reCAPTCHA settings
		 *
		 * @param  object event  The event type of where this method been called
		 * @since 2.8.0
		 */
        _toggleReCaptcha: function (event) {
			if ( 'undefined' === typeof pp_recaptcha ) {
				return;
			}
            var form = $('.fl-builder-settings'),
                nodeId = form.attr('data-node'),
                enabled = form.find('input[name=enable_recaptcha]'),
                captchaKey = pp_recaptcha.site_key,
                captType = form.find('select[name=recaptcha_validate_type]').val(),
                theme = form.find('input[name=recaptcha_theme]').val(),
                reCaptcha = $('.fl-node-' + nodeId).find('.pp-grecaptcha'),
                reCaptchaId = nodeId + '-pp-grecaptcha',
                target = typeof event !== 'undefined' ? $(event.currentTarget) : null,
                typeEvent = target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_validate_type',
                themeEvent = target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_theme',
                scriptTag = $('<script>'),
				isRender = false;
				
			if ( 'invisible_v3' === captType ) {
				captType = 'invisible';
				captchaKey = pp_recaptcha.v3_site_key;
			}

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

            if ('yes' === enabled.val() && captchaKey.length) {

                // reCAPTCHA is not yet exists
                if (0 === reCaptcha.length) {
                    isRender = true;
                }
                // If reCAPTCHA element exists, then reset reCAPTCHA if existing key does not matched with the input value
                else if ((typeEvent || themeEvent) && (reCaptcha.data('sitekey') != captchaKey || reCaptcha.data('validate') != captType || reCaptcha.data('theme') != theme)
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
            else if ('yes' === enabled.val() && captchaKey.length === 0 && reCaptcha.length > 0) {
                reCaptcha.parent().remove();
            }
            else if ('yes' !== enabled.val() && reCaptcha.length > 0) {
                reCaptcha.parent().hide();
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
		 * @since 2.8.0
		 */
        _renderReCaptcha: function (nodeId, reCaptchaId, reCaptchaKey, reCaptType, theme) {
            var captchaField = $('<div class="pp-login-form-field pp-field-group pp-field-type-recaptcha">'),
                captchaElement = $('<div id="' + reCaptchaId + '" class="pp-grecaptcha">'),
                widgetID;

            captchaElement.attr('data-sitekey', reCaptchaKey);
            captchaElement.attr('data-validate', reCaptType);
            captchaElement.attr('data-theme', theme);

            // Append recaptcha element to an appended element
            captchaField
                .html(captchaElement)
                .insertBefore($('.fl-node-' + nodeId).find('.pp-login-form .pp-login-form-fields > .pp-field-type-submit'));

            widgetID = grecaptcha.render(reCaptchaId, {
                sitekey: reCaptchaKey,
                size: reCaptType,
                theme: theme
            });
            captchaElement.attr('data-widgetid', widgetID);
        },

		_toggleHCaptcha: function(event) {
			var form = $('.fl-builder-settings'),
				nodeId = form.attr('data-node'),
				toggle = form.find('input[name=enable_hcaptcha]'),
				captchaKey = '',
				hCaptcha = $('.fl-node-' + nodeId).find('.pp-hcaptcha'),
				hCaptchaId = nodeId + '-pp-hcaptcha',
				target = typeof event !== 'undefined' ? $(event.currentTarget) : null,
				selectEvent = target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'enable_hcaptcha',
				scriptTag = $('<script>'),
				isRender = false;

			if ( 'undefined' !== typeof pp_hcaptcha ) {
				captchaKey = pp_hcaptcha.site_key;
			}

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

			if ('yes' === toggle.val() && captchaKey.length) {

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
            else if ('yes' === toggle.val() && captchaKey.length === 0 && hCaptcha.length > 0) {
                hCaptcha.parent().remove();
            }
            else if ('no' === toggle.val() && hCaptcha.length > 0) {
                hCaptcha.parent().hide();
            }
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
            var captchaField = $('<div class="pp-login-form-field pp-field-group pp-field-type-hcaptcha">'),
                captchaElement = $('<div id="' + hCaptchaId + '" class="pp-hcaptcha h-captcha">'),
                widgetID;

            captchaElement.attr('data-sitekey', hCaptchaKey);

            // Append hCaptcha element to an appended element
            captchaField
                .html(captchaElement)
                .insertBefore($('.fl-node-' + nodeId).find('.pp-login-form .pp-login-form-fields > .pp-field-type-submit'));

            widgetID = hcaptcha.render(hCaptchaId, {
                sitekey: hCaptchaKey
            });
            captchaElement.attr('data-hcaptcha-widget-id', widgetID);
        }
	});

})(jQuery);
