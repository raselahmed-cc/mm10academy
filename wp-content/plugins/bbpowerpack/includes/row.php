<?php

function pp_row_settings_init() {

    require_once BB_POWERPACK_DIR . 'includes/row-settings.php';
    require_once BB_POWERPACK_DIR . 'includes/row-css.php';
    require_once BB_POWERPACK_DIR . 'includes/row-js.php';

    $extensions = BB_PowerPack_Admin_Settings::get_enabled_extensions();

    pp_row_register_settings( $extensions );
    pp_row_render_css( $extensions );

    if ( array_key_exists( 'separators', $extensions['row'] ) || in_array( 'separators', $extensions['row'] ) ) {
       // add_action( 'fl_builder_before_render_row', 'pp_before_render_row' );
        add_action( 'fl_builder_before_render_row_bg', 'pp_output_before_row_bg' );
    }

    if ( array_key_exists( 'downarrow', $extensions['row'] ) || in_array( 'downarrow', $extensions['row'] ) ) {
        add_action( 'fl_builder_after_render_row_bg', 'pp_output_after_render_row_bg', 100 );
    }

    pp_row_render_js( $extensions );

	// Specify min-width to rows when fixed width is enabled.
	add_filter( 'fl_builder_render_css', 'pp_row_extra_css', 10, 3 );
}

function pp_row_extra_css( $css, $nodes, $global_settings ) {
	foreach ( $nodes['rows'] as $row ) {
        ob_start();
        ?>

        <?php if ( $row->settings->content_width === 'fixed' ) { ?>
			.fl-node-<?php echo $row->node; ?> .fl-row-content {
				min-width: 0px;
			}
		<?php }

		$css .= ob_get_clean();
	}

	return $css;
}

function pp_row_separator_html( $type, $position, $color, $height, $shadow ) {
    ob_start();
    ?>
    <div class="pp-row-separator pp-row-separator-<?php echo $position; ?> pp-separator-<?php echo $type; ?>" style="color: <?php echo $color; ?>">
        <?php
		switch( $type ) :
			case 'triangle':
			case 'triangle_shadow':
			case 'triangle_left':
			case 'triangle_right':
			case 'triangle_small':
			case 'tilt_left':
			case 'tilt_right':
			case 'curve':
			case 'twin_curves':
			case 'curve_layers':
			case 'wave':
			case 'cloud':
			case 'slit':
			case 'water':
			case 'mountains':
                include BB_POWERPACK_DIR . 'includes/shapes/' . $type . '.svg.php';
				break;
            case 'box':
                echo '<div class="pp-box"></div>';
            	break;
            case 'pyramid':
                echo '<div class="pp-pyramid"></div>';
            	break;
            case 'zigzag':
                echo '<div class="pp-zigzag"></div>';
            	break;
            default:
				break;
        endswitch;
		?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Fallback for row position.
 */
function pp_before_render_row( $row ) {
    if ( isset( $row->settings->enable_separator ) && 'no' == $row->settings->enable_separator && 'none' != $row->settings->separator_type ) {
        $row_settings = FLBuilderModel::get_node_settings( $row );
        $row_settings->separator_type = 'none';
    }
    if ( isset( $row->settings->enable_separator ) && 'yes' == $row->settings->enable_separator &&  'none' != $row->settings->separator_type ) {
        if ( 'bottom' == $row->settings->separator_position ) {

            // Get row settings.
            $row_settings = FLBuilderModel::get_node_settings( $row );
            $template_post_id 	= FLBuilderModel::is_node_global( $row );

            // Add top separator setting to bottom.
            $row_settings->separator_type_bottom            = $row_settings->separator_type;
            $row_settings->separator_type                   = 'none';
            $row_settings->separator_color_bottom           = $row_settings->separator_color;
            $row_settings->separator_shadow_bottom          = $row_settings->separator_shadow;
            $row_settings->separator_height_bottom          = $row_settings->separator_height;
            $row_settings->separator_tablet_bottom          = $row_settings->separator_tablet;
            $row_settings->separator_tablet                 = 'no';
            $row_settings->separator_height_tablet_bottom   = $row_settings->separator_height_tablet;
            $row_settings->separator_height_tablet          = '';
            $row_settings->separator_mobile_bottom          = $row_settings->separator_mobile;
            $row_settings->separator_mobile                 = 'no';
            $row_settings->separator_height_mobile_bottom   = $row_settings->separator_height_mobile;
            $row_settings->separator_height_mobile          = '';
            $row_settings->separator_position               = 'top';

            // Get layout data.
            $data = FLBuilderModel::get_layout_data();
            // Replace row settings with new settings.
    		$data[$row->node]->settings = $row_settings;
    		// Update the layout data.
    		FLBuilderModel::update_layout_data($data);

            // Save settings for global rows.
            if ( $template_post_id && ! FLBuilderModel::is_post_node_template() ) {

    			// Get the template data.
    			$template_data = FLBuilderModel::get_layout_data( 'published', $template_post_id );

    			// Update the template node settings.
    			$template_data[ $row->template_node_id ]->settings = $row_settings;

    			// Save the template data.
    			FLBuilderModel::update_layout_data( $template_data, 'published', $template_post_id );
    			FLBuilderModel::update_layout_data( $template_data, 'draft', $template_post_id );

    			// Delete the template asset cache.
    			FLBuilderModel::delete_all_asset_cache( $template_post_id );
    			FLBuilderModel::delete_node_template_asset_cache( $template_post_id );
    		}
        }
    }
}

/**
 * Output for Rows
 */
function pp_output_before_row_bg( $row ) {
	if ( 'pp_animated_bg' === $row->settings->bg_type &&  isset( $row->settings->animation_type ) ) {
		$anim_type = $row->settings->animation_type;
		if ( 'particles' == $anim_type || 'nasa' == $anim_type || 'bubble' == $anim_type || 'snow' == $anim_type || 'custom' == $anim_type ) {
			echo '<div id="pp-particles-wrap-' . $row->node .'" class="pp-particles-wrap"></div>';
		}
	}

    if ( 'yes' == $row->settings->enable_separator && 'none' != $row->settings->separator_type ) {
        $type       = $row->settings->separator_type;
        $position   = 'top';
        $color      = pp_get_color_value( $row->settings->separator_color );
        $height     = $row->settings->separator_height;
        $shadow     = 'triangle_shadow' == $type ? $row->settings->separator_shadow : '';
        echo pp_row_separator_html( $type, $position, $color, $height, $shadow );
    }

    if ( 'yes' == $row->settings->enable_separator && isset( $row->settings->separator_type_bottom ) && 'none' != $row->settings->separator_type_bottom ) {
        $type       = $row->settings->separator_type_bottom;
        $position   = 'bottom';
        $color      = pp_get_color_value( $row->settings->separator_color_bottom );
        $height     = $row->settings->separator_height_bottom;
        $shadow     = 'triangle_shadow' == $type ? $row->settings->separator_shadow_bottom : '';
        echo pp_row_separator_html( $type, $position, $color, $height, $shadow );
    }
}

/**
 * Output for Down Arrow.
 */
function pp_output_after_render_row_bg( $row ) {
	if ( is_object($row) && isset($row->settings->enable_down_arrow) && 'yes' == $row->settings->enable_down_arrow ) {
		?>
		<div class="pp-down-arrow-container">
			<div class="pp-down-arrow-wrap">
				<?php if ( ! isset( $row->settings->da_icon_style ) || 'style-1' == $row->settings->da_icon_style ) { ?>
				<div class="pp-down-arrow<?php echo ( $row->settings->da_animation == 'yes' ) ? ' pp-da-bounce' : ''; ?>" data-row-id="<?php echo $row->node; ?>" data-top-offset="<?php echo $row->settings->da_top_offset; ?>" data-transition-speed="<?php echo $row->settings->da_transition_speed; ?>">
					<?php
					echo apply_filters( 'pp_down_arrow_html', '<svg xmlns="http://www.w3.org/2000/svg" role="presentation"><path stroke="null" d="m1.00122,14.45485c0,-0.24438 0.10878,-0.48877 0.32411,-0.67587c0.4329,-0.37231 1.13663,-0.37231 1.56952,0l19.19382,16.50735l19.19381,-16.50735c0.4329,-0.37231 1.13663,-0.37231 1.56952,0s0.43289,0.97753 0,1.34983l-19.97969,17.18324c-0.43289,0.3723 -1.13662,0.3723 -1.56951,0l-19.97969,-17.18324c-0.21755,-0.1871 -0.32411,-0.43149 -0.32411,-0.67587l0.00222,0.00191z" fill="#000000" id="svg_1"/></svg>', $row->settings );
					?>
				</div>
				<?php } else { ?>
				<div class='pp-down-arrow pp-down-icon-scroll' data-row-id="<?php echo $row->node; ?>" data-top-offset="<?php echo $row->settings->da_top_offset; ?>" data-transition-speed="<?php echo $row->settings->da_transition_speed; ?>"></div>
				<?php } ?>
			</div>
		</div>
		<?php
	}
}

pp_row_settings_init();
