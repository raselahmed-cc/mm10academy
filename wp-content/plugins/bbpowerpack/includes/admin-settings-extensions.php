<?php
/**
 * Extensions settings page.
 *
 * @package bb-powerpack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php
$extensions         = pp_extensions();
$enabled_extensions = self::get_enabled_extensions();
?>
<div class="pp-admin-settings-content-head">
	<h3><?php esc_html_e( 'Extensions', 'bb-powerpack' ); ?></h3>
	<p class="description"><?php esc_html_e( 'You can enable / disable extensions from here.', 'bb-powerpack' ); ?></p>
</div>
<form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">
	<div class="pp-admin-settings-section">
		<div class="pp-admin-settings-section-header">
			<h3><?php esc_html_e( 'Row Extensions', 'bb-powerpack' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Row extensions are used to add additional functionality to Beaver Builder row.', 'bb-powerpack' ); ?></p>
		</div>
		<table class="form-table pp-flex-table">
			<?php foreach ( $extensions['row'] as $key => $extension ) { ?>
				<tr valign="top" tabindex="0">
					<th scope="row" valign="top">
						<label for="pp_extension_row_<?php echo $key; ?>"><?php echo $extension['label']; ?></label>
						<p class="description"><?php echo $extension['description']; ?></p>
					</th>
					<td>
						<?php
						$is_enabled = ( array_key_exists( $key, $enabled_extensions['row'] ) || in_array( $key, $enabled_extensions['row'] ) ) ? true : false;
						?>
						<label class="pp-admin-field-toggle">
							<input id="pp_extension_row_<?php echo $key; ?>" class="dashicons" name="bb_powerpack_extensions[row][]" type="checkbox" tabindex="-1" value="<?php echo $key; ?>"<?php echo $is_enabled ? ' checked="checked"' : '' ?> />
							<span class="pp-admin-field-toggle-slider" aria-hidden="true"></span>
						</label>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<div class="pp-admin-settings-section">
		<div class="pp-admin-settings-section-header">
			<h3><?php esc_html_e( 'Column Extensions', 'bb-powerpack' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Column extensions are used to add additional functionality to Beaver Builder column.', 'bb-powerpack' ); ?></p>
		</div>
		<table class="form-table pp-flex-table">
			<?php foreach ( $extensions['col'] as $key => $extension ) { ?>
				<tr valign="top" tabindex="0">
					<th scope="row" valign="top">
						<label for="pp_extension_col_<?php echo $key; ?>"><?php echo $extension['label']; ?></label>
						<p class="description"><?php echo $extension['description']; ?></p>
					</th>
					<td>
						<?php
						$is_enabled = ( array_key_exists( $key, $enabled_extensions['col'] ) || in_array( $key, $enabled_extensions['col'] ) ) ? true : false;
						?>
						<label class="pp-admin-field-toggle">
							<input id="pp_extension_col_<?php echo $key; ?>" class="dashicons" name="bb_powerpack_extensions[col][]" type="checkbox" tabindex="-1" value="<?php echo $key; ?>"<?php echo $is_enabled ? ' checked="checked"' : '' ?> />
							<span class="pp-admin-field-toggle-slider"></span>
						</label>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<div class="pp-admin-settings-section">
		<div class="pp-admin-settings-section-header">
			<h3><?php esc_html_e( 'Taxonomy Thumbnail', 'bb-powerpack' ); ?></h3>
			<p class="description"><?php esc_html_e( 'This extension allows you to add image thumbnail option to taxonomies.', 'bb-powerpack' ); ?></p>
		</div>
		<?php $enabled = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_taxonomy_thumbnail_enable', true); ?>
		<table class="pp-flex-table form-table">
			<tr align="top" tabindex="0">
				<th scope="row" valign="top">
					<label for="bb_powerpack_taxonomy_thumbnail_enable"><?php esc_html_e('Enable Taxonomy Thumbnail', 'bb-powerpack'); ?></label>
					<p class="description"><?php echo __( 'Enabling this option will add control to upload image for selected taxonomies.', 'bb-powerpack' ); ?></p>
				</th>
				<td>
					<label class="pp-admin-field-toggle">
						<input id="bb_powerpack_taxonomy_thumbnail_enable" name="bb_powerpack_taxonomy_thumbnail_enable" type="checkbox" tabindex="-1" value="yes"<?php echo 'enabled' === $enabled ? ' checked="checked"' : '' ?> />
						<span class="pp-admin-field-toggle-slider"></span>
					</label>
				</td>
			</tr>
		</table>
		<table class="taxonomies-list form-table"<?php echo 'enabled' === $enabled ? '' : ' style="display: none;"'; ?>>
			<tr align="top">
				<td>
					<?php
					BB_PowerPack_Taxonomy_Thumbnail::get_taxonomies_checklist();
					?>
				</td>
			</tr>
		</table>
	</div>

	<div class="pp-admin-settings-section">
		<div class="pp-admin-settings-section-header">
			<h3><?php esc_html_e( 'Author Social Profile URLs', 'bb-powerpack' ); ?></h3>
			<p class="description"><?php esc_html_e( 'This extension allows you to add social media profile URLs to user profiles.', 'bb-powerpack' ); ?></p>
		</div>
		<table class="pp-flex-table form-table">
			<tr align="top" tabindex="0">
				<th scope="row" valign="top">
					<label for="bb_powerpack_user_social_profile_urls"><?php esc_html_e('Enable Social Profile URLs', 'bb-powerpack'); ?></label>
					<p class="description"><?php echo __( 'Enabling this option will add controls to add social media URLs under the user profile.', 'bb-powerpack' ); ?></p>
				</th>
				<td>
					<label class="pp-admin-field-toggle">
						<input id="bb_powerpack_user_social_profile_urls" class="dashicons" name="bb_powerpack_user_social_profile_urls" type="checkbox" tabindex="-1" value="yes"<?php echo 'yes' === get_option( 'bb_powerpack_user_social_profile_urls' ) ? ' checked="checked"' : '' ?> />
						<span class="pp-admin-field-toggle-slider"></span>
					</label>
				</td>
			</tr>
		</table>
	</div>
	<div class="pp-admin-settings-section">
		<div class="pp-admin-settings-section-header">
			<h3><?php esc_html_e( 'Toggle Lazy Load', 'bb-powerpack' ); ?></h3>
			<p class="description"><?php esc_html_e( 'This option allows you to disable the lazy load feature of the browser.', 'bb-powerpack' ); ?></p>
		</div>
		<table class="pp-flex-table form-table">
			<tr align="top" tabindex="0">
				<th scope="row" valign="top">
					<label for="bb_powerpack_disable_wp_lazyload"><?php esc_html_e('Disable Lazy Load', 'bb-powerpack'); ?></label>
					<p class="description"><?php _e( 'If you find some modules with images behaving weird then it maybe a lazy load issue of a browser. You can disable the lazy load from here.', 'bb-powerpack' ); ?></p>
				</th>
				<td>
					<label class="pp-admin-field-toggle">
						<input id="bb_powerpack_disable_wp_lazyload" class="dashicons" name="bb_powerpack_disable_wp_lazyload" type="checkbox" tabindex="-1" value="yes"<?php echo 'yes' === get_option( 'bb_powerpack_disable_wp_lazyload' ) ? ' checked="checked"' : '' ?> />
						<span class="pp-admin-field-toggle-slider"></span>
					</label>
				</td>
			</tr>
		</table>
	</div>
	<?php submit_button(); ?>
	<?php wp_nonce_field('pp-extensions', 'pp-extensions-nonce'); ?>
	<input type="hidden" name="bb_powerpack_override_ms" value="1" />
	<?php do_action( 'pp_admin_settings_before_form_close', $current_tab ); ?>
</form>
<script>
(function($) {
	$('#bb_powerpack_taxonomy_thumbnail_enable').on('change', function() {
		if ( $(this).is(':checked') ) {
			$('.taxonomies-list').show();
		} else {
			$('.taxonomies-list').hide();
		}
	});
})(jQuery);
</script>