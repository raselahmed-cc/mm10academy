<?php

/**
 * @class PPImagePanelsModule
 */
class PPImagePanelsModule extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Image Panels', 'bb-powerpack' ),
				'description'     => __( 'Create beautiful images panels.', 'bb-powerpack' ),
				'group'           => pp_get_modules_group(),
				'category'        => pp_get_modules_cat( 'media' ),
				'dir'             => BB_POWERPACK_DIR . 'modules/pp-image-panels/',
				'url'             => BB_POWERPACK_URL . 'modules/pp-image-panels/',
				'editor_export'   => true, // Defaults to true and can be omitted.
				'enabled'         => true, // Defaults to true and can be omitted.
				'partial_refresh' => true,
			)
		);
	}

	public function filter_settings( $settings, $helper ) {
		for ( $i = 0; $i < count( $settings->image_panels ); $i++ ) {

			if ( ! is_object( $settings->image_panels[ $i ] ) ) {
				continue;
			}

			// Handle old link, link_target fields.
			$settings->image_panels[ $i ] = PP_Module_Fields::handle_link_field(
				$settings->image_panels[ $i ],
				array(
					'link'        => array(
						'type' => 'link',
					),
					'link_target' => array(
						'type' => 'target',
					),
				),
				'link'
			);

			// Handle old title background & text dual color field.
			if ( isset( $settings->image_panels[ $i ]->title_colors ) ) {
				$settings->image_panels[ $i ] = PP_Module_Fields::handle_dual_color_field(
					$settings->image_panels[ $i ],
					'title_colors',
					array(
						'primary'           => 'title_text_color',
						'secondary'         => 'title_bg_color',
						'secondary_opacity' => isset( $settings->image_panels[ $i ]->title_opacity ) ? $settings->image_panels[ $i ]->title_opacity : 1,
					)
				);

				if ( isset( $settings->image_panels[ $i ]->title_colors ) ) {
					unset( $settings->image_panels[ $i ]->title_colors );
				}
			}

			// Handle title opacity + background color field.
			if ( isset( $settings->image_panels[ $i ]->title_opacity ) ) {
				unset( $settings->image_panels[ $i ]->title_opacity );
			}
		}

		// Handle title's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'title_font'        => array(
					'type' => 'font',
				),
				'title_font_size'   => array(
					'type' => 'font_size',
					'keys' => array(
						'desktop' => 'title_font_size_desktop',
						'tablet'  => 'title_font_size_tablet',
						'mobile'  => 'title_font_size_mobile',
					)
				),
				'title_line_height' => array(
					'type' => 'line_height',
					'keys' => array(
						'desktop' => 'title_line_height_desktop',
						'tablet'  => 'title_line_height_tablet',
						'mobile'  => 'title_line_height_mobile',
					),
				),
				'title_alignment'   => array(
					'type' => 'text_align',
				),
			),
			'title_typography'
		);

		// Handle title old padding field.
		$settings = PP_Module_Fields::handle_multitext_field(
			$settings,
			'title_padding',
			'padding',
			'title_padding',
			array(
				'top'    => 'title_top_padding',
				'bottom' => 'title_bottom_padding',
				'left'   => 'title_left_padding',
				'right'  => 'title_right_padding',
			)
		);

		return $settings;
	}

	/**
	 * Use this method to work with settings data before
	 * it is saved. You must return the settings object.
	 *
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		return $settings;
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		$this->add_js( 'jquery-magnificpopup' );
		$this->add_css( 'jquery-magnificpopup' );
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module(
	'PPImagePanelsModule',
	array(
		'content' => array( // Tab
			'title'    => __( 'Panel', 'bb-powerpack' ), // Tab title
			'sections' => array( // Tab Sections
				'separator' => array(
					'title'  => '',
					'fields' => array(
						'image_panels' => array(
							'type'         => 'form',
							'label'        => __( 'Panel', 'bb-powerpack' ),
							'form'         => 'pp_image_panels_form',
							'preview_text' => 'title',
							'multiple'     => true,
						),
					),
				),
			),
		),
		'style'   => array(
			'title'    => __( 'Style', 'bb-powerpack' ),
			'sections' => array(
				'panel_style'            => array(
					'title'  => __( 'Panel', 'bb-powerpack' ),
					'fields' => array(
						'panel_height'      => array(
							'type'      	=> 'unit',
							'label'     	=> __( 'Height', 'bb-powerpack' ),
							'default' 		=> 400,
							'units'   		=> array( 'px' ),
							'slider'		=> true,
							'responsive'	=> true,
							'preview'   	=> array(
								'type'  		=> 'css',
								'selector'  	=> '.pp-image-panels-wrap .pp-panel-item',
								'property'  	=> 'height',
								'unit'      	=> 'px'
							)
						),
						'spacing' 		  => array(
							'type'              => 'unit',
							'label'             => __( 'Spacing', 'bb-powerpack' ),
							'default'           => '',
							'units'             => array( 'px' ),
							'slider'            => true,
							'responsive'        => true,
							'preview'           => array(
								'type'              => 'css',
								'selector'          => '.pp-image-panels-inner',
								'property'          => 'gap',
								'unit'              => 'px'
							)
						),
						'border' => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'       => 'css',
								'selector'   => '.pp-image-panels-wrap .pp-panel-item',
							),
						),
						'expand_width' => array(
							'type'       => 'unit',
							'label'      => __( 'Expand Width', 'bb-powerpack' ),
							'help'       => __( 'Set the width of the active panel when expanded. Leave empty for default behavior.', 'bb-powerpack' ),
							'default'    => '',
							'units'      => array( '%' ),
							'slider'     => true,
						),
						'expand_panel' => array(
							'type'       => 'unit',
							'label'      => __( 'Expand Panel', 'bb-powerpack' ),
							'help'       => __( 'Set a panel number to expand by default on page load. Leave empty for default behavior.', 'bb-powerpack' ),
							'default'    => '',
						),
						'speed' => array(
							'type'       => 'unit',
							'label'      => __( 'Animation Speed', 'bb-powerpack' ),
							'default'    => 400,
							'units'     => array( 'ms' ),
						),
						'show_title'        => array(
							'type'              => 'pp-switch',
							'label'             => __( 'Show Content', 'bb-powerpack' ),
							'default'           => 'yes',
							'options'           => array(
								'yes'               => __( 'Yes', 'bb-powerpack' ),
								'no'                => __( 'No', 'bb-powerpack' )
							),
							'toggle' => array(
								'yes' => array(
									'sections' => array( 'content_style' ),
									'fields'   => array( 'show_title_on_expand' ),
								)
							)
						),
						'show_title_on_expand' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Content on Expand', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'show_image_effect' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Image Effects', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'yes' => array(
									'sections' => array( 'image_effects_active', 'image_effects_inactive' ),
								),
							),
						),
						'responsive_stack'	=> array(
							'type'	=> 'pp-switch',
							'label'	=> __( 'Stack on mobile devices', 'bb-powerpack' ),
							'default' => 'no',
							'preview' => array(
								'type'	=> 'none',
							),
						),
					),
				),
				'image_effects_active'   => array(
					'title'     => __( 'Effects on Active/Default Panel', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => pp_image_effect_fields(),
				),
				'image_effects_inactive' => array(
					'title'     => __( 'Effects on Inactive Panel', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => pp_image_effect_fields( true ),
				),
				'content_style'             => array(
					'title'         => __( 'Content', 'bb-powerpack' ),
					'collapsed' 	=> true,
					'fields'        => array(
						'title_color' => array(
							'type'			=> 'color',
							'label'			=> __( 'Title Color', 'bb-powerpack' ),
							'default'		=> '',
							'show_reset'	=> true,
							'connections'	=> array( 'color' ),
							'preview'	   => array(
								'type'     => 'css',
								'selector' => '.pp-image-panels-wrap .pp-panel .pp-panel-title-text',
								'property' => 'color',
							),
						),
						'title_bg' => array(
							'type'			=> 'color',
							'label'			=> __( 'Background Color', 'bb-powerpack' ),
							'default'		=> '',
							'show_alpha'	=> true,
							'show_reset'	=> true,
							'connections'	=> array( 'color' ),
							'preview'	   => array(
								'type'     => 'css',
								'selector' => '.pp-image-panels-wrap .pp-panel .pp-panel-title',
								'property' => 'background-color',
							),
						),
						'title_bg_as_gradient' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Use Background as Gradient', 'bb-powerpack' ),
							'default' => 'no',
						),
						'title_tag' => array(
							'type'    => 'select',
							'label'   => __( 'Title HTML Tag', 'bb-powerpack' ),
							'default' => 'h3',
							'sanitize' => array( 'pp_esc_tags', 'h3' ),
							'options' => array(
								'h1'   => 'H1',
								'h2'   => 'H2',
								'h3'   => 'H3',
								'h4'   => 'H4',
								'h5'   => 'H5',
								'h6'   => 'H6',
								'div'  => 'div',
								'span' => 'span',
								'p'    => 'p',
							),
						),
						'title_typography'	=> array(
							'type'			=> 'typography',
							'label'			=> __( 'Title Typography', 'bb-powerpack' ),
							'responsive'  	=> true,
							'preview'		=> array(
								'type'			=> 'css',
								'selector'		=> '.pp-image-panels-wrap .pp-panel .pp-panel-title-text',
							),
						),
						'description_color' => array(
							'type'			=> 'color',
							'label'			=> __( 'Description Color', 'bb-powerpack' ),
							'default'		=> '',
							'show_reset'	=> true,
							'connections'	=> array( 'color' ),
							'preview'	   => array(
								'type'     => 'css',
								'selector' => '.pp-image-panels-wrap .pp-panel .pp-panel-description',
								'property' => 'color',
							),
						),
						'description_typography'	=> array(
							'type'			=> 'typography',
							'label'			=> __( 'Description Typography', 'bb-powerpack' ),
							'responsive'  	=> true,
							'preview'		=> array(
								'type'			=> 'css',
								'selector'		=> '.pp-image-panels-wrap .pp-panel .pp-panel-description',
							),
						),
						'title_padding'	=> array(
							'type'				=> 'dimension',
							'label'				=> __( 'Padding', 'bb-powerpack' ),
							'default'			=> '10',
							'units'				=> array( 'px' ),
							'slider'			=> true,
							'responsive'		=> true,
							'preview'			=> array(
								'type'			=> 'css',
								'selector'		=> '.pp-image-panels-wrap .pp-panel .pp-panel-title',
								'property'		=> 'padding',
								'unit'			=> 'px',
							),
						),
					)
				),
			),
		),
	)
);

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form(
	'pp_image_panels_form',
	array(
		'title' => __( 'Add Panel', 'bb-powerpack' ),
		'tabs'  => array(
			'general'      => array( // Tab
				'title'         => __( 'Panel', 'bb-powerpack' ), // Tab title
				'sections'      => array( // Tab Sections
					'content'          => array(
						'title'      => '',
						'fields'     => array(
							'title'     => array(
								'type'          => 'text',
								'label'         => __( 'Title', 'bb-powerpack' ),
								'connections'   => array( 'string', 'html', 'url' ),
							),
							'description' => array(
								'type'          => 'textarea',
								'label'         => __( 'Description', 'bb-powerpack' ),
								'connections'   => array( 'string', 'html', 'url' ),
							),
							'photo'     => array(
								'type'          => 'photo',
								'label'         => __( 'Image', 'bb-powerpack' ),
								'connections'   => array( 'photo' ),
							),
							'position'  => array(
								'type'      => 'pp-switch',
								'label'     => __( 'Image Position', 'bb-powerpack' ),
								'default'   => 'center',
								'options'   => array(
									'center'    => __( 'Center', 'bb-powerpack' ),
									'custom'    => __( 'Custom', 'bb-powerpack' )
								),
								'toggle'    => array(
									'custom'    => array(
										'fields'    => array( 'custom_position' )
									)
								)
							),
							'custom_position'   => array(
								'type'              => 'unit',
								'label'             => __( 'Set Position', 'bb-powerpack' ),
								'default'           => 50,
								'units'       		=> array( '%' ),
								'slider'            => true,
								'preview'           => array(
									'type'              => 'css',
									'selector'          => '.pp-image-panels-wrap .pp-panel-item',
									'property'          => 'background-position',
									'unit'              => '%'
								)
							),
							'link_type'     => array(
								'type'          => 'select',
								'label'         => __( 'Link Type', 'bb-powerpack' ),
								'default'       => 'none',
								'options'       => array(
									'none'          => __( 'None', 'bb-powerpack' ),
									'title'         => __( 'Title', 'bb-powerpack' ),
									'panel'         => __( 'Panel', 'bb-powerpack' ),
									'lightbox'      => __( 'Lightbox', 'bb-powerpack' ),
								),
								'toggle'    => array(
									'title' => array(
										'fields'    => array( 'link' ),
									),
									'panel' => array(
										'fields'    => array( 'link' ),
									),
								),
							),
							'link'  => array(
								'type'          => 'link',
								'label'         => __( 'Link', 'bb-powerpack' ),
								'placeholder'   => 'http://www.example.com',
								'show_target'	=> true,
								'connections'   => array( 'url' ),
								'preview'       => array(
									'type'          => 'none'
								)
							),
						),
					),
					'style'     => array(
						'title' => __( 'Style', 'bb-powerpack' ),
						'fields'    => array(
							'title_text_color'	=> array(
								'type'			=> 'color',
								'label'			=> __( 'Title Color', 'bb-powerpack' ),
								'default'		=> '',
								'show_reset'	=> true,
								'connections'	=> array( 'color' ),
							),
							'description_text_color'	=> array(
								'type'			=> 'color',
								'label'			=> __( 'Description Color', 'bb-powerpack' ),
								'default'		=> '',
								'show_reset'	=> true,
								'connections'	=> array( 'color' ),
							),
							'title_bg_color'	=> array(
								'type'			=> 'color',
								'label'			=> __( 'Background Color', 'bb-powerpack' ),
								'default'		=> '',
								'show_alpha'	=> true,
								'show_reset'	=> true,
								'connections'	=> array( 'color' ),
							),
						),
					),
				)
			),
		),
	)
);
