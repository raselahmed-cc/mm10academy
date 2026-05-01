(function($){

	FLBuilder.registerModuleHelper('pp-instagram-feed', {
		init: function() {
			var form = $('.fl-builder-settings');
			var self = this;

			this._toggleFields();

			form.find( 'input[name=use_api]' ).on('change', function() {
				self._toggleFields();
			});

			if ( form.find('input[name="image_overlay_type"]').val() === 'gradient' ) {
				form.find('#fl-field-image_overlay_angle').show();
			} else {
				form.find('#fl-field-image_overlay_angle').hide();
			}

			form.find('input[name="image_overlay_type"]').on('change', function() {
				if ( $(this).val() === 'gradient' ) {
					form.find('#fl-field-image_overlay_angle').show();
				} else {
					form.find('#fl-field-image_overlay_angle').hide();
				}
			});
			
			if ( form.find('input[name="image_hover_overlay_type"]').val() === 'gradient' ) {
				form.find('#fl-field-image_hover_overlay_angle').show();
			} else {
				form.find('#fl-field-image_hover_overlay_angle').hide();
			}

			form.find('input[name="image_hover_overlay_type"]').on('change', function() {
				if ( $(this).val() === 'gradient' ) {
					form.find('#fl-field-image_hover_overlay_angle').show();
				} else {
					form.find('#fl-field-image_hover_overlay_angle').hide();	
				}
			});
		},

		submit: function() {
			return true;
		},

		_toggleFields: function() {
			var form = $( '.fl-builder-settings' );
			if ( form.find( 'input[name=use_api]' ).val() === 'no' ) {
				form.find('#fl-field-feed_by_tags').hide();
				form.find('#fl-field-tag_name').hide();
			} else {
				form.find('#fl-field-feed_by_tags').show();
				if ( form.find( 'input[name=feed_by_tags]' ).val() === 'yes' ) {
					form.find('#fl-field-tag_name').show();
				}
			}
		}
	});

})(jQuery);