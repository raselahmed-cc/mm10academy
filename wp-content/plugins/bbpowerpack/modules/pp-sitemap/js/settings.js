(function($){

	/**
	 * Use this file to register a module helper that
	 * adds additional logic to the settings form. The
	 * method 'FLBuilder._registerModuleHelper' accepts
	 * two parameters, the module slug (same as the folder name)
	 * and an object containing the helper methods and properties.
	 */
	FLBuilder._registerModuleHelper(
		"pp_sitemap_list",
		{
			_taxonomies: '',

			/**
			 * The 'init' method is called by the builder when
			 * the settings form is opened.
			 *
			 * @method init
			 */
			init: function() {
				var self = this;

				$( '#fl-field-sitemap_type select[name=sitemap_type]' ).on(
					'change',
					function(){
						self._typeChange();
						$( '.fl-taxonomy-filter' ).hide();
						if ( 'post_type' === $( this ).val() ) {
							$( '.fl-post_type-filter' ).show();
						} else {
							$( '.fl-post_type-filter' ).hide();
							$( '#fl-field-sitemap_taxonomy_source select[name=sitemap_taxonomy_source]' ).trigger( 'change' );
						}
					}
				);

				$( '#fl-field-sitemap_taxonomy_source select[name=sitemap_taxonomy_source]' ).on(
					'change',
					function () {
						$( '.fl-taxonomy-filter' ).hide();
						$( '.fl-post_type-filter' ).hide();
						if ('taxonomy' === $( '#fl-field-sitemap_type select[name=sitemap_type]' ).val()) {
							$( '.fl-taxonomy-filter.fl-tax-' + $( this ).val() + '-filter' ).show();
						}
					}
				);

				$( '#fl-field-sitemap_type select[name=sitemap_type]' ).trigger( 'change' );
				self._typeChange();
			},

			_typeChange: function() {
				var type = $( 'select[name="sitemap_type"]' ).val();

				if ( 'taxonomy' === type ) {
					this._setTaxonomies();
				}
			},

			_getTaxonomies: function(callback) {
				if ( 'undefined' === typeof callback ) {
					return;
				}

				$.post(
					bb_powerpack.getAjaxUrl(),
					{
						action: 'pp_get_taxonomies',
						post_type: 'all'
					},
					function( response ) {
						callback(response);
					}
				);
			},

			_setTaxonomies: function() {
				var form = $('.fl-builder-settings'),
					select = form.find( 'select[name="sitemap_taxonomy_source"]' ),
					value = '',
					self = this;

				value = FLBuilderSettingsForms.config.settings['sitemap_taxonomy_source'];

				if ( this._taxonomies !== '' ) {
					select.html( this._taxonomies );
					select.find( 'option[value="' + value + '"]').attr('selected', 'selected');

					return;
				}

				this._getTaxonomies(function(data) {
					self._taxonomies = data;
					select.html( data );
					if ( '' !== value ) {
						select.find( 'option[value="' + value + '"]').attr('selected', 'selected');
					}
				});
			}
		}
	);

})( jQuery );
