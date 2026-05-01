<?php
/**
 * Content Ticker Module
 *
 * @package BB_PowerPack
 */

/**
 * PPContentTickerModule Class inherits FLBuilderModule class.
 */
class PPContentTickerModule extends FLBuilderModule {

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
				'name'            => __( 'Content Ticker', 'bb-powerpack' ),
				'description'     => __( 'Display posts or custom content in a trendy tab', 'bb-powerpack' ),
				'group'           => pp_get_modules_group(),
				'category'        => pp_get_modules_cat( 'content' ),
				'dir'             => BB_POWERPACK_DIR . 'modules/pp-content-ticker/',
				'url'             => BB_POWERPACK_URL . 'modules/pp-content-ticker/',
				'editor_export'   => false,
				'partial_refresh' => true,
			)
		);
	}

	public function enqueue_scripts() {
		$this->add_css( 'jquery-swiper' );
		$this->add_js( 'jquery-swiper' );
	}

	public function enqueue_icon_styles() {
		$enqueue = false;

		if ( 'yes' === $this->settings->date_toggle && ! empty( $this->settings->date_icon ) ) {
			$enqueue = true;
		}
		if ( 'yes' === $this->settings->author_toggle && ! empty( $this->settings->author_icon ) ) {
			$enqueue = true;
		}
		if ( 'yes' === $this->settings->header_enable && ! empty( $this->settings->heading_icon ) ) {
			$enqueue = true;
		}
		if ( 'yes' === $this->settings->nav_arrow && ! empty( $this->settings->arrow_type ) ) {
			$enqueue = true;
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function get_ticker_items() {
		$settings = $this->settings;

		if ( 'posts' === $settings->source ) {
			$items = $this->get_post_items();
		} else {
			$items = $this->get_custom_items();
		}

		return apply_filters( 'pp_content_ticker_items', $items, $settings );
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
				$image   = wp_get_attachment_image_src( get_post_thumbnail_id(), $settings->image_size );
				$date 	 = get_the_date();

				if ( pp_is_tribe_events_post( get_the_ID() ) && function_exists( 'tribe_get_start_date' ) ) {
					$date = tribe_get_start_date( null, false, $date_format );
				}

				$data    = array(
					'id'     => get_the_ID(),
					'title'  => get_the_title(),
					'image'  => ! empty( $image ) ? $image[0] : null,
					'author' => get_the_author(),
					'date'   => $date,
					'link'   => get_permalink(),
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
		$custom_items = $settings->ticker_custom_items;
		$count        = 1;

		if ( ! is_array( $custom_items ) || '' === $custom_items ) {
			return $items;
		}

		foreach ( $custom_items as $item ) {
			if ( ! is_object( $item ) ) {
				continue;
			}

			$title    = empty( $item->item_text ) ? sprintf( __( 'Ticker Item %s', 'bb-powerpack' ), $count ) : $item->item_text;
			$nofollow = isset( $item->link_nofollow ) ? $item->link_nofollow : 'no';
			$target   = isset( $item->link_target ) ? $item->link_target : '_self';
			$image    = wp_get_attachment_image_src( $item->item_image, $settings->image_size );

			$data    = array(
				'id'            => $count,
				'title'         => $title,
				'image'         => ! empty( $image ) ? $image[0] : null,
				'link'          => esc_url( do_shortcode( $item->link ) ),
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
			$link_attrs .= 'href="' . esc_url( do_shortcode( $item['link'] ) ) . '"';
		}
		if ( isset( $item['link_nofollow'] ) && 'no' !== $item['link_nofollow'] ) {
			$link_attrs .= ' rel="nofollow"';
		}
		if ( isset( $item['link_target'] ) ) {
			$link_attrs .= ' target="' . esc_attr( $item['link_target'] ) . '"';
		}

		return $link_attrs;
	}
}

BB_PowerPack::register_module(
	'PPContentTickerModule',
	array(
		'general_tab' => array(
			'title'    => __( 'General', 'bb-powerpack' ),
			'sections' => array(
				'general_section'         => array(
					'title'  => '',
					'fields' => array(
						'source'         => array(
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
									'sections' => array( 'post_meta_section' ),
									'fields'   => array( 'posts_count' ),
								),
								'custom' => array(
									'sections' => array( 'ticker_items_section' ),
								),
							),

						),
						'link_type'      => array(
							'type'    => 'select',
							'label'   => __( 'Link', 'bb-powerpack' ),
							'default' => 'title',
							'options' => array(
								'none'  => __( 'None', 'bb-powerpack' ),
								'title' => __( 'Title', 'bb-powerpack' ),
								'image' => __( 'Image', 'bb-powerpack' ),
								'both'  => __( 'Title + Image', 'bb-powerpack' ),
							),
						),
						'posts_per_page' => array(
							'type'      => 'text',
							'label'     => __( 'Posts Count', 'bb-powerpack' ),
							'default'   => 5,
							'maxlength' => '2',
							'size'      => '3',
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
						'title_tag'      => array(
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
				'post_meta_section'       => array(
					'title'     => __( 'Post Meta', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'post_meta_toggle' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Post Meta', 'bb-powerpack' ),
							'default' => 'no',
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'author_toggle', 'author_icon', 'date_toggle', 'date_icon', 'content_meta_color', 'content_meta_typography', 'content_meta_spacing' ),
								),
							),
						),
						'date_toggle'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Date', 'bb-powerpack' ),
							'default' => 'no',
						),
						'date_icon'        => array(
							'type'        => 'icon',
							'label'       => __( 'Date Icon', 'bb-powerpack' ),
							'show_remove' => true,
						),
						'author_toggle'    => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Author', 'bb-powerpack' ),
							'default' => 'no',
						),
						'author_icon'      => array(
							'type'        => 'icon',
							'label'       => __( 'Author Icon', 'bb-powerpack' ),
							'show_remove' => true,
						),
					),
				),
				'ticker_items_section'    => array(
					'title'     => __( 'Ticker Items', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'ticker_custom_items' => array(
							'type'     => 'form',
							'label'    => __( 'Ticker Item', 'bb-powerpack' ),
							'form'     => 'custom_ticker_items_form',
							'preview_text' => 'item_text',
							'default'  => array(
								array(
									'item_text' => __( 'Ticker Item 1', 'bb-powerpack' ),
								),
								array(
									'item_text' => __( 'Ticker Item 2', 'bb-powerpack' ),
								),
								array(
									'item_text' => __( 'Ticker Item 3', 'bb-powerpack' ),
								),
							),
							'multiple' => true,
						),
					),
				),
				'header_section'          => array(
					'title'     => __( 'Header', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'header_enable'        => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Heading', 'bb-powerpack' ),
							'default' => 'yes',
							'toggle'  => array(
								'yes' => array(
									'fields'   => array( 'heading_text', 'heading_icon', 'heading_icon_align', 'heading_arrow_enable' ),
									'sections' => array( 'style_heading' ),
								),
							),
						),
						'heading_text'         => array(
							'type'        => 'text',
							'label'       => __( 'Heading Text', 'bb-powerpack' ),
							'default'     => __( 'Trending Now', 'bb-powerpack' ),
							'connections' => array( 'string' ),
						),
						'heading_icon'         => array(
							'type'        => 'icon',
							'label'       => __( 'Icon', 'bb-powerpack' ),
							'show_remove' => true,
							'default'     => 'fas fa-bolt',
						),
						'heading_icon_align'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Icon Position', 'bb-powerpack' ),
							'default' => 'left',
							'options' => array(
								'left'  => __( 'Left', 'bb-powerpack' ),
								'right' => __( 'Right', 'bb-powerpack' ),
							),
						),
						'heading_arrow_enable' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Arrow', 'bb-powerpack' ),
							'default' => 'yes',
						),
						'heading_link' => array(
							'type' => 'link',
							'label' => __( 'Link', 'bb-powerpack' ),
							'show_nofollow' => true,
							'show_target' => true,
							'connections' => array( 'url' ),
						),
					),
				),
				'ticker_settings_section' => array(
					'title'     => __( 'Ticker Settings', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'ticker_effect'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Effect', 'bb-powerpack' ),
							'default' => 'fade',
							'help'    => __( 'Sets Transition effect', 'bb-powerpack' ),
							'options' => array(
								'fade'  => __( 'Fade', 'bb-powerpack' ),
								'slide' => __( 'Slide', 'bb-powerpack' ),
								'cube'  => __( '3D', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'slide' => array(
									'fields' => array( 'slide_direction' ),
								),
							),
						),
						'slide_direction'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Slide Direction', 'bb-powerpack' ),
							'default' => 'horizontal',
							'options' => array(
								'horizontal' => __( 'Horizontal', 'bb-powerpack' ),
								'vertical'   => __( 'Vertical', 'bb-powerpack' ),
							),
						),
						'slide_speed'       => array(
							'type'    => 'unit',
							'label'   => __( 'Transition Duration', 'bb-powerpack' ),
							'units'   => array( 'ms' ),
							'default' => 800,
							'slider'  => array(
								'min'  => 0,
								'max'  => 4000,
								'step' => 1,
							),
							'help'    => __( 'Duration of transition between slides(in ms)', 'bb-powerpack' ),
						),
						'autoplay'          => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Autoplay', 'bb-powerpack' ),
							'default' => 'yes',
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
						'nav_arrow'         => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Navigation Arrows', 'bb-powerpack' ),
							'default' => 'yes',
							'toggle'  => array(
								'yes' => array(
									'sections' => array( 'style_arrow' ),
								),
							),
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
				'style_heading' => array(
					'title'  => __( 'Header', 'bb-powerpack' ),
					'fields' => array(
						'heading_bgcolor'    => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-heading',
								'property' => 'background-color',
							),
						),
						'heading_txtcolor'   => array(
							'type'        => 'color',
							'label'       => __( 'Text Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-heading, .pp-content-ticker-container .pp-content-ticker-heading a',
								'property' => 'color',
							),
						),
						'heading_padding'    => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-heading',
								'property' => 'padding',
							),
						),
						'heading_border'     => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-heading',
								'property' => 'border',
							),
						),
						'heading_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-heading',
							),
						),
						'heading_alignment'  => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Alignment', 'bb-powerpack' ),
							'default' => 'flex-start',
							'options' => array(
								'flex-start' => __( 'Left', 'bb-powerpack' ),
								'center'     => __( 'Center', 'bb-powerpack' ),
								'flex-end'   => __( 'Right', 'bb-powerpack' ),
							),
						),
						'heading_width'      => array(
							'type'         => 'unit',
							'label'        => __( 'Width', 'bb-powerpack' ),
							'units'        => array( 'px', '%' ),
							'slider'       => true,
							'responsive'   => true,
							'default_unit' => '%',
							'preview'      => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-heading',
								'property' => 'width',
							),
						),
					),
				),
				'style_content' => array(
					'title'     => __( 'Content', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'content_padding'          => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-wrap',
								'property' => 'padding',
							),
						),
						'content_bgcolor'          => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container',
								'property' => 'background-color',
							),
						),
						'content_bgcolor_hover'    => array(
							'type'        => 'color',
							'label'       => __( 'Hover Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container',
								'property' => 'background-color',
							),
						),
						'content_border'           => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container',
								'property' => 'border',
							),
						),
						'content_title_color'      => array(
							'type'        => 'color',
							'label'       => __( 'Title Text Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-item-title, .pp-content-ticker-container .pp-content-ticker-item-title a',
								'property' => 'color',
							),
						),
						'content_title_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Title Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-item-title',
							),
						),
						'content_alignment'        => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Alignment', 'bb-powerpack' ),
							'default' => 'flex-start',
							'options' => array(
								'flex-start' => __( 'Left', 'bb-powerpack' ),
								'center'     => __( 'Center', 'bb-powerpack' ),
								'flex-end'   => __( 'Right', 'bb-powerpack' ),
							),
						),
						'content_bottom_spacing'   => array(
							'type'       => 'unit',
							'label'      => __( 'Bottom Spacing', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-item-title',
								'property' => 'margin-bottom',
							),
						),
						'content_meta_color'       => array(
							'type'        => 'color',
							'label'       => __( 'Post Meta Text Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-meta',
								'property' => 'color',
							),
						),
						'content_meta_typography'  => array(
							'type'       => 'typography',
							'label'      => __( 'Post Meta Typography', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-meta',
							),
						),
						'content_meta_spacing'     => array(
							'type'       => 'unit',
							'label'      => __( 'Post Meta Item Spacing', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-date',
								'property' => 'margin-right',
							),
						),
					),
				),
				'style_image'   => array(
					'title'     => __( 'Image', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'content_image_border' => array(
							'type'       => 'border',
							'label'      => __( 'Image Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-image img',
								'property' => 'border',
							),
						),
						'content_image_width'  => array(
							'type'       => 'unit',
							'label'      => __( 'Image Width', 'bb-powerpack' ),
							'units'      => array( 'px', '%' ),
							'responsive' => true,
							'slider'     => array(
								'min'  => 0,
								'max'  => 500,
								'step' => 1,
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-image',
								'property' => 'width',
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
								'selector' => '.pp-content-ticker-container .pp-content-ticker-image',
								'property' => 'margin',
							),
						),
					),
				),
				'style_arrow'   => array(
					'title'     => __( 'Arrow', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'arrow_type'          => array(
							'type'    => 'icon',
							'label'   => __( 'Choose Icon', 'bb-powerpack' ),
							'default' => 'fa fa-angle-right',
							'show_remove' => true,
							'options' => array(
								'fa fa-angle-right'        => __( 'Angle', 'bb-powerpack' ),
								'fa fa-angle-double-right' => __( 'Double Angle', 'bb-powerpack' ),
								'fa fa-chevron-right'      => __( 'Chevron', 'bb-powerpack' ),
								'fa fa-chevron-circle-right' => __( 'Chevron Circle', 'bb-powerpack' ),
								'fa fa-arrow-right'        => __( 'Arrow', 'bb-powerpack' ),
								'fa fa-long-arrow-right'   => __( 'Long Arrow', 'bb-powerpack' ),
								'fa fa-caret-right'        => __( 'Caret', 'bb-powerpack' ),
								'fa fa-caret-square-o-right' => __( 'Caret Square', 'bb-powerpack' ),
								'fa fa-arrow-circle-right' => __( 'Arrow Circle', 'bb-powerpack' ),
								'fa fa-arrow-circle-o-right' => __( 'Arrow Circle O', 'bb-powerpack' ),
								'fa fa-toggle-right'       => __( 'Toggle', 'bb-powerpack' ),
								'fa fa-hand-o-right'       => __( 'Hand', 'bb-powerpack' ),
							),
						),
						'arrow_size'          => array(
							'type'       => 'unit',
							'label'      => __( 'Size', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .swiper-button-prev, .pp-content-ticker-container .swiper-button-next',
								'property' => 'font-size',
							),
						),
						'arrow_bgcolor'       => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .swiper-button-prev, .pp-content-ticker-container .swiper-button-next',
								'property' => 'background-color',
							),
						),
						'arrow_color'         => array(
							'type'        => 'color',
							'label'       => __( 'Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .swiper-button-prev, .pp-content-ticker-container .swiper-button-next',
								'property' => 'color',
							),
						),
						'arrow_bgcolor_hover' => array(
							'type'        => 'color',
							'label'       => __( 'Hover Background Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .swiper-button-prev:hover, .pp-content-ticker-container .swiper-button-next:hover',
								'property' => 'background-color',
							),
						),
						'arrow_color_hover'   => array(
							'type'        => 'color',
							'label'       => __( 'Hover Color', 'bb-powerpack' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => false,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .swiper-button-prev:hover, .pp-content-ticker-container .swiper-button-next:hover',
								'property' => 'color',
							),
						),
						'arrow_border'        => array(
							'type'       => 'border',
							'label'      => __( 'Border', 'bb-powerpack' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .swiper-button-prev, .pp-content-ticker-container .swiper-button-next',
								'property' => 'border',
							),
						),
						'arrow_spacing'       => array(
							'type'       => 'unit',
							'label'      => __( 'Spacing', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .swiper-button-prev',
								'property' => 'margin-right',
							),
						),
						'arrow_padding'       => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-content-ticker-container .pp-content-ticker-navigation',
								'property' => 'padding',
							),
						),
					),
				),
			),
		),
	)
);

FLBuilder::register_settings_form(
	'custom_ticker_items_form',
	array(
		'title' => __( 'Content Ticker Item', 'bb-powerpack' ),
		'tabs'  => array(
			'custom_general' => array(
				'title'    => __( 'Content Ticker Item', 'bb-powerpack' ),
				'sections' => array(
					'general_section' => array(
						'title'  => '',
						'fields' => array(
							'item_text'  => array(
								'type'        => 'text',
								'label'       => __( 'Text', 'bb-powerpack' ),
								'connections' => array( 'string' ),
							),
							'item_image' => array(
								'type'        => 'photo',
								'label'       => __( 'Image', 'bb-powerpack' ),
								'show_remove' => true,
								'className'   => 'pp-module--photo-size-hidden',
								'connections' => array( 'photo' ),
							),
							'link'       => array(
								'type'          => 'link',
								'label'         => __( 'Link', 'bb-powerpack' ),
								'show_target'   => true,
								'show_nofollow' => true,
								'connections'   => array( 'url' ),
							),
						),
					),
				),
			),
		),
	)
);
