
<?php
// Default Settings
$defaults = array(
	'post_type'      => 'post',
	'posts_per_page' => 5,
	'order_by'       => 'date',
	'order'          => 'DESC',
);

$settings = (object) array_merge( $defaults, (array) $settings );
?>
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
					'row_class' => 'fl-custom-query',
				),
				$settings
			);

			FLBuilder::render_settings_field(
				'posts_per_page',
				array(
					'type'    => 'unit',
					'label'   => __( 'Total Number of Posts', 'bb-powerpack' ),
					'default' => '10',
					'slider'  => true,
					'help'    => __( 'Leave blank or add -1 for all posts.', 'bb-powerpack' ),
				),
				$settings
			);

			foreach ( FLBuilderLoop::post_types() as $slug => $type ) {
				// Posts
				FLBuilder::render_settings_field( 'posts_' . $slug, array(
					'type'      => 'suggest',
					'action'    => 'fl_as_posts',
					'data'      => $slug,
					/* translators: %s: type label */
					'label'     => sprintf( __( 'Filter by %1$s', 'fl-builder' ), $type->label ),
					/* translators: %s: type label */
					'help'      => sprintf( __( 'Enter a list of %1$s.', 'fl-builder' ), $type->label ),
					'matching'  => true,
					'row_class' => "fl-custom-query-filter fl-custom-query-{$slug}-filter",
				), $settings );

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

		<table class="fl-form-table fl-post-type-other-setting">
			<?php

			// Order by
			FLBuilder::render_settings_field('order_by', array(
				'type'    => 'select',
				'label'   => __( 'Order By', 'bb-powerpack' ),
				'options' => array(
					'none'           => __( 'None', 'bb-powerpack' ),
					'ID'             => __( 'ID', 'bb-powerpack' ),
					'author'         => __( 'Author', 'bb-powerpack' ),
					'title'          => __( 'Title', 'bb-powerpack' ),
					'name'           => __( 'Name', 'bb-powerpack' ),
					'date'           => __( 'Date', 'bb-powerpack' ),
					'modified'       => __( 'Last Modified', 'bb-powerpack' ),
					'comment_count'  => __( 'Comment Count', 'bb-powerpack' ),
					'menu_order'     => __( 'Menu Order', 'bb-powerpack' ),
					'meta_value'     => __( 'Meta Value (Alphabetical)', 'bb-powerpack' ),
					'meta_value_num' => __( 'Meta Value (Numeric)', 'bb-powerpack' ),
					'rand'           => __( 'Random', 'bb-powerpack' ),
					'post__in'       => __( 'Selection Order', 'bb-powerpack' ),
				),
				'toggle'  => array(
					'meta_value'     => array(
						'fields' => array( 'order_by_meta_key' ),
					),
					'meta_value_num' => array(
						'fields' => array( 'order_by_meta_key' ),
					),
				),
			), $settings);

			// Meta Key
			FLBuilder::render_settings_field('order_by_meta_key', array(
				'type'  => 'text',
				'label' => __( 'Meta Key', 'bb-powerpack' ),
			), $settings);

			FLBuilder::render_settings_field(
				'order',
				array(
					'type'    => 'select',
					'label'   => __( 'Order', 'bb-powerpack' ),
					'default' => 'DESC',
					'options' => array(
						'ASC'  => __( 'Ascending', 'bb-powerpack' ),
						'DESC' => __( 'Descending', 'bb-powerpack' ),
					),
				),
				$settings
			);

			// Offset
			FLBuilder::render_settings_field('offset', array(
				'type'        => 'unit',
				'label'       => _x( 'Offset', 'How many posts to skip.', 'bb-powerpack' ),
				'default'     => '0',
				'sanitize'    => 'absint',
				'help'        => __( 'Skip this many posts that match the specified criteria.', 'bb-powerpack' ),
			), $settings);
			?>
		</table>
	</div>
</div>
