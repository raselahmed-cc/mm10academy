<?php

function pp_row_render_css( $extensions ) {

    // if ( array_key_exists( 'gradient', $extensions['row'] ) || in_array( 'gradient', $extensions['row'] ) ) {
    //     add_filter( 'fl_builder_render_css', 'pp_row_gradient_css', 10, 3 );
    // }
    if ( array_key_exists( 'overlay', $extensions['row'] ) || in_array( 'overlay', $extensions['row'] ) ) {
        add_filter( 'fl_builder_render_css', 'pp_row_overlay_css', 10, 3 );
    }
    if ( array_key_exists( 'separators', $extensions['row'] ) || in_array( 'separators', $extensions['row'] ) ) {
        add_filter( 'fl_builder_render_css', 'pp_row_separators_css', 10, 3 );
    }
    if ( array_key_exists( 'expandable', $extensions['row'] ) || in_array( 'expandable', $extensions['row'] ) ) {
        add_filter( 'fl_builder_render_css', 'pp_row_expandable_css', 10, 3 );
    }
    if ( array_key_exists( 'downarrow', $extensions['row'] ) || in_array( 'downarrow', $extensions['row'] ) ) {
        add_filter( 'fl_builder_render_css', 'pp_row_downarrow_css', 10, 3 );
    }
    if ( array_key_exists( 'background_effect', $extensions['row'] ) || in_array( 'background_effect', $extensions['row'] ) ) {
        add_filter( 'fl_builder_render_css', 'pp_row_infinite_bg_css', 10, 3 );
		add_filter( 'fl_builder_render_css', 'pp_row_animated_bg_css', 10, 3 );
	}

}

function pp_row_gradient_css( $css, $nodes, $global_settings ) {
    foreach ( $nodes['rows'] as $row ) {
        ob_start();

        if ( isset( $row->settings->bg_type ) && 'pp_gradient' == $row->settings->bg_type ) {
			$primary_color   = pp_get_color_value( $row->settings->gradient_color['primary'] );
			$secondary_color = pp_get_color_value( $row->settings->gradient_color['secondary'] );
        ?>

            <?php if ( $row->settings->gradient_type == 'linear' && isset( $row->settings->gradient_color ) ) { ?>
                <?php if ( $row->settings->linear_direction == 'bottom' ) { ?>
                    .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap {
                        background-color: <?php echo $primary_color; ?>;
                        background-image: -webkit-linear-gradient(top, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -moz-linear-gradient(bottom, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -o-linear-gradient(bottom, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -ms-linear-gradient(bottom, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: linear-gradient(to bottom, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    }
                <?php } ?>
                <?php if ( $row->settings->linear_direction == 'right' ) { ?>
                    .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap {
                        background-color: <?php echo $primary_color; ?>;
                        background-image: -webkit-linear-gradient(left, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -moz-linear-gradient(right, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -o-linear-gradient(right, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -ms-linear-gradient(right, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: linear-gradient(to right, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    }
                <?php } ?>
                <?php if ( $row->settings->linear_direction == 'top_right_diagonal' ) { ?>
                    .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap {
                        background-color: <?php echo $primary_color; ?>;
                        background-image: -webkit-linear-gradient(45deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -moz-linear-gradient(45deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -o-linear-gradient(45deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -ms-linear-gradient(45deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: linear-gradient(45deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    }
                <?php } ?>
                <?php if ( $row->settings->linear_direction == 'top_left_diagonal' ) { ?>
                    .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap {
                        background-color: <?php echo $primary_color; ?>;
                        background-image: -webkit-linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -moz-linear-gradient(315deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -o-linear-gradient(315deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -ms-linear-gradient(315deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: linear-gradient(315deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    }
                <?php } ?>
                <?php if ( $row->settings->linear_direction == 'bottom_right_diagonal' ) { ?>
                    .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap {
                        background-color: <?php echo $primary_color; ?>;
                        background-image: -webkit-linear-gradient(315deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -moz-linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -o-linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -ms-linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    }
                <?php } ?>
                <?php if ( $row->settings->linear_direction == 'bottom_left_diagonal' ) { ?>
                    .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap {
                        background-color: <?php echo $primary_color; ?>;
                        background-image: -webkit-linear-gradient(255deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -moz-linear-gradient(210deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -o-linear-gradient(210deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: -ms-linear-gradient(210deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                        background-image: linear-gradient(210deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    }
                <?php } ?>
            <?php } ?>
            <?php if ( $row->settings->gradient_type == 'radial' && isset( $row->settings->gradient_color ) ) { ?>
                .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap {
                    background-color: <?php echo $primary_color; ?>;
                    background-image: -webkit-radial-gradient(circle, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    background-image: -moz-radial-gradient(circle, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    background-image: -o-radial-gradient(circle, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    background-image: -ms-radial-gradient(circle, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                    background-image: radial-gradient(circle, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
                }
            <?php } ?>

        <?php
        }

        $css .= ob_get_clean();
    }

    return $css;
}

function pp_row_overlay_css( $css, $nodes, $global_settings ) {
    foreach ( $nodes['rows'] as $row ) {
        ob_start();
        ?>

        <?php if ( $row->settings->pp_bg_overlay_type == 'vertical_left' ) { ?>
            .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap:after {
                background-color: transparent !important;
                background: -webkit-linear-gradient( -170deg, rgba(225, 255, 255, 0) 0%, rgba(225, 255, 255, 0) 54.96%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%);
                background: -moz-linear-gradient( -170deg, rgba(225, 255, 255, 0) 0%, rgba(225, 255, 255, 0) 54.96%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%);
                background: -ms-linear-gradient( -170deg, rgba(225, 255, 255, 0) 0%, rgba(225, 255, 255, 0) 54.96%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%);
                background: linear-gradient( -100deg, rgba(225, 255, 255, 0) 0%, rgba(225, 255, 255, 0) 54.96%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%);
            }
        <?php } ?>
        <?php if ( $row->settings->pp_bg_overlay_type == 'vertical_right' ) { ?>
            .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap:after {
                background-color: transparent !important;
                background: -webkit-linear-gradient( -10deg, rgba(225, 255, 255, 0) 0%, rgba(225, 255, 255, 0) 54.96%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%);
                background: -moz-linear-gradient( -10deg, rgba(225, 255, 255, 0) 0%, rgba(225, 255, 255, 0) 54.96%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%);
                background: -ms-linear-gradient( -10deg, rgba(225, 255, 255, 0) 0%, rgba(225, 255, 255, 0) 54.96%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%);
                background: linear-gradient( 100deg, rgba(225, 255, 255, 0) 0%, rgba(225, 255, 255, 0) 54.96%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%, <?php echo pp_get_color_value( $row->settings->bg_overlay_color ); ?> 55%);
            }
        <?php } ?>
        <?php if ( $row->settings->pp_bg_overlay_type == 'half_width' ) { ?>
            .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap:after {
                width: 50%;
            }
        <?php } ?>
        <?php if ( $row->settings->pp_bg_overlay_type == 'half_right' ) { ?>
            .fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap:after {
                width: 50%;
				left: auto;
            }
        <?php } ?>

        <?php
        $css .= ob_get_clean();
    }

    return $css;
}

function pp_row_separators_css( $css, $nodes, $global_settings ) {
    foreach ( $nodes['rows'] as $row ) {
        ob_start();
        ?>

        .fl-builder-row-settings #fl-field-separator_position {
            display: none !important;
        }
        <?php if ( 'none' != $row->settings->separator_type || 'none' != $row->settings->separator_type_bottom ) { ?>

            .fl-node-<?php echo $row->node; ?> .pp-row-separator {
                position: absolute;
                left: 0;
                width: 100%;
                z-index: 1;
            }
            .pp-previewing .fl-node-<?php echo $row->node; ?> .pp-row-separator {
                z-index: 2001;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator svg {
                position: absolute;
                left: 0;
                width: 100%;
            }
			.fl-node-<?php echo $row->node; ?> .pp-row-separator-top {
				margin-top: -1px;
			}
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg {
                top: 0;
                bottom: auto;
            }
			.fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                margin-bottom: -1px;
			}
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg {
                top: auto;
                bottom: 0;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-big-triangle,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-big-triangle-shadow,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-big-triangle-left,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-big-triangle-right,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-small-triangle,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-tilt-right,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-tilt-right,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-curve,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-twin-curves,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-wave,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-cloud,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-slit,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-water-separator {
                transform: scaleY(-1);
            }
            
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-big-triangle-right,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-tilt-right {
                transform: scaleX(-1);
            }

            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-tilt-left {
                transform: scale(-1);
            }

            <?php if ( 'zigzag' == $row->settings->separator_type || 'zigzag' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator .pp-zigzag:before,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator .pp-zigzag:after {
                content: '';
                pointer-events: none;
                position: absolute;
                right: 0;
                left: 0;
                z-index: 1;
                display: block;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top .pp-zigzag:before,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top .pp-zigzag:after {
                height: <?php echo $row->settings->separator_height; ?>px;
                background-size: <?php echo $row->settings->separator_height; ?>px 100%;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom .pp-zigzag:before,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom .pp-zigzag:after {
                height: <?php echo $row->settings->separator_height_bottom; ?>px;
                background-size: <?php echo $row->settings->separator_height_bottom; ?>px 100%;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator .pp-zigzag:after {
                top: 100%;
                background-position: 50%;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top .pp-zigzag:after {
                background-image: -webkit-gradient(linear, 0 0, 300% 100%, color-stop(0.25, <?php echo pp_get_color_value( $row->settings->separator_color ); ?>), color-stop(0.25, <?php echo pp_get_color_value( $row->settings->separator_color ); ?>));
                background-image: linear-gradient(135deg, <?php echo pp_get_color_value( $row->settings->separator_color ); ?> 25%, transparent 25%), linear-gradient(225deg, <?php echo pp_get_color_value( $row->settings->separator_color ); ?> 25%, transparent 25%);
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom .pp-zigzag:after {
                background-image: -webkit-gradient(linear, 0 0, 300% 100%, color-stop(0.25, <?php echo pp_get_color_value( $row->settings->separator_color_bottom ); ?>), color-stop(0.25, <?php echo pp_get_color_value( $row->settings->separator_color_bottom ); ?>));
                background-image: linear-gradient(135deg, <?php echo pp_get_color_value( $row->settings->separator_color_bottom ); ?> 25%, transparent 25%), linear-gradient(225deg, <?php echo pp_get_color_value( $row->settings->separator_color_bottom ); ?> 25%, transparent 25%);
            }
            <?php } ?>

			<?php if ( 'mountains' == $row->settings->separator_type || 'mountains' == $row->settings->separator_type_bottom ) { ?>
				.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains {
					position: absolute;
					left: 0;
					right: 0;
					width: 100%;
					height: 200px;
					z-index: 2;
				}
                .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains.pp-row-separator-top {
					height: <?php echo $row->settings->separator_height; ?>px;
					transform: rotate(180deg) translateZ(0);
				}
                .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains.pp-row-separator-bottom {
					height: <?php echo $row->settings->separator_height_bottom; ?>px;
					transform: translateZ(0);
				}
                .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains svg {
                    height: 100% !important;
                    width: 100%;
					left: 0;
					bottom: -1px;
					position: absolute;
                }
				.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains svg path:first-child {
					opacity: 0.1;
				}
				.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains svg path:nth-child(2) {
					opacity: 0.12;
				}
				.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains svg path:nth-child(3) {
					opacity: 0.13;
				}
				.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains svg path:nth-child(4) {
					opacity: 0.33;
				}
			<?php } ?>

			<?php if ( 'curve_layers' == $row->settings->separator_type || 'curve_layers' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers {
                position: absolute;
				left: 0;
				right: 0;
				width: 100%;
				height: 150px;
				z-index: 2;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers.pp-row-separator-top {
				top: -1px;
    			bottom: auto;
				height: <?php echo $row->settings->separator_height; ?>px;
				transform: rotate(180deg);
			}
			.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers.pp-row-separator-bottom {
				height: <?php echo $row->settings->separator_height_bottom; ?>px;
				transform: translateZ(0);
			}
			.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers svg {
				width: 100%;
				left: 0;
				bottom: -1px;
				height: 100%;
				position: absolute;
			}
			.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers svg path:nth-child(1) {
				opacity: 0.15;
			}
			.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers svg path:nth-child(2) {
				opacity: 0.3;
			}
            <?php } ?>

            <?php if ( isset( $row->settings->separator_large, $row->settings->separator_large_bottom ) ) { ?>
            @media only screen and (max-width: <?php echo $global_settings->large_breakpoint; ?>px) {
                <?php if ( 'no' == $row->settings->separator_large ) { ?>
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-top {
                    display: none;
                }
                <?php } ?>
                <?php if ( 'no' == $row->settings->separator_large_bottom ) { ?>
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                    display: none;
                }
                <?php } ?>
                <?php if ( 'yes' == $row->settings->separator_large && $row->settings->separator_height_large > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg {
                        height: <?php echo $row->settings->separator_height_large; ?>px;
                    }
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains.pp-row-separator-top,
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers.pp-row-separator-top {
						height: <?php echo $row->settings->separator_height_large; ?>px;
					}
                <?php } ?>
                <?php if ( 'yes' == $row->settings->separator_large_bottom && $row->settings->separator_height_large_bottom > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg {
                        height: <?php echo $row->settings->separator_height_large_bottom; ?>px;
                    }
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains.pp-row-separator-bottom,
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers.pp-row-separator-bottom {
						height: <?php echo $row->settings->separator_height_large_bottom; ?>px;
					}
                <?php } ?>
            }
            <?php } ?>
            @media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-top,
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                    display: block;
                }
                <?php if ( 'no' == $row->settings->separator_tablet ) { ?>
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-top {
                    display: none;
                }
                <?php } ?>
                <?php if ( 'no' == $row->settings->separator_tablet_bottom ) { ?>
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                    display: none;
                }
                <?php } ?>
                <?php if ( 'yes' == $row->settings->separator_tablet && $row->settings->separator_height_tablet > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg {
                        height: <?php echo $row->settings->separator_height_tablet; ?>px;
                    }
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains.pp-row-separator-top,
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers.pp-row-separator-top {
						height: <?php echo $row->settings->separator_height_tablet; ?>px;
					}
                <?php } ?>
                <?php if ( 'yes' == $row->settings->separator_tablet_bottom && $row->settings->separator_height_tablet_bottom > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg {
                        height: <?php echo $row->settings->separator_height_tablet_bottom; ?>px;
                    }
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains.pp-row-separator-bottom,
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers.pp-row-separator-bottom {
						height: <?php echo $row->settings->separator_height_tablet_bottom; ?>px;
					}
                <?php } ?>
            }
            @media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-top,
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                    display: block;
                }
                <?php if ( 'no' == $row->settings->separator_mobile ) { ?>
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-top {
                    display: none;
                }
                <?php } ?>
                <?php if ( 'no' == $row->settings->separator_mobile_bottom ) { ?>
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                    display: none;
                }
                <?php } ?>
                <?php if ( 'yes' == $row->settings->separator_mobile && $row->settings->separator_height_mobile > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg {
                        height: <?php echo $row->settings->separator_height_mobile; ?>px;
                    }
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains.pp-row-separator-top,
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers.pp-row-separator-top {
						height: <?php echo $row->settings->separator_height_mobile; ?>px;
					}
                <?php } ?>
                <?php if ( 'yes' == $row->settings->separator_mobile_bottom && $row->settings->separator_height_mobile_bottom > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg {
                        height: <?php echo $row->settings->separator_height_mobile_bottom; ?>px;
                    }
					.fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-mountains.pp-row-separator-bottom,
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator.pp-separator-curve_layers.pp-row-separator-bottom {
						height: <?php echo $row->settings->separator_height_mobile_bottom; ?>px;
					}
                <?php } ?>
            }
        <?php } ?>

        <?php
        $css .= ob_get_clean();
    }

    return $css;
}

function pp_row_expandable_css( $css, $nodes, $global_settings ) {
    foreach ( $nodes['rows'] as $row ) {
        ob_start();
        ?>

        <?php if ( $row->settings->enable_expandable == 'yes' ) { ?>
            <?php if ( ! FLBuilderModel::is_builder_active() ) { ?>
            .fl-node-<?php echo $row->node; ?> .fl-row-content-wrap {
                <?php if ( 'collapsed' == $row->settings->er_default_state ) { ?>
                display: none;
                <?php } ?>
            }
            <?php } ?>
            .fl-node-<?php echo $row->node; ?> .pp-er {
                width: 100%;
            }
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-wrap {
                width: 100%;
                <?php echo $row->settings->er_bg_color ? 'background-color: ' . pp_hex2rgba(pp_get_color_value($row->settings->er_bg_color), $row->settings->er_bg_opacity) : ''; ?>;
                padding-top: <?php echo $row->settings->er_title_padding['top']; ?>px;
                padding-bottom: <?php echo $row->settings->er_title_padding['bottom']; ?>px;
                cursor: pointer;
                -webkit-user-select: none;
            }
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-title-wrap {
                text-align: center;
                display: <?php echo $row->settings->er_arrow_pos != 'bottom' ? 'table' : 'block'; ?>;
                width: auto;
                margin: 0 auto;
            }
            <?php if ( $row->settings->er_arrow_pos != 'bottom' ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-title-wrap:before {
                content: "";
                display: inline-block;
                vertical-align: middle;
                height: 100%;
            }
            <?php } ?>
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-title {
                display: <?php echo $row->settings->er_arrow_pos == 'bottom' ? 'block' : 'inline-block'; ?>;
				color: inherit;
				<?php if ( isset( $row->settings->er_title_color_normal ) && ! empty( $row->settings->er_title_color_normal ) ) { ?>
					color: <?php echo pp_get_color_value( $row->settings->er_title_color_normal ); ?>;
				<?php } ?>
                <?php if( $row->settings->er_title_font['family'] != 'Default' ) {
                    FLBuilderFonts::font_css( $row->settings->er_title_font );
                } ?>
                <?php echo is_numeric($row->settings->er_title_font_size) ? 'font-size: ' . $row->settings->er_title_font_size . 'px;' : ''; ?>
                margin-bottom: <?php echo $row->settings->er_arrow_pos == 'bottom' ? $row->settings->er_title_margin['bottom'] : 0; ?>px;
                margin-right: <?php echo $row->settings->er_arrow_pos != 'bottom' ? $row->settings->er_title_margin['right'] : 0; ?>px;
                text-transform: <?php echo $row->settings->er_title_case; ?>;
                vertical-align: middle;
            }
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-arrow {
				display: <?php echo $row->settings->er_arrow_pos == 'bottom' ? 'block' : 'table-cell'; ?>;
				color: inherit;
				<?php if ( isset( $row->settings->er_arrow_color_normal ) && ! empty( $row->settings->er_arrow_color_normal ) ) { ?>
					color: <?php echo pp_get_color_value( $row->settings->er_arrow_color_normal ); ?>;
				<?php } else if ( isset( $row->settings->er_title_color_normal ) && ! empty( $row->settings->er_title_color_normal ) ) { ?>
					color: <?php echo pp_get_color_value( $row->settings->er_title_color_normal ); ?>;
				<?php } ?>
                <?php echo is_numeric($row->settings->er_arrow_size) ? 'font-size: ' . $row->settings->er_arrow_size . 'px;' : ''; ?>
                vertical-align: middle;
            }
			<?php
			FLBuilderCSS::border_field_rule( array(
				'settings' => $row->settings,
				'setting_name' => 'er_arrow_border_group',
				'selector' => ".fl-node-{$row->node} .pp-er .pp-er-arrow:before"
			) );

			FLBuilderCSS::dimension_field_rule( array(
				'settings'		=> $row->settings,
				'setting_name'	=> 'er_arrow_padding',
				'selector'		=> ".fl-node-{$row->node} .pp-er .pp-er-arrow:before",
				'unit'			=> 'px',
				'props'			=> array(
					'padding-top'    => 'er_arrow_padding_top',
					'padding-right'	 => 'er_arrow_padding_right',
					'padding-bottom' => 'er_arrow_padding_bottom',
					'padding-left'   => 'er_arrow_padding_left',
				)
			) );
			?>
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-arrow:before {
				display: inline-block;
				<?php if ( isset( $row->settings->er_arrow_bg_normal ) && ! empty( $row->settings->er_arrow_bg_normal ) ) { ?>
					background-color: <?php echo pp_get_color_value( $row->settings->er_arrow_bg_normal ); ?>;
				<?php } ?>
            }
            .fl-node-<?php echo $row->node; ?> .pp-er-open .pp-er-arrow:before {
                <?php if ( $row->settings->er_arrow_weight == 'bold' ) { ?>
                content: "\f077";
                <?php } else { ?>
                content: "\f106";
                <?php } ?>
            }
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-wrap:hover .pp-er-title {
				<?php if ( isset( $row->settings->er_title_color_hover ) && ! empty( $row->settings->er_title_color_hover ) ) { ?>
					color: <?php echo pp_get_color_value( $row->settings->er_title_color_hover ); ?>;
				<?php } ?>
            }
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-wrap:hover .pp-er-arrow {
				<?php if ( isset( $row->settings->er_arrow_color_hover ) && ! empty( $row->settings->er_arrow_color_hover ) ) { ?>
					color: <?php echo pp_get_color_value( $row->settings->er_arrow_color_hover ); ?>;
				<?php } else if ( isset( $row->settings->er_title_color_hover ) && ! empty( $row->settings->er_title_color_hover ) ) { ?>
					color: <?php echo pp_get_color_value( $row->settings->er_title_color_hover ); ?>;
				<?php } ?>
            }
            .fl-node-<?php echo $row->node; ?> .pp-er .pp-er-wrap:hover .pp-er-arrow:before {
				<?php if ( isset( $row->settings->er_arrow_bg_hover ) && ! empty( $row->settings->er_arrow_bg_hover ) ) { ?>
					background-color: <?php echo pp_get_color_value( $row->settings->er_arrow_bg_hover ); ?>;
				<?php } ?>
                <?php if ( isset( $row->settings->er_arrow_border_hover ) ) { ?>
					border-color: <?php echo pp_get_color_value( $row->settings->er_arrow_border_hover ); ?>;
				<?php } ?>
            }
        <?php } ?>

        <?php
        $css .= ob_get_clean();
    }

    return $css;
}

function pp_row_downarrow_css( $css, $nodes, $global_settings ) {
    foreach ( $nodes['rows'] as $row ) {
        ob_start();
        ?>

        <?php if ( $row->settings->enable_down_arrow == 'yes' ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-down-arrow-container {
                margin-top: <?php echo $row->settings->da_arrow_margin['top']; ?>px;
            }
            .fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap {
                text-align: center;
                position: absolute;
                width: 100%;
                left: 0;
                bottom: <?php echo $row->settings->da_arrow_margin['bottom']; ?>px;
                z-index: 1;
            }

			<?php if ( ! isset( $row->settings->da_icon_style ) || 'style-1' == $row->settings->da_icon_style ) { ?>
				<?php
				FLBuilderCSS::border_field_rule( array(
					'settings' => $row->settings,
					'setting_name' => 'da_arrow_border_group',
					'selector' => ".fl-node-{$row->node} .pp-down-arrow-wrap .pp-down-arrow"
				) );
				?>
            .fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-arrow {
                display: inline-block;
				<?php if ( isset( $row->settings->da_arrow_bg_normal ) ) { ?>
                background-color: <?php echo pp_get_color_value( $row->settings->da_arrow_bg_normal ); ?>;
				<?php } ?>
                line-height: 0;
                cursor: pointer;
                padding: <?php echo $row->settings->da_arrow_padding; ?>px;
            }
            .fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-arrow:hover {
                <?php if ( isset( $row->settings->da_arrow_bg_hover ) ) { ?>
                background-color: <?php echo pp_get_color_value( $row->settings->da_arrow_bg_hover ); ?>;
				<?php } ?>
				<?php if ( isset( $row->settings->da_arrow_border_hover ) ) { ?>
                border-color: <?php echo pp_get_color_value( $row->settings->da_arrow_border_hover ); ?>;
				<?php } ?>
            }
            .fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-arrow.pp-da-bounce {
                -moz-animation: bounce 2s infinite;
                -webkit-animation: bounce 2s infinite;
                animation: bounce 2s infinite;
            }
            .fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-arrow svg {
                width: 45px;
	            height: 45px;
            }
            .fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-arrow svg path {
				<?php if ( isset( $row->settings->da_arrow_color_normal ) ) { ?>
                stroke: <?php echo pp_get_color_value( $row->settings->da_arrow_color_normal ); ?>;
	            fill: <?php echo pp_get_color_value( $row->settings->da_arrow_color_normal ); ?>;
				<?php } ?>
	            stroke-width: <?php echo 'bold' == $row->settings->da_arrow_weight ? 2 : 0; ?>px;
            }
            .fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-arrow:hover svg path {
				<?php if ( isset( $row->settings->da_arrow_color_hover ) ) { ?>
                stroke: <?php echo pp_get_color_value( $row->settings->da_arrow_color_hover ); ?>;
	            fill: <?php echo pp_get_color_value( $row->settings->da_arrow_color_hover ); ?>;
				<?php } ?>
            }
			<?php } ?>

			<?php if ( isset( $row->settings->da_icon_style ) && 'style-2' == $row->settings->da_icon_style ) { ?>
			.fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-icon-scroll,
			.fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-icon-scroll:before {
				position: absolute;
				left: 50%;
			}

			.fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-icon-scroll {
				width: 40px;
				height: 70px;
				cursor: pointer;
				<?php if ( isset( $row->settings->da_arrow_bg_normal ) ) { ?>
                background-color: <?php echo pp_get_color_value( $row->settings->da_arrow_bg_normal ); ?>;
				<?php } ?>
			}

			<?php
			FLBuilderCSS::border_field_rule( array(
				'settings' => $row->settings,
				'setting_name' => 'da_arrow_border_group',
				'selector' => ".fl-node-{$row->node} .pp-down-arrow-wrap .pp-down-icon-scroll"
			) );
			?>

			.fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-icon-scroll:hover {
				<?php if ( isset( $row->settings->da_arrow_bg_hover ) ) { ?>
                background-color: <?php echo pp_get_color_value( $row->settings->da_arrow_bg_hover ); ?>;
				<?php } ?>
				<?php if ( isset( $row->settings->da_arrow_border_hover ) ) { ?>
                border-color: <?php echo pp_get_color_value( $row->settings->da_arrow_border_hover ); ?>;
				<?php } ?>
			}

			.fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-icon-scroll:before {
				content: '';
				width: 8px;
				height: 8px;
				<?php if ( isset( $row->settings->da_arrow_color_normal ) ) { ?>
				background: <?php echo pp_get_color_value( $row->settings->da_arrow_color_normal ); ?>;
				<?php } ?>
				margin-left: -4px;
				top: 8px;
				border-radius: 4px;
				animation-duration: 1.5s;
				animation-iteration-count: infinite;
				animation-name: pp-arrow-scroll;
			}

			.fl-node-<?php echo $row->node; ?> .pp-down-arrow-wrap .pp-down-icon-scroll:hover:before {
				<?php if ( isset( $row->settings->da_arrow_color_hover ) ) { ?>
				background: <?php echo pp_get_color_value( $row->settings->da_arrow_color_hover ); ?>;
				<?php } ?>
			}

			@-webkit-keyframes pp-arrow-scroll {
			0% {
				opacity: 1;
			}
			100% {
				opacity: 0;
				-webkit-transform: translateY(46px);
						transform: translateY(46px);
			}
			}

			@keyframes pp-arrow-scroll {
				0% {
					opacity: 1;
				}
				100% {
					opacity: 0;
					-webkit-transform: translateY(46px);
						transform: translateY(46px);
				}
			}
			<?php } ?>

            @media only screen and (max-width: 767px) {
                .fl-node-<?php echo $row->node; ?> .pp-down-arrow-container {
                    <?php if ( $row->settings->da_hide_mobile == 'yes' ) : ?>
                        display: none;
                    <?php endif; ?>
                }
            }

            @-moz-keyframes pp-da-bounce {
              0%, 20%, 50%, 80%, 100% {
                -moz-transform: translateY(0);
                transform: translateY(0);
              }
              40% {
                -moz-transform: translateY(-30px);
                transform: translateY(-30px);
              }
              60% {
                -moz-transform: translateY(-15px);
                transform: translateY(-15px);
              }
            }
            @-webkit-keyframes pp-da-bounce {
              0%, 20%, 50%, 80%, 100% {
                -webkit-transform: translateY(0);
                transform: translateY(0);
              }
              40% {
                -webkit-transform: translateY(-30px);
                transform: translateY(-30px);
              }
              60% {
                -webkit-transform: translateY(-15px);
                transform: translateY(-15px);
              }
            }
            @keyframes pp-da-bounce {
              0%, 20%, 50%, 80%, 100% {
                -moz-transform: translateY(0);
                -ms-transform: translateY(0);
                -webkit-transform: translateY(0);
                transform: translateY(0);
              }
              40% {
                -moz-transform: translateY(-30px);
                -ms-transform: translateY(-30px);
                -webkit-transform: translateY(-30px);
                transform: translateY(-30px);
              }
              60% {
                -moz-transform: translateY(-15px);
                -ms-transform: translateY(-15px);
                -webkit-transform: translateY(-15px);
                transform: translateY(-15px);
              }
            }
        <?php } ?>

        <?php
        $css .= ob_get_clean();
    }

    return $css;
}

function pp_row_infinite_bg_css( $css, $nodes, $global_settings ) {

	foreach ( $nodes['rows'] as $row ) {
		ob_start();
		?>

		<?php if ( isset( $row->settings->bg_type ) && 'pp_infinite_bg' == $row->settings->bg_type ) { ?>
			<?php if ( isset( $row->settings->pp_bg_image ) ){ ?>
				.fl-node-<?php echo $row->node; ?>.fl-row-bg-pp_infinite_bg .fl-row-content-wrap .fl-builder-shape-layer {
					z-index: 1;
				}
				.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap {
					background-color: transparent;
					background-image: url(<?php echo $row->settings->pp_bg_image_src;?>);
					background-position: 0 0;
				}

				<?php if ( isset( $global_settings->large_breakpoint ) && isset( $row->settings->pp_bg_image_large_src ) && ! empty( $row->settings->pp_bg_image_large_src ) ) { ?>
					@media only screen and (max-width: <?php echo $global_settings->large_breakpoint; ?>px) {
						.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap {
							background-image: url(<?php echo $row->settings->pp_bg_image_large_src; ?>);
						}
					}
				<?php } ?>
				<?php if ( isset( $row->settings->pp_bg_image_medium_src ) && ! empty( $row->settings->pp_bg_image_medium_src ) ) { ?>
					@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
						.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap {
							background-image: url(<?php echo $row->settings->pp_bg_image_medium_src; ?>);
						}
					}
				<?php } ?>
				<?php if ( isset( $row->settings->pp_bg_image_responsive_src ) && ! empty( $row->settings->pp_bg_image_responsive_src ) ) { ?>
					@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
						.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap {
							background-image: url(<?php echo $row->settings->pp_bg_image_responsive_src; ?>);
						}
					}
				<?php } ?>

				<?php if ( isset($row->settings->scrolling_direction ) && 'horizontal' == $row->settings->scrolling_direction ) { ?>
					.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap {
						animation: pp-animation-horizontally-<?php echo $row->settings->scrolling_direction_h; ?>-<?php echo $row->node; ?> <?php echo $row->settings->scrolling_speed; ?>s linear infinite;
						background-size: cover;
						background-repeat: repeat-x;
					}
				<?php } elseif ( isset($row->settings->scrolling_direction ) && 'vertical' == $row->settings->scrolling_direction ) { ?>
					.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap {
						animation: pp-animation-vertically-<?php echo $row->settings->scrolling_direction_v; ?>-<?php echo $row->node; ?> <?php echo $row->settings->scrolling_speed; ?>s linear infinite;
						background-size: cover;
						background-repeat: repeat-y;
					}
				<?php } ?>
				<?php if( isset($row->settings->pp_infinite_overlay) ) { ?>
					.fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap:after {
						background-color: <?php echo isset($row->settings->pp_infinite_overlay) ? pp_get_color_value($row->settings->pp_infinite_overlay) : 'transparent'; ?>;
						border-radius: inherit;
						content: '';
						display: block;
						position: absolute;
						top: 0;
						right: 0;
						bottom: 0;
						left: 0;
						z-index: 0;
					}
					.fl-node-<?php echo $row->node; ?> > .fl-row-content-wrap .fl-row-content {
						position: relative;
						z-index: 2;
					}
				<?php } ?>
			<?php } ?>
        <?php } ?>

        <?php
        $css .= ob_get_clean();
    }

    return $css;
}

function pp_row_animated_bg_css( $css, $nodes, $global_settings ) {

	foreach ( $nodes['rows'] as $row ) {
		ob_start();
		?>

		<?php if ( isset( $row->settings->bg_type ) && 'pp_animated_bg' == $row->settings->bg_type ) { ?>
			<?php if ( isset( $row->settings->pp_bg_image ) ){ ?>
				.fl-node-<?php echo $row->node; ?>.fl-row-bg-pp_animated_bg .fl-row-content-wrap .fl-builder-shape-layer {
					z-index: 1;
				}
			<?php }
			$anim_type = $row->settings->animation_type;

			if ( 'particles' == $anim_type || 'nasa' == $anim_type || 'bubble' == $anim_type || 'snow' == $anim_type || 'custom' == $anim_type ) { ?>
				.fl-node-<?php echo $row->node; ?> .pp-particles-wrap {
					position: absolute;
					top: 0;
					bottom: 0;
					left: 0;
					right: 0;
				}
				.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap {
					background-color: <?php echo isset( $row->settings->part_bg_color ) ? pp_get_color_value($row->settings->part_bg_color) : '#07192f'; ?>
				}
				<?php if ( 'yes' == $row->settings->part_bg_type ) { ?>
					.fl-node-<?php echo $row->node; ?> .pp-particles-wrap {
						background-image: url(<?php echo isset( $row->settings->part_bg_image ) ? $row->settings->part_bg_image_src : ''; ?>);
						background-size: <?php echo ! empty( $row->settings->part_bg_size ) ? $row->settings->part_bg_size . '%' : 'cover'; ?>;
						background-repeat: no-repeat;
						background-position: <?php echo isset( $row->settings->part_bg_position ) ? $row->settings->part_bg_position : '50% 50%'; ?>;
					}
					<?php if ( isset( $global_settings->large_breakpoint ) && isset( $row->settings->part_bg_image_large_src ) && ! empty( $row->settings->part_bg_image_large_src ) ) { ?>
					@media only screen and (max-width: <?php echo $global_settings->large_breakpoint; ?>px) {
						.fl-node-<?php echo $row->node; ?> .pp-particles-wrap {
							background-image: url(<?php echo $row->settings->part_bg_image_large_src; ?>);
						}
					}
					<?php } ?>
					<?php if ( isset( $row->settings->part_bg_image_medium_src ) && ! empty( $row->settings->part_bg_image_medium_src ) ) { ?>
						@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
							.fl-node-<?php echo $row->node; ?> .pp-particles-wrap {
								background-image: url(<?php echo $row->settings->part_bg_image_medium_src; ?>);
							}
						}
					<?php } ?>
					<?php if ( isset( $row->settings->part_bg_image_responsive_src ) && ! empty( $row->settings->part_bg_image_responsive_src ) ) { ?>
						@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
							.fl-node-<?php echo $row->node; ?> .pp-particles-wrap {
								background-image: url(<?php echo $row->settings->part_bg_image_responsive_src; ?>);
							}
						}
					<?php } ?>
				<?php } ?>
				.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap .pp-particles-wrap {
					z-index: 0;
				}
				.fl-node-<?php echo $row->node; ?> .fl-row-content-wrap .fl-row-content {
					position: relative;
					z-index: 1;
				}

			<?php }else{ ?>
				#fl-builder-settings-section-pp_animated_bg #fl-field-part_hover_size,
				#fl-builder-settings-section-pp_animated_bg #fl-field-part_bg_image,
				#fl-builder-settings-section-pp_animated_bg #fl-field-part_bg_size,
				#fl-builder-settings-section-pp_animated_bg #fl-field-part_hover_size,
				#fl-builder-settings-section-pp_animated_bg #fl-field-part_bg_position {
					display: none !important;
				}
			<?php } ?>

        <?php } ?>

        <?php
        $css .= ob_get_clean();
    }

    return $css;
}