<?php
/**
 * Handles logic for the site Header / Footer.
 *
 * @package BB_PowerPack
 * @since 2.6.10
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BB_PowerPack_Header_Footer
 */
final class BB_PowerPack_Header_Footer {
	/**
	 * Settings tab constant.
	 */
	const SETTINGS_TAB = 'header_footer';

	/**
	 * Holds an array of posts.
	 *
	 * @var array $templates
	 * @since 2.7.1
	 */
	static private $templates = array();

	/**
	 * Holds the post ID for header.
	 *
	 * @var int $header
	 * @since 2.7.1
	 */
	static public $header;

	/**
	 * Holds the post ID for footer.
	 *
	 * @var int $footer
	 * @since 2.7.1
	 */
	static public $footer;

	/**
	 * Holds boolean.
	 *
	 * @var int $render_css
	 * @since 2.22.2
	 */
	static public $render_css = false;

	/**
	 * Holds boolean.
	 *
	 * @var int $render_js
	 * @since 2.22.2
	 */
	static public $render_js = false;

	/**
	 * Initialize hooks.
	 *
	 * @since 2.7.1
	 * @return void
	 */
	static public function init() {
		add_filter( 'pp_admin_settings_tabs', 	__CLASS__ . '::render_settings_tab', 10, 1 );
		add_action( 'pp_admin_settings_save', 	__CLASS__ . '::save_settings' );

		add_action( 'after_setup_theme', __CLASS__ . '::load' );
		add_filter( 'fl_builder_render_css', __CLASS__ . '::render_css' );
		add_filter( 'fl_builder_render_js', __CLASS__ . '::render_js' );
		add_action( 'template_redirect', __CLASS__ . '::disable_content_rendering' );
	}

	/**
	 * Render settings tab.
	 *
	 * Adds Header/Footer tab in PowerPack admin settings.
	 *
	 * @since 2.7.1
	 * @param array $tabs Array of existing settings tabs.
	 */
	static public function render_settings_tab( $tabs ) {
		$tabs[ self::SETTINGS_TAB ] = array(
			'title'    => esc_html__( 'Header / Footer', 'bb-powerpack' ),
			'icon'     => '<span class="dashicons dashicons-align-wide"></span>',
			'show'     => ! is_network_admin() && ! BB_PowerPack_Admin_Settings::get_option( 'ppwl_hide_header_footer_tab' ),
			'cap'      => ! is_network_admin() ? 'manage_options' : 'manage_network_plugins',
			'file'     => BB_POWERPACK_DIR . 'includes/admin-settings-header-footer.php',
			'priority' => 300,
		);

		return $tabs;
	}

	/**
	 * Save settings.
	 *
	 * Saves setting fields value in options.
	 *
	 * @since 2.7.1
	 */
	static public function save_settings() {
		if ( ! isset( $_POST['bb_powerpack_header_footer_page'] ) ) {
			return;
		}

		$header = isset( $_POST['bb_powerpack_header_footer_template_header'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_powerpack_header_footer_template_header'] ) ) : '';
		$footer = isset( $_POST['bb_powerpack_header_footer_template_footer'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_powerpack_header_footer_template_footer'] ) ) : '';

		update_option( 'bb_powerpack_header_footer_template_header', $header );
		update_option( 'bb_powerpack_header_footer_template_footer', $footer );

		if ( isset( $_POST['bb_powerpack_header_footer_fixed_header'] ) ) {
			update_option( 'bb_powerpack_header_footer_fixed_header', 1 );
		} else {
			delete_option( 'bb_powerpack_header_footer_fixed_header' );
		}

		if ( isset( $_POST['bb_powerpack_header_footer_fixed_header_devices'] ) ) {
			update_option( 'bb_powerpack_header_footer_fixed_header_devices', wp_unslash( $_POST['bb_powerpack_header_footer_fixed_header_devices'] ) );
		} else {
			update_option( 'bb_powerpack_header_footer_fixed_header_devices', array() );
		}

		if ( isset( $_POST['bb_powerpack_header_footer_shrink_header'] ) ) {
			update_option( 'bb_powerpack_header_footer_shrink_header', 1 );
		} else {
			delete_option( 'bb_powerpack_header_footer_shrink_header' );
		}

		if ( isset( $_POST['bb_powerpack_header_footer_overlay_header'] ) ) {
			update_option( 'bb_powerpack_header_footer_overlay_header', 1 );
		} else {
			delete_option( 'bb_powerpack_header_footer_overlay_header' );
		}

		if ( isset( $_POST['bb_powerpack_header_footer_overlay_header_bg'] ) ) {
			update_option( 'bb_powerpack_header_footer_overlay_header_bg', sanitize_text_field( wp_unslash( $_POST['bb_powerpack_header_footer_overlay_header_bg'] ) ) );
		}

		// Clear BB's assets cache.
		// if ( class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'delete_asset_cache_for_all_posts' ) ) {
		// 	FLBuilderModel::delete_asset_cache_for_all_posts();
		// }
	}

	/**
	 * Get templates.
	 *
	 * Get all pages and BB's templates.
	 *
	 * @since 2.7.1
	 */
	static public function get_templates() {
		if ( ! empty( self::$templates ) ) {
			return self::$templates;
		}

		$args = array(
			'post_type' 		=> 'page',
			'post_status'		=> 'publish',
			'orderby' 			=> 'title',
			'order' 			=> 'ASC',
			'posts_per_page' 	=> '-1',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$pages = get_posts( $args );

		$args['post_type'] = 'fl-builder-template';

		$args['tax_query'] = array(
			array(
				'taxonomy'		=> 'fl-builder-template-type',
				'field'			=> 'slug',
				'terms'			=> array(
					'layout',
					'row',
				),
			),
		);

		$templates = get_posts( $args );

		self::$templates = array(
			'templates'	=> $templates,
			'pages'		=> $pages,
		);

		return self::$templates;
	}

	/**
	 * Get templates HTML.
	 *
	 * Get all pages and BB's templates and build options for select field.
	 *
	 * @since 2.7.1
	 * @param string $selected Selected template for the field.
	 */
	static public function get_templates_html( $selected = '' ) {
		$templates = self::get_templates();

		$options = '<option value="">' . esc_html__( 'Default', 'bb-powerpack' ) . '</option>';

		foreach ( $templates as $type => $data ) {
			if ( ! count( $data ) ) {
				continue;
			}

			$label = '';

			if ( 'pages' === $type ) {
				$label = esc_html__( 'Pages', 'bb-powerpack' );
			}
			if ( 'templates' === $type ) {
				$label = esc_html__( 'Builder Templates', 'bb-powerpack' );
			}

			$options .= '<optgroup label="' . $label . '">';

			if ( ! empty( $data ) ) {
				foreach ( $data as $post ) {
					$options .= '<option value="' . $post->ID . '" ' . selected( $selected, $post->ID, false ) . '>' . $post->post_title . '</option>';
				}
			} else {
				$options .= '<option value="" disabled>' . __( 'Not found', 'bb-powerpack' ) . '</option>';
			}

			$options .= '</optgroup>';
		}

		return $options;
	}

	/**
	 * Returns the slug for supported theme.
	 *
	 * @since 2.7.1
	 * @return mixed
	 */
	static public function get_theme_support_slug() {
		$slug = false;

		if ( defined( 'FL_THEME_VERSION' ) ) {
			$slug = 'bb-theme';
		} elseif ( defined( 'ASTRA_THEME_VERSION' ) ) {
			$slug = 'astra';
		} elseif ( function_exists( 'genesis' ) ) {
			$slug = 'genesis';
		} elseif ( defined( 'GENERATE_VERSION' ) ) {
			$slug = 'generatepress';
		} elseif ( function_exists( 'storefront_is_woocommerce_activated' ) ) {
			$slug = 'storefront';
		} elseif ( get_theme_support( 'pp-header-footer' ) ) {
			$slug = 'custom';
		}

		return apply_filters( 'pp_header_footer_theme_slug', $slug );
	}

	/**
	 * Loads theme support if we have a supported theme.
	 *
	 * @since 2.7.1
	 * @return void
	 */
	static public function load() {
		self::$header = get_option( 'bb_powerpack_header_footer_template_header' );
		self::$footer = get_option( 'bb_powerpack_header_footer_template_footer' );

		// Remove option if header template has deleted.
		if ( ! empty( self::$header ) && 'publish' != get_post_status( self::$header ) ) {
			delete_option( 'bb_powerpack_header_footer_template_header' );
		}
		// Remove option if footer template has deleted.
		if ( ! empty( self::$footer ) && 'publish' != get_post_status( self::$footer ) ) {
			delete_option( 'bb_powerpack_header_footer_template_footer' );
		}

		if ( empty( self::$header ) && empty( self::$footer ) ) {
			return;
		}

		$slug = self::get_theme_support_slug();

		add_filter( 'body_class', __CLASS__ . '::body_class' );

		if ( $slug && is_callable( 'FLBuilder::render_content_by_id' ) ) {
			/**
			 * Hook to support additional themes or perform any actions.
			 *
			 * @since 2.15.2
			 *
			 * @param string $slug Current theme slug or custom.
			 */
			do_action( 'pp_header_footer_before_setup', $slug );

			$file = BB_POWERPACK_DIR . "classes/theme-support/class-pp-theme-support-$slug.php";

			if ( file_exists( $file ) ) {
				require_once $file;
			}

			/**
			 * Hook to support additional themes or perform any actions.
			 *
			 * @since 2.9.0
			 *
			 * @param string $slug Current theme slug or custom.
			 */
			do_action( 'pp_header_footer_after_setup', $slug );
		}
	}

	/**
	 * Renders the CSS for header/footer and adds
	 * it to the cached builder CSS layout file.
	 *
	 * @param string $css	CSS of nodes.
	 * @since 2.7.1
	 * @return string
	 */
	static public function render_css( $css ) {
		if ( ! is_callable( 'FLBuilderModel::get_post_id' ) || empty( self::$header ) || self::$render_css ) {
			return $css;
		}

		$id = self::get_wpml_element_id( self::$header );

		if ( $id ) {
			$css .= file_get_contents( BB_POWERPACK_DIR . 'assets/css/header-layout.css' );
			self::$render_css = true;
		}

		return $css;
	}

	/**
	 * Renders the JS for header/footer and adds
	 * it to the cached builder JS layout file.
	 *
	 * @param string $js	JS of nodes.
	 * @since 2.7.1
	 * @return string
	 */
	static public function render_js( $js ) {
		if ( ! is_callable( 'FLBuilderModel::get_post_id' ) || empty( self::$header ) || self::$render_js ) {
			return $js;
		}

		$id = self::get_wpml_element_id( self::$header );

		if ( $id ) {
			$js .= file_get_contents( BB_POWERPACK_DIR . 'assets/js/header-layout.js' );
			self::$render_js = true;
		}

		return $js;
	}

	/**
	 * Enqueue the styles and scripts for a single layout
	 * using the provided post ID.
	 *
	 * @since 2.22.2
	 * @param int $post_id
	 * @return void
	 */
	static public function enqueue_layout_styles_scripts_by_id( $post_id ) {
		if ( is_callable( 'FLBuilder::enqueue_layout_styles_scripts' ) && FLBuilderModel::is_builder_active() ) {
			FLBuilderModel::set_post_id( $post_id );
			FLBuilder::enqueue_layout_styles_scripts( true );
			FLBuilderModel::reset_post_id();
		} else {
			if ( ( defined( 'FL_BUILDER_VERSION' ) && version_compare( FL_BUILDER_VERSION, '2.5.2.3', '<' ) ) || FLBuilderModel::is_builder_active() ) {
				// Ensure global assets are rendered.
				if ( is_callable( 'FLBuilder::clear_enqueued_global_assets' ) ) {
					FLBuilder::clear_enqueued_global_assets();
				}
			}

			if ( is_callable( 'FLBuilder::enqueue_layout_styles_scripts_by_id' ) ) {
				FLBuilder::enqueue_layout_styles_scripts_by_id( $post_id );
			}
		}
	}

	/**
	 * Renders the header for the current page.
	 * Used by theme support classes.
	 *
	 * @param mixed $tag	HTML tag for header.
	 * @since 2.7.1
	 * @return void
	 */
	static public function render_header( $tag = null ) {
		$tag 		= ! $tag ? 'header' : $tag;
		$id 		= self::$header;
		$id 		= self::get_wpml_element_id( $id );
		$is_fixed 	= get_option( 'bb_powerpack_header_footer_fixed_header' );
		$devices 	= get_option( 'bb_powerpack_header_footer_fixed_header_devices' );
		$is_shrink 	= get_option( 'bb_powerpack_header_footer_shrink_header' );
		$is_overlay = get_option( 'bb_powerpack_header_footer_overlay_header' );
		$overlay_bg = get_option( 'bb_powerpack_header_footer_overlay_header_bg', 'default' );

		do_action( 'pp_header_footer_before_render_header', $id );

		$inline_assets = pp_is_bb_rendering_assets_inline();

		if ( ! $inline_assets && ! FLBuilderModel::is_builder_active() ) {
			add_filter( 'fl_builder_render_assets_inline', '__return_true' );
		}

		// Enqueue jQuery throttle.
		wp_enqueue_script( 'jquery-throttle' );

		// Enqueue imagesloaded.
		wp_enqueue_script( 'imagesloaded' );

		// Enqueue styles and scripts for this post.
		self::enqueue_layout_styles_scripts_by_id( $id );

		// Print the styles if we are outside of the head tag.
		if ( did_action( 'wp_enqueue_scripts' ) && ! doing_filter( 'wp_enqueue_scripts' ) ) {
			wp_print_styles();
		}

		if ( ! is_array( $devices ) ) {
			$devices = array();
		}

		FLBuilder::render_content_by_id( $id, $tag, array(
			'itemscope'       => 'itemscope',
			'itemtype'        => 'http://schema.org/WPHeader',
			'data-type'       => 'header',
			'data-sticky'     => $is_fixed ? '1' : '0',
			'data-sticky-devices' => implode( ',', $devices ),
			'data-shrink'     => $is_shrink ? '1' : '0',
			'data-overlay'    => $is_overlay ? '1' : '0',
			'data-overlay-bg' => $overlay_bg,
			'role'            => 'banner'
		) );

		if ( ! $inline_assets && ! FLBuilderModel::is_builder_active() ) {
			add_filter( 'fl_builder_render_assets_inline', '__return_false' );
		}

		do_action( 'pp_header_footer_after_render_header', $id );
	}

	/**
	 * Renders the footer for the current page.
	 * Used by theme support classes.
	 *
	 * @param mixed $tag	HTML tag for footer.
	 * @since 2.7.1
	 * @return void
	 */
	static public function render_footer( $tag = null ) {
		$tag = ! $tag ? 'footer' : $tag;
		$id  = self::$footer;
		$id  = self::get_wpml_element_id( $id );

		do_action( 'pp_header_footer_before_render_footer', $id );

		$inline_assets = pp_is_bb_rendering_assets_inline();

		if ( ! $inline_assets && ! FLBuilderModel::is_builder_active() ) {
			add_filter( 'fl_builder_render_assets_inline', '__return_true' );
		}

		if ( ( defined( 'FL_BUILDER_VERSION' ) && version_compare( FL_BUILDER_VERSION, '2.5.2.3', '<' ) ) || FLBuilderModel::is_builder_active() ) {
			// Ensure global assets are rendered.
			if ( is_callable( 'FLBuilder::clear_enqueued_global_assets' ) ) {
				FLBuilder::clear_enqueued_global_assets();
			}
		}

		// Enqueue styles and scripts for this post.
		self::enqueue_layout_styles_scripts_by_id( $id );

		// Print the styles if we are outside of the head tag.
		if ( did_action( 'wp_enqueue_scripts' ) && ! doing_filter( 'wp_enqueue_scripts' ) ) {
			wp_print_styles();
		}

		FLBuilder::render_content_by_id( $id, $tag, array(
			'itemscope'       => 'itemscope',
			'itemtype'        => 'http://schema.org/WPFooter',
			'data-type'       => 'footer',
		) );

		if ( ! $inline_assets && ! FLBuilderModel::is_builder_active() ) {
			add_filter( 'fl_builder_render_assets_inline', '__return_false' );
		}

		do_action( 'pp_header_footer_after_render_footer', $id );
	}

	/**
	 * Disables builder content rendering for headers
	 * and footers since those are edited in place.
	 *
	 * @since 2.7.1
	 * @return void
	 */
	static public function disable_content_rendering() {
		global $post;

		// Additional check for empty post ID to avoid breaking the content
		// of other plugins which use template_redirect hook and query_posts 
		// to render their content.
		if ( is_a( $post, 'WP_Post' ) && ! empty( $post->ID ) ) {
			$header = get_option( 'bb_powerpack_header_footer_template_header' );
			$footer = get_option( 'bb_powerpack_header_footer_template_footer' );
			$has_header = $post->ID == $header;
			$has_footer = $post->ID == $footer;

			if ( $has_header || $has_footer ) {
				remove_filter( 'the_content', 'FLBuilder::render_content' );
				add_filter( 'the_content', __CLASS__ . '::override_the_content' );
			}
		}
	}

	/**
	 * Overrides the default editor content for headers
	 * and footers since those are edited in place.
	 *
	 * @param string $content	Post content.
	 * @since 2.7.1
	 * @return string
	 */
	static public function override_the_content( $content ) {
		return '<div style="padding: 200px 100px; text-align:center; opacity:0.5;">' . __( 'Content Area', 'bb-powerpack' ) . '</div>';
	}

	/**
	 * Get the ID of WPML translated post.
	 *
	 * @param string $id	Original Post ID.
	 * @since 2.7.1
	 * @return string
	 */
	static public function get_wpml_element_id( $id ) {
		if ( class_exists( 'sitepress' ) && class_exists( 'WPML_Post_Element' ) ) {
			global $sitepress;

			$current_lang = $sitepress->get_current_language();
			
			$wpml_post      = new WPML_Post_Element( $id, $sitepress );
			$wpml_post_lang = $wpml_post->get_language_code();
			
			if ( $current_lang !== $wpml_post_lang && ! is_null( $wpml_post_lang ) ) {
				$type 		  = $wpml_post->get_wpml_element_type();
				$trid         = $sitepress->get_element_trid( $id, $type );
				$translations = $sitepress->get_element_translations( $trid, $type );
				if ( is_array( $translations ) && ! empty( $translations ) && isset( $translations[ $current_lang ] ) && isset( $translations[ $current_lang ]->element_id ) ) {
					$id = $translations[ $current_lang ]->element_id;
				}
			}
		}
		
		return $id;
	}

	/**
	 * Add CSS classes to the body tag.
	 *
	 * Fired by `body_class` filter.
	 *
	 * @since 2.7.1
	 *
	 * @param array $classes An array of body classes.
	 *
	 * @return array An array of body classes.
	 */
	static public function body_class( $classes ) {
		$classes[] = 'bb-powerpack-header-footer';

		if ( FLBuilderModel::is_builder_active() ) {
			$post_id = FLBuilderModel::get_post_id();

			if ( self::$header == $post_id ) {
				$classes[] = 'bb-powerpack-header-edit';
			}
			if ( self::$footer == $post_id ) {
				$classes[] = 'bb-powerpack-footer-edit';
			}
		}

		return $classes;
	}
}

// Initialize the class.
BB_PowerPack_Header_Footer::init();
