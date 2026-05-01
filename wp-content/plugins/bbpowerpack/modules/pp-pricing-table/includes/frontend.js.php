;(function($) {

	new PPPricingTable({
		id: '<?php echo $id; ?>',
		dualPricing: <?php echo ( 'yes' == $settings->dual_pricing ) ? 'true' : 'false'; ?>
	});

	var adjustHeights = function() {
		//var spaceHeight = $('.fl-node-<?php echo $id; ?> .pp-pricing-table .pp-pricing-table-card .pp-pricing-table-price').outerHeight();
		//$(".fl-node-<?php echo $id; ?> .pp-pricing-table .pp-pricing-table-matrix .pp-pricing-table-price").css('height', spaceHeight + 'px');

		var tallestTitle = 0;
		$('.fl-node-<?php echo $id; ?> .pp-pricing-table .pp-pricing-table-card .pp-pricing-table-title').each(function() {
			if ( $(this).outerHeight() > tallestTitle ) {
				tallestTitle = $(this).outerHeight();
			}
		});

		if ( tallestTitle > 0 ) {
			$('.fl-node-<?php echo $id; ?> .pp-pricing-table .pp-pricing-table-card .pp-pricing-table-title').css( 'height', tallestTitle + 'px' );
		}

		var tallest = 0;
		$('.fl-node-<?php echo $id; ?> .pp-pricing-table .pp-pricing-table-card .pp-pricing-table-header').each(function() {
			if ( $(this).outerHeight() > tallest ) {
				tallest = $(this).outerHeight();
			}
		});

		if ( tallest > 0 ) {
			$('.fl-node-<?php echo $id; ?> .pp-pricing-table .pp-pricing-table-matrix .pp-pricing-table-header').css( 'height', tallest + 'px' );
		}

		$('.fl-node-<?php echo $id; ?> .pp-pricing-table-matrix .pp-pricing-table-features li').each(function() {
			var height = $(this).outerHeight();
			var index = $(this).index();
			var item = $('.fl-node-<?php echo $id; ?> .pp-pricing-table-card .pp-pricing-table-features li.pp-pricing-table-item-' + (index+1));
			if ( height > item.outerHeight() ) {
				item.css('height', height + 'px');
			} else {
				$(this).css('height', item.outerHeight() + 'px');
			}
		});
	};

	$( adjustHeights );

	$(window).on( 'resize', adjustHeights );

	$(document).on( 'pp_expandable_row_toggle', function( e, selector ) {
		if ( selector.parent().find( '.fl-node-<?php echo $id; ?>' ).length > 0 ) {
			adjustHeights();
		}
	} );

})(jQuery);