<?php

/**
 *
 * @class PPAdvancedMenu
 */
class PPAdvancedMenu extends FLBuilderModule {
	/**
	 * @property $pp_page_id
	 */
	public static $pp_page_id;

    /**
     * Parent class constructor.
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Advanced Menu', 'bb-powerpack'),
            'description'   => __('A module for advanced menu.', 'bb-powerpack'),
			'group'         => pp_get_modules_group(),
			'category'      => pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-advanced-menu/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-advanced-menu/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));

		// Actions
		add_action( 'pre_get_posts', array( $this, 'set_pre_get_posts_query' ), 10, 2 );

		// Filters
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'menu_woo_cart_ajax_fragments' ) );
		}
    }

	public function enqueue_icon_styles() {
		$enqueue = false;
		$settings = $this->settings;

		if ( isset( $settings->show_search ) && 'yes' === $settings->show_search ) {
			$enqueue = true;
		}
		if ( isset( $settings->show_woo_cart ) && 'yes' === $settings->show_woo_cart ) {
			$enqueue = true;
		}
		if ( isset( $settings->custom_toggle_icon ) && ! empty( $settings->custom_toggle_icon ) ) {
			$enqueue = true;
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function filter_settings( $settings, $helper ) {
		// Handle old link padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'menu_link_padding', 'padding' );
		
		// Handle old submenu link padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'submenu_link_padding', 'padding' );

		// Handle old responsive link padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'responsive_link_padding', 'padding' );
		
		// Handle old responsive overlay padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'responsive_overlay_padding', 'padding' );

		// Handle old responsive link border width field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'responsive_link_border_width', 'dimension' );

		// Handle old border fields.
		if ( isset( $settings->border_size ) && isset( $settings->border_color ) ) {
			if ( empty( $settings->border_color ) ) {
				$settings->border_size = array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => ''
				);
			}
		}

		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'border_size' => array(
				'type' => 'width'
			),
			'border_style' => array(
				'type' => 'style'
			),
			'border_color' => array(
				'type' => 'color'
			),
		), 'border' );

		// Handle old submenu border field.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'submenu_border_width'	=> array(
				'type'					=> 'width'
			),
			'submenu_box_border_color'	=> array(
				'type'						=> 'color'
			),
			'submenu_box_shadow'	=> array(
				'type'					=> 'shadow',
				'condition'				=> ( isset( $settings->submenu_box_shadow_display ) && 'yes' == $settings->submenu_box_shadow_display )
			),
			'submenu_box_shadow_color'	=> array(
				'type'				=> 'shadow_color',
				'condition'			=> ( isset( $settings->submenu_box_shadow_display ) && 'yes' == $settings->submenu_box_shadow_display ),
				'opacity'			=> ( isset( $settings->submenu_box_shadow_opacity ) && ! empty( $settings->submenu_box_shadow_opacity ) ) ? ( $settings->submenu_box_shadow_opacity / 100 ) : 1
			)
		), 'submenu_container_border' );

		// Handle old link typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'link_font_family'	=> array(
				'type'				=> 'font'
			),
			'link_font_size_custom'	=> array(
				'type'				=> 'font_size',
				'condition'			=> ( isset( $settings->link_font_size ) && 'custom' == $settings->link_font_size )
			),
			'link_line_height_custom'	=> array(
				'type'					=> 'line_height',
				'condition'				=> ( isset( $settings->link_line_height ) && 'custom' == $settings->link_line_height )
			),
			'link_text_transform'	=> array(
				'type'					=> 'text_transform'
			)
		), 'link_typography' );

		// Handle old submenu typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'submenu_font_family'	=> array(
				'type'				=> 'font'
			),
			'submenu_font_size_custom'	=> array(
				'type'				=> 'font_size',
				'condition'			=> ( isset( $settings->submenu_font_size ) && 'custom' == $settings->submenu_font_size )
			),
			'submenu_line_height_custom'	=> array(
				'type'					=> 'line_height',
				'condition'				=> ( isset( $settings->submenu_line_height ) && 'custom' == $settings->submenu_line_height )
			),
			'submenu_text_transform'	=> array(
				'type'					=> 'text_transform'
			)
		), 'submenu_typography' );

		return $settings;
	}

	public static function _get_menus() {
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

	public function render_toggle_button() {
		$settings = $this->settings;
		$toggle = $this->settings->mobile_toggle;
		$menu_text = empty( $this->settings->custom_menu_text ) ? __( 'Menu', 'bb-powerpack' ) : $this->settings->custom_menu_text;

		if ( isset( $toggle ) && $toggle != 'expanded' ) {
			?>
			<div class="pp-advanced-menu-mobile">
			<button class="pp-advanced-menu-mobile-toggle <?php echo esc_attr( $toggle ); ?>" tabindex="0" aria-label="<?php echo esc_attr( $menu_text ); ?>" aria-expanded="false">
				<?php
				$inner_html = '';

				if ( in_array( $toggle, array( 'hamburger', 'hamburger-label' ) ) ) {

					if ( isset( $settings->custom_toggle_icon ) && ! empty( $settings->custom_toggle_icon ) ) {
						$inner_html .= '<i class="pp-advanced-menu-mobile-toggle-icon ' . $settings->custom_toggle_icon . '"></i>';
					} else {
						$inner_html .= '<div class="pp-hamburger">';
						$inner_html .= '<div class="pp-hamburger-box">';
						$inner_html .= '<div class="pp-hamburger-inner"></div>';
						$inner_html .= '</div>';
						$inner_html .= '</div>';
					}

					if ( $toggle == 'hamburger-label' ) {
						$inner_html .= '<span class="pp-advanced-menu-mobile-toggle-label">' . $menu_text . '</span>';
					}

				} elseif ( $toggle == 'text' ) {
					$inner_html .= '<span class="pp-advanced-menu-mobile-toggle-label">'. $menu_text .'</span>';
				}

				$inner_html = apply_filters( 'pp_advanced_menu_html_toggle', $inner_html, $this->settings );

				echo $inner_html;
			?>
			</button>
			</div>
			<?php
		}
	}

	public function set_pre_get_posts_query( $query ) {
		if ( ! is_admin() && $query->is_main_query() ) {

			if ( $query->queried_object_id ) {

				self::$pp_page_id = $query->queried_object_id;

			// Fix when menu module is rendered via hook
			} elseif ( isset( $query->query_vars['page_id'] ) && 0 != $query->query_vars['page_id'] ) {

				self::$pp_page_id = $query->query_vars['page_id'];

			}
		}
	}

	public function sort_nav_objects( $sorted_menu_items, $args ) {
		$menu_items   = array();
		$parent_items = array();
		foreach ( $sorted_menu_items as $key => $menu_item ) {
			$classes = (array) $menu_item->classes;

			// Setup classes for current menu item.
			if ( $menu_item->ID == self::$pp_page_id || self::$pp_page_id == $menu_item->object_id ) {
				$parent_items[ $menu_item->object_id ] = $menu_item->menu_item_parent;

				if ( ! in_array( 'current-menu-item', $classes ) ) {
					$classes[] = 'current-menu-item';

					if ( 'page' == $menu_item->object ) {
						$classes[] = 'current_page_item';
					}
				}
			}
			$menu_item->classes = $classes;
			$menu_items[ $key ] = $menu_item;
		}

		// Setup classes for parent's current item.
		foreach ( $menu_items as $key => $sorted_item ) {
			if ( in_array( $sorted_item->db_id, $parent_items ) && ! in_array( 'current-menu-parent', (array) $sorted_item->classes ) ) {
				$menu_items[ $key ]->classes[] = 'current-menu-ancestor';
				$menu_items[ $key ]->classes[] = 'current-menu-parent';
			}
		}

		return $menu_items;
	}

	public function get_menu_label() {
		$label = __( 'Menu', 'bb-powerpack' );

		if ( isset( $this->settings ) && isset( $this->settings->menu_name ) && ! empty( $this->settings->menu_name ) ) {
			$label = apply_filters( 'pp_nav_menu_label', $this->settings->menu_name, $this->settings );
		}

		return esc_attr( $label );
	}

	public function get_media_breakpoint() {
		$global_settings = FLBuilderModel::get_global_settings();
		$media_width = $global_settings->responsive_breakpoint;
		$mobile_breakpoint = $this->settings->mobile_breakpoint;

		if ( isset( $mobile_breakpoint ) && 'expanded' != $this->settings->mobile_toggle ) {
			if ( 'medium-mobile' == $mobile_breakpoint ) {
				$media_width = $global_settings->medium_breakpoint;
			} elseif ( 'mobile' == $this->settings->mobile_breakpoint ) {
				$media_width = $global_settings->responsive_breakpoint;
			} elseif ( 'always' == $this->settings->mobile_breakpoint ) {
				$media_width = 'always';
			} elseif ( 'custom' == $this->settings->mobile_breakpoint ) {
				$media_width = (int) $this->settings->custom_breakpoint;
			}
		}

		return $media_width;
	}

	public function filter_nav_menu_items( $items ) {
		$settings = $this->settings;

		if ( isset( $settings->show_woo_cart ) && 'yes' == $settings->show_woo_cart ) {
			$items = $this->render_menu_woo_cart( $items );
		}

		if ( isset( $settings->show_search ) && 'yes' == $settings->show_search ) {
			$items = $this->render_menu_search( $items );
		}

		return $items;
	}

	/**
	 * Add Woo cart to menu.
	 *
	 * @return string
	 */
	public function render_menu_woo_cart( $items ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $items;
		}

		// Bail out if no data to load.
		if ( empty( WC()->cart ) ) {
			return $items;
		}

		$settings = $this->settings;
		$classes  = 'menu-item pp-menu-cart-item';

		$show_on_checkout = isset( $settings->woo_cart_on_checkout ) && 'yes' == $settings->woo_cart_on_checkout;

		if ( ! $show_on_checkout && ( is_checkout() || is_cart() ) ) {
			$classes .= ' pp-menu-cart-item-hidden';
		}

		ob_start();
		do_action( 'pp_advanced_menu_before_woo_cart_content', $settings );
		echo $this->menu_woo_cart_content();
		do_action( 'pp_advanced_menu_after_woo_cart_content', $settings );
		$cart_content = ob_get_clean();

		$menu_cart_content = $cart_content;
		$menu_item_li      = "<li class='$classes'>$menu_cart_content</li>";
		$items            .= $menu_item_li;

		return $items;
	}

	public function render_menu_search( $items ) {
		$settings = $this->menu_search_settings();

		if ( class_exists( 'PPSearchFormModule' ) ) {
			ob_start();
			?>
			<a href="javascript:void(0)" role="button" aria-label="<?php _e( 'Search', 'bb-powerpack' ); ?>">
				<span class="menu-item-text"><i class="<?php echo $this->settings->search_icon; ?>" aria-hidden="true"></i></span>
			</a>
			<?php
			FLBuilder::render_module_html( 'pp-search-form', $settings );
			$search_content = ob_get_clean();
		} else {
			$modules_manager = BB_PowerPack_Admin_Settings::get_form_action( '&tab=modules' );
			// translators: %s denotes anchor tag attributes
			$search_content = sprintf( __( '<a%s>Click here</a> to enable Search Form module.', 'bb-powerpack' ), ' href="' . $modules_manager . '" target="_blank"' );
		}

		$items .= "<li class='menu-item pp-menu-search-item'>$search_content</li>";

		return $items;
	}

	public function menu_search_settings() {
		$search_module_settings = array(
			'style'       => 'minimal',
			'placeholder' => esc_attr( $this->settings->search_placeholder ),
			'size'        => esc_attr( $this->settings->search_container_size ),
			'input_icon'  => '',
			'icon'        => '',
			'toggle_icon' => '',
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, 'input_' ) ) {
				$search_module_settings[ $key ] = $value;
			}
		}

		return apply_filters( 'pp_advanced_menu_search_settings', $search_module_settings, $this->settings );
	}

	/**
	 * Enable Woo ajax cart.
	 *
	 * @return array
	 */
	public function menu_woo_cart_ajax_fragments( $fragments ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '9.3', '<=' ) && ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}

		$menu_fragment = $this->menu_woo_cart_content();
		if ( ! empty( $menu_fragment ) ) {
			$fragments['a.pp-menu-cart-contents'] = $menu_fragment;
		}

		return $fragments;
	}

	public function menu_woo_cart_content() {
		$cart_count   = WC()->cart->get_cart_contents_count();
		$settings     = null;
		$item_content = '';

		if ( 0 == $cart_count ) {
			$menu_item_args = apply_filters( 'pp_advanced_menu_woo_empty_cart_menu_item', array(
				'title' => __( 'Start shopping', 'bb-powerpack' ),
				'url'   => wc_get_page_permalink( 'shop' ),
			) );

			$cart_url          = $menu_item_args['url'];
			$menu_item_title   = $menu_item_args['title'];
			$menu_item_classes = 'pp-menu-cart-contents empty-pp-menu-cart-visible';
		} else {
			$menu_item_args = apply_filters( 'pp_advanced_menu_woo_cart_menu_item', array(
				'title' => __( 'View your shopping cart', 'bb-powerpack' ),
				'url'   => wc_get_cart_url(),
			) );

			$cart_url          = $menu_item_args['url'];
			$menu_item_title   = $menu_item_args['title'];
			$menu_item_classes = 'pp-menu-cart-contents';
		}

		if ( isset( $this->settings ) ) {
			$settings = $this->settings;
		} elseif ( $_REQUEST && isset( $_REQUEST['pp-advanced-menu-node'] ) ) {
			$menu_node = wp_unslash( $_REQUEST['pp-advanced-menu-node'] );
			$post_id   = (int) wp_unslash( $_REQUEST['post-id'] );

			$data = FLBuilderModel::get_layout_data( 'published', $post_id );
			if ( isset( $data[ $menu_node ] ) ) {
				$module = $data[ $menu_node ];

				if ( $module && isset( $module->settings->show_woo_cart ) && 'yes' == $module->settings->show_woo_cart ) {
					$settings = $module->settings;
				}
			}
		}

		if ( $settings ) {
			$display_type = isset( $settings->woo_cart_display_type ) ? $settings->woo_cart_display_type : 'count';
			/* translators: %d: item count */
			$items_count  = sprintf( _n( '%d item', '%d items', $cart_count, 'bb-powerpack' ), $cart_count );
			$cart_total   = $this->get_woo_cart_total();
			$cart_content = '<span class="pp-menu-cart-count">' . $items_count . '</span>';
			$icon         = '';

			if ( isset( $settings->woo_cart_icon ) && ! empty( $settings->woo_cart_icon ) ) {
				$icon = '<i class="pp-menu-cart-icon ' . $settings->woo_cart_icon . '" role="img" aria-label="' . __( 'Cart', 'bb-powerpack' ) . '"></i>';
			}

			if ( in_array( $display_type, array( 'total', 'count-total' ) ) ) {
				$total_content = '<span class="pp-menu-cart-total">' . $cart_total . '</span>';
				if ( 'count-total' == $display_type ) {
					$cart_content .= ' &ndash; ' . $total_content;
				} else {
					$cart_content = $total_content;
				}
			}

			$menu_item_classes .= ' pp-menu-cart-type-' . $display_type;

			$item_content  = '<a class="' . $menu_item_classes . '" href="' . $cart_url . '" title="' . $menu_item_title . '">';
			$item_content .= $icon . $cart_content;
			$item_content .= '</a>';
		}

		return $item_content;
	}

	/**
	 * Get Woo cart total price.
	 */
	public function get_woo_cart_total() {
		$cart_total_type     = 'subtotal'; // subtotal | checkout_total
		$cart_contents_total = 0;
		if ( 'subtotal' == $cart_total_type ) {
			if ( WC()->cart->display_prices_including_tax() ) {
				$cart_contents_total = wc_price( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );
			} else {
				$cart_contents_total = wc_price( WC()->cart->get_subtotal() );
			}
		} elseif ( 'checkout_total' == $cart_total_type ) {
			$cart_contents_total = wc_price( WC()->cart->get_total( 'edit' ) );
		} else {
			if ( WC()->cart->display_prices_including_tax() ) {
				$cart_contents_total = wc_price( WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() );
			} else {
				$cart_contents_total = wc_price( WC()->cart->get_cart_contents_total() );
			}
		}

		return $cart_contents_total;
	}

	public function render_nav( $node_id = '' ) {
		$settings = $this->settings;

		do_action( 'pp_advanced_menu_before', $settings->mobile_menu_type, $settings, $node_id );

		if ( ! empty( $settings->wp_menu ) ) {

			if ( isset( $settings->menu_layout ) ) {
				if ( in_array( $settings->menu_layout, array( 'vertical', 'horizontal' ) ) && isset( $settings->submenu_hover_toggle ) ) {
					$toggle = ' pp-toggle-'. esc_attr( $settings->submenu_hover_toggle );
				} elseif ( $settings->menu_layout == 'accordion' && isset( $settings->submenu_click_toggle ) ) {
					$toggle = ' pp-toggle-'. esc_attr( $settings->submenu_click_toggle );
				} else {
					$toggle = ' pp-toggle-arrows';
				}
			} else {
				$toggle = ' pp-toggle-arrows';
			}

			$layout = isset( $settings->menu_layout ) ? 'pp-advanced-menu-'. esc_attr( $settings->menu_layout ) : 'pp-advanced-menu-horizontal';

			$defaults = array(
				'menu'			=> $settings->wp_menu,
				'container'		=> false,
				'menu_class'	=> 'menu '. $layout . $toggle,
				'walker'		=> new Advanced_Menu_Walker(),
				'item_spacing'  => 'discard',
			);

			add_filter( 'wp_nav_menu_' . $settings->wp_menu . '_items', array( $this, 'filter_nav_menu_items' ), 10 );
			add_filter( 'wp_nav_menu_objects', array( $this, 'sort_nav_objects' ), 10, 2 );

			wp_nav_menu( $defaults );

			remove_filter( 'wp_nav_menu_objects', array( $this, 'sort_nav_objects' ) );
			remove_filter( 'wp_nav_menu_' . $settings->wp_menu . '_items', array( $this, 'filter_nav_menu_items' ), 10 );
		}

		do_action( 'pp_advanced_menu_after', $settings->mobile_menu_type, $settings, $node_id );
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module('PPAdvancedMenu', array(
    'general'       => array( // Tab
        'title'         => __('General', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => '', // Section Title
                'fields'        => array( // Section Fields
					'wp_menu' => array(
						'type'          => 'select',
						'label'         => __( 'Menu', 'bb-powerpack' ),
						'helper'		=> __( 'Select a WordPress menu that you created in the admin under Appearance > Menus.', 'bb-powerpack' ),
						'options'		=> PPAdvancedMenu::_get_menus(),
					),
					'menu_name' => array(
						'label'   => __( 'Name', 'bb-powerpack' ),
						'type'    => 'text',
						'help'    => __( 'This is used as the aria attribute for accessibility and is not visible on frontend.', 'bb-powerpack' ),
						'default' => __( 'Menu', 'bb-powerpack' ),
					),
					'menu_layout' => array(
					    'type'          => 'pp-switch',
					    'label'         => __( 'Layout', 'bb-powerpack' ),
					    'default'       => 'horizontal',
					    'options'       => array(
					    	'horizontal'	=> __( 'Horizontal', 'bb-powerpack' ),
					    	'vertical'		=> __( 'Vertical', 'bb-powerpack' ),
					    	'accordion'		=> __( 'Accordion', 'bb-powerpack' ),
					    	'expanded'		=> __( 'Expanded', 'bb-powerpack' ),
					    ),
					    'toggle'		=> array(
					    	'horizontal'	=> array(
					    		'fields'		=> array( 'submenu_hover_toggle', 'menu_align' ),
					    	),
					    	'vertical'		=> array(
					    		'fields'		=> array( 'submenu_hover_toggle' ),
					    	),
					    	'accordion'		=> array(
					    		'fields'		=> array( 'submenu_click_toggle', 'collapse' ),
					    	),
					    )
					),
					'submenu_hover_toggle' => array(
					    'type'          => 'pp-switch',
					    'label'         => __( 'Submenu Icon', 'bb-powerpack' ),
					    'default'       => 'arrows',
					    'options'       => array(
					    	'arrows'		=> __( 'Arrows', 'bb-powerpack' ),
					    	'plus'			=> __( 'Plus Sign', 'bb-powerpack' ),
					    	'none'			=> __( 'None', 'bb-powerpack' ),
					    )
					),
					'submenu_click_toggle' => array(
					    'type'          => 'pp-switch',
					    'label'         => __( 'Submenu Icon click', 'bb-powerpack' ),
					    'default'       => 'arrows',
					    'options'       => array(
					    	'arrows'		=> __( 'Arrows', 'bb-powerpack' ),
					    	'plus'			=> __( 'Plus Sign', 'bb-powerpack' ),
					    )
					),
					'collapse'   => array(
						'type'          => 'pp-switch',
						'label'         => __('Collapse Inactive', 'bb-powerpack'),
						'default'       => '1',
						'options'       => array(
							'1'             => __('Yes', 'bb-powerpack'),
							'0'             => __('No', 'bb-powerpack')
						),
						'help'          => __('Choosing yes will keep only one item open at a time. Choosing no will allow multiple items to be open at the same time.', 'bb-powerpack'),
						'preview'       => array(
							'type'          => 'none'
						)
					),
                )
            ),
			'search'	=> array(
				'title'		=> __( 'Search', 'bb-powerpack' ),
				'collapsed' => true,
				'fields'	=> array(
					'show_search' => array(
						'type'	  => 'pp-switch',
						'label'	  => __( 'Show Search', 'bb-powerpack' ),
						'default' => 'no',
						'toggle'  => array(
							'yes'	=> array(
								'sections' => array( 'search_style' ),
								'fields' => array( 'search_icon', 'search_placeholder' ),
							),
						),
					),
					'search_icon' => array(
						'type' => 'icon',
						'label' => __( 'Icon', 'bb-powerpack' ),
						'default' => 'fas fa-search',
						'show_remove' => true,
					),
					'search_placeholder'	=> array(
						'type'			=> 'text',
						'label'			=> __('Placeholder', 'bb-powerpack'),
						'default'		=> __('Search', 'bb-powerpack'),
						'connections'	=> array('string'),
					),
				),
			),
			'woo_cart' => class_exists( 'WooCommerce' ) ? array(
				'title'	    => __( 'WooCommerce', 'bb-powerpack' ),
				'collapsed' => true,
				'fields'    => array(
					'show_woo_cart' => array(
						'type'	  => 'pp-switch',
						'label'	  => __( 'Show Cart', 'bb-powerpack' ),
						'default' => 'no',
						'toggle'  => array(
							'yes'	=> array(
								'sections' => array( 'woo_cart_style' ),
								'fields' => array( 'woo_cart_icon', 'woo_cart_on_checkout', 'woo_cart_display_type' ),
							),
						),
					),
					'woo_cart_icon' => array(
						'type'        => 'icon',
						'label'       => __( 'Icon', 'bb-powerpack' ),
						'default'     => 'fas fa-shopping-cart',
						'show_remove' => true,
					),
					'woo_cart_on_checkout' => array(
						'type'	  => 'pp-switch',
						'label'	  => __( 'Show on Checkout', 'bb-powerpack' ),
						'default' => 'no',
					),
					'woo_cart_display_type'       => array(
						'type'    => 'select',
						'label'   => __( 'Display Type', 'bb-powerpack' ),
						'default' => 'count',
						'options' => array(
							'count'       => __( 'Items Count', 'bb-powerpack' ),
							'total'       => __( 'Total Amount', 'bb-powerpack' ),
							'count-total' => __( 'Items Count and Total Amount', 'bb-powerpack' ),
						),
					),
				),
			) : array(),
			'mobile'       => array(
				'title'         => __( 'Responsive', 'bb-powerpack' ),
				'collapsed' 	=> true,
				'fields'        => array(
                    'mobile_breakpoint' => array(
                        'type'          => 'select',
                        'label'         => __( 'Responsive Breakpoint', 'bb-powerpack' ),
                        'default'       => 'mobile',
                        'options'       => array(
                            'always'		=> __( 'Always', 'bb-powerpack' ),
                            'medium-mobile'	=> __( 'Medium & Small Devices Only', 'bb-powerpack' ),
                            'mobile'		=> __( 'Small Devices Only', 'bb-powerpack' ),
                            'custom'		=> __( 'Custom', 'bb-powerpack' ),
                        ),
                        'toggle'	=> array(
                            'custom'	=> array(
                                'fields'	=> array('custom_breakpoint')
                                )
                        )
                    ),
                    'custom_breakpoint'	=> array(
                        'type'				=> 'text',
                        'label'             => __('Custom Breakpoint', 'bb-powerpack'),
                        'default'       	=> '768',
                        'description'       => 'px',
                        'size'              => 5
                    ),
					'mobile_toggle' => array(
					    'type'          => 'select',
					    'label'         => __( 'Responsive Toggle', 'bb-powerpack' ),
					    'default'       => 'hamburger',
					    'options'       => array(
					    	'hamburger'			=> __( 'Hamburger Icon', 'bb-powerpack' ),
					    	'hamburger-label'	=> __( 'Hamburger Icon + Label', 'bb-powerpack' ),
					    	'text'				=> __( 'Menu Button', 'bb-powerpack' ),
					    	'expanded'			=> __( 'None', 'bb-powerpack' ),
					    ),
					    'toggle'		=> array(
					    	'hamburger'	=> array(
					    		'fields'		=> array( 'mobile_menu_type', 'mobile_breakpoint', 'mobile_toggle_size', 'mobile_toggle_thickness', 'menu_position', 'custom_toggle_icon' ),
								'sections'		=> array('mobile_toggle_typography', 'mobile_toggle_style'),
					    	),
					    	'hamburger-label'	=> array(
					    		'fields'		=> array( 'mobile_menu_type', 'mobile_breakpoint', 'custom_menu_text', 'mobile_toggle_font', 'mobile_toggle_size', 'mobile_toggle_thickness', 'menu_position', 'custom_toggle_icon' ),
								'sections'		=> array('mobile_toggle_typography', 'mobile_toggle_style'),
					    	),
					    	'text'	=> array(
					    		'fields'		=> array( 'mobile_menu_type', 'mobile_breakpoint', 'custom_menu_text', 'mobile_toggle_font', 'menu_position' ),
								'sections'		=> array('mobile_toggle_typography', 'mobile_toggle_style'),
					    	),
					    )
					),
					'custom_toggle_icon' => array(
						'type'        => 'icon',
						'label'       => __( 'Custom Toggle Icon', 'bb-powerpack' ),
						'show_remove' => true
					),
					'custom_menu_text'	=> array(
						'type'				=> 'text',
						'label'				=> __( 'Custom Menu Toggle Text', 'bb-powerpack' ),
						'default'			=> __('Menu', 'bb-powerpack'),
						'preview'			=> array(
							'type'			=> 'text',
							'selector'		=> '.pp-advanced-menu-mobile-toggle-label',
						),
						'connections'		=> array('string')
					),
					'menu_position' => array(
						'type'	=> 'pp-switch',
						'label'	=> __( 'Menu Position', 'bb-powerpack' ),
						'default' => 'below',
						'options' => array(
							'inline' => __( 'Inline', 'bb-powerpack' ),
							'below'  => __( 'Below Row', 'bb-powerpack' ),
						),
					),
					'mobile_menu_type'	=> array(
						'type'          => 'select',
					    'label'         => __( 'Menu Type', 'bb-powerpack' ),
					    'default'       => 'default',
					    'options'       => array(
					    	'default'		=> __( 'Default', 'bb-powerpack' ),
					    	'off-canvas'	=> __( 'Off Canvas', 'bb-powerpack' ),
					    	'full-screen'	=> __( 'Full Screen Overlay', 'bb-powerpack' ),
					    ),
						'toggle'	=> array(
							'off-canvas'	=> array(
                                'sections'      => array('menu_shadow', 'close_icon'),
								'fields'	    => array( 'offcanvas_direction', 'offcanvas_width', 'animation_speed', 'responsive_overlay_bg_color', 'responsive_overlay_bg_opacity', 'responsive_overlay_padding', 'close_icon_size', 'close_icon_color', 'responsive_alignment_vertical' )
							),
							'full-screen'	=> array(
								'sections'	=> array('close_icon'),
								'fields'	    => array( 'full_screen_effects', 'animation_speed', 'responsive_overlay_bg_color', 'responsive_overlay_bg_opacity', 'responsive_overlay_padding', 'close_icon_size', 'close_icon_color'  )
							)
						)
					),
					'full_screen_effects'	=> array(
						'type'          => 'select',
					    'label'         => __( 'Full Screen Effects', 'bb-powerpack' ),
					    'default'       => 'fade',
					    'options'       => array(
					    	'fade'			=> __( 'Fade', 'bb-powerpack' ),
					    	'corner'		=> __( 'Corner', 'bb-powerpack' ),
					    	'slide-down'	=> __( 'Slide Down', 'bb-powerpack' ),
					    	'scale'			=> __( 'Zoom', 'bb-powerpack' ),
					    	'door'			=> __( 'Door', 'bb-powerpack' ),
					    ),
					),
					'offcanvas_direction'	=> array(
						'type'          => 'select',
					    'label'         => __( 'Off Canvas Direction', 'bb-powerpack' ),
					    'default'       => 'left',
					    'options'       => array(
					    	'left'			=> __( 'From Left', 'bb-powerpack' ),
					    	'right'			=> __( 'From Right', 'bb-powerpack' ),
					    ),
					),
					'offcanvas_width'	=> array(
						'type'	=> 'unit',
						'label'	=> __( 'Off Canvas Width', 'bb-powerpack' ),
						'default' => '',
						'units'	=> array('px', 'vw'),
						'slider'	=> array(
							'min'		=> 100,
							'max'		=> 500,
							'step'		=> 10
						),
						'responsive' => true,
					),
					'animation_speed'   => array(
                        'type'              => 'text',
                        'label'             => __('Animation Speed', 'bb-powerpack'),
                        'default'       	=> 500,
                        'description'       => __('ms', 'bb-powerpack'),
                        'size'              => 5
                    ),
				)
			),
        )
    ),
    'style'       => array( // Tab
		'title'         => __('Style', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => '', // Section Title
                'fields'        => array( // Section Fields
                    'alignment'    => array(
                        'type'          => 'align',
                        'label'         => __('Alignment', 'bb-powerpack'),
						'default'       => 'center',
						'responsive'	=> true,
                    ),
                    'spacing'    => array(
						'type' 			=> 'unit',
						'label' 		=> __('Horizontal Spacing', 'bb-powerpack'),
						'placeholder'   => '10',
						'units' 		=> array('px'),
						'slider'		=> true,
                        'help'          => __( 'This option controls the left-right spacing of each link.', 'bb-powerpack' ),
						'responsive' => array(
							'placeholder' => array(
								'default' => '10',
								'medium' => '',
								'responsive' => '',
							),
						),
                    ),
                    'link_bottom_spacing'    => array(
						'type' 			=> 'unit',
						'label' 		=> __('Vertical Spacing', 'bb-powerpack'),
						'units' 		=> array('px'),
						'slider'		=> true,
                        'help'          => __( 'This option controls the top-bottom spacing of each link.', 'bb-powerpack' ),
						'preview' 		=> array(
							'type' 			=> 'css',
							'selector'		=> '.pp-advanced-menu .menu > li',
							'property'		=> 'margin-bottom',
							'unit' 			=> 'px'
						),
						'responsive' => true,
					),
					'menu_link_padding'		=> array(
						'type'			=> 'dimension',
						'label'			=> __('Link Padding', 'bb-powerpack'),
						'default'		=> 10,
						'slider'		=> true,
						'units'			=> array('px'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-advanced-menu .menu > li > a, .pp-advanced-menu .menu > li > .pp-has-submenu-container > a',
							'property'      => 'padding',
							'unit'          => 'px'
						)
					),
					// 'submenu_arrow_pos' => array(
					// 	'type' 			=> 'unit',
					// 	'label' 		=> __('Submenu Indicator Spacing', 'bb-powerpack'),
					// 	'units' 		=> array('px'),
					// 	'slider'		=> true,
                    //     'help'          => __( 'This option is given to set toggle indicator horizontal position. It might NOT useful in some cases.', 'bb-powerpack' ),
					// ),
                )
            ),
            'color_settings'       => array( // Section
                'title'         => __('Colors', 'bb-powerpack'), // Section Title
				'collapsed' 	=> true,
                'fields'        => array( // Section Fields
                    'link_color' => array(
                        'type'       => 'color',
                        'label'      => __('Link Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'          => 'css',
							'rules'			=> array(
								array(
									'selector'        => '.pp-advanced-menu .menu > li > a, .pp-advanced-menu .menu > li > .pp-has-submenu-container > a',
									'property'        => 'color',
								),
								array(
									'selector'        => '.pp-advanced-menu-mobile-toggle rect',
									'property'        => 'fill',
								),
								array(
									'selector'        => '.pp-advanced-menu .pp-wp-toggle-arrows .pp-menu-toggle:before, .pp-advanced-menu .pp-toggle-none .pp-menu-toggle:before, .pp-advanced-menu .pp-toggle-plus .pp-menu-toggle:before, .pp-advanced-menu .pp-toggle-plus .pp-menu-toggle:after',
									'property'        => 'border-color',
								)
							)
						)
                    ),
                    'link_hover_color' => array(
                        'type'       => 'color',
                        'label'      => __('Link Hover Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'          => 'css',
							'rules'			=> array(
								array(
									'selector'        => '.menu > li > a:hover, .menu > li > a:focus, .menu > li > .pp-has-submenu-container:hover > a, .menu > li > .pp-has-submenu-container.focus > a, .menu > li.current-menu-item > a, .menu > li.current-menu-item > .pp-has-submenu-container > a',
									'property'        => 'color',
								),
								array(
									'selector'        => '.pp-advanced-menu .pp-wp-toggle-arrows li:hover .pp-menu-toggle:before, .pp-advanced-menu .pp-toggle-none li:hover .pp-menu-toggle:before, .pp-advanced-menu .pp-toggle-plus li:hover .pp-menu-toggle:before, .pp-advanced-menu .pp-toggle-plus li:hover .pp-menu-toggle:after',
									'property'        => 'border-color',
								)
							)
						)
                    ),
                    'background_color' => array(
                        'type'       => 'color',
                        'label'      => __('Background Color', 'bb-powerpack'),
                        'default'    => '',
                        'show_reset' => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-advanced-menu .menu > li > a, .pp-advanced-menu .menu > li > .pp-has-submenu-container > a',
							'property'        => 'background-color',
						)
                    ),
                    'background_hover_color' => array(
                        'type'       => 'color',
                        'label'      => __('Background Hover Color', 'bb-powerpack'),
                        'default'    => '',
                        'show_reset' => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.menu > li > a:hover, .menu > li > a:focus, .menu > li > .pp-has-submenu-container:hover > a, .menu > li > .pp-has-submenu-container.focus > a, .menu > li.current-menu-item > a, .menu > li.current-menu-item > .pp-has-submenu-container > a',
							'property'        => 'background-color',
						)
                    ),
                )
            ),
            'border_settings'       => array( // Section
                'title'         => __('Border', 'bb-powerpack'), // Section Title
				'collapsed' 	=> true,
                'fields'        => array( // Section Fields
					'border' => array(
						'type'     => 'border',
						'label'    => __( 'Border', 'bb-powerpack' ),
						'preview'   => array(
							'type'     => 'css',
							'selector' => '.pp-advanced-menu .menu > li > a, .pp-advanced-menu .menu > li > .pp-has-submenu-container > a',
						)
					),
					'border_hover_color' => array(
                        'type'       => 'color',
                        'label'      => __('Border Hover Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'none',
						)
                    ),
                )
            ),
			'submenu_style'	=> array(
				'title'		=> __( 'Sub Menu', 'bb-powerpack' ),
				'collapsed' => true,
				'fields'	=> array(
					'submenu_width'		=> array(
						'type'				=> 'unit',
						'label'				=> __('Width', 'bb-powerpack'),
						'default'			=> '220',
						'slider'			=> true,
						'units'				=> array('px'),
						'help'				=> __('Width of sub-menu for desktop. Default width is 220px.', 'bb-powerpack')
					),
					'submenu_width_as_min' => array(
						'type'					=> 'pp-switch',
						'label'					=> __( 'Apply as min-width', 'bb-powerpack' ),
						'default'				=> 'no',
						'help'					=> __( 'It will apply the width as min-width in CSS. Useful in alignment issues.', 'bb-powerpack' ),
					),
					'submenu_spacing' => array(
						'type'          => 'unit',
						'label'         => __( 'Spacing', 'bb-powerpack' ),
						'default'       => '0',
						'slider'		=> true,
						'units'			=> array('px'),
						'preview'      	=> array(
							'type'         	=> 'css',
							'selector'		=> '.pp-advanced-menu ul.sub-menu',
							'property'		=> 'padding',
							'unit'			=> 'px'
						),
					),
					'submenu_container_border'	=> array(
						'type'				=> 'border',
						'label'				=> __('Container Border', 'bb-powerpack'),
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-advanced-menu .sub-menu'
						)
					),
					'submenu_container_bg_color' => array(
                        'type'       => 'color',
                        'label'      => __('Container Background Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-advanced-menu ul.sub-menu',
							'property'        => 'background-color',
						),
						'help'		=> __('You can set sub-menu container background color if you are using mega menu.', 'bb-powerpack')
                    ),
					'submenu_background_color' => array(
                        'type'       	=> 'color',
                        'label'      	=> __('Link Background Color', 'bb-powerpack'),
                        'default'    	=> '',
						'show_reset' 	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-advanced-menu .sub-menu > li > a, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a',
							'property'        => 'background-color',
						)
                    ),
                    'submenu_background_hover_color' => array(
                        'type'       	=> 'color',
                        'label'      	=> __('Link Background Hover Color', 'bb-powerpack'),
                        'default'    	=> '',
						'show_reset' 	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-advanced-menu .sub-menu > li > a:hover, .sub-menu > li > a:focus, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a:hover, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a:focus',
							'property'        => 'background-color',
						)
                    ),
					'submenu_link_color' => array(
                        'type'       => 'color',
                        'label'      => __('Link Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-advanced-menu .sub-menu > li > a, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a',
							'property'        => 'color',
						)
                    ),
                    'submenu_link_hover_color' => array(
                        'type'       => 'color',
                        'label'      => __('Link Hover Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-advanced-menu .sub-menu > li > a:hover, .pp-advanced-menu .sub-menu > li > a:focus, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a:hover, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a:focus',
							'property'        => 'color',
						)
					),
					'submenu_link_padding'		=> array(
						'type'			=> 'dimension',
						'label'			=> __('Link Padding', 'bb-powerpack'),
						'default'		=> 10,
						'slider'		=> true,
						'units'			=> array('px'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-advanced-menu .sub-menu > li > a, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a',
							'property'      => 'padding',
							'unit'          => 'px'
						)
					),
					'submenu_border_style' => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Link Separator Style', 'bb-powerpack'),
                        'default'       => 'solid',
                        'options'       => array(
                            'solid'        => __('Solid', 'bb-powerpack'),
                            'dashed'       => __('Dashed', 'bb-powerpack'),
                            'double'       => __('Double', 'bb-powerpack'),
                            'dotted'       => __('Dotted', 'bb-powerpack'),
                        ),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-advanced-menu .sub-menu > li > a, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a',
							'property'        => 'border-style',
						)
                    ),
                    'submenu_border_size'    => array(
						'type' 					=> 'unit',
						'label' 				=> __('Link Separator Size', 'bb-powerpack'),
						'units' 				=> array('px'),
						'slider'				=> true,
						'preview'         		=> array(
							'type'            		=> 'css',
							'selector'        		=> '.pp-advanced-menu .sub-menu > li > a, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a',
							'property'        		=> 'border-bottom-width',
							'unit'			  		=> 'px'
						),
						'responsive' => true,
                    ),
                    'submenu_border_color' => array(
                        'type'       => 'color',
                        'label'      => __('Link Separator Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-advanced-menu .sub-menu > li > a, .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a',
							'property'        => 'border-color',
						)
                    ),
					'submenu_border_hover_color' => array(
                        'type'       => 'color',
                        'label'      => __('Link Separator Hover Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
                    ),
				)
			),
			'search_style' => array(
				'title' => __( 'Search', 'bb-powerpack' ),
				'collapsed' => true,
				'fields' => array(
					'input_bg_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Background Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form-wrap:not(.pp-search-form--style-full_screen) .pp-search-form__container:not(.pp-search-form--lightbox)',
							'property'		=> 'background-color'
						)
					),
					'input_focus_bg_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Background Focus Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form-wrap:not(.pp-search-form--style-full_screen) .pp-search-form--focus .pp-search-form__container:not(.pp-search-form--lightbox)',
							'property'		=> 'background-color'
						)
					),
					'input_placeholder_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Placeholder Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'none',
						)
					),
					'input_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Text Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__input',
							'property'		=> 'color'
						)
					),
					'input_focus_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Text Focus Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__input:focus',
							'property'		=> 'color'
						)
					),
					'input_border'	=> array(
						'type'			=> 'border',
						'label'			=> __('Border & Shadow', 'bb-powerpack'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__container:not(.pp-search-form--lightbox)'
						)
					),
					'input_focus_border_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Border Focus Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form--focus .pp-search-form__container:not(.pp-search-form--lightbox)',
							'property'		=> 'border-color'
						)
					),
					'search_container_size' => array(
						'type'			=> 'unit',
						'label'			=> __('Form Height', 'bb-powerpack'),
						'default'		=> '40',
						'slider'		=> true,
						'help'			=> __( 'This option controls the height and padding.', 'bb-powerpack' ),
					),
					'search_container_width' => array(
						'type'			=> 'unit',
						'label'			=> __('Form Width', 'bb-powerpack'),
						'default'		=> '400',
						'units'			=> array( 'px', '%' ),
						'responsive'	=> true,
						'slider'		=> true,
					),
				),
			),
			'woo_cart_style' => class_exists( 'WooCommerce' ) ? array(
				'title'	=> __( 'WooCommerce Cart', 'bb-powerpack' ),
				'collapsed' => true,
				'fields' => array(
					'woo_cart_bg_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Background Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-advanced-menu .menu li.pp-menu-cart-item a.pp-menu-cart-contents',
							'property'		=> 'background-color'
						)
					),
					'woo_cart_bg_hover_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Background Hover Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-advanced-menu .menu li.pp-menu-cart-item:hover a.pp-menu-cart-contents',
							'property'		=> 'background-color'
						)
					),
					'woo_cart_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Text Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-advanced-menu .menu li.pp-menu-cart-item a.pp-menu-cart-contents',
							'property'		=> 'color'
						)
					),
					'woo_cart_hover_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Text Hover Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-advanced-menu .menu li.pp-menu-cart-item:hover a.pp-menu-cart-contents:hover',
							'property'		=> 'color'
						)
					),
					'woo_cart_border'	=> array(
						'type'			=> 'border',
						'label'			=> __('Border', 'bb-powerpack'),
						'disabled'		=> array( 'default' => array( 'shadow' ) ),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-advanced-menu .menu li.pp-menu-cart-item a.pp-menu-cart-contents'
						)
					),
				),
			) : array(),
        )
    ),
	'responsive_style'	=> array(
		'title'	=> __('Responsive', 'bb-powerpack'),
		'description'	=> __( 'These settings are intended for <strong>responsive breakpoint only</strong>. Preview will not work on large or medium devices.', 'bb-powerpack' ),
		'sections'	=> array(
			'responsive_container'	=> array(
				'title'	=> __( 'Container', 'bb-powerpack' ),
				'fields'	=> array(
					'responsive_alignment'    => array(
                        'type'          => 'align',
                        'label'         => __('Content Horizontal Alignment', 'bb-powerpack'),
                        'default'       => 'center',
						'description'   => __( '^ This option will be removed in the next version. Please set responsive aligment through "Style" tab > Alignment.', 'bb-powerpack' ),
                    ),
                    'responsive_alignment_vertical' => array(
                        'type'  => 'pp-switch',
                        'label' => __('Content Vertical Alignment', 'bb-powerpack'),
                        'default'   => 'top',
                        'options'   => array(
                            'top'       => __('Top', 'bb-powerpack'),
                            'center'    => __('Center', 'bb-powerpack')
                        )
					),
					'responsive_toggle_alignment'	=> array(
						'type'          => 'pp-switch',
                        'label'         => __('Hamburger Icon Alignment', 'bb-powerpack'),
                        'default'       => 'default',
                        'options'       => array(
							'default'		=> __('Default', 'bb-powerpack'),
                            'left'         	=> __('Left', 'bb-powerpack'),
                            'center'        => __('Center', 'bb-powerpack'),
                            'right'        	=> __('Right', 'bb-powerpack'),
                        ),
					),
					'responsive_overlay_bg_color' => array(
                        'type'       => 'color',
                        'label'      => __('Background Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'connections'	=> array('color'),
						'preview'	 => array(
							'type'		=> 'css',
							'selector'	=> '.pp-advanced-menu .pp-menu-overlay, .pp-advanced-menu .pp-off-canvas-menu',
							'property'	=> 'background-color'
						)
                    ),
					'responsive_overlay_bg_opacity'    => array(
                        'type'          => 'text',
                        'label'         => __( 'Background Opacity', 'bb-powerpack' ),
                        'placeholder'   => '50',
						'default'		=> '80',
                        'size'          => '8',
                        'description'   => '%',
					),
					'responsive_overlay_padding'		=> array(
						'type'			=> 'dimension',
						'label'			=> __('Padding', 'bb-powerpack'),
						'default'		=> 50,
						'slider'		=> true,
						'units'			=> array('px'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-advanced-menu.full-screen .pp-menu-overlay, .pp-advanced-menu.off-canvas .menu',
							'property'      => 'padding',
							'unit'          => 'px'
						)
					),
				),
			),
			'responsive_colors'	=> array(
				'title'			=> __('Links', 'bb-powerpack'),
				'collapsed' 	=> true,
				'fields'		=> array(
					'responsive_link_color' => array(
						'type'       => 'color',
						'label'      => __('Link Color', 'bb-powerpack'),
						'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
						'preview'	 => array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-advanced-menu.full-screen .menu li a, .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a, .pp-advanced-menu.off-canvas .menu li a, .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a',
									'property'	=> 'color'
								),
								array(
									'selector'	=> '.pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle:before, .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu .pp-menu-toggle:before, .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-menu-toggle:before, .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-menu-toggle:after,
									.pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu .pp-menu-toggle:before, .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu .pp-menu-toggle:after, .pp-advanced-menu.full-screen .pp-toggle-arrows .pp-menu-toggle:before, .pp-advanced-menu.full-screen .pp-toggle-arrows .sub-menu .pp-menu-toggle:before,
									 .pp-advanced-menu.full-screen .pp-toggle-plus .pp-menu-toggle:before, .pp-advanced-menu.full-screen .pp-toggle-plus .pp-menu-toggle:after, .pp-advanced-menu.full-screen .pp-toggle-plus .sub-menu .pp-menu-toggle:before, .pp-advanced-menu.full-screen .pp-toggle-plus .sub-menu .pp-menu-toggle:after',
									'property'	=> 'border-color'
								)
							)
						)
					),
					'responsive_link_hover_color' => array(
						'type'       => 'color',
						'label'      => __('Link Hover Color', 'bb-powerpack'),
						'default'    => '',
						'show_reset' => true,
						'connections'	=> array('color'),
						'preview'	 => array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-advanced-menu.full-screen .menu li a:hover, .pp-advanced-menu.full-screen .menu li a:focus, .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a:hover, .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a:focus, .pp-advanced-menu.off-canvas .menu li a:hover,
									.pp-advanced-menu.off-canvas .menu li a:focus, .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a:hover, .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a:focus',
									'property'	=> 'color'
								),
								array(
									'selector'	=> '.pp-advanced-menu.off-canvas .pp-toggle-arrows li:hover .pp-menu-toggle:before, .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu li:hover .pp-menu-toggle:before,
									.pp-advanced-menu.off-canvas .pp-toggle-plus li:hover .pp-menu-toggle:before, .pp-advanced-menu.off-canvas .pp-toggle-plus li:hover .pp-menu-toggle:after, .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:before, .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:after, .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:before,
									.pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:after, .pp-advanced-menu.full-screen .pp-toggle-arrows li:hover .pp-menu-toggle:before, .pp-advanced-menu.full-screen .pp-toggle-arrows .sub-menu li:hover .pp-menu-toggle:before, .pp-advanced-menu.full-screen .pp-toggle-plus li:hover .pp-menu-toggle:before,
									.pp-advanced-menu.full-screen .pp-toggle-plus li:hover .pp-menu-toggle:after, .pp-advanced-menu.full-screen .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:before, .pp-advanced-menu.full-screen .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:after',
									'property'	=> 'border-color'
								)
							)
						)
					),
					'responsive_link_bg_color'  => array(
						'type'       => 'color',
						'label'      => __('Link Background Color', 'bb-powerpack'),
						'default'    => '',
						'show_reset' => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'    => array(
							'type'      => 'css',
							'selector'	=> '.pp-advanced-menu.full-screen .menu li a, .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a, .pp-advanced-menu.off-canvas .menu li a, .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a',
							'property'	=> 'background-color'
						)
					),
					'responsive_link_bg_hover_color'  => array(
						'type'       => 'color',
						'label'      => __('Link Background Hover Color', 'bb-powerpack'),
						'default'    => '',
						'show_reset' => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'none'
						)
					),
					'responsive_link_padding'		=> array(
						'type'			=> 'dimension',
						'label'			=> __('Link Padding', 'bb-powerpack'),
						'default'		=> 10,
						'slider'		=> true,
						'units'			=> array('px'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-advanced-menu.full-screen .menu li a span.menu-item-text, .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a span.menu-item-text, .pp-advanced-menu.off-canvas .menu li a, .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a',
							'property'      => 'padding',
							'unit'          => 'px'
						)
					),
					'responsive_submenu_bg_color'  => array(
						'type'       => 'color',
						'label'      => __('Submenu Background Color', 'bb-powerpack'),
						'default'    => '',
						'show_reset' => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'    => array(
							'type'      => 'none',
						)
					),
				)
			),
			'responsive_border'	=> array(
				'title'		=> __('Border', 'bb-powerpack'),
				'collapsed' => true,
				'fields'	=> array(
					'responsive_link_border_width'		=> array(
						'type'			=> 'dimension',
						'label'			=> __('Link Border Width', 'bb-powerpack'),
						'default'		=> 0,
						'slider'		=> true,
						'units'			=> array('px'),
					),
					'responsive_link_border_color' => array(
						'type'       => 'color',
						'label'      => __('Link Border Color', 'bb-powerpack'),
						'default'    => '',
						'show_reset' => true,
						'connections'	=> array('color'),
						'preview'	 => array(
							'type'		=> 'css',
							'selector'	=> '.pp-advanced-menu.full-screen .menu li a span.menu-item-text, .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a span.menu-item-text, .pp-advanced-menu.off-canvas .menu li a, .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a',
							'property'	=> 'border-color'
						)
					),
				)
			),
			'menu_shadow'   => array(
                'title'         => __('Shadow', 'bb-powerpack'),
				'collapsed' 	=> true,
                'fields'        => array(
                    'enable_shadow'     => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Enable Shadow', 'bb-powerpack'),
                        'default'           => 'no',
                        'options'           => array(
                            'yes'               => __('Yes', 'bb-powerpack'),
                            'no'                => __('No', 'bb-powerpack'),
                        ),
                        'toggle'            => array(
                            'yes'               => array(
                                'fields'            => array('menu_shadow', 'menu_shadow_color', 'menu_shadow_opacity')
                            )
                        )
                    ),
                    'menu_shadow' 		=> array(
                        'type'              => 'pp-multitext',
                        'label'             => __('Shadow', 'bb-powerpack'),
                        'default'           => array(
                            'vertical'			=> 0,
                            'horizontal'		=> 0,
                            'blur'				=> 10,
                            'spread'			=> 0
                        ),
                        'options'			=> array(
                            'horizontal'		=> array(
                                'placeholder'		=> __('Horizontal', 'bb-powerpack'),
                                'tooltip'			=> __('Horizontal', 'bb-powerpack'),
                                'icon'				=> 'fa-arrows-h'
                            ),
                            'vertical'			=> array(
                                'placeholder'		=> __('Vertical', 'bb-powerpack'),
                                'tooltip'			=> __('Vertical', 'bb-powerpack'),
                                'icon'				=> 'fa-arrows-v'
                            ),
                            'blur'				=> array(
                                'placeholder'		=> __('Blur', 'bb-powerpack'),
                                'tooltip'			=> __('Blur', 'bb-powerpack'),
                                'icon'				=> 'fa-circle-o'
                            ),
                            'spread'			=> array(
                                'placeholder'		=> __('Spread', 'bb-powerpack'),
                                'tooltip'			=> __('Spread', 'bb-powerpack'),
                                'icon'				=> 'fa-paint-brush'
                            ),
                        )
                    ),
                    'menu_shadow_color'     => array(
                        'type'                  => 'color',
                        'label'                 => __('Shadow Color', 'bb-powerpack'),
						'default'               => '000000',
						'connections'			=> array('color'),
                    ),
                    'menu_shadow_opacity'   => array(
                        'type'                  => 'text',
                        'label'                 => __('Shadow Opacity', 'bb-powerpack'),
                        'description'           => '%',
                        'size'                  => 5,
                        'default'               => 10,
                    ),
                )
            ),
			'mobile_toggle_style' => array(
				'title'	=> __( 'Mobile Toggle', 'bb-powerpack' ),
				'collapsed' => true,
				'fields'	=> array(
					'mobile_toggle_size'    => array(
                        'type'          => 'unit',
                        'label'         => __( 'Size', 'bb-powerpack' ),
                        'placeholder'   => '30',
						'default'		=> '30',
						'units'			=> array('px'),
						'slider'		=> true,
						'preview'	 => array(
                            'type'		=> 'css',
							'rules'     => array(
								array(
									'selector'	=> '.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box,
											.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner,
											.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:before,
											.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:after',
									'property'	=> 'width',
									'unit'		=> 'px'
								),
								array(
									'selector'	=> '.pp-advanced-menu-mobile-toggle i',
									'property'  => 'font-size',
									'unit'      => 'px'
								)
							),
                        )
                    ),
					'mobile_toggle_thickness'    => array(
                        'type'          => 'unit',
                        'label'         => __( 'Thickness', 'bb-powerpack' ),
						'default'		=> '3',
                        'units'			=> array('px'),
						'help'          => __( 'Works for the default hamburger icon. Not useful for custom toggle icon.', 'bb-powerpack' ),
						'slider'		=> true,
						'preview'	 => array(
                            'type'		=> 'css',
                            'selector'	=> '.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner,
											.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:before,
											.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:after',
                            'property'	=> 'height',
							'unit'		=> 'px'
                        )
                    ),
					'mobile_toggle_color' => array(
                        'type'       => 'color',
                        'label'      => __('Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'connections'	=> array('color'),
                        'preview'	 => array(
                            'type'		=> 'css',
                            'rules'		=> array(
								array(
									'selector'	=> '.pp-advanced-menu-mobile-toggle',
                            		'property'	=> 'color'
								),
								array(
									'selector'	=> '.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner,
													.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:before,
													.pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:after',
                            		'property'	=> 'background-color'
								),
							)
                        )
                    ),
					'mobile_toggle_bg_color' => array(
                        'type'       => 'color',
                        'label'      => __('Background Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'show_alpha' => true,
						'connections'	=> array('color'),
                        'preview'	 => array(
                            'type'		=> 'css',
                            'selector'	=> '.pp-advanced-menu-mobile-toggle',
                            'property'	=> 'background-color'
                        )
                    ),
					'mobile_toggle_border' => array(
						'type' => 'border',
						'label' => __( 'Border', 'bb-powerpack' ),
						'preview'	 => array(
                            'type'		=> 'css',
                            'selector'	=> '.pp-advanced-menu-mobile-toggle',
                        )
					),
				)
			),
			'close_icon'	=> array(
				'title'		=> __('Close Icon', 'bb-powerpack'),
				'collapsed' => true,
				'fields'	=> array(
					'close_icon_size'    => array(
                        'type'          => 'unit',
                        'label'         => __( 'Close Icon Size', 'bb-powerpack' ),
                        'placeholder'   => '30',
						'units'			=> array('px'),
						'slider'		=> true,
						'preview'         => array(
							'type'            => 'css',
							'rules'			  => array(
								array(
									'selector'        => '.pp-advanced-menu.off-canvas .pp-off-canvas-menu .pp-menu-close-btn',
									'property'        => 'font-size',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn',
									'property'        => 'width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn, .pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn:before, .pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn:after',
									'property'        => 'height',
									'unit'            => 'px'
								),
							)
						)
                    ),
                    'close_icon_color' => array(
                        'type'       => 'color',
                        'label'      => __('Close Icon Color', 'bb-powerpack'),
                        'default'    => '',
						'show_reset' => true,
						'connections'	=> array('color'),
						'preview'	 => array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn:before, .pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn:after',
									'property'	=> 'background-color'
								),
								array(
									'selector'	=> '.pp-advanced-menu .pp-off-canvas-menu .pp-menu-close-btn',
									'property'	=> 'color'
								),
							)
						)
                    ),
				)
			)
		)
	),
    'typography'       => array( // Tab
        'title'         => __('Typography', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'link_typography' => array(
                'title' => __('Link', 'bb-powerpack' ),
                'fields'    => array(
					'link_typography'	=> array(
						'type'				=> 'typography',
						'label'				=> __('Typography', 'bb-powerpack'),
						'responsive'		=> true
					),
                )
            ),
            'submenu_typography'    => array(
                'title'                 => __('Sub Menu', 'bb-powerpack'),
                'fields'                => array(
					'submenu_typography'	=> array(
						'type'				=> 'typography',
						'label'				=> __('Typography', 'bb-powerpack'),
						'responsive'		=> true
					),
                )
            ),
			'mobile_toggle_typography' => array(
                'title' => __('Mobile Toggle', 'bb-powerpack' ),
                'fields'    => array(
                    'mobile_toggle_font'       => array(
                        'type'          => 'font',
                        'label'         => __('Font Family', 'bb-powerpack'),
                        'default'       => array(
                            'family'        => 'Default',
                            'weight'        => 'Default'
                        ),
                        'preview'         => array(
                            'type'            => 'font',
                            'selector'        => '.pp-advanced-menu-mobile-toggle'
                        )
                    ),
					'mobile_toggle_font_size'     => array(
                        'type'          => 'pp-switch',
						'label'         => __('Font Size', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       => __('Default', 'bb-powerpack'),
							'custom'        => __('Custom', 'bb-powerpack')
						),
						'toggle'        => array(
							'custom'        => array(
								'fields'        => array('mobile_toggle_font_size_custom')
							)
						)
                    ),
					'mobile_toggle_font_size_custom' => array(
						'type' 			=> 'unit',
						'label' 		=> __('Custom Font Size', 'bb-powerpack'),
						'units' 		=> array('px'),
						'slider'		=> true,
						'preview' 		=> array(
							'type' 			=> 'css',
							'selector'		=> '.pp-advanced-menu-mobile-toggle',
							'property'		=> 'font-size',
							'unit' 			=> 'px'
						),
						'responsive' 	=> array(
							'placeholder' 	=> array(
								'default' 	=> '18',
								'medium' 	=> '',
								'responsive' => '',
							),
						),
					),
                )
            ),
        )
    ),
));

class Advanced_Menu_Walker extends Walker_Nav_Menu {

    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $args   = ( object )$args;

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $submenu = $args->has_children ? ' pp-has-submenu' : '';

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = ' class="' . esc_attr( $class_names ) . $submenu . '"';
		
		$item_id = apply_filters( 'pp_advanced_menu_item_id', 'menu-item-' . $item->ID, $item, $depth );

        $output .= $indent . '<li id="'. $item_id . '"' . $value . $class_names . '>';

		$atts = array();

		$atts['title'] = ! empty( $item->attr_title ) ? esc_attr( $item->attr_title ) : '';
		$atts['target'] = ! empty( $item->target ) ? esc_attr( $item->target ) : '';
		
		if ( '_blank' === $item->target && empty( $item->xfn ) ) {
			$atts['rel'] = 'noopener noreferrer';
		} else {
			$atts['rel'] = esc_attr( $item->xfn );
		}

		$atts['href'] = ! empty( $item->url ) ? esc_attr( $item->url ) : '';
		$atts['aria-current'] = $item->current ? 'page' : '';

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		//$atts['tabindex'] 	= '0';
		//$atts['role'] 		= 'link';

		$attributes = '';

		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value 		 = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

        $item_output = $args->has_children ? '<div class="pp-has-submenu-container">' : '';
        $item_output .= $args->before;
        $item_output .= '<a'. $attributes .'><span class="menu-item-text">';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		if( $args->has_children ) {
			$item_output .= '<span class="pp-menu-toggle" tabindex="0" aria-expanded="false" aria-label="' . strip_tags( $item->title ) . ': submenu' . '" role="button"></span>';
		}
		$item_output .= '</span>';
		if ( apply_filters( 'pp_advanced_menu_enable_item_description', false ) && ! empty( $item->description ) ) {
			$item_output .= '<p class="menu-item-description">' . $item->description . '</p>';
		}
		$item_output .= '</a>';


        $item_output .= $args->after;
        $item_output .= $args->has_children ? '</div>' : '';

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        $id_field = $this->db_fields['id'];
        if ( is_object( $args[0] ) ) {
            $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
        }
        return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }
}
