(function($){

    FLBuilder._registerModuleHelper('pp-gravity-form', {

		_forms: '',

        rules: {
            'form_border_width': {
                number: true
            },
            'form_padding': {
                number: true
            },
            'title_font_size': {
                number: true
            },
            'description_font_size': {
                number: true
            },
            'label_font_size': {
                number: true
            },
            'input_font_size': {
                number: true
            },
            'input_field_border_width': {
                number: true
            },
            'input_field_border_radius': {
                number: true
            },
            'input_field_padding': {
                number: true
            },
            'input_field_margin': {
                number: true
            },
            'button_width_size': {
                number: true
            },
            'button_font_size': {
                number: true
            },
            'button_padding_top_bottom': {
                number: true
            },
            'button_padding_top_bottom': {
                number: true
            },
            'button_padding_left_right': {
                number: true
            },
            'button_border_radius': {
                number: true
            },
            'button_border_width': {
                number: true
            },
            'form_error_input_border_width': {
                number: true
            },
            'validation_error_font_size': {
                number: true
            }
		},
		
		init: function() {
			this._setForms();
		},

		_getForms: function(callback) {
			$.post(
				bb_powerpack.getAjaxUrl(),
				{
					action: 'pp_gf_forms_dropdown_html',
				},
				function( response ) {
					callback( response );
				}
			);
		},

		_setForms: function()
		{
			var form = $('.fl-builder-settings'),
				select = form.find( 'select[name="select_form_field"]' ),
				value = '', self = this;

			if ( 'undefined' !== typeof FLBuilderSettingsForms && 'undefined' !== typeof FLBuilderSettingsForms.config ) {
				if ( "pp-gravity-form" === FLBuilderSettingsForms.config.id ) {
					value = FLBuilderSettingsForms.config.settings['select_form_field'];
				}
			}

			if ( this._forms !== '' ) {
				select.html( this._forms );
				select.find( 'option[value="' + value + '"]').attr('selected', 'selected');

				return;
			}

			this._getForms(function(data) {
				self._forms = data;
				select.html( data );
				if ( '' !== value ) {
					select.find( 'option[value="' + value + '"]').attr('selected', 'selected');
				}
			});
		},
    });

})(jQuery);
