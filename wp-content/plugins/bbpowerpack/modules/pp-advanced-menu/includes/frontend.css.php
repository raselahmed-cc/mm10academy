<?php

$toggle_width 	= 28;
$toggle_height 	= 28;
$toggle_spacing = $settings->menu_link_padding_right > 10 ? $settings->menu_link_padding_right : 10;
$toggle_padding = ! empty( $settings->menu_link_padding_right ) ? $settings->menu_link_padding_right : 0;
$toggle_width   = ( $toggle_padding + 14 );
$toggle_height  = ceil( ( ( $toggle_padding * 2 ) + 14 ) * 0.65 );
?>

/**
 * Overall menu alignment
 */
<?php
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'alignment',
	'prop'         => 'text-align',
	'selector'     => ".fl-node-$id .pp-advanced-menu"
) );
?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal {
	<?php if ( 'left' === $settings->alignment ) { ?>
		justify-content: flex-start;
	<?php } ?>
	<?php if ( 'center' === $settings->alignment ) { ?>
		justify-content: center;
	<?php } ?>
	<?php if ( 'right' === $settings->alignment ) { ?>
		justify-content: flex-end;
	<?php } ?>
}

/**
 * Overall menu styling
 */

.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li {
	<?php if ( isset( $settings->spacing ) && '' !== $settings->spacing ) { ?>
	<?php if( $settings->alignment == 'left' ) { ?>
		margin-right: <?php echo ( $settings->spacing ); ?>px;
    <?php } elseif( $settings->alignment == 'right' ) { ?>
		margin-left: <?php echo ( $settings->spacing ); ?>px;
    <?php } else { ?>
		margin-left: <?php echo ( $settings->spacing / 2 ); ?>px;
		margin-right: <?php echo ( $settings->spacing / 2 ); ?>px;
	<?php } ?>
	<?php } ?>
}
<?php
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'link_bottom_spacing',
	'prop'         => 'margin-bottom',
	'unit'         => 'px',
	'selector'     => ".fl-node-$id .pp-advanced-menu .menu > li"
) );
?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu-container a > span {
	<?php if ( $settings->submenu_hover_toggle !== 'none' && 'center' !== $settings->alignment ) { ?>
		padding-right: 38px;
	<?php } else { ?>
		padding-right: 0;
	<?php } ?>
}

.fl-node-<?php echo $id; ?>-clone {
    display: none;
}

/*
@media (min-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle + .pp-clear + .pp-menu-nav ul.menu {
    	display: block;
	}
}
*/

/**
 * Links
 */
<?php
// Link typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'link_typography',
	'selector'		=> ".fl-node-$id .pp-advanced-menu .menu a"
) );
?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li > .pp-has-submenu-container > a {
	<?php if ( ! empty( $settings->background_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->background_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->link_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->link_color ); ?>;
	<?php } ?>
}

<?php
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id .pp-advanced-menu .menu > li > a, .fl-node-$id .pp-advanced-menu .menu > li > .pp-has-submenu-container > a"
) );
?>

<?php if ( ! empty( $settings->border_hover_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li > .pp-has-submenu-container > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li > .pp-has-submenu-container > a:focus {
	border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;
}
<?php } ?>
<?php
// Link Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'menu_link_padding',
	'selector' 		=> ".fl-node-$id .pp-advanced-menu .menu > li > a, .fl-node-$id .pp-advanced-menu .menu > li > .pp-has-submenu-container > a",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'menu_link_padding_top',
		'padding-right' 	=> 'menu_link_padding_right',
		'padding-bottom' 	=> 'menu_link_padding_bottom',
		'padding-left' 		=> 'menu_link_padding_left',
	),
) );
?>

<?php if ( ! empty( $settings->link_color ) ) { ?>
	<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none .pp-menu-toggle:before {
		border-color: <?php echo pp_get_color_value( $settings->link_color ); ?>;
	}
	<?php } elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-menu-toggle:after {
		border-color: <?php echo pp_get_color_value( $settings->link_color ); ?>;
	}
	<?php } ?>
<?php } ?>

<?php if ( ! empty( $settings->link_hover_color ) ) { ?>
	<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows li:hover .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none li:hover .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows li.focus .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none li.focus .pp-menu-toggle:before {
		border-color: <?php echo pp_get_color_value( $settings->link_hover_color ); ?>;
	}
	<?php } elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li:hover .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li:hover .pp-menu-toggle:after,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li.focus .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li.focus .pp-menu-toggle:after {
		border-color: <?php echo pp_get_color_value( $settings->link_hover_color ); ?>;
	}
	<?php } ?>
	<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows li a:hover .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none li a:hover .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows li a:focus .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none li a:focus .pp-menu-toggle:before {
		border-color: <?php echo pp_get_color_value( $settings->link_hover_color ); ?>;
	}
	<?php } elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li a:hover .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li a:hover .pp-menu-toggle:after,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li a:focus .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li a:focus .pp-menu-toggle:after {
		border-color: <?php echo pp_get_color_value( $settings->link_hover_color ); ?>;
	}
	<?php } ?>
<?php }

/**
 * Links - hover / active
 */
if ( ! empty( $settings->background_hover_color ) || $settings->link_hover_color ) { ?>
	.fl-node-<?php echo $id; ?> .menu > li > a:hover,
	.fl-node-<?php echo $id; ?> .menu > li > a:focus,
	.fl-node-<?php echo $id; ?> .menu > li:hover > .pp-has-submenu-container > a,
	.fl-node-<?php echo $id; ?> .menu > li.focus > .pp-has-submenu-container > a,
	.fl-node-<?php echo $id; ?> .menu > li.current-menu-item > a,
	.fl-node-<?php echo $id; ?> .menu > li.current-menu-item > .pp-has-submenu-container > a {
		<?php if( !empty( $settings->background_hover_color ) ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->background_hover_color ); ?>;
		<?php }
			if( !empty( $settings->link_hover_color ) ) {
				?>
				color: <?php echo pp_get_color_value( $settings->link_hover_color ); ?>;
				<?php
			}
		?>
	}
<?php } ?>

<?php if ( ! empty( $settings->link_hover_color ) ) { ?>
	<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows .pp-has-submenu-container:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows .pp-has-submenu-container.focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows li.current-menu-item > .pp-has-submenu-container .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none .pp-has-submenu-container:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none .pp-has-submenu-container.focus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none li.current-menu-item > .pp-has-submenu-container .pp-menu-toggle:before {
			border-color: <?php echo pp_get_color_value( $settings->link_hover_color ) ?>;
		}
		<?php } elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-has-submenu-container:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-has-submenu-container.focus .pp-menu-toggle:before,
        .fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-has-submenu-container a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-has-submenu-container.focus a .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li.current-menu-item > .pp-has-submenu-container .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-has-submenu-container:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-has-submenu-container.focus .pp-menu-toggle:after,
        .fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-has-submenu-container a:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .pp-has-submenu-container.focus a .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus li.current-menu-item > .pp-has-submenu-container .pp-menu-toggle:after {
			border-color: <?php echo pp_get_color_value( $settings->link_hover_color ) ?>;
		}
	<?php } ?>
<?php } ?>

/**
 * Sub Menu
 **/
<?php // Submenu - horizontal or vertical ?>
<?php if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) { ?>
	.fl-node-<?php echo $id; ?> .menu .pp-has-submenu .sub-menu {
		display: none;
	}
<?php } ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu {
	<?php if ( ! empty( $settings->submenu_container_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->submenu_container_bg_color ); ?>;
	<?php } ?>
}
@media (min-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu {
	<?php if ( $settings->submenu_width ) { ?>
		<?php if ( isset( $settings->submenu_width_as_min ) && 'yes' === $settings->submenu_width_as_min ) { ?>
			min-width: <?php echo $settings->submenu_width; ?>px;
			width: auto;
		<?php } else { ?>
			width: <?php echo $settings->submenu_width; ?>px;
		<?php } ?>
	<?php } ?>
	}
}
<?php
// Submenu Border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'submenu_container_border',
	'selector' 		=> ".fl-node-$id .pp-advanced-menu .sub-menu",
) );

// Sbumenu typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'submenu_typography',
	'selector'		=> ".fl-node-$id .pp-advanced-menu .menu .sub-menu a"
) );
?>

<?php if ( ! empty( $settings->submenu_container_bg_color ) ) { ?>
.fl-node-<?php echo $id; ?> ul.pp-advanced-menu-horizontal li.mega-menu > ul.sub-menu {
	background: <?php echo pp_get_color_value( $settings->submenu_container_bg_color ); ?>;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a {
	border-width: 0;
	border-style: <?php echo $settings->submenu_border_style; ?>;
	border-bottom-width: <?php echo ( $settings->submenu_border_size != '' && $settings->submenu_border_color ) ? $settings->submenu_border_size : ''; ?>px;
	<?php if ( ! empty( $settings->submenu_border_color ) ) { ?>
	border-color: <?php echo pp_get_color_value( $settings->submenu_border_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->submenu_background_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->submenu_background_color ); ?>;
	<?php } ?>
	color: <?php echo empty( $settings->submenu_link_color ) ? pp_get_color_value( $settings->link_color ) : pp_get_color_value( $settings->submenu_link_color ); ?>;
}
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a:focus {
	<?php if ( isset( $settings->submenu_border_hover_color ) && ! empty( $settings->submenu_border_hover_color ) ) { ?>
	border-color: <?php echo pp_get_color_value( $settings->submenu_border_hover_color ); ?>;
	<?php } ?>
}
<?php
// Submenu link Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'submenu_link_padding',
	'selector' 		=> ".fl-node-$id .pp-advanced-menu .sub-menu > li > a, .fl-node-$id .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'submenu_link_padding_top',
		'padding-right' 	=> 'submenu_link_padding_right',
		'padding-bottom' 	=> 'submenu_link_padding_bottom',
		'padding-left' 		=> 'submenu_link_padding_left',
	),
) );
?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li:last-child > a:not(:focus),
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li:last-child > .pp-has-submenu-container > a:not(:focus) {
	border: 0;
}

.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li.current-menu-item > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li.current-menu-item > .pp-has-submenu-container > a {
	<?php if ( ! empty( $settings->submenu_background_hover_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->submenu_background_hover_color ); ?>;
	<?php } ?>
	color: <?php echo empty( $settings->submenu_link_hover_color ) ? pp_get_color_value( $settings->link_hover_color ) : pp_get_color_value( $settings->submenu_link_hover_color ); ?>;
}

<?php if ( ! empty( $settings->submenu_link_color ) ) { ?>
	<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows .sub-menu .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none .sub-menu .pp-menu-toggle:before {
		border-color: <?php echo pp_get_color_value( $settings->submenu_link_color ); ?>;
	}
	<?php } elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .sub-menu .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .sub-menu .pp-menu-toggle:after {
		border-color: <?php echo pp_get_color_value( $settings->submenu_link_color ); ?>;
	}
	<?php } ?>
<?php } ?>

<?php if ( ! empty( $settings->submenu_link_hover_color ) ) { ?>
	<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-arrows .sub-menu li:hover .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-none .sub-menu li:hover .pp-menu-toggle:before {
		border-color: <?php echo pp_get_color_value( $settings->submenu_link_hover_color ); ?>;
	}
	<?php } elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:after {
		border-color: <?php echo pp_get_color_value( $settings->submenu_link_hover_color ); ?>;
	}
	<?php } ?>
<?php } ?>

<?php if ( 'none' === $settings->submenu_hover_toggle ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu ul.pp-advanced-menu-horizontal li.mega-menu.pp-has-submenu.focus > ul.sub-menu {
	/*display: flex !important;*/
}
<?php } ?>

<?php

/**
 * Toggle - Arrows / None
 */
if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) :
	?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle:before {
		content: '';
		position: absolute;
		right: 50%;
		top: 50%;
		z-index: 1;
		display: block;
		width: 9px;
		height: 9px;
		margin: -5px -5px 0 0;
		border-right: 2px solid;
		border-bottom: 2px solid;
		-webkit-transform-origin: right bottom;
			-ms-transform-origin: right bottom;
			    transform-origin: right bottom;
		-webkit-transform: translateX( -5px ) rotate( 45deg );
			-ms-transform: translateX( -5px ) rotate( 45deg );
				transform: translateX( -5px ) rotate( 45deg );
	}
	<?php if ( 'vertical' == $settings->menu_layout && 'arrows' == $settings->submenu_hover_toggle ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu:not(.off-canvas):not(.full-screen) .pp-has-submenu .pp-menu-toggle:before {
			-webkit-transform: translateY( -5px ) rotate( -45deg );
				-ms-transform: translateY( -5px ) rotate( -45deg );
					transform: translateY( -5px ) rotate( -45deg );
		}
	<?php } ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu.pp-active > .pp-has-submenu-container .pp-menu-toggle {
		-webkit-transform: rotate( -180deg );
			-ms-transform: rotate( -180deg );
				transform: rotate( -180deg );
	}
<?php

/**
 * Toggle - Plus
 */
elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) :
	?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle:after {
		content: '';
		position: absolute;
		z-index: 1;
		display: block;
		border-color: #333;
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle:before {
		left: 50%;
		top: 50%;
		width: 12px;
		border-top: 3px solid;
		-webkit-transform: translate( -50%, -50% );
			-ms-transform: translate( -50%, -50% );
				transform: translate( -50%, -50% );
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle:after {
		left: 50%;
		top: 50%;
		border-left: 3px solid;
		height: 12px;
		-webkit-transform: translate( -50%, -50% );
			-ms-transform: translate( -50%, -50% );
				transform: translate( -50%, -50% );
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu.pp-active > .pp-has-submenu-container .pp-menu-toggle:after {
		display: none;
	}
	<?php
endif;

/*
if ( $settings->menu_layout == 'vertical' && $settings->submenu_hover_toggle == 'arrows' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle:before {
		border-right: 0;
		border-left: 2px solid;
		border-bottom: 2px solid;
	}
<?php }
*/


if ( $settings->menu_layout == 'expanded' && $settings->alignment == 'center' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu-container a > span {
		padding-right: 0;
	}
<?php }

?>

<?php if ( isset( $settings->submenu_arrow_pos ) && '' !== $settings->submenu_arrow_pos ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle {
	right: <?php echo $settings->submenu_arrow_pos; ?>px;
}
<?php } ?>

<?php

/**
 * Submenu toggle
 */
if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) :
	?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-<?php echo $settings->menu_layout; ?>.pp-toggle-arrows .pp-has-submenu-container a {
		padding-right: <?php echo $toggle_width; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-<?php echo $settings->menu_layout ?>.pp-toggle-arrows .pp-has-submenu-container > a > span {
		padding-right: <?php echo 'default' === $settings->mobile_menu_type ? $toggle_width : '0'; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-<?php echo $settings->menu_layout ?>.pp-toggle-arrows .pp-menu-toggle,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-<?php echo $settings->menu_layout ?>.pp-toggle-none .pp-menu-toggle {
		width: <?php echo $toggle_height; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height/2 ?>px 0 0;
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-arrows .pp-menu-toggle,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-none .pp-menu-toggle,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-vertical.pp-toggle-arrows .pp-menu-toggle,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-vertical.pp-toggle-none .pp-menu-toggle {
		width: <?php echo $toggle_width; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height/2 ?>px 0 0;
	}
<?php elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) : ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-<?php echo $settings->menu_layout ?>.pp-toggle-plus .pp-has-submenu-container a {
		padding-right: <?php echo $toggle_width; ?>px;
	}

	.fl-node-<?php echo $id; ?> .pp-advanced-menu-accordion.pp-toggle-plus .pp-menu-toggle {
		width: <?php echo $toggle_height; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height/2; ?>px 0 0;
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-plus .pp-menu-toggle,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-vertical.pp-toggle-plus .pp-menu-toggle {
		width: <?php echo $toggle_width; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height/2; ?>px 0 0;
	}
<?php endif;


/**
 * Separators
 */
?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu li:first-child {
	border-top: none;
}
<?php
if ( isset( $settings->show_separator ) && $settings->show_separator == 'yes' ) { ?>
	<?php

		$separator_raw_color = ! empty( $settings->separator_color ) ? $settings->separator_color : '000000';
		$separator_opacity   = ! empty( $settings->separator_opacity ) ? $settings->separator_opacity : '100';
		$separator_color     = 'rgba('. implode( ',', FLBuilderColor::hex_to_rgb( $separator_raw_color ) ) .','. ( $separator_opacity / 100 ) .')';

	 ?>
	.fl-node-<?php echo $id; ?> .menu.pp-advanced-menu-<?php echo $settings->menu_layout ?> li,
	.fl-node-<?php echo $id; ?> .menu.pp-advanced-menu-horizontal li li {
		border-color: <?php echo pp_get_color_value( $separator_raw_color ); ?>;
		border-color: <?php echo $separator_color; ?>;
	}
<?php } ?>

<?php if ( 'always' == $module->get_media_breakpoint() ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile + .pp-clear + .pp-menu-nav ul.menu {
    display: none;
}
.fl-node-<?php echo $id; ?> .pp-advanced-menu:not(.off-canvas):not(.full-screen) .pp-advanced-menu-horizontal {
	display: block;
}
	<?php if ( 'expanded' != $settings->mobile_toggle ) { ?>
	body:not(.fl-builder-edit) .fl-node-<?php echo $id; ?>:not(.fl-node-<?php echo $id; ?>-clone):not(.pp-menu-full-screen):not(.pp-menu-off-canvas) .pp-menu-position-below .pp-menu-nav {
		display: none;
	}
	<?php } ?>
<?php } else { ?>
	@media only screen and (max-width: <?php echo $module->get_media_breakpoint() ?>px) {
		.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle + .pp-clear + .pp-menu-nav ul.menu {
			display: none;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu:not(.off-canvas):not(.full-screen) .pp-advanced-menu-horizontal {
			display: block;
		}
		<?php if ( 'expanded' != $settings->mobile_toggle ) { ?>
		.fl-node-<?php echo $id; ?>:not(.fl-node-<?php echo $id; ?>-clone):not(.pp-menu-full-screen):not(.pp-menu-off-canvas) .pp-menu-position-below .pp-menu-nav {
			display: none;
		}
		<?php } ?>
	}
<?php } ?>

<?php
/**
 * Responsive enabled
 */
if ( $global_settings->responsive_enabled ) : ?>

	<?php if ( isset( $settings->mobile_toggle ) && in_array( $settings->mobile_toggle, array( 'hamburger', 'hamburger-label' ) ) ) { ?>
		<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
			@media ( max-width: <?php echo $module->get_media_breakpoint() ?>px ) {
		<?php endif; ?>

			.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu {
				margin-top: 20px;
			}
			<?php if ( $settings->mobile_toggle != 'expanded' ) : ?>
				.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu {
				}
			<?php endif; ?>
			.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li {
				margin-left: 0 !important;
				margin-right: 0 !important;
			}

			.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu .pp-menu-close-btn,
			.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn {
				display: block;
			}

			.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu {
				box-shadow: none;
				border: 0;
			}

		<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
		} <?php // close media max-width ?>
		<?php endif; ?>
	<?php } ?>

	<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
		@media ( min-width: <?php echo ( $module->get_media_breakpoint() ) + 1 ?>px ) {

		<?php // if menu is horizontal ?>
		<?php if ( $settings->menu_layout == 'horizontal' ) : ?>
			.fl-node-<?php echo $id; ?> .menu > li {
				display: inline-block;
			}

			.fl-node-<?php echo $id; ?> .menu li {
				border-top: none;
			}

			.fl-node-<?php echo $id; ?> .menu li:first-child {
				border: none;
			}
			.fl-node-<?php echo $id; ?> .menu li li {
				border-left: none;
			}

			.fl-node-<?php echo $id; ?> .menu .pp-has-submenu .sub-menu {
				position: absolute;
				top: 100%;
				left: 0;
				z-index: 10;
				visibility: hidden;
				opacity: 0;
				text-align:left;
			}

			.fl-node-<?php echo $id; ?> .pp-has-submenu .pp-has-submenu .sub-menu {
				top: 0;
				left: 100%;
			}

		<?php // if menu is vertical ?>
		<?php elseif ( $settings->menu_layout == 'vertical' ) : ?>

			.fl-node-<?php echo $id; ?> .menu .pp-has-submenu .sub-menu {
				position: absolute;
				top: 0;
				left: 100%;
				z-index: 10;
				visibility: hidden;
				opacity: 0;
			}

		<?php endif; ?>

		<?php // if menu is horizontal or vertical ?>
		<?php if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) : ?>

			.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu:hover > .sub-menu,
			.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu.pp-active .sub-menu
			/*.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu.focus > .sub-menu*/ {
				display: block;
				visibility: visible;
				opacity: 1;
			}

			.fl-node-<?php echo $id; ?> .menu .pp-has-submenu.pp-menu-submenu-right .sub-menu {
				top: 100%;
				left: inherit;
				right: 0;
			}

			.fl-node-<?php echo $id; ?> .menu .pp-has-submenu .pp-has-submenu.pp-menu-submenu-right .sub-menu {
				top: 0;
				left: inherit;
				right: 100%;
			}

			.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu.pp-active > .pp-has-submenu-container .pp-menu-toggle {
				-webkit-transform: none;
					-ms-transform: none;
						transform: none;
			}

			<?php //change selector depending on layout ?>
			<?php if ( 'arrows' == $settings->submenu_hover_toggle ) : ?>
				<?php if ( 'horizontal' == $settings->menu_layout ) : ?>
					.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu .pp-has-submenu .pp-menu-toggle:before {
				<?php elseif ( 'vertical' == $settings->menu_layout ) : ?>
					.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu .pp-menu-toggle:before {
				<?php endif; ?>
						-webkit-transform: translateY( -5px ) rotate( -45deg );
							-ms-transform: translateY( -5px ) rotate( -45deg );
								transform: translateY( -5px ) rotate( -45deg );
					}
			<?php endif; ?>

			<?php if ( 'none' == $settings->submenu_hover_toggle ) : ?>
				.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle {
					display: none;
				}
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( $settings->mobile_toggle != 'expanded' ) : ?>
			div.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle {
				display: none;
			}
		<?php endif; ?>

		} <?php // close media min-width ?>
		<?php
	endif;
/**
 * Responsive NOT enabled
 */
else: ?>

	<?php // if menu is horizontal ?>
	<?php if ( $settings->menu_layout == 'horizontal' ) : ?>

		.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li {
			float: left;
		}

		.fl-node-<?php echo $id; ?> .menu li {
			border-left: 1px solid transparent;
		}

		.fl-node-<?php echo $id; ?> .menu li:first-child {
			border: none;
		}

		.fl-node-<?php echo $id; ?> .menu li li {
			border-left: none;
		}

	<?php endif; ?>

	<?php // if menu is horizontal or vertical ?>
	<?php if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) : ?>

		.fl-node-<?php echo $id; ?> .menu .pp-has-submenu .sub-menu {
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 10;
			visibility: hidden;
			opacity: 0;
		}

		.fl-node-<?php echo $id; ?> .menu .pp-has-submenu .pp-has-submenu .sub-menu {
			top: 0;
			left: 100%;
		}

		.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu.pp-toggle-arrows .pp-has-submenu .pp-has-submenu .pp-menu-toggle:before {
			-webkit-transform: translateY( -2px ) rotate( -45deg );
				-ms-transform: translateY( -2px ) rotate( -45deg );
					transform: translateY( -2px ) rotate( -45deg );
		}

		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu:hover > .sub-menu,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-has-submenu.focus > .sub-menu {
			display: block;
			visibility: visible;
			opacity: 1;
		}

		<?php if ( $settings->submenu_hover_toggle == 'none' ) : ?>
			.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-toggle {
				display: none;
			}
		<?php endif; ?>

	<?php endif; ?>

	<?php if( $settings->mobile_toggle == 'expanded' ) { ?>
		div.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle {
			display: none;
		}
	<?php } ?>

	<?php if ( 'always' != $module->get_media_breakpoint() ) { ?>
	@media (min-width: <?php echo $module->get_media_breakpoint(); ?>px) {
		div.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle {
			display: none;
		}
	}
	<?php } ?>

<?php endif; ?>

<?php

/**
 * Mobile toggle button
 */
if( isset( $settings->mobile_toggle ) && $settings->mobile_toggle != 'expanded' ) { ?>
	<?php if( !empty( $settings->menu_align ) && $settings->menu_align != 'default' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle{
			<?php
				if( in_array( $settings->menu_align, array( 'left', 'right' ) ) ) {
					echo 'float: '. $settings->menu_align .';';
				}
			?>
		}
	<?php } ?>

	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile {
		text-align: <?php echo $settings->alignment; ?>;
		<?php
			$toggle_alignment_desktop = 'center';
			if ( 'left' == $settings->alignment ) {
				$toggle_alignment_desktop = 'flex-start';
			} elseif ( 'right' == $settings->alignment ) {
				$toggle_alignment_desktop = 'flex-end';
			}
		?>
		justify-content: <?php echo $toggle_alignment_desktop; ?>;
	}

	.fl-builder-content .fl-node-<?php echo $id; ?>.fl-module .pp-advanced-menu-mobile .pp-advanced-menu-mobile-toggle,
	.fl-page .fl-builder-content .fl-node-<?php echo $id; ?>.fl-module .pp-advanced-menu-mobile .pp-advanced-menu-mobile-toggle {
		<?php if ( isset( $settings->mobile_toggle_bg_color ) && ! empty( $settings->mobile_toggle_bg_color ) ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->mobile_toggle_bg_color ); ?>;
		<?php } ?>
		<?php if ( $settings->mobile_toggle_font['family'] != 'Default' && ( $settings->mobile_toggle == 'hamburger-label' || $settings->mobile_toggle == 'text' ) ) { ?>
		<?php FLBuilderFonts::font_css( $settings->mobile_toggle_font ); ?>
	   	<?php } ?>
		<?php if ( $settings->mobile_toggle_font_size == 'custom' && $settings->mobile_toggle_font_size_custom ) { ?>
			font-size: <?php echo $settings->mobile_toggle_font_size_custom; ?>px;
		<?php } ?>
	}

	.fl-builder-content .fl-node-<?php echo $id; ?>.fl-module .pp-advanced-menu-mobile .pp-advanced-menu-mobile-toggle,
	.fl-page .fl-builder-content .fl-node-<?php echo $id; ?>.fl-module .pp-advanced-menu-mobile .pp-advanced-menu-mobile-toggle,
	fl-builder-content .fl-node-<?php echo $id; ?>.fl-module .pp-advanced-menu-mobile .pp-advanced-menu-mobile-toggle i,
	.fl-page .fl-builder-content .fl-node-<?php echo $id; ?>.fl-module .pp-advanced-menu-mobile .pp-advanced-menu-mobile-toggle i {
		<?php if ( ! empty( $settings->mobile_toggle_color ) ) { ?>
			color: <?php echo pp_get_color_value( $settings->mobile_toggle_color ); ?>;
		<?php } ?>
	}

	<?php
	// Mobile toggle border.
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'mobile_toggle_border',
		'selector' 		=> ".fl-builder-content .fl-node-$id .pp-advanced-menu .pp-advanced-menu-mobile .pp-advanced-menu-mobile-toggle, .fl-page .fl-builder-content .fl-node-$id .pp-advanced-menu .pp-advanced-menu-mobile .pp-advanced-menu-mobile-toggle",
	) );
	?>

	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:after {
		<?php if ( $settings->mobile_toggle_size !== '' ) { ?>
			width: <?php echo $settings->mobile_toggle_size; ?>px;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:before,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:after {
		<?php if ( !empty( $settings->mobile_toggle_color ) ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->mobile_toggle_color ); ?>;
		<?php } ?>
		<?php if ( $settings->mobile_toggle_thickness !== '' ) { ?>
			height: <?php echo $settings->mobile_toggle_thickness; ?>px;
		<?php } ?>
	}

	<?php if ( $settings->mobile_toggle_size !== '' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle i {
		font-size: <?php echo $settings->mobile_toggle_size; ?>px;
	}
	<?php } ?>

	<?php if ( ! empty( $settings->link_color ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle rect {
		fill: <?php echo pp_get_color_value( $settings->link_color ); ?>;
	}
	<?php } ?>
<?php } ?>

<?php if ( isset( $settings->mobile_button_label ) && $settings->mobile_button_label == 'no' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle.hamburger .pp-menu-mobile-toggle-label {
		display: none;
	}
<?php } ?>

<?php if ( isset( $settings->show_search ) && 'yes' === $settings->show_search && class_exists( 'PPSearchFormModule' ) ) {
	FLBuilder::render_module_css( 'pp-search-form', $id, $module->menu_search_settings() );

	// Size
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'search_container_width',
		'selector'     => ".fl-node-$id .pp-search-form__container",
		'prop'         => 'width',
		'unit'			=> $settings->search_container_width_unit
	) );
} ?>

/**
 * Woo Menu Cart
 */
<?php if ( class_exists( 'WooCommerce' ) && isset( $settings->show_woo_cart ) && 'yes' == $settings->show_woo_cart ) :
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .pp-advanced-menu li.pp-menu-cart-item-hidden",
		'enabled'  => ! empty( $settings->woo_cart_on_checkout ) && 'no' == $settings->woo_cart_on_checkout,
		'props'    => array(
			'display' => 'none',
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .pp-advanced-menu li.pp-menu-cart-item a.pp-menu-cart-contents",
		'enabled'  => ! empty( $settings->woo_cart_bg_color ),
		'props'    => array(
			'background-color' => $settings->woo_cart_bg_color,
		),
	) );
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .pp-advanced-menu li.pp-menu-cart-item:hover a.pp-menu-cart-contents",
		'enabled'  => ! empty( $settings->woo_cart_bg_hover_color ),
		'props'    => array(
			'background-color' => $settings->woo_cart_bg_hover_color,
		),
	) );

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .pp-advanced-menu li.pp-menu-cart-item a.pp-menu-cart-contents",
		'enabled'  => ! empty( $settings->woo_cart_color ),
		'props'    => array(
			'color' => $settings->woo_cart_color,
		),
	) );
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .pp-advanced-menu li.pp-menu-cart-item:hover a.pp-menu-cart-contents",
		'enabled'  => ! empty( $settings->woo_cart_hover_color ),
		'props'    => array(
			'color' => $settings->woo_cart_hover_color,
		),
	) );

	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'woo_cart_border',
		'selector' 		=> ".fl-node-$id .pp-advanced-menu li.pp-menu-cart-item a.pp-menu-cart-contents",
	) );
endif; ?>

<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
	@media ( min-width: <?php echo ( $module->get_media_breakpoint() ) + 1 ?>px ) {
		<?php // if menu is horizontal or vertical ?>
		<?php if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) { ?>
			.fl-node-<?php echo $id; ?> .pp-advanced-menu ul.sub-menu {
				padding: <?php echo ! empty( $settings->submenu_spacing ) ? $settings->submenu_spacing . 'px' : '0' ?>;
			}
		<?php } ?>
		<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
			.fl-node-<?php echo $id; ?> .pp-advanced-menu-<?php echo $settings->menu_layout ?>.pp-toggle-arrows .pp-has-submenu-container > a > span {
				padding-right: <?php echo $toggle_width; ?>px;
			}
		<?php } ?>
	}
<?php endif; ?>

@media only screen and (max-width: <?php echo $global_settings->large_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal {
		<?php if ( 'left' === $settings->alignment_large ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'center' === $settings->alignment_large ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'right' === $settings->alignment_large ) { ?>
			justify-content: flex-end;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li {
		<?php if ( isset( $settings->spacing_large ) && '' !== $settings->spacing_large ) { ?>
			<?php if( $settings->alignment_large == 'left' ) { ?>
				margin-right: <?php echo ( $settings->spacing_large ); ?>px;
			<?php } elseif( $settings->alignment_large == 'right' ) { ?>
				margin-left: <?php echo ( $settings->spacing_large ); ?>px;
			<?php } else { ?>
				margin-left: <?php echo ( $settings->spacing_large / 2 ); ?>px;
				margin-right: <?php echo ( $settings->spacing_large / 2 ); ?>px;
			<?php } ?>
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile {
		<?php
			$toggle_alignment_large = 'center';
			if ( 'left' == $settings->alignment_large ) {
				$toggle_alignment_large = 'flex-start';
			} elseif ( 'right' == $settings->alignment_large ) {
				$toggle_alignment_large = 'flex-end';
			}
		?>
		justify-content: <?php echo $toggle_alignment_large; ?>;
	}
}

@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal {
		<?php if ( 'left' === $settings->alignment_medium ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'center' === $settings->alignment_medium ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'right' === $settings->alignment_medium ) { ?>
			justify-content: flex-end;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li {
		<?php if ( isset( $settings->spacing_medium ) && '' !== $settings->spacing_medium ) { ?>
			<?php if( $settings->alignment_medium == 'left' ) { ?>
				margin-right: <?php echo ( $settings->spacing_medium ); ?>px;
			<?php } elseif( $settings->alignment_medium == 'right' ) { ?>
				margin-left: <?php echo ( $settings->spacing_medium ); ?>px;
			<?php } else { ?>
				margin-left: <?php echo ( $settings->spacing_medium / 2 ); ?>px;
				margin-right: <?php echo ( $settings->spacing_medium / 2 ); ?>px;
			<?php } ?>
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > a,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a {
		border-bottom-width: <?php echo ( $settings->submenu_border_size_medium != '' && $settings->submenu_border_color ) ? $settings->submenu_border_size_medium : ''; ?>px;
		<?php if( isset( $settings->responsive_submenu_bg_color ) ) { ?>
			background-color: <?php echo pp_get_color_value($settings->responsive_submenu_bg_color); ?>;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu {
		width: auto;
	}

	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile {
		<?php
			$toggle_alignment_medium = 'center';
			if ( 'left' == $settings->alignment_medium ) {
				$toggle_alignment_medium = 'flex-start';
			} elseif ( 'right' == $settings->alignment_medium ) {
				$toggle_alignment_medium = 'flex-end';
			}
		?>
		justify-content: <?php echo $toggle_alignment_medium; ?>;
	}

	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle {
		<?php if( $settings->mobile_toggle_font_size == 'custom' && $settings->mobile_toggle_font_size_custom_medium ) { ?>
		font-size: <?php echo $settings->mobile_toggle_font_size_custom_medium; ?>px;
		<?php } ?>
	}

	<?php if( ( isset( $settings->alignment_medium ) && 'right' == $settings->alignment_medium ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-right: 0;
			padding-left: 28px;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-arrows .pp-menu-toggle {
			right: 0;
			left: 28px;
		}
	<?php } ?>
}

@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> div.pp-advanced-menu {
		text-align: <?php echo $settings->responsive_alignment; ?>;
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal {
		<?php if ( 'left' === $settings->alignment_responsive ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'center' === $settings->alignment_responsive ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'right' === $settings->alignment_responsive ) { ?>
			justify-content: flex-end;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li {
		<?php if ( isset( $settings->spacing_responsive ) && '' !== $settings->spacing_responsive ) { ?>
			<?php if( $settings->alignment_responsive == 'left' ) { ?>
				margin-right: <?php echo ( $settings->spacing_responsive ); ?>px;
			<?php } elseif( $settings->alignment_responsive == 'right' ) { ?>
				margin-left: <?php echo ( $settings->spacing_responsive ); ?>px;
			<?php } else { ?>
				margin-left: <?php echo ( $settings->spacing_responsive / 2 ); ?>px;
				margin-right: <?php echo ( $settings->spacing_responsive / 2 ); ?>px;
			<?php } ?>
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li > a,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .menu > li > .pp-has-submenu-container > a {
		<?php if ( ! empty( $settings->responsive_link_bg_color ) ) { ?>background-color: <?php echo pp_get_color_value( $settings->responsive_link_bg_color ); ?>;<?php } ?>
		<?php if ( ! empty( $settings->responsive_link_color ) ) { ?>color: <?php echo pp_get_color_value( $settings->responsive_link_color ); ?>;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > a,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .sub-menu > li > .pp-has-submenu-container > a {
		border-bottom-width: <?php echo ( $settings->submenu_border_size_responsive != '' && $settings->submenu_border_color ) ? $settings->submenu_border_size_responsive : ''; ?>px;
		<?php if ( isset( $settings->responsive_submenu_bg_color ) && ! empty( $settings->responsive_submenu_bg_color ) ) { ?>
			background-color: <?php echo pp_get_color_value($settings->responsive_submenu_bg_color); ?>;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile {
		<?php if ( isset( $settings->responsive_toggle_alignment ) && 'default' != $settings->responsive_toggle_alignment ) { ?>
			text-align: <?php echo $settings->responsive_toggle_alignment; ?>;
			<?php
			if ( 'left' === $settings->responsive_toggle_alignment ) {
				$toggle_alignment = 'flex-start';
			} elseif ( 'right' === $settings->responsive_toggle_alignment ) {
				$toggle_alignment = 'flex-end';
			} else {
				$toggle_alignment = 'center';
			}
			?>
			justify-content: <?php echo $toggle_alignment; ?>;
		<?php } ?>

		<?php if ( isset( $settings->responsive_toggle_alignment ) && 'default' == $settings->responsive_toggle_alignment ) { ?>
			<?php
			$toggle_alignment_responsive = 'center';
			if ( 'left' == $settings->alignment_responsive ) {
				$toggle_alignment_responsive = 'flex-start';
			} elseif ( 'right' == $settings->alignment_responsive ) {
				$toggle_alignment_responsive = 'flex-end';
			}
			?>
			justify-content: <?php echo $toggle_alignment_responsive; ?>;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle {
		<?php if( $settings->mobile_toggle_font_size == 'custom' && $settings->mobile_toggle_font_size_custom_responsive ) { ?>
			font-size: <?php echo $settings->mobile_toggle_font_size_custom_responsive; ?>px;
		<?php } ?>
	}

	<?php if( $settings->responsive_alignment == 'right' || ( isset( $settings->alignment_responsive ) && 'right' == $settings->alignment_responsive ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-right: 0;
			padding-left: 28px;
		}
		.fl-node-<?php echo $id; ?> .pp-advanced-menu-horizontal.pp-toggle-arrows .pp-menu-toggle {
			right: 0;
			left: 28px;
		}
	<?php } ?>

}

<?php if (  'expanded' != $settings->mobile_toggle ) : ?>
	<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
		@media only screen and ( max-width: <?php echo $module->get_media_breakpoint() ?>px ) {

			.fl-node-<?php echo $id; ?> .pp-advanced-menu {
				text-align: <?php echo $settings->responsive_alignment; ?>;
			}

			.fl-node-<?php echo $id; ?> .pp-advanced-menu-mobile-toggle {
			<?php if ( isset( $settings->responsive_toggle_alignment ) && 'default' != $settings->responsive_toggle_alignment ) { ?>
				text-align: <?php echo $settings->responsive_toggle_alignment; ?>;
				<?php
				if ( 'left' === $settings->responsive_toggle_alignment ) {
					$toggle_alignment = 'flex-start';
				} elseif ( 'right' === $settings->responsive_toggle_alignment ) {
					$toggle_alignment = 'flex-end';
				} else {
					$toggle_alignment = 'center';
				}
				?>
				-webkit-justify-content: <?php echo $toggle_alignment; ?>;
				-ms-flex-pack: <?php echo $toggle_alignment; ?>;
				justify-content: <?php echo $toggle_alignment; ?>;
			<?php } ?>
			}
		}
	<?php endif; ?>
<?php endif; ?>

<?php
if( 'default' != $settings->mobile_menu_type ) {
	include $module->dir . 'includes/menu-' . $settings->mobile_menu_type . '.css.php';
}
