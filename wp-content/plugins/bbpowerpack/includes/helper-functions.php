<?php

if ( ! function_exists( 'pp_set_error' ) ) {
	/**
	 * Error messages.
	 *
	 * @since 1.2.0
	 * @return mixed
	 */
	function pp_set_error( $key ) {
		$errors = array(
			'fetch_error'      	=> esc_html__( 'Unable to fetch template data. Please click on the "Reload" button.', 'bb-powerpack' ),
			'connection_lost'	=> esc_html__( 'Error donwloading template data. Please check your internet connection and click on the "Reload" button.', 'bb-powerpack' ),
		);
		if ( isset( $errors[$key] ) && ! isset( BB_PowerPack::$errors[$key] ) ) {
			BB_PowerPack::$errors[$key] = $errors[$key];
		}
	}
}

if ( ! function_exists( 'pp_is_ssl' ) ) {
	/**
	 * Checks to see if the site has SSL enabled or not.
	 *
	 * @since 1.2.1
	 * @return bool
	 */
	function pp_is_ssl() {
		if ( is_ssl() ) {
			return true;
		}
		else if ( 0 === stripos( get_option( 'siteurl' ), 'https://' ) ) {
			return true;
		}
		else if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'pp_get_upload_dir' ) ) {
	/**
	 * Returns an array of paths for the upload directory
	 * of the current site.
	 *
	 * @since 1.1.7
	 * @return array
	 */
	function pp_get_upload_dir() {
		$wp_info = wp_upload_dir();

		// Get main upload directory for every sub-sites.
		if ( is_multisite() ) {
			switch_to_blog(1);
			$wp_info = wp_upload_dir();
			restore_current_blog();
		}

		$dir_name = basename( BB_POWERPACK_DIR );

		// SSL workaround.
		if ( pp_is_ssl() ) {
			$wp_info['baseurl'] = str_ireplace( 'http://', 'https://', $wp_info['baseurl'] );
		}

		// Build the paths.
		$dir_info = array(
			'path'	 => $wp_info['basedir'] . '/' . $dir_name . '/',
			'url'	 => $wp_info['baseurl'] . '/' . $dir_name . '/'
		);

		// Create .htaccess file for security.
		$htaccess = '<FilesMatch "\.(php|php\.)$">';
		$htaccess .= "\n\r";
		$htaccess .= 'Order Allow,Deny';
		$htaccess .= "\n\r";
		$htaccess .= 'Deny from all';
		$htaccess .= "\n\r";
		$htaccess .= '</FilesMatch>';

		// Create the upload dir if it doesn't exist.
		if ( function_exists( 'fl_builder_filesystem' ) ) {
			if ( ! fl_builder_filesystem()->file_exists( $dir_info['path'] ) ) {

				// Create the directory.
				fl_builder_filesystem()->mkdir( $dir_info['path'] );

				// Add an index file for security.
				fl_builder_filesystem()->file_put_contents( $dir_info['path'] . 'index.html', '' );

				// Add .htaccess file.
				fl_builder_filesystem()->file_put_contents( $dir_info['path'] . '.htaccess', $htaccess );
			}
		} else {
			if ( ! file_exists( $dir_info['path'] ) ) {

				// Create the directory.
				mkdir( $dir_info['path'] );

				// Add an index file for security.
				file_put_contents( $dir_info['path'] . 'index.html', '' );

				// Add .htaccess file.
				file_put_contents( $dir_info['path'] . '.htaccess', $htaccess );
			}
		}

		return $dir_info;
	}
}

if ( ! function_exists( 'pp_row_templates_categories' ) ) {
	/**
	 * Row templates categories
	 */
	function pp_row_templates_categories() {
		$cats = array(
			'pp-contact-blocks'  => __('Contact Blocks', 'bb-powerpack'),
			'pp-contact-forms'   => __('Contact Forms', 'bb-powerpack'),
			'pp-call-to-action'  => __('Call To Action', 'bb-powerpack'),
			'pp-hero'            => __('Hero', 'bb-powerpack'),
			'pp-heading'         => __('Heading', 'bb-powerpack'),
			'pp-subscribe-forms' => __('Subscribe Forms', 'bb-powerpack'),
			'pp-content'         => __('Content', 'bb-powerpack'),
			'pp-blog-posts'      => __('Blog Posts', 'bb-powerpack'),
			'pp-lead-generation' => __('Lead Generation', 'bb-powerpack'),
			'pp-logos'           => __('Logos', 'bb-powerpack'),
			'pp-faq'             => __('FAQ', 'bb-powerpack'),
			'pp-team'            => __('Team', 'bb-powerpack'),
			'pp-testimonials'    => __('Testimonials', 'bb-powerpack'),
			'pp-features'        => __('Features', 'bb-powerpack'),
			'pp-services'        => __('Services', 'bb-powerpack'),
			'pp-header'          => __('Header', 'bb-powerpack'),
			'pp-footer'          => __('Footer', 'bb-powerpack'),
		);

		if ( is_array( $cats ) ) {
			ksort( $cats );
		}

		return $cats;
	}
}

if ( ! function_exists( 'pp_templates_categories' ) ) {
	/**
	 * Templates categories
	 */
	function pp_templates_categories( $type ) {
		$templates = BB_PowerPack_Templates_Lib::get_templates_data( $type );
		$data = array();

		if ( is_array( $templates ) ) {
			foreach ( $templates as $cat => $info ) {
				$data[ $cat ] = array(
					'title'		=> $info['name'],
					'type'		=> $info['type'],
				);
				if ( isset( $info['count'] ) ) {
					$data[ $cat ]['count'] = $info['count'];
				}
			}

			$data = array_reverse( $data );
		}

		return $data;
	}
}

if ( ! function_exists( 'pp_template_filters' ) ) {
	/**
	 * Templates filters
	 */
	function pp_template_filters() {
		$filters = array(
			'all'				 => __( 'All', 'bb-powerpack' ),
			'home'				 => __( 'Home', 'bb-powerpack' ),
			'about'				 => __( 'About', 'bb-powerpack' ),
			'contact'			 => __( 'Contact', 'bb-powerpack' ),
			'landing'			 => __( 'Landing', 'bb-powerpack' ),
			'sales'				 => __( 'Sales', 'bb-powerpack' ),
			'coming-soon'		 => __( 'Coming Soon', 'bb-powerpack' ),
			'login'				 => __( 'Login', 'bb-powerpack' ),
			'under-construction' => __( 'Under Construction', 'bb-powerpack' ),
		);

		return $filters;
	}
}

if ( ! function_exists( 'pp_templates_src' ) ) {
	/**
	 * Templates source URL
	 */
	function pp_templates_src( $type = 'page', $category = '' ) {
		if ( ! is_admin() ) {
			return;
		}

		$src = array();
		$url = 'https://ppbeaver.s3.amazonaws.com/data/';

		if ( $type == 'row' ) {
			$mode = 'color';
			$url  = $url . $mode . '/';
		}

		foreach ( pp_templates_categories( $type ) as $slug => $title ) {
			$src[ $slug ] = $url . $slug . '.dat';
		}

		if ( '' != $category && isset( $src[ $category ] ) ) {
			return $src[ $category ];
		}

		return $src;
	}
}

if ( ! function_exists( 'pp_templates_preview_src' ) ) {
	/**
	 * Templates demo source URL
	 */
	function pp_templates_preview_src( $type = 'page', $category = '' ) {
		$templates = BB_PowerPack_Templates_Lib::get_templates_data( $type );
		$url  = 'https://wpbeaveraddons.com/page-templates/';
		$data = array();

		if ( is_array( $templates ) ) {

			foreach ( $templates as $cat => $info ) {
				$data[ $cat ] = $info['slug'];
			}

		}

		if ( '' == $category ) {
			return $data;
		}

		if ( isset( $data[ $category ] ) ) {
			return $data[ $category ];
		}

		return $url;
	}
}

if ( ! function_exists( 'pp_get_template_screenshot_url' ) ) {
	/**
	 * Get template screenshot URL.
	 */
	function pp_get_template_screenshot_url( $type, $category, $mode = '' ) {
		$url = 'https://ppbeaver.s3.amazonaws.com/assets/400x400/';

		return $url . $category . '.jpg';
	}
}

if ( ! function_exists( 'pp_modules' ) ) {
	/**
	 * Modules
	 */
	function pp_modules() {
		foreach( FLBuilderModel::$modules as $module ) {
			if ( $module->category == BB_POWERPACK_CAT ) {
				$slug = is_object( $module ) ? $module->slug : $module['slug'];
				$modules[ $slug ] = $module->name;
			}
		}

		return $modules;
	}
}

if ( ! function_exists( 'pp_extensions' ) ) {
	/**
	 * Row and Column Extensions
	 */
	function pp_extensions() {
		$extensions = array(
			'row' => array(
				'separators' => array(
					'label'       => __( 'Separators', 'bb-powerpack' ),
					'description' => __( 'Row separators can be added to the top, bottom or both the ends of a row.', 'bb-powerpack' ),
				),
				'overlay' => array(
					'label'       => __( 'Overlay Style', 'bb-powerpack' ),
					'description' => __( 'Choose overlay pattern among Half Overlay Left or Right, Vertical Angled Left or Right.', 'bb-powerpack' ),
				),
				'expandable' => array(
					'label'       => __( 'Expandable', 'bb-powerpack'),
					'description' => __( 'This feature lets you toggle the entire row on just a single click.', 'bb-powerpack' ),
				),
				'downarrow' => array(
					'label'       => __( 'Down Arrow', 'bb-powerpack'),
					'description' => __( 'This feature will add an arrow icon button at the bottom of a row which let users jump to the next row by clicking on it.', 'bb-powerpack' ),
				),
				'background_effect'	=> array(
					'label'       => __( 'Background Effects', 'bb-powerpack'),
					'description' => __( 'This feature includes 13 types of amazing animated background for row. These animations consist of an extensive list of styling options.', 'bb-powerpack' ),
				),
			),
			'col' => array(
				'separators' => array(
					'label'       => __( 'Separators', 'bb-powerpack' ),
					'description' => __( 'Just like row separators, this feature adds various shapes for column.', 'bb-powerpack' ),
				),
			)
		);

		return $extensions;
	}
}

if ( ! function_exists( 'pp_hex2rgba' ) ) {
	/**
	 * Hex to Rgba
	 */
	function pp_hex2rgba( $hex, $opacity = 1 ) {
		if ( stristr( $hex, 'rgb' ) || stristr( $hex, 'var' ) ) {
			return $hex;
		}
		
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr($hex,0,1).substr($hex,0,1) );
			$g = hexdec( substr($hex,1,1).substr($hex,1,1) );
			$b = hexdec( substr($hex,2,1).substr($hex,2,1) );
		} else {
			$r = hexdec( substr($hex,0,2) );
			$g = hexdec( substr($hex,2,2) );
			$b = hexdec( substr($hex,4,2) );
		}
		$opacity = ( $opacity > 1 ) ? ( $opacity / 100 ) : $opacity;
		$rgba = array( $r, $g, $b, $opacity );

		return 'rgba(' . implode( ', ', $rgba ) . ')';
	}
}

if ( ! function_exists( 'pp_get_color_value' ) ) {
	/**
	 * Get color value hex or rgba
	 */
	function pp_get_color_value( $color ) {
		if ( is_callable( 'FLBuilderColor::hex_or_rgb' ) ) {
			return FLBuilderColor::hex_or_rgb( $color );
		}
		if ( ! empty( $color ) && ! stristr( $color, 'rgb' ) && ! stristr( $color, 'var' ) ) {
			return '#' . $color;
		} else {
			return $color;
		}
	}
}

if ( ! function_exists( 'pp_long_day_format' ) ) {
	/**
	 * Returns long day format.
	 *
	 * @since 1.2.2
	 * @param string $day
	 * @return mixed
	 */
	function pp_long_day_format( $day = '' ) {
		$days = array(
			'Sunday'    => __( 'Sunday', 'bb-powerpack' ),
			'Monday'    => __( 'Monday', 'bb-powerpack' ),
			'Tuesday'   => __( 'Tuesday', 'bb-powerpack' ),
			'Wednesday' => __( 'Wednesday', 'bb-powerpack' ),
			'Thursday'  => __( 'Thursday', 'bb-powerpack' ),
			'Friday'    => __( 'Friday', 'bb-powerpack' ),
			'Saturday'  => __( 'Saturday', 'bb-powerpack' ),
		);

		if ( isset( $days[ $day ] ) ) {
			return $days[ $day ];
		}

		return $days;
	}
}

if ( ! function_exists( 'pp_short_day_format' ) ) {
	/**
	 * Returns short day format.
	 *
	 * @since 1.2.2
	 * @param string $day
	 * @return string
	 */
	function pp_short_day_format( $day ) {
		$days = array(
			'Sunday'    => __( 'Sun', 'bb-powerpack' ),
			'Monday'    => __( 'Mon', 'bb-powerpack' ),
			'Tuesday'   => __( 'Tue', 'bb-powerpack' ),
			'Wednesday' => __( 'Wed', 'bb-powerpack' ),
			'Thursday'  => __( 'Thu', 'bb-powerpack' ),
			'Friday'    => __( 'Fri', 'bb-powerpack' ),
			'Saturday'  => __( 'Sat', 'bb-powerpack' ),
		);

		if ( isset( $days[ $day ] ) ) {
			return $days[ $day ];
		}
	}
}

if ( ! function_exists( 'pp_get_date_formats' ) ) {
	/**
	 * Date formats.
	 */
	function pp_get_date_formats() {
		return array(
			''       => __( 'Default', 'bb-powerpack' ),
			'F j, Y' => current_time( 'F j, y' ),
			'Y-m-d'  => current_time( 'Y-m-d' ),
			'm/d/Y'  => current_time( 'm/d/Y' ),
			'd/m/Y'  => current_time( 'd/m/Y' ),
		);
	}
}

if ( ! function_exists( 'pp_is_tribe_events_post' ) ) {
	/**
	 * Check if it is tribe event post.
	 */
	function pp_is_tribe_events_post( $post_id ) {
		return 'tribe_events' === get_post_type( $post_id );
	}
}

if ( ! function_exists( 'pp_get_user_agent' ) ) {
	/**
	 * Returns user agent.
	 *
	 * @since 1.2.4
	 * @return string
	 */
	function pp_get_user_agent() {
		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return;
		}

		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		if (stripos( $user_agent, 'Chrome') !== false) {
			return 'chrome';
		} elseif (stripos( $user_agent, 'Safari') !== false) {
			return 'safari';
		} elseif (stripos( $user_agent, 'Firefox') !== false) {
			return 'firefox';
		} elseif (stripos( $user_agent, 'MSIE') !== false) {
			return 'ie';
		} elseif (stripos( $user_agent, 'Trident/7.0; rv:11.0' ) !== false) {
			return 'ie';
		}

		return;
	}
}

if ( ! function_exists( 'pp_get_client_ip' ) ) {
	/**
	 * Return client IP address.
	 */
	function pp_get_client_ip() {
		$keys = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
				return $_SERVER[ $key ];
			}
		}

		// fallback IP address.
		return '127.0.0.1';
	}
}

if ( ! function_exists( 'pp_get_client_details' ) ) {
	/**
	 * Get client IP address and user agent.
	 */
	function pp_get_client_details() {
		$ip = pp_get_client_ip();

		$user_agent = pp_get_user_agent();

		return array(
			'ip'			=> $ip,
			'user_agent'	=> $user_agent
		);
	}
}

if ( ! function_exists( 'pp_get_modules_categories' ) ) {
	/**
	 * Module categories.
	 *
	 * @param string $cat
	 * @return mixed
	 */
	function pp_get_modules_categories( $cat = '' ) {
		if ( isset( $_GET['tab'] ) && 'modules' === $_GET['tab'] ) {
			$admin_label = '';
		} else {
			$admin_label = ' - ' . pp_get_admin_label();
		}

		$cats = array(
			'creative'   => sprintf( __('Creative Modules%s', 'bb-powerpack'), $admin_label ),
			'content'    => sprintf( __('Content Modules%s', 'bb-powerpack'), $admin_label ),
			'form_style' => sprintf( __('Form Styler Modules%s', 'bb-powerpack'), $admin_label ),
			'lead_gen'   => sprintf( __('Lead Generation Modules%s', 'bb-powerpack'), $admin_label ),
			'media'      => sprintf( __('Media Modules%s', 'bb-powerpack'), $admin_label ),
			'social'     => sprintf( __('Social Media Modules%s', 'bb-powerpack'), $admin_label ),
		);

		if ( empty( $cat ) ) {
			return $cats;
		}

		if ( isset( $cats[ $cat ] ) ) {
			return $cats[ $cat ];
		} else {
			return $cat;
		}
	}
}

if ( ! function_exists( 'pp_get_modules_cat' ) ) {
	/**
	 * Returns modules category name for Beaver Builder 2.0 compatibility.
	 *
	 * @since 1.3
	 * @return string
	 */
	function pp_get_modules_cat( $cat ) {
		return class_exists( 'FLBuilderUIContentPanel' ) ? pp_get_modules_categories( $cat ) : BB_POWERPACK_CAT;
	}
}

if ( ! function_exists( 'pp_get_admin_label' ) ) {
	/**
	 * Returns admin label for PowerPack settings.
	 *
	 * @since 1.3
	 * @return string
	 */
	function pp_get_admin_label() {
		$admin_label = BB_PowerPack_Admin_Settings::get_option( 'ppwl_admin_label' );
		$admin_label = trim( $admin_label ) !== '' ? trim( $admin_label ) : 'PowerPack';

		return $admin_label;
	}
}

if ( ! function_exists( 'pp_get_modules_group' ) ) {
	/**
	 * Returns group name for BB 2.x.
	 *
	 * @since 1.5
	 * @return string
	 */
	function pp_get_modules_group() {
		$list_with_standard = BB_PowerPack_Admin_Settings::get_option( 'ppwl_list_modules_with_standard' );

		if ( $list_with_standard ) {
			return '';
		}

		$group_name = BB_PowerPack_Admin_Settings::get_option( 'ppwl_builder_label' );
		$group_name = trim( $group_name ) !== '' ? trim( $group_name ) : 'PowerPack ' . __('Modules', 'bb-powerpack');

		return $group_name;
	}
}

if ( ! function_exists( 'pp_get_module_dir' ) ) {
	/**
	 * Returns path of the module.
	 *
	 * @since 2.3
	 * @return string
	 */
	function pp_get_module_dir( $module = '' ) {
		if ( empty( $module ) ) {
			return;
		}

		$theme_dir = '';
		$module_dir = '';
		$module_path = 'modules/' . $module;

		if ( is_child_theme() ) {
			$theme_dir = get_stylesheet_directory();
		} else {
			$theme_dir = get_template_directory();
		}

		if ( file_exists( $theme_dir . '/bb-powerpack/' . $module_path ) ) {
			$module_dir = $theme_dir . '/bb-powerpack/' . $module_path;
		}
		elseif ( file_exists( $theme_dir . '/bbpowerpack/' . $module_path ) ) {
			$module_dir = $theme_dir . '/bbpowerpack/' . $module_path;
		}
		else {
			$module_dir = BB_POWERPACK_DIR . $module_path;
		}

		return $module_dir . '/';
	}
}

if ( ! function_exists( 'pp_get_module_url' ) ) {
	/**
	 * Returns URL of the module.
	 *
	 * @since 2.3
	 * @return string
	 */
	function pp_get_module_url( $module = '' ) {
		if ( empty( $module ) ) {
			return;
		}

		$theme_dir = '';
		$theme_url = '';
		$module_url = '';
		$module_path = 'modules/' . $module;

		if ( is_child_theme() ) {
			$theme_dir = get_stylesheet_directory();
			$theme_url = get_stylesheet_directory_uri();
		} else {
			$theme_dir = get_template_directory();
			$theme_url = get_template_directory_uri();
		}

		if ( file_exists( $theme_dir . '/bb-powerpack/' . $module_path ) ) {
			$module_url = trailingslashit( $theme_url ) . 'bb-powerpack/' . $module_path;
		}
		elseif ( file_exists( $theme_dir . '/bbpowerpack/' . $module ) ) {
			$module_url = trailingslashit( $theme_url ) . 'bbpowerpack/' . $module_path;
		}
		else {
			$module_url = BB_POWERPACK_URL . $module_path;
		}

		return trailingslashit( $module_url );
	}
}

if ( ! function_exists( 'pp_get_fb_app_id' ) ) {
	/**
	 * Returns Facebook App ID stored in options.
	 *
	 * @since 2.4
	 * @return mixed
	 */
	function pp_get_fb_app_id() {
		$app_id = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_fb_app_id' );

		return $app_id;
	}
}

if ( ! function_exists( 'pp_get_fb_sdk_url' ) ) {
	/**
	 * Build the URL of Facebook SDK.
	 *
	 * @since 2.4
	 * @return string
	 */
	function pp_get_fb_sdk_url( $app_id = '' ) {
		$app_id = empty( $app_id ) ? pp_get_fb_app_id() : $app_id;
		$api_ver = apply_filters( 'pp_fb_api_version', '11.0' );
		
		if ( $app_id && ! empty( $app_id ) ) {
			return sprintf( 'https://connect.facebook.net/%s/sdk.js#xfbml=1&version=v%s&appId=%s', get_locale(), $api_ver, $app_id );
		}

		return sprintf( 'https://connect.facebook.net/%s/sdk.js#xfbml=1&version=v%s', get_locale(), $api_ver );
	}
}

if ( ! function_exists( 'pp_get_fb_app_settings_url' ) ) {
	/**
	 * Get FB App settings page URL.
	 */
	function pp_get_fb_app_settings_url() {
		$app_id = pp_get_fb_app_id();

		if ( $app_id ) {
			return sprintf( 'https://developers.facebook.com/apps/%d/settings/', $app_id );
		} else {
			return 'https://developers.facebook.com/apps/';
		}
	}
}

if ( ! function_exists( 'pp_get_fb_module_desc' ) ) {
	/**
	 * Get FB module description.
	 */
	function pp_get_fb_module_desc() {
		$app_id = pp_get_fb_app_id();

		if ( ! $app_id ) {
			// translators: %s: Setting Page link
			return sprintf( __( 'You can set your Facebook App ID in the <a href="%s" target="_blank">Integrations Settings</a>', 'bb-powerpack' ), BB_PowerPack_Admin_Settings::get_form_action() );
		} else {
			// translators: %1$s: app_id, %2$s: Setting Page link.
			return sprintf( __( 'You are connected to Facebook App %1$s, <a href="%2$s" target="_blank">Change App</a>', 'bb-powerpack' ), $app_id, BB_PowerPack_Admin_Settings::get_form_action() );
		}
	}
}

if ( ! function_exists( 'pp_get_google_api_key' ) ) {
	/**
	 * Get Google API key from admin settings.
	 */
	function pp_get_google_api_key() {
		return BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_google_api_key' );
	}
}

if ( ! function_exists( 'pp_get_google_api_url' ) ) {
	/**
	 * Get Google Maps JavaScript API URL.
	 */
	function pp_get_google_api_url() {
		$key = pp_get_google_api_key();
		if ( ! empty( $key ) ) {
			return "https://maps.googleapis.com/maps/api/js?key={$key}&callback=bb_powerpack.callback";
		}

		return false;
	}
}

if ( ! function_exists( 'pp_get_google_places_api_key' ) ) {
	/**
	 * Get Google Places API key from settings.
	 */
	function pp_get_google_places_api_key() {
		return BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_google_places_api_key' );
	}
}

if ( ! function_exists( 'pp_get_google_places_api_url' ) ) {
	/**
	 * Get Google Places API URL.
	 */
	function pp_get_google_places_api_url() {
		$key = pp_get_google_places_api_key();
		if ( ! empty( $key ) ) {
			return "https://maps.googleapis.com/maps/api/place/details/json?key={$key}";
		}

		return false;
	}
}

if ( ! function_exists( 'pp_get_yelp_api_key' ) ) {
	/**
	 * Get Yelp API key from settings.
	 */
	function pp_get_yelp_api_key() {
		return BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_yelp_api_key' );
	}
}

if ( ! function_exists( 'pp_get_instagram_token' ) ) {
	/**
	 * Get Instagram token from settings.
	 */
	function pp_get_instagram_token() {
		return BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_instagram_access_token', true );
	}
}

if ( ! function_exists( 'pp_get_instagram_cache_duration' ) ) {
	/**
	 * Get Instagram cache duration from settings.
	 */
	function pp_get_instagram_cache_duration() {
		return BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_instagram_cache_duration', true );
	}
}

if ( ! function_exists( 'pp_clear_enabled_templates' ) ) {
	/**
	 * Clear enabled templates cache.
	 */
	function pp_clear_enabled_templates() {
		BB_PowerPack_Admin_Settings::update_option( 'bb_powerpack_page_templates', array('disabled') );
		BB_PowerPack_Admin_Settings::update_option( 'bb_powerpack_templates', array('disabled') );
		BB_PowerPack_Admin_Settings::delete_option( 'bb_powerpack_row_templates_type' );
		BB_PowerPack_Admin_Settings::delete_option( 'bb_powerpack_row_templates_all' );
		BB_PowerPack_Admin_Settings::delete_option( 'bb_powerpack_override_ms' );
	}
}

if ( ! function_exists( 'pp_get_image_alt' ) ) {
	/**
	 * Build image alternate (alt) text from the attachment data.
	 */
	function pp_get_image_alt( $img_id = false, $default = '' ) {
		if ( ! $img_id || ! absint( $img_id ) ) {
			return;
		}
		if ( ! class_exists( 'FLBuilderPhoto' ) ) {
			$image_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
			$image_alt = empty( $image_alt ) ? esc_attr( $default ) : $image_alt;
			return $image_alt;
		}
		
		$img_id          = absint( $img_id );
		$attachment_data = FLBuilderPhoto::get_attachment_data( $img_id );
		$image_alt       = ( ! empty( $default ) ) ? $default : '';
		
		if ( is_object( $attachment_data ) ) {
			$image_alt = $attachment_data->alt;
			if ( empty( $image_alt ) ) {
				$image_alt = $attachment_data->caption;
				if ( empty( $image_alt ) ) {
					$image_alt = $attachment_data->title;
				}
			}
		}

		return $image_alt;
	}
}

if ( ! function_exists( 'pp_get_image_size_attrs' ) ) {
	/**
	 * Build image HTML attributes string from the attachment data.
	 */
	function pp_get_image_size_attrs( $attachment_id ) {
		$image_data = wp_get_attachment_metadata( $attachment_id );
		$attrs = '';

		if ( ! $image_data ) {
			return $attrs;
		}

		if ( isset( $image_data['width'] ) ) {
			$attrs .= ' width="' . $image_data['width'] . '"';
		}
		if ( isset( $image_data['height'] ) ) {
			$attrs .= ' height="' . $image_data['height'] . '"';
		}

		return $attrs;
	}
}

if ( ! function_exists( 'pp_gradient_angle_to_direction' ) ) {
	/**
	 * Gradient angle to direction.
	 */
	function pp_gradient_angle_to_direction( $angle = 45 ) {
		$direction = 'top_right_diagonal';
		
		// Top to Bottom.
		if ( 180 == $angle ) {
			$direction = 'bottom';
		}
		// Left to Right.
		if ( 90 == $angle ) {
			$direction = 'right';
		}
		// Bottom Left to Top Right.
		if ( 45 == $angle ) {
			$direction = 'top_right_diagonal';
		}
		// Bottom Right to Top Left.
		if ( 315 == $angle ) {
			$direction = 'top_left_diagonal';
		}
		// Top Left to Bottom Right.
		if ( 135 == $angle ) {
			$direction = 'bottom_right_diagonal';
		}
		// Top Right to Bottom Left.
		if ( 225 == $angle ) {
			$direction = 'bottom_left_diagonal';
		}

		return $direction;
	}
}

if ( ! function_exists( 'pp_image_effect_fields' ) ) {
	/**
	 * Image effect fields for modules Content Grid, Image, and Image Panels.
	 */
	function pp_image_effect_fields( $hover = false ) {
		$suffix = $hover ? '_hover' : '';

		return array(
			'image_effect_opacity'.$suffix		=> array(
				'type'						=> 'unit',
				'label'						=> __('Opacity', 'bb-powerpack'),
				'property'					=> 'opacity',
				'slider'					=> array(
					'min'						=> 0,
					'max'						=> 1,
					'step'						=> 0.1
				),
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
			'image_effect_brightness'.$suffix	=> array(
				'type'						=> 'unit',
				'label'						=> __('Brightness', 'bb-powerpack'),
				'property'					=> 'brightness',
				'units'						=> array('%'),
				'slider'					=> array(
					'min'						=> 0,
					'max'						=> 200,
					'step'						=> 1
				),
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
			'image_effect_contrast'.$suffix		=> array(
				'type'						=> 'unit',
				'label'						=> __('Contrast', 'bb-powerpack'),
				'property'					=> 'contrast',
				'units'						=> array('%'),
				'slider'					=> array(
					'min'						=> 0,
					'max'						=> 200,
					'step'						=> 1
				),
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
			'image_effect_saturate'.$suffix		=> array(
				'type'						=> 'unit',
				'label'						=> __('Saturate', 'bb-powerpack'),
				'property'					=> 'saturate',
				'slider'					=> array(
					'min'						=> 0,
					'max'						=> 1,
					'step'						=> 0.1
				),
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
			'image_effect_hue_rotate'.$suffix	=> array(
				'type'						=> 'unit',
				'label'						=> __('Hue Rotate', 'bb-powerpack'),
				'property'					=> 'hue-rotate',
				'units'						=> array('deg'),
				'slider'					=> array(
					'min'						=> 0,
					'max'						=> 360,
					'step'						=> 1
				),
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
			'image_effect_grayscale'.$suffix	=> array(
				'type'						=> 'unit',
				'label'						=> __('Grayscale', 'bb-powerpack'),
				'property'					=> 'grayscale',
				'units'						=> array( '%' ),
				'slider'					=> true,
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
			'image_effect_blur'.$suffix			=> array(
				'type'						=> 'unit',
				'label'						=> __('Blur', 'bb-powerpack'),
				'property'					=> 'blur',
				'units'						=> array( 'px' ),
				'slider'					=> array(
					'min'						=> 1,
					'max'						=> 30,
					'step'						=> 1
				),
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
			'image_effect_sepia'.$suffix		=> array(
				'type'						=> 'unit',
				'label'						=> __('Sepia', 'bb-powerpack'),
				'property'					=> 'sepia',
				'slider'					=> array(
					'min'						=> 0,
					'max'						=> 1,
					'step'						=> 0.1
				),
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
			'image_effect_invert'.$suffix		=> array(
				'type'						=> 'unit',
				'label'						=> __('Invert', 'bb-powerpack'),
				'property'					=> 'invert',
				'units'						=> array('%'),
				'slider'					=> array(
					'min'						=> 0,
					'max'						=> 100,
					'step'						=> 1
				),
				'preview'	=> array(
					'type'		=> 'refresh'
				),
			),
		);
	}
}

if ( ! function_exists( 'pp_image_effect_render_style' ) ) {
	/**
	 * Build and render image effect CSS.
	 * Used in Content Grid, Image, and Image Panels modules.
	 */
	function pp_image_effect_render_style( $settings, $selector, $is_hover = false ) {
		$fields = pp_image_effect_fields( $is_hover );
		$css    = "\n $selector {\n";

		$webkit_props = array();
		$filter_props = array();

		foreach ( $fields as $name => $field ) {
			$unit = isset( $field['units'] ) ? $field['units'][0] : '';
			if ( isset( $settings->{$name} ) && '' != $settings->{$name} ) {
				$webkit_props[] = $field['property']."(" . $settings->{$name} . $unit .")";
				$filter_props[] = $field['property']."(" . $settings->{$name} . $unit .")";
			}
		}

		if ( ! empty( $webkit_props ) ) {
			$css .= "\n\t" . '-webkit-filter: ' . implode( ' ', $webkit_props ) . ';';
		}
		if ( ! empty( $filter_props ) ) {
			$css .= "\n\t" . 'filter: ' . implode( ' ', $filter_props ) . ';';
		}
		$css .= "\n" . '}';

		echo $css;
	}
}

if ( ! function_exists( 'pp_get_site_domain' ) ) {
	/**
	 * Get site domain.
	 */
	function pp_get_site_domain() {
		return str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	}
}

if ( ! function_exists( 'pp_get_recaptcha_desc' ) ) {
	/**
	 * Get recaptcha field description.
	 */
	function pp_get_recaptcha_desc() {
		// translators: %s: Integration Setting Page link
		return sprintf(
			__( 'To use reCAPTCHA, you need to add the API keys in the <a href="%s" target="_blank">Integrations Settings</a> and complete the setup process.', 'bb-powerpack' ),
			BB_PowerPack_Admin_Settings::get_form_action( '&tab=integration' )
		);
	}
}

if ( ! function_exists( 'pp_get_hcaptcha_desc' ) ) {
	/**
	 * Get hcaptcha field description.
	 */
	function pp_get_hcaptcha_desc() {
		// translators: %s: Integration Setting Page link
		return sprintf(
			__( 'To use hCaptcha, you need to add the API keys in the <a href="%s" target="_blank">Integrations Settings</a> and complete the setup process.', 'bb-powerpack' ),
			BB_PowerPack_Admin_Settings::get_form_action( '&tab=integration' )
		);
	}
}

if ( ! function_exists( 'pp_is_builder_active' ) ) {
	/**
	 * Check whether the builder is active or not.
	 */
	function pp_is_builder_active() {
		return is_user_logged_in() && isset( $_GET['fl_builder'] );
	}
}

if ( ! function_exists( 'pp_wl_reset_settings' ) ) {
	/**
	 * Reset white label settings.
	 * @see class-pp-admin-settings.php
	 */
	function pp_wl_reset_settings() {
		delete_option( 'ppwl_hide_form' );
		delete_option( 'ppwl_hide_plugin' );

		if ( is_network_admin() ) {
			delete_site_option( 'ppwl_hide_form' );
			delete_site_option( 'ppwl_hide_plugin' );
		}
	}
}

if ( ! function_exists( 'pp_wl_get_reset_url' ) ) {
	/**
	 * White label settings reset URL.
	 */
	function pp_wl_get_reset_url() {
		return BB_PowerPack_Admin_Settings::get_form_action( '&tab=white-label&reset_wl=' . pp_plugin_get_hash() );
	}
}

if ( ! function_exists( 'pp_plugin_get_hash' ) ) {
	/**
	 * Text to md5 hash.
	 */
	function pp_plugin_get_hash() {
		return md5( 'PowerPack for Beaver Builder' );
	}
}

if ( ! function_exists( 'pp_get_attachment_data' ) ) {
	/**
	 * Get media attachement data.
	 */
	function pp_get_attachment_data( $id ) {
		add_filter( 'image_size_names_choose', 'BB_PowerPack_Post_Helper::additional_image_sizes' );

		$data = FLBuilderPhoto::get_attachment_data( $id );

		remove_filter( 'image_size_names_choose', 'BB_PowerPack_Post_Helper::additional_image_sizes' );

		return $data;
	}
}

if ( ! function_exists( 'pp_prev_icon_svg' ) ) {
	/**
	 * SVG code for the previous arrow icon used in various modules.
	 */
	function pp_prev_icon_svg( $sr_text = '', $echo = true ) {
		$svg = '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"><path fill="currentColor" d="M25.1 247.5l117.8-116c4.7-4.7 12.3-4.7 17 0l7.1 7.1c4.7 4.7 4.7 12.3 0 17L64.7 256l102.2 100.4c4.7 4.7 4.7 12.3 0 17l-7.1 7.1c-4.7 4.7-12.3 4.7-17 0L25 264.5c-4.6-4.7-4.6-12.3.1-17z"></path></svg>';

		$svg = '<span aria-hidden="true">' . apply_filters( 'pp_prev_icon_svg', $svg ) . '</span>';

		if ( ! empty( $sr_text ) ) {
			$svg .= '<span class="sr-only">' . $sr_text . '</span>';
		}

		if ( $echo ) {
			echo $svg;
		} else {
			return $svg;
		}
	}
}

if ( ! function_exists( 'pp_next_icon_svg' ) ) {
	/**
	 * SVG code for the next arrow icon used in various modules.
	 */
	function pp_next_icon_svg( $sr_text = '', $echo = true ) {
		$svg = '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"><path fill="currentColor" d="M166.9 264.5l-117.8 116c-4.7 4.7-12.3 4.7-17 0l-7.1-7.1c-4.7-4.7-4.7-12.3 0-17L127.3 256 25.1 155.6c-4.7-4.7-4.7-12.3 0-17l7.1-7.1c4.7-4.7 12.3-4.7 17 0l117.8 116c4.6 4.7 4.6 12.3-.1 17z"></path></svg>';

		$svg = '<span aria-hidden="true">' . apply_filters( 'pp_next_icon_svg', $svg ) . '</span>';

		if ( ! empty( $sr_text ) ) {
			$svg .= '<span class="sr-only">' . $sr_text . '</span>';
		}

		if ( $echo ) {
			echo $svg;
		} else {
			return $svg;
		}
	}
}

if ( ! function_exists( 'pp_get_post_content' ) ) {
	function pp_get_wpml_element_id( $id ) {
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
}

if ( ! function_exists( 'pp_get_post_content' ) ) {
	/**
	 * Get post content.
	 */
	function pp_get_post_content( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		ob_start();

		pp_render_post_content( $post->ID );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'pp_render_post_content' ) ) {
	/**
	 * Render post content.
	 */
	function pp_render_post_content( $post_id ) {
		if ( FLBuilderModel::is_builder_enabled( $post_id ) ) {

			$inline_assets = pp_is_bb_rendering_assets_inline();

			if ( ! $inline_assets ) {
				//add_filter( 'fl_builder_render_assets_inline', '__return_true' );
			}

			// Enqueue styles and scripts for the post.
			FLBuilder::enqueue_layout_styles_scripts_by_id( $post_id );

			// Print the styles if we are outside of the head tag.
			if ( did_action( 'wp_enqueue_scripts' ) && ! doing_filter( 'wp_enqueue_scripts' ) ) {
				wp_print_styles();
			}

			// Render the builder content.
			FLBuilder::render_content_by_id( $post_id );

			if ( ! $inline_assets ) {
				//add_filter( 'fl_builder_render_assets_inline', '__return_false' );
			}
		} else {
			$wpautop = has_filter( 'the_content', 'wpautop' );

			if ( ! $wpautop ) {
				add_filter( 'the_content', 'wpautop' );
			}

			// Render the WP editor content if the builder isn't enabled.
			echo apply_filters( 'the_content', get_the_content( null, false, $post_id ) );

			if ( ! $wpautop ) {
				remove_filter( 'the_content', 'wpautop' );
			}
		}
	}
}

if ( ! function_exists( 'pp_enqueue_layout_assets' ) ) {
	/**
	 * Enqueue or render layout assets.
	 */
	function pp_enqueue_layout_assets( $layout_id ) {
		$inline_assets = pp_is_bb_rendering_assets_inline();

		if ( ! $inline_assets ) {
			add_filter( 'fl_builder_render_assets_inline', '__return_true' );
		}

		// Enqueue styles and scripts for the post.
		FLBuilder::enqueue_layout_styles_scripts_by_id( $layout_id );

		// Print the styles if we are outside of the head tag.
		if ( did_action( 'wp_enqueue_scripts' ) && ! doing_filter( 'wp_enqueue_scripts' ) ) {
			wp_print_styles();
		}

		if ( ! $inline_assets ) {
			add_filter( 'fl_builder_render_assets_inline', '__return_false' );
		}
	}
}

if ( ! function_exists( 'pp_is_bb_rendering_assets_inline' ) ) {
	/**
	 * Whether inline assets rendering enabled or not.
	 */
	function pp_is_bb_rendering_assets_inline() {
		$inline = false;

		if ( is_callable( 'FLBuilderModel::get_asset_enqueue_method' ) ) {
			$inline = 'inline' === FLBuilderModel::get_asset_enqueue_method();
		} else {
			$inline = apply_filters( 'fl_builder_render_assets_inline', false );
		}

		return $inline;
	}
}

if ( ! function_exists( 'pp_esc_tags' ) ) {
	function pp_esc_tags( $setting, $default = false ) {
		$tags = array(
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'div',
			'p',
			'li',
			'ul',
			'ol',
			'article',
			'section',
			'aside',
			'main',
			'span',
			'header',
			'footer',
		);
		foreach ( $tags as $tag ) {
			if ( is_array( $setting ) && in_array( $tag, $setting ) ) {
				return $setting;
			}
			if ( $tag === $setting ) {
				return $setting;
			}
		}
		return $default;
	}
}

if ( ! function_exists( 'pp_builder_setting_form_section_icon' ) ) {
	function pp_builder_setting_form_section_icon() {
		if ( false === strpos( FL_BUILDER_VERSION, '2.8' ) ) {
			echo '<svg width="20" height="20"><use href="#fl-builder-forms-down-caret" /></svg>';
		} else {
			echo '<svg class="fl-symbol"><use href="#fl-down-caret" /></svg>';
		}
	}
}

if ( ! function_exists( 'pp_extract_styles_scripts' ) ) {
	function pp_extract_styles_scripts( $content = '' ) {
		if ( empty( $content ) ) {
			return;
		}
		
		$style_scripts = '';

		if ( class_exists( 'DOMDocument' ) ) {
			$dom = new DOMDocument();
			libxml_use_internal_errors(true); // Prevent errors from malformed HTML
			$dom->loadHTML( $content, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_COMPACT );
			libxml_clear_errors();

			foreach ( $dom->getElementsByTagName('link') as $link ) {
				$style_scripts .= $dom->saveHTML( $link );
			}
			foreach ( $dom->getElementsByTagName('style') as $style ) {
				$style_scripts .= $dom->saveHTML( $style );
			}
			foreach ( $dom->getElementsByTagName('script') as $script ) {
				$style_scripts .= $dom->saveHTML( $script );
			}
		} else {
			if ( preg_match_all( '/<link\b[^>]*>/is', $content, $link_tags ) ) {
				foreach ( $link_tags[0] as $value ) {
					$style_scripts .= $value;
				}
			}
			if ( preg_match_all( '/<style\b[^>]*>.*?<\/style>/is', $content, $styles ) ) {
				foreach ( $styles[0] as $value ) {
					$style_scripts .= $value;
				}

				$content = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $content);
			}
			if ( preg_match_all( '/<script\b[^>]*>.*?<\/script>/is', $content, $scripts ) ) {
				foreach ( $scripts[0] as $value ) {
					$style_scripts .= $value;
				}

				$content = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $content);
			}
		}

		return $style_scripts;
	}
}