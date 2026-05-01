<?php

/**
 * @class PPTestimonialsModule
 */
class PPTestimonialsModule extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'          => __( 'Testimonials', 'bb-powerpack' ),
				'description'   => __( 'Addon to display testimonials.', 'bb-powerpack' ),
				'group'         => pp_get_modules_group(),
				'category'      => pp_get_modules_cat( 'content' ),
				'dir'           => BB_POWERPACK_DIR . 'modules/pp-testimonials/',
				'url'           => BB_POWERPACK_URL . 'modules/pp-testimonials/',
				'editor_export' => true, // Defaults to true and can be omitted.
				'enabled'       => true, // Defaults to true and can be omitted.,
			)
		);
	}

	public function enqueue_scripts() {
		$this->add_js( 'imagesloaded' );

		if ( FLBuilderModel::is_builder_active() || ( isset( $this->settings ) && 'slider' == $this->settings->layout ) ) {
			$this->add_css( 'pp-owl-carousel' );
			$this->add_css( 'pp-owl-carousel-theme' );
			$this->add_js( 'pp-owl-carousel' );
		}
	}

	public function filter_settings( $settings, $helper ) {

		// Handle heading's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'heading_font'      => array(
					'type' => 'font',
				),
				'heading_font_size' => array(
					'type' => 'font_size',
				),
				'heading_alignment' => array(
					'type' => 'text_align',
				),
			),
			'heading_typography'
		);

		// Handle title's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'title_font'      => array(
					'type' => 'font',
				),
				'title_font_size' => array(
					'type' => 'font_size',
				),
			),
			'title_typography'
		);

		// Handle subtitle's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'subtitle_font'      => array(
					'type' => 'font',
				),
				'subtitle_font_size' => array(
					'type' => 'font_size',
				),
			),
			'subtitle_typography'
		);

		// Handle text's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'text_font'      => array(
					'type' => 'font',
				),
				'text_font_size' => array(
					'type' => 'font_size',
				),
			),
			'text_typography'
		);

		// Handle old image border and radius fields.
		$settings = PP_Module_Fields::handle_border_field(
			$settings,
			array(
				'image_border_style' => array(
					'type' => 'style',
				),
				'border_width'       => array(
					'type' => 'width',
				),
				'border_color'       => array(
					'type' => 'color',
				),
				'border_radius'      => array(
					'type' => 'radius',
				),
			),
			'image_border'
		);

		// Handle old content border and radius fields.
		$settings = PP_Module_Fields::handle_border_field(
			$settings,
			array(
				'box_border_style'  => array(
					'type' => 'style',
				),
				'box_border_width'  => array(
					'type' => 'width',
				),
				'box_border_color'  => array(
					'type' => 'color',
				),
				'box_border_radius' => array(
					'type' => 'radius',
				),
				'box_shadow'        => array(
					'type'      => 'shadow',
					'condition' => ( isset( $settings->box_shadow_setting ) && 'yes' == $settings->box_shadow_setting ),
				),
				'box_shadow_color'  => array(
					'type'      => 'shadow_color',
					'condition' => ( isset( $settings->box_shadow_setting ) && 'yes' == $settings->box_shadow_setting ),
					'opacity'   => isset( $settings->box_shadow_opacity ) ? $settings->box_shadow_opacity : 1,
				),
			),
			'box_border'
		);

		if ( isset( $settings->carousel ) && 1 != $settings->carousel ) {
			$settings->move_slides = 1;
			$settings->min_slides = 1;

			unset( $settings->carousel );
		} 

		return $settings;
	}

	public function get_alt( $settings ) {
		if ( is_object( $settings->photo ) ) {
			$photo = $settings->photo;
		} else {
			$photo = FLBuilderPhoto::get_attachment_data( $settings->photo );
		}

		if ( ! empty( $photo->alt ) ) {
			return htmlspecialchars( $photo->alt );
		} elseif ( ! empty( $photo->description ) ) {
			return htmlspecialchars( $photo->description );
		} elseif ( ! empty( $photo->caption ) ) {
			return htmlspecialchars( $photo->caption );
		} elseif ( ! empty( $photo->title ) ) {
			return htmlspecialchars( $photo->title );
		} elseif ( ! empty( $settings->title ) ) {
			return htmlspecialchars( $settings->title );
		}
	}
	
	public function get_testimonials( $settings = null ) {
		$settings     = empty( $settings ) ? $this->settings : $settings;
		$items        = $this->settings->testimonials;
		$testimonials = array();

		if ( empty( $items ) ) {
			return $testimonials;
		}

		foreach ( $items as $item ) {
			if ( ! is_object( $item ) ) {
				continue;
			}
			$testimonial = array(
				'title'             => do_shortcode( $item->title ),
				'subtitle'          => do_shortcode( $item->subtitle ),
				'testimonial_title' => isset( $item->testimonial_title ) ? do_shortcode( $item->testimonial_title ) : '',
				'testimonial'       => do_shortcode( $item->testimonial ),
				'photo'             => array(
					'src' => '',
					'alt' => '',
				),
			);
			if ( isset( $item->photo_src ) ) {
				$testimonial['photo'] = array(
					'src' => $item->photo_src,
					'alt' => $this->get_alt( $item ),
				);
			}

			$testimonials[] = $testimonial;
		}

		return apply_filters( 'pp_testimonial_items', $testimonials, $settings );
	}
}


/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module(
	'PPTestimonialsModule',
	array(
		'general'      => array( // Tab
			'title'    => __( 'General', 'bb-powerpack' ), // Tab title
			'sections' => array( // Tab Sections
				'heading'   => array( // Section
					'title'  => '', // Section Title
					'fields' => array( // Section Fields
						'heading'         => array(
							'type'        => 'text',
							'default'     => __( 'Testimonials', 'bb-powerpack' ),
							'label'       => __( 'Heading', 'bb-powerpack' ),
							'connections' => array( 'string', 'html' ),
							'preview'     => array(
								'type'     => 'text',
								'selector' => '.pp-testimonials-heading',
							),
						),
						'layout'          => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Layout', 'bb-powerpack' ),
							'default' => 'slider',
							'options' => array(
								'grid'   => __( 'Grid', 'bb-powerpack' ),
								'slider' => __( 'Slider', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'grid'   => array(
									'fields' => array( 'grid_columns', 'spacing' ),
								),
								'slider' => array(
									'sections' => array( 'slider', 'arrow_nav', 'dot_nav' ),
								),
							),
						),
						'order'           => array(
							'type'    => 'select',
							'label'   => __( 'Order', 'bb-powerpack' ),
							'default' => 'asc',
							'options' => array(
								'asc'    => __( 'Ascending', 'bb-powerpack' ),
								'desc'   => __( 'Descending', 'bb-powerpack' ),
								'random' => __( 'Random', 'bb-powerpack' ),
							),
						),
						'adaptive_height' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Fixed Height', 'bb-powerpack' ),
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
							'help'    => __( 'Fix height to the tallest item.', 'bb-powerpack' ),
						),
						'grid_columns'    => array(
							'type'       => 'unit',
							'label'      => __( 'Grid Columns', 'bb-powerpack' ),
							'description' => __( 'Max 6', 'bb-powerpack' ),
							'default'    => '3',
							'slider'     => array(
								'min'  => 1,
								'max'  => 6,
								'step' => 1,
							),
							'responsive' => true,
						),
						'spacing'         => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'bb-powerpack' ),
							'default'    => '20',
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
						),
					),
				),
				'slider'    => array( // Section
					'title'  => __( 'Slider Settings', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields' => array( // Section Fields
						'autoplay'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Autoplay', 'bb-powerpack' ),
							'default' => '1',
							'options' => array(
								'1' => __( 'Yes', 'bb-powerpack' ),
								'0' => __( 'No', 'bb-powerpack' ),
							),
						),
						'pause'        => array(
							'type'    => 'unit',
							'label'   => __( 'Autoplay Speed', 'bb-powerpack' ),
							'default' => '4',
							'units'   => array( 'seconds' ),
						),
						'hover_pause'  => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Pause on hover', 'bb-powerpack' ),
							'default' => '1',
							'help'    => __( 'Pause when mouse hovers over slider.', 'bb-powerpack' ),
							'options' => array(
								'1' => __( 'Yes', 'bb-powerpack' ),
								'0' => __( 'No', 'bb-powerpack' ),
							),
						),
						'disable_mouse_drag' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Disable Mouse Drag', 'bb-powerpack' ),
							'default' => '0',
							'help'    => __( 'Disables the swipe via mouse drag.', 'bb-powerpack' ),
							'options' => array(
								'1' => __( 'Yes', 'bb-powerpack' ),
								'0' => __( 'No', 'bb-powerpack' ),
							),
						),
						'loop'         => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Loop', 'bb-powerpack' ),
							'default' => '1',
							'options' => array(
								'1' => __( 'Yes', 'bb-powerpack' ),
								'0' => __( 'No', 'bb-powerpack' ),
							),
						),
						'transition'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Mode', 'bb-powerpack' ),
							'default' => 'horizontal',
							'options' => array(
								'horizontal' => _x( 'Horizontal', 'Transition type.', 'bb-powerpack' ),
								'vertical'   => _x( 'Vertical', 'Transition type.', 'bb-powerpack' ),
								'fade'       => __( 'Fade', 'bb-powerpack' ),
							),
						),
						'speed'        => array(
							'type'    => 'unit',
							'label'   => __( 'Transition Speed', 'bb-powerpack' ),
							'default' => '0.5',
							'units'   => array( 'seconds' ),
						),
						'min_slides'   => array(
							'type'       => 'unit',
							'label'      => __( 'Number of Slides', 'bb-powerpack' ),
							'default'    => '1',
							'slider'     => true,
							'responsive' => true,
							'help'       => __( 'The minimum number of slides to be shown.', 'bb-powerpack' ),
						),
						'move_slides'  => array(
							'type'       => 'unit',
							'label'      => __( 'Move Slides', 'bb-powerpack' ),
							'default'    => '1',
							'slider'     => true,
							'responsive' => true,
							'help'       => __( 'The number of slides to move on transition.', 'bb-powerpack' ),
						),
						'slide_margin' => array(
							'type'       => 'unit',
							'label'      => __( 'Slides Margin', 'bb-powerpack' ),
							'default'    => '20',
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'help'       => __( 'Margin between each slide.', 'bb-powerpack' ),
						),
					),
				),
				'arrow_nav' => array( // Section
					'title'  => __( 'Navigation Arrows', 'bb-powerpack' ),
					'collapsed' => true,
					'fields' => array( // Section Fields
						'arrows'          => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Arrows', 'bb-powerpack' ),
							'default' => '1',
							'options' => array(
								'1' => __( 'Yes', 'bb-powerpack' ),
								'0' => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'1' => array(
									'fields' => array( 'arrow_color', 'arrow_bg_color', 'arrow_hover_color', 'arrow_bg_hover_color' ),
								),
							),
						),
						'arrow_color'     => array(
							'type'        => 'color',
							'label'       => __( 'Arrow Color', 'bb-powerpack' ),
							'default'     => '',
							'show_alpha'  => false,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'css',
								'selector' => '.pp-testimonials-wrap .owl-nav button',
								'property' => 'color'
							),
						),
						'arrow_hover_color'     => array(
							'type'        => 'color',
							'label'       => __( 'Arrow Hover Color', 'bb-powerpack' ),
							'default'     => '',
							'show_alpha'  => false,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'css',
								'selector' => '.pp-testimonials-wrap .owl-nav button:hover',
								'property' => 'color'
							),
						),
						'arrow_bg_color'  => array(
							'type'        => 'color',
							'label'       => __( 'Arrow Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'css',
								'selector' => '.pp-testimonials-wrap .owl-nav button',
								'property' => 'background'
							),
						),
						'arrow_bg_hover_color'  => array(
							'type'        => 'color',
							'label'       => __( 'Arrow Background Hover Color', 'bb-powerpack' ),
							'default'     => '',
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'css',
								'selector' => '.pp-testimonials-wrap .owl-nav button:hover',
								'property' => 'background'
							),
						),
						'arrow_position' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Arrow Position', 'bb-powerpack' ),
							'default' => 'bottom',
							'options' => array(
								'bottom' => __( 'Bottom', 'bb-powerpack' ),
								'side'   => __( 'Side', 'bb-powerpack' ),
							),
							'toggle' => array(
								'bottom' => array(
									'fields' => array( 'arrow_alignment' ),
								),
							),
						),
						'arrow_alignment' => array(
							'type'    => 'align',
							'label'   => __( 'Arrow Alignment', 'bb-powerpack' ),
							'default' => 'center',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-wrap .owl-nav',
								'property' => 'text-align',
							),
						),
						'arrow_size'      => array(
							'type'    => 'unit',
							'label'   => __( 'Arrow Size', 'bb-powerpack' ),
							'default' => '20',
							'slider'  => true,
							'units'   => array( 'px' ),
							'responsive' => true,
							'preview' => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.pp-testimonials-wrap .owl-nav button',
										'property' => 'width',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-testimonials-wrap .owl-nav button',
										'property' => 'height',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-testimonials-wrap .owl-nav button svg',
										'property' => 'height',
										'unit'     => 'px',
									),
								),
							),
						),
						'arrow_spacing' => array(
							'type'    => 'unit',
							'label'   => __( 'Arrow Spacing', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'responsive' => true,
							'preview' => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.pp-testimonials-wrap .owl-nav.position-bottom',
										'property' => 'gap',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-testimonials-wrap .owl-nav.position-side button.owl-prev',
										'property' => 'left',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-testimonials-wrap .owl-nav.position-side button.owl-next',
										'property' => 'right',
										'unit'     => 'px',
									),
								),
							),
						),
						'arrow_border' => array(
							'type'       => 'border',
							'label'      => __( 'Arrow Border', 'bb-powerpack' ),
							'show_reset' => true,
							'show_alpha' => true,
							'preview'     => array(
								'type' => 'css',
								'selector' => '.pp-testimonials-wrap .owl-nav button',
							),
						),
					),
				),
				'dot_nav'   => array( // Section
					'title'  => __( 'Navigation Dots', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields' => array( // Section Fields
						'dots'             => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Dots', 'bb-powerpack' ),
							'default' => '1',
							'options' => array(
								'1' => __( 'Yes', 'bb-powerpack' ),
								'0' => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'1' => array(
									'fields' => array( 'dot_color', 'active_dot_color' ),
								),
							),
						),
						'dot_color'        => array(
							'type'        => 'color',
							'label'       => __( 'Dot Color', 'bb-powerpack' ),
							'default'     => '999999',
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-wrap .bx-wrapper .bx-pager a',
								'property' => 'background',
							),
						),
						'active_dot_color' => array(
							'type'        => 'color',
							'label'       => __( 'Active Dot Color', 'bb-powerpack' ),
							'default'     => '333333',
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-wrap .bx-wrapper .bx-pager a.active',
								'property' => 'background',
							),
						),
					),
				),
			),
		),
		'testimonials' => array( // Tab
			'title'    => __( 'Testimonials', 'bb-powerpack' ), // Tab title
			'sections' => array( // Tab Sections
				'general' => array( // Section
					'title'  => '', // Section Title
					'fields' => array( // Section Fields
						'testimonials' => array(
							'type'         => 'form',
							'label'        => __( 'Testimonial', 'bb-powerpack' ),
							'form'         => 'pp_testimonials_form', // ID from registered form below
							'preview_text' => 'title', // Name of a field to use for the preview text
							'multiple'     => true,
						),
					),
				),
			),
		),
		'layouts'      => array(
			'title'    => __( 'Layout', 'bb-powerpack' ),
			'sections' => array(
				'layout' => array( // Section
					'title'  => '', // Section Title
					'fields' => array( // Section Fields
						'testimonial_layout' => array(
							'type'    => 'layout',
							'label'   => __( 'Layout', 'bb-powerpack' ),
							'default' => 1,
							'options' => array(
								'1' => BB_POWERPACK_URL . 'modules/pp-testimonials/images/layout-1.jpg',
								'2' => BB_POWERPACK_URL . 'modules/pp-testimonials/images/layout-2.jpg',
								'3' => BB_POWERPACK_URL . 'modules/pp-testimonials/images/layout-3.jpg',
								'4' => BB_POWERPACK_URL . 'modules/pp-testimonials/images/layout-4.jpg',
								'5' => BB_POWERPACK_URL . 'modules/pp-testimonials/images/layout-5.jpg',
							),
							'toggle' => array(
								4 => array(
									'fields' => array( 'padding_top' ),
								),
							),
						),
					),
				),
			),
		),
		'styles'       => array( // Tab
			'title'    => __( 'Style', 'bb-powerpack' ), // Tab title
			'sections' => array( // Tab Sections
				'box' => array(
					'title'  => __( 'Box', 'bb-powerpack' ),
					'fields' => array( // Section Fields
						'full_box_bg' => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'  => 'css',
								'selector' => '.pp-testimonial',
								'property' => 'background',
							),
						),
						'full_box_border'          => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-testimonial',
								'property' => 'border',
							),
						),
						'full_box_padding' => array(
							'type'				=> 'dimension',
							'label'				=> __('Padding', 'bb-powerpack'),
							'default'			=> '',
							'units'				=> array('px'),
							'slider'			=> true,
							'responsive'		=> true,
						),
					),
				),
				'content_box' => array(
					'title'  => __( 'Content Box', 'bb-powerpack' ),
					'collapsed' => true,
					'fields' => array( // Section Fields
						'layout_4_content_bg' => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.pp-testimonials .layout-1 .pp-content-wrapper',
										'property' => 'background-color',
									),
									array(
										'selector' => '.pp-testimonials .layout-2 .pp-content-wrapper',
										'property' => 'background-color',
									),
									array(
										'selector' => '.pp-testimonials .layout-3 .pp-content-wrapper',
										'property' => 'background-color',
									),
									array(
										'selector' => '.pp-testimonials .layout-4 .layout-4-content',
										'property' => 'background-color',
									),
									array(
										'selector' => '.pp-testimonials .layout-5 .pp-content-wrapper',
										'property' => 'background-color',
									),
									array(
										'selector' => '.pp-testimonials .pp-arrow-top',
										'property' => 'border-bottom-color',
									),
									array(
										'selector' => '.pp-testimonials .pp-arrow-bottom',
										'property' => 'border-top-color',
									),
									array(
										'selector' => '.pp-testimonials .pp-arrow-left',
										'property' => 'border-right-color',
									),
								),
							),
						),
						'box_border'          => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-testimonial.layout-1 .pp-content-wrapper, .pp-testimonial.layout-2 .pp-content-wrapper, .pp-testimonial.layout-3 .pp-content-wrapper, .pp-testimonial.layout-4 .layout-4-content, .pp-testimonial.layout-5 .pp-content-wrapper',
								'property' => 'border',
							),
						),
						'box_padding' => array(
							'type'				=> 'dimension',
							'label'				=> __('Padding', 'bb-powerpack'),
							'default'			=> '',
							'units'				=> array('px'),
							'slider'			=> true,
							'responsive'		=> true,
						),
						'show_arrow'          => array(
							'type'    => 'pp-switch',
							'default' => 'no',
							'label'   => __( 'Show Content Indicator', 'bb-powerpack' ),
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
					),
				),
				'image_box'     => array(
					'title'  => __( 'Image Box', 'bb-powerpack' ),
					'collapsed' => true,
					'fields' => array( // Section Fields
						'image_size'   => array(
							'type'    => 'unit',
							'label'   => __( 'Image Size', 'bb-powerpack' ),
							'default' => 100,
							'units'   => array( 'px' ),
							'slider'  => true,
						),
						'image_border' => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-image img',
								'property' => 'border',
							),
						),
						'padding_top' => array(
							'type'	=> 'unit',
							'label' => __( 'Top Spacing', 'bb-powerpack' ),
							'default' => '',
							'description' => __( 'Only applicable for the layout 4', 'bb-powerpack' ),
							'units' => array( 'px' ),
							'prevuew'	=> array(
								'type' => 'css',
								'selector' => '.pp-testimonials .layout-4',
								'property' => 'padding-top',
								'unit' => 'px',
							),
						),
					),
				),
			),
		),
		'typography'   => array(
			'title'    => __( 'Typography', 'bb-powerpack' ),
			'sections' => array(
				'heading_fonts'  => array(
					'title'  => __( 'Heading', 'bb-powerpack' ),
					'fields' => array( // Section Fields
						'heading_tag'    => array(
							'type'          => 'select',
							'label'         => __('HTML Tag', 'bb-powerpack'),
							'default'       => 'h2',
							'sanitize' => array( 'pp_esc_tags', 'h2' ),
							'options'       => array(
								'h1'            => 'H1',
								'h2'            => 'H2',
								'h3'            => 'H3',
								'h4'            => 'H4',
								'h5'            => 'H5',
								'h6'            => 'H6',
								'div'			=> 'div',
								'p'				=> 'p',
								'span'			=> 'span',
							)
						),
						'heading_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-heading',
							),
						),
						'heading_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-heading',
								'property' => 'color',
							),
						),
					),
				),
				'title_fonts'    => array(
					'title'  => __( 'Name', 'bb-powerpack' ),
					'fields' => array(
						'title_tag' => array(
							'type'          => 'select',
							'label'         => __('HTML Tag', 'bb-powerpack'),
							'default'       => 'div',
							'sanitize' => array( 'pp_esc_tags', 'div' ),
							'options'       => array(
								'h1'            => 'H1',
								'h2'            => 'H2',
								'h3'            => 'H3',
								'h4'            => 'H4',
								'h5'            => 'H5',
								'h6'            => 'H6',
								'div'			=> 'div',
								'p'				=> 'p',
								'span'			=> 'span',
							)
						),
						'title_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-name',
							),
						),
						'title_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-name',
								'property' => 'color',
							),
						),
						'title_margin'     => array(
							'type'        => 'pp-multitext',
							'label'       => __( 'Margin', 'bb-powerpack' ),
							'description' => 'px',
							'default'     => array(
								'top'    => '',
								'bottom' => '',
							),
							'options'     => array(
								'top'    => array(
									'placeholder' => __( 'Top', 'bb-powerpack' ),
									'tooltip'     => __( 'Top', 'bb-powerpack' ),
									'icon'        => 'fa-long-arrow-up',
									'preview'     => array(
										'selector' => '.pp-testimonials-name',
										'property' => 'margin-top',
										'unit'     => 'px',
									),
								),
								'bottom' => array(
									'placeholder' => __( 'Bottom', 'bb-powerpack' ),
									'tooltip'     => __( 'Bottom', 'bb-powerpack' ),
									'icon'        => 'fa-long-arrow-down',
									'preview'     => array(
										'selector' => '.pp-testimonials-name',
										'property' => 'margin-bottom',
										'unit'     => 'px',
									),
								),
							),
						),
					),
				),
				'subtitle_fonts' => array(
					'title'  => __( 'Designation', 'bb-powerpack' ),
					'fields' => array(
						'subtitle_tag' => array(
							'type'          => 'select',
							'label'         => __('HTML Tag', 'bb-powerpack'),
							'default'       => 'div',
							'sanitize' => array( 'pp_esc_tags', 'div' ),
							'options'       => array(
								'h1'            => 'H1',
								'h2'            => 'H2',
								'h3'            => 'H3',
								'h4'            => 'H4',
								'h5'            => 'H5',
								'h6'            => 'H6',
								'div'			=> 'div',
								'p'				=> 'p',
								'span'			=> 'span',
							)
						),
						'subtitle_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-designation',
							),
						),
						'subtitle_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-designation',
								'property' => 'color',
							),
						),
						'subtitle_margin'     => array(
							'type'        => 'pp-multitext',
							'label'       => __( 'Margin', 'bb-powerpack' ),
							'description' => 'px',
							'default'     => array(
								'top'    => '',
								'bottom' => '',
							),
							'options'     => array(
								'top'    => array(
									'placeholder' => __( 'Top', 'bb-powerpack' ),
									'tooltip'     => __( 'Top', 'bb-powerpack' ),
									'icon'        => 'fa-long-arrow-up',
									'preview'     => array(
										'selector' => '.pp-testimonials-designation',
										'property' => 'margin-top',
										'unit'     => 'px',
									),
								),
								'bottom' => array(
									'placeholder' => __( 'Bottom', 'bb-powerpack' ),
									'tooltip'     => __( 'Bottom', 'bb-powerpack' ),
									'icon'        => 'fa-long-arrow-down',
									'preview'     => array(
										'selector' => '.pp-testimonials-designation',
										'property' => 'margin-bottom',
										'unit'     => 'px',
									),
								),
							),
						),
					),
				),
				'testimonial_title_fonts' => array(
					'title'  => __( 'Testimonial Title', 'bb-powerpack' ),
					'fields' => array(
						'testimonial_title_tag' => array(
							'type'          => 'select',
							'label'         => __('HTML Tag', 'bb-powerpack'),
							'default'       => 'h3',
							'sanitize' => array( 'pp_esc_tags', 'h3' ),
							'options'       => array(
								'h1'            => 'H1',
								'h2'            => 'H2',
								'h3'            => 'H3',
								'h4'            => 'H4',
								'h5'            => 'H5',
								'h6'            => 'H6',
								'div'			=> 'div',
								'p'				=> 'p',
								'span'			=> 'span',
							)
						),
						'testimonial_title_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-title',
							),
						),
						'testimonial_title_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-title',
								'property' => 'color',
							),
						),
						'testimonial_title_margin'  => array(
							'type'        => 'pp-multitext',
							'label'       => __( 'Margin', 'bb-powerpack' ),
							'description' => 'px',
							'default'     => array(
								'top'    => '',
								'bottom' => '',
							),
							'options'     => array(
								'top'    => array(
									'placeholder' => __( 'Top', 'bb-powerpack' ),
									'tooltip'     => __( 'Top', 'bb-powerpack' ),
									'icon'        => 'fa-long-arrow-up',
									'preview'     => array(
										'selector' => '.pp-testimonials-title',
										'property' => 'margin-top',
										'unit'     => 'px',
									),
								),
								'bottom' => array(
									'placeholder' => __( 'Bottom', 'bb-powerpack' ),
									'tooltip'     => __( 'Bottom', 'bb-powerpack' ),
									'icon'        => 'fa-long-arrow-down',
									'preview'     => array(
										'selector' => '.pp-testimonials-title',
										'property' => 'margin-bottom',
										'unit'     => 'px',
									),
								),
							),
						),
					),
				),
				'testimonial_fonts'  => array(
					'title'  => __( 'Testimonial', 'bb-powerpack' ),
					'fields' => array(
						'text_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-content',
							),
						),
						'text_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-testimonials-content',
								'property' => 'color',
							),
						),
						'content_margin'  => array(
							'type'        => 'pp-multitext',
							'label'       => __( 'Margin', 'bb-powerpack' ),
							'description' => 'px',
							'default'     => array(
								'top'    => '',
								'bottom' => '',
							),
							'options'     => array(
								'top'    => array(
									'placeholder' => __( 'Top', 'bb-powerpack' ),
									'tooltip'     => __( 'Top', 'bb-powerpack' ),
									'icon'        => 'fa-long-arrow-up',
									'preview'     => array(
										'selector' => '.pp-testimonials-content',
										'property' => 'margin-top',
										'unit'     => 'px',
									),
								),
								'bottom' => array(
									'placeholder' => __( 'Bottom', 'bb-powerpack' ),
									'tooltip'     => __( 'Bottom', 'bb-powerpack' ),
									'icon'        => 'fa-long-arrow-down',
									'preview'     => array(
										'selector' => '.pp-testimonials-content',
										'property' => 'margin-bottom',
										'unit'     => 'px',
									),
								),
							),
						),
					),
				),
			),
		),
	)
);

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form(
	'pp_testimonials_form',
	array(
		'title' => __( 'Add Testimonial', 'bb-powerpack' ),
		'tabs'  => array(
			'general' => array( // Tab
				'title'    => __( 'General', 'bb-powerpack' ), // Tab title
				'sections' => array( // Tab Sections
					'title'   => array(
						'title'  => '',
						'fields' => array(
							'title'    => array(
								'type'        => 'text',
								'label'       => __( 'Name', 'bb-powerpack' ),
							),
							'subtitle' => array(
								'type'        => 'text',
								'label'       => __( 'Designation', 'bb-powerpack' ),
							),
							'photo'    => array(
								'type'        => 'photo',
								'label'       => __( 'Photo', 'bb-powerpack' ),
								'show_remove' => true,
							),
						),
					),
					'content' => array( // Section
						'title'  => __( 'Content', 'bb-powerpack' ), // Section Title
						'fields' => array( // Section Fields
							'testimonial_title' => array(
								'type'    => 'text',
								'label'   => __( 'Title', 'bb-powerpack' ),
								'default' => ''
							),
							'testimonial' => array(
								'type'  => 'editor',
								'label' => '',
							),
						),
					),
				),
			),
		),
	)
);

