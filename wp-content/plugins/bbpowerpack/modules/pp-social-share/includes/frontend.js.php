;(function($) {
	$('.fl-node-<?php echo $id; ?> .pp-social-share-content .pp-share-button:not(.pp-share-button-print):not(.pp-share-button-email) .pp-share-button-link').on( 'click', function (e) {
		e.preventDefault();
		var href = $(this).attr('href');
		if ( window.innerWidth <= 1024 && $(this).parent().hasClass('pp-share-button-fb-messenger') ) {
			href = href.replace( 'https://www.facebook.com/dialog/send', 'fb-messenger://share/' );
		}
		window.open(href, '', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=1');
		return false;
	});
})(jQuery);
