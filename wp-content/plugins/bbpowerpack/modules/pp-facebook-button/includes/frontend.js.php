;(function($) {
	$(function() {
		new PPFacebookButton({
			id: '<?php echo $id; ?>',
			sdkUrl: '<?php echo pp_get_fb_sdk_url(); ?>'
		});
	});
})(jQuery);
