<?php
	$columns            = empty( $settings->logos_grid_columns ) ? 6 : $settings->logos_grid_columns;
	$columns_large      = isset( $settings->logos_grid_columns_large ) && ! empty( $settings->logos_grid_columns_large ) ? $settings->logos_grid_columns_large : $columns;
	$columns_medium     = isset( $settings->logos_grid_columns_medium ) && ! empty( $settings->logos_grid_columns_medium ) ? $settings->logos_grid_columns_medium : $columns_large;
	$columns_responsive = isset( $settings->logos_grid_columns_responsive ) && ! empty( $settings->logos_grid_columns_responsive ) ? $settings->logos_grid_columns_responsive : $columns_medium;

    $spacing = empty( $settings->logos_grid_spacing ) ? 0 : $settings->logos_grid_spacing;

	$space_desktop = $space_large = $space_medium = $space_responsive = ( $columns - 1 ) * $spacing;
	$space_desktop = 0 == $space_desktop ? $spacing : $space_desktop;

	$space_large = $space_medium = $space_responsive = ( $columns_large - 1 ) * $spacing;
	$space_large = 0 == $space_large ? $spacing : $space_large;

	$space_medium = $space_responsive = ( $columns_medium - 1 ) * $spacing;
	$space_medium = 0 == $space_medium ? $spacing : $space_medium;

	$space_responsive = ( $columns_responsive - 1 ) * $spacing;
	$space_responsive = 0 == $space_responsive ? $spacing : $space_responsive;
?>
.fl-node-<?php echo $id; ?> .pp-logos-wrapper.pp-logos-grid {
    gap: <?php echo $spacing; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-logos-wrapper.pp-logos-ticker {
    gap: <?php echo $settings->logos_carousel_spacing; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo {
    <?php if ( $settings->logos_layout == 'grid' ) { ?>
        width: calc((100% - <?php echo $space_desktop + 1; ?>px) / <?php echo $columns; ?>);
    <?php } ?>
    <?php if ( $settings->logos_layout == 'carousel' && $settings->logo_slider_transition == 'fade' ) { ?>
        width: calc((100% - <?php echo ( $settings->logo_carousel_minimum_grid - 1 ) * $settings->logos_carousel_spacing; ?>px) / <?php echo $settings->logo_carousel_minimum_grid; ?>);
    <?php } ?>
	<?php if ( ! empty( $settings->logo_grid_bg_color ) ) { ?>
    background-color: <?php echo pp_get_color_value( $settings->logo_grid_bg_color ); ?>;
    <?php } ?>
}

<?php
	for ( $i = 0; $i < count( $settings->logos_grid ); $i++ ) {

		if ( ! is_object( $settings->logos_grid[ $i ] ) ) {
			continue;
		}

		$item = $settings->logos_grid[ $i ];

		if ( isset( $item->item_bg_color ) && ! empty( $item->item_bg_color ) ) {
		?>
        .fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo-<?php echo $i; ?> {
            background-color: <?php echo pp_get_color_value( $item->item_bg_color ); ?>
        }
		<?php
		}

		if ( isset( $item->item_bg_hover ) && ! empty( $item->item_bg_hover ) ) {
		?>
        .fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo-<?php echo $i; ?>:hover {
            background-color: <?php echo pp_get_color_value( $item->item_bg_hover ); ?>
        }
		<?php
		}
	}
?>

<?php
	// Logo Grid - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'logo_grid_border',
		'selector' 		=> ".fl-node-$id .pp-logos-content .pp-logo",
	) );

	// Logo Grid - Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'logo_grid_padding',
		'selector' 		=> ".fl-node-$id .pp-logos-content .pp-logo",
		'unit'			=> 'px',
		'props'			=> array(
			'padding-top' 		=> 'logo_grid_padding_top',
			'padding-right' 	=> 'logo_grid_padding_right',
			'padding-bottom' 	=> 'logo_grid_padding_bottom',
			'padding-left' 		=> 'logo_grid_padding_left',
		),
	) );
?>

.fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo:hover {
	<?php if( $settings->logo_grid_bg_hover ) { ?>
    background-color: <?php echo pp_get_color_value( $settings->logo_grid_bg_hover ); ?>;
    <?php } ?>
}

<?php
// Height
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'custom_height',
	'selector'		=> ".fl-node-$id .pp-logos-content .pp-logo",
	'prop'			=> 'height',
	'unit'			=> 'px',
) );
?>

.fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo div.title-wrapper {
    display: <?php echo $settings->upload_logo_show_title; ?>
}

.fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo div.title-wrapper p.logo-title {
    <?php if ( ! empty( $settings->logo_grid_title_color ) ) { ?>
    color: <?php echo pp_get_color_value( $settings->logo_grid_title_color ); ?>;
    <?php } ?>
    <?php if ( $settings->logo_grid_title_top_margin >= 0 ) { ?>
    margin-top: <?php echo $settings->logo_grid_title_top_margin; ?>px;
    <?php } ?>
    <?php if ( $settings->logo_grid_title_bottom_margin >= 0 ) { ?>
    margin-bottom: <?php echo $settings->logo_grid_title_bottom_margin; ?>px;
    <?php } ?>
}

<?php
	// Title Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'title_typography',
		'selector' 		=> ".fl-node-$id .pp-logos-content .pp-logo div.title-wrapper p.logo-title",
	) );
?>

.fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo:hover div.title-wrapper p.logo-title {
    <?php if( $settings->logo_grid_title_hover ) { ?>
    color: <?php echo pp_get_color_value( $settings->logo_grid_title_hover ); ?>;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo img {
    <?php if ( $settings->logo_grid_grayscale == 'grayscale' ) { ?>
        -webkit-filter: grayscale(100%);
        filter: grayscale(100%);
    <?php } else { ?>
        -webkit-filter: inherit;
        filter: inherit;
    <?php } ?>
    <?php if ( $settings->logo_grid_logo_border_style ) { ?>
    border-style: <?php echo $settings->logo_grid_logo_border_style; ?>;
    <?php } ?>
    <?php if ( $settings->logo_grid_logo_border_width >= 0 ) { ?>
    border-width: <?php echo $settings->logo_grid_logo_border_width; ?>px;
    <?php } ?>
    <?php if ( $settings->logo_grid_logo_border_color ) { ?>
    border-color: <?php echo pp_get_color_value( $settings->logo_grid_logo_border_color ); ?>;
    <?php } ?>
    <?php if ( $settings->logo_grid_logo_border_radius >= 0 ) { ?>
    border-radius: <?php echo $settings->logo_grid_logo_border_radius; ?>px;
    <?php } ?>
    margin: 0 auto;
    <?php if ( $settings->logo_grid_opacity >= 0 ) { ?>
    opacity: <?php echo $settings->logo_grid_opacity / 100; ?>;
    -webkit-transition: opacity 0.3s ease-in-out;
    -moz-transition: opacity 0.3s ease-in-out;
    -ms-transition: opacity 0.3s ease-in-out;
    transition: opacity 0.3s ease-in-out;
    <?php } ?>
}
<?php
	// Logo Image - Height
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'logo_grid_size',
		'selector'		=> ".fl-node-$id .pp-logos-content .pp-logo img",
		'prop'			=> 'height',
		'unit'			=> 'px',
	) );
?>

.fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo:hover img {
    <?php if ( $settings->logo_grid_grayscale_hover == 'grayscale' ) { ?>
        -webkit-filter: grayscale(100%);
        filter: grayscale(100%);
    <?php } else { ?>
        -webkit-filter: inherit;
        filter: inherit;
    <?php } ?>
    <?php if ( $settings->logo_grid_logo_border_hover != '' ) { ?>
    border-color: <?php echo pp_get_color_value( $settings->logo_grid_logo_border_hover ); ?>;
    <?php } ?>
    <?php if ( $settings->logo_grid_opacity_hover >= 0 ) { ?>
    opacity: <?php echo $settings->logo_grid_opacity_hover / 100; ?>;
    -webkit-transition: opacity 0.3s ease-in-out;
    -moz-transition: opacity 0.3s ease-in-out;
    -ms-transition: opacity 0.3s ease-in-out;
    transition: opacity 0.3s ease-in-out;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-logos-content .bx-pager a {
	opacity: 1;
	<?php if ( isset( $settings->logo_grid_dot_bg_color ) && ! empty( $settings->logo_grid_dot_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->logo_grid_dot_bg_color ); ?>;
	<?php } ?>
    <?php if( $settings->logo_grid_dot_width >= 0 ) { ?>
    width: <?php echo $settings->logo_grid_dot_width; ?>px;
    <?php } ?>
    <?php if( $settings->logo_grid_dot_width >= 0 ) { ?>
    height: <?php echo $settings->logo_grid_dot_width; ?>px;
    <?php } ?>
    <?php if( $settings->logo_grid_dot_border_radius >= 0 ) { ?>
    border-radius: <?php echo $settings->logo_grid_dot_border_radius; ?>px;
    <?php } ?>
    box-shadow: none;
}

.fl-node-<?php echo $id; ?> .pp-logos-content .bx-pager a.active,
.fl-node-<?php echo $id; ?> .pp-logos-content .bx-pager a:hover {
	<?php if ( isset( $settings->logo_grid_dot_bg_hover ) && ! empty( $settings->logo_grid_dot_bg_hover ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->logo_grid_dot_bg_hover ); ?>;
	<?php } ?>
	opacity: 1;
    box-shadow: none;
}

<?php
	// Carousel Arrow - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'logo_grid_arrow',
		'selector' 		=> ".fl-node-$id .pp-logos-content button.logo-slider-nav, .fl-node-$id .pp-logos-content button.logo-slider-nav:hover",
	) );
?>

.fl-node-<?php echo $id; ?> .pp-logos-content button.logo-slider-nav {
	<?php if ( ! $settings->logo_slider_arrows ) { ?>
		display: none;
	<?php } ?>
    <?php if( $settings->logo_slider_arrow_color ) { ?>
	color: <?php echo pp_get_color_value( $settings->logo_slider_arrow_color ); ?>;
    <?php } ?>
	<?php if ( isset( $settings->logo_slider_arrow_bg_color ) && ! empty( $settings->logo_slider_arrow_bg_color ) ) { ?>
	background: <?php echo pp_get_color_value( $settings->logo_slider_arrow_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->logo_grid_arrow_font_size ) && absint( $settings->logo_grid_arrow_font_size ) > 0 ) { ?>
		height: <?php echo ( $settings->logo_grid_arrow_font_size + 10 ); ?>px;
		width: <?php echo ( $settings->logo_grid_arrow_font_size + 10 ); ?>px;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-logos-content .logo-slider-nav svg {
	<?php if ( isset( $settings->logo_grid_arrow_font_size ) && absint( $settings->logo_grid_arrow_font_size ) > 0 ) { ?>
		height: <?php echo $settings->logo_grid_arrow_font_size; ?>px;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-logos-content .logo-slider-nav svg path {
	<?php if ( $settings->logo_slider_arrow_color ) { ?>
	fill: <?php echo pp_get_color_value( $settings->logo_slider_arrow_color ); ?>;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-logos-content button.logo-slider-nav:hover {
    <?php if ( $settings->logo_slider_arrow_color_hover ) { ?>
    color: <?php echo pp_get_color_value( $settings->logo_slider_arrow_color_hover ); ?>;
    <?php } ?>
	<?php if ( isset( $settings->logo_slider_arrow_bg_hover ) && ! empty( $settings->logo_slider_arrow_bg_hover ) ) { ?>
	background: <?php echo pp_get_color_value( $settings->logo_slider_arrow_bg_hover ); ?>;
	<?php } ?>
    <?php if ( $settings->logo_grid_arrow_border_hover ) { ?>
    border-color: <?php echo pp_get_color_value( $settings->logo_grid_arrow_border_hover ); ?>;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-logos-content button.logo-slider-nav:hover svg path {
	<?php if ( $settings->logo_slider_arrow_color_hover ) { ?>
    fill: <?php echo pp_get_color_value( $settings->logo_slider_arrow_color_hover ); ?>;
    <?php } ?>
}

@media only screen and (max-width: <?php echo $global_settings->large_breakpoint; ?>px) {
    .fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo {
        <?php if ( $settings->logos_layout == 'grid' && $columns_large >= 0 ) { ?>
        width: calc((100% - <?php echo $space_large + 1; ?>px) / <?php echo $columns_large; ?>);
        <?php } ?>
    }
	.fl-node-<?php echo $id; ?> .pp-logos-content .logo-slider-nav button {
		<?php if ( isset( $settings->logo_grid_arrow_font_size_large ) && absint( $settings->logo_grid_arrow_font_size_large ) > 0 ) { ?>
			height: <?php echo ( $settings->logo_grid_arrow_font_size_large + 10 ); ?>px;
			width: <?php echo ( $settings->logo_grid_arrow_font_size_large + 10 ); ?>px;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-logos-content .logo-slider-nav svg {
		<?php if ( isset( $settings->logo_grid_arrow_font_size_large ) && absint( $settings->logo_grid_arrow_font_size_large ) > 0 ) { ?>
			height: <?php echo $settings->logo_grid_arrow_font_size_large; ?>px;
		<?php } ?>
	}
}

@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
    .fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo {
        <?php if ( $settings->logos_layout == 'grid' && $columns_medium >= 0 ) { ?>
        width: calc((100% - <?php echo $space_medium + 1; ?>px) / <?php echo $columns_medium; ?>);
        <?php } ?>
    }
	.fl-node-<?php echo $id; ?> .pp-logos-content .logo-slider-nav button {
		<?php if ( isset( $settings->logo_grid_arrow_font_size_medium ) && absint( $settings->logo_grid_arrow_font_size_medium ) > 0 ) { ?>
			height: <?php echo ( $settings->logo_grid_arrow_font_size_medium + 10 ); ?>px;
			width: <?php echo ( $settings->logo_grid_arrow_font_size_medium + 10 ); ?>px;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-logos-content .logo-slider-nav svg {
		<?php if ( isset( $settings->logo_grid_arrow_font_size_medium ) && absint( $settings->logo_grid_arrow_font_size_medium ) > 0 ) { ?>
			height: <?php echo $settings->logo_grid_arrow_font_size_medium; ?>px;
		<?php } ?>
	}
}

@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
    .fl-node-<?php echo $id; ?> .pp-logos-content .pp-logo {
        <?php if ( $settings->logos_layout == 'grid' && $columns_responsive >= 0 ) { ?>
        width: calc((100% - <?php echo $space_responsive + 1; ?>px) / <?php echo $columns_responsive; ?>);
			<?php if ( $columns_responsive == 1 ) { ?>
			width: 100%;
			<?php } ?>
        <?php } ?>
    }
	.fl-node-<?php echo $id; ?> .pp-logos-content .logo-slider-nav button {
		<?php if ( isset( $settings->logo_grid_arrow_font_size_responsive ) && absint( $settings->logo_grid_arrow_font_size_responsive ) > 0 ) { ?>
			height: <?php echo ( $settings->logo_grid_arrow_font_size_responsive + 10 ); ?>px;
			width: <?php echo ( $settings->logo_grid_arrow_font_size_responsive + 10 ); ?>px;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-logos-content .logo-slider-nav svg {
		<?php if ( isset( $settings->logo_grid_arrow_font_size_responsive ) && absint( $settings->logo_grid_arrow_font_size_responsive ) > 0 ) { ?>
			height: <?php echo $settings->logo_grid_arrow_font_size_responsive; ?>px;
		<?php } ?>
	}
}