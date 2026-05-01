<?php
/**
 * Header / Footer settings page.
 *
 * @package bb-powerpack
 */
if ( ! defined( 'ABSPATH' ) ) {	
	exit; // Exit if accessed directly.
}
?>
<div class="pp-admin-settings-content-head">
	<h3><?php esc_html_e('Header / Footer', 'bb-powerpack'); ?></h3>
	<p class="description"><?php esc_html_e( 'You can set custom header and footer templates for your site.', 'bb-powerpack' ); ?></p>
</div>
<?php if ( BB_PowerPack_Header_Footer::get_theme_support_slug() ) { ?>
<form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">
	<table class="form-table">
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_header_footer_template_header"><?php esc_html_e('Header', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<select id="bb_powerpack_header_footer_template_header" name="bb_powerpack_header_footer_template_header" style="min-width: 200px;">
					<?php $selected = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_header_footer_template_header', true); ?>
					<?php echo BB_PowerPack_Header_Footer::get_templates_html( $selected ); ?>
				</select>
				<p class="description">
					<span class="desc--template-select"><?php _e('Select a template for header.', 'bb-powerpack'); ?></span>
					<span class="desc--template-edit"><a href="" class="edit-template" target="_blank"><?php _e('Edit', 'bb-powerpack'); ?></a></span>
				</p>
			</td>
		</tr>
		<tr align="top" id="field-bb_powerpack_header_footer_fixed_header">
			<th scope="row" valign="top"></th>
			<td>
				<label for="bb_powerpack_header_footer_fixed_header" style="font-weight: 500;">
					<?php $checked = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_header_footer_fixed_header', true); ?>
					<input type="checkbox" id="bb_powerpack_header_footer_fixed_header" class="dashicons" name="bb_powerpack_header_footer_fixed_header" value="1"<?php echo $checked ? ' checked="checked"' : ''; ?> />
					<?php esc_html_e('Fixed Header, also enable it on', 'bb-powerpack'); ?>
				</label>
				<?php $devices = (array) BB_PowerPack_Admin_Settings::get_option('bb_powerpack_header_footer_fixed_header_devices', true); ?>
				<label for="bb_powerpack_header_footer_fixed_header_tablet" style="font-weight: 400;">
					<input type="checkbox" id="bb_powerpack_header_footer_fixed_header_tablet" class="dashicons" name="bb_powerpack_header_footer_fixed_header_devices[]" value="tablet"<?php echo in_array( 'tablet', $devices ) ? ' checked="checked"' : ''; ?> />
					<?php esc_html_e('Tablet', 'bb-powerpack'); ?>
				</label>
				<label for="bb_powerpack_header_footer_fixed_header_mobile" style="font-weight: 400;">
					<input type="checkbox" id="bb_powerpack_header_footer_fixed_header_mobile" class="dashicons" name="bb_powerpack_header_footer_fixed_header_devices[]" value="mobile"<?php echo in_array( 'mobile', $devices ) ? ' checked="checked"' : ''; ?> />
					<?php esc_html_e('Mobile', 'bb-powerpack'); ?>
				</label>
				<p class="description">
					<?php _e('Stick this header to the top of the window as the page is scrolled.', 'bb-powerpack'); ?>
				</p>
			</td>
		</tr>
		<tr align="top" id="field-bb_powerpack_header_footer_shrink_header">
			<th scope="row" valign="top"></th>
			<td>
				<label for="bb_powerpack_header_footer_shrink_header" style="font-weight: 500;">
					<?php $checked = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_header_footer_shrink_header', true); ?>
					<input type="checkbox" id="bb_powerpack_header_footer_shrink_header" class="dashicons" name="bb_powerpack_header_footer_shrink_header" value="1"<?php echo $checked ? ' checked="checked"' : ''; ?> />
					<?php esc_html_e('Shrink Header', 'bb-powerpack'); ?>
				</label>
				<p class="description">
					<?php _e('Shrink this header when the page is scrolled.', 'bb-powerpack'); ?>
				</p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top"></th>
			<td>
				<label for="bb_powerpack_header_footer_overlay_header" style="font-weight: 500;">
					<?php $checked = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_header_footer_overlay_header', true); ?>
					<input type="checkbox" id="bb_powerpack_header_footer_overlay_header" class="dashicons" name="bb_powerpack_header_footer_overlay_header" value="1"<?php echo $checked ? ' checked="checked"' : ''; ?> />
					<?php esc_html_e('Overlay Header', 'bb-powerpack'); ?>
				</label>
				<p class="description">
					<?php _e('Overlay this header on top of the page content with a transparent background.', 'bb-powerpack'); ?>
				</p>
			</td>
		</tr>
		<tr align="top" id="field-bb_powerpack_header_footer_overlay_header_bg">
			<th scope="row" valign="top"></th>
			<td>
				<label for="bb_powerpack_header_footer_overlay_header_bg" style="font-weight: 500;">
					<?php esc_html_e('Overlay Header Background', 'bb-powerpack'); ?>
				</label>
				<?php $selected = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_header_footer_overlay_header_bg', true); ?>
				<select id="bb_powerpack_header_footer_overlay_header_bg" name="bb_powerpack_header_footer_overlay_header_bg">
					<option value="default"<?php echo ( 'default' == $selected ) ? ' selected="selected"' : ''; ?>><?php esc_html_e('Default', 'bb-powerpack'); ?></option>
					<option value="transparent"<?php echo ( 'transparent' == $selected ) ? ' selected="selected"' : ''; ?>><?php esc_html_e('Transparent', 'bb-powerpack'); ?></option>
				</select>
				<p class="description">
					<?php _e('Use either the default background color or transparent background color until the page is scrolled.', 'bb-powerpack'); ?>
				</p>
			</td>
		</tr>
		<tr align="top">
			<th scope="row" valign="top">
				<label for="bb_powerpack_header_footer_template_footer"><?php esc_html_e('Footer', 'bb-powerpack'); ?></label>
			</th>
			<td>
				<select id="bb_powerpack_header_footer_template_footer" name="bb_powerpack_header_footer_template_footer" style="min-width: 200px;">
					<?php $selected = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_header_footer_template_footer', true); ?>
					<?php echo BB_PowerPack_Header_Footer::get_templates_html( $selected ); ?>
				</select>
				<p class="description">
					<span class="desc--template-select"><?php _e('Select a template for footer.', 'bb-powerpack'); ?></span>
					<span class="desc--template-edit"><a href="" class="edit-template" target="_blank"><?php _e('Edit', 'bb-powerpack'); ?></a></span>
				</p>
			</td>
		</tr>
	</table>

	<input type="hidden" name="bb_powerpack_header_footer_page" value="1" />
	<?php submit_button(); ?>
	<?php do_action( 'pp_admin_settings_before_form_close', $current_tab ); ?>
</form>
<script type="text/javascript">
(function($) {
	$('#bb_powerpack_header_footer_template_header, #bb_powerpack_header_footer_template_footer').on('change', function() {
		$(this).parent().find('.description span').hide();
		if ( $(this).val() === '' ) {
			$(this).parent().find('.desc--template-select').show();
		} else {
			$(this).parent().find('.desc--template-edit')
				.show()
				.find('a.edit-template').attr('href', '<?php echo home_url(); ?>?p=' + $(this).val() + '&fl_builder');
		}
	}).trigger('change');

	$('#bb_powerpack_header_footer_fixed_header').on('change', function() {
		if ( $(this).is(':checked') ) {
			$('#field-bb_powerpack_header_footer_shrink_header').show();
		} else {
			$('#field-bb_powerpack_header_footer_shrink_header').hide();
		}
	}).trigger('change');

	$('#bb_powerpack_header_footer_overlay_header').on('change', function() {
		if ( $(this).is(':checked') ) {
			$('#field-bb_powerpack_header_footer_overlay_header_bg').show();
		} else {
			$('#field-bb_powerpack_header_footer_overlay_header_bg').hide();
		}
	}).trigger('change');
})(jQuery);
</script>
<?php } else { ?>
	<div>
		<p style="color: red; font-size: 14px;"><?php esc_html_e( 'This feature does not support your current theme.', 'bb-powerpack' ); ?></p>
	</div>
<?php } ?>