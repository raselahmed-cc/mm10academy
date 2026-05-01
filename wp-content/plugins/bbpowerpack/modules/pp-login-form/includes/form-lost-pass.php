<?php

defined( 'ABSPATH' ) || exit;

do_action( 'pp_login_form_before_lost_password_form', $settings, $id );
?>
<form method="post" class="pp-login-form pp-login-form--lost-pass">
	<p><?php echo apply_filters( 'pp_login_form_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'bb-powerpack' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>
	<div class="pp-login-form-fields">
		<div class="pp-login-form-field pp-field-group pp-field-type-text">
			<?php if ( $show_label ) { ?>
			<label for="user_login"><?php echo $settings->username_label; ?></label>
			<?php } ?>
			<input class="pp-login-form--input" type="text" name="user_login" id="user_login" size="1" autocomplete="username" placeholder="<?php echo esc_attr( $settings->username_placeholder ); ?>" />
		</div>
		<?php
		// Render reCAPTCHA field.
		if ( 'yes' === $settings->enable_recaptcha ) {
			$module->render_recaptcha_field( $id );
		}

		// Render hCaptcha field.
		if ( isset( $settings->enable_hcaptcha ) && 'yes' === $settings->enable_hcaptcha ) {
			$module->render_hcaptcha_field( $id );
		}

		/**
		 * Fires inside the lostpassword form tags, before the hidden fields.
		 *
		 * @since 2.21.0
		 */
		do_action( 'lostpassword_form' );
		?>
		<div class="pp-field-group pp-field-type-submit">
			<button type="submit" name="pp-login-form-lost-pw" class="pp-login-form--button">
				<span class="pp-login-form--button-text"><?php esc_html_e( 'Reset password', 'bb-powerpack' ); ?></span>
			</button>
		</div>
	</div>

	<?php wp_nonce_field( 'lost_password', 'pp-lf-lost-password-nonce' ); ?>
</form>
<?php
do_action( 'pp_login_form_after_lost_password_form', $settings, $id );