;(function($) {
	$(function() {
		new PPSlidingMenus({
			id: '<?php echo $id; ?>',
			linknav: '<?php echo $settings->link_navigation; ?>',
			backicon: '<?php echo apply_filters( 'pp_sliding_menus_arrow_left', pp_prev_icon_svg( '', false ) ); ?>',
			backtext: '<?php echo ! empty( $settings->back_text ) ? $settings->back_text : esc_html__( 'Back', 'bb-powerpack' ); ?>',
		});
	});
})(jQuery);
