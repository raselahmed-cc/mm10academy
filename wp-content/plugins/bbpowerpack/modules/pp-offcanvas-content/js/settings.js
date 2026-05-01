(function($){
	var nodeId = '';
	FLBuilder.registerModuleHelper('pp-offcanvas-content', {

		rules: {
			item_spacing: {
				required: true,
				number: true
			}
		},

		_templates: {
			module: '',
			row: '',
			layout: ''
		},

		init: function() {
			nodeId = $( '.fl-builder-settings' ).data( 'node' );
			//$( 'select[name="content_type"]' ).on( 'change', this._contentTypeChange.bind( this ) );
			//this._contentTypeChange();

			$('body').on( 'change', '.fl-builder-settings select[name="content_type"]', this._contentTemplateChange.bind( this ) );
			$('body').on( 'change', '.fl-builder-settings select[name="content_module"]', this._contentTemplateChange.bind( this ) );
			$('body').on( 'change', '.fl-builder-settings select[name="content_row"]', this._contentTemplateChange.bind( this ) );
			$('body').on( 'change', '.fl-builder-settings select[name="content_layout"]', this._contentTemplateChange.bind( this ) );

			$('body').on( 'click', '.fl-builder-settings .content_edit', this._buttonClick.bind( this ) );

			this._contentTemplateChange();

			$('.pp-modal-node-id').text( nodeId );
			$('.pp-modal-hide-js').val( 'pp_offcanvas_' + nodeId + '._close()' );
		},

		_contentTypeChange: function()
		{
			var type = $( 'select[name="content_type"]' ).val();

			if ( 'module' === type ) {
				this._setTemplates('module');
			}
			if ( 'row' === type ) {
				this._setTemplates('row');
			}
			if ( 'layout' === type ) {
				this._setTemplates('layout');
			}
		},

		_contentTemplateChange: function() {
			if ( 'module' === $('.fl-builder-settings select[name="content_type"]').val() ) {
				var postId = $('.fl-builder-settings select[name="content_module"]').val();
				$('.fl-builder-settings .content_edit').attr( 'href', location.origin + '?p=' + postId + '&fl_builder' );
			}
			if ( 'row' === $('.fl-builder-settings select[name="content_type"]').val() ) {
				var postId = $('.fl-builder-settings select[name="content_row"]').val();
				$('.fl-builder-settings .content_edit').attr( 'href', location.origin + '?p=' + postId + '&fl_builder' );
			}
			if ( 'layout' === $('.fl-builder-settings select[name="content_type"]').val() ) {
				var postId = $('.fl-builder-settings select[name="content_layout"]').val();
				$('.fl-builder-settings .content_edit').attr( 'href', location.origin + '?p=' + postId + '&fl_builder' );
			}
		},
		
		_buttonClick: function(e) {
			var link = $(e.target).attr( 'href' );

			if ( '#' !== link && '' !== link ) {
				window.open( link, '_blank' );
			}
		},

		_getTemplates: function(type, callback)
		{
			if ( 'undefined' === typeof type ) {
				return;
			}

			if ( 'undefined' === typeof callback ) {
				return;
			}

			$.post(
				bb_powerpack.getAjaxUrl(),
				{
					action: 'pp_get_saved_templates',
					type: type,
					currentPost: FLBuilderConfig ? FLBuilderConfig.postId : 0
				},
				function( response ) {
					callback(response);
				}
			);
		},

		_setTemplates: function(type)
		{
			var form = $('.fl-builder-settings'),
				select = form.find( 'select[name="content_' + type + '"]' ),
				value = '',
				self = this;

			value = FLBuilderSettingsForms.config.settings['content_' + type];

			if ( this._templates[type] !== '' ) {
				select.html( this._templates[type] );
				select.find( 'option[value="' + value + '"]').attr('selected', 'selected');

				return;
			}

			this._getTemplates(type, function(data) {
				var response = JSON.parse( data );

				if ( response.success ) {
					self._templates[type] = response.data;
					select.html( response.data );
					if ( '' !== value ) {
						select.find( 'option[value="' + value + '"]').attr('selected', 'selected');
					}
				}
			});
		}
	});

	FLBuilder.registerModuleHelper('pp_content_form', {
		init: function() {
			$('.pp-module-id-class').html( '<strong>pp-offcanvas-' + nodeId + '-close</strong>' );
		}
	});

})(jQuery);
