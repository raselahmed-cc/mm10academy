<?php
/**
 * PowerPack integration settings page.
 *
 * @package bb-powerpack
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php
$ig_access_token = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_instagram_access_token', true);
$ig_token_generated_msg = '';

if ( isset( $_GET['ig_access_token'] ) && ! empty( $_GET['ig_access_token'] ) ) {
	$ig_access_token = esc_attr( $_GET['ig_access_token'] );
	$ig_token_generated_msg = '<p style="color: red;">' . esc_html__( 'Token generated. Please save the changes.', 'bb-powerpack' ) . '</p>';
	?>
	<div class="notice notice-info"><p><strong><?php esc_html_e( 'A new Instagram Access Token is generated. Please save the settings below!', 'bb-powerpack' ); ?></strong></p></div>
	<?php
}
?>
<div class="pp-admin-settings-content-head">
	<h3><?php esc_html_e( 'Integration', 'bb-powerpack' ); ?></h3>
	<p class="description"><?php esc_html_e( 'You can configure the integration settings for various third-party services from here.', 'bb-powerpack' ); ?></p>
</div>
<form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">
	<p style="margin-top: 0;"><?php echo __( 'Facebook App ID is required only if you want to use Facebook Comments Module. All other Facebook Modules can be used without a Facebook App ID. Note that this option will not work on local sites and on domains that don\'t have public access.', 'bb-powerpack' ); ?></p>
	<table class="form-table">
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_fb_app_id"><?php esc_html_e( 'Facebook App ID', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_fb_app_id" name="bb_powerpack_fb_app_id" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_fb_app_id', true); ?>" autofill="false" autocomplete="false" autosuggest="false" />
				<p class="description">
					<?php // translators: %s: Facebook App Setting link ?>
					<?php echo sprintf( __( 'To get your Facebook App ID, you need to <a href="%s" target="_blank">register and configure</a> an app. Once registered, add the domain to your <a href="%s" target="_blank">App Domains</a>', 'bb-powerpack' ), 'https://developers.facebook.com/docs/apps/register/', pp_get_fb_app_settings_url() ); ?>
				</p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_fb_app_secret"><?php esc_html_e( 'Facebook App Secret', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_fb_app_secret" name="bb_powerpack_fb_app_secret" type="password" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_fb_app_secret', true); ?>" autofill="false" autocomplete="false" autosuggest="false" />
				<p class="description">
					<?php // translators: %s: Facebook App Setting link ?>
					<?php echo sprintf( __( 'To get your Facebook App Secret, you need to <a href="%s" target="_blank">register and configure</a> an app. Once registered, you will find App Secret under <a href="%s" target="_blank">App Domains</a>', 'bb-powerpack' ), 'https://developers.facebook.com/docs/apps/register/', pp_get_fb_app_settings_url() ); ?>
				</p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_google_client_id"><?php esc_html_e( 'Google Client ID', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_google_client_id" name="bb_powerpack_google_client_id" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_google_client_id', true); ?>" />
				<p class="description">
					<?php // translators: %s: Google API document ?>
					<?php echo sprintf( __( 'To get your Google Client ID, read <a href="https://wpbeaveraddons.com/docs/powerpack/modules/login-form/create-google-client-id" target="_blank">this document</a>', 'bb-powerpack' ), '#' ); ?>
				</p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_google_api_key"><?php esc_html_e( 'Google API Key', 'bb-powerpack' ); ?><p class="description"><?php esc_html_e( '(for Google Maps and Google Reviews)', 'bb-powerpack' ); ?></p></label>
			</th>
			<td>
				<input id="bb_powerpack_google_api_key" name="bb_powerpack_google_api_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_google_api_key', true); ?>" />
				<p class="description">
					<?php // translators: %s: Google API document ?>
					<?php echo sprintf( __( 'To get your Google API Key, read <a href="%s" target="_blank">this document</a>', 'bb-powerpack' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key' ); ?>
				</p>
			</td>
		</tr>
		<tr align="top" style="display: none;">
			<th scope="row" valign="top">
				<label for="bb_powerpack_google_places_api_key"><?php esc_html_e('Google Places API Key (deprecated)', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_google_places_api_key" name="bb_powerpack_google_places_api_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option('bb_powerpack_google_places_api_key', true); ?>" />
				<p class="description">
					<?php echo sprintf( __( 'To get your Google Places API Key, read <a href="%s" target="_blank">this document</a>', 'bb-powerpack' ), 'https://developers.google.com/places/web-service/get-api-key' ); ?>
				</p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_yelp_api_key"><?php esc_html_e('Yelp Business API Key', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_yelp_api_key" name="bb_powerpack_yelp_api_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option('bb_powerpack_yelp_api_key', true); ?>" />
				<p class="description">
					<?php // translators: %s: Yelp API document ?>
					<?php echo sprintf( __( 'To get your Yelp API Key, read <a href="%s" target="_blank">this document</a>', 'bb-powerpack' ), 'https://www.yelp.com/developers/documentation/v3/authentication' ); ?>
				</p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_instagram_access_token"><?php esc_html_e('Instagram Access Token', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_instagram_access_token" name="bb_powerpack_instagram_access_token" type="text" class="regular-text" value="<?php echo $ig_access_token; ?>" />
				<div style="margin-top: 8px; display: none;">
					<a id="ig_token_generate_btn" class="button" href="https://www.instagram.com/oauth/authorize/?client_id=707735606362444&redirect_uri=https://connect.wpbeaveraddons.com/auth/instagram/redirect.php&response_type=code&scope=user_profile%2Cuser_media&state={<?php echo base64_encode( self::get_form_action( '&tab=' . $current_tab ) ); ?>}"><?php esc_html_e( 'Generate Token', 'bb-powerpack' ); ?></a>
					<div style="display: inline-flex;flex-direction: column;margin-bottom: 10px;">
						<span style="font-size: 12px; margin-left: 10px; margin-bottom: 5px;"><?php echo sprintf( esc_html__( 'Please note, use of this feature is subject to %1$s Facebook\'s Platform Terms %2$s', 'bb-powerpack' ), '<a href="https://developers.facebook.com/terms/" target="_blank">', '</a>' ); // translators: %1$s opening anchor tag, %2$s closing anchor tag. ?></span>
						<span style="font-size: 12px; margin-left: 10px;"><?php esc_html_e( 'Also, you will need to manually generate the token for Instagram Business account.', 'bb-powerpack' ); ?></span>
					</div>
				</div>
				<?php echo $ig_token_generated_msg; ?>
				<p class="description">
					<?php // translators: %s: Instagram Access Token document ?>
					<?php echo sprintf( __( 'To manually generate Instagram Access Token, read <a href="%s" target="_blank">this document</a>', 'bb-powerpack' ), 'https://wpbeaveraddons.com/docs/powerpack/modules/instagram-feed/beaver-builder-instagram-module-setup-powerpack/' ); ?>
				</p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_instagram_cache_duration"><?php esc_html_e('Instagram Cache Duration', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<?php
					$cache_duration = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_instagram_cache_duration', true);
					$cache_duration = empty( $cache_duration ) ? 'hour' : $cache_duration;
				?>
				<select id="bb_powerpack_instagram_cache_duration" name="bb_powerpack_instagram_cache_duration" class="regular-text">
					<option value="none" <?php echo selected( 'none', $cache_duration ); ?>><?php _e( 'None', 'bb-powerpack' ); ?></option>
					<option value="minute" <?php echo selected( 'minute', $cache_duration ); ?>><?php _e( 'Minute', 'bb-powerpack' ); ?></option>
					<option value="hour" <?php echo selected( 'hour', $cache_duration ); ?>><?php _e( 'Hour', 'bb-powerpack' ); ?></option>
					<option value="day" <?php echo selected( 'day', $cache_duration ); ?>><?php _e( 'Day', 'bb-powerpack' ); ?></option>
					<option value="week" <?php echo selected( 'week', $cache_duration ); ?>><?php _e( 'Week', 'bb-powerpack' ); ?></option>
				</select>
				<p class="description">
					<?php echo __( 'We will check for the new posts after the given duration.', 'bb-powerpack' ); ?>
				</p>
			</td>
		</tr>
	</table>

	<h3><?php esc_html_e( 'reCAPTCHA V2', 'bb-powerpack' ); ?></h3>
	<p>
		<?php // translators: %s: reCAPTCHA Site Key document ?>
		<?php echo sprintf( __( 'Register keys for your website at the <a href="%s" target="_blank">Google Admin Console</a>.', 'bb-powerpack' ), 'https://www.google.com/recaptcha/admin' ); ?>
	</p>
	<table class="form-table">
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_recaptcha_site_key"><?php esc_html_e( 'Site Key', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_recaptcha_site_key" name="bb_powerpack_recaptcha_site_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_site_key', true ); ?>" />
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_recaptcha_secret_key"><?php esc_html_e( 'Secret Key', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_recaptcha_secret_key" name="bb_powerpack_recaptcha_secret_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_secret_key', true ); ?>" />
			</td>
		</tr>
	</table>

	<h3><?php esc_html_e( 'reCAPTCHA V3', 'bb-powerpack' ); ?></h3>
	<p>
		<?php // translators: %s: reCAPTCHA Site Key document ?>
		<?php echo sprintf( __( 'Register keys for your website at the <a href="%s" target="_blank">Google Admin Console</a>.', 'bb-powerpack' ), 'https://www.google.com/recaptcha/admin' ); ?>
		<br />
		<?php echo sprintf( __( '<a href="%s" target="_blank">More info about reCAPTCHA V3</a>', 'bb-powerpack' ), 'https://developers.google.com/recaptcha/docs/v3' ); ?>
	</p>
	<table class="form-table">
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_recaptcha_v3_site_key"><?php esc_html_e( 'Site Key', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_recaptcha_v3_site_key" name="bb_powerpack_recaptcha_v3_site_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_v3_site_key', true ); ?>" />
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_recaptcha_v3_secret_key"><?php esc_html_e( 'Secret Key', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_recaptcha_v3_secret_key" name="bb_powerpack_recaptcha_v3_secret_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_v3_secret_key', true ); ?>" />
			</td>
		</tr>
	</table>

	<h3><?php esc_html_e( 'hCaptcha', 'bb-powerpack' ); ?></h3>
	<p>
		<?php echo sprintf( __( 'Register keys for your website at <a href="%s" target="_blank">hCaptcha Dashboard</a>.', 'bb-powerpack' ), 'https://dashboard.hcaptcha.com/' ); ?>
	</p>
	<table class="form-table">
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_hcaptcha_site_key"><?php esc_html_e( 'Site Key', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_hcaptcha_site_key" name="bb_powerpack_hcaptcha_site_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_hcaptcha_site_key', true ); ?>" />
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_hcaptcha_secret_key"><?php esc_html_e( 'Secret Key', 'bb-powerpack' ); ?></label>
			</th>
			<td>
				<input id="bb_powerpack_hcaptcha_secret_key" name="bb_powerpack_hcaptcha_secret_key" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_hcaptcha_secret_key', true ); ?>" />
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
<?php do_action( 'pp_admin_settings_before_form_close', $current_tab ); ?>
</form>