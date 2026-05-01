<?php
/**
 * Table of Contents module.
 *
 * @package BB_PowerPack
 */

/**
 * @class PPToCModule
 */
class PPToCModule extends FLBuilderModule {

	/**
	 * Defines the name, group of module.
	 *
	 * Sets the name, group, category, directory of the custom module.
	 *
	 * @since 1.0.0
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Table of Contents', 'bb-powerpack' ),
				'description'     => __( 'Display a Table of Contents', 'bb-powerpack' ),
				'group'           => pp_get_modules_group(),
				'category'        => pp_get_modules_cat( 'content' ),
				'dir'             => BB_POWERPACK_DIR . 'modules/pp-toc/',
				'url'             => BB_POWERPACK_URL . 'modules/pp-toc/',
				'editor_export'   => true,
				'enabled'         => true,
				'partial_refresh' => true,
			)
		);
	}

	public function enqueue_icon_styles() {
		$enqueue = false;
		$settings = $this->settings;

		if ( 'icon' === $settings->list_style && ! empty( $settings->list_icon_field ) ) {
			$enqueue = true;
		}
		if ( 'yes' === $settings->collapsable_toc && ( ! empty( $settings->collapse_icon_field ) || ! empty( $settings->expand_icon_field ) ) ) {
			$enqueue = true;
		}
		if ( ! empty( $settings->scroll_icon ) ) {
			$enqueue = true;
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public static function get_devices() {
		return array(
			'none'    		=> __( 'None', 'bb-powerpack' ),
			'all'			=> __( 'All Devices', 'bb-powerpack' ),
			'xl'            => __( 'Extra Large Devices Only', 'bb-powerpack' ),
			'xl-desktop'    => __( 'Extra Large & Large Devices Only', 'bb-powerpack' ),
			'xl-desktop-medium' => __( 'Extra Large, Large & Medium Devices Only', 'bb-powerpack' ),
			'large' 		=> __( 'Large Devices Only', 'bb-powerpack' ),
			'large-medium' 	=> __( 'Large and Medium Devices', 'bb-powerpack' ),
			'medium'  		=> __( 'Medium Devices Only', 'bb-powerpack' ),
			'medium-responsive'  => __( 'Medium and Small Devices', 'bb-powerpack' ),
			'responsive'  	=> __( 'Small Devices Only', 'bb-powerpack' ),
		);
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module(
	'PPToCModule',
	array(
		'content' => array(
			'title'    => __( 'Content', 'bb-powerpack' ),
			'sections' => array(
				'tc_content'         => array(
					'title'  => __( 'Table of Contents', 'bb-powerpack' ),
					'fields' => array(
						'heading_title'   => array(
							'type'    => 'text',
							'label'   => __( 'Title', 'bb-powerpack' ),
							'default' => '',
							'connections' => array( 'string' ),
						),
						'list_style'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'List Style', 'bb-powerpack' ),
							'default' => 'numbers',
							'options' => array(
								'numbers' => __( 'Numbers', 'bb-powerpack' ),
								'bullets' => __( 'Bullets', 'bb-powerpack' ),
								'icon'    => __( 'Icons', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'icon' => array(
									'fields' => array( 'list_icon_field' ),
								),
							),
						),
						'list_icon_field' => array(
							'type'        => 'icon',
							'label'       => __( 'Icon', 'bb-powerpack' ),
							'show_remove' => true,
						),
						'hierarchical_view'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Hierarchical View', 'bb-powerpack' ),
							'default' => 'yes',
						),
						'anchor_id' => array(
							'type'	=> 'select',
							'label' => __( 'Generate Anchor From', 'bb-powerpack' ),
							'default' => 'auto',
							'options' => array(
								'auto'	=> __( 'Auto Generate', 'bb-powerpack' ),
								'text'	=> __( 'Heading Text', 'bb-powerpack' ),
							),
						),
						'additional_offset' => array(
							'type'	=> 'unit',
							'label' => __( 'Additional Offset', 'bb-powerpack' ),
							'help'	=> __( 'You can add some extra offset if your site has sticky header. This will scroll the content to the header.', 'bb-powerpack' ),
							'default' => '0',
							'responsive' => true,
							'slider' => true
						),
					),
				),
				'include'            => array(
					'title'     => __( 'Include', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'anchor_tag'        => array(
							'type'         => 'select',
							'label'        => __( 'Anchors By Tags', 'bb-powerpack' ),
							'default'      => 'h2',
							'sanitize' => array( 'pp_esc_tags', 'h2' ),
							'options'      => array(
								'h1' => 'H1',
								'h2' => 'H2',
								'h3' => 'H3',
								'h4' => 'H4',
								'h5' => 'H5',
								'h6' => 'H6',
							),
							'multi-select' => true,
							'help'         => __( 'Select multiple headings you want to include with Shift + click', 'bb-powerpack' ),
						),
						'include_container' => array(
							'type'        => 'text',
							'default'     => 'body',
							'label'       => __( 'Container', 'bb-powerpack' ),
							'help'        => __( 'Type in the container you want to include the headings from. Remember to use period(.) before a class and (#) before an ID you want to include from', 'bb-powerpack' ),
							'description' => __( 'Ex: body or .container-class or #container-id', 'bb-powerpack' ),
						),
					),
				),
				'exclude'            => array(
					'title'     => __( 'Exclude', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'exclude_container' => array(
							'type'        => 'text',
							'label'       => __( 'Container', 'bb-powerpack' ),
							'default'     => '',
							'help'        => __( 'Use (.) before classname and (#) before ID and separate multiple elements with a comma(,) and a single space afterward', 'bb-powerpack' ),
							'description' => __( 'Ex: .container-class, #container-id', 'bb-powerpack' ),
						),
					),
				),
				'collapsable_toc' => array(
					'title'     => __( 'Collapsable ToC', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'collapsable_toc'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Enable', 'bb-powerpack' ),
							'default' => 'yes',
							'help'		=> __( 'Title should not be empty for collapsable feature.', 'bb-powerpack' ),
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'collapse_on', 'collapse_icon_field', 'expand_icon_field' ),
								),
							),
						),
						'collapse_icon_field' => array(
							'type'        => 'icon',
							'label'       => __( 'Collapse Icon', 'bb-powerpack' ),
							'default'     => 'fa fa-minus',
							'show_remove' => true,
						),
						'expand_icon_field'   => array(
							'type'        => 'icon',
							'label'       => __( 'Expand Icon', 'bb-powerpack' ),
							'default'     => 'fa fa-plus',
							'show_remove' => true,
						),
						'collapse_on'         => array(
							'type'    => 'select',
							'label'   => __( 'Default Collapsed On', 'bb-powerpack' ),
							'default' => 'none',
							'options' => PPToCModule::get_devices(),
						),
					),
				),
				'sticky_toc' => array(
					'title'     => __( 'Sticky ToC', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'sticky_on'        => array(
							'type'    => 'select',
							'label'   => __( 'Enable Sticky On', 'bb-powerpack' ),
							'default' => 'none',
							'options' => PPToCModule::get_devices(),
						),
						'sticky_builder_off'	=> array(
							'type'	=> 'pp-switch',
							'label' => __( 'Disable in Builder', 'bb-powerpack' ),
							'default' => 'no',
						),
						'sticky_type'           => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Sticky ToC on Scroll', 'bb-powerpack' ),
							'default' => 'fixed',
							'options' => array(
								'fixed'  => __( 'Sticky in Place', 'bb-powerpack' ),
								'custom' => __( 'Custom Position', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'custom' => array(
									'fields' => array( 'horizontal_position', 'vertical_position' ),
								),
								'fixed'  => array(
									'fields' => array( 'fixed_offset_position' ),
								),
							),
						),
						'horizontal_position'   => array(
							'type'         => 'unit',
							'label'        => __( 'Horizontal Position', 'bb-powerpack' ),
							'units'        => array( 'px', 'vw', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-toc-sticky-custom',
								'property' => 'left',
							),
						),
						'vertical_position'     => array(
							'type'         => 'unit',
							'label'        => __( 'Vertical Position', 'bb-powerpack' ),
							'units'        => array( 'px', 'vw', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-toc-sticky-custom',
								'property' => 'bottom',
							),
						),
						'fixed_offset_position' => array(
							'type'         => 'unit',
							'label'        => __( 'Offset Position', 'bb-powerpack' ),
							'units'        => array( 'px', 'vw', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => 'px',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-toc-sticky-fixed',
								'property' => 'top',
							),
						),
						'hide_toc_to' => array(
							'type' => 'text',
							'label' => __( 'Hide ToC when reaches to', 'bb-powerpack' ),
							'help' => __( 'Enter the CSS class of the row/section so it won\'t get overlapped by ToC.', 'bb-powerpack' ),
						),
					),
				),
				'scroll_top' => array(
					'title'     => __( 'Scroll to Top', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'scroll_top'         => array(
							'type'    => 'select',
							'label'   => __( 'Enable on', 'bb-powerpack' ),
							'default' => 'none',
							'options' => PPToCModule::get_devices(),
						),
						'scroll_to'                  => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Scroll to', 'bb-powerpack' ),
							'default' => 'window',
							'options' => array(
								'window' => __( 'Window Top', 'bb-powerpack' ),
								'toc'    => __( 'Table of Contents', 'bb-powerpack' ),
							),
						),
						'scroll_icon'                => array(
							'type'        => 'icon',
							'label'       => __( 'Icon', 'bb-powerpack' ),
							'default'     => 'fas fa-arrow-up',
							'show_remove' => true,
						),
						'scroll_alignment'           => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Alignment', 'bb-powerpack' ),
							'default' => 'left',
							'options' => array(
								'left'  => __( 'Left', 'bb-powerpack' ),
								'right' => __( 'Right', 'bb-powerpack' ),
							),
						),
						'scroll_horizontal_position' => array(
							'type'         => 'unit',
							'label'        => __( 'Horizontal Position', 'bb-powerpack' ),
							'units'        => array( 'px', 'vw', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-toc-scroll-align-left',
								'property' => 'left',
							),
						),
						'scroll_vertical_position'   => array(
							'type'         => 'unit',
							'label'        => __( 'Vertical Position', 'bb-powerpack' ),
							'units'        => array( 'px', 'vw', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-toc-scroll-top-container',
								'property' => 'bottom',
							),
						),
						'scroll_z_index'             => array(
							'type'    => 'unit',
							'label'   => __( 'Z-Index', 'bb-powerpack' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-toc-scroll-top-container',
								'property' => 'z-index',
							),
						),
					),
				),
			),
		),
		'style'   => array(
			'title'    => __( 'Style', 'bb-powerpack' ),
			'sections' => array(
				'box_section'          => array(
					'title'  => __( 'Box', 'bb-powerpack' ),
					'fields' => array(
						'box_bg_color'   => array(
							'type'       => 'color',
							'label'      => __( 'Background Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-body',
								'property' => 'background-color',
							),
						),
						'box_border'     => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container',
								'property' => 'border',
							),
						),
						'box_min_height' => array(
							'type'         => 'unit',
							'label'        => __( 'Min Height', 'bb-powerpack' ),
							'units'        => array( 'px', 'vw' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => 'px',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container',
								'property' => 'height',
							),
						),
					),
				),
				'header_section'       => array(
					'title'     => __( 'Header', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'header_alignment'       => array(
							'type'    => 'align',
							'label'   => __( 'Alignment', 'bb-powerpack' ),
							'default' => 'left',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-header-title',
								'property' => 'text-align',
							),
						),
						'header_padding'         => array(
							'type'    => 'dimension',
							'label'   => __( 'Padding', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-header',
								'property' => 'padding',
							),
						),
						'header_bg_color'        => array(
							'type'       => 'color',
							'label'      => __( 'Background Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-header',
								'property' => 'background-color',
							),
						),
						'header_text_color'      => array(
							'type'       => 'color',
							'label'      => __( 'Text Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => false,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-header-title',
								'property' => 'color',
							),
						),
						'header_text_typo'       => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-header-title',
							),
						),
						'header_icon_color'      => array(
							'type'       => 'color',
							'label'      => __( 'Icon Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => false,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .header-icon-collapse, .pp-toc-container .header-icon-expand',
								'property' => 'color',
							),
						),
						'header_separator_width' => array(
							'type'       => 'unit',
							'label'      => __( 'Separator Width', 'bb-powerpack' ),
							'units'      => array( 'px', 'vw', '%' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-separator',
								'property' => 'height',
							),
						),
						'header_separator_color' => array(
							'type'       => 'color',
							'label'      => __( 'Separator Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => false,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-separator',
								'property' => 'background-color',
							),
						),
					),
				),
				'list_section'         => array(
					'title'     => __( 'List', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'list_padding'          => array(
							'type'    => 'dimension',
							'label'   => __( 'Padding', 'bb-powerpack' ),
							'slider'  => true,
							'units'   => array( 'px' ),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-body',
								'property' => 'padding',
							),
						),
						'list_spacing'       => array(
							'type'    => 'unit',
							'label'   => __( 'Spacing', 'bb-powerpack' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-list-wrapper li',
								'property' => 'margin-top',
							),
						),
						'list_typo'             => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-body .pp-toc-list-wrapper a',
							),
						),
						'list_hover_underline'  => array(
							'type'    => 'pp-switch',
							'label'   => __( ' Hover Underline', 'bb-powerpack' ),
							'default' => 'no',
						),
						'list_normal_color'     => array(
							'type'       => 'color',
							'label'      => __( 'Text Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => false,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-list-wrapper li a',
								'property' => 'color',
							),
						),
						'list_hover_color'      => array(
							'type'       => 'color',
							'label'      => __( 'Hover Text Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => false,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-list-wrapper li a:hover',
								'property' => 'color',
							),
						),
						'list_marker_color'     => array(
							'type'       => 'color',
							'label'      => __( 'Marker Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => false,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-list-wrapper.pp-toc-list-bullet li::before, .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-number li::before, .pp-toc-container .pp-toc-list-icon span',
								'property' => 'color',
							),
						),
						'list_marker_size'      => array(
							'type'       => 'unit',
							'label'      => __( 'Marker Size', 'bb-powerpack' ),
							'units'      => array( 'px', 'vw', '%' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-container .pp-toc-list-wrapper.pp-toc-list-bullet li::before, .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-number li::before, .pp-toc-container .pp-toc-list-icon span',
								'property' => 'font-size',
							),
						),
						'list_marker_space'     => array(
							'type'    => 'unit',
							'label'   => __( 'Marker Spacing', 'bb-powerpack' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'refresh',
								'selector' => '.pp-toc-container .pp-toc-list-wrapper.pp-toc-list-bullet li::before, .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-number li::before, .pp-toc-container .pp-toc-list-icon span',
								'property' => 'margin-right',
							),
						),
					),
				),
				'sticky_style_section' => array(
					'title'     => __( 'Sticky ToC', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'sticky_toc_width'   => array(
							'type'         => 'unit',
							'label'        => __( 'Width', 'bb-powerpack' ),
							'units'        => array( 'px', 'vw', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
						),
						'sticky_toc_opacity' => array(
							'type'    => 'unit',
							'label'   => __( 'Background Opacity', 'bb-powerpack' ),
							'units'   => array( 'px' ),
							'slider'  => array(
								'min'  => 0,
								'max'  => 1,
								'step' => .10,
							),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-toc-sticky-custom, .pp-toc-sticky-fixed',
								'property' => 'opacity',
							),
						),
						'sticky_toc_shadow'  => array(
							'type'        => 'shadow',
							'label'       => __( 'Box Shadow', 'bb-powerpack' ),
							'show_spread' => true,
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-toc-sticky-custom',
								'property' => 'box-shadow',
							),
						),
					),
				),
				'scroll_style_section' => array(
					'title'     => __( 'Scroll to Top', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'scroll_icon_size'   => array(
							'type'       => 'unit',
							'label'      => __( 'Icon Size', 'bb-powerpack' ),
							'units'      => array( 'px', 'em' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-scroll-top-icon',
								'property' => 'font-size',
							),
						),
						'scroll_top_padding' => array(
							'type'    => 'unit',
							'label'   => __( 'Padding', 'bb-powerpack' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-toc-scroll-top-container',
								'property' => 'padding',
							),
						),
						'scroll_icon_color'  => array(
							'type'       => 'color',
							'label'      => __( 'Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => false,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-scroll-top-icon',
								'property' => 'color',
							),
						),
						'scroll_bg_color'    => array(
							'type'       => 'color',
							'label'      => __( 'Background Color', 'bb-powerpack' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-scroll-top-container',
								'property' => 'background-color',
							),
						),
						'scroll_border'      => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-toc-scroll-top-container',
								'property' => 'border',
							),
						),
					),
				),
			),
		),
	)
);
