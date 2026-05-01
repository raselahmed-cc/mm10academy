<?php
/**
 * Card Slider Module
 *
 * @package BB_PowerPack
 */

/**
 * PPCardSliderModule Class inherits FLBuilderModule class.
 */
class PPCardSliderModule extends FLBuilderModule {

	/**
	 * Class constructor.
	 *
	 * Sets the name, group, category, directory of the custom module.
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Card Slider', 'bb-powerpack' ),
				'description'     => __( 'Display posts or custom content in a trendy card', 'bb-powerpack' ),
				'group'           => pp_get_modules_group(),
				'category'        => pp_get_modules_cat( 'content' ),
				'dir'             => BB_POWERPACK_DIR . 'modules/pp-card-slider/',
				'url'             => BB_POWERPACK_URL . 'modules/pp-card-slider/',
				'editor_export'   => false,
				'partial_refresh' => true,
			)
		);
	}

	public function enqueue_scripts() {
		$this->add_css( 'jquery-swiper' );
		$this->add_js( 'jquery-swiper' );

		if ( FLBuilderModel::is_builder_active() || $this->has_lightbox() ) {
			$this->add_css( 'jquery-magnificpopup' );
			$this->add_js( 'jquery-magnificpopup' );
		}
	}

	public function enqueue_icon_styles() {
		$enqueue = false;

		if ( 'yes' === $this->settings->show_date && ! empty( $this->settings->date_icon ) ) {
			$enqueue = true;
		}
		if ( 'yes' === $this->settings->show_author && ! empty( $this->settings->author_icon ) ) {
			$enqueue = true;
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function get_slider_items() {
		$settings = $this->settings;

		if ( 'posts' === $settings->source ) {
			$items = $this->get_post_items();
		} else {
			$items = $this->get_custom_items();
		}

		return apply_filters( 'pp_card_slider_items', $items, $settings );
	}

	private function get_post_items() {
		$settings = $this->settings;
		$items    = array();

		global $post;
		$initial_current_post = $post;
		$query                = FLBuilderLoop::query( $settings );
		$date_format 		  = get_option( 'date_format' );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$image      = wp_get_attachment_image_src( get_post_thumbnail_id(), $settings->image_size );
				$image_full = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
				$date 	    = get_the_date();

				if ( pp_is_tribe_events_post( get_the_ID() ) && function_exists( 'tribe_get_start_date' ) ) {
					$date = tribe_get_start_date( null, false, $date_format );
				}

				$data    = array(
					'id'         => get_the_ID(),
					'title'      => get_the_title(),
					'image'      => ! empty( $image ) ? $image[0] : null,
					'image_full' => ! empty( $image_full ) ? $image_full[0] : null,
					'author'     => get_the_author(),
					'content'    => wp_trim_words( get_the_content(), $settings->excerpt_length, '...' ),
					'date'       => $date,
					'link'       => get_permalink(),
				);
				$items[] = $data;
			}
		}

		wp_reset_postdata();
		$post = $initial_current_post;
		setup_postdata( $initial_current_post );

		return $items;
	}

	private function get_custom_items() {
		$settings     = $this->settings;
		$items        = array();
		$custom_items = $settings->card_custom_items;
		$count        = 1;

		if ( ! is_array( $custom_items ) || '' === $custom_items ) {
			return $items;
		}

		foreach ( $custom_items as $item ) {
			if ( ! is_object( $item ) ) {
				continue;
			}

			$title      = empty( $item->item_text ) ? sprintf( __( 'Card Item %s', 'bb-powerpack' ), $count ) : $item->item_text;
			$nofollow   = isset( $item->link_nofollow ) ? $item->link_nofollow : 'no';
			$target     = isset( $item->link_target ) ? $item->link_target : '_self';
			$image      = wp_get_attachment_image_src( $item->item_image, $settings->image_size );
			$image_full = wp_get_attachment_image_src( $item->item_image, 'full' );

			$data    = array(
				'id'            => $count,
				'title'         => $title,
				'content'       => $item->item_content,
				'image'         => ! empty( $image ) ? $image[0] : null,
				'image_full'    => ! empty( $image_full ) ? $image_full[0] : null,
				'link'          => esc_url( $item->link ),
				'link_nofollow' => esc_attr( $nofollow ),
				'link_target'   => esc_attr( $target ),
			);

			$items[] = $data;

			$count++;
		}

		return $items;
	}

	public function get_item_link_attrs( $item ) {
		$link_attrs = '';

		if ( ! isset( $item['link'] ) || empty( $item['link'] ) ) {
			return $link_attrs;
		} else {
			$link_attrs .= 'href="' . esc_url( $item['link'] ) . '"';
		}
		if ( isset( $item['link_nofollow'] ) && 'no' !== $item['link_nofollow'] ) {
			$link_attrs .= ' rel="nofollow"';
		}
		if ( isset( $item['link_target'] ) ) {
			$link_attrs .= ' target="' . esc_attr( $item['link_target'] ) . '"';
		}
		if ( isset( $item['title'] ) ) {
			$link_attrs .= ' aria-label="' . esc_attr( $item['title'] ) . '"';
		}

		return $link_attrs;
	}

	public function has_lightbox() {
		if ( ! isset( $this->settings ) ) {
			return false;
		}
		if ( ! in_array( $this->settings->link_type, array( 'none', 'title', 'button' ) ) ) {
			return false;
		}
		if ( ! isset( $this->settings->enable_lightbox ) || 'no' === $this->settings->enable_lightbox ) {
			return false;
		}

		return true;
	}

}

BB_PowerPack::register_module(
	'PPCardSliderModule',
	array(
		'general_tab' => array(
			'title'    => __( 'General', 'bb-powerpack' ),
			'sections' => array(
				'general_section'         => array(
					'title'  => '',
					'fields' => array(
						'source'           => array(
							'type'    => 'select',
							'label'   => __( 'Source', 'bb-powerpack' ),
							'default' => 'posts',
							'options' => array(
								'posts'  => __( 'Posts', 'bb-powerpack' ),
								'custom' => __( 'Custom', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'posts'  => array(
									'tabs'     => array( 'content_tab' ),
									'fields'   => array( 'posts_per_page' ),
									'sections' => array( 'post_meta_section', 'meta_style_section' ),
								),
								'custom' => array(
									'sections' => array( 'card_items_section' ),
								),
							),

						),
						'link_type'        => array(
							'type'    => 'select',
							'label'   => __( 'Link', 'bb-powerpack' ),
							'default' => 'title',
							'options' => array(
								'none'   => __( 'None', 'bb-powerpack' ),
								'title'  => __( 'Title', 'bb-powerpack' ),
								'image'  => __( 'Image', 'bb-powerpack' ),
								'button' => __( 'Button', 'bb-powerpack' ),
								'box'    => __( 'Box', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'none'   => array(
									'fields' => array( 'enable_lightbox' ),
								),
								'title'   => array(
									'fields' => array( 'enable_lightbox' ),
								),
								'button' => array(
									'sections' => array( 'card_button_style_section' ),
									'fields'   => array( 'card_button_text', 'enable_lightbox' ),
								),
							),
						),
						'card_button_text' => array(
							'type'    => 'text',
							'label'   => __( 'Button Text', 'bb-powerpack' ),
							'default' => 'Read More',
						),
						'posts_per_page'   => array(
							'type'      => 'text',
							'label'     => __( 'Posts Count', 'bb-powerpack' ),
							'default'   => 5,
							'maxlength' => '2',
							'size'      => '3',
						),
						'title_tag'        => array(
							'type'    => 'select',
							'label'   => __( 'Title HTML Tag', 'bb-powerpack' ),
							'default' => 'h3',
							'sanitize' => array( 'pp_esc_tags', 'h3' ),
							'options' => array(
								'h1'   => __( 'H1', 'bb-powerpack' ),
								'h2'   => __( 'H2', 'bb-powerpack' ),
								'h3'   => __( 'H3', 'bb-powerpack' ),
								'h4'   => __( 'H4', 'bb-powerpack' ),
								'h5'   => __( 'H5', 'bb-powerpack' ),
								'h6'   => __( 'H6', 'bb-powerpack' ),
								'div'  => __( 'div', 'bb-powerpack' ),
								'span' => __( 'span', 'bb-powerpack' ),
								'p'    => __( 'p', 'bb-powerpack' ),
							),
						),
						'not_found_msg' => array(
							'type'	=> 'text',
							'label' => __( 'No Items Found Message', 'bb-powerpack' ),
							'default' => __( 'No items found', 'bb-powerpack' ),
							'connections' => array( 'string' ),
						),
					),
				),
				'card_content_section'    => array(
					'title'     => __( 'Card Content', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'show_title'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Title', 'bb-powerpack' ),
							'default' => 'yes',
							'toggle'  => array(
								'yes' => array(
									'sections' => array( 'title_style_section' ),
								),
							),
						),
						'show_image'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Image', 'bb-powerpack' ),
							'default' => 'yes',
							'toggle'  => array(
								'yes' => array(
									'sections' => array( 'style_image' ),
									'fields'   => array( 'image_size' ),
								),
							),
						),
						'image_size'     => array(
							'type'        => 'photo-sizes',
							'label'       => __( 'Image Size', 'bb-powerpack' ),
							'default'     => 'thumbnail',
							'connections' => array( 'photo' ),
						),
						'enable_lightbox'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Enable Lightbox on image click', 'bb-powerpack' ),
							'default' => 'no',
							'help'    => __( 'If the image is enabled then it will display the image in lightbox when clicked.', 'bb-powerpack' ),
						),
						'show_excerpt'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Excerpt', 'bb-powerpack' ),
							'default' => 'yes',
							'toggle'  => array(
								'yes' => array(
									'sections' => array( 'content_style_section' ),
									'fields'   => array( 'excerpt_length' ),
								),
							),
						),
						'excerpt_length' => array(
							'type'    => 'unit',
							'label'   => __( 'Excerpt Length', 'bb-powerpack' ),
							'units'   => array( 'words' ),
							'slider'  => array(
								'min'  => 0,
								'max'  => 100,
								'step' => 1,
							),
							'default' => 20,
						),
					),
				),
				'post_meta_section'       => array(
					'title'     => __( 'Post Meta', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'show_author'    => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Author', 'bb-powerpack' ),
							'default' => 'yes',
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'author_icon' ),
								),
							),
						),
						'author_icon'    => array(
							'type'        => 'icon',
							'label'       => __( 'Author Icon', 'bb-powerpack' ),
							'show_remove' => true,
						),
						'show_date'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Date', 'bb-powerpack' ),
							'default' => 'yes',
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'date_icon' ),
								),
							),
						),
						'date_icon'      => array(
							'type'        => 'icon',
							'label'       => __( 'Date Icon', 'bb-powerpack' ),
							'show_remove' => true,
						),
						'meta_placement' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Meta Placement', 'bb-powerpack' ),
							'default' => 'below',
							'options' => array(
								'above' => __( 'Above Title', 'bb-powerpack' ),
								'below' => __( 'Below Title', 'bb-powerpack' ),
							),
						),
					),
				),
				'card_items_section'      => array(
					'title'     => __( 'Card Items', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'card_custom_items' => array(
							'type'     => 'form',
							'label'    => __( 'Card Item', 'bb-powerpack' ),
							'preview_text' => 'item_text',
							'form'     => 'custom_card_items_form',
							'default'  => array(
								array(
									'item_text' => __( 'Card Item 1', 'bb-powerpack' ),
								),
								array(
									'item_text' => __( 'Card Item 2', 'bb-powerpack' ),
								),
								array(
									'item_text' => __( 'Card Item 3', 'bb-powerpack' ),
								),
							),
							'multiple' => true,
						),
					),
				),
				'slider_settings_section' => array(
					'title'     => __( 'Slider Settings', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'slide_speed'       => array(
							'type'    => 'unit',
							'label'   => __( 'Transition Speed', 'bb-powerpack' ),
							'units'   => array( 'ms' ),
							'default' => 800,
							'slider'  => array(
								'min'  => 0,
								'max'  => 4000,
								'step' => 1,
							),
							'help'    => __( 'Duration of transition between slides(in ms)', 'bb-powerpack' ),
						),
						'slide_direction'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Direction', 'bb-powerpack' ),
							'default' => 'vertical',
							'options' => array(
								'vertical'   => __( 'Vertical', 'bb-powerpack' ),
								'horizontal' => __( 'Horizontal', 'bb-powerpack' ),
							),
						),
						'autoplay'          => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Autoplay', 'bb-powerpack' ),
							'default' => 'no',
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'pause_interaction', 'autoplay_speed' ),
								),
							),
						),
						'autoplay_speed'    => array(
							'type'    => 'unit',
							'label'   => __( 'Autoplay Speed', 'bb-powerpack' ),
							'units'   => array( 'ms' ),
							'default' => 1000,
							'slider'  => array(
								'min'  => 0,
								'max'  => 4000,
								'step' => 1,
							),
						),
						'pause_interaction' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Pause on Interaction', 'bb-powerpack' ),
							'default' => 'no',
						),
						'infinite_loop'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Infinite Loop', 'bb-powerpack' ),
							'default' => 'no',
						),
						'grab_cursor'       => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Grab Cursor', 'bb-powerpack' ),
							'default' => 'no',
						),
						'show_pagination'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Pagination', 'bb-powerpack' ),
							'default' => 'yes',
							'toggle'  => array(
								'yes' => array(
									'sections' => array( 'dots_style_section' ),
								),
							),
						),
						'keyboard_nav'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Keyboard Navigation', 'bb-powerpack' ),
							'default' => 'no',
						),
					),
				),
			),
		),
		'content_tab' => array(
			'title' => __( 'Query', 'bb-powerpack' ),
			'file'  => FL_BUILDER_DIR . 'includes/loop-settings.php',
		),
		'style_tab'   => array(
			'title'    => __( 'Style', 'bb-powerpack' ),
			'sections' => array(
				'style_card'                => array(
					'title'  => __( 'Card', 'bb-powerpack' ),
					'fields' => array(
						'card_width'         => array(
							'type'         => 'unit',
							'label'        => __( 'Width', 'bb-powerpack' ),
							'units'        => array( 'px', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container',
								'property' => 'width',
							),
						),
						'card_max_width'         => array(
							'type'         => 'unit',
							'label'        => __( 'Max Width', 'bb-powerpack' ),
							'units'        => array( 'px', '%' ),
							'slider'  => array(
								'px'   => array(
									'min'  => 0,
									'max'  => 1000,
									'step' => 10,
								),
								'%'    => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 1,
								),
							),
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider',
								'property' => 'max-width',
							),
						),
						'card_padding'       => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider',
								'property' => 'padding',
							),
						),
						'card_bgcolor'       => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider',
								'property' => 'background-color',
							),
						),
						'card_bgcolor_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Hover Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider:hover',
								'property' => 'background-color',
							),
						),
						'card_border'        => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider',
								'property' => 'border',
							),
						),
					),
				),
				'content_spacing_section'   => array(
					'title'    => __( 'Content Spacing', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'card_content_margin'       => array(
							'type'       => 'dimension',
							'label'      => __( 'Margin', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-content-wrap',
								'property' => 'margin',
							),
						),
						'card_content_padding'       => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-content-wrap',
								'property' => 'padding',
							),
						),
					),
				),
				'title_style_section'       => array(
					'title'     => __( 'Title', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'title_color'          => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-title, .pp-card-slider-container .pp-card-slider-title a',
								'property' => 'color',
							),
						),
						'title_color_hover'    => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-title:hover, .pp-card-slider-container .pp-card-slider-title a:hover',
								'property' => 'color',
							),
						),
						'title_typography'     => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-title, .pp-card-slider-container .pp-card-slider-title a',
							),
						),
						'title_bottom_spacing' => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-title',
								'property' => 'margin-bottom',
							),
						),
					),
				),
				'meta_style_section'        => array(
					'title'     => __( 'Post Meta', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'meta_color'          => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-meta',
								'property' => 'color',
							),
						),
						'meta_color_hover'    => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-meta:hover',
								'property' => 'color',
							),
						),
						'meta_typography'     => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-meta',
							),
						),
						'meta_bottom_spacing' => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-meta',
								'property' => 'margin-bottom',
							),
						),
					),
				),
				'content_style_section'     => array(
					'title'     => __( 'Excerpt', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'content_color'          => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-content',
								'property' => 'color',
							),
						),
						'content_color_hover'    => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-content:hover',
								'property' => 'color',
							),
						),
						'content_typography'     => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-content',
							),
						),
						'content_bottom_spacing' => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-content',
								'property' => 'margin-bottom',
							),
						),
					),
				),
				'style_image'               => array(
					'title'     => __( 'Image', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'image_direction'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Direction', 'bb-powerpack' ),
							'default' => 'left',
							'options' => array(
								'left'  => __( 'Left', 'bb-powerpack' ),
								'right' => __( 'Right', 'bb-powerpack' ),
							),
						),
						'card_image_width'     => array(
							'type'         => 'unit',
							'label'        => __( 'Image Width', 'bb-powerpack' ),
							'units'        => array( 'px', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-image',
								'property' => 'width',
							),
						),
						'card_image_height'    => array(
							'type'         => 'unit',
							'label'        => __( 'Image Height', 'bb-powerpack' ),
							'units'        => array( 'px', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-image',
								'property' => 'height',
							),
						),
						'content_image_margin' => array(
							'type'       => 'dimension',
							'label'      => __( 'Image Margin', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-image',
								'property' => 'margin',
							),
						),
						'content_image_border' => array(
							'type'       => 'border',
							'label'      => __( 'Image Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-image, .pp-card-slider-container .pp-card-slider-image img, .pp-card-slider-container .pp-card-slider-image:after',
								'property' => 'border',
							),
						),
						'overlay_type'         => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Image Overlay Type', 'bb-powerpack' ),
							'default' => 'classic',
							'options' => array(
								'classic'  => __( 'Classic', 'bb-powerpack' ),
								'gradient' => __( 'Gradient', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'classic'  => array(
									'fields' => array( 'overlay_color' ),
								),
								'gradient' => array(
									'fields' => array( 'image_gradient' ),
								),
							),
						),
						'image_gradient'       => array(
							'type'    => 'gradient',
							'label'   => __( 'Gradient', 'bb-powerpack' ),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-image:after',
								'property' => 'background-image',
							),
						),
						'overlay_color'        => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-image:after',
								'property' => 'background-color',
							),
						),
					),
				),
				'card_button_style_section' => array(
					'title'     => __( 'Button', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'button_size'          => array(
							'type'         => 'unit',
							'label'        => __( 'Size', 'bb-powerpack' ),
							'units'        => array( 'px', '%' ),
							'slider'       => true,
							'default_unit' => '%',
							'responsive'   => true,
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button',
								'property' => 'width',
							),
						),
						'button_spacing'       => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button-wrap',
								'property' => 'margin-top',
							),
						),
						'button_bgcolor'       => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button',
								'property' => 'background-color',
							),
						),
						'button_bgcolor_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Hover Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button:hover',
								'property' => 'background-color',
							),
						),
						'button_color'         => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button',
								'property' => 'color',
							),
						),
						'button_color_hover'   => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button:hover',
								'property' => 'color',
							),
						),
						'button_typography'    => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button',
							),
						),
						'button_padding'       => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button',
								'property' => 'padding',
							),
						),
						'button_border'        => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .pp-card-slider-button',
								'property' => 'border',
							),
						),
					),
				),
				'dots_style_section'        => array(
					'title'     => __( 'Dots', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'dots_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .swiper-pagination .swiper-pagination-bullet',
								'property' => 'background-color',
							),
						),
						'dots_color_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .swiper-pagination .swiper-pagination-bullet:hover',
								'property' => 'background-color',
							),
						),
						'dots_width'       => array(
							'type'       => 'unit',
							'label'      => __( 'Width', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .swiper-pagination .swiper-pagination-bullet',
								'property' => 'width',
							),
						),
						'dots_height'      => array(
							'type'       => 'unit',
							'label'      => __( 'Height', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .swiper-pagination .swiper-pagination-bullet',
								'property' => 'height',
							),
						),
						'dots_spacing'     => array(
							'type'    => 'unit',
							'label'   => __( 'Spacing', 'bb-powerpack' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .swiper-pagination .swiper-pagination-bullet',
								'property' => 'margin-top',
							),
						),
						'dots_margin'      => array(
							'type'       => 'dimension',
							'label'      => __( 'Position', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .swiper-pagination',
								'property' => 'margin',
							),
						),
						'dots_border'      => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-card-slider-container .swiper-pagination .swiper-pagination-bullet',
								'property' => 'border',
							),
						),
					),
				),
			),
		),
	)
);

FLBuilder::register_settings_form(
	'custom_card_items_form',
	array(
		'title' => __( 'Card Slider Item', 'bb-powerpack' ),
		'tabs'  => array(
			'custom_general' => array(
				'title'    => __( 'Card Slider Item', 'bb-powerpack' ),
				'sections' => array(
					'general_section' => array(
						'title'  => '',
						'fields' => array(
							'item_text'    => array(
								'type'        => 'text',
								'label'       => __( 'Title', 'bb-powerpack' ),
								'connections' => array( 'string' ),
							),
							'item_image'   => array(
								'type'        => 'photo',
								'label'       => __( 'Image', 'bb-powerpack' ),
								'show_remove' => true,
								'className'   => 'pp-module--photo-size-hidden',
								'connections' => array( 'photo' ),
							),
							'link'         => array(
								'type'          => 'link',
								'label'         => __( 'Link', 'bb-powerpack' ),
								'show_target'   => true,
								'show_nofollow' => true,
								'connections'   => array( 'url' ),
							),
							'item_content' => array(
								'type'          => 'editor',
								'label'         => __( 'Content', 'bb-powerpack' ),
								'media_buttons' => false,
								'wpautop'       => true,
							),
						),
					),
				),
			),
		),
	)
);
