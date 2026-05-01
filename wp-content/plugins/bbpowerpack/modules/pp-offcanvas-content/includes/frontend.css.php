<?php

// Offcanvas Border - Settings
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'offcanvas_bar_border',
		'selector'     => ".pp-offcanvas-content.pp-offcanvas-content-$id",
	)
);
// Offcanvas Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'offcanvas_bar_padding',
		'selector'     => ".pp-offcanvas-content.pp-offcanvas-content-$id",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'offcanvas_bar_padding_top',
			'padding-right'  => 'offcanvas_bar_padding_right',
			'padding-bottom' => 'offcanvas_bar_padding_bottom',
			'padding-left'   => 'offcanvas_bar_padding_left',
		),
	)
);

// Offcanvas Bar - Width
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'offcanvas_bar_width',
		'selector'     => ".pp-offcanvas-content.pp-offcanvas-content-$id",
		'prop'         => 'width',
	)
);

?>
.pp-offcanvas-content.pp-offcanvas-content-<?php echo $id; ?> {
	background: <?php echo pp_get_color_value( $settings->offcanvas_bar_bg ); ?>;
}
.pp-offcanvas-content.pp-offcanvas-content-<?php echo $id; ?>.pp-offcanvas-content-top,
.pp-offcanvas-content.pp-offcanvas-content-<?php echo $id; ?>.pp-offcanvas-content-bottom {
	width: 100%;
}

<?php
// Off-Canvas Content - Height
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'offcanvas_bar_width',
		'selector'     => ".pp-offcanvas-content.pp-offcanvas-content-$id.pp-offcanvas-content-top, .pp-offcanvas-content.pp-offcanvas-content-$id.pp-offcanvas-content-bottom",
		'prop'         => 'height',
	)
);
?>

.pp-offcanvas-content-<?php echo $id; ?>-open .pp-offcanvas-container:after {
	background: <?php echo pp_get_color_value( $settings->overlay_color ); ?>;
}
.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container,
.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container,
.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container {
	transform: translate3d(<?php echo $settings->offcanvas_bar_width; ?><?php echo $settings->offcanvas_bar_width_unit; ?>, 0, 0);
}
.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container,
.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container,
.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container {
	transform: translate3d(-<?php echo $settings->offcanvas_bar_width; ?><?php echo $settings->offcanvas_bar_width_unit; ?>, 0, 0);
}
.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container,
.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container,
.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container {
	transform: translate3d(0, <?php echo $settings->offcanvas_bar_width; ?><?php echo $settings->offcanvas_bar_width_unit; ?>, 0);
}
.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container,
.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container,
.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container {
	transform: translate3d(0, -<?php echo $settings->offcanvas_bar_width; ?><?php echo $settings->offcanvas_bar_width_unit; ?>, 0);
}
.pp-offcanvas-content.pp-offcanvas-content-<?php echo $id; ?> .pp-offcanvas-body {
	text-align: <?php echo $settings->content_align; ?>;
	color: <?php echo pp_get_color_value( $settings->content_text_color ); ?>;
	background: <?php echo pp_get_color_value( $settings->content_bg_color ); ?>;
}
.pp-offcanvas-content.pp-offcanvas-content-<?php echo $id; ?> .pp-offcanvas-body a {
	color: <?php echo pp_get_color_value( $settings->content_link_color ); ?>;
}
<?php
// Title Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'title_typography',
		'selector'     => ".pp-offcanvas-content.pp-offcanvas-content-$id .pp-offcanvas-body .pp-offcanvas-content-title",
	)
);
// Description Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'description_typography',
		'selector'     => ".pp-offcanvas-content.pp-offcanvas-content-$id .pp-offcanvas-body .pp-offcanvas-content-description",
	)
);
// Content Border - Settings
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_border',
		'selector'     => ".pp-offcanvas-content.pp-offcanvas-content-$id .pp-offcanvas-body",
	)
);
// Content Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_padding',
		'selector'     => ".pp-offcanvas-content.pp-offcanvas-content-$id .pp-offcanvas-body",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'content_padding_top',
			'padding-right'  => 'content_padding_right',
			'padding-bottom' => 'content_padding_bottom',
			'padding-left'   => 'content_padding_left',
		),
	)
);
?>
.pp-offcanvas-content-<?php echo $id; ?>.pp-offcanvas-content .pp-offcanvas-header {
	text-align: <?php echo $settings->close_button_align; ?>;
}
.pp-offcanvas-content-<?php echo $id; ?> .pp-offcanvas-header .pp-offcanvas-close span,
.pp-offcanvas-content-<?php echo $id; ?> .pp-offcanvas-header .pp-offcanvas-close span:before {
	color: <?php echo pp_get_color_value( $settings->close_button_color ); ?>;
	<?php if ( ! empty( $settings->close_button_size ) ) { ?>
	font-size: <?php echo ( $settings->close_button_size ); ?>px;
	<?php } ?>
}
.pp-offcanvas-content-<?php echo $id; ?> .pp-offcanvas-close .pp-offcanvas-close-icon {
	<?php if ( ! empty( $settings->close_button_size ) ) { ?>
	height: <?php echo ( $settings->close_button_size ); ?>px;
	width: <?php echo ( $settings->close_button_size ); ?>px;
	<?php } ?>
}
.pp-offcanvas-content-<?php echo $id; ?> .pp-offcanvas-close .pp-offcanvas-close-icon:before,
.pp-offcanvas-content-<?php echo $id; ?> .pp-offcanvas-close .pp-offcanvas-close-icon:after {
	background-color: <?php echo pp_get_color_value( $settings->close_button_color ); ?>;
}
<?php
// Toggle Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'close_button_padding',
		'selector'     => ".pp-offcanvas-content-$id.pp-offcanvas-content .pp-offcanvas-header",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'close_button_padding_top',
			'padding-right'  => 'close_button_padding_right',
			'padding-bottom' => 'close_button_padding_bottom',
			'padding-left'   => 'close_button_padding_left',
		),
	)
);
?>

.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap {
	text-align: <?php echo $settings->toggle_align; ?>;
}
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle {
	color: <?php echo pp_get_color_value( $settings->toggle_text_color ); ?>;
	background: <?php echo pp_get_color_value( $settings->toggle_bg_color ); ?>;
	transition: all 0.3s ease-in-out;
	<?php if ( 'button' == $settings->toggle_source && 'yes' == $settings->toggle_full_width ) { ?>
		width: 100%;
		display: block;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle .pp-hamburger-box,
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle .pp-hamburger-inner,
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle .pp-hamburger-inner::before,
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle .pp-hamburger-inner::after {
	width: <?php echo $settings->hamburger_size; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle .pp-hamburger-inner,
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle .pp-hamburger-inner::before,
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle .pp-hamburger-inner::after {
	background-color: <?php echo pp_get_color_value( $settings->hamburger_color ); ?>;
	transition: all 0.3s ease-in-out;
	height: <?php echo $settings->hamburger_thickness; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle:hover {
	color: <?php echo pp_get_color_value( $settings->toggle_color_hover ); ?>;
	background: <?php echo pp_get_color_value( $settings->toggle_bg_color_hover ); ?>;
	border-color: <?php echo pp_get_color_value( $settings->toggle_border_color_hover ); ?>;
}
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle:hover .pp-hamburger-inner,
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle:hover .pp-hamburger-inner::before,
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle:hover .pp-hamburger-inner::after {
	background-color: <?php echo pp_get_color_value( $settings->hamburger_color_hover ); ?>;
}

<?php
// Toggle Border - Settings
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'toggle_border',
		'selector'     => ".fl-node-$id .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle",
	)
);
// Toggle Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'toggle_padding',
		'selector'     => ".fl-node-$id .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'toggle_padding_top',
			'padding-right'  => 'toggle_padding_right',
			'padding-bottom' => 'toggle_padding_bottom',
			'padding-left'   => 'toggle_padding_left',
		),
	)
);
?>

.fl-node-<?php echo $id; ?> .pp-offcanvas-icon-after .pp-offcanvas-toggle-icon {
	margin-left: <?php echo $settings->toggle_text_space; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-offcanvas-icon-before .pp-offcanvas-toggle-icon {
	margin-right: <?php echo $settings->toggle_text_space; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-hamburger-after .pp-hamburger-box {
	order: 2;
	margin-left: <?php echo $settings->toggle_text_space; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-hamburger-before .pp-hamburger-box {
	margin-right: <?php echo $settings->toggle_text_space; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle-icon {
	color: <?php echo pp_get_color_value( $settings->button_icon_color ); ?>;
	font-size: <?php echo $settings->button_icon_size; ?>px;
	transition: all 0.3s ease-in-out;
}
.fl-node-<?php echo $id; ?> .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle:hover .pp-offcanvas-toggle-icon {
	color: <?php echo pp_get_color_value( $settings->button_icon_color_hover ); ?>;
}

<?php
// Toggle Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'toggle_typography',
		'selector'     => ".fl-node-$id .pp-offcanvas-toggle-wrap .pp-offcanvas-toggle .pp-toggle-label",
	)
);
?>

@media only screen and ( max-width: <?php echo $global_settings->medium_breakpoint; ?>px ) {
	.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container,
	.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container,
	.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container {
		transform: translate3d(<?php echo $settings->offcanvas_bar_width_medium; ?><?php echo $settings->offcanvas_bar_width_medium_unit; ?>, 0, 0);
	}
	.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container,
	.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container,
	.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container {
		transform: translate3d(-<?php echo $settings->offcanvas_bar_width_medium; ?><?php echo $settings->offcanvas_bar_width_medium_unit; ?>, 0, 0);
	}
	.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container,
	.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container,
	.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container {
		transform: translate3d(0, <?php echo $settings->offcanvas_bar_width_medium; ?><?php echo $settings->offcanvas_bar_width_medium_unit; ?>, 0);
	}
	.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container,
	.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container,
	.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container {
		transform: translate3d(0, -<?php echo $settings->offcanvas_bar_width_medium; ?><?php echo $settings->offcanvas_bar_width_medium_unit; ?>, 0);
	}
}

@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {
	.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container,
	.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container,
	.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-left .pp-offcanvas-container {
		transform: translate3d(<?php echo $settings->offcanvas_bar_width_responsive; ?><?php echo $settings->offcanvas_bar_width_responsive_unit; ?>, 0, 0);
	}
	.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container,
	.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container,
	.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-right .pp-offcanvas-container {
		transform: translate3d(-<?php echo $settings->offcanvas_bar_width_responsive; ?><?php echo $settings->offcanvas_bar_width_responsive_unit; ?>, 0, 0);
	}
	.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container,
	.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container,
	.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-top .pp-offcanvas-container {
		transform: translate3d(0, <?php echo $settings->offcanvas_bar_width_responsive; ?><?php echo $settings->offcanvas_bar_width_responsive_unit; ?>, 0);
	}
	.pp-offcanvas-content-reveal.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container,
	.pp-offcanvas-content-push.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container,
	.pp-offcanvas-content-slide-along.pp-offcanvas-content-<?php echo $id; ?>-open.pp-offcanvas-content-bottom .pp-offcanvas-container {
		transform: translate3d(0, -<?php echo $settings->offcanvas_bar_width_responsive; ?><?php echo $settings->offcanvas_bar_width_responsive_unit; ?>, 0);
	}
	/* Animated Headlines fix */
	.pp-offcanvas-content-<?php echo $id; ?>-open .pp-offcanvas-container .pp-headline-dynamic-wrapper {
		display: none;
	}
}
