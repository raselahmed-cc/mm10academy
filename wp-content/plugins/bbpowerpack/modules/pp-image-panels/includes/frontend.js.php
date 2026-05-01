;(function($) {
	var ppImagePanels = new PPImagePanels({
		id: '<?php echo $id; ?>',
		expandWidth: <?php echo ( isset( $settings->expand_width ) && ! empty( $settings->expand_width ) ) ? $settings->expand_width : '0'; ?>,
		expandPanel: <?php echo ( isset( $settings->expand_panel ) && ! empty( $settings->expand_panel ) ) ? absint( $settings->expand_panel ) : '-1'; ?>,
	});

	// Store the instance in the element's data.
	$('.fl-node-<?php echo $id; ?> .pp-image-panels-wrap').data('ppImagePanels', ppImagePanels);
})(jQuery);