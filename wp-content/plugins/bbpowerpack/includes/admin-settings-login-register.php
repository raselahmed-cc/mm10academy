<?php
/**
 * Login/Register settings page.
 *
 * @package bb-powerpack
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="pp-admin-settings-content-head">
	<h3><?php esc_html_e( 'Login / Register Pages', 'bb-powerpack' ); ?></h3>
	<p class="description"><?php esc_html_e( 'You can set custom pages for login and register.', 'bb-powerpack' ); ?></p>
	<?php if ( ! is_network_admin() && is_multisite() ) : ?>
	<div class="alert alert-info"><?php esc_html_e( 'NOTE: By changing setting here will override the network settings.', 'bb-powerpack' ); ?></div>
	<?php endif; ?>
</div>
<form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">
	<table class="form-table maintenance-mode-config">
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_login_page"><?php esc_html_e('Login page', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<?php $selected = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_login_page', true); ?>
				<select id="bb_powerpack_login_page" name="bb_powerpack_login_page" style="min-width: 200px;">
					<?php echo BB_PowerPack_Login_Register::get_pages( $selected ); ?>
				</select>
				<p class="description"><?php _e( 'It will replace native login page with the custom one. Please make sure that you have login form on the selected page.', 'bb-powerpack' ); ?></p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_register_page"><?php esc_html_e('Register page', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<?php $selected = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_register_page', true); ?>
				<select id="bb_powerpack_register_page" name="bb_powerpack_register_page" style="min-width: 200px;">
					<?php echo BB_PowerPack_Login_Register::get_pages( $selected ); ?>
				</select>
				<p class="description"><?php _e( 'It will replace native registration page with the custom one. Please make sure that you have registration form on the selected page.', 'bb-powerpack' ); ?></p>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
<?php do_action( 'pp_admin_settings_before_form_close', $current_tab ); ?>
</form>