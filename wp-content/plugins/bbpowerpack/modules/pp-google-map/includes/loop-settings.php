<?php
include BB_POWERPACK_DIR . 'includes/ui-loop-settings-simple.php';
?>
<div id="fl-builder-settings-section-location" class="fl-builder-settings-section">
	<div class="fl-builder-settings-section-header">
		<button class="fl-builder-settings-title">
			<?php pp_builder_setting_form_section_icon(); ?>
			<?php _e( 'Location', 'bb-powerpack' ); ?>
		</button>
	</div>

	<div class="fl-builder-settings-section-content">
		<table class="fl-form-table">
		<?php
		FLBuilder::render_settings_field(
			'post_map_name',
			array(
				'type'        => 'text',
				'label'       => __( 'Location Name', 'bb-powerpack' ),
				'help'        => __( 'A browser based tooltip will be applied on marker.', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			),
			$settings
		);
		FLBuilder::render_settings_field(
			'post_map_latitude',
			array(
				'type'        => 'text',
				'label'       => __( 'Latitude', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			),
			$settings
		);
		FLBuilder::render_settings_field(
			'post_map_longitude',
			array(
				'type'        => 'text',
				'label'       => __( 'Longitude', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			),
			$settings
		);
		FLBuilder::render_settings_field(
			'post_marker_point',
			array(
				'type'    => 'pp-switch',
				'label'   => __( 'Marker Icon', 'bb-powerpack' ),
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'bb-powerpack' ),
					'custom'  => __( 'Custom', 'bb-powerpack' ),
				),
				'toggle'  => array(
					'custom' => array(
						'fields' => array( 'post_marker_img' ),
					),
				),
			),
			$settings
		);
		FLBuilder::render_settings_field(
			'post_marker_img',
			array(
				'type'        => 'photo',
				'label'       => __( 'Custom Marker', 'bb-powerpack' ),
				'show_remove' => true,
				'connections' => array( 'photo' ),
			),
			$settings
		);
		FLBuilder::render_settings_field(
			'post_enable_info',
			array(
				'type'    => 'pp-switch',
				'label'   => __( 'Show Info Window', 'bb-powerpack' ),
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Yes', 'bb-powerpack' ),
					'no'  => __( 'No', 'bb-powerpack' ),
				),
				'toggle'  => array(
					'yes' => array(
						'fields' => array( 'post_info_window_text' ),
					),
				),
			),
			$settings
		);
		FLBuilder::render_settings_field(
			'post_info_window_text',
			array(
				'type'          => 'editor',
				'label'         => '',
				'default'       => __( 'IdeaBox Creations', 'bb-powerpack' ),
				'media_buttons' => false,
				'connections'   => array( 'string', 'html' ),
			),
			$settings
		);
		?>
		</table>
	</div>
</div>