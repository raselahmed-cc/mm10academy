.fl-node-<?php echo $id; ?> .pp-login-form {
	<?php if ( ! empty( $settings->form_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->form_bg_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-login-form-wrap.pp-event-disabled:before {
	background-image: url(<?php echo BB_POWERPACK_URL; ?>assets/images/spinner.gif);
}
<?php if ( isset( $settings->social_button_position ) && 'above' === $settings->social_button_position ) { ?>
	.fl-node-<?php echo $id; ?> .pp-login-form-inner,
	.fl-node-<?php echo $id; ?> .pp-login-form-inner .pp-social-login {
		display: flex;
		flex-direction: column-reverse;
	}
	.fl-node-<?php echo $id; ?> .pp-login-form-inner .pp-social-login-wrap {
		margin-top: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-login-form-sep {
		margin-bottom: 20px;
	}
<?php } ?>

<?php
// Form padding.
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'form_padding',
	'selector'		=> ".fl-node-$id .pp-login-form",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'form_padding_top',
		'padding-right' 	=> 'form_padding_right',
		'padding-bottom' 	=> 'form_padding_bottom',
		'padding-left' 		=> 'form_padding_left',
	),	
) );

// Form border.
FLBuilderCSS::border_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'form_border',
	'selector'		=> ".fl-node-$id .pp-login-form"
) );
?>

.fl-node-<?php echo $id; ?> .pp-field-group {
	<?php if ( ! empty( $settings->fields_spacing ) ) { ?>
	margin-bottom: <?php echo $settings->fields_spacing; ?>px;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-field-group:last-of-type {
	margin-bottom: 0;
}

.fl-node-<?php echo $id; ?> .pp-field-group > a {
	<?php if ( ! empty( $settings->links_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->links_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-field-group > a:hover,
.fl-node-<?php echo $id; ?> .pp-field-group > a:focus {
	<?php if ( ! empty( $settings->links_hover_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->links_hover_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-field-group > label {
	<?php if ( ! empty( $settings->label_spacing ) ) { ?>
	margin-bottom: <?php echo $settings->label_spacing; ?>px;
	<?php } ?>
	<?php if ( ! empty( $settings->label_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_color ); ?>;
	<?php } ?>
}
<?php
// Label Typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'label_typography',
	'selector'		=> ".fl-node-$id .pp-field-group > label",
) );
?>

.fl-node-<?php echo $id; ?> .pp-login-form .pp-login-form-field button.pp-lf-toggle-pw svg path {
	<?php if ( ! empty( $settings->field_text_color ) ) { ?>
		fill: <?php echo pp_get_color_value( $settings->field_text_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--input {
	<?php if ( ! empty( $settings->field_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->field_text_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->field_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->field_bg_color ); ?>;
	<?php } ?>
}

<?php if ( isset( $settings->field_placeholder_color ) && ! empty( $settings->field_placeholder_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--input::-webkit-input-placeholder {
	color: <?php echo pp_get_color_value( $settings->field_placeholder_color ); ?>;
}
.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--input::-ms-input-placeholder {
	color: <?php echo pp_get_color_value( $settings->field_placeholder_color ); ?>;
}
.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--input::-moz-placeholder {
	color: <?php echo pp_get_color_value( $settings->field_placeholder_color ); ?>;
}
.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--input:-moz-placeholder {
	color: <?php echo pp_get_color_value( $settings->field_placeholder_color ); ?>;
}
.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--input::placeholder {
	color: <?php echo pp_get_color_value( $settings->field_placeholder_color ); ?>;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--input:focus,
.fl-node-<?php echo $id; ?> .pp-field-group input[type="checkbox"]:focus {
	<?php if ( ! empty( $settings->field_border_focus_color ) ) { ?>
	border-color: <?php echo pp_get_color_value( $settings->field_border_focus_color ); ?>;
	<?php } ?>
}
<?php
// Input border.
FLBuilderCSS::border_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'field_border',
	'selector'		=> ".fl-node-$id .pp-field-group .pp-login-form--input"
) );

// Input height.
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'field_height',
	'selector'		=> ".fl-node-$id .pp-field-group .pp-login-form--input",
	'prop'			=> 'height',
	'unit'			=> 'px'
) );

// Input padding.
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'field_padding',
	'selector'		=> ".fl-node-$id .pp-field-group .pp-login-form--input",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'field_padding_top',
		'padding-right' 	=> 'field_padding_right',
		'padding-bottom' 	=> 'field_padding_bottom',
		'padding-left' 		=> 'field_padding_left',
	),
) );

// Input Typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'fields_typography',
	'selector'		=> ".fl-node-$id .pp-field-group .pp-login-form--input",
) );
?>

.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--button {
	<?php if ( ! empty( $settings->button_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->button_text_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->button_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->button_bg_color ); ?>;
	<?php } ?>
}
<?php
// Button align.
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'button_align',
	'selector'		=> ".fl-node-$id .pp-field-group.pp-field-type-submit, .fl-node-$id .pp-field-group.pp-field-type-link, .fl-node-$id .pp-field-group.pp-field-type-recaptcha, .fl-node-$id .pp-field-group.pp-field-type-hcaptcha",
	'prop'			=> 'text-align'
) );

// Button border.
FLBuilderCSS::border_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'button_border',
	'selector'		=> ".fl-node-$id .pp-field-group .pp-login-form--button"
) );

// Button padding.
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'button_padding',
	'selector'		=> ".fl-node-$id .pp-field-group .pp-login-form--button",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'button_padding_top',
		'padding-right' 	=> 'button_padding_right',
		'padding-bottom' 	=> 'button_padding_bottom',
		'padding-left' 		=> 'button_padding_left',
	),
) );

// Button width.
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'button_width',
	'selector'		=> ".fl-node-$id .pp-field-group .pp-login-form--button",
	'prop'			=> 'width',
	'unit'			=> $settings->button_width_unit
) );

// Button Typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'button_typography',
	'selector'		=> ".fl-node-$id .pp-field-group .pp-login-form--button .pp-login-form--button-text",
) );
?>

.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--button:hover,
.fl-node-<?php echo $id; ?> .pp-field-group .pp-login-form--button:focus {
	<?php if ( ! empty( $settings->button_text_hover_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->button_text_hover_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->button_bg_hover_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->button_bg_hover_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->button_border_hover_color ) ) { ?>
	border-color: <?php echo pp_get_color_value( $settings->button_border_hover_color ); ?>;
	<?php } ?>
}

<?php
/******************************
 * Social Login Buttons.
 ******************************/
?>
.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-wrap {
	<?php if ( 'left' === $settings->social_button_alignment ) { ?>
		justify-content: flex-start;
		align-items: flex-start;
	<?php } elseif ( 'right' === $settings->social_button_alignment ) { ?>
		justify-content: flex-end;
		align-items: flex-end;
	<?php } ?>
}
<?php
// Social Buttons width.
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'social_button_width',
	'selector'		=> ".fl-node-$id .pp-login-form .pp-social-login-wrap .pp-social-login-button",
	'prop'			=> 'width',
	'unit'			=> $settings->social_button_width_unit
) );

// Social Buttons Spacing.
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'social_button_spacing',
	'selector'		=> ".fl-node-$id .pp-login-form .pp-social-login-wrap .pp-social-login-button",
	'prop'			=> 'inline' === $settings->social_button_layout ? 'margin-right' : 'margin-bottom',
	'unit'			=> 'px',
) );

// Social Buttons Typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'social_button_typography',
	'selector'		=> ".fl-node-$id .pp-login-form .pp-social-login-button .pp-social-login-label",
) );
?>

<?php
// Button Type - Custom
if ( 'custom' === $settings->social_button_type ) :
	// Facebook - Button border.
	FLBuilderCSS::border_field_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'social_button_fb_border',
		'selector'		=> ".fl-node-$id .pp-login-form .pp-social-login-button.pp-fb-login-button"
	) );
?>
.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-button.pp-fb-login-button {
	<?php if ( ! empty( $settings->social_button_fb_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->social_button_fb_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->social_button_fb_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->social_button_fb_bg_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-button.pp-fb-login-button:hover {
	<?php if ( ! empty( $settings->social_button_fb_hover_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->social_button_fb_hover_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->social_button_fb_hover_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->social_button_fb_hover_bg_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->social_button_fb_border_hover_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->social_button_fb_border_hover_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-button.pp-fb-login-button span svg path {
	<?php if ( ! empty( $settings->social_button_fb_color ) ) { ?>
		fill: <?php echo pp_get_color_value( $settings->social_button_fb_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-button.pp-fb-login-button:hover span svg path {
	<?php if ( ! empty( $settings->social_button_fb_hover_color ) ) { ?>
		fill: <?php echo pp_get_color_value( $settings->social_button_fb_hover_color ); ?>;
	<?php } ?>
}
<?php endif; ?>

.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-button.pp-google-login-button[data-theme="filled_blue"] {
	background-color: #1a73e8;
}
.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-button.pp-google-login-button[data-theme="filled_black"] {
	background-color: #000;
}
.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-button.pp-google-login-button[data-theme="outline"] {
	background-color: #fff;
	border: 1px solid #ddd;
}
.fl-node-<?php echo $id; ?> .pp-login-form .pp-social-login-button.pp-google-login-button[data-theme="outline"] * {
	color: #333;
}

<?php
// Separator.
if ( isset( $settings->separator ) && 'no' !== $settings->separator ) :
	// Separator Text Typography.
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'separator_text_typography',
		'selector'		=> ".fl-node-$id .pp-login-form .pp-login-form-sep-text",
	) );

	// Separator width.
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'separator_width',
		'selector'		=> ".fl-node-$id .pp-login-form-sep-text:before, .fl-node-$id .pp-login-form-sep-text:after",
		'prop'			=> 'width',
		'unit'			=> 'px',
	) );

	// Separator spacing.
	FLBuilderCSS::dimension_field_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'separator_spacing',
		'selector'		=> ".fl-node-$id .pp-login-form-sep",
		'unit'			=> 'px',
		'props'			=> array(
			'margin-top' 		=> 'separator_spacing_top',
			'margin-right' 		=> 'separator_spacing_right',
			'margin-bottom' 	=> 'separator_spacing_bottom',
			'margin-left' 		=> 'separator_spacing_left',
		),
	) );
	?>
	.fl-node-<?php echo $id; ?> .pp-login-form-sep {
		text-align: <?php echo $settings->social_button_alignment; ?>;
	}
	<?php if ( 'left' === $settings->social_button_alignment ) { ?>
		.fl-node-<?php echo $id; ?> .pp-login-form-sep-text {
			padding-left: 0;
		}
		.fl-node-<?php echo $id; ?> .pp-login-form-sep-text:before {
			content: none;
		}
	<?php } elseif ( 'right' === $settings->social_button_alignment ) { ?>
		.fl-node-<?php echo $id; ?> .pp-login-form-sep-text {
			padding-right: 0;
		}
		.fl-node-<?php echo $id; ?> .pp-login-form-sep-text:after {
			content: none;
		}
	<?php } ?>
	.fl-node-<?php echo $id; ?> .pp-login-form-sep-text:before,
	.fl-node-<?php echo $id; ?> .pp-login-form-sep-text:after {
		<?php if ( ! empty( $settings->separator_color ) ) { ?>
			color: <?php echo pp_get_color_value( $settings->separator_color ); ?>;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-login-form-sep-text {
		<?php if ( ! empty( $settings->separator_text_color ) ) { ?>
			color: <?php echo pp_get_color_value( $settings->separator_text_color ); ?>;
		<?php } ?>
	}
<?php endif; ?>
