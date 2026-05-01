/* Container */
<?php
FLBuilderCSS::responsive_rule( array(
	'settings' => $settings,
	'setting_name' => 'offcanvas_width',
	'selector' => ".fl-node-$id .pp-advanced-menu .pp-off-canvas-menu",
	'prop' => 'width',
	'unit' => $settings->offcanvas_width_unit
) );
?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu {
	<?php $responsive_overlay_bg_opacity = '' !== $settings->responsive_overlay_bg_opacity ? ($settings->responsive_overlay_bg_opacity / 100) : 1; ?>
	background-color: <?php echo pp_hex2rgba( pp_get_color_value( $settings->responsive_overlay_bg_color ), $responsive_overlay_bg_opacity ); ?>;
	<?php
	    if ( $settings->enable_shadow == 'yes' ) {
	        $menu_shadow_h = empty($settings->menu_shadow['horizontal']) ? '0' : $settings->menu_shadow['horizontal'];
	        $menu_shadow_v = empty($settings->menu_shadow['vertical']) ? '0' : $settings->menu_shadow['vertical'];
	        $menu_shadow_b = empty($settings->menu_shadow['blur']) ? '0' : $settings->menu_shadow['blur'];
	        $menu_shadow_s = empty($settings->menu_shadow['spread']) ? '0' : $settings->menu_shadow['spread'];
	        ?>
			-webkit-box-shadow: <?php echo $menu_shadow_h; ?>px <?php echo $menu_shadow_v; ?>px <?php echo $menu_shadow_b; ?>px <?php echo $menu_shadow_s; ?>px <?php echo pp_hex2rgba( pp_get_color_value( $settings->menu_shadow_color ), $settings->menu_shadow_opacity / 100 ); ?>;
			-moz-box-shadow: <?php echo $menu_shadow_h; ?>px <?php echo $menu_shadow_v; ?>px <?php echo $menu_shadow_b; ?>px <?php echo $menu_shadow_s; ?>px <?php echo pp_hex2rgba( pp_get_color_value( $settings->menu_shadow_color ), $settings->menu_shadow_opacity / 100 ); ?>;
			box-shadow: <?php echo $menu_shadow_h; ?>px <?php echo $menu_shadow_v; ?>px <?php echo $menu_shadow_b; ?>px <?php echo $menu_shadow_s; ?>px <?php echo pp_hex2rgba( pp_get_color_value( $settings->menu_shadow_color ), $settings->menu_shadow_opacity / 100 ); ?>;
	        <?php
	    }
	?>
}

<?php if ( isset( $settings->offcanvas_width ) && ! empty( $settings->offcanvas_width ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-left {
    transform: translate3d(-<?php echo $settings->offcanvas_width + 30; ?><?php echo $settings->offcanvas_width_unit; ?>, 0, 0);
}
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-right {
    transform: translate3d(<?php echo $settings->offcanvas_width + 30; ?><?php echo $settings->offcanvas_width_unit; ?>, 0, 0);
}
<?php } ?>

/* Close Button */
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu .pp-menu-close-btn {
	font-size: <?php echo ( $settings->close_icon_size ) ? $settings->close_icon_size : '30'; ?>px;
	<?php if( $settings->close_icon_color ) { ?>color: <?php echo pp_get_color_value( $settings->close_icon_color ); ?>;<?php } ?>
}

/* Menu */
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu {
	<?php if ( $settings->responsive_alignment_vertical == 'center' ) { ?>
		position: absolute;
	    left: 0;
	    top: 50%;
		-webkit-transform: translate(0, -50%);
	    -moz-transform: translate(0, -50%);
	    -ms-transform: translate(0, -50%);
	    transform: translate(0, -50%);
		width: 100%;
		margin-top: 0;
	<?php } ?>
}
<?php
// Responsive Overlay Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'responsive_overlay_padding',
	'selector' 		=> ".fl-node-$id .pp-advanced-menu.off-canvas .menu",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'responsive_overlay_padding_top',
		'padding-right' 	=> 'responsive_overlay_padding_right',
		'padding-bottom' 	=> 'responsive_overlay_padding_bottom',
		'padding-left' 		=> 'responsive_overlay_padding_left',
	),
) );
?>

/* Sub Menu */
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu {
	box-shadow: none;
	border: none;
}
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu > li.current-menu-parent.pp-has-submenu .sub-menu {
	display: block;
}

/* Links */
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu > li,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li {
	display: block;
}

<?php if( false && $settings->alignment != 'right' ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
	padding-right: 0;
}
<?php } ?>

/*
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu > li > .pp-has-submenu-container > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a:focus {
	background-color: transparent;
}
*/

<?php if ( ! empty( $settings->submenu_background_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a {
	background-color: <?php echo pp_get_color_value( $settings->submenu_background_color ); ?>;
}
<?php } ?>
<?php if ( ! empty( $settings->submenu_background_hover_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li.current-menu-item > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li.current-menu-item > .pp-has-submenu-container > a {
	background-color: <?php echo pp_get_color_value( $settings->submenu_background_hover_color ); ?>;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a {
	<?php if( $settings->responsive_link_bg_color ) { ?>background-color: <?php echo pp_get_color_value( $settings->responsive_link_bg_color ); ?>;<?php } ?>
	<?php if( $settings->responsive_link_color ) { ?>color: <?php echo pp_get_color_value( $settings->responsive_link_color ); ?>;<?php } ?>
	border-style: solid;
	border-bottom-color: <?php echo ($settings->responsive_link_border_color) ? pp_get_color_value( $settings->responsive_link_border_color ) : 'transparent'; ?>;
	-webkit-transition: all 0.3s ease-in-out;
	-moz-transition: all 0.3s ease-in-out;
	transition: all 0.3s ease-in-out;
}
<?php
// Responsive Link Border Width
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'responsive_link_border_width',
	'selector' 		=> ".fl-node-$id .pp-advanced-menu.off-canvas .menu li a, .fl-node-$id .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a",
	'unit'			=> 'px',
	'props'			=> array(
		'border-top-width' 		=> 'responsive_link_border_width_top',
		'border-right-width' 	=> 'responsive_link_border_width_right',
		'border-bottom-width' 	=> 'responsive_link_border_width_bottom',
		'border-left-width' 	=> 'responsive_link_border_width_left',
	),
) );

// Responsive Link Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'responsive_link_padding',
	'selector' 		=> ".fl-node-$id .pp-advanced-menu.off-canvas .menu li a, .fl-node-$id .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'responsive_link_padding_top',
		'padding-right' 	=> 'responsive_link_padding_right',
		'padding-bottom' 	=> 'responsive_link_padding_bottom',
		'padding-left' 		=> 'responsive_link_padding_left',
	),
) );
?>
<?php
// Submenu Link Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'submenu_link_padding',
	'selector' 		=> ".fl-node-$id .pp-advanced-menu.off-canvas .sub-menu > li > a, .fl-node-$id .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'submenu_link_padding_top',
		'padding-right' 	=> 'submenu_link_padding_right',
		'padding-bottom' 	=> 'submenu_link_padding_bottom',
		'padding-left' 		=> 'submenu_link_padding_left',
	),
) );
?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li.current-menu-item > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li.current-menu-parent .pp-has-submenu-container li.current-menu-item > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li.pp-active > .pp-has-submenu-container > a {
	<?php if ( $settings->responsive_link_bg_hover_color ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->responsive_link_bg_hover_color ); ?>;
	<?php } ?>
	<?php if ( $settings->responsive_link_hover_color ) { ?>
		color: <?php echo pp_get_color_value( $settings->responsive_link_hover_color ); ?>;
	<?php } ?>
}

<?php if ( '' != $settings->animation_speed ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-left,
	.fl-node-<?php echo $id; ?> .menu-open.pp-advanced-menu .pp-off-canvas-menu.pp-menu-left,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-right,
	.fl-node-<?php echo $id; ?> .menu-open.pp-advanced-menu .pp-off-canvas-menu.pp-menu-right {
		transition-duration: <?php echo ( $settings->animation_speed / 1000 ) . 's'; ?>;
	}
<?php } ?>

<?php if( !empty( $settings->responsive_link_color ) ) { ?>
		<?php if( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .sub-menu .pp-menu-toggle:before {
			border-color: <?php echo pp_get_color_value( $settings->responsive_link_color ); ?>;
		}
		<?php } elseif( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu .pp-menu-toggle:after {
			border-color: <?php echo pp_get_color_value( $settings->responsive_link_color ); ?>;
		}
		<?php } ?>
<?php } ?>

<?php if ( ! empty( $settings->responsive_link_hover_color ) ) { ?>

		<?php if( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows li a:focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none li a:focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .sub-menu li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .sub-menu li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu li a:focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .sub-menu li a:focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container.focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows li.pp-active > .pp-has-submenu-container .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li a:focus .pp-menu-toggle:before {
			border-color: <?php echo pp_get_color_value( $settings->responsive_link_hover_color ); ?>;
		}
		<?php } elseif( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li a:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li a:focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li a:focus .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li a:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li a:focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li a:focus .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container.focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container.focus .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li.pp-active > .pp-has-submenu-container .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li.pp-active > .pp-has-submenu-container .pp-menu-toggle:after {
			border-color: <?php echo pp_get_color_value( $settings->responsive_link_hover_color ); ?>;
		}
		<?php } ?>
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle {
    width: <?php echo $toggle_width ?>px;
    height: <?php echo $toggle_height ?>px;
}

<?php if( $settings->responsive_alignment == 'right' || ( isset( $settings->alignment_responsive ) && 'right' == $settings->alignment_responsive ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
		padding-right: 0;
		padding-left: 28px;
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-menu-toggle {
		right: 0;
		left: 10px;
	}
<?php } ?>

<?php if ( 'expanded' !== $settings->mobile_toggle && 'off-canvas' === $settings->mobile_menu_type ) { ?>
	<?php if ( 'always' !== $module->get_media_breakpoint() ) { ?>
		@media ( max-width: <?php echo $module->get_media_breakpoint() ?>px ) {
	<?php } ?>
			.fl-node-<?php echo $id; ?> .pp-advanced-menu.pp-menu-default {
				display: none;
			}
	<?php if ( 'always' != $module->get_media_breakpoint() ) { ?>
		}
	<?php } ?>
<?php } ?>

@media ( max-width: 1024px ) {
	html.pp-off-canvas-menu-open {
		overflow: hidden !important;
	}
}

@media only screen and (max-width: <?php echo $global_settings->large_breakpoint; ?>px) {
	<?php if ( isset( $settings->offcanvas_width_large ) && ! empty( $settings->offcanvas_width_large ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-left {
			transform: translate3d(-<?php echo $settings->offcanvas_width_large + 30; ?><?php echo $settings->offcanvas_width_unit; ?>, 0, 0);
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-right {
			transform: translate3d(<?php echo $settings->offcanvas_width_large + 30; ?><?php echo $settings->offcanvas_width_unit; ?>, 0, 0);
		}
	<?php } ?>

	<?php if ( isset( $settings->alignment_large ) && 'right' == $settings->alignment_large ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-right: 0;
			padding-left: 28px;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle {
			right: 0;
			left: 28px;
		}
	<?php } ?>
	<?php if ( isset( $settings->alignment_large ) && 'left' == $settings->alignment_large ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-left: 0;
			padding-right: 28px;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle {
			left: auto;
			right: 0;
		}
	<?php } ?>
}

@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
	<?php if ( isset( $settings->offcanvas_width_medium ) && ! empty( $settings->offcanvas_width_medium ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-left {
			transform: translate3d(-<?php echo $settings->offcanvas_width_medium + 30; ?><?php echo $settings->offcanvas_width_unit; ?>, 0, 0);
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-right {
			transform: translate3d(<?php echo $settings->offcanvas_width_medium + 30; ?><?php echo $settings->offcanvas_width_unit; ?>, 0, 0);
		}
	<?php } ?>

	<?php if( ( isset( $settings->alignment_medium ) && 'right' == $settings->alignment_medium ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-right: 0;
			padding-left: 28px;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle {
			right: 0;
			left: 28px;
		}
	<?php } ?>
	<?php if ( isset( $settings->alignment_medium ) && 'left' == $settings->alignment_medium ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-left: 0;
			padding-right: 28px;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle {
			left: auto;
			right: 0;
		}
	<?php } ?>
}

@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	<?php if ( isset( $settings->offcanvas_width_responsive ) && ! empty( $settings->offcanvas_width_responsive ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-left {
			transform: translate3d(-<?php echo $settings->offcanvas_width_responsive + 30; ?><?php echo $settings->offcanvas_width_unit; ?>, 0, 0);
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-right {
			transform: translate3d(<?php echo $settings->offcanvas_width_responsive + 30; ?><?php echo $settings->offcanvas_width_unit; ?>, 0, 0);
		}
	<?php } ?>

	<?php if( ( isset( $settings->alignment_responsive ) && 'right' == $settings->alignment_responsive ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-right: 0;
			padding-left: 28px;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle {
			right: 0;
			left: 28px;
		}
	<?php } ?>
	<?php if ( isset( $settings->alignment_responsive ) && 'left' == $settings->alignment_responsive ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-left: 0;
			padding-right: 28px;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle {
			left: auto;
			right: 0;
		}
	<?php } ?>
}