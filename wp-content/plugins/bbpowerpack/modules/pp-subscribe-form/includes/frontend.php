<?php
$messages    = $module->get_strings_i18n();
$show_labels = isset( $settings->show_labels ) && 'show' === $settings->show_labels ? true : false;
$name        = isset( $_GET['pp_name'] ) ? sanitize_text_field( $_GET['pp_name'] ) : '';
$email       = isset( $_GET['pp_email'] ) ? sanitize_email( $_GET['pp_email'] ) : '';

$recaptcha_site_key   = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_site_key' );
$recaptcha_secret_key = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_recaptcha_secret_key' );
$hcaptcha_sitekey     = BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_hcaptcha_site_key' );
?>
<?php if ( 'standard' != $settings->box_type && 'fixed_bottom' != $settings->box_type ) { ?>
	<div class="pp-subscribe-<?php echo $id; ?> pp-subscribe-<?php echo esc_attr( $settings->box_type ); ?> pp-subscribe-box fl-node-<?php echo $id; ?> pp-clearfix">
		<div class="pp-subscribe-inner">
			<a href="#" class="pp-box-close" aria-label="<?php esc_attr_e( 'Close', 'bb-powerpack' ); ?>">
				<svg class="pp-box-close-svg" viewbox="0 0 40 40">
			    	<path d="M 10,10 L 30,30 M 30,10 L 10,30" />
			  	</svg>
			</a>
			<div class="pp-subscribe-body">
				<div class="pp-subscribe-content">
					<?php echo $settings->box_content; ?>
				</div>
<?php } ?>

	<div class="pp-subscribe-form pp-subscribe-form-<?php echo esc_attr( $settings->layout ); ?> pp-subscribe-form-name-<?php echo esc_attr( $settings->show_name ); ?> pp-form pp-clearfix" <?php if ( isset( $module->template_id ) ) echo 'data-template-id="' . $module->template_id . '" data-template-node-id="' . $module->template_node_id . '"'; ?>>

		<?php if ( 'fixed_bottom' == $settings->box_type ) { ?>
			<div class="pp-box-close"></div>
		<?php } ?>

		<div class="pp-subscribe-form-inner pp-clearfix">

			<?php if ( ('standard' == $settings->box_type || 'fixed_bottom' == $settings->box_type) && 'yes' == $settings->show_content ) { ?>
			<div class="pp-subscribe-content">
				<?php echo $settings->box_content; ?>
			</div>
			<?php } ?>

			<?php if ( 'show' == $settings->show_name ) : ?>

				<div class="pp-form-field pp-name-field">
				<?php
					if ( $show_labels && isset( $settings->input_name_label ) && ! empty( $settings->input_name_label ) ) { ?>
						<label for="pp-subscribe-form-name-<?php echo $id; ?>"><?php echo $settings->input_name_label; ?></label>
					<?php
					}
				 ?>
					<input id="pp-subscribe-form-name-<?php echo $id; ?>" type="text" name="pp-subscribe-form-name" placeholder="<?php echo esc_attr( $settings->input_name_placeholder ); ?>" value="<?php echo esc_attr( $name ); ?>" />
					<div class="pp-form-error-message"><?php echo $messages['empty_name']; ?></div>
				</div>

			<?php endif; ?>

			<?php do_action( 'pp_subscribe_form_before_email_field', $settings ); ?>

			<div class="pp-form-field pp-email-field">
			<?php
					if ( $show_labels && isset( $settings->input_email_label ) && ! empty( $settings->input_email_label ) ) { ?>
						<label for="pp-subscribe-form-email-<?php echo $id; ?>"><?php echo $settings->input_email_label; ?></label>
					<?php
					}
				 ?>
				<input id="pp-subscribe-form-email-<?php echo $id; ?>" type="email" name="pp-subscribe-form-email" placeholder="<?php echo esc_attr( $settings->input_email_placeholder ); ?>" value="<?php echo esc_attr( $email ); ?>" />
				<div class="pp-form-error-message"><?php echo $messages['empty_invalid_email']; ?></div>
			</div>

			<?php do_action( 'pp_subscribe_form_after_email_field', $settings ); ?>

			<?php if ( ( 'stacked' == $settings->layout || 'compact' == $settings->layout ) ) : ?>
				<?php if ( isset( $settings->checkbox_field ) && 'show' == $settings->checkbox_field ) : ?>
				<div class="pp-form-field pp-acceptance-field pp-checkbox-input">
					<input type="checkbox" name="pp-subscribe-form-acceptance" id="pp-subscribe-form-acceptance-<?php echo $id; ?>" value="1" />
					<label for="pp-subscribe-form-acceptance-<?php echo $id; ?>"><?php echo $settings->checkbox_field_text; ?></label>
					<div class="pp-form-error-message"><?php echo $messages['not_checked']; ?></div>
				</div>
				<?php endif; ?>

				<?php if ( isset( $settings->recaptcha_toggle ) && 'show' == $settings->recaptcha_toggle && ! empty( $recaptcha_site_key ) ) : ?>
				<div class="pp-form-field pp-recaptcha">
					<div id="<?php echo $id; ?>-pp-grecaptcha" class="pp-grecaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>"<?php if ( isset( $settings->recaptcha_validate_type ) ) { echo ' data-validate="' . $settings->recaptcha_validate_type . '"';} ?><?php if ( isset( $settings->recaptcha_theme ) ) { echo ' data-theme="' . $settings->recaptcha_theme . '"';} ?>></div>
					<p class="pp-form-error-message"><?php echo $messages['captcha_error']; ?></p>
				</div>
				<?php endif; ?>

				<?php if ( isset( $settings->hcaptcha_toggle ) && 'show' == $settings->hcaptcha_toggle && ! empty( $hcaptcha_sitekey ) ) : ?>
				<div class="pp-form-field pp-hcaptcha">
					<div id="<?php echo $id; ?>-pp-hcaptcha" class="h-captcha" data-sitekey="<?php echo $hcaptcha_sitekey; ?>"></div>
					<p class="pp-form-error-message"><?php echo $messages['captcha_error']; ?></p>
				</div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="pp-form-button pp-button-wrap" data-wait-text="<?php echo $messages['wait_text']; ?>">

				<?php

					FLBuilder::render_module_html( 'fl-button', array(
						'align'             => '',
						'bg_color'          => $settings->btn_bg_color,
						'bg_hover_color'    => $settings->btn_bg_hover_color,
						'bg_opacity'        => 100,
						'bg_hover_opacity'  => 100,
						'icon'              => $settings->btn_icon,
						'icon_position'     => $settings->btn_icon_position,
						'icon_animation'    => $settings->btn_icon_animation,
						'link'              => '#',
						'link_target'       => '_self',
						'style'             => $settings->btn_style,
						'text'              => $settings->btn_text,
						'text_color'        => $settings->btn_text_color,
						'text_hover_color'  => $settings->btn_text_hover_color,
						'width'             => 'full',
						'class'				=> 'pp-button'
					));

				?>

			</div>

			<?php if ( 'inline' == $settings->layout ) : ?>
				<?php if ( isset( $settings->checkbox_field ) && 'show' == $settings->checkbox_field ) : ?>
				<div class="pp-form-field pp-acceptance-field pp-checkbox-input">
					<input type="checkbox" name="pp-subscribe-form-acceptance" id="pp-subscribe-form-acceptance-<?php echo $id; ?>" value="1" />
					<label for="pp-subscribe-form-acceptance-<?php echo $id; ?>"><?php echo $settings->checkbox_field_text; ?></label>
					<div class="pp-form-error-message"><?php echo $messages['not_checked']; ?></div>
				</div>
				<?php endif; ?>

				<?php if ( isset( $settings->recaptcha_toggle ) && 'show' == $settings->recaptcha_toggle && ! empty( $recaptcha_site_key ) ) : ?>
				<div class="pp-form-field pp-recaptcha">
					<div id="<?php echo $id; ?>-pp-grecaptcha" class="pp-grecaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>"<?php if ( isset( $settings->recaptcha_validate_type ) ) { echo ' data-validate="' . $settings->recaptcha_validate_type . '"';} ?><?php if ( isset( $settings->recaptcha_theme ) ) { echo ' data-theme="' . $settings->recaptcha_theme . '"';} ?>></div>
					<p class="pp-form-error-message"><?php echo $messages['captcha_error']; ?></p>
				</div>
				<?php endif; ?>

				<?php if ( isset( $settings->hcaptcha_toggle ) && 'show' == $settings->hcaptcha_toggle && ! empty( $hcaptcha_sitekey ) ) : ?>
				<div class="pp-form-field pp-hcaptcha">
					<div id="<?php echo $id; ?>-pp-hcaptcha" class="h-captcha" data-sitekey="<?php echo $hcaptcha_sitekey; ?>"></div>
					<p class="pp-form-error-message"><?php echo $messages['captcha_error']; ?></p>
				</div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="pp-form-error-message"><?php echo $messages['form_error']; ?></div>
		</div>
		<?php if( '' != $settings->footer_text ) { ?>
			<div class="pp-subscribe-form-footer">
				<?php echo $settings->footer_text; ?>
			</div>
		<?php } ?>
	</div>

<?php if ( 'standard' != $settings->box_type && 'fixed_bottom' != $settings->box_type ) { ?>
			</div><!-- .pp-subscribe-body -->
		</div><!-- .pp-subscribe-inner -->
	</div>
<?php } ?>
