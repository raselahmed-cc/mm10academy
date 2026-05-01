var $str = "<?php echo htmlspecialchars( $settings->coupon_code ); ?>";
;(function($) {
	new PPCoupon({
		id: '<?php echo $id; ?>',
		coupon_style: '<?php echo $settings->coupon_style; ?>',
		coupon_code: $str,
	});

})(jQuery);
