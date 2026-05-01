<?php

/**
 * @class PPGravityFormModule
 */
class PPGravityFormModule extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'          => __( 'Gravity Form', 'bb-powerpack' ),
				'description'   => __( 'A module for Gravity Form.', 'bb-powerpack' ),
				'group'         => pp_get_modules_group(),
				'category'      => pp_get_modules_cat( 'form_style' ),
				'dir'           => BB_POWERPACK_DIR . 'modules/pp-gravity-form/',
				'url'           => BB_POWERPACK_URL . 'modules/pp-gravity-form/',
				'editor_export' => true, // Defaults to true and can be omitted.
				'enabled'       => true, // Defaults to true and can be omitted.
			)
		);

		add_action( 'wp_ajax_pp_gf_forms_dropdown_html', array( $this, 'gf_forms_dropdown_html' ) );
		add_action( 'wp_ajax_nopriv_pp_gf_forms_dropdown_html', array( $this, 'gf_forms_dropdown_html' ) );
	}

	public function enqueue_scripts() {
		if ( isset( $_GET['fl_builder'] ) ) {
			wp_enqueue_style( 'gforms_formsmain_css' );
		}
	}

	public function gf_forms_dropdown_html() {
		$options = '<option value="">' . __( 'None', 'bb-powerpack' ) . '</option>';

		global $wpdb;

		if ( class_exists( 'GFForms' ) ) {
			$form_table_name = GFFormsModel::get_form_table_name();
			$id              = 0;
			$forms           = $wpdb->get_results( $wpdb->prepare( 'SELECT id, title FROM ' . $form_table_name . ' WHERE id != %d', $id ), object );
			if ( ! is_wp_error( $forms ) ) {
				foreach ( $forms as $form ) {
					$options .= '<option value="' . $form->id . '">' . $form->title . '</option>';
				}
			}
		}

		echo $options;
		die;
	}

	public function filter_settings( $settings, $helper ) {
		// Handle old Form border and radius fields.
		$settings = PP_Module_Fields::handle_border_field(
			$settings,
			array(
				'form_border_style'  => array(
					'type' => 'style',
				),
				'form_border_width'  => array(
					'type' => 'width',
				),
				'form_border_color'  => array(
					'type' => 'color',
				),
				'form_border_radius' => array(
					'type' => 'radius',
				),
			),
			'form_border_group'
		);

		if ( isset( $settings->input_field_border_width ) && isset( $settings->input_field_border_position ) ) {
			$border_width = $settings->input_field_border_width;
			$border_pos = explode( '-', $settings->input_field_border_position );

			if ( ! isset( $border_pos[1] ) ) {
				$settings->input_field_border_width_array = array(
					'top'    => $border_width,
					'left'   => $border_width,
					'bottom' => $border_width,
					'right'  => $border_width,
				);
			} else {
				$settings->input_field_border_width_array = array();
				$settings->input_field_border_width_array[ $border_pos[1] ] = $border_width;
			}

			unset( $settings->input_field_border_width );
			unset( $settings->input_field_border_position );
		}

		// Handle old Input border and radius fields.
		$settings = PP_Module_Fields::handle_border_field(
			$settings,
			array(
				'input_field_border_width_array'  => array(
					'type' => 'width',
					'style' => 'solid',
				),
				'input_field_border_color'  => array(
					'type' => 'color',
				),
				'input_field_border_radius' => array(
					'type' => 'radius',
				),
			),
			'input_field_border_group'
		);

		if ( isset( $settings->input_field_box_shadow ) ) {
			if ( 'none' === $settings->input_field_box_shadow ) {
				if ( ! isset( $settings->input_field_border_group['shadow'] ) || ! is_array( $settings->input_field_border_group['shadow'] ) ) {
					$settings->input_field_border_group['shadow'] = array();
				}
				$settings->input_field_border_group['shadow']['color'] = '';
				$settings->input_field_border_group['shadow']['horizontal'] = 0;
				$settings->input_field_border_group['shadow']['vertical'] = 0;
				$settings->input_field_border_group['shadow']['blur'] = 0;
				$settings->input_field_border_group['shadow']['spread'] = 0;
			}

			unset( $settings->input_field_box_shadow );
		}

		// Handle old Button border and radius fields.
		$settings = PP_Module_Fields::handle_border_field(
			$settings,
			array(
				'button_border_width'  => array(
					'type' => 'width',
				),
				'button_border_color'  => array(
					'type' => 'color',
				),
				'button_border_radius' => array(
					'type' => 'radius',
				),
			),
			'button_border_group'
		);

		// Handle Form old padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'form_padding', 'padding', 'form_padding' );

		// Handle title's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'title_font_family' => array(
					'type' => 'font',
				),
				'title_font_size'   => array(
					'type' => 'font_size',
				),
				'title_alignment'   => array(
					'type' => 'text_align',
				),
			),
			'title_typography'
		);

		// Handle Description old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'description_font_family' => array(
					'type' => 'font',
				),
				'description_font_size'   => array(
					'type' => 'font_size',
				),
				'description_alignment'   => array(
					'type' => 'text_align',
				),
			),
			'description_typography'
		);

		// Handle Section old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'section_font'      => array(
					'type' => 'font',
				),
				'section_font_size' => array(
					'type' => 'font_size',
				),
			),
			'section_typography'
		);

		if ( isset( $settings->label_font_family ) ) {
			$settings->label_font_family_old = $settings->label_font_family;
		}

		// Handle Label old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'label_font_family' => array(
					'type' => 'font',
				),
				'label_font_size'   => array(
					'type' => 'font_size',
				),
			),
			'label_typography'
		);

		// Handle Input description old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'label_font_family_old' => array(
					'type' => 'font',
				),
				'input_desc_font_size' => array(
					'type' => 'font_size',
				),
				'input_desc_line_height'   => array(
					'type' => 'line_height',
					'unit' => isset( $settings->input_desc_line_height_unit ) ? $settings->input_desc_line_height_unit : '',
				),
			),
			'input_desc_typography'
		);

		// Handle Input old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'input_font_family' => array(
					'type' => 'font',
				),
				'input_font_size' => array(
					'type' => 'font_size',
				),
				'input_field_text_alignment' => array(
					'type' => 'text_align'
				),
			),
			'input_typography'
		);

		// Handle Button old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'button_font_family' => array(
					'type' => 'font',
				),
				'button_font_size'   => array(
					'type' => 'font_size',
				),
			),
			'button_typography'
		);

		// Handle Form Background opacity  color field.
		if ( isset( $settings->form_background_opacity ) ) {
			$opacity    = $settings->form_background_opacity >= 0 ? $settings->form_background_opacity : 1;
			$color_form = $settings->form_bg_color;

			if ( ! empty( $color_form ) ) {
				$color_form              = pp_hex2rgba( pp_get_color_value( $color_form ), $opacity );
				$settings->form_bg_color = $color_form;
			}

			unset( $settings->form_background_opacity );
		}

		// Handle Input Background opacity  color field.
		if ( isset( $settings->input_field_background_opacity ) ) {
			$opacity     = $settings->input_field_background_opacity >= 0 ? $settings->input_field_background_opacity : 1;
			$color_input = $settings->input_field_bg_color;

			if ( ! empty( $color_input ) ) {
				$color_input                    = pp_hex2rgba( pp_get_color_value( $color_input ), $opacity );
				$settings->input_field_bg_color = $color_input;
			}

			unset( $settings->input_field_background_opacity );
		}

		// Handle Button Background opacity  color field.
		if ( isset( $settings->button_background_opacity ) ) {
			$opacity      = $settings->button_background_opacity >= 0 ? $settings->button_background_opacity : 1;
			$color_button = $settings->button_bg_color;

			if ( ! empty( $color_button ) ) {
				$color_button              = pp_hex2rgba( pp_get_color_value( $color_button ), $opacity );
				$settings->button_bg_color = $color_button;
			}

			unset( $settings->button_background_opacity );
		}

		return $settings;
	}
}

//require_once BB_POWERPACK_DIR . 'modules/pp-gravity-form/includes/functions.php';

/**
	* Register the module and its form settings.
	*/
BB_PowerPack::register_module(
	'PPGravityFormModule',
	array(
		'form'            => array( // Tab
			'title'    => __( 'General', 'bb-powerpack' ), // Tab title
			'sections' => array( // Tab Sections
				'select_form'   => array( // Section
					'title'  => '', // Section Title
					'fields' => array( // Section Fields
						'select_form_field' => array(
							'type'        => 'select',
							'label'       => __( 'Select Form', 'bb-powerpack' ),
							'default'     => '',
							'options'     => array(),
							'connections' => array( 'string' ),
						),
					),
				),
				'form_settings' => array(
					'title'  => __( 'Settings', 'bb-powerpack' ),
					'fields' => array(
						'form_custom_title_desc' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Custom Title & Description', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'custom_title', 'custom_description' ),
								),
								'no'  => array(
									'fields' => array( 'title_field', 'description_field' ),
								),
							),
						),
						'title_field'            => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Title', 'bb-powerpack' ),
							'default' => 'true',
							'options' => array(
								'true'  => __( 'Show', 'bb-powerpack' ),
								'false' => __( 'Hide', 'bb-powerpack' ),
							),
						),
						'custom_title'           => array(
							'type'        => 'text',
							'label'       => __( 'Custom Title', 'bb-powerpack' ),
							'default'     => '',
							'description' => '',
							'connections' => array( 'string' ),
							'preview'     => array(
								'type'     => 'text',
								'selector' => '.form-title',
							),
						),
						'description_field'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Description', 'bb-powerpack' ),
							'default' => 'true',
							'options' => array(
								'true'  => __( 'Show', 'bb-powerpack' ),
								'false' => __( 'Hide', 'bb-powerpack' ),
							),
						),
						'custom_description'     => array(
							'type'        => 'textarea',
							'label'       => __( 'Custom Description', 'bb-powerpack' ),
							'default'     => '',
							'placeholder' => '',
							'rows'        => '6',
							'connections' => array( 'string', 'html' ),
							'preview'     => array(
								'type'     => 'text',
								'selector' => '.form-description',
							),
						),
						'display_labels'         => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Labels', 'bb-powerpack' ),
							'default' => 'block',
							'options' => array(
								'block' => __( 'Show', 'bb-powerpack' ),
								'none'  => __( 'Hide', 'bb-powerpack' ),
							),
						),
						'form_ajax'              => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Enable AJAX', 'bb-powerpack' ),
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'form_tab_index'         => array(
							'type'    => 'text',
							'label'   => __( 'Tab Index', 'bb-powerpack' ),
							'class'   => 'bb-gf-input input-small',
						),
					),
				),
			),
		),
		'style'           => array( // Tab
			'title'    => __( 'Style', 'bb-powerpack' ), // Tab title
			'sections' => array( // Tab Sections
				'form_setting'   => array( // Section
					'title'  => __( 'Form Background', 'bb-powerpack' ), // Section Title
					'fields' => array( // Section Fields
						'form_bg_type'            => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Background Type', 'bb-powerpack' ),
							'default' => 'color',
							'options' => array(
								'color' => __( 'Color', 'bb-powerpack' ),
								'image' => __( 'Image', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'color' => array(
									'fields' => array( 'form_bg_color' ),
								),
								'image' => array(
									'fields' => array( 'form_bg_image', 'form_bg_size', 'form_bg_repeat', 'form_bg_overlay', 'form_bg_overlay_opacity' ),
								),
							),
						),
						'form_bg_color'           => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => 'ffffff',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content',
								'property' => 'background-color',
							),
						),
						'form_bg_image'           => array(
							'type'        => 'photo',
							'label'       => __( 'Background Image', 'bb-powerpack' ),
							'default'     => '',
							'show_remove' => true,
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content',
								'property' => 'background-image',
							),
						),
						'form_bg_size'            => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Background Size', 'bb-powerpack' ),
							'default' => 'cover',
							'options' => array(
								'contain' => __( 'Contain', 'bb-powerpack' ),
								'cover'   => __( 'Cover', 'bb-powerpack' ),
							),
						),
						'form_bg_repeat'          => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Background Repeat', 'bb-powerpack' ),
							'default' => 'no-repeat',
							'options' => array(
								'repeat-x'  => __( 'Repeat X', 'bb-powerpack' ),
								'repeat-y'  => __( 'Repeat Y', 'bb-powerpack' ),
								'no-repeat' => __( 'No Repeat', 'bb-powerpack' ),
							),
						),
						'form_bg_overlay'         => array(
							'type'        => 'color',
							'label'       => __( 'Background Overlay Color', 'bb-powerpack' ),
							'default'     => '000000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
						),
						'form_bg_overlay_opacity' => array(
							'type'        => 'text',
							'label'       => __( 'Background Overlay Opacity', 'bb-powerpack' ),
							'class'       => 'bb-gf-input input-small',
							'default'     => '50',
							'description' => __( '%', 'bb-powerpack' ),
						),
					),
				),
				'form_border'    => array(
					'title'     => __( 'Form Border', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'form_border_group' => array(
							'type'       => 'border',
							'label'      => __( 'Border Style', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content',
							),
						),
					),
				),
				'form_container' => array(
					'title'     => __( 'Padding', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'form_padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'slider'     => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content',
								'property' => 'padding',
								'unit'     => 'px',
							),
							'responsive' => true,
						),
					),
				),
				'general_style'  => array( // Section
					'title'     => __( 'General', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields'    => array( // Section Fields
						'product_price_color' => array(
							'type'        => 'color',
							'label'       => __( 'Product Price Color', 'bb-powerpack' ),
							'default'     => '900900',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper span.ginput_product_price',
								'property' => 'color',
							),
						),
					),
				),
				'section_style'  => array( // Section
					'title'     => __( 'Sections', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields'    => array( // Section Fields
						'section_border_width' => array(
							'type'    => 'unit',
							'label'   => __( 'Border Width', 'bb-powerpack' ),
							'default' => 1,
							'slider'  => array(
								'px' => array(
									'min'  => 0,
									'max'  => 10,
									'step' => 1,
								),
							),
							'units'   => array( 'px' ),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content .gform_wrapper .gsection',
								'property' => 'border-bottom-width',
							),
						),
						'section_border_color' => array(
							'type'        => 'color',
							'label'       => __( 'Border Color', 'bb-powerpack' ),
							'default'     => 'cccccc',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content .gform_wrapper .gsection',
								'property' => 'border-bottom-color',
							),
						),
						'section_field_margin' => array(
							'type'    => 'unit',
							'label'   => __( 'Margin Bottom', 'bb-powerpack' ),
							'slider'  => array(
								'px' => array(
									'min'  => 0,
									'max'  => 50,
									'step' => 1,
								),
							),
							'units'   => array( 'px' ),
							'default' => '20',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content .gform_wrapper .gsection',
								'property' => 'margin-bottom',
							),
						),
					),
				),
			),
		),
		'input_style'     => array(
			'title'    => __( 'Inputs', 'bb-powerpack' ),
			'sections' => array(
				'input_general'     => array( // Section
					'title'     => '', // Section Title
					'fields'    => array( // Section Fields
						'input_field_width'          => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Full Width', 'bb-powerpack' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'bb-powerpack' ),
								'false' => __( 'No', 'bb-powerpack' ),
							),
						),
						'input_field_height'         => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Height', 'bb-powerpack' ),
							'default' => 'auto',
							'options' => array(
								'auto'   => __( 'Auto', 'bb-powerpack' ),
								'custom' => __( 'Custom', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'custom' => array(
									'fields' => array( 'input_field_height_custom' ),
								),
							),
						),
						'input_field_height_custom'  => array(
							'type'    => 'unit',
							'label'   => __( 'Custom Height', 'bb-powerpack' ),
							'default' => '45',
							'slider'  => true,
							'units'   => array( 'px' ),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]), .gform_wrapper .gfield select',
								'property' => 'height',
								'unit'     => 'px',
							),
						),
						'input_field_padding'        => array(
							'type'       => 'unit',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'slider'     => true,
							'units'      => array( 'px' ),
							'default'    => '',
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]), .gform_wrapper .gfield select, .gform_wrapper .gfield textarea',
								'property' => 'padding',
								'unit'     => 'px',
							),
							'responsive' => true,
						),
						'input_field_margin'         => array(
							'type'    => 'unit',
							'label'   => __( 'Margin Bottom', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'default' => '',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]), .gform_wrapper .gfield select, .gform_wrapper .gfield textarea',
								'property' => 'margin-bottom',
								'unit'     => 'px',
							),
						),
					),
				),
				'input_background'  => array(
					'title'  => __( 'Colors', 'bb-powerpack' ),
					'collapsed' => true,
					'fields' => array(
						'input_field_text_color' => array(
							'type'        => 'color',
							'label'       => __( 'Text Color', 'bb-powerpack' ),
							'default'     => '',
							'connections' => array( 'color' ),
							'show_reset'	=> true,
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]), .gform_wrapper .gfield select, .gform_wrapper .gfield textarea',
								'property' => 'color',
							),
						),
						'input_field_bg_color'   => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]), .gform_wrapper .gfield select, .gform_wrapper .gfield textarea',
								'property' => 'background-color',
							),
						),
						'form_label_color'         => array(
							'type'        => 'color',
							'label'       => __( 'Label Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content .gform_wrapper .gfield .gfield_label, .pp-gf-content .gform_wrapper table.gfield_list thead th, .pp-gf-content .gform_wrapper span.ginput_product_price_label, .pp-gf-content .gform_wrapper span.ginput_quantity_label, .pp-gf-content .gform_wrapper .gfield_html',
								'property' => 'color',
							),
						),
						'form_required_text_color'         => array(
							'type'        => 'color',
							'label'       => __( 'Label "Required" Text Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content .gform_wrapper .gfield_required',
								'property' => 'color',
							),
						),
						'input_desc_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Input Description Color', 'bb-powerpack' ),
							'default'     => '',
							'connections' => array( 'color' ),
							'show_reset'	=> true,
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-gf-content .gform_wrapper .gfield .gfield_description',
								'property' => 'color',
							),
						),
					),
				),
				'input_border'      => array(
					'title'     => __( 'Border & Shadow', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'input_field_border_group' => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]), .gform_wrapper .gfield select, .gform_wrapper .gfield textarea',
							),
						),
						'input_field_focus_color'     => array(
							'type'        => 'color',
							'label'       => __( 'Focus Border Color', 'bb-powerpack' ),
							'default'     => '719ece',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]):focus, .gform_wrapper .gfield select:focus, .gform_wrapper .gfield textarea:focus',
								'property' => 'border-color',
							),
						),
					),
				),
				'placeholder_style' => array( // Section
					'title'     => __( 'Placeholder', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields'    => array( // Section Fields
						'gf_input_placeholder_display' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Placeholder', 'bb-powerpack' ),
							'default' => 'block',
							'options' => array(
								'block' => __( 'Yes', 'bb-powerpack' ),
								'none'  => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'block' => array(
									'fields' => array( 'gf_input_placeholder_color' ),
								),
							),
						),
						'gf_input_placeholder_color'   => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => 'eeeeee',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input::-webkit-input-placeholder, .gform_wrapper .gfield select::-webkit-input-placeholder, .gform_wrapper .gfield textarea::-webkit-input-placeholder',
								'property' => 'color',
							),
						),
					),
				),
				'radio_cb_style'    => array(
					'title'     => __( 'Radio & Checkbox', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'radio_cb_style'           => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Enable Custom Style', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'radio_cb_size', 'radio_cb_color', 'radio_cb_checked_color', 'radio_cb_border_width', 'radio_cb_border_color', 'radio_cb_radius', 'radio_cb_checkbox_radius' ),
								),
							),
						),
						'radio_cb_size'            => array(
							'type'    => 'unit',
							'label'   => __( 'Size', 'bb-powerpack' ),
							'default' => '15',
							'slider'  => true,
							'units'   => array( 'px' ),
							'class'   => 'bb-gf-input input-small',
						),
						'radio_cb_color'           => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => 'dddddd',
							'show_reset'  => true,
							'connections' => array( 'color' ),
						),
						'radio_cb_checked_color'   => array(
							'type'        => 'color',
							'label'       => __( 'Checked Color', 'bb-powerpack' ),
							'default'     => '999999',
							'show_reset'  => true,
							'connections' => array( 'color' ),
						),
						'radio_cb_border_width'    => array(
							'type'    => 'unit',
							'label'   => __( 'Border Width', 'bb-powerpack' ),
							'default' => '1',
							'slider'  => true,
							'units'   => array( 'px' ),
						),
						'radio_cb_border_color'    => array(
							'type'        => 'color',
							'label'       => __( 'Border Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
						),
						'radio_cb_radius'          => array(
							'type'    => 'unit',
							'label'   => __( 'Radio Round Corners', 'bb-powerpack' ),
							'default' => '50',
							'slider'  => true,
							'units'   => array( 'px' ),
						),
						'radio_cb_checkbox_radius' => array(
							'type'    => 'unit',
							'label'   => __( 'Checkbox Round Corners', 'bb-powerpack' ),
							'default' => '0',
							'slider'  => true,
							'units'   => array( 'px' ),
						),
					),
				),
				'file_upload_style' => array(
					'title'     => __( 'File Upload', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'file_bg_color'           => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input[type=file]',
								'property' => 'background-color',
							),
						),
						'file_text_color'         => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input[type=file]',
								'property' => 'color',
							),
						),
						'file_border_style'       => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Border Style', 'bb-powerpack' ),
							'default' => 'none',
							'options' => array(
								'none'   => __( 'None', 'bb-powerpack' ),
								'solid'  => __( 'Solid', 'bb-powerpack' ),
								'dashed' => __( 'Dashed', 'bb-powerpack' ),
								'dotted' => __( 'Dotted', 'bb-powerpack' ),
							),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input[type=file]',
								'property' => 'border-style',
							),
							'toggle'  => array(
								'solid'  => array(
									'fields' => array( 'file_border_width', 'file_border_color' ),
								),
								'dashed' => array(
									'fields' => array( 'file_border_width', 'file_border_color' ),
								),
								'dotted' => array(
									'fields' => array( 'file_border_width', 'file_border_color' ),
								),
							),
						),
						'file_border_width'       => array(
							'type'    => 'unit',
							'label'   => __( 'Border Width', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'default' => '',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input[type=file]',
								'property' => 'border-width',
								'unit'     => 'px',
							),
						),
						'file_border_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Border Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input[type=file]',
								'property' => 'border-color',
							),
						),
						'file_horizontal_padding' => array(
							'type'    => 'unit',
							'label'   => __( 'Horizontal Padding', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'default' => '',
							'preview' => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.gform_wrapper .gfield input[type=file]',
										'property' => 'padding-left',
										'unit'     => 'px',
									),
									array(
										'selector' => '.gform_wrapper .gfield input[type=file]',
										'property' => 'padding-right',
										'unit'     => 'px',
									),
								),
							),
						),
						'file_vertical_padding'   => array(
							'type'    => 'unit',
							'label'   => __( 'Vertical Padding', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'default' => '',
							'preview' => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.gform_wrapper .gfield input[type=file]',
										'property' => 'padding-top',
										'unit'     => 'px',
									),
									array(
										'selector' => '.gform_wrapper .gfield input[type=file]',
										'property' => 'padding-bottom',
										'unit'     => 'px',
									),
								),
							),
						),
					),
				),
			),
		),
		'button_style'    => array(
			'title'    => __( 'Button', 'bb-powerpack' ),
			'sections' => array(
				'button_bg'       => array(
					'title'  => __( 'Colors', 'bb-powerpack' ),
					'fields' => array(
						'button_text_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Text Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gform-button, .gform_wrapper .gform_footer .gform_button, .gform_wrapper .gform_page_footer .button',
								'property' => 'color',
							),
						),
						'button_hover_text_color' => array(
							'type'        => 'color',
							'label'       => __( 'Text Color Hover', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gform-button:hover, .gform_wrapper .gform_footer .gform_button:hover, .gform_wrapper .gform_page_footer .button:hover',
								'property' => 'color',
							),
						),
						'button_bg_color'         => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gform-button, .gform_wrapper .gform_footer .gform_button, .gform_wrapper .gform_page_footer .button',
								'property' => 'background-color',
							),
						),
						'button_hover_bg_color'   => array(
							'type'        => 'color',
							'label'       => __( 'Background Color Hover', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gform-button:hover, .gform_wrapper .gform_footer .gform_button:hover, .gform_wrapper .gform_page_footer .button:hover',
								'property' => 'background-color',
							),
						),
					),
				),
				'button_border'   => array(
					'title'     => __( 'Border & Shadow', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'button_border_group' => array(
							'type'       => 'border',
							'label'      => __( 'Border Style', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gform-button, .gform_wrapper .gform_footer .gform_button, .gform_wrapper .gform_page_footer .button',
							),
						),
					),
				),
				'button_settings' => array( // Section
					'title'     => __( 'Size & Alignment', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields'    => array( // Section Fields
						'button_width'        => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Full Width', 'bb-powerpack' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'bb-powerpack' ),
								'false' => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'false' => array(
									'fields' => array( 'button_width_size', 'button_alignment' ),
								),
							),
						),
						'button_custom_width' => array(
							'type'    => 'unit',
							'label'   => __( 'Custom Width', 'bb-powerpack' ),
							'default' => '',
							'slider'  => true,
							'responsive'  => true,
							'units'   => array( 'px' ),
						),
						'button_alignment'    => array(
							'type'    => 'align',
							'label'   => __( 'Button Alignment', 'bb-powerpack' ),
							'default' => 'left',
						),
					),
				),
				'button_corners'  => array(
					'title'     => __( 'Corners & Padding', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'button_padding_top_bottom' => array(
							'type'       => 'unit',
							'label'      => __( 'Top/Bottom Padding', 'bb-powerpack' ),
							'slider'     => true,
							'units'      => array( 'px' ),
							'default'    => '',
							'preview'    => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.gform_wrapper .gform-button, .gform_wrapper .gform_footer .gform_button, .gform_wrapper .gform_page_footer .button',
										'property' => 'padding-top',
										'unit'     => 'px',
									),
									array(
										'selector' => '.gform_wrapper .gform-button, .gform_wrapper .gform_footer .gform_button, .gform_wrapper .gform_page_footer .button',
										'property' => 'padding-bottom',
										'unit'     => 'px',
									),
								),
							),
							'responsive' => true,
						),
						'button_padding_left_right' => array(
							'type'       => 'unit',
							'label'      => __( 'Left/Right Padding', 'bb-powerpack' ),
							'slider'     => true,
							'units'      => array( 'px' ),
							'default'    => '',
							'preview'    => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.gform_wrapper .gform-button, .gform_wrapper .gform_footer .gform_button, .gform_wrapper .gform_page_footer .button',
										'property' => 'padding-left',
										'unit'     => 'px',
									),
									array(
										'selector' => '.gform_wrapper .gform-button, .gform_wrapper .gform_footer .gform_button, .gform_wrapper .gform_page_footer .button',
										'property' => 'padding-right',
										'unit'     => 'px',
									),
								),
							),
							'responsive' => true,
						),
					),
				),
			),
		),
		'error_style'     => array(
			'title'    => __( 'Errors', 'bb-powerpack' ),
			'sections' => array(
				'form_error_styling' => array( // Section
					'title'  => __( 'Errors Style', 'bb-powerpack' ), // Section Title
					'fields' => array( // Section Fields
						'validation_error'              => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Submission Error', 'bb-powerpack' ),
							'default' => 'block',
							'options' => array(
								'block' => __( 'Show', 'bb-powerpack' ),
								'none'  => __( 'Hide', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'block' => array(
									'fields'   => array( 'validation_error_color' ),
									'sections' => array( 'errors_typography' ),
								),
							),
						),
						'validation_error_color'        => array(
							'type'        => 'color',
							'label'       => __( 'Submission Error Text Color', 'bb-powerpack' ),
							'default'     => '790000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .validation_error',
								'property' => 'color',
							),
						),
						'validation_error_border_color' => array(
							'type'        => 'color',
							'label'       => __( 'Error Border Color', 'bb-powerpack' ),
							'default'     => '790000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .validation_error',
								'property' => 'border-color',
							),
						),
						'form_error_field_background_color' => array(
							'type'        => 'color',
							'label'       => __( 'Error Field Background Color', 'bb-powerpack' ),
							'default'     => 'ffdfe0',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield.gfield_error',
								'property' => 'color',
							),
						),
						'form_error_field_label_color'  => array(
							'type'        => 'color',
							'label'       => __( 'Error Field Label Color', 'bb-powerpack' ),
							'default'     => '790000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield.gfield_error .gfield_label',
								'property' => 'color',
							),
						),
						'form_error_input_border_color' => array(
							'type'        => 'color',
							'label'       => __( 'Error Field Input Border Color', 'bb-powerpack' ),
							'default'     => '790000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield_error .ginput_container input, .gform_wrapper .gfield_error .ginput_container select, .gform_wrapper .gfield_error .ginput_container textarea',
								'property' => 'color',
							),
						),
						'form_error_input_border_width' => array(
							'type'    => 'unit',
							'label'   => __( 'Error Field Input Border Width', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'default' => '1',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield_error .ginput_container input, .gform_wrapper .gfield_error .ginput_container select, .gform_wrapper .gfield_error .ginput_container textarea',
								'property' => 'border-width',
								'unit'     => 'px',
							),
						),
						'validation_message'            => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Error Field Message', 'bb-powerpack' ),
							'default' => 'block',
							'options' => array(
								'block' => __( 'Show', 'bb-powerpack' ),
								'none'  => __( 'Hide', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'block' => array(
									'fields' => array( 'validation_message_color' ),
								),
							),
						),
						'validation_message_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Error Field Message Color', 'bb-powerpack' ),
							'default'     => '790000',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield_error .validation_message',
								'property' => 'color',
							),
						),
					),
				),
			),
		),
		'messages_style'  => array(
			'title'    => __( 'Messages', 'bb-powerpack' ),
			'sections' => array(
				'message_style' => array(
					'title'  => __( 'Success Message', 'bb-powerpack' ),
					'fields' => array(
						'message_bg_color'     => array(
							'type'       => 'color',
							'label'      => __( 'Background Color', 'bb-powerpack' ),
							'show_reset' => true,
							'show_alpha' => true,
							'connections' => array( 'color' ),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_confirmation_wrapper',
								'property' => 'background-color',
							),
						),
						'message_color'        => array(
							'type'       => 'color',
							'label'      => __( 'Text Color', 'bb-powerpack' ),
							'show_reset' => true,
							'connections' => array( 'color' ),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_confirmation_wrapper .gform_confirmation_message',
								'property' => 'color',
							),
						),
						'message_border_group' => array(
							'type'       => 'border',
							'label'      => __( 'Border Style', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_confirmation_wrapper',
							),
						),
						'message_padding'      => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'slider'     => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_confirmation_wrapper',
								'property' => 'padding',
								'unit'     => 'px',
							),
							'responsive' => true,
						),
						'message_typography'   => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_confirmation_wrapper .gform_confirmation_message',
							),
						),
					),
				),
			),
		),
		'form_typography' => array( // Tab
			'title'    => __( 'Typography', 'bb-powerpack' ), // Tab title
			'sections' => array( // Tab Sections
				'title_typography'       => array( // Section
					'title'  => __( 'Title', 'bb-powerpack' ), // Section Title
					'fields' => array( // Section Fields
						'title_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_title, .form-title',
							),
						),
						'title_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_title, .form-title',
								'property' => 'color',
							),
						),
					),
				),
				'description_typography' => array(
					'title'     => __( 'Description', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'description_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_description, .form-description',
							),
						),
						'description_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_description, .form-description',
								'property' => 'color',
							),
						),
					),
				),
				'section_typography'     => array(
					'title'     => __( 'Sections', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'section_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper h2.gsection_title, .gform_wrapper h3.gsection_title',
							),
						),
						'section_text_color' => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '333333',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper h2.gsection_title, .gform_wrapper h3.gsection_title',
								'property' => 'color',
							),
						),
					),
				),
				'label_typography'       => array( // Section
					'title'     => __( 'Label', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields'    => array( // Section Fields
						'label_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Label Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield .gfield_label',
							),
						),
						'radio_checkbox_font_size' => array(
							'type'    => 'unit',
							'label'   => __( 'Radio & Checkbox Label Font Size', 'bb-powerpack' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'responsive'  => true,
							'default' => '',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper ul.gfield_radio li label, .gform_wrapper ul.gfield_checkbox li label',
								'property' => 'font-size',
								'unit'     => 'px',
							),
						),
						'input_desc_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Input Description Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield .gfield_description',
							),
						),
					),
				),
				'input_typography'       => array( // Section
					'title'     => __( 'Input', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields'    => array( // Section Fields
						'input_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Input Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gfield input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]), .gform_wrapper .gfield select, .gform_wrapper .gfield textarea',
							),
						),
					),
				),
				'button_typography'      => array( // Section
					'title'     => __( 'Button', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields'    => array( // Section Fields
						'button_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .gform_footer .gform_button, .gform_wrapper .gform_page_footer .button',
							),
						),
					),
				),
				'errors_typography'      => array( // Section
					'title'     => __( 'Error', 'bb-powerpack' ), // Section Title
					'collapsed' => true,
					'fields'    => array( // Section Fields
						'validation_error_font_size' => array(
							'type'    => 'unit',
							'label'   => __( 'Error Description Font Size', 'bb-powerpack' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'default' => '',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.gform_wrapper .validation_error',
								'property' => 'font-size',
								'unit'     => 'px',
							),
						),
					),
				),
			),
		),
	)
);
