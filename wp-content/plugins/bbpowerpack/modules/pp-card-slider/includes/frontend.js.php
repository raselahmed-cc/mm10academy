var pp_card_<?php echo $id; ?>;
<?php
if ( 'yes' === $settings->autoplay ) {
	$autoplay_speed = ( '' !== $settings->autoplay_speed ) ? $settings->autoplay_speed : 2000;
} else {
	$autoplay_speed = 9999999;
}
?>

;(function($) {
	<?php if ( $module->has_lightbox() ) : ?>
	$(function() {
		var selector = $( '.fl-node-<?php echo $id; ?>' );
		if ( selector.length && typeof $.fn.magnificPopup !== 'undefined') {
			selector.magnificPopup({
				delegate: '.pp-card-slider-item:not(.swiper-slide-duplicate) .pp-card-slider-image a, .pp-card-slider-item.swiper-slide-duplicate .pp-card-slider-image a',
				closeBtnInside: false,
				type: 'image',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					tCounter: ''
				},
				mainClass: 'mfp-<?php echo $id; ?>',
				<?php if ( isset( $settings->lightbox_caption ) && 'yes' == $settings->lightbox_caption ) { ?>
				'image': {
					titleSrc: function(item) {
						var caption = item.el.data('caption') || '';
						return caption;
					}
				}
				<?php } ?>
			});
		}
	});
	<?php endif; ?>

	pp_card_<?php echo $id; ?> = new PPCardSlider( {
		id: '<?php echo $id; ?>',
		loop: <?php echo 'yes' === $settings->infinite_loop ? 'true' : 'false'; ?>,
		effect: 'fade',
		speed: <?php echo '' !== $settings->slide_speed ? $settings->slide_speed : 1000; ?>,
		grabCursor: <?php echo 'yes' === $settings->grab_cursor ? 'true' : 'false'; ?>,
		direction: '<?php echo $settings->slide_direction; ?>',
		autoplay: {
			delay: <?php echo $autoplay_speed; ?>,
			disableOnInteraction: <?php echo 'yes' === $settings->pause_interaction ? 'true' : 'false'; ?>
		},
		pagination: {
			el: '.swiper-pagination',
			type: 'bullets',
			clickable: true,
		},
		keyboard: {
			enabled: <?php echo ( 'yes' === $settings->keyboard_nav ) ? 'true' : 'false'; ?>,
			onlyInViewport: false,
		},
		responsive: <?php echo $global_settings->responsive_breakpoint; ?>,
	} );

})(jQuery);
