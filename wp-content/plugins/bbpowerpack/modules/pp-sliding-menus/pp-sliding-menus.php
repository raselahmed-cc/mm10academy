<?php

/**
 * @class PPSlidingMenusModule
 */
class PPSlidingMenusModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Sliding Menu', 'bb-powerpack' ),
				'description'     => __( 'A module to create a sliding menu.', 'bb-powerpack' ),
				'group'           => pp_get_modules_group(),
				'category'        => pp_get_modules_cat( 'creative' ),
				'dir'             => BB_POWERPACK_DIR . 'modules/pp-sliding-menus/',
				'url'             => BB_POWERPACK_URL . 'modules/pp-sliding-menus/',
				'editor_export'   => true,
				'partial_refresh' => true,
			)
		);
	}

	/**
	 * Nav Menu Index
	 *
	 * @since  2.8.0
	 * @var    int
	 */
	public $nav_menu_index = 1;

	/**
	 * Get Available Menu
	 *
	 * Return the list of available WP menus
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public static function get_available_menus() {
		if ( ! isset( $_GET['fl_builder'] ) ) {
			return array();
		}

		$get_menus = wp_get_nav_menus( array( 'hide_empty' => true ) );
		$options = array();

		if ( $get_menus ) {

			foreach( $get_menus as $key => $menu ) {

				if ( $key == 0 ) {
					$fields['default'] = $menu->name;
				}

				$options[ $menu->slug ] = $menu->name;
			}

		} else {
			$options = array( '' => __( 'No Menus Found', 'bb-powerpack' ) );
		}

		return $options;
	}

	/**
	 * Get Nav Menu Index
	 *
	 * @since  2.8.0
	 * @return int
	 */
	public function get_nav_menu_index() {
		return $this->nav_menu_index++;
	}

	public function get_menu_label() {
		$label = __( 'Menu', 'bb-powerpack' );

		if ( isset( $this->settings ) && isset( $this->settings->menu_name ) && ! empty( $this->settings->menu_name ) ) {
			$label = apply_filters( 'pp_nav_menu_label', $this->settings->menu_name, $this->settings );
		}

		return esc_attr( $label );
	}

	/**
	 * Handle Link Classes
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public function handle_link_classes( $atts, $item, $args, $depth ) {
		$classes = $depth ? 'pp-slide-menu-item-link pp-slide-menu-sub-item-link' : 'pp-slide-menu-item-link';

		if ( in_array( 'current-menu-item', $item->classes ) ) {
			$classes .= '  pp-slide-menu-item-link-current';
		}

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		return $atts;
	}

	/**
	 * Handle Submenu Classes
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public function handle_sub_menu_classes( $classes ) {
		$classes[] = 'pp-slide-menu-sub-menu';

		return $classes;
	}

	/**
	 * Handle Menu Item Classes
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public function handle_menu_item_classes( $classes ) {
		$classes[] = 'pp-slide-menu-item';

		if ( in_array( 'menu-item-has-children', $classes ) ) {
			$classes[] = 'pp-slide-menu-item-has-children';
		}

		if ( in_array( 'current-menu-item', $classes ) ) {
			$classes[] = 'pp-slide-menu-item-current';
		}

		return $classes;
	}
}

/**
 * Register the module settings.
 */
BB_PowerPack::register_module(
	'PPSlidingMenusModule',
	array(
		'General'    => array(
			'title'    => __( 'General', 'bb-powerpack' ),
			'sections' => array(
				'settings' => array(
					'title'  => __( 'Settings', 'bb-powerpack' ),
					'fields' => array(
						'menu'            => array(
							'type'    => 'select',
							'label'   => __( 'Menu', 'bb-powerpack' ),
							'default' => '',
							'options' => PPSlidingMenusModule::get_available_menus(),
						),
						'menu_name' => array(
							'label'   => __( 'Name', 'bb-powerpack' ),
							'type'    => 'text',
							'help'    => __( 'This is used as the aria attribute for accessibility and is not visible on frontend.', 'bb-powerpack' ),
							'default' => __( 'Menu', 'bb-powerpack' ),
						),
						'effect'          => array(
							'type'    => 'select',
							'label'   => __( 'Effect', 'bb-powerpack' ),
							'default' => 'overlay',
							'options' => array(
								'overlay' => __( 'Overlay', 'bb-powerpack' ),
								'push'    => __( 'Push', 'bb-powerpack' ),
							),
						),
						'direction'       => array(
							'type'    => 'select',
							'label'   => __( 'Direction', 'bb-powerpack' ),
							'default' => 'left',
							'options' => array(
								'left'   => __( 'Left', 'bb-powerpack' ),
								'right'  => __( 'Right', 'bb-powerpack' ),
								'bottom' => __( 'Bottom', 'bb-powerpack' ),
								'top'    => __( 'Top', 'bb-powerpack' ),
							),
						),
						'duration'        => array(
							'type'       => 'unit',
							'label'      => __( 'Transition Duration', 'bb-powerpack' ),
							'default'    => '',
							'units'      => array( 'seconds' ),
							'slider'     => array(
								'min'  => '0',
								'max'  => '3',
								'step' => '0.1',
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu__menu, .pp-sliding-menus .pp-sliding-menus .pp-slide-menu-sub-menu',
								'property' => 'transition-duration',
								'unit'     => 's',
							),
						),
						'back_text'       => array(
							'type'        => 'text',
							'label'       => __( 'Back Label', 'bb-powerpack' ),
							'default'     => __( 'Back', 'bb-powerpack' ),
							'connections' => array( 'string' ),
						),
						'link_navigation' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Link Navigation', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
							'help'    => __( 'Allow navigating to sub-menus by clicking the links instead of just the arrows.', 'bb-powerpack' ),
						),
					),
				),
			),
		),
		'style'      => array( // Tab
			'title'    => __( 'Style', 'bb-powerpack' ), // Tab title
			'sections' => array( // Tab Sections
				'menu_style'   => array( // Section
					'title'  => __( 'Menu', 'bb-powerpack' ), // Section Title
					'fields' => array( // Section Fields
						'width'      => array(
							'type'       => 'unit',
							'label'      => __( 'Width', 'bb-powerpack' ),
							'default'    => '',
							'units'      => array( 'px', '%' ),
							'slider'     => array(
								'px' => array(
									'min' => 100,
									'max' => 1000,
								),
								'%'  => array(
									'min' => 0,
									'max' => 100,
								),
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus',
								'property' => 'width',
								'unit'     => 'px',
							),
						),
						'min_height' => array(
							'type'       => 'unit',
							'label'      => __( 'Minimum Height', 'bb-powerpack' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 100,
								'max' => 1000,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus',
								'property' => 'min-height',
								'unit'     => 'px',
							),
						),
						'alignment'  => array(
							'type'    => 'align',
							'label'   => __( ' Align', 'bb-powerpack' ),
							'default' => 'left',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.fl-module-pp-sliding-menus',
								'property' => 'text-align',
							),
						),
						'bg_color'   => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus, .pp-sliding-menus .pp-slide-menu-sub-menu',
								'property' => 'background-color',
							),
						),
						'border'     => array(
							'type'  => 'border',
							'label' => __( 'Border', 'bb-powerpack' ),
						),
					),
				),
				'links_style'  => array(
					'title'     => __( 'Links', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'links_spacing'               => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'bb-powerpack' ),
							'default'    => '0',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item:not(:last-child)',
								'property' => 'margin-bottom',
							),
						),
						'links_separator_thickness'   => array(
							'type'       => 'unit',
							'label'      => __( 'Separator Thickness', 'bb-powerpack' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item',
								'property' => 'border-bottom-width',
								'unit'     => 'px',
							),
						),
						'links_transition_easing'     => array(
							'type'    => 'select',
							'label'   => __( 'Transition', 'bb-powerpack' ),
							'default' => 'ease-in',
							'options' => array(
								'linear'      => __( 'Linear', 'bb-powerpack' ),
								'ease-in'     => __( 'Ease In', 'bb-powerpack' ),
								'ease-out'    => __( 'Ease Out', 'bb-powerpack' ),
								'ease-in-out' => __( 'Ease In Out', 'bb-powerpack' ),
							),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item-link, .pp-sliding-menus .pp-slide-menu-arrow',
								'property' => 'transition-timing-function',
							),
						),
						'links_transition_duration'   => array(
							'type'    => 'unit',
							'label'   => __( 'Transition Duration', 'bb-powerpack' ),
							'default' => '0.3',
							'units'   => array( 's' ),
							'slider'  => array(
								'min'  => '0',
								'max'  => '3',
								'step' => '0.1',
							),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item-link, .pp-sliding-menus .pp-slide-menu-arrow',
								'property' => 'transition-duration',
								'unit'     => 's',
							),
						),
						'links_padding'               => array(
							'type'    => 'dimension',
							'label'   => __( 'Links Padding', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'default' => '',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item-link, .pp-sliding-menus .pp-slide-menu-arrow',
								'property' => 'padding',
								'unit'     => 'px',
							),
						),
						'links_alignment'             => array(
							'type'    => 'align',
							'label'   => __( 'Links Align', 'bb-powerpack' ),
							'default' => 'left',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item-link',
								'property' => 'text-align',
							),
						),
						'links_colors_separator'      => array(
							'type'  => 'pp-separator',
							'color' => 'e6eaed',
						),
						'links_bg_color'              => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item-link',
								'property' => 'bakcground-color',
							),
						),
						'links_color'                 => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item-link',
								'property' => 'color',
							),
						),
						'links_separator_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Separator Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item',
								'property' => 'border-color',
							),
						),
						'links_bg_color_hover'        => array(
							'type'        => 'color',
							'label'       => __( 'Background Hover Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item-link:hover',
								'property' => 'background-color',
							),
						),
						'links_color_hover'           => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item-link:hover',
								'property' => 'color',
							),
						),
						'links_separator_color_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Separator Hover Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-item:hover',
								'property' => 'border-color',
							),
						),
					),
				),
				'arrows_style' => array(
					'title'     => __( 'Arrows', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'arrow_separator_thickness'    => array(
							'type'       => 'unit',
							'label'      => __( 'Separator Thickness', 'bb-powerpack' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.pp-sliding-menus .pp-slide-menu-item.pp-slide-menu-item-has-children > .pp-slide-menu-arrow',
										'property' => 'border-left-width',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-sliding-menus .pp-slide-menu-back > .pp-slide-menu-arrow',
										'property' => 'border-right-width',
										'unit'     => 'px',
									),
								),
							),
						),
						'arrow_size'                   => array(
							'type'       => 'unit',
							'label'      => __( 'Size', 'bb-powerpack' ),
							'default'    => '24',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'rules'    => array(
									array(
										'selector' => '.pp-sliding-menus .pp-slide-menu-arrow svg',
										'property' => 'width',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-sliding-menus .pp-slide-menu-arrow svg',
										'property' => 'height',
										'unit'     => 'px',
									)
								)
							),
						),
						'arrow_left_padding'           => array(
							'type'       => 'unit',
							'label'      => __( 'Left Padding', 'bb-powerpack' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-arrow',
								'property' => 'padding-left',
								'unit'     => 'px',
							),
						),
						'arrow_right_padding'          => array(
							'type'       => 'unit',
							'label'      => __( 'Right Padding', 'bb-powerpack' ),
							'default'    => '',
							'units'      => array( 'px' ),
							'slider'     => array(
								'min' => 0,
								'max' => 50,
							),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-arrow',
								'property' => 'padding-right',
								'unit'     => 'px',
							),
						),
						'arrows_colors_separator'      => array(
							'type'  => 'pp-separator',
							'color' => 'e6eaed',
						),
						'arrows_bg_color'              => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-arrow',
								'property' => 'background-color',
							),
						),
						'arrows_color'                 => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-arrow',
								'property' => 'color',
							),
						),
						'arrows_separator_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Separator Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-arrow',
								'property' => 'border-color',
							),
						),
						'arrows_bg_color_hover'        => array(
							'type'        => 'color',
							'label'       => __( 'Background Hover Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-arrow:hover',
								'property' => 'background-color',
							),
						),
						'arrows_color_hover'           => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-arrow:hover',
								'property' => 'color',
							),
						),
						'arrows_separator_color_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Separator Hover Color', 'bb-powerpack' ),
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus .pp-slide-menu-arrow:hover',
								'property' => 'border-color',
							),
						),
					),
				),
			),
		),
		'typography' => array(
			'title'    => __( 'Typography', 'bb-powerpack' ),
			'sections' => array(
				'menu_typography' => array(
					'title'  => __( 'Menu', 'bb-powerpack' ),
					'fields' => array(
						'menu_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-sliding-menus',
							),
						),
					),
				),
			),
		),
	)
);
