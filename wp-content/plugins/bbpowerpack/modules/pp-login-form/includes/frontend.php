<?php
$messages            = $module->get_js_messages_i18n();
$action              = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';
$current_url         = remove_query_arg( 'fake_arg' );
$redirect_url        = $current_url;
$logout_redirect_url = $current_url;
$show_label          = 'yes' == $settings->show_labels;
$show_lost_password  = 'yes' == $settings->show_lost_password;
$show_register       = 'yes' == $settings->show_register && get_option( 'users_can_register' );
$is_lost_password    = 'lost_pass' === $action || isset( $_GET['lost_pass'] );
$is_reset_password   = 'reset_pass' === $action || isset( $_GET['reset_pass'] );
$is_logged_in        = is_user_logged_in();
$is_builder_active   = FLBuilderModel::is_builder_active();
$reauth              = false;

/**
 * Fires before a specified login form action.
 *
 * The dynamic portion of the hook name, `$action`, refers to the action
 * that brought the visitor to the login form. Actions include 'lost_pass',
 * 'reset_pass', etc.
 *
 * @since 2.8.1
 */
do_action( "login_form_{$action}" );

if ( 'yes' == $settings->redirect_after_login && ! empty( $settings->redirect_url ) ) {
	$redirect_url = $settings->redirect_url;
}
// Update redirect URL if session has expired in WP admin.
if ( isset( $_GET['redirect_to'] ) && isset( $_GET['reauth'] ) ) {
	if ( ! empty( $_GET['redirect_to'] ) && $_GET['reauth'] ) {
		// Clear any stale cookies.
		//wp_clear_auth_cookie();
		// Get redirect URL.
		$redirect_url = $_GET['redirect_to'];
		$reauth = true;
	}
}
if ( 'yes' == $settings->redirect_after_logout && ! empty( $settings->redirect_logout_url ) ) {
	$logout_redirect_url = $settings->redirect_logout_url;
}
if ( ! isset( $_GET['key'] ) || empty( $_GET['key'] ) ) {
	$is_reset_password = false;
}
if ( ! isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
	$is_reset_password = false;
}

$args = apply_filters( 'pp_login_form_args', array(
	'redirect_url'        => $redirect_url,
	'logout_redirect_url' => $logout_redirect_url,
), $settings );
?>
<div class="pp-login-form-wrap">
	<?php if ( $is_logged_in && ! $is_builder_active && ! $reauth ) {
		if ( 'yes' == $settings->show_logged_in_message ) { $current_user = wp_get_current_user(); ?>
			<div class="pp-login-message">
				<?php
				// translators: Here %1$s is for current user's display name and %2$s is for logout URL.
				$msg = sprintf( __( 'You are Logged in as %1$s (<a href="%2$s">Logout</a>)', 'bb-powerpack' ), $current_user->display_name, wp_logout_url( $args['logout_redirect_url'] ) );
				echo apply_filters( 'pp_login_form_logged_in_message', $msg, $current_user->display_name, wp_logout_url( $args['logout_redirect_url'] ) );
				?>
			</div>
		<?php }
	} ?>

	<?php if ( ! $is_logged_in || $is_builder_active || $reauth ) { ?>
		<?php if ( ! $is_lost_password && ! $is_reset_password ) { ?>
			<?php if ( isset( $_GET['reset_success'] ) ) { ?>
				<p class="pp-lf-success pp-lf-pwd-reset-success"><?php echo $messages['reset_success']; ?></p>
			<?php } ?>
		<form class="pp-login-form" id="pp-form-<?php echo $id; ?>" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">
			<?php wp_nonce_field( 'login_nonce', 'pp-lf-login-nonce' ); ?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $args['redirect_url'] ); ?>">
			<?php if ( $reauth ) { ?>
				<input type="hidden" name="reauth" value="1" />
			<?php } ?>
			<div class="pp-login-form-inner">
				<div class="pp-login-form-fields">
					<div class="pp-login-form-field pp-field-group pp-field-type-text">
						<?php if ( $show_label ) { ?>
						<label for="user"><?php echo $settings->username_label; ?></label>
						<?php } ?>
						<div class="pp-field-inner">
							<input size="1" type="text" name="log" id="user" placeholder="<?php echo esc_attr( $settings->username_placeholder ); ?>" class="pp-login-form--input" />
						</div>
					</div>

					<div class="pp-login-form-field pp-field-group pp-field-type-password">
						<?php if ( $show_label ) { ?>
						<label for="password"><?php echo $settings->password_label; ?></label>
						<?php } ?>
						<div class="pp-field-inner">
							<input size="1" type="password" name="pwd" id="password" placeholder="<?php echo esc_attr( $settings->password_placeholder ); ?>" class="pp-login-form--input" />
							<button type="button" class="pp-lf-toggle-pw hide-if-no-js" aria-label="<?php _e( 'Show password', 'bb-powerpack' ); ?>">
								<span class="pw-visible" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M288 80c-65.2 0-118.8 29.6-159.9 67.7C89.6 183.5 63 226 49.4 256c13.6 30 40.2 72.5 78.6 108.3C169.2 402.4 222.8 432 288 432s118.8-29.6 159.9-67.7C486.4 328.5 513 286 526.6 256c-13.6-30-40.2-72.5-78.6-108.3C406.8 109.6 353.2 80 288 80zM95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c46.8 43.5 78.1 95.4 93 131.1c3.3 7.9 3.3 16.7 0 24.6c-14.9 35.7-46.2 87.7-93 131.1C433.5 443.2 368.8 480 288 480s-145.5-36.8-192.6-80.6C48.6 356 17.3 304 2.5 268.3c-3.3-7.9-3.3-16.7 0-24.6C17.3 208 48.6 156 95.4 112.6zM288 336c44.2 0 80-35.8 80-80s-35.8-80-80-80c-.7 0-1.3 0-2 0c1.3 5.1 2 10.5 2 16c0 35.3-28.7 64-64 64c-5.5 0-10.9-.7-16-2c0 .7 0 1.3 0 2c0 44.2 35.8 80 80 80zm0-208a128 128 0 1 1 0 256 128 128 0 1 1 0-256z"/></svg>
								</span>
								<span class="pw-hidden" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zm151 118.3C226 97.7 269.5 80 320 80c65.2 0 118.8 29.6 159.9 67.7C518.4 183.5 545 226 558.6 256c-12.6 28-36.6 66.8-70.9 100.9l-53.8-42.2c9.1-17.6 14.2-37.5 14.2-58.7c0-70.7-57.3-128-128-128c-32.2 0-61.7 11.9-84.2 31.5l-46.1-36.1zM394.9 284.2l-81.5-63.9c4.2-8.5 6.6-18.2 6.6-28.3c0-5.5-.7-10.9-2-16c.7 0 1.3 0 2 0c44.2 0 80 35.8 80 80c0 9.9-1.8 19.4-5.1 28.2zm9.4 130.3C378.8 425.4 350.7 432 320 432c-65.2 0-118.8-29.6-159.9-67.7C121.6 328.5 95 286 81.4 256c8.3-18.4 21.5-41.5 39.4-64.8L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5l-41.9-33zM192 256c0 70.7 57.3 128 128 128c13.3 0 26.1-2 38.2-5.8L302 334c-23.5-5.4-43.1-21.2-53.7-42.3l-56.1-44.2c-.2 2.8-.3 5.6-.3 8.5z"/></svg>
								</span>
							</button>
						</div>
					</div>

					<?php if ( 'yes' == $settings->show_remember_me ) { ?>
					<div class="pp-login-form-field pp-field-group pp-field-type-checkbox">
						<label for="pp-login-remember-me">
							<input type="checkbox" name="rememberme" id="pp-login-remember-me" class="pp-login-form--checkbox" />
							<span class="pp-login-remember-me"><?php echo ! empty( $settings->remember_me_text ) ? $settings->remember_me_text : __( 'Remember Me', 'bb-powerpack' ); ?></span>
						</label>
					</div>
					<?php } ?>

					<?php
					// Render reCAPTCHA field.
					if ( 'yes' === $settings->enable_recaptcha ) {
						$module->render_recaptcha_field( $id );
					}

					// Render hCaptcha field.
					if ( isset( $settings->enable_hcaptcha ) && 'yes' === $settings->enable_hcaptcha ) {
						$module->render_hcaptcha_field( $id );
					}
					?>

					<div class="pp-field-group pp-login-form-extra">
						<?php do_action( 'login_form' ); ?>
					</div>

					<div class="pp-field-group pp-field-type-submit">
						<button type="submit" name="wp-submit" class="pp-login-form--button pp-submit-button">
							<span class="pp-login-form--button-text"><?php echo $settings->button_text; ?></span>
						</button>
					</div>

					<?php if ( $show_lost_password || $show_register ) { ?>
					<div class="pp-field-group pp-field-type-link">
						<?php if ( $show_lost_password ) { ?>
							<a class="pp-login-lost-password" href="<?php echo add_query_arg( 'lost_pass', '1' ); ?>">
								<?php echo ! empty( $settings->lost_password_text ) ? $settings->lost_password_text : __( 'Lost your password?', 'bb-powerpack' ); ?>
							</a>
						<?php } ?>
						<?php if ( $show_register ) { ?>
							<?php if ( $show_lost_password ) { ?>
								<span class="pp-login-separator"> | </span>
							<?php } ?>
							<a class="pp-login-register" href="<?php echo $module->get_registration_url(); ?>">
								<?php echo isset( $settings->register_link_text ) && ! empty( $settings->register_link_text ) ? $settings->register_link_text : __( 'Register', 'bb-powerpack' ); ?>
							</a>
						<?php } ?>
					</div>
					<?php } ?>
				</div><!-- /.pp-login-form-fields -->
				<?php if ( $module->has_social_login() ) {
					include $module->dir . 'includes/social-login.php';
				} ?>
			</div>
		</form>
		<?php
		} elseif ( $is_lost_password && file_exists( $module->dir . 'includes/form-lost-pass.php' ) ) {
			include $module->dir . 'includes/form-lost-pass.php';
		} elseif ( $is_reset_password && file_exists( $module->dir . 'includes/form-reset-pass.php' ) ) {
			include $module->dir . 'includes/form-reset-pass.php';
		}
		?>
	<?php } ?>
</div>