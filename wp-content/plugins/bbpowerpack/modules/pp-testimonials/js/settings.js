(function($){

	FLBuilder.registerModuleHelper('pp-testimonials', {

		rules: {
			'testimonials[]': {
				required: true
			},
			pause: {
				number: true,
				required: true
			},
			speed: {
				number: true,
				required: true
			},
			'border_width': {
				number: true
			},
			'border_radius': {
				number: true
			},
			'heading_font_size': {
                number: true
            },
            'title_font_size': {
                number: true
            },
            'subtitle_font_size': {
                number: true
            },
            'text_font_size': {
                number: true
            },
		},

		init: function() {
			$('input[name="testimonial_layout"]').on( 'change', this._onLayoutChange.bind( this ) );
		},

		_onLayoutChange: function() {
			var preview = FLBuilder.preview;
			preview.delay( 1000, preview.preview.bind( preview ) );
		}
	});

})(jQuery);
