<?php
// ---------------------------------------
// Border Rules.
// ---------------------------------------
// Container Box Border.
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'box_border',
		'selector'     => ".fl-node-$id .pp-toc-container",
	)
);

// Scroll to Top Border.
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'scroll_border',
		'selector'     => ".fl-node-$id .pp-toc-scroll-top-container",
	)
);

// ---------------------------------------
// Typography Rules.
// ---------------------------------------
// Header Typography.
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'header_text_typo',
		'selector'     => ".fl-node-$id .pp-toc-container .pp-toc-header-title",
	)
);

// List Typography.
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'list_typo',
		'selector'     => ".fl-node-$id .pp-toc-container .pp-toc-list-wrapper a",
	)
);

// ---------------------------------------
// Padding Rules.
// ---------------------------------------
// Header Padding.
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'header_padding',
		'selector'     => ".fl-node-$id .pp-toc-container .pp-toc-header",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'header_padding_top',
			'padding-right'  => 'header_padding_right',
			'padding-bottom' => 'header_padding_bottom',
			'padding-left'   => 'header_padding_left',
		),
	)
);

// Body Padding.
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'list_padding',
		'selector'     => ".fl-node-$id .pp-toc-container .pp-toc-body",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'list_padding_top',
			'padding-right'  => 'list_padding_right',
			'padding-bottom' => 'list_padding_bottom',
			'padding-left'   => 'list_padding_left',
		),
	)
);

// ---------------------------------------
// Responsive Rules.
// ---------------------------------------

// Container Min Height.
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'box_min_height',
		'selector'     => ".fl-node-$id .pp-toc-container",
		'prop'         => 'min-height',
	)
);

// Separator Width.
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'header_separator_width',
		'selector'     => ".fl-node-$id .pp-toc-container .pp-toc-separator",
		'prop'         => 'height',
	)
);

// Marker Size.
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'list_marker_size',
		'selector'     => ".fl-node-$id .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-bullet li::before, .fl-node-$id .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-number li::before, .fl-node-$id .pp-toc-container .pp-toc-list-icon span",
		'prop'         => 'font-size',
	)
);

// ------------------Sticky ToC-------------------------

// Horizontal Position (Available on Custom position).
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'horizontal_position',
		'selector'     => ".fl-node-$id .pp-toc-sticky-custom",
		'prop'         => 'left',
	)
);

// Vertical Position (Available on Custom position).
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'vertical_position',
		'selector'     => ".fl-node-$id .pp-toc-sticky-custom",
		'prop'         => 'bottom',
	)
);

// Offset (Available on Fixed position).
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'fixed_offset_position',
		'selector'     => ".fl-node-$id .pp-toc-sticky-fixed",
		'prop'         => 'top',
	)
);

// -----------------------Scroll to Top----------------------

// Horizontal Position of Scroll to top button.
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'scroll_horizontal_position',
		'selector'     => ".fl-node-$id .pp-toc-scroll-align-left",
		'prop'         => 'left',
	)
);

FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'scroll_horizontal_position',
		'selector'     => ".fl-node-$id .pp-toc-scroll-align-right",
		'prop'         => 'right',
	)
);

// Vertical Position of Scroll to top button.
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'scroll_vertical_position',
		'selector'     => ".fl-node-$id .pp-toc-scroll-top-container",
		'prop'         => 'bottom',
	)
);

// ----------Scroll Styling Section--------------

// Scroll to top Icon Size.
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'scroll_icon_size',
		'selector'     => ".fl-node-$id .pp-toc-scroll-top-icon",
		'prop'         => 'font-size',
	)
);
?>
.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-body {
	<?php if ( isset( $settings->box_bg_color ) && ! empty( $settings->box_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->box_bg_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-header-title {
	<?php if ( isset( $settings->header_alignment ) && ! empty( $settings->header_alignment ) ) { ?>
	text-align: <?php echo $settings->header_alignment; ?>;
	<?php } ?>
	<?php if ( isset( $settings->header_text_color ) && ! empty( $settings->header_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->header_text_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-header {
	<?php if ( isset( $settings->header_bg_color ) && ! empty( $settings->header_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->header_bg_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-container .header-icon-collapse,
.fl-node-<?php echo $id; ?> .pp-toc-container .header-icon-expand {
	<?php if ( isset( $settings->header_icon_color ) && ! empty( $settings->header_icon_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->header_icon_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-separator {
	<?php if ( isset( $settings->header_separator_color ) && ! empty( $settings->header_separator_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->header_separator_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper li {
	<?php if ( isset( $settings->list_spacing ) && '' !== $settings->list_spacing ) { ?>
	margin-top: <?php echo $settings->list_spacing; ?>px;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper > li:first-of-type {
	margin-top: 0;
}

.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-bullet li:before,
.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-number li:before,
.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-icon li span {
	<?php if ( isset( $settings->list_marker_color ) && ! empty( $settings->list_marker_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->list_marker_color ); ?>;
	<?php } ?>

	<?php if ( isset( $settings->list_marker_space ) && '' !== $settings->list_marker_space ) { ?>
	margin-right: <?php echo $settings->list_marker_space; ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper.pp-toc-list-number li:before {
	<?php if ( isset( $settings->list_marker_space ) && '' !== $settings->list_marker_space ) { ?>
	padding-right: <?php echo $settings->list_marker_space; ?>px;
	margin-right: 0;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper li a {
	<?php if ( isset( $settings->list_normal_color ) && ! empty( $settings->list_normal_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->list_normal_color ); ?>;
	<?php } ?>
	word-wrap: normal;
	transition: 0.3s;
}

.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper li a:hover,
.fl-node-<?php echo $id; ?> .pp-toc-container .pp-toc-list-wrapper li a:active {
	<?php if ( isset( $settings->list_hover_color ) && ! empty( $settings->list_hover_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->list_hover_color ); ?>;
	<?php } ?>

	<?php if ( 'yes' === $settings->list_hover_underline ) { ?>
		text-decoration: underline;
	<?php } else { ?>
		text-decoration: none;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-sticky-fixed,
.fl-node-<?php echo $id; ?> .pp-toc-sticky-custom {
	<?php if ( isset( $settings->sticky_toc_opacity ) && '' !== $settings->sticky_toc_opacity ) { ?>
	opacity: <?php echo $settings->sticky_toc_opacity; ?>;
	<?php } ?>
	<?php if ( isset( $settings->sticky_toc_shadow ) && ! empty( FLBuilderColor::shadow( $settings->sticky_toc_shadow ) ) ) { ?>
	box-shadow: <?php echo FLBuilderColor::shadow( $settings->sticky_toc_shadow ); ?>
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-sticky-fixed,
.fl-node-<?php echo $id; ?> .pp-toc-sticky-custom {
	<?php if ( ! empty( $settings->sticky_toc_width ) ) { ?>
	width: <?php echo $settings->sticky_toc_width; ?><?php echo $settings->sticky_toc_width_unit; ?> !important;
	<?php } ?>
}

.admin-bar .fl-node-<?php echo $id; ?> .pp-toc-sticky-fixed {
	<?php if ( '' !== ( $settings->fixed_offset_position ) ) { ?>
	top: calc( 
		<?php
		echo $settings->fixed_offset_position;
		echo $settings->fixed_offset_position_unit;
		?>
	 + 32px );
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-toc-scroll-top-container {
	<?php if ( isset( $settings->scroll_bg_color ) && ! empty( $settings->scroll_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->scroll_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->scroll_icon_color ) && ! empty( $settings->scroll_icon_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->scroll_icon_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->scroll_top_padding ) && '' !== $settings->scroll_top_padding ) { ?>
	padding: <?php echo $settings->scroll_top_padding; ?>px;
	<?php } ?>
	<?php if ( isset( $settings->scroll_z_index ) && '' !== $settings->scroll_z_index ) { ?>
	z-index: <?php echo $settings->scroll_z_index; ?>;
	<?php } ?>
}

@media only screen and ( max-width: <?php echo $global_settings->large_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-toc-sticky-fixed,
	.fl-node-<?php echo $id; ?> .pp-toc-sticky-custom {
		<?php if ( isset( $settings->sticky_toc_width_large ) && ! empty( $settings->sticky_toc_width_large ) ) { ?>
		width: <?php echo $settings->sticky_toc_width_large; ?><?php echo $settings->sticky_toc_width_large_unit; ?> !important;
		<?php } ?>
	}
}

@media only screen and ( max-width: <?php echo $global_settings->medium_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-toc-sticky-fixed,
	.fl-node-<?php echo $id; ?> .pp-toc-sticky-custom {
		<?php if ( ! empty( $settings->sticky_toc_width_medium ) ) { ?>
		width: <?php echo $settings->sticky_toc_width_medium; ?><?php echo $settings->sticky_toc_width_medium_unit; ?> !important;
		<?php } ?>
	}
}

@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-toc-sticky-fixed,
	.fl-node-<?php echo $id; ?> .pp-toc-sticky-custom {
		<?php if ( ! empty( $settings->sticky_toc_width_responsive ) ) { ?>
		width: <?php echo $settings->sticky_toc_width_responsive; ?><?php echo $settings->sticky_toc_width_responsive_unit; ?> !important;
		<?php } ?>
	}
}
