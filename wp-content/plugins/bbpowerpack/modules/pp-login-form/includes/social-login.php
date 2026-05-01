<div class="pp-social-login">
	<?php if ( isset( $settings->separator ) && 'no' !== $settings->separator ) { ?>
	<div class="pp-login-form-sep">
		<span class="pp-login-form-sep-text"><?php echo $settings->separator_text; ?></span>
	</div>
	<?php } ?>
	<div class="pp-social-login-wrap pp-social-login--<?php echo esc_attr( $settings->social_button_type ); ?> pp-social-login--layout-<?php echo esc_attr( $settings->social_button_layout ); ?>">
		<?php if ( 'yes' === $settings->facebook_login && ( $is_builder_active || '' !== pp_get_fb_app_id() ) ) { ?>
			<div class="pp-fb-login-button pp-social-login-button" id="pp-fb-login-button" tabindex="0" role="button">
				<span class="pp-social-login-icon">
					<svg xmlns="http://www.w3.org/2000/svg">
						<path d="M22.688 0H1.323C.589 0 0 .589 0 1.322v21.356C0 23.41.59 24 1.323 24h11.505v-9.289H9.693V11.09h3.124V8.422c0-3.1 1.89-4.789 4.658-4.789 1.322 0 2.467.1 2.8.145v3.244h-1.922c-1.5 0-1.801.711-1.801 1.767V11.1h3.59l-.466 3.622h-3.113V24h6.114c.734 0 1.323-.589 1.323-1.322V1.322A1.302 1.302 0 0 0 22.688 0z"></path>
					</svg>
				</span>
				<span class="pp-social-login-label"><?php echo apply_filters( 'pp_lf_facebook_button_text', __( 'Facebook', 'bb-powerpack' ) ); ?></span>
			</div>
		<?php } ?>
		<?php if ( 'yes' === $settings->google_login ) { ?>
			<?php
			$btn_width = 250;
			if ( ! empty( $settings->social_button_width ) && 'px' === $settings->social_button_width_unit ) {
				$btn_width = $settings->social_button_width;
			}
			?>
			<?php if ( ! $is_builder_active ) { ?>
			<div id="g_id_onload"
				data-client_id="<?php echo esc_attr( BB_PowerPack_Admin_Settings::get_option( 'bb_powerpack_google_client_id' ) ); ?>"
				data-context="signin"
				data-ux_mode="popup"
				data-callback="onLoadGoogleSignIn"
				data-auto_prompt="false">
			</div>
			<div class="g_id_signin"
				data-type="standard"
				data-text="sign_in_with"
				data-size="large"
				data-theme="<?php echo isset( $settings->google_login_btn_theme ) ? esc_attr( $settings->google_login_btn_theme ) : 'outline'; ?>"
				data-logo_alignment="left"
				data-width="<?php echo esc_attr( $btn_width ); ?>">
			</div>
			<?php } else { ?>
			<div class="pp-google-login-button pp-social-login-button" id="pp-google-login-button" tabindex="0" role="button" data-theme="<?php echo isset( $settings->google_login_btn_theme ) ? esc_attr( $settings->google_login_btn_theme ) : 'outline'; ?>">
				<span class="pp-social-login-icon">
					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
						<g>
							<path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
							<path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
							<path fill="none" d="M0 0h48v48H0z"></path>
						</g>
					</svg>
				</span>
				<span class="pp-social-login-label"><?php _e( 'Sign in with Google', 'bb-powerpack' ); ?></span>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>