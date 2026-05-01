<?php
global $post;

$post_id = ( $post instanceof WP_Post ) ? $post->ID : 0;

$title_tag = isset( $settings->title_tag ) ? esc_attr( $settings->title_tag ) : 'h3';

if ( isset( $settings->recaptcha_key_source ) && 'default' == $settings->recaptcha_key_source ) {
	$recaptcha_site_key = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_site_key' );
	$recaptcha_secret_key = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_secret_key' );
} else {
	$recaptcha_site_key = $settings->recaptcha_site_key;
	$recaptcha_secret_key = $settings->recaptcha_secret_key;
}
$hcaptcha_sitekey = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_hcaptcha_site_key' );

$messages = $module->get_strings_i18n();
?>

<form class="pp-contact-form pp-form-<?php echo esc_attr( $settings->form_layout ); ?>" <?php if ( isset( $module->template_id ) ) echo 'data-template-id="' . $module->template_id . '" data-template-node-id="' . $module->template_node_id . '"'; ?>>
	<input type="hidden" name="fl-layout-id" value="<?php echo $post_id; ?>" />
	<?php if ( ! empty( $settings->custom_title ) ) { ?>
    	<<?php echo $title_tag; ?> class="pp-form-title"><?php echo $settings->custom_title; ?></<?php echo $title_tag; ?>>
	<?php } ?>
	<?php if ( ! empty( $settings->custom_description ) ) { ?>
		<p class="pp-form-description"><?php echo $settings->custom_description; ?></p>
	<?php } ?>
    <div class="pp-contact-form-inner pp-clearfix">
        <?php if( $settings->form_layout == 'stacked-inline' ) { ?>
            <div class="pp-contact-form-fields-left">
        <?php } ?>
    	<?php if ($settings->name_toggle == 'show') : ?>
    	<div class="pp-input-group pp-name<?php echo ! isset( $settings->name_required ) || 'yes' === $settings->name_required ? ' pp-input-required' : ''; ?>">
    		<label for="pp-name-<?php echo $id; ?>"><?php echo ( ! isset( $settings->name_label ) ) ? _x( 'Name', 'Contact form Name field label.', 'bb-powerpack' ) : $settings->name_label;?></label>
    		<span class="pp-contact-error"><?php echo $messages['empty_name']; ?></span>
    		<input type="text" name="pp-name" id="pp-name-<?php echo $id; ?>" value="" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php echo ! empty($settings->name_label) ? esc_attr( do_shortcode( $settings->name_label ) ) : esc_attr__( 'Name', 'bb-powerpack' ); ?>" <?php } ?> />
    	</div>
    	<?php endif; ?>

    	<?php if ($settings->email_toggle == 'show') : ?>
    	<div class="pp-input-group pp-email<?php echo ! isset( $settings->email_required ) || 'yes' === $settings->email_required ? ' pp-input-required' : ''; ?>">
    		<label for="pp-email-<?php echo $id; ?>"><?php echo ( ! isset( $settings->email_label ) ) ? _x( 'Email', 'Contact form Email field label.', 'bb-powerpack' ) : $settings->email_label;?></label>
    		<span class="pp-contact-error"><?php echo $messages['empty_email']; ?></span>
    		<input type="email" name="pp-email" id="pp-email-<?php echo $id; ?>" value="" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php echo ! empty($settings->email_label) ? esc_attr( do_shortcode( $settings->email_label ) ) : esc_attr__( 'Email', 'bb-powerpack' ); ?>" <?php } ?> />
    	</div>
    	<?php endif; ?>

    	<?php if ($settings->phone_toggle == 'show') : ?>
    	<div class="pp-input-group pp-phone<?php echo ! isset( $settings->phone_required ) || 'yes' === $settings->phone_required ? ' pp-input-required' : ''; ?>">
    		<label for="pp-phone-<?php echo $id; ?>"><?php echo ( ! isset( $settings->phone_label ) ) ? _x( 'Phone', 'Contact form Phone field label.', 'bb-powerpack' ) : $settings->phone_label;?></label>
    		<span class="pp-contact-error"><?php echo $messages['empty_phone']; ?></span>
    		<input type="tel" name="pp-phone" id="pp-phone-<?php echo $id; ?>" value="" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php echo ! empty($settings->phone_label) ? esc_attr( do_shortcode( $settings->phone_label ) ) : esc_attr__( 'Phone', 'bb-powerpack' ); ?>" <?php } ?> />
    	</div>
    	<?php endif; ?>

        <?php if( $settings->form_layout == 'stacked-inline' ) { ?>
        </div>
        <?php } ?>

        <?php if( $settings->form_layout == 'stacked-inline' ) { ?>
            <div class="pp-contact-form-fields-right">
        <?php } ?>

    	<?php if ($settings->subject_toggle == 'show') : ?>
    	<div class="pp-input-group pp-subject<?php echo ! isset( $settings->subject_required ) || 'yes' === $settings->subject_required ? ' pp-input-required' : ''; ?>">
    		<label for="pp-subject-<?php echo $id; ?>"><?php echo ( ! isset( $settings->subject_label ) ) ? _x( 'Subject', 'Contact form Subject field label.', 'bb-powerpack' ) : $settings->subject_label;?></label>
    		<span class="pp-contact-error"><?php echo $messages['empty_subject']; ?></span>
    		<input type="text" name="pp-subject" id="pp-subject-<?php echo $id; ?>" value="" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php echo ! empty($settings->subject_label) ? esc_attr( do_shortcode( $settings->subject_label ) ) : esc_attr__( 'Subject', 'bb-powerpack' ); ?>" <?php } ?> />
    	</div>
    	<?php endif; ?>

        <?php if ($settings->message_toggle == 'show') : ?>
    	<div class="pp-input-group pp-message<?php echo ! isset( $settings->message_required ) || 'yes' === $settings->message_required ? ' pp-input-required' : ''; ?>">
    		<label for="pp-message-<?php echo $id; ?>"><?php echo ( ! isset( $settings->message_label ) ) ? _x( 'Message', 'Contact form Message field label.', 'bb-powerpack' ) : $settings->message_label;?></label>
    		<span class="pp-contact-error"><?php echo $messages['empty_message']; ?></span>
    		<textarea name="pp-message" id="pp-message-<?php echo $id; ?>" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php echo ! empty($settings->message_label) ? esc_attr( do_shortcode( $settings->message_label ) ) : esc_attr__( 'Message', 'bb-powerpack' ); ?>" <?php } ?>></textarea>
    	</div>
        <?php endif; ?>

        <?php if( $settings->form_layout == 'stacked-inline' ) { ?>
        </div>
        <?php } ?>

		<?php
		if ( 'show' == $settings->recaptcha_toggle && ( isset( $recaptcha_site_key ) && ! empty( $recaptcha_site_key ) ) ) :
		?>
		<div class="pp-input-group pp-recaptcha">
			<p class="pp-contact-error"><?php echo $messages['captcha_error']; ?></p>
			<div id="<?php echo $id; ?>-pp-grecaptcha" class="pp-grecaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>"<?php if ( isset( $settings->recaptcha_validate_type ) ) { echo ' data-validate="' . $settings->recaptcha_validate_type . '"';} ?><?php if ( isset( $settings->recaptcha_theme ) ) { echo ' data-theme="' . esc_attr( $settings->recaptcha_theme ) . '"';} ?>></div>
		</div>
		<?php endif; ?>

		<?php if ( isset( $settings->hcaptcha_toggle ) && 'show' == $settings->hcaptcha_toggle && ! empty( $hcaptcha_sitekey ) ) : ?>
		<div class="pp-input-group pp-hcaptcha">
			<p class="pp-contact-error"><?php echo $messages['captcha_error']; ?></p>
			<div id="<?php echo $id; ?>-pp-hcaptcha" class="h-captcha" data-sitekey="<?php echo $hcaptcha_sitekey; ?>"></div>
		</div>
		<?php endif; ?>
	</div>
	
	<?php if ($settings->checkbox_toggle == 'show') : ?>
    	<div class="pp-input-group pp-checkbox<?php echo ! isset( $settings->checkbox_required ) || 'yes' === $settings->checkbox_required ? ' pp-input-required' : ''; ?>">
		<input type="checkbox" name="pp-checkbox" id="pp-checkbox_<?php echo $id; ?>" value="1"<?php echo ( isset( $settings->checked_default ) && 'yes' == $settings->checked_default ) ? ' checked="checked"' : ''; ?> />
		<label for="pp-checkbox_<?php echo $id; ?>"><?php echo ( ! isset( $settings->checkbox_label ) ) ? _x( 'I accept the Terms & Conditions', 'Contact form custom checkbox field label.', 'bb-powerpack' ) : do_shortcode( $settings->checkbox_label ); ?></label>
		<span class="pp-contact-error"><?php echo $messages['checkbox_error']; ?></span>
	</div>
	<?php endif; ?>

    <div class="pp-button-wrap fl-button-wrap">
    	<a href="#" target="_self" class="fl-button<?php if ('enable' == $settings->btn_icon_animation): ?> fl-button-icon-animation<?php endif; ?> pp-submit-button" role="button">
    		<?php if ( ! empty( $settings->btn_icon ) && ( 'before' == $settings->btn_icon_position || ! isset( $settings->btn_icon_position ) ) ) : ?>
    		<i class="fl-button-icon fl-button-icon-before fa <?php echo $settings->btn_icon; ?>" aria-hidden="true"></i>
    		<?php endif; ?>
    		<?php if ( ! empty( $settings->btn_text ) ) : ?>
    		<span class="fl-button-text"><?php echo $settings->btn_text; ?></span>
    		<?php endif; ?>
    		<?php if ( ! empty( $settings->btn_icon ) && 'after' == $settings->btn_icon_position ) : ?>
    		<i class="fl-button-icon fl-button-icon-after fa <?php echo $settings->btn_icon; ?>" aria-hidden="true"></i>
    		<?php endif; ?>
    	</a>
    </div>

	<?php if ( $settings->success_action == 'redirect' ) : ?>
		<input type="hidden" value="<?php echo $settings->success_url; ?>" class="pp-success-url">
	<?php elseif($settings->success_action == 'none') : ?>
		<span class="pp-success-none" style="display:none;"><?php echo $messages['sent_message']; ?></span>
	<?php endif; ?>
	<?php $error_msg = isset( $settings->error_message ) && ! empty( $settings->error_message ) ? $settings->error_message : $messages['failed_message']; ?>
	<span class="pp-send-error" style="display:none;"><?php echo $error_msg; ?></span>
</form>
<?php if ($settings->success_action == 'show_message' ) : ?>
  <span class="pp-success-msg" style="display:none;"><?php echo $settings->success_message; ?></span>
<?php endif; ?>
