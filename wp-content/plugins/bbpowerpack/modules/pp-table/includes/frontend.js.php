(function($) {

	new PPTable({
		id: '<?php echo $id; ?>',
		mode: '<?php echo $settings->scrollable; ?>',
		breakpoint: <?php echo ! empty( $settings->custom_breakpoint ) ? intval( $settings->custom_breakpoint ) : '0'; ?>
	});

	// Disable stacking.
	// $(".fl-node-<?php echo $id; ?> table.pp-table-content").data('tablesaw-stack').destroy();

})(jQuery);
