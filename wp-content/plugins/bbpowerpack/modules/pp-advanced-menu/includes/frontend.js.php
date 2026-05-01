<?php

	// set defaults
	$type 				= isset( $settings->menu_layout ) ? $settings->menu_layout : 'horizontal';
	$mobile 			= isset( $settings->mobile_toggle ) ? $settings->mobile_toggle : 'expanded';
	$mobile_breakpoint 	= isset( $settings->mobile_breakpoint ) ? $settings->mobile_breakpoint : 'mobile';
 ?>

var pp_menu_<?php echo $id; ?>;
(function($) {

    pp_menu_<?php echo $id; ?> = new PPAdvancedMenu({
    	id: '<?php echo $id ?>',
    	type: '<?php echo $type ?>',
		mobile: '<?php echo $mobile ?>',
		menuPosition: '<?php echo isset( $settings->menu_position ) ? $settings->menu_position : 'below'; ?>',
		breakpoints: {
			large: <?php echo empty( $global_settings->large_breakpoint ) ? 1200 : $global_settings->large_breakpoint; ?>,
			medium: <?php echo empty( $global_settings->medium_breakpoint ) ? 992 : $global_settings->medium_breakpoint; ?>,
			small: <?php echo empty( $global_settings->responsive_breakpoint ) ? 768 : $global_settings->responsive_breakpoint; ?>,
			custom: <?php echo empty($settings->custom_breakpoint) ? 0 : $settings->custom_breakpoint; ?>
		},
		mobileBreakpoint: '<?php echo $mobile_breakpoint ?>',
		mediaBreakpoint: '<?php echo $module->get_media_breakpoint(); ?>',
		mobileMenuType: '<?php echo $settings->mobile_menu_type; ?>',
		offCanvasDirection: '<?php echo $settings->offcanvas_direction; ?>',
		fullScreenAnimation: '',
		isBuilderActive: <?php echo ( FLBuilderModel::is_builder_active() ) ? 'true' : 'false'; ?>
    });

})(jQuery);
