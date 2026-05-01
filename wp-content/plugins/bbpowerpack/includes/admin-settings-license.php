<?php
/**
 * License settings page.
 *
 * @package bb-powerpack
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php
$status = self::get_option( 'bb_powerpack_license_status', true, true );
$license = self::get_option( 'bb_powerpack_license_key', true, true );
if ( ! bb_powerpack_get( 'bb_powerpack_license_check_completed' ) ) {
	$license_status = bb_powerpack_check_license();
	if ( is_array( $license_status ) && isset( $license_status['message'] ) ) {
		self::add_error( self::parse_error( $license_status['message'] ) );
		$license_status = $status;
	} else {
		if ( ! is_array( $license_status ) && $status !== $license_status ) {
			if ( 'site_inactive' === $license_status || 'invalid' === $license_status || 'expired' === $license_status ) {
				$status = '';
			}
			if ( 'valid' === $license_status ) {
				$status = 'valid';
			}

			if ( ! isset( $_GET['status'] ) ) {
				bb_powerpack_update( 'bb_powerpack_license_status', $status );
			}
		}
	}
}
if ( '' == $status ) {
	$status = 'inactive';
}
?>

<?php settings_fields( 'bb_powerpack_license' ); ?>
<div class="pp-admin-settings-content-head">
	<h3><?php _e('License', 'bb-powerpack'); ?></h3>
<?php if ( ! self::has_license_key_defined() ) { ?>
	<?php if ( ! self::get_option( 'ppwl_remove_license_key_link' ) ) { ?>
		<p class="description"><?php echo sprintf(__('Enter your <a href="%s" target="_blank">license key</a> to enable remote updates and support.', 'bb-powerpack'), 'https://wpbeaveraddons.com/checkout/purchase-history/?utm_medium=powerpack&utm_source=license-settings-page&utm_campaign=license-key-link'); ?></p>
	<?php } else { ?>
		<p class="description"><?php _e('Enter your license key to enable remote updates and support.', 'bb-powerpack'); ?></p>
	<?php } ?>
<?php } else { ?>
	<p class="description"><?php _e('Your license key is defined in wp-config.php file.', 'bb-powerpack'); ?></p>
<?php } ?>
</div>
<form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">
<table class="form-table">
	<tbody>
		<?php if ( ! self::has_license_key_defined() ) { ?>
		<tr valign="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_license_key"><?php esc_html_e('License Key', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<label style="display: flex; align-items: center; margin-bottom: 10px;">
				<input id="bb_powerpack_license_key" name="bb_powerpack_license_key" type="password" class="regular-text" value="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" />
				<span style="color: <?php echo 'valid' == $status ? '#4caf50' : 'red'; ?>; text-transform: capitalize; margin: 0 12px;"><?php echo 'valid' == $status ? esc_html__( 'active', 'bb-powerpack' ) : $status; ?></span>
				</label>
				<?php if( false !== $license ) { ?>
				<div>
					<?php if ( $status == 'valid' ) { ?>
						<?php wp_nonce_field( 'bb_powerpack_nonce', 'bb_powerpack_nonce' ); ?>
							<input type="submit" class="button button-secondary" name="bb_powerpack_license_deactivate" value="<?php esc_html_e('Deactivate License', 'bb-powerpack'); ?>" />
					<?php } else { ?>
						<?php
						wp_nonce_field( 'bb_powerpack_nonce', 'bb_powerpack_nonce' ); ?>
						<input type="submit" class="button button-secondary" name="bb_powerpack_license_activate" value="<?php esc_html_e( 'Activate License', 'bb-powerpack' ); ?>"/>
						<p class="description"><?php esc_html_e( 'Please click the “Activate License” button to activate your license.', 'bb-powerpack' ); ?>
					<?php } ?>
					</div>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php if ( ! self::has_license_key_defined() ) { ?>
	<?php if ( is_multisite() ) { ?>
		<input type="hidden" name="bb_powerpack_override_ms" value="1" />
	<?php } ?>
	<?php submit_button(); ?>
<?php } ?>
<?php do_action( 'pp_admin_settings_before_form_close', $current_tab ); ?>
</form>
<script>
	if ( null !== document.getElementById('bb_powerpack_license_key') ) {
		document.getElementById('bb_powerpack_license_key').value = atob('<?php echo base64_encode( $license ); ?>');
	}
</script>