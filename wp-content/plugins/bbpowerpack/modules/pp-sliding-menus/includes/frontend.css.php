.fl-node-<?php echo $id; ?> {
	text-align: <?php echo $settings->alignment; ?>;
}

<?php
// Slide Menu - Border
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'border',
		'selector'     => ".fl-node-$id .pp-sliding-menus",
	)
);

// Slide Menu - Transition Duration
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'duration',
		'selector'     => ".fl-node-$id .pp-sliding-menus .pp-slide-menu__menu, .fl-node-$id .pp-sliding-menus .pp-slide-menu-sub-menu",
		'prop'         => 'transition-duration',
		'unit'         => 's',
	)
);
?>

.fl-node-<?php echo $id; ?> .pp-sliding-menus {
	<?php if ( isset( $settings->bg_color ) && ! empty( $settings->bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->bg_color ); ?>;
	<?php } ?>
}

<?php

// Slide Menu - Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'menu_typography',
		'selector'     => ".fl-node-$id .pp-sliding-menus",
	)
);

// Slide Menu - Width
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'width',
		'selector'     => ".fl-node-$id .pp-sliding-menus",
		'prop'         => 'width',
		'unit'         => $settings->width_unit,
	)
);

// Slide Menu - Min Height
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'min_height',
		'selector'     => ".fl-node-$id .pp-sliding-menus",
		'prop'         => 'min-height',
		'unit'         => 'px',
	)
);
?>

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-sub-menu {
	<?php if ( isset( $settings->bg_color ) && ! empty( $settings->bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->bg_color ); ?>;
	<?php } ?>
}

<?php

// Slide Menu - Links Spacing
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'links_spacing',
		'selector'     => ".fl-node-$id .pp-sliding-menus .pp-slide-menu-item:not(:last-child)",
		'prop'         => 'margin-bottom',
		'unit'         => 'px',
	)
);
?>

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-item {
	<?php if ( isset( $settings->links_separator_thickness ) && '' != $settings->links_separator_thickness ) { ?>
		border-bottom-width: <?php echo $settings->links_separator_thickness; ?>px;
		border-bottom-style: solid;
	<?php } ?>
	<?php if ( isset( $settings->links_separator_color ) && ! empty( $settings->links_separator_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->links_separator_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-item:hover {
	<?php if ( isset( $settings->links_separator_color_hover ) && ! empty( $settings->links_separator_color_hover ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->links_separator_color_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-item-link,
.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-arrow {
	transition-property: all;
	transition-duration: <?php echo $settings->links_transition_duration; ?>s;
	transition-timing-function: <?php echo $settings->links_transition_easing; ?>;
}

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-item-link {
	text-align: <?php echo $settings->links_alignment; ?>;
	<?php if ( isset( $settings->links_bg_color ) && ! empty( $settings->links_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->links_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->links_color ) && ! empty( $settings->links_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->links_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-item-link:hover {
	<?php if ( isset( $settings->links_bg_color_hover ) && ! empty( $settings->links_bg_color_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->links_bg_color_hover ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->links_color_hover ) && ! empty( $settings->links_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->links_color_hover ); ?>;
	<?php } ?>
}

<?php

// Slide Menu - Links Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'links_padding',
		'selector'     => ".fl-node-$id .pp-sliding-menus .pp-slide-menu-item-link",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'links_padding_top',
			'padding-right'  => 'links_padding_right',
			'padding-bottom' => 'links_padding_bottom',
			'padding-left'   => 'links_padding_left',
		),
	)
);

// Slide Menu - Arrow Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'links_padding',
		'selector'     => ".fl-node-$id .pp-sliding-menus .pp-slide-menu-arrow",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'links_padding_top',
			'padding-bottom' => 'links_padding_bottom',
		),
	)
);

?>

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-arrow {
	<?php if ( isset( $settings->arrows_bg_color ) && ! empty( $settings->arrows_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->arrows_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->arrows_color ) && ! empty( $settings->arrows_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->arrows_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->arrows_separator_color ) && ! empty( $settings->arrows_separator_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->arrows_separator_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-arrow:hover {
	<?php if ( isset( $settings->arrows_bg_color_hover ) && ! empty( $settings->arrows_bg_color_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->arrows_bg_color_hover ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->arrows_color_hover ) && ! empty( $settings->arrows_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->arrows_color_hover ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->arrows_separator_color_hover ) && ! empty( $settings->arrows_separator_color_hover ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->arrows_separator_color_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-item.pp-slide-menu-item-has-children > .pp-slide-menu-arrow {
	<?php if ( isset( $settings->arrow_separator_thickness ) && '' != $settings->arrow_separator_thickness ) { ?>
		border-left-width: <?php echo $settings->arrow_separator_thickness; ?>px;
		border-left-style: solid;
	<?php } ?>
	<?php if ( isset( $settings->arrows_separator_color ) && ! empty( $settings->arrows_separator_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->arrows_separator_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-sliding-menus .pp-slide-menu-back > .pp-slide-menu-arrow {
	<?php if ( isset( $settings->arrow_separator_thickness ) && '' != $settings->arrow_separator_thickness ) { ?>
		border-right-width: <?php echo $settings->arrow_separator_thickness; ?>px;
		border-right-style: solid;
	<?php } ?>
	<?php if ( isset( $settings->arrows_separator_color ) && ! empty( $settings->arrows_separator_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->arrows_separator_color ); ?>;
	<?php } ?>
}

<?php

// Slide Menu - Arrows Size
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'arrow_size',
		'selector'     => ".fl-node-$id .pp-sliding-menus .pp-slide-menu-arrow svg",
		'prop'         => 'width',
		'unit'         => 'px',
	)
);

// Slide Menu - Arrows Size
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'arrow_size',
		'selector'     => ".fl-node-$id .pp-sliding-menus .pp-slide-menu-arrow svg",
		'prop'         => 'height',
		'unit'         => 'px',
	)
);

// Slide Menu - Arrows Left Padding
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'arrow_left_padding',
		'selector'     => ".fl-node-$id .pp-sliding-menus .pp-slide-menu-arrow",
		'prop'         => 'padding-left',
		'unit'         => 'px',
	)
);

// Slide Menu - Arrows Right Padding
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'arrow_right_padding',
		'selector'     => ".fl-node-$id .pp-sliding-menus .pp-slide-menu-arrow",
		'prop'         => 'padding-right',
		'unit'         => 'px',
	)
);
?>
