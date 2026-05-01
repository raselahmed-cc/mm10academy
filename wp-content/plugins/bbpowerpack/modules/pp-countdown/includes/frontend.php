<div class="pp-countdown-wrapper">
	<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-countdown-title"><?php echo $settings->title; ?></<?php echo esc_attr( $settings->title_tag ); ?>>
	<div id="countdown-<?php echo $module->node; ?>" class="pp-countdown pp-countdown-<?php echo esc_attr( $settings->timer_type ); ?>-timer<?php if ( 'yes' == $settings->show_separator && isset( $settings->separator_type ) ) { echo ' pp-countdown-separator-' . esc_attr( $settings->separator_type ); } ?>"></div>
</div>