<?php
$date_format = isset( $settings->date_format ) ? $settings->date_format : '';
?>
<span class="pp-content-grid-date pp-post-date">
	<?php if ( pp_is_tribe_events_post( $post_id ) && function_exists( 'tribe_get_start_date' ) ) { ?>
		<?php echo tribe_get_start_date( null, false, $date_format ); ?>
	<?php } else { ?>
		<?php echo get_the_date( $date_format ); ?>
	<?php } ?>
</span>