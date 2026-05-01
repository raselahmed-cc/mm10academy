<?php
/**
 * Turnstile Settings
 *
 * @package Turnstile Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$branding_name       = BB_Ultimate_Addon_Helper::get_builder_uabb_branding( 'uabb-plugin-name' );
$branding_short_name = BB_Ultimate_Addon_Helper::get_builder_uabb_branding( 'uabb-plugin-short-name' );
?>
<div id="fl-uabb-turnstile-form" class="fl-settings-form uabb-turnstile-fl-settings-form">

	<h3 class="fl-settings-form-header"><?php esc_html_e( 'Cloudflare Turnstile', 'uabb' ); ?></h3>

	<form id="uabb-turnstile-form" action="<?php UABBBuilderAdminSettings::render_form_action( 'uabb-turnstile' ); ?>" method="post">
		<div class="fl-settings-form-content">
			<?php
			$integration_settings = get_option( '_uabb_integration', array() );
			
			$turnstile_site_key   = isset( $integration_settings['cloudflare_turnstile_site_key'] ) ? $integration_settings['cloudflare_turnstile_site_key'] : '';
			$turnstile_secret_key = isset( $integration_settings['cloudflare_turnstile_secret_key'] ) ? $integration_settings['cloudflare_turnstile_secret_key'] : '';
			$turnstile_theme      = isset( $integration_settings['cloudflare_turnstile_theme'] ) ? $integration_settings['cloudflare_turnstile_theme'] : 'auto';
			$turnstile_size       = isset( $integration_settings['cloudflare_turnstile_size'] ) ? $integration_settings['cloudflare_turnstile_size'] : 'normal';
			?>
			<div class="uabb-form-setting">
				<h4><?php esc_html_e( 'Cloudflare Turnstile Configuration', 'uabb' ); ?></h4>
				<p class="uabb-admin-help">
					<?php esc_html_e( 'To use Cloudflare Turnstile, you need to obtain site and secret keys from your Cloudflare dashboard.', 'uabb' ); ?>
					<a target="_blank" rel="noopener" href="https://dash.cloudflare.com/"><?php esc_html_e( 'Get your keys here', 'uabb' ); ?></a>.
					<?php esc_html_e( 'Learn more about', 'uabb' ); ?>
					<a target="_blank" rel="noopener" href="https://developers.cloudflare.com/turnstile/get-started/"><?php esc_html_e( 'Turnstile setup', 'uabb' ); ?></a>.
				</p>

				<p class="uabb-admin-help"><?php esc_html_e( 'Site Key', 'uabb' ); ?></p>
				<input type="text" class="regular-text uabb-turnstile-site-key" name="cloudflare_turnstile_site_key" value="<?php echo esc_attr( $turnstile_site_key ); ?>" placeholder="<?php esc_attr_e( 'Enter your Turnstile site key', 'uabb' ); ?>" /><br/>
				<label class="uabb-turnstile-error uabb-turnstile-site-key-err" style="display: none; color: #dc3232;"><?php esc_html_e( 'This field is required.', 'uabb' ); ?></label><br/>

				<p class="uabb-admin-help"><?php esc_html_e( 'Secret Key', 'uabb' ); ?></p>
				<input type="password" class="regular-text uabb-turnstile-secret-key" name="cloudflare_turnstile_secret_key" value="<?php echo esc_attr( $turnstile_secret_key ); ?>" placeholder="<?php esc_attr_e( 'Enter your Turnstile secret key', 'uabb' ); ?>" /><br/>
				<label class="uabb-turnstile-error uabb-turnstile-secret-key-err" style="display: none; color: #dc3232;"><?php esc_html_e( 'This field is required.', 'uabb' ); ?></label><br/>

				<p class="uabb-admin-help"><?php esc_html_e( 'Theme', 'uabb' ); ?></p>
				<select name="cloudflare_turnstile_theme" class="uabb-turnstile-theme">
					<option value="auto" <?php selected( $turnstile_theme, 'auto' ); ?>><?php esc_html_e( 'Auto', 'uabb' ); ?></option>
					<option value="light" <?php selected( $turnstile_theme, 'light' ); ?>><?php esc_html_e( 'Light', 'uabb' ); ?></option>
					<option value="dark" <?php selected( $turnstile_theme, 'dark' ); ?>><?php esc_html_e( 'Dark', 'uabb' ); ?></option>
				</select><br/>
				<span class="uabb-admin-help"><?php esc_html_e( 'Choose the appearance theme for the Turnstile widget', 'uabb' ); ?></span><br/><br/>

				<p class="uabb-admin-help"><?php esc_html_e( 'Size', 'uabb' ); ?></p>
				<select name="cloudflare_turnstile_size" class="uabb-turnstile-size">
					<option value="normal" <?php selected( $turnstile_size, 'normal' ); ?>><?php esc_html_e( 'Normal', 'uabb' ); ?></option>
					<option value="compact" <?php selected( $turnstile_size, 'compact' ); ?>><?php esc_html_e( 'Compact', 'uabb' ); ?></option>
				</select><br/>
				<span class="uabb-admin-help"><?php esc_html_e( 'Choose the size of the Turnstile widget', 'uabb' ); ?></span><br/><br/>
			</div>

		</div>

		<?php wp_nonce_field( 'uabb-turnstile-nonce', 'uabb-turnstile-nonce' ); ?>
		<?php wp_nonce_field( 'uabb', 'fl-uabb-nonce' ); ?>

		<p class="submit">
			<input type="submit" name="uabb-turnstile-submit" class="button-primary" value="<?php esc_attr_e( 'Save Turnstile Settings', 'uabb' ); ?>" />
		</p>
	</form>
</div>

<script>
jQuery(document).ready(function($) {
	$('#uabb-turnstile-form').on('submit', function(e) {
		var siteKey = $('.uabb-turnstile-site-key').val();
		var secretKey = $('.uabb-turnstile-secret-key').val();
		var hasError = false;

		// Hide all error messages
		$('.uabb-turnstile-error').hide();

		// Basic validation
		if (!siteKey.trim()) {
			$('.uabb-turnstile-site-key-err').show();
			hasError = true;
		}

		if (!secretKey.trim()) {
			$('.uabb-turnstile-secret-key-err').show();
			hasError = true;
		}

		if (hasError) {
			e.preventDefault();
			return false;
		}
	});
});
</script>
