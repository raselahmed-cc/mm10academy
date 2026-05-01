<?php
/**
 * Support for the Custom theme.
 *
 * @since 2.9.0
 * @package BB_PowerPack
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Support for the Custom theme.
 *
 * @since 2.9.0
 */
final class BB_PowerPack_Header_Footer_Custom {
	/**
	 * Setup support for the theme.
	 *
	 * @since 2.9.0
	 * @return void
	 */
	static public function init() {
		add_action( 'wp', __CLASS__ . '::setup_headers_and_footers' );
	}

	/**
	 * Setup headers and footers.
	 *
	 * @since 2.9.0
	 * @return void
	 */
	static public function setup_headers_and_footers() {
		if ( did_action( 'pp_maintenance_mode_before_render' ) ) {
			return;
		}

		$theme_support = get_theme_support( 'pp-header-footer' );

		if ( is_array( $theme_support ) && isset( $theme_support[0] ) ) {
			$hooks = $theme_support[0];
			$has_header_hook = isset( $hooks['header_hook'] ) && ! empty( $hooks['header_hook'] ) && is_string( $hooks['header_hook'] );
			$has_footer_hook = isset( $hooks['footer_hook'] ) && ! empty( $hooks['footer_hook'] ) && is_string( $hooks['footer_hook'] );

			if ( $has_header_hook && ! empty( BB_PowerPack_Header_Footer::$header ) ) {
				// Remove all actions attached to provided theme's header hook.
				remove_all_actions( $hooks['header_hook'] );
				// Hook custom header set in PowerPack's settings to provided hook.
				add_action( $hooks['header_hook'], __CLASS__ . '::render_header' );
			}
			if ( $has_footer_hook && ! empty( BB_PowerPack_Header_Footer::$footer ) ) {
				// Remove all actions attached to provided theme's footer hook.
				remove_all_actions( $hooks['footer_hook'] );
				// Hook custom footer set in PowerPack's settings to provided hook.
				add_action( $hooks['footer_hook'], __CLASS__ . '::render_footer' );
			}
		}

		if ( ! $has_header_hook && ! empty( BB_PowerPack_Header_Footer::$header ) ) {
			add_action( 'pp_custom_theme_header', __CLASS__ . '::render_header' );
		}
		if ( ! $has_footer_hook && ! empty( BB_PowerPack_Header_Footer::$footer ) ) {
			add_action( 'pp_custom_theme_footer', __CLASS__ . '::render_footer' );
		}
	}

	/**
	 * Renders the header for the current page.
	 *
	 * @since 2.9.0
	 * @return void
	 */
	static public function render_header() {
		BB_PowerPack_Header_Footer::render_header();
	}

	/**
	 * Renders the footer for the current page.
	 *
	 * @since 2.9.0
	 * @return void
	 */
	static public function render_footer() {
		BB_PowerPack_Header_Footer::render_footer();
	}
}

BB_PowerPack_Header_Footer_Custom::init();
