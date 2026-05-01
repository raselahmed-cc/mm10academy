<div id="fl-builder-settings-section-post" class="fl-builder-settings-section">
	<div class="fl-builder-settings-section-header">
		<button class="fl-builder-settings-title">
			<?php pp_builder_setting_form_section_icon(); ?>
			<?php _e( 'Query', 'bb-powerpack' ); ?>
		</button>
	</div>

	<div class="fl-builder-settings-section-content">

		<table class="fl-form-table">
			<?php

			FLBuilder::render_settings_field(
				'post_type',
				array(
					'type'      => 'post-type',
					'label'     => __( 'Post Type', 'bb-powerpack' ),
					'multi-select' => true,
					'row_class' => 'fl-custom-query',
				),
				$settings
			);

			foreach ( FLBuilderLoop::post_types() as $slug => $type ) {

				// Taxonomies
				$taxonomies = FLBuilderLoop::taxonomies( $slug );

				$field_settings = new stdClass;
				foreach ( $settings as $k => $setting ) {
					if ( false !== strpos( $k, 'tax_' . $slug ) ) {
						$field_settings->$k = $setting;
					}
				}

				foreach ( $taxonomies as $tax_slug => $tax ) {
					$field_key = 'tax_' . $slug . '_' . $tax_slug;

					if ( isset( $settings->$field_key ) ) {
						$field_settings->$field_key = $settings->$field_key;
					}

					FLBuilder::render_settings_field( $field_key, array(
						'type'      => 'suggest',
						'action'    => 'fl_as_terms',
						'data'      => $tax_slug,
						/* translators: %s: tax label */
						'label'     => sprintf( __( 'Filter by %1$s', 'fl-builder' ), $tax->label ),
						/* translators: %s: tax label */
						'help'      => sprintf( __( 'Enter a list of %1$s.', 'fl-builder' ), $tax->label ),
						'matching'  => true,
						'row_class' => "fl-custom-query-filter fl-custom-query-{$slug}-filter",
					), $field_settings );
				}
			}
			?>
		</table>
	</div>
</div>
