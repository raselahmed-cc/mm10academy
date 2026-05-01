<?php
/**
 * Handles logic for registering global style fields.
 *
 * @package BB_PowerPack
 * @since 2.40.1
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PPGlobalStyles.
 */
final class PPGlobalStyles {
	/**
	 * Initialize hooks and filters.
	 *
	 * @return void
	 */
	static public function init() {
		if ( is_admin() ) {
			return;
		}
		// Filters.
		add_filter( 'fl_builder_register_settings_form',   	 __CLASS__ . '::register_global_styles', 10, 2 );
		add_filter( 'fl_builder_before_generate_global_css', __CLASS__ . '::generate_global_css', 10 );
	}

	/**
	 * Register new sections and fields under the Global Styles form.
	 *
	 * @param array $form
	 * @param string $slug
	 * @return array
	 */
	static public function register_global_styles( $form, $slug ) {
		if ( 'styles' !== $slug ) {
			return $form;
		}

		if ( ! isset( $form['tabs']['elements'] ) ) {
			return $form;
		}

		$prefix   = pp_get_admin_label();
		$sections = self::get_sections( $prefix );

		foreach ( $sections as $section_key => $section ) {
			$form['tabs']['elements']['sections'][ $section_key ] = $section;
		}

		$global_styles_fields = array(); //BB_PowerPack_Modules::get_global_styles_fields();

		if ( ! empty( $global_styles_fields ) ) {
			foreach ( $global_styles_fields as $slug => $data ) {
				if ( ! isset( $data['fields'] ) || empty( $data['fields'] ) ) {
					continue;
				}
				$field_prefix = str_replace( '-', '_', $slug );
				if ( ! isset( $form['tabs']['elements']['sections'][ $field_prefix ] ) ) {
					$form['tabs']['elements']['sections'][ $field_prefix ] = array(
						'collapsed' => true,
						'title'     => sprintf( '%s - %s', $prefix, $data['name'] ),
						'fields'    => array()
					);
				}
				foreach ( $data['fields'] as $field_key => $field ) {
					$key = $field_prefix . '_' . $field_key;
					$form['tabs']['elements']['sections'][ $field_prefix ]['fields'][ $key ] = $field;
				}
			}
		}

		return $form;
	}

	/**
	 * Generate global CSS.
	 *
	 * @return void
	 */
	static public function generate_global_css() {
		if ( ! isset( $settings ) && is_callable( 'FLBuilderGlobalStyles::get_settings' ) ) {
			$settings = FLBuilderGlobalStyles::get_settings();
		}

		if ( ! isset( $settings ) || empty( $settings ) ) {
			return;
		}

		$sections = self::get_sections();

		foreach ( $sections as $section ) {
			$fields = $section['fields'];
			foreach ( $fields as $field_key => $field ) {
				if ( ! isset( $field['global_css'] ) ) {
					continue;
				}

				$global_css = $field['global_css'];

				if ( isset( $global_css['rules'] ) ) {
					foreach ( $global_css['rules'] as $rule ) {
						self::generate_css_rules( $field['type'], $rule, $settings, $field_key );
					}
				} else {
					self::generate_css_rules( $field['type'], $global_css, $settings, $field_key );
				}
			}
		}
	}

	static private function generate_css_rules( $field_type, $rule, $settings, $setting_name ) {
		if ( 'border' === $field_type ) {
			FLBuilderCSS::border_field_rule( array(
				'selector'     => $rule['selector'],
				'settings'     => $settings,
				'setting_name' => $setting_name,
			) );
		} elseif ( 'dimension' === $field_type ) {
			$prop = $rule['prop'];
			FLBuilderCSS::dimension_field_rule( array(
				'settings'		=> $settings,
				'setting_name'	=> $setting_name,
				'selector'		=> $rule['selector'],
				'unit'			=> isset( $rule['unit'] ) ? $rule['unit'] : 'px',
				'props'			=> array(
					"$prop-top"		=> "{$setting_name}_top",
					"$prop-right"	=> "{$setting_name}_right",
					"$prop-bottom"	=> "{$setting_name}_bottom",
					"$prop-left"	=> "{$setting_name}_left",
				)
			) );
		} elseif ( 'typography' === $field_type ) {
			FLBuilderCSS::typography_field_rule( array(
				'selector'     => $rule['selector'],
				'settings'     => $settings,
				'setting_name' => $setting_name,
			) );
		} else {
			$prop = $rule['prop'];
			if ( isset( $settings->{$setting_name} ) ) {
				FLBuilderCSS::rule( array(
					'selector' => $rule['selector'],
					'props'    => array(
						"$prop" => $settings->{$setting_name},
					),
				) );
			}
		}
	}

	static private function get_sections( $prefix = 'PowerPack' ) {
		$sections = array();

		$sections['pp_accordion'] = array(
			'collapsed' => true,
			'title'  => sprintf( __( '%s - Advanced Accordion', 'bb-powerpack' ), $prefix ),
			'fields' => array(
				'pp_accord_label_bg_color' => array(
					'type'        => 'color',
					'label'       => __( 'Title Background Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => true,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-button',
						'prop'     => 'background-color'
					)
				),
				'pp_accord_label_bg_color_active' => array(
					'type'        => 'color',
					'label'       => __( 'Title Background Color - Active', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => true,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-accordion-item-active .pp-accordion-button',
						'prop'     => 'background-color'
					)
				),
				'pp_accord_label_color' => array(
					'type'        => 'color',
					'label'       => __( 'Title Text Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => false,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-button',
						'prop'     => 'color'
					)
				),
				'pp_accord_label_color_active' => array(
					'type'        => 'color',
					'label'       => __( 'Title Text Color - Active', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => false,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-accordion-item-active .pp-accordion-button',
						'prop'     => 'color'
					)
				),
				'pp_accord_label_border' => array(
					'type'        => 'border',
					'label'       => __( 'Title Border', 'bb-powerpack' ),
					'responsive'  => true,
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-button',
					)
				),
				'pp_accord_label_padding' => array(
					'type'        => 'dimension',
					'label'       => __( 'Title Padding', 'bb-powerpack' ),
					'responsive'  => true,
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-button',
						'prop'     => 'padding'
					)
				),
				'pp_accord_label_typography' => array(
					'type'        => 'typography',
					'label'       => __( 'Title Typography', 'bb-powerpack' ),
					'responsive'  => true,
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-button .pp-accordion-button-label',
					)
				),
				'pp_accord_content_bg_color' => array(
					'type'        => 'color',
					'label'       => __( 'Content Background Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => true,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-content',
						'prop'     => 'background-color'
					)
				),
				'pp_accord_content_color' => array(
					'type'        => 'color',
					'label'       => __( 'Content Text Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => false,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-content',
						'prop'     => 'color'
					)
				),
				'pp_accord_content_border' => array(
					'type'        => 'border',
					'label'       => __( 'Content Border', 'bb-powerpack' ),
					'responsive'  => true,
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-content',
					)
				),
				'pp_accord_content_padding' => array(
					'type'        => 'dimension',
					'label'       => __( 'Content Padding', 'bb-powerpack' ),
					'responsive'  => true,
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-content',
						'prop'     => 'padding'
					)
				),
				'pp_accord_content_typography' => array(
					'type'        => 'typography',
					'label'       => __( 'Content Typography', 'bb-powerpack' ),
					'responsive'  => true,
					'global_css'  => array(
						'selector' => '.pp-accordion-item .pp-accordion-content',
					)
				),
			)
		);

		$sections['pp_tabs'] = array(
			'collapsed' => true,
			'title'  => sprintf( __( '%s - Advanced Tabs', 'bb-powerpack' ), $prefix ),
			'fields' => array(
				'pp_tabs_label_bg_color' => array(
					'type'        => 'color',
					'label'       => __( 'Title Background Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => true,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-label',
						'prop'     => 'background-color'
					)
				),
				'pp_tabs_label_bg_color_active' => array(
					'type'        => 'color',
					'label'       => __( 'Title Background Color - Active', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => true,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-label.pp-tab-active, .pp-tabs-style-5 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:after',
						'prop'     => 'background-color'
					)
				),
				'pp_tabs_label_color' => array(
					'type'        => 'color',
					'label'       => __( 'Title Text Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => false,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-label',
						'prop'     => 'color'
					)
				),
				'pp_tabs_label_color_active' => array(
					'type'        => 'color',
					'label'       => __( 'Title Text Color - Active', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => false,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'rules' => array(
							array(
								'selector' => '.pp-tabs .pp-tabs-label.pp-tab-active',
								'prop'     => 'color'
							),
							array(
								'selector' => '.pp-tabs-style-6 .pp-tabs-label:last-child::before',
								'prop'     => 'background-color'
							)
						)
					)
				),
				'pp_tabs_label_desc_color' => array(
					'type'        => 'color',
					'label'       => __( 'Title Description Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => false,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-label .pp-tab-description',
						'prop'     => 'color'
					)
				),
				'pp_tabs_label_desc_color_active' => array(
					'type'        => 'color',
					'label'       => __( 'Title Description Color - Active', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => false,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-label.pp-tab-active .pp-tab-description',
						'prop'     => 'color'
					)
				),
				'pp_tabs_border_color' => array(
					'type'        => 'color',
					'label'       => __( 'Border Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => true,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-label',
						'prop'     => 'border-color'
					)
				),
				'pp_tabs_content_bg_color' => array(
					'type'        => 'color',
					'label'       => __( 'Content Background Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => true,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-panel-content',
						'prop'     => 'background-color'
					)
				),
				'pp_tabs_content_color' => array(
					'type'        => 'color',
					'label'       => __( 'Content Text Color', 'bb-powerpack' ),
					'show_reset'  => true,
					'show_alpha'  => false,
					'default'     => '',
					'connections' => array( 'color' ),
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-panel-content',
						'prop'     => 'color'
					)
				),
				'pp_tabs_content_padding' => array(
					'type'        => 'dimension',
					'label'       => __( 'Content Padding', 'bb-powerpack' ),
					'responsive'  => true,
					'global_css'  => array(
						'selector' => '.pp-tabs .pp-tabs-panel-content',
						'prop'     => 'padding'
					)
				),
			)
		);

		return $sections;
	}
}

if ( defined( 'FL_BUILDER_VERSION' ) && ( version_compare( FL_BUILDER_VERSION, '2.9', '>=' ) || false !== strpos( FL_BUILDER_VERSION, '2.9-' ) ) ) {
	PPGlobalStyles::init();
}