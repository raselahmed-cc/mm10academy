<?php
/**
 * Plugin Name: PowerPack for Beaver Builder
 * Plugin URI: https://wpbeaveraddons.com
 * Description: A set of 90+ creative and advanced modules, 350+ templates for Beaver Builder to speed up your web design and development process.
 * Version: 2.40.10
 * Author: IdeaBox Creations
 * Author URI: https://ideaboxcreations.com
 * Copyright: (c) 2025 IdeaBox Creations
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bb-powerpack
 * Domain Path: /languages
 * Requires at least: 4.6
 * Tested up to: 6.9
 * Requires PHP: 7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class BB_PowerPack {
	/**
	 * Holds the class object.
	 *
	 * @since 1.1.4
	 * @var object
	 */
	public static $instance;

	/**
	 * Holds the upload dir path.
	 *
	 * @since 1.1.8
	 * @var array
	 */
	public static $upload_dir;

	/**
	 * Holds error messages.
	 *
	 * @since 1.1.8
	 * @var array
	 */
	public static $errors;

	/**
	 * Holds FontAwesome CSS class.
	 *
	 * @since 2.1
	 * @var string
	 */
	public $fa_css = '';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.1.4
	 */
	public function __construct() {
		if ( is_admin() ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$lite_dirname   = 'powerpack-addon-for-beaver-builder';
			$lite_active    = is_plugin_active( $lite_dirname . '/bb-powerpack-lite.php' );
			$plugin_dirname = basename( dirname( dirname( __FILE__ ) ) );

			if ( class_exists( 'BB_PowerPack_Lite' ) || ( $plugin_dirname != $lite_dirname && $lite_active ) ) {
				add_action( 'admin_init', array( $this, 'deactivate_lite' ) );
				return;
			}
		}

		self::$errors = array();

		$this->define_constants();

		/* Includes */
		require_once 'includes/helper-functions.php';

		/* Classes */
		require_once 'classes/class-pp-post-helper.php';
		require_once 'classes/class-pp-ajax.php';
		require_once 'classes/class-pp-admin-settings.php';
		require_once 'classes/class-pp-module-fields.php';
		require_once 'classes/class-pp-recaptcha.php';
		require_once 'classes/class-pp-templates-library.php';
		require_once 'classes/class-pp-header-footer.php';
		require_once 'classes/class-pp-maintenance-mode.php';
		require_once 'classes/class-pp-login-register.php';
		require_once 'classes/class-pp-media-fields.php';
		require_once 'classes/class-pp-wpml-compatibility.php';
		require_once 'classes/class-pp-taxonomy-thumbnail.php';

		/* Updater */
		require_once 'classes/class-pp-update.php';
		require_once 'includes/updater/update-config.php';

		/* WP CLI Commands */
		if ( defined( 'WP_CLI' ) ) {
			require_once 'classes/class-pp-wpcli-command.php';
		}

		/* Hooks */
		$this->init_hooks();
		$this->reset_hide_plugin();
	}

	/**
	 * Auto deactivate PowerPack Lite.
	 *
	 * @since 1.1.7
	 */
	public function deactivate_lite() {
		deactivate_plugins( 'bb-powerpack-lite/bb-powerpack-lite.php' );
	}

	/**
	 * Define PowerPack constants.
	 *
	 * @since 1.1.5
	 * @return void
	 */
	private function define_constants() {
		define( 'BB_POWERPACK_VER', '2.40.10' );
		define( 'BB_POWERPACK_DIR', plugin_dir_path( __FILE__ ) );
		define( 'BB_POWERPACK_URL', plugins_url( '/', __FILE__ ) );
		define( 'BB_POWERPACK_PATH', plugin_basename( __FILE__ ) );
		define( 'BB_POWERPACK_CAT', $this->register_wl_cat() );
	}

	/**
	 * Initializes actions and filters.
	 *
	 * @since 1.1.5
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'loader' ), 12 );
		add_action( 'init', array( $this, 'load_modules' ), 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 9999 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_action( 'wp_head', array( $this, 'render_scripts' ) );
		add_action( 'admin_head', array( $this, 'render_admin_scripts' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'network_admin_notices', array( $this, 'admin_notices' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'rank_math/researches/toc_plugins', array( $this, 'rank_math_toc_plugins' ) );
		add_filter( 'mainwp_request_update_premium_plugins', array( $this, 'mainwp_detect_premium_plugins_update' ), 10 );
	}

	/**
	 * Load language files.
	 *
	 * @since 1.1.4
	 * @return void
	 */

	public function load_textdomain() {
		// Traditional WordPress plugin locale filter
		// Uses get_user_locale() which was added in 4.7 so we need to check its available.
		if ( function_exists( 'get_user_locale' ) ) {
			$locale = apply_filters( 'plugin_locale', get_user_locale(), 'bb-powerpack' );
		} else {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'bb-powerpack' );
		}

		//Setup paths to current locale file
		$mofile_global = trailingslashit( WP_LANG_DIR ) . 'plugins/bbpowerpack/bb-powerpack-' . $locale . '.mo';
		$mofile_local  = BB_POWERPACK_DIR . 'languages/bb-powerpack-' . $locale . '.mo';

		if ( file_exists( $mofile_global ) ) {
			//Look in global /wp-content/languages/plugins/bbpowerpack/ folder
			load_textdomain( 'bb-powerpack', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			//Look in local /wp-content/plugins/bbpowerpack/languages/ folder
			load_textdomain( 'bb-powerpack', $mofile_local );
		} else {
			load_plugin_textdomain( 'bb-powerpack', false, BB_POWERPACK_DIR . 'languages/' );
		}
	}

	/**
	 * Include row and column setting extendor.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function loader() {
		require_once 'classes/class-pp-global-styles.php';
		require_once 'classes/class-pp-module.php';

		if ( ! is_admin() && class_exists( 'FLBuilder' ) ) {

			// Panel functions
			require_once 'includes/panel-functions.php';

			$extensions = BB_PowerPack_Admin_Settings::get_enabled_extensions();

			/* Extend row settings */
			if ( isset( $extensions['row'] ) && count( $extensions['row'] ) > 0 ) {
				require_once 'includes/row.php';
			}

			/* Extend column settings */
			if ( isset( $extensions['col'] ) && count( $extensions['col'] ) > 0 ) {
				require_once 'includes/column.php';
			}
		}
	}

	/**
	 * Include modules.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_modules() {
		if ( ! class_exists( 'FLBuilder' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'network_admin_notices', array( $this, 'admin_notices' ) );
			return;
		} else {

			$this->init_fa_css();

			$this->filter_wp_lazy_loading();

			require_once 'classes/class-pp-modules.php';
			require_once 'includes/modules.php';
		}
	}

	public function init_fa_css() {
		$enabled_icons = FLBuilderModel::get_enabled_icons();

		if ( in_array( 'font-awesome-5-solid', $enabled_icons )
			|| in_array( 'font-awesome-5-regular', $enabled_icons )
			|| in_array( 'font-awesome-5-brands', $enabled_icons ) ) {
				$this->fa_css = 'font-awesome-5';
		} else {
			$this->fa_css = 'font-awesome';
		}
	}

	public function filter_wp_lazy_loading() {
		$lazyload = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_disable_wp_lazyload' );

		if ( 'yes' === $lazyload ) {
			add_filter( 'wp_lazy_loading_enabled', '__return_false' );
		}
	}

	public static function register_module( $class, $form ) {
		BB_PowerPack_Modules::register_module( $class, $form );
	}

	/**
	 * Register the styles and scripts.
	 *
	 * @since 2.5.0
	 * @return void
	 */
	public function register_scripts() {
		wp_register_style( 'pp-animate', BB_POWERPACK_URL . 'assets/css/animate.min.css', array(), '3.5.1' );
		wp_register_style( 'pp-jquery-fancybox', BB_POWERPACK_URL . 'assets/css/jquery.fancybox.min.css', array(), '3.5.4' );
		wp_register_style( 'jquery-justifiedgallery', BB_POWERPACK_URL . 'assets/css/justifiedGallery.min.css', array(), '3.7.0' );
		wp_register_style( 'jquery-swiper', BB_POWERPACK_URL . 'assets/css/swiper.min.css', array(), '8.4.7' );
		wp_register_style( 'pp-owl-carousel', BB_POWERPACK_URL . 'assets/css/owl.carousel.css', array(), BB_POWERPACK_VER );
		wp_register_style( 'pp-owl-carousel-theme', BB_POWERPACK_URL . 'assets/css/owl.theme.css', array( 'pp-owl-carousel' ), BB_POWERPACK_VER );
		wp_register_style( 'jquery-slick', BB_POWERPACK_URL . 'assets/css/slick.css', array(), '1.6.0' );
		wp_register_style( 'jquery-slick-theme', BB_POWERPACK_URL . 'assets/css/slick-theme.css', array( 'jquery-slick' ), '1.6.0' );
		wp_register_style( 'tablesaw', BB_POWERPACK_URL . 'assets/css/tablesaw.css', array(), '2.0.1' );
		wp_register_style( 'twentytwenty', BB_POWERPACK_URL . 'assets/css/twentytwenty.css', array() );
		wp_register_style( 'tooltipster', BB_POWERPACK_URL . 'assets/css/tooltipster.bundle.min.css', array() );

		wp_register_script( 'pp-facebook-sdk', pp_get_fb_sdk_url(), array(), '2.12', true );
		//wp_register_script( 'pp-twitter-widgets', BB_POWERPACK_URL . 'assets/js/twitter-widgets.js', array(), BB_POWERPACK_VER, true );
		wp_register_script( 'pp-twitter-widgets', 'https://platform.twitter.com/widgets.js', array(), BB_POWERPACK_VER, true );
		wp_register_script( 'instafeed', BB_POWERPACK_URL . 'assets/js/instafeed.min.js', array( 'jquery' ), BB_POWERPACK_VER, true );
		wp_register_script( 'jquery-instagramfeed', BB_POWERPACK_URL . 'assets/js/jquery.instagramFeed.js', array( 'jquery' ), '1.2.0', true );
		wp_register_script( 'jquery-isotope', BB_POWERPACK_URL . 'assets/js/isotope.pkgd.min.js', array( 'jquery' ), '3.0.1', true );
		wp_register_script( 'jquery-colorbox', BB_POWERPACK_URL . 'assets/js/jquery.colorbox.js', array( 'jquery' ), '1.6.3', true );
		wp_register_script( 'jquery-cookie', BB_POWERPACK_URL . 'assets/js/jquery.cookie.min.js', array( 'jquery' ), '1.4.1' );
		wp_register_script( 'pp-jquery-plugin', BB_POWERPACK_URL . 'assets/js/jquery.plugin.js', array( 'jquery' ), BB_POWERPACK_VER, true );
		wp_register_script( 'pp-jquery-countdown', BB_POWERPACK_URL . 'assets/js/jquery.countdown.js', array( 'jquery', 'pp-jquery-plugin' ), '2.0.2', true );
		wp_register_script( 'pp-jquery-fancybox', BB_POWERPACK_URL . 'assets/js/jquery.fancybox.min.js', array( 'jquery' ), '3.5.7', true );
		wp_register_script( 'jquery-justifiedgallery', BB_POWERPACK_URL . 'assets/js/jquery.justifiedGallery.min.js', array( 'jquery' ), '3.7.0', true );
		wp_register_script( 'jquery-swiper', BB_POWERPACK_URL . 'assets/js/swiper.min.js', array(), '8.4.7', true );
		wp_register_script( 'jquery-slick', BB_POWERPACK_URL . 'assets/js/slick.min.js', array( 'jquery' ), '1.6.0', true );
		wp_register_script( 'modernizr-custom', BB_POWERPACK_URL . 'assets/js/modernizr.custom.53451.js', array(), '3.6.0', true );
		wp_register_script( 'pp-owl-carousel', BB_POWERPACK_URL . 'assets/js/owl.carousel.min.js', array( 'jquery' ), BB_POWERPACK_VER, true );
		wp_register_script( 'tablesaw', BB_POWERPACK_URL . 'assets/js/tablesaw.js', array( 'jquery' ), '2.0.1', true );
		wp_register_script( 'twentytwenty', BB_POWERPACK_URL . 'assets/js/jquery.twentytwenty.js', array( 'jquery' ), '', true );
		wp_register_script( 'jquery-event-move', BB_POWERPACK_URL . 'assets/js/jquery.event.move.js', array( 'jquery' ), '2.0.0', true );
		wp_register_script( 'tooltipster', BB_POWERPACK_URL . 'assets/js/tooltipster.main.js', array( 'jquery' ), '', true );
		wp_register_script( 'pp-jquery-carousel', BB_POWERPACK_URL . 'assets/js/jquery-carousel.js', array( 'jquery' ), '', true );
		wp_register_script( 'pp-cluster', BB_POWERPACK_URL . 'assets/js/cluster.js', array( 'jquery' ), '', true );
		wp_register_script( 'jquery-carousel-ticker', BB_POWERPACK_URL . 'assets/js/jquery.carouselTicker.js', array( 'jquery' ), '', true );
	}

	/**
	 * Custom scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_scripts() {
		wp_enqueue_style( 'pp-animate' );
		if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
			wp_enqueue_style( 'pp-fields-style', BB_POWERPACK_URL . 'assets/css/fields.css', array(), BB_POWERPACK_VER );
			wp_enqueue_script( 'pp-fields-script', BB_POWERPACK_URL . 'assets/js/fields.js', array( 'jquery' ), BB_POWERPACK_VER, true );
			wp_enqueue_style( 'pp-panel-style', BB_POWERPACK_URL . 'assets/css/panel.css', array(), BB_POWERPACK_VER );
			wp_enqueue_script( 'pp-panel-script', BB_POWERPACK_URL . 'assets/js/panel.js', array( 'jquery' ), BB_POWERPACK_VER, true );

			wp_add_inline_script( 'g-recaptcha', 'var pp_recaptcha = ' . json_encode( array(
				'site_key' => BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_site_key' ),
				'v3_site_key' => BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_v3_site_key' ),
			) ) );

			wp_add_inline_script( 'h-captcha', 'var pp_hcaptcha = ' . json_encode( array(
				'site_key' => BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_hcaptcha_site_key' ),
			) ) );
		}
	}

	public function enqueue_block_editor_assets() {
		wp_enqueue_style( 'pp-fields-style', BB_POWERPACK_URL . 'assets/css/fields.css', array(), BB_POWERPACK_VER );
		wp_enqueue_script( 'pp-fields-script', BB_POWERPACK_URL . 'assets/js/fields.js', array( 'jquery' ), BB_POWERPACK_VER, true );
	}

	/**
	 * Custom inline scripts.
	 *
	 * @since 1.3
	 * @return void
	 */
	public function render_scripts() {
		$app_id = pp_get_fb_app_id();

		if ( $app_id ) {
			printf( '<meta property="fb:app_id" content="%s" />', esc_attr( $app_id ) );
		}

		if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
		?>
		<style>
		form[class*="fl-builder-pp-"] .fl-lightbox-header h1:before {
			content: "<?php echo pp_get_admin_label(); ?> " !important;
			position: relative;
			display: inline-block;
			margin-right: 5px;
		}
		</style>
		<?php
		}
		?>
		<script>
			var bb_powerpack = {
				version: '<?php echo BB_POWERPACK_VER; ?>',
				getAjaxUrl: function() { return atob( '<?php echo base64_encode( admin_url( 'admin-ajax.php' ) ); ?>' ); },
				callback: function() {},
				mapMarkerData: {},
				post_id: '<?php echo is_callable( 'FLBuilderModel::get_post_id' ) ? FLBuilderModel::get_post_id() : '0'; ?>',
				search_term: '<?php echo get_search_query(); ?>',
				current_page: '<?php echo esc_url( network_home_url( $_SERVER['REQUEST_URI'] ) ); ?>',
				conditionals: {
					is_front_page: <?php echo is_front_page() ? 'true' : 'false'; ?>,
					is_home: <?php echo is_home() ? 'true' : 'false'; ?>,
					is_archive: <?php echo ( is_archive() || is_post_type_archive() ) ? 'true' : 'false'; ?>,
					current_post_type: '<?php echo is_post_type_archive() ? get_post_type() : ''; ?>',
					is_tax: <?php echo ( is_tax() || is_category() ) ? 'true' : 'false'; ?>,
					<?php if ( isset( get_queried_object()->taxonomy ) && isset( get_queried_object()->slug ) ) { ?>
					current_tax: '<?php echo get_queried_object()->taxonomy; ?>',
					current_term: '<?php echo get_queried_object()->slug; ?>',
					<?php } ?>
					is_author: <?php echo is_author() ? 'true' : 'false'; ?>,
					current_author: <?php echo is_author() ? get_queried_object()->ID : 'false'; ?>,
					is_search: <?php echo is_search() ? 'true' : 'false'; ?>,
					<?php if ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) { ?>
					orderby: '<?php echo esc_attr( wp_unslash( $_GET['orderby'] ) ); ?>',
					<?php } ?>
				}
			};
		</script>
		<?php
	}

	public function render_admin_scripts() {
		?>
		<script>
			var bb_powerpack = {
				ajaxurl: '<?php echo admin_url( 'admin-ajax.php' ); ?>'
			};
		</script>
		<?php
	}

	/**
	 * Admin notices.
	 *
	 * @since 1.1.1
	 * @return void
	 */
	public function admin_notices() {
		if ( ! is_admin() ) {
			return;
		} elseif ( ! is_user_logged_in() ) {
			return;
		} elseif ( ! current_user_can( 'update_core' ) ) {
			return;
		}

		// print any message returned by license activator.
		if ( isset( $_GET['sl_activation'] ) && isset( $_GET['message'] ) ) {
			if ( isset( $_GET['page'] ) && 'ppbb-settings' == $_GET['page'] ) {
				self::$errors[] = esc_attr( wp_unslash( $_GET['message'] ) );
			}
		}

		if ( ! class_exists( 'FLBuilder' ) ) {
			$bb_lite = '<a href="https://wordpress.org/plugins/beaver-builder-lite-version/" target="_blank">Beaver Builder Lite</a>';
			$bb_pro = '<a href="https://www.wpbeaverbuilder.com/pricing/" target="_blank">Beaver Builder Pro / Agency</a>';
			// translators: %1$s for Beaver Builder Lite link and %2$s for Beaver Builder Pro link.
			self::$errors[] = sprintf( esc_html__( 'Please install and activate %1$s or %2$s to use PowerPack add-on.', 'bb-powerpack' ), $bb_lite, $bb_pro );
		}

		if ( defined( 'FL_BUILDER_VERSION' ) && version_compare( FL_BUILDER_VERSION, '2.2.0', '<' ) ) {
			self::$errors[] = esc_html__( 'It seems Beaver Builder plugin is out dated. PowerPack requires Beaver Builder 2.2 or higher.', 'bb-powerpack' );
		}

		if ( count( self::$errors ) ) {
			foreach ( self::$errors as $key => $msg ) {
				?>
				<div class="notice notice-error">
					<p><?php echo $msg; ?></p>
				</div>
				<?php
			}
		}
	}

	/**
	 * Add CSS class to body.
	 *
	 * @since 1.1.1
	 * @param array $classes	Array of body CSS classes.
	 * @return array $classes	Array of body CSS classes.
	 */
	public function body_class( $classes ) {
		if ( class_exists( 'FLBuilder' ) && class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
			$classes[] = 'bb-powerpack';
			if ( class_exists( 'FLBuilderUIContentPanel' ) ) {
				$classes[] = 'bb-powerpack-ui';
			}
		}

		return $classes;
	}

	/**
	 * Filter to add plugins to the RankMath TOC list.
	 *
	 * @since 2.12.5
	 * @param array TOC plugins.
	 */
	public function rank_math_toc_plugins( $toc_plugins ) {
		$toc_plugins[ BB_POWERPACK_PATH ] = 'PowerPack for Beaver Builder';
		return $toc_plugins;
	}

	public function mainwp_detect_premium_plugins_update( $premiums ) {
		$premiums[] = 'bbpowerpack/bb-powerpack.php';
  		return $premiums;
	}

	/**
	 * Register white label category
	 *
	 * @since 1.0.1
	 * @return string $ppwl
	 */
	public function register_wl_cat() {
		$ppwl = ( is_multisite() ) ? get_site_option( 'ppwl_builder_label' ) : get_option( 'ppwl_builder_label' );

		if ( '' == $ppwl || false == $ppwl ) {
			$ppwl = esc_html__( 'PowerPack Modules' );
		}

		return $ppwl;
	}

	public function reset_hide_plugin() {
		if ( ! is_admin() ) {
			return;
		}

		if ( isset( $_GET['pp_reset_wl_plugin'] ) ) {
			delete_option( 'ppwl_hide_plugin' );
			delete_site_option( 'ppwl_hide_plugin' );
		}
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 * @return object The BB_PowerPack object.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BB_PowerPack ) ) {
			self::$instance = new BB_PowerPack();
		}

		return self::$instance;
	}

}

// Load the PowerPack class.
function BB_POWERPACK() { // @codingStandardsIgnoreLine.
	return BB_PowerPack::get_instance();
}

BB_POWERPACK();

/**
 * Enable white labeling setting form after re-activating the plugin
 *
 * @since 1.0.1
 * @return void
 */
function bb_powerpack_plugin_activation() {
	delete_option( 'ppwl_hide_form' );
	delete_option( 'ppwl_hide_plugin' );
	if ( get_option( 'bb_powerpack_templates_reset' ) != 1 ) {
		delete_option( 'bb_powerpack_override_ms' );
		update_option( 'bb_powerpack_templates', array( 'disabled' ) );
		update_option( 'bb_powerpack_page_templates', array( 'disabled' ) );
		update_option( 'bb_powerpack_templates_reset', 1 );
	}
	if ( is_network_admin() ) {
		delete_site_option( 'ppwl_hide_form' );
		delete_site_option( 'ppwl_hide_plugin' );
		if ( get_site_option( 'bb_powerpack_templates_reset' ) != 1 ) {
			delete_site_option( 'bb_powerpack_override_ms' );
			update_site_option( 'bb_powerpack_templates', array( 'disabled' ) );
			update_site_option( 'bb_powerpack_page_templates', array( 'disabled' ) );
			update_site_option( 'bb_powerpack_templates_reset', 1 );
		}
	}
}
register_activation_hook( __FILE__, 'bb_powerpack_plugin_activation' );
