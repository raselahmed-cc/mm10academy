<?php
$fb_app_id = pp_get_fb_app_id();
$fb_sdk_url = pp_get_fb_sdk_url( $fb_app_id );
$messages = $module->get_js_messages_i18n();
?>
;(function($) {

	new PPLoginForm({
		id: '<?php echo $id; ?>',
		messages: <?php echo json_encode( $messages ); ?>,
		page_url: '<?php echo get_permalink(); ?>',
		facebook_login: <?php echo isset( $settings->facebook_login ) && 'yes' === $settings->facebook_login ? 'true' : 'false'; ?>,
		facebook_app_id: '<?php echo $fb_app_id; ?>',
		facebook_sdk_url: '<?php echo $fb_sdk_url; ?>',
		google_login: <?php echo isset( $settings->google_login ) && 'yes' === $settings->google_login ? 'true' : 'false'; ?>,
		google_client_id: '<?php echo BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_google_client_id' ); ?>',
		ajaxurl: '<?php echo admin_url( 'admin-ajax.php' ); ?>'
	});

})(jQuery);