;(function($) {
<?php if($settings->link_type == 'lightbox') : ?>
	$('.fl-node-<?php echo $id; ?> .pp-photo-content').magnificPopup({
		delegate: 'a',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
		},
		type: 'image',
		closeOnContentClick: true,
		closeBtnInside: false
	});
<?php endif; ?>
	if ( $('.fl-node-<?php echo $id; ?> .pp-photo-rollover').length > 0 ) {
		$('body').on( 'mouseenter.pp-rollover', '.fl-node-<?php echo $id; ?> .pp-photo-rollover .pp-photo-content', function() {
			$(this).addClass( 'is-hover' );
		} ).on( 'mouseleave.pp-rollover', '.fl-node-<?php echo $id; ?> .pp-photo-rollover .pp-photo-content', function() {
			$(this).removeClass( 'is-hover' );
		} );
	}
})(jQuery);