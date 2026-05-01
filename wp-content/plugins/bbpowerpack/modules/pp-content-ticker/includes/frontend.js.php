var pp_ticker_<?php echo $id; ?>;
<?php
if ( 'yes' === $settings->autoplay ) {
	$autoplay_speed = ( '' !== $settings->autoplay_speed ) ? $settings->autoplay_speed : 2000;
} else {
	$autoplay_speed = 9999999;
}
?>
;(function($) {
	pp_ticker_<?php echo $id; ?> = new PPContentTicker( {
		id: '<?php echo $id; ?>',
		loop: <?php echo 'yes' === $settings->infinite_loop ? 'true' : 'false'; ?>,
		effect: '<?php echo $settings->ticker_effect; ?>',
		speed: <?php echo '' !== $settings->slide_speed ? $settings->slide_speed : 1000; ?>,
		grabCursor: <?php echo 'yes' === $settings->grab_cursor ? 'true' : 'false'; ?>,
		direction: '<?php echo $settings->slide_direction; ?>',
		autoplay: {
			delay: <?php echo $autoplay_speed; ?>,
			disableOnInteraction: <?php echo 'yes' === $settings->pause_interaction ? 'true' : 'false'; ?>
		},
	} );
})(jQuery);
