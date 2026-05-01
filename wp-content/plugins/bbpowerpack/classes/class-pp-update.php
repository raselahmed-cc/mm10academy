<?php

/**
 * Helper class for builder updates.
 *
 * @since 2.32
 */
final class BB_PowerPack_Update {

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	static public function init() {
		add_action( 'init', __CLASS__ . '::maybe_run', 11 );
		add_action( 'fl_site_url_changed', array( __CLASS__, 'maybe_reregister_license' ), 10, 2 );
	}

	static public function maybe_reregister_license( $current, $saved ) {
		if ( ! function_exists( 'bb_powerpack_get_license_key' ) ) {
			return;
		}
		$license = bb_powerpack_get_license_key();
		if ( '' !== $license ) {
			bb_powerpack_delete( 'bb_powerpack_license_status' );
			bb_powerpack_license( 'activate_license', $license );
		}
	}

	/**
	 * Checks to see if an update should be run. If it should,
	 * the appropriate update method is run and the version
	 * number is updated in the database.
	 *
	 * @return void
	 */
	static public function maybe_run() {
		// Make sure the user is logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Only run on the main site for multisite installs.
		if ( is_multisite() && ! is_main_site() ) {
			return;
		}

		// Get the saved version.
		$saved_version = get_site_option( 'bb_powerpack_version' );

		// No saved version number. This must be a fresh install.
		if ( ! $saved_version ) {
			update_site_option( 'bb_powerpack_version', BB_POWERPACK_VER );
			return;
		} elseif ( ! version_compare( $saved_version, BB_POWERPACK_VER, '=' ) ) {

			if ( is_multisite() ) {
				self::run_multisite( $saved_version );
			} else {
				self::run( $saved_version );
			}

			/**
			 * Fires after cache is cleared.
			 * @see fl_builder_cache_cleared
			 */
			do_action( 'fl_builder_cache_cleared' );

			update_site_option( 'bb_powerpack_version', BB_POWERPACK_VER );

			update_site_option( 'bb_powerpack_update_info', array(
				'from' => $saved_version,
				'to'   => BB_POWERPACK_VER,
			) );
		}
	}

	/**
	 * Runs the update for a specific version.
	 *
	 * @access private
	 * @return void
	 */
	static private function run( $saved_version ) {
		if ( is_callable( 'FLBuilderModel::delete_asset_cache_for_all_posts' ) ) {
			// Clear all asset cache.
			FLBuilderModel::delete_asset_cache_for_all_posts();
		}
	}

	/**
	 * Runs the update for all sites on a network install.
	 *
	 * @access private
	 * @return void
	 */
	static private function run_multisite( $saved_version ) {
		global $blog_id;
		global $wpdb;

		// Save the original blog id.
		$original_blog_id = $blog_id;

		// Get all blog ids.
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		// Loop through the blog ids and run the update.
		foreach ( $blog_ids as $id ) {
			switch_to_blog( $id );
			self::run( $saved_version );
		}

		// Revert to the original blog.
		switch_to_blog( $original_blog_id );
	}
}

BB_PowerPack_Update::init();
