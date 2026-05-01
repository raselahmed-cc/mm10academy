<div class="fl-custom-query fl-loop-data-source" data-source="custom_query">
	<div id="fl-builder-settings-section-filter" class="fl-builder-settings-section">
		<h3 class="fl-builder-settings-title"></h3>

		<table class="fl-form-table fl-post-type-filter">
		<?php
			$post_types    = array();
			$taxonomy_type = array();

		foreach ( FLBuilderLoop::post_types() as $slug => $type ) {

			$taxonomies = FLBuilderLoop::taxonomies( $slug );

			if ( ! empty( $taxonomies ) ) {
				$post_types[ $slug ] = $type->label;

				foreach ( $taxonomies as $tax_slug => $tax ) {
					$taxonomy_type[ $slug ][ $tax_slug ] = $tax->label;
				}
			}
		}

			FLBuilder::render_settings_field(
				'post_type',
				array(
					'type'    => 'select',
					'label'   => __( 'Post Type', 'bb-powerpack' ),
					'options' => $post_types,
					'default' => isset( $settings->post_type ) ? $settings->post_type : 'post',
				),
				$settings
			);
			?>
		</table>

		<?php
		foreach ( $post_types as $slug => $label ) :
			$selected = isset( $settings->{'posts_' . $slug . '_type'} ) ? $settings->{'posts_' . $slug . '_type'} : 'post';
			?>
			<table class="fl-form-table fl-custom-query-filter fl-custom-query-<?php echo $slug; ?>-filter"<?php echo ( $slug == $selected ) ? ' style="display:table;"' : ''; ?>>
			<?php

			FLBuilder::render_settings_field(
				'posts_' . $slug . '_tax_type',
				array(
					'type'    => 'select',
					'label'   => __( 'Taxonomy', 'bb-powerpack' ),
					'options' => $taxonomy_type[ $slug ],
					'multi-select' => true
				),
				$settings
			);

			foreach ( $taxonomy_type[ $slug ] as $tax_slug => $tax_label ) {

				FLBuilder::render_settings_field(
					'tax_' . $slug . '_' . $tax_slug,
					array(
						'type'     => 'suggest',
						'action'   => 'fl_as_terms',
						'data'     => $tax_slug,
						'label'    => $tax_label,
						/* translators: %s: tax label */
						'help'     => sprintf( __( 'Enter a list of %1$s.', 'bb-powerpack' ), $tax_label ),
						'matching' => true,
					),
					$settings
				);
			}

			?>
			</table>
		<?php endforeach; ?>
		<p class="fl-builder-settings-description">
			<a href="<?php echo admin_url( 'options-general.php?page=ppbb-settings&tab=extensions' ); ?>" target='_blank'>
				<?php echo __( 'Click to enable thumbnail for taxonomies.', 'bb-powerpack' ); ?>
			</a>
		</p>
		<table class='fl-form-table'>
			<?php

				FLBuilder::render_settings_field(
					'display_data',
					array(
						'type'    => 'select',
						'label'   => __( 'Display', 'bb-powerpack' ),
						'default' => isset( $settings->display_data ) ? $settings->display_data : 'default',
						'options' => array(
							'default'       => __( 'Default', 'bb-powerpack' ),
							'parent_only'   => __( 'Parent Only', 'bb-powerpack' ),
							'children_only' => __( 'Children Only', 'bb-powerpack' ),
						),
					),
					$settings
				);

				FLBuilder::render_settings_field(
					'on_tax_archive',
					array(
						'type'    => 'select',
						'label'   => __( 'On Taxonomy Archive', 'bb-powerpack' ),
						'default' => isset( $settings->on_tax_archive ) ? $settings->on_tax_archive : 'default',
						'options' => array(
							'default'       => __( 'Default', 'bb-powerpack' ),
							'parent_only'   => __( 'Parent of Current Category', 'bb-powerpack' ),
							'children_only' => __( 'Children of Current Category', 'bb-powerpack' ),
						),
					),
					$settings
				);

				if ( is_single() ) {
					FLBuilder::render_settings_field(
						'on_post',
						array(
							'type'    => 'select',
							'label'   => __( 'On Single Post', 'bb-powerpack' ),
							'default' => isset( $settings->on_post ) ? $settings->on_post : 'default',
							'options' => array(
								'default'       => __( 'Default', 'bb-powerpack' ),
								'assigned_only' => __( 'Assigned only', 'bb-powerpack' ),
							),
						),
						$settings
					);
				}
				?>
		</table>

		<table class='fl-form-table'>
			<?php
				FLBuilder::render_settings_field(
					'order_by',
					array(
						'type'    => 'select',
						'label'   => __( 'Order By', 'bb-powerpack' ),
						'default' => isset( $settings->order_by ) ? $settings->order_by : 'name',
						'options' => array(
							'term_id'	=> __( 'ID', 'bb-powerpack' ),
							'name'   => __( 'Name', 'bb-powerpack' ),
							'slug'   => __( 'Slug', 'bb-powerpack' ),
							'parent' => __( 'Parent', 'bb-powerpack' ),
							'count'  => __( 'Post Count', 'bb-powerpack' ),
							'term_group' => __( 'Term Group', 'bb-powerpack' ),
							'include' => __( 'Include', 'bb-powerpack' ),
							'meta_value' => __( 'Meta Value', 'bb-powerpack' ),
							'meta_value_num' => __( 'Meta Value Number', 'bb-powerpack' ),
							'none'  => __( 'None', 'bb-powerpack' ),
						),
						'toggle' => array(
							'meta_value' => array(
								'fields'   => array( 'order_by_meta_key' ),
							),
							'meta_value_num' => array(
								'fields'   => array( 'order_by_meta_key' ),
							),
						),
					),
					$settings
				);

				FLBuilder::render_settings_field(
					'order_by_meta_key',
					array(
						'type'    => 'text',
						'label'   => __( 'Meta Key', 'bb-powerpack' ),
						'default' => ''
					),
					$settings
				);

				FLBuilder::render_settings_field(
					'order',
					array(
						'type'    => 'select',
						'label'   => __( 'Order', 'bb-powerpack' ),
						'default' => isset( $settings->order ) ? $settings->order : 'ASC',
						'options' => array(
							'ASC'  => __( 'Ascending', 'bb-powerpack' ),
							'DESC' => __( 'Descending', 'bb-powerpack' ),
						),
					),
					$settings
				);

				FLBuilder::render_settings_field(
					'show_empty',
					array(
						'type'    => 'pp-switch',
						'label'   => __( 'Show Empty?', 'bb-powerpack' ),
						'default' => 'no',
						'options' => array(
							'yes' => __( 'Yes', 'bb-powerpack' ),
							'no'  => __( 'No', 'bb-powerpack' ),
						),
					),
					$settings
				);
				?>
		</table>
	</div>
</div>
