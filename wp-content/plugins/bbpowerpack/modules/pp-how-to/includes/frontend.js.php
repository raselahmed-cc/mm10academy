;(function($) {
	<?php if ( isset( $settings->enable_lightbox ) && $settings->enable_lightbox == 'yes' ) { ?>
	$(function() {
		if ( $.fn.magnificPopup ) {
			$('.fl-node-<?php echo $id; ?> .pp-how-to-step-image a').magnificPopup({
				type: 'image',
				closeOnContentClick: true,
				closeBtnInside: false
			});
		}
	});
	<?php } ?>
})(jQuery);