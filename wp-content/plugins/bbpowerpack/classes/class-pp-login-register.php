<?php
/**
 * Handles logic for login and registration pages.
 *
 * @package BB_PowerPack
 * @since 2.7.11
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BB_PowerPack_Login_Register.
 */
final class BB_PowerPack_Login_Register {
	/**
	 * Settings Tab constant.
	 */
	const SETTINGS_TAB = 'login_register';

	private static $cached_data = array();

	/**
	 * Initializing PowerPack maintenance mode.
	 *
	 * @since 2.7.11
	 */
	static public function init() {
		add_filter( 'pp_admin_settings_tabs', 	__CLASS__ . '::render_settings_tab', 10, 1 );
		add_action( 'pp_admin_settings_save', 	__CLASS__ . '::save_settings' );
		add_action( 'template_redirect',		__CLASS__ . '::page_headers' );
		add_action( 'login_init', 				__CLASS__ . '::redirect' );
		add_filter( 'login_url',				__CLASS__ . '::login_url', 10, 3 );
		//add_action( 'init', 					__CLASS__ . '::login_redirect' );
		add_filter( 'authenticate', 			__CLASS__ . '::auth_redirect', 10, 3 );
		add_action( 'wp_logout', 				__CLASS__ . '::logout_redirect' );
		add_action( 'wp_loaded',				__CLASS__ . '::check_reauth' );
	}

	/**
	 * Render settings tab.
	 *
	 * Adds Login / Register tab in PowerPack admin settings.
	 *
	 * @since 2.7.11
	 * @param array $tabs Array of existing settings tabs.
	 */
	static public function render_settings_tab( $tabs ) {
		$tabs[ self::SETTINGS_TAB ] = array(
			'title'				=> esc_html__( 'Login / Register', 'bb-powerpack' ),
			'icon' 				=> '<span class="dashicons dashicons-admin-users"></span>',
			'show'				=> ! BB_PowerPack_Admin_Settings::get_option( 'ppwl_hide_login_register_tab' ),
			'file'				=> BB_POWERPACK_DIR . 'includes/admin-settings-login-register.php',
			'priority'			=> 350,
		);

		return $tabs;
	}

	/**
	 * Save settings.
	 *
	 * Saves setting fields value in options.
	 *
	 * @since 2.6.10
	 */
	static public function save_settings() {
		if ( isset( $_POST['bb_powerpack_login_page'] ) ) {
			$login_page = wp_unslash( $_POST['bb_powerpack_login_page'] );
			self::update_option( 'bb_powerpack_login_page', $login_page );
		}
		if ( isset( $_POST['bb_powerpack_register_page'] ) ) {
			$register_page = wp_unslash( $_POST['bb_powerpack_register_page'] );
			self::update_option( 'bb_powerpack_register_page', $register_page );
		}

		if ( isset( $_POST['bb_powerpack_login_page'] ) || isset( $_POST['bb_powerpack_register_page'] ) ) {
			flush_rewrite_rules();
		}
	}

	static private function update_option( $key, $value ) {
		$is_network_admin = is_multisite() && is_network_admin();

		if ( $is_network_admin ) {
			update_site_option( $key, $value );
		} else {
			update_option( $key, $value );
		}
	}

	/**
	 * Get pages.
	 *
	 * Get all pages and create options for select field.
	 *
	 * @since 2.7.11
	 * @param string $selected 	Selected page for the field.
	 * @return array $options	An array of pages.
	 */
	static public function get_pages( $selected = '' ) {
		if ( empty( self::$cached_data ) ) {
			$args = array(
				'post_type' 		=> 'page',
				'post_status'		=> 'publish',
				'orderby' 			=> 'title',
				'order' 			=> 'ASC',
				'posts_per_page' 	=> '-1',
				'update_post_meta_cache' => false
			);
	
			self::$cached_data = get_posts( $args );
		}

		$options = '<option value="">' . __( '-- Select --', 'bb-powerpack' ) . '</option>';

		if ( count( self::$cached_data ) ) {
			foreach ( self::$cached_data as $post ) {
				$options .= '<option value="' . $post->ID . '" ' . selected( $selected, $post->ID, false ) . '>' . $post->post_title . '</option>';
			}
		} else {
			$options = '<option value="" disabled>' . __( 'No pages found!', 'bb-powerpack' ) . '</option>';
		}

		return $options;
	}

	static public function get_login_page_id() {
		$id = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_login_page', true );

		return absint( $id );
	}

	static public function get_register_page_id() {
		$id = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_register_page', true );

		return absint( $id );
	}

	/**
	 * Page Headers.
	 *
	 * Prevent caching on the custom login page. 
	 *
	 * @since 2.36.x
	 * @return void
	 */
	static public function page_headers() {
		$login_page_id = self::get_login_page_id();

		if ( empty( $login_page_id ) || ! is_page( $login_page_id ) ) {
			return;
		}

		if ( ! headers_sent() ) {
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
			if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
				define( 'DONOTCACHEOBJECT', true );
			}
			if ( ! defined( 'DONOTCACHEDB' ) ) {
				define( 'DONOTCACHEDB', true );
			}
			nocache_headers();
		}
	}

	/**
	 * Redirect.
	 *
	 * Redirects wp-login.php to custom login page or register page.
	 *
	 * @since 2.7.11
	 * @return void
	 */
	static public function redirect() {
		$redirect_to = '';
		$action = isset( $_GET['action'] ) ? $_GET['action'] : '';

		if ( isset( $_REQUEST['interim-login'] ) ) {
			return;
		}
		if ( 'postpass' === $action ) {
			return;
		}

		if ( 'register' === $action ) {
			$page_id     = self::get_register_page_id();
			$redirect_to = self::get_permalink( $page_id );
			if ( ! empty( $page_id ) && ! empty( $redirect_to ) ) {
				wp_redirect( $redirect_to );
				exit;
			}
		} else {
			if ( ! is_user_logged_in() ) {
				$page_id     = self::get_login_page_id();
				$redirect_to = self::get_permalink( $page_id );
				if ( 'lostpassword' === $action || 'retrievepassword' === $action ) {
					$redirect_to = add_query_arg( 'action', 'lost_pass', $redirect_to );
				}
				if ( 'rp' === $action || 'resetpass' === $action ) {
					$query_args = array(
						'action' => 'reset_pass',
					);

					if ( isset( $_GET['key'] ) ) {
						$query_args['key'] = wp_unslash( $_GET['key'] );
					}
					if ( isset( $_GET['login'] ) ) {
						$user_name = wp_unslash( $_GET['login'] );
						$user = get_user_by( 'login', $user_name );
						if ( $user && ! is_wp_error( $user ) ) {
							$query_args['id'] = $user->ID;
						}
					}

					$redirect_to = add_query_arg( $query_args, $redirect_to );
				}
				if ( ! empty( $page_id ) && ! empty( $redirect_to ) ) {
					wp_redirect( $redirect_to );
					exit;
				}
			}
		}
	}

	static public function login_url( $login_url, $redirect, $force_reauth ) {
		$page_id = self::get_login_page_id();
		
		if ( ! empty( $page_id ) ) {
			$login_url = self::get_permalink( $page_id );

			if ( ! empty( $redirect ) ) {
				$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
			}
		
			if ( $force_reauth ) {
				$login_url = add_query_arg( 'reauth', '1', $login_url );
			}
		}

		return $login_url;
	}

	/**
	 * Login redirect.
	 *
	 * Redirects wp-login.php to custom login page.
	 *
	 * @since 2.7.11
	 * @return void
	 */
	static public function login_redirect() {
		$redirect_to = '';

		if (
			'wp-login.php' == basename( $_SERVER['REQUEST_URI'] ) && 
			'GET' == $_SERVER['REQUEST_METHOD']
		) {
			$page_id = '';

			if ( isset( $_GET['action'] ) && 'register' == $_GET['action'] ) {
				$page_id = self::get_register_page_id();
			} else {
				$page_id = self::get_login_page_id();
			}

			if ( ! empty( $page_id ) ) {
				$redirect_to = self::get_permalink( $page_id );
			}
		}

		if ( ! empty( $redirect_to ) ) {
			wp_redirect( $redirect_to );
			exit;
		}
	}

	/**
	 * Authentication redirect.
	 *
	 * Redirect to custom login page if username and password fields
	 * left empty.
	 *
	 * @since 2.7.11
	 * @param object $user 		User object.
	 * @param string $username 	User's login name.
	 * @param string $password 	User's login password.
	 * @return object $user
	 */
	static public function auth_redirect( $user, $username, $password ) {
		if ( isset( $_REQUEST['interim-login'] ) ) {
			return $user;
		}

		if ( empty( $username ) || empty( $password ) ) {
			$id = self::get_login_page_id();
		
			if ( ! empty( $id ) ) {
				$login_page = self::get_permalink( $id );

				wp_redirect( $login_page );
				exit;
			}
		}

		return $user;
	}

	/**
	 * Logout redirect.
	 *
	 * Redirects to login page after succesful logout.
	 *
	 * @since 2.7.11
	 * @return void
	 */
	static public function logout_redirect() {
		$id = self::get_login_page_id();
		
		if ( ! empty( $id ) ) {
			$login_page = self::get_permalink( $id );

			wp_redirect( $login_page );
			exit;
		}
	}

	static public function check_reauth() {
		$id = self::get_login_page_id();

		if ( ! empty( $id ) && isset( $_GET['redirect_to'] ) && isset( $_GET['reauth'] ) ) {
			if ( ! empty( $_GET['redirect_to'] ) && $_GET['reauth'] ) {
				// Clear any stale cookies.
				wp_clear_auth_cookie();
			}
		}
	}

	static public function get_permalink( $page_id ) {
		if ( isset( $GLOBALS['wp_rewrite'] ) ) {
			$permalink = get_permalink( $page_id );
		} else {
			$permalink = add_query_arg( 'p', $page_id, home_url() );
		}

		return $permalink;
	}
}

// Initialize the class.
BB_PowerPack_Login_Register::init();