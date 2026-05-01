<?php

/**
 * @class PPAccordionModule
 */
class PPAccordionModule extends FLBuilderModule {

	private $cached_content = array();

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Advanced Accordion', 'bb-powerpack' ),
				'description'     => __( 'Display a collapsible accordion of items.', 'bb-powerpack' ),
				'group'           => pp_get_modules_group(),
				'category'        => pp_get_modules_cat( 'content' ),
				'dir'             => BB_POWERPACK_DIR . 'modules/pp-advanced-accordion/',
				'url'             => BB_POWERPACK_URL . 'modules/pp-advanced-accordion/',
				'editor_export'   => true, // Defaults to true and can be omitted.
				'enabled'         => true, // Defaults to true and can be omitted.
				'partial_refresh' => true,
			)
		);
	}

	public function enqueue_scripts() {
		$this->add_css( BB_POWERPACK()->fa_css );
	}

	public function filter_settings( $settings, $helper ) {
		if ( isset( $settings->accordion_source ) ) {
			$settings->data_source = $settings->accordion_source;
			unset( $settings->accordion_source );
		}
		if ( isset( $settings->post_slug ) ) {
			$settings->post_type = $settings->post_slug;
			unset( $settings->post_slug );
		}
		if ( isset( $settings->post_count ) ) {
			$settings->posts_per_page = $settings->post_count;
			unset( $settings->post_count );
		}
		if ( isset( $settings->post_order_by ) ) {
			$settings->order_by = $settings->post_order_by;
			unset( $settings->post_order_by );
		}
		if ( isset( $settings->post_order_by_meta_key ) ) {
			$settings->order_by_meta_key = $settings->post_order_by_meta_key;
			unset( $settings->post_order_by_meta_key );
		}
		if ( isset( $settings->post_order ) ) {
			$settings->order = $settings->post_order;
			unset( $settings->post_order );
		}
		if ( isset( $settings->post_offset ) ) {
			$settings->offset = $settings->post_offset;
			unset( $settings->post_offset );
		}

		// Handle old label background dual color field.
		$settings = PP_Module_Fields::handle_dual_color_field(
			$settings,
			'label_background_color',
			array(
				'primary'   => 'label_bg_color_default',
				'secondary' => 'label_bg_color_active',
				'opacity'   => 'label_background_opacity',
			)
		);

		// Handle old label text dual color field.
		$settings = PP_Module_Fields::handle_dual_color_field(
			$settings,
			'label_text_color',
			array(
				'primary'   => 'label_text_color_default',
				'secondary' => 'label_text_color_active',
			)
		);

		// Handle old label padding field.
		if ( isset( $settings->label_padding ) && is_array( $settings->label_padding ) ) {
			$settings = PP_Module_Fields::handle_multitext_field( $settings, 'label_padding', 'padding', 'label_padding' );
		}

		// Handle old label border field.
		$settings = PP_Module_Fields::handle_border_field(
			$settings,
			array(
				'label_border_style'  => array(
					'type' => 'style',
				),
				'label_border_width'  => array(
					'type' => 'width',
				),
				'label_border_color'  => array(
					'type' => 'color',
				),
				'label_border_radius' => array(
					'type' => 'radius',
				),
			),
			'label_border'
		);

		// Merge content bg opacity to content bg color.
		if ( isset( $settings->content_bg_opacity ) ) {
			$opacity = 1;
			if ( '0' === $settings->content_bg_opacity ) {
				$opacity = 0;
			} else {
				$opacity = ( $settings->content_bg_opacity / 100 );
			}
			$content_bg_color = $settings->content_bg_color;
			if ( ! empty( $content_bg_color ) ) {
				$settings->content_bg_color = pp_hex2rgba( $content_bg_color, $opacity );
			}

			unset( $settings->content_bg_opacity );
		}

		// Handle old content padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'content_padding', 'padding', 'content_padding' );

		// Handle old content border field.
		$settings = PP_Module_Fields::handle_border_field(
			$settings,
			array(
				'content_border_style'  => array(
					'type' => 'style',
				),
				'content_border_width'  => array(
					'type' => 'width',
				),
				'content_border_color'  => array(
					'type' => 'color',
				),
				'content_border_radius' => array(
					'type' => 'radius',
				),
			),
			'content_border'
		);

		// Handle old label typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'label_font'             => array(
					'type' => 'font',
				),
				'label_custom_font_size' => array(
					'type'      => 'font_size',
					'condition' => ( isset( $settings->label_font_size ) && 'custom' === $settings->label_font_size ),
				),
				'label_line_height'      => array(
					'type' => 'line_height',
				),
			),
			'label_typography'
		);

		// Handle old content typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'content_font'             => array(
					'type' => 'font',
				),
				'content_custom_font_size' => array(
					'type'      => 'font_size',
					'condition' => ( isset( $settings->content_font_size ) && 'custom' === $settings->content_font_size ),
				),
				'content_line_height'      => array(
					'type' => 'line_height',
				),
				'content_alignment'        => array(
					'type' => 'text_align',
				),
			),
			'content_typography'
		);

		return $settings;
	}

	public function get_data_source() {
		if ( ! isset( $this->settings->data_source ) || empty( $this->settings->data_source ) ) {
			return 'manual';
		}

		return $this->settings->data_source;
	}

	public function get_cpt_data() {
		if ( ! isset( $this->settings->post_type ) || empty( $this->settings->post_type ) ) {
			return;
		}

		$settings = $this->settings;

		$settings->post_type      = ! empty( $this->settings->post_type ) ? $this->settings->post_type : 'post';
		$settings->posts_per_page = ! empty( $this->settings->posts_per_page ) || '-1' !== $this->settings->posts_per_page ? $this->settings->posts_per_page : '-1';
		$settings->order          = ! empty( $this->settings->order ) ? $this->settings->order : 'DESC';

		$data = BB_PowerPack_Post_Helper::get_posts_properties_as_data( $settings, array( 'title' => 'label' ) );

		return $data;
	}

	public function get_acf_data( $post_id = false ) {
		if ( ! isset( $this->settings->acf_repeater_name ) || empty( $this->settings->acf_repeater_name ) ) {
			return;
		}

		$data = array();

		if ( is_tax() || is_category() ) {
			$post_id = get_queried_object();
		}

		$post_id = apply_filters( 'pp_accordion_acf_post_id', $post_id, $this->settings );

		$repeater_name = $this->settings->acf_repeater_name;
		$label_name    = $this->settings->acf_repeater_label;
		$content_name  = $this->settings->acf_repeater_content;

		global $wp_embed;

		if ( have_rows( $repeater_name, $post_id ) ) {
			while ( have_rows( $repeater_name, $post_id ) ) {
				the_row();

				$label = get_sub_field( $label_name );
				$content_obj = get_sub_field_object( $content_name );
				$content = $content_obj['value'];

				if ( 'file' === $content_obj['type'] ) {
					$content = sprintf( '<a href="%s" target="_blank" rel="nofollow">%s</a>', $content, $content );
				}

				$item          = new stdClass;
				$item->post_id = $post_id;
				$item->label   = $label;
				$item->content = wpautop( $wp_embed->autoembed( $content ) );

				$data[] = $item;
			}
		}

		return $data;
	}

	public function get_acf_relationship_data() {
		if ( ! isset( $this->settings->acf_relational_key ) || empty( $this->settings->acf_relational_key ) ) {
			return;
		}

		$data = array();
		$settings = new stdClass;

		$settings->data_source = 'acf_relationship';
		$settings->data_source_acf_relational_key = $this->settings->acf_relational_key;
		$settings->data_source_acf_relational_type = $this->settings->acf_relational_type;
		$settings->data_source_acf_order_by = $this->settings->acf_order_by;
		$settings->data_source_acf_order = $this->settings->acf_order;
		$settings->data_source_acf_order_by_meta_key = $this->settings->acf_order_by_meta_key;
		$settings->posts_per_page = '-1';
		$settings->id = $this->settings->id;
		$settings->class = $this->settings->class;

		$settings = apply_filters( 'pp_accordion_acf_relationship_data_settings', $settings, $this->settings );

		$data = BB_PowerPack_Post_Helper::get_posts_properties_as_data( $settings, array( 'title' => 'label' ) );

		return $data;
	}

	public function get_acf_options_page_data( $post_id = false ) {
		if ( ! isset( $this->settings->acf_options_page_repeater_name ) || empty( $this->settings->acf_options_page_repeater_name ) ) {
			return;
		}

		$data = array();

		$repeater_name = $this->settings->acf_options_page_repeater_name;
		$label_name    = $this->settings->acf_options_page_repeater_label;
		$content_name  = $this->settings->acf_options_page_repeater_content;

		$repeater_rows = get_field( $repeater_name, 'option' );
		if ( ! $repeater_rows ) {
			return $data;
		}

		foreach ( $repeater_rows as $row ) {
			$item          = new stdClass;
			$item->label   = isset( $row[ $label_name ] ) ? $row[ $label_name ] : '';
			$item->content = isset( $row[ $content_name ] ) ? $row[ $content_name ] : '';

			$data[] = $item;
		}
		return $data;
	}

	public function get_accordion_items( $id = '' ) {
		$items = array();
		$source = $this->get_data_source();

		if ( 'manual' === $source ) {
			$items = $this->settings->items;
		} elseif ( 'acf' === $source ) {
			$items = $this->get_acf_data();
		} elseif ( 'acf_relationship' === $source ) {
			$items = $this->get_acf_relationship_data();
		} elseif ( 'acf_options_page' === $source ) {
			$items = $this->get_acf_options_page_data();
		} elseif ( 'post' === $source || 'pods_relationship' === $source ) {
			$items = $this->get_cpt_data();
		}

		if ( ! empty( $items ) ) {
			for ( $i = 0; $i < count( $items ); $i++ ) {
				$html_id = ( '' !== $this->settings->accordion_id_prefix ) ? $this->settings->accordion_id_prefix . '-' . ( $i + 1 ) : 'pp-accord-' . $id . '-' . ( $i + 1 );
				$items[ $i ]->html_id = $html_id;
			}
		}

		return apply_filters( 'pp_accordion_items', $items, $this->settings );
	}

	public function render_accordion_item_icon( $item ) {
		$icon_type = isset( $item->accordion_icon_type ) ? $item->accordion_icon_type : 'icon';

		if ( 'icon' === $icon_type && isset( $item->accordion_font_icon ) && ! empty( $item->accordion_font_icon ) ) {
			?>
			<span class="pp-accordion-icon <?php echo $item->accordion_font_icon; ?>"></span>
			<?php
		}

		if ( 'image' === $icon_type && isset( $item->accordion_image_icon ) && ! empty( $item->accordion_image_icon ) ) {
			$image = wp_get_attachment_image( $item->accordion_image_icon, apply_filters( 'pp_accordion_icon_image_size', 'thumbnail', $item, $this->settings ) );
			$image = empty( $image ) ? '<img src="' . esc_url( $item->accordion_image_icon_src ) . '" alt="' . htmlspecialchars( $item->label ) . '" />' : $image;
			?>
			<span class="pp-accordion-icon"><?php echo $image; ?></span>
			<?php
		}
	}

	public function render_post_content( $post_id ) {
		global $post;

		if ( $post instanceof WP_Post && $post->ID == $post_id ) {
			if ( isset( $_GET['fl_builder'] ) ) {
                echo esc_html__( 'You cannot use the current post as template.', 'bb-powerpack' );
            }
			return;
		}

		pp_render_post_content( $post_id );
	}

	/**
	 * Render content.
	 *
	 * @since 1.4
	 */
	public function render_content( $item ) {
		if ( 'manual' !== $this->get_data_source() ) {
			echo $item->content;
			return;
		}

		$html = '';

		switch ( $item->content_type ) {
			case 'content':
				global $wp_embed;
				$html  = '<div itemprop="text">';
				$html .= wpautop( $wp_embed->autoembed( $item->content ) );
				$html .= '</div>';
				echo $html;
				break;
			case 'photo':
				$alt   = ! empty( $item->content_photo ) ? get_post_meta( $item->content_photo , '_wp_attachment_image_alt', true ) : '';
				$alt   =  empty( $alt ) ? htmlspecialchars( $item->label ) : htmlspecialchars( $alt );
				$html  = '<div itemprop="image">';
				$html .= '<img src="' . $item->content_photo_src . '" alt="' . $alt . '" style="max-width: 100%;" />';
				$html .= '</div>';
				echo $html;
				break;
			case 'video':
				global $wp_embed;
				echo $wp_embed->autoembed( $item->content_video );
				break;
			case 'module':
				$this->render_post_content( $item->content_module );
				break;
			case 'column':
				$this->render_post_content( $item->content_column );
				break;
			case 'row':
				$this->render_post_content( $item->content_row );
				break;
			case 'layout':
				$this->render_post_content( $item->content_layout );
				break;
			default:
				break;
		}
	}

	public function get_schema_items( $items = null ) {
		$enabled = apply_filters( 'pp_accordion_enable_faq_schema', false, $this->settings );

		if ( ! $enabled ) {
			return array();
		}

		if ( is_null( $items ) ) {
			$items = $this->get_accordion_items();
		}

		if ( empty( $items ) ) {
			return array();
		}

		$faq_items = array();

		foreach ( $items as $item ) {
			$faq_item           = new stdClass;
			$faq_item->question = $item->label;
			$faq_item->answer   = $item->content;
			$faq_items[]        = $faq_item;
		}

		return $faq_items;
	}

	public function maybe_render_schema( $return = false, $items = null ) {
		$enabled = apply_filters( 'pp_accordion_enable_faq_schema', false, $this->settings );

		if ( ! $enabled ) {
			return;
		}

		if ( ! is_callable( 'PPModuleExtend::get_faq_schema' ) ) {
			return;
		}

		$faq_items = $this->get_schema_items( $items );

		if ( empty( $faq_items ) ) {
			return;
		}

		$this->settings->enable_schema = 'yes';

		$schema_data = PPModuleExtend::get_faq_schema( $faq_items, $this->settings );

		if ( $return ) {
			return $schema_data;
		}
		?>
		<script type="application/ld+json">
		<?php echo json_encode( $schema_data ); ?>
		</script>
		<?php
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module(
	'PPAccordionModule',
	array(
		'items'      => array(
			'title'    => __( 'Items', 'bb-powerpack' ),
			'sections' => array(
				'general'      => array(
					'title' => '',
					'file'  => BB_POWERPACK_DIR . 'includes/ui-setting-fields.php',
				),
				'items' => array(
					'title' => __( 'Items', 'bb-powerpack' ),
					'fields' => array(
						'items'            => array(
							'type'         => 'form',
							'label'        => __( 'Item', 'bb-powerpack' ),
							'form'         => 'pp_accordion_items_form',
							'preview_text' => 'label',
							'multiple'     => true,
							'default'  => array(
								array(
									'label' => __( 'Accordion Item 1', 'bb-powerpack' ),
									'content' => __( 'Accordion Item Content 1', 'bb-powerpack' ),
								),
								array(
									'label' => __( 'Accordion Item 2', 'bb-powerpack' ),
									'content' => __( 'Accordion Item Content 2', 'bb-powerpack' ),
								),
								array(
									'label' => __( 'Accordion Item 3', 'bb-powerpack' ),
									'content' => __( 'Accordion Item Content 3', 'bb-powerpack' ),
								),
							),
						),
					),
				),
				'post_content' => array(
					'title' => __( 'Content', 'bb-powerpack' ),
					'file'  => BB_POWERPACK_DIR . 'includes/ui-loop-settings-simple.php',
				),
			),
		),
		'icon_style' => array(
			'title'    => __( 'Icon', 'bb-powerpack' ),
			'sections' => array(
				'accordion_icon_style'    => array(
					'title'	=> '',
					'fields'	=> array(
						'accordion_icon_size'   => array(
							'type'          => 'unit',
							'label'         => __( 'Size', 'bb-powerpack' ),
							'units'			=> array( 'px' ),
							'slider'		=> true,
							'default'       => '15',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-accordion-item .pp-accordion-icon, .pp-accordion-item .pp-accordion-icon:before',
								'property'  => 'font-size',
								'unit'      => 'px'
							)
						),
						'accordion_icon_custom_spacing'   => array(
							'type'          => 'unit',
							'label'         => __( 'Spacing', 'bb-powerpack' ),
							'units'			=> array( 'px' ),
							'slider'		=> true,
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-accordion-item .pp-accordion-icon',
								'property'  => 'margin-right',
								'unit'      => 'px'
							)
						),
					)
				),
				'responsive_toggle_icons' => array(
					'title'	=> __( 'Toggle Icons', 'bb-powerpack' ),
					'fields'	=> array(
						'accordion_open_icon'         => array(
							'type'          => 'icon',
							'label'         => __( 'Open Icon', 'bb-powerpack' ),
							'show_remove'   => true
						),
						'accordion_close_icon'        => array(
							'type'          => 'icon',
							'label'         => __( 'Close Icon', 'bb-powerpack' ),
							'show_remove'   => true
						),
						'accordion_icon_position'     => array(
							'type'    => 'select',
							'label'   => __( 'Icon Position', 'bb-powerpack' ),
							'default' => 'right',
							'options' => array(
								'left'  => __( 'Before Text', 'bb-powerpack' ),
								'right' => __( 'After Text', 'bb-powerpack' ),
							),
						),
						'accordion_icon_spacing'      => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'default'    => '15',
						),
						'accordion_toggle_icon_size'  => array(
							'type'          => 'unit',
							'label'         => __( 'Size', 'bb-powerpack' ),
							'units'			=> array( 'px' ),
							'slider'		=> true,
							'default'       => '14',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-accordion-item .pp-accordion-button-icon, .pp-accordion-item .pp-accordion-button-icon:before',
								'property'  => 'font-size',
								'unit'      => 'px'
							)
						),
						'accordion_toggle_icon_color' => array(
							'type'          => 'color',
							'label'         => __( 'Color', 'bb-powerpack' ),
							'default'       => '',
							'show_reset'	=> true,
							'connections'	=> array( 'color' ),
							'preview'	    => array(
								'type'	=> 'css',
								'selector'	=> '.pp-accordion-item .pp-accordion-button-icon',
								'property'	=> 'color',
							),
						),
					),
				),
			),
		),
		'style'      => array(
			'title'         => __( 'Style', 'bb-powerpack' ),
			'sections'      => array(
				'general'       => array(
					'title'         => '',
					'fields'        => array(
						'item_spacing'     => array(
							'type'          => 'unit',
							'label'         => __( 'Item Spacing', 'bb-powerpack' ),
							'default'       => '10',
							'units'			=> array( 'px' ),
							'slider'		=> true,
							'preview'       => array(
								'type'          => 'css',
								'selector'      => '.pp-accordion-item',
								'property'      => 'margin-bottom',
								'unit'			=> 'px'
							)
						),
						'item_border'		=> array(
							'type'				=> 'border',
							'label'         	=> __( 'Border', 'bb-powerpack' ),
							'responsive'		=> true,
							'preview'       	=> array(
								'type'          	=> 'css',
								'selector'			=> '.pp-accordion-item',
								'important'			=> false,
							),
						),
						'collapse'   => array(
							'type'          => 'pp-switch',
							'label'         => __( 'Collapse Inactive', 'bb-powerpack' ),
							'default'       => '1',
							'options'       => array(
								'1'             => __( 'Yes', 'bb-powerpack' ),
								'0'             => __( 'No', 'bb-powerpack' )
							),
							'help'          => __( 'Enabling this option will keep only one item open at a time. Or it will allow multiple items to be open at the same time.', 'bb-powerpack' ),
							'preview'       => array(
								'type'          => 'none'
							)
						),
						'open_first'       => array(
							'type'          => 'pp-switch',
							'label'         => __( 'Expand First Item', 'bb-powerpack' ),
							'default'       => '0',
							'options'       => array(
								'1'             => __( 'Yes', 'bb-powerpack' ),
								'0'             => __( 'No', 'bb-powerpack' ),
							),
							'help' 			=> __( 'Choosing yes will expand the first item by default.', 'bb-powerpack' ),
							'toggle'		=> array(
								'0'				=> array(
									'fields'		=> array( 'open_custom' )
								)
							)
						),
						'open_custom'	=> array(
							'type'			=> 'text',
							'label'			=> __( 'Expand Custom', 'bb-powerpack' ),
							'default'		=> '',
							'size'			=> 5,
							'help'			=> __( 'Add item number to expand by default.', 'bb-powerpack' )
						),
						'responsive_collapse'	=> array(
							'type'					=> 'pp-switch',
							'label'					=> __( 'Responsive Collapse All', 'bb-powerpack' ),
							'default'				=> 'no',
							'options'				=> array(
								'yes'					=> __( 'Yes', 'bb-powerpack' ),
								'no'					=> __( 'No', 'bb-powerpack' ),
							),
							'help'					=> __( 'Items will not appear as expanded on responsive devices until user clicks on it.', 'bb-powerpack' )
						),
						'accordion_id_prefix'	=> array(
							'type'			=> 'text',
							'label'			=> __( 'Custom ID Prefix', 'bb-powerpack' ),
							'default'		=> '',
							'placeholder'	=> __( 'myaccordion', 'bb-powerpack' ),
							'help'			=> __( 'A prefix that will be applied to ID attribute of accordion items in HTML. For example, prefix "myaccordion" will be applied as "myaccordion-1", "myaccordion-2" in ID attribute of accordion item 1 and accordion item 2 respectively. It should only contain dashes, underscores, letters or numbers. No spaces.', 'bb-powerpack' )
						),
					)
				),
				'label_style'       => array(
					'title'         => __( 'Title', 'bb-powerpack' ),
					'fields'        => array(
						'label_bg_color_default'	=> array(
							'type'			=> 'color',
							'label'			=> __( 'Background Color - Default', 'bb-powerpack' ),
							'default'		=> '',
							'show_reset'	=> true,
							'show_alpha'	=> true,
							'connections'	=> array( 'color' ),
							'preview'		=> array(
								'type'			=> 'css',
								'selector'		=> '.pp-accordion-item .pp-accordion-button',
								'property'		=> 'background-color',
							),
						),
						'label_bg_color_active'	=> array(
							'type'			=> 'color',
							'label'			=> __( 'Background Color - Active', 'bb-powerpack' ),
							'default'		=> '',
							'show_reset'	=> true,
							'show_alpha'	=> true,
							'connections'	=> array( 'color' ),
						),
						'label_text_color_default'	=> array(
							'type'			=> 'color',
							'label'			=> __( 'Text Color - Default', 'bb-powerpack' ),
							'default'		=> '',
							'show_reset'	=> true,
							'connections'	=> array( 'color' ),
							'preview'		=> array(
								'type'			=> 'css',
								'selector'		=> '.pp-accordion-item .pp-accordion-button',
								'property'		=> 'color',
							),
						),
						'label_text_color_active'	=> array(
							'type'			=> 'color',
							'label'			=> __( 'Text Color - Active', 'bb-powerpack' ),
							'default'		=> '',
							'connections'	=> array( 'color' ),
							'show_reset'	=> true,
						),
						'label_border'		=> array(
							'type'				=> 'border',
							'label'         	=> __( 'Border', 'bb-powerpack' ),
							'responsive'		=> true,
							'preview'       	=> array(
								'type'          	=> 'css',
								'selector'			=> '.pp-accordion-item .pp-accordion-button',
								'important'			=> false,
							),
						),
						'label_padding'	=> array(
							'type'			=> 'dimension',
							'label'			=> __( 'Padding', 'bb-powerpack' ),
							'units'			=> array( 'px' ),
							'default'		=> '',
							'slider'		=> true,
							'responsive'	=> true,
							'preview'		=> array(
								'type'			=> 'css',
								'selector'		=> '.pp-accordion-item .pp-accordion-button',
								'property'		=> 'padding',
								'unit'			=> 'px',
							),
						),
					),
				),
				'content_style'       => array(
					'title'         => __( 'Content', 'bb-powerpack' ),
					'fields'        => array(
						'content_bg_color'  => array(
							'type'          => 'color',
							'label'         => __( 'Background Color', 'bb-powerpack' ),
							'default'       => '',
							'show_reset'	=> true,
							'show_alpha'	=> true,
							'connections'	=> array( 'color' ),
							'preview'		=> array(
								'type'			=> 'css',
								'selector'		=> '.pp-accordion-item .pp-accordion-content',
								'property'		=> 'background-color',
							),
						),
						'content_text_color'  => array(
							'type'          => 'color',
							'label'         => __( 'Text Color', 'bb-powerpack' ),
							'default'       => '',
							'show_reset'	=> true,
							'connections'	=> array( 'color' ),
							'preview'		=> array(
								'type'			=> 'css',
								'selector'		=> '.pp-accordion-item .pp-accordion-content',
								'property'		=> 'color',
							),
						),
						'content_border'	=> array(
							'type'				=> 'border',
							'label'				=> __( 'Border', 'bb-powerpack' ),
							'responsive'		=> true,
							'preview'       	=> array(
								'type'          	=> 'css',
								'selector'			=> '.pp-accordion-item .pp-accordion-content',
								'important'			=> false,
							),
						),
						'content_padding'	=> array(
							'type'				=> 'dimension',
							'label'				=> __( 'Padding', 'bb-powerpack' ),
							'default'			=> '',
							'units'				=> array( 'px' ),
							'slider'			=> true,
							'responsive'		=> true,
						),
					),
				),
			),
		),
		'typography' => array(
			'title'         => __( 'Typography', 'bb-powerpack' ),
			'sections'      => array(
				'label_typography'	=> array(
					'title'				=> __( 'Title', 'bb-powerpack' ),
					'fields'			=> array(
						'label_html_tag'    => array(
							'type'  => 'select',
							'label' => __( 'HTML Tag', 'bb-powerpack' ),
							'default' => 'span',
							'sanitize' => array( 'pp_esc_tags', 'span' ),
							'options' => array(
								'h1'   => 'h1',
								'h2'   => 'h2',
								'h3'   => 'h3',
								'h4'   => 'h4',
								'h5'   => 'h5',
								'h6'   => 'h6',
								'div'  => 'div',
								'span' => 'span',
							),
						),
						'label_typography'	=> array(
							'type'				=> 'typography',
							'label'				=> __( 'Title Typography', 'bb-powerpack' ),
							'responsive'  		=> true,
							'preview'			=> array(
								'type'				=> 'css',
								'selector'			=> '.pp-accordion-item .pp-accordion-button .pp-accordion-button-label',
							),
						),
					),
				),
				'content_typography'	=> array(
					'title'	=> __( 'Content', 'bb-powerpack' ),
					'fields'	=> array(
						'content_typography'	=> array(
							'type'					=> 'typography',
							'label'					=> __( 'Content Typography', 'bb-powerpack' ),
							'responsive'  			=> true,
							'preview'				=> array(
								'type'					=> 'css',
								'selector'				=> '.pp-accordion-item .pp-accordion-content'
							)
						),
					)
				),
			)
		),
	)
);

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form( 'pp_accordion_items_form', array(
	'title' => __( 'Add Item', 'bb-powerpack' ),
	'tabs'  => array(
		'general'      => array(
			'title'         => __( 'General', 'bb-powerpack' ),
			'sections'      => array(
				'general'       => array(
					'title'         => '',
					'fields'        => array(
						'accordion_icon_type' => array(
							'type' => 'pp-switch',
							'label' => __( 'Icon Type', 'bb-powerpack' ),
							'default' => 'icon',
							'options' => array(
								'icon' => __( 'Icon', 'bb-powerpack' ),
								'image' => __( 'Image', 'bb-powerpack' ),
							),
							'toggle'	=> array(
								'icon' => array(
									'fields' => array( 'accordion_font_icon' ),
								),
								'image' => array(
									'fields' => array( 'accordion_image_icon' ),
								),
							),
						),
						'accordion_font_icon' => array(
							'type'          => 'icon',
							'label'         => __( 'Icon', 'bb-powerpack' ),
							'show_remove'   => true,
						),
						'accordion_image_icon' => array(
							'type' => 'photo',
							'label' => __( 'Image', 'bb-powerpack' ),
							'connections' => array( 'photo' ),
							'show_remove' => true,
						),
						'label'         => array(
							'type'          => 'text',
							'label'         => __( 'Title', 'bb-powerpack' ),
							'connections'   => array( 'string', 'html', 'url' ),
						)
					)
				),
				'content'       => array(
					'title'         => __( 'Content', 'bb-powerpack' ),
					'fields'        => array(
						'content_type'	=> array(
							'type'			=> 'select',
							'label'			=> __( 'Type', 'bb-powerpack' ),
							'default'		=> 'content',
							'options'		=> array(
								'content'		=> __( 'Content', 'bb-powerpack' ),
								'photo'			=> __( 'Photo', 'bb-powerpack' ),
								'video'			=> __( 'Video', 'bb-powerpack' ),
								'module'		=> __( 'Saved Module', 'bb-powerpack' ),
								'column'		=> __('Saved Column', 'bb-powerpack'),
								'row'			=> __( 'Saved Row', 'bb-powerpack' ),
								'layout'		=> __( 'Saved Layout', 'bb-powerpack' ),
							),
							'toggle'		=> array(
								'content'		=> array(
									'fields'		=> array( 'content' )
								),
								'photo'		=> array(
									'fields'	=> array( 'content_photo' )
								),
								'video'		=> array(
									'fields'	=> array( 'content_video' )
								),
								'module'	=> array(
									'fields'	=> array( 'content_module', 'content_edit' )
								),
								'column'	=> array(
									'fields'	=> array( 'content_column', 'content_edit' )
								),
								'row'		=> array(
									'fields'	=> array( 'content_row', 'content_edit' )
								),
								'layout'	=> array(
									'fields'	=> array( 'content_layout', 'content_edit' )
								)
							)
						),
						'content'       => array(
							'type'          => 'editor',
							'label'         => '',
							'connections'   => array( 'string', 'html', 'url' ),
						),
						'content_photo'	=> array(
							'type'			=> 'photo',
							'label'			=> __( 'Photo', 'bb-powerpack' ),
							'connections'   => array( 'photo' ),
						),
						'content_video'     => array(
							'type'              => 'textarea',
							'label'             => __( 'Embed Code / URL', 'bb-powerpack' ),
							'rows'              => 6,
							'connections'   	=> array( 'string', 'html', 'url' ),
						),
						'content_module'	=> array(
							'type'				=> 'select',
							'label'				=> __('Saved Module', 'bb-powerpack'),
							'options'			=> array(),
							'saved_data'        => 'module',
						),
						'content_column'	=> array(
							'type'				=> 'select',
							'label'				=> __('Saved Column', 'bb-powerpack'),
							'options'			=> array(),
							'saved_data'        => 'column',
						),
						'content_row'		=> array(
							'type'				=> 'select',
							'label'				=> __('Saved Row', 'bb-powerpack'),
							'options'			=> array(),
							'saved_data'        => 'row',
						),
						'content_layout'	=> array(
							'type'				=> 'select',
							'label'				=> __('Saved Layout', 'bb-powerpack'),
							'options'			=> array(),
							'saved_data'        => 'layout',
						),
						'content_edit' => array(
							'type'      => 'button',
							'label'     => __( 'Edit', 'bb-powerpack' ),
							'className' => 'content_edit'
						),
					)
				)
			)
		),
		'style' => array(
			'title' => __( 'Style', 'bb-powerpack' ),
			'sections' => array(
				'item_label_style'       => array(
					'title'         => __( 'Title', 'bb-powerpack' ),
					'fields'        => array(
						'item_label_bg_color_default'	=> array(
							'type'			=> 'color',
							'label'			=> __( 'Background Color - Default', 'bb-powerpack' ),
							'default'		=> '',
							'show_reset'	=> true,
							'show_alpha'	=> true,
							'connections'	=> array( 'color' ),
						),
						'item_label_bg_color_active'	=> array(
							'type'			=> 'color',
							'label'			=> __( 'Background Color - Active', 'bb-powerpack' ),
							'default'		=> '',
							'show_reset'	=> true,
							'show_alpha'	=> true,
							'connections'	=> array( 'color' ),
						),
						'item_label_text_color_default'	=> array(
							'type'			=> 'color',
							'label'			=> __( 'Text Color - Default', 'bb-powerpack' ),
							'default'		=> '',
							'show_reset'	=> true,
							'connections'	=> array( 'color' ),
						),
						'item_label_text_color_active'	=> array(
							'type'			=> 'color',
							'label'			=> __( 'Text Color - Active', 'bb-powerpack' ),
							'default'		=> '',
							'connections'	=> array( 'color' ),
							'show_reset'	=> true,
						),
						'item_label_border'		=> array(
							'type'				=> 'border',
							'label'         	=> __( 'Border', 'bb-powerpack' ),
							'responsive'		=> true,
						),
					),
				),
				'item_content_style'       => array(
					'title'         => __( 'Content', 'bb-powerpack' ),
					'fields'        => array(
						'item_content_bg_color'  => array(
							'type'          => 'color',
							'label'         => __( 'Background Color', 'bb-powerpack' ),
							'default'       => '',
							'show_reset'	=> true,
							'show_alpha'	=> true,
							'connections'	=> array( 'color' ),
						),
						'item_content_text_color'  => array(
							'type'          => 'color',
							'label'         => __( 'Text Color', 'bb-powerpack' ),
							'default'       => '',
							'show_reset'	=> true,
							'connections'	=> array( 'color' ),
						),
						'item_content_border'	=> array(
							'type'				=> 'border',
							'label'				=> __( 'Border', 'bb-powerpack' ),
							'responsive'		=> true,
						),
					),
				),
			),
		)
	)
));
