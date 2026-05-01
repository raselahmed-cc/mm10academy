;(function($) {
	<?php if ( isset( $settings->item_lightbox ) && 'yes' === $settings->item_lightbox ) { ?>
	$(function() {
		if ( $.fn.magnificPopup ) {
			$('.fl-node-<?php echo $id; ?> .pp-restaurant-menu-item-images a').magnificPopup({
				type: 'image',
				closeOnContentClick: true,
				closeBtnInside: false,
				mainClass: 'mfp-<?php echo $id; ?>',
				image: {
					titleSrc: function(item) {
						var caption = item.el.parent().find('.pp-restaurant-menu-item-title').text() || '';
						return caption;
					}
				}
			});
		}
	});
	<?php } ?>
})(jQuery);