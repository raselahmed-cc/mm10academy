<?php

/**
 * @class PPQuoteModule
 */
class PPQuoteModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Pullquote', 'bb-powerpack'),
            'description'   => __('Addon to display quote.', 'bb-powerpack'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'creative' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-pullquote/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-pullquote/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
		));
    }

	public function enqueue_icon_styles() {
		$enqueue = false;
		$settings = $this->settings;

		if ( isset( $settings->show_icon ) && 'yes' === $settings->show_icon ) {
			if ( isset( $settings->quote_icon ) && ! empty( $settings->quote_icon ) ) {
				$enqueue = true;
			}
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function filter_settings( $settings, $helper ) {
		// Handle old icon field.
		if ( isset( $settings->show_pullquote_icon ) ) {
			$settings->show_icon = $settings->show_pullquote_icon;
			unset( $settings->show_pullquote_icon );
		}
		if ( isset( $settings->pullquote_icon ) ) {
			$settings->quote_icon = $settings->pullquote_icon;
			unset( $settings->pullquote_icon );
		}

		// Handle old content field.
		if ( isset( $settings->pullquote_content ) ) {
			$settings->quote_text = $settings->pullquote_content;
			unset( $settings->pullquote_content );
		}

		// Handle old title field.
		if ( isset( $settings->pullquote_title ) ) {
			$settings->quote_name = $settings->pullquote_title;
			unset( $settings->pullquote_title );
		}

		// Handle old alignment field.
		if ( isset( $settings->pullquote_alignment ) ) {
			$settings->quote_alignment = $settings->pullquote_alignment;
			unset( $settings->pullquote_alignment );
		}

		// Handle old width field.
		if ( isset( $settings->pullquote_width ) ) {
			$settings->quote_width = $settings->pullquote_width;
			unset( $settings->pullquote_width );
		}

		// Handle old border and radius fields.
		$border_fields = array(
			'quote_border_style' => array(
				'type' => 'style',
			),
			'quote_border_width' => array(
				'type' => 'width',
			),
			'quote_border_color' => array(
				'type' => 'color',
			),
			'quote_border_radius' => array(
				'type' => 'radius',
			),
		);
		if ( isset( $settings->quote_border_width ) && is_array( $settings->quote_border_width ) ) {
			$border_fields['quote_border_width']['value'] = array(
				'top'    => $settings->quote_border_width['quote_border_top_width'],
				'right'  => $settings->quote_border_width['quote_border_right_width'],
				'bottom' => $settings->quote_border_width['quote_border_bottom_width'],
				'left'   => $settings->quote_border_width['quote_border_left_width'],
			);
		}
		$settings = PP_Module_Fields::handle_border_field( $settings, $border_fields, 'quote_border' );

		// Handle box old padding field.
		$settings = PP_Module_Fields::handle_multitext_field(
			$settings,
			'quote_padding', // old field name.
			'padding', // type.
			'quote_padding', // new field name.
			array(
				'top'    => 'quote_top_padding',
				'right'  => 'quote_right_padding',
				'bottom' => 'quote_bottom_padding',
				'left'   => 'quote_left_padding'
			)
		);

		// Handle old title color field.
		if ( isset( $settings->title_color ) && ! empty( $settings->title_color ) ) {
			$settings->name_color = $settings->title_color;
			unset( $settings->title_color );
		}

		// Handle title's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'title_font' => array(
				'type' => 'font'
			),
			'title_font_size' => array(
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
				)
			),
		), 'name_typography' );

		// Handle old text color field.
		if ( isset( $settings->content_color ) && ! empty( $settings->content_color ) ) {
			$settings->text_color = $settings->content_color;
			unset( $settings->content_color );
		}

		// Handle old text typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'text_font' => array(
				'type' => 'font'
			),
			'text_font_size' => array(
				'type' => 'font_size',
				'keys' => array(
					'desktop' => 'text_font_size_desktop',
					'tablet'  => 'text_font_size_tablet',
					'mobile'  => 'text_font_size_mobile',
				)
			),
			'text_line_height' => array(
				'type' => 'line_height',
				'keys' => array(
					'desktop' => 'text_line_height_desktop',
					'tablet'  => 'text_line_height_tablet',
					'mobile'  => 'text_line_height_mobile',
				)
			),
		), 'text_typography' );

		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module('PPQuoteModule', array(
	'general'      => array( // Tab
		'title'         => __('General', 'bb-powerpack'), // Tab title
		'sections'      => array( // Tab Sections
            'pullquote_section' => array(
                'title'     => '',
                'fields'    => array(
                    'show_icon'   => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Show Icon', 'bb-powerpack'),
                        'default'   => 'no',
                        'options'   => array(
                            'yes'    => __('Yes', 'bb-powerpack'),
                            'no'    => __('No', 'bb-powerpack'),
                        ),
                        'toggle'    => array(
                            'yes'   => array(
                                'fields'    => array('quote_icon'),
                                'sections'    => array('quote_icon_styles'),
                            ),
                        )
                    ),
                    'quote_icon'    => array(
                        'type'      => 'icon',
                        'label'     => __('Icon', 'bb-powerpack'),
                    ),
                    'quote_text' => array(
                        'type'        => 'textarea',
                        'label'       => __('Quote', 'bb-powerpack'),
                        'connections' => array( 'string', 'html', 'url' ),
                    ),
                    'quote_name' => array(
                        'type'        => 'text',
                        'label'       => __('Name', 'bb-powerpack'),
                        'connections' => array( 'string', 'html', 'url' ),
                    ),
                    'quote_alignment'   => array( // uses CSS float property.
                        'type'    => 'pp-switch',
                        'label'   => __('Overall Alignment', 'bb-powerpack'),
                        'default' => 'none',
						'options' => array(
							'left'  => __( 'Left', 'bb-powerpack' ),
							'none'  => __( 'Center', 'bb-powerpack' ),
							'right' => __( 'Right', 'bb-powerpack' ),
						),
						'preview' => array(
							'type'     => 'css',
							'selector' => '.pp-pullquote-wrapper',
							'property' => 'float'
						),
                    ),
                ),
            ),
		)
	),
    'styles'      => array( // Tab
		'title'         => __('Style', 'bb-powerpack'), // Tab title
		'sections'      => array( // Tab Sections
            'quote_styles'        => array(
                'title'     => __('Quote', 'bb-powerpack'),
                'fields'        => array( // Section Fields
                    'quote_background'      => array(
                        'type'      => 'color',
                        'label'     => __('Background Color', 'bb-powerpack'),
						'show_reset'    => true,
						'show_alpha'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-pullquote-wrapper',
                            'property'  => 'background'
                        ),
                    ),
                    'quote_text_alignment'  => array(
                        'type'      => 'align',
                        'label'     => __('Text Alignment', 'bb-powerpack'),
                        'default'   => 'center',
						'responsive' => true,
						'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-pullquote-wrapper',
                            'property'  => 'text-align'
                        ),
                    ),
					'quote_border' => array(
						'type' => 'border',
						'label' => __( 'Border', 'bb-powerpack' ),
						'responsive' => true,
						'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-pullquote-wrapper',
                        ),
					),
					'quote_padding' => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'bb-powerpack' ),
						'units'      => array( 'px' ),
						'responsive' => true,
						'preview'    => array(
                            'type'      => 'css',
                            'selector'  => '.pp-pullquote-wrapper',
							'property'  => 'padding',
							'unit'      => 'px'
                        ),
					),
					'quote_width'   => array(
                        'type'      => 'unit',
                        'label'     => __('Quote Width', 'bb-powerpack'),
                        'units'     => array('px'),
                        'default'   => 300,
						'responsive' => true,
                        'preview'   => array(
                            'selector'  => '.pp-pullquote .pp-pullquote-wrapper',
                            'property'  => 'max-width',
                            'unit'      => 'px'
                        ),
                    ),
				),
            ),
            'quote_icon_styles'     => array(
                'title'     => __('Icon', 'bb-powerpack'),
                'fields'    => array(
                    'icon_color'    => array(
                        'type'      => 'color',
                        'label'     => __('Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-pullquote .pp-pullquote-wrapper .pp-pullquote-icon .pp-icon',
                            'property'  => 'color'
                        ),
                    ),
                    'icon_font_size'    => array(
                        'type'      => 'unit',
                        'label'     => __('Size', 'bb-powerpack'),
                        'units'     => array( 'px' ),
                        'default'   => 16,
						'responsive' => true,
                        'preview'   => array(
                            'type'  => 'css',
                            'rules' => array(
                                array(
                                    'selector'  => '.pp-pullquote .pp-pullquote-wrapper .pp-pullquote-icon .pp-icon',
                                    'property'  => 'font-size',
                                    'unit'  => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-pullquote .pp-pullquote-wrapper .pp-pullquote-icon .pp-icon:before',
                                    'property'  => 'font-size',
                                    'unit'  => 'px'
                                ),
                            )
                        ),
                    ),
                ),
            ),
		)
	),
    'typography'    => array(
        'title'     => __('Typography', 'bb-powerpack'),
        'sections'  => array(
			'text_typography'        => array(
                'title'     => __('Quote', 'bb-powerpack'),
                'fields'        => array( // Section Fields
					'text_typography' => array(
						'type'    => 'typography',
						'label'   => __( 'Typography', 'bb-powerpack' ),
						'responsive' => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => '.pp-pullquote .pp-pullquote-content p'
						)
					),
                    'text_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-pullquote .pp-pullquote-content p',
                            'property'      => 'color',
                        )
					),
                ),
            ),
            'name_typography'        => array(
                'title'     => __('Name', 'bb-powerpack'),
                'fields'        => array( // Section Fields
					'name_html_tag' => array(
						'type'  => 'select',
						'label' => __( 'HTML Tag', 'bb-powerpack' ),
						'default' => 'h4',
						'sanitize' => array( 'pp_esc_tags', 'h4' ),
						'options' => array(
							'h1' => 'H1',
							'h2' => 'H2',
							'h3' => 'H3',
							'h4' => 'H4',
							'h5' => 'H5',
							'h6' => 'H6',
						),
					),
					'name_typography' => array(
						'type'    => 'typography',
						'label'   => __( 'Typography', 'bb-powerpack' ),
						'responsive' => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => '.pp-pullquote .pp-pullquote-name'
						)
					),
                    'name_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-pullquote .pp-pullquote-name',
                            'property'      => 'color',
                        )
					),
				)
            ),
        ),
    ),
));
