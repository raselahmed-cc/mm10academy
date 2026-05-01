<?php

function pp_column_render_js( $extensions ) {

    if ( array_key_exists( 'separators', $extensions['col'] ) || in_array( 'separators', $extensions['col'] ) ) {
        add_filter( 'fl_builder_render_js', 'pp_column_separators_js', 10, 3 );
    }
}

function pp_column_separators_js( $js, $nodes, $global_settings ) {
    ob_start();
    foreach ( $nodes['columns'] as $column ) {
        if ( isset( $column->settings->enable_separator ) && 'yes' == $column->settings->enable_separator ) {
            ?>
            ;(function($) {
				var previewMode = 'default';
				$('.fl-node-<?php echo $column->node; ?> .pp-col-separator').prependTo( $('.fl-node-<?php echo $column->node; ?>' ) );
                function setStructure() {
					<?php $pos = $med_pos = $column->settings->separator_position; ?>
					if ( $('.fl-node-<?php echo $column->node; ?> .pp-col-separator').hasClass( 'responsive-enabled' ) ) {
						$('.fl-node-<?php echo $column->node; ?> .pp-col-separator').removeAttr( 'class' ).addClass( 'pp-col-separator pp-col-separator-<?php echo $pos; ?>' );
					}
					<?php if ( isset( $column->settings->separator_position_medium ) && ! empty( $column->settings->separator_position_medium ) ) {
						$med_pos = $column->settings->separator_position_medium;
						?>
						if ( ( window.innerWidth > <?php echo $global_settings->responsive_breakpoint; ?> && window.innerWidth <= <?php echo $global_settings->medium_breakpoint; ?> ) || 'medium' === previewMode ) {
							$('.fl-node-<?php echo $column->node; ?> .pp-col-separator').removeClass( 'pp-col-separator-<?php echo $pos; ?>' ).addClass( 'pp-col-separator-<?php echo $med_pos; ?> responsive-enabled' );
						}
					<?php } ?>
					<?php if ( isset( $column->settings->separator_position_responsive ) && ! empty( $column->settings->separator_position_responsive ) ) {
						$resp_pos = $column->settings->separator_position_responsive;
						?>
						if ( window.innerWidth <= <?php echo $global_settings->responsive_breakpoint; ?> || 'responsive' === previewMode ) {
							$('.fl-node-<?php echo $column->node; ?> .pp-col-separator').removeClass( 'pp-col-separator-<?php echo $pos; ?> pp-col-separator-<?php echo $med_pos; ?>' ).addClass( 'pp-col-separator-<?php echo $resp_pos; ?> responsive-enabled' );
						}
					<?php } ?>
                    var colH_<?php echo $column->node; ?> = $('.fl-node-<?php echo $column->node; ?>').outerHeight();
                    $('.fl-node-<?php echo $column->node; ?> .pp-col-separator-left svg, .fl-node-<?php echo $column->node; ?> .pp-col-separator-right svg').css('width', colH_<?php echo $column->node; ?> + 'px');
                    $('.fl-node-<?php echo $column->node; ?> .pp-col-separator-top svg, .fl-node-<?php echo $column->node; ?> .pp-col-separator-bottom svg').css('width', '100%');
				}
                $(window).on('load resize', setStructure);
				<?php if ( isset( $_GET['fl_builder'] ) ) { ?>
                FLBuilder.addHook( 'responsive-editing-switched', function(e, mode) {
					previewMode = mode;
					setStructure();
				} );
				<?php } ?>
            })(jQuery);
            <?php
        }
    }
    $js .= ob_get_clean();

    return $js;
}
