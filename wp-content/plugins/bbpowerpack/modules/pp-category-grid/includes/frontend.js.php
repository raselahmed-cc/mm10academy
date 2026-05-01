;(function($) {
	<?php
	$columns            = empty( $settings->columns ) ? 3 : $settings->columns;
	$columns_large      = isset( $settings->columns_large ) && empty( $settings->columns_large ) ? $columns : $settings->columns_large;
	$columns_medium     = empty( $settings->columns_medium ) ? $columns_large : $settings->columns_medium;
	$columns_responsive = empty( $settings->columns_responsive ) ? $columns_medium : $settings->columns_responsive;
	?>

	<?php
	$slides_to_scroll = $slides_to_scroll_large = $slides_to_scroll_tablet = $slides_to_scroll_mobile = 1;
	if ( isset( $settings->slides_to_scroll ) && ! empty( $settings->slides_to_scroll ) ) {
		$slides_to_scroll = absint( $settings->slides_to_scroll );
	}
	if ( isset( $settings->slides_to_scroll_large ) && ! empty( $settings->slides_to_scroll_large ) ) {
		$slides_to_scroll_large = absint( $settings->slides_to_scroll_large );
	} else {
		$slides_to_scroll_large = $slides_to_scroll;
	}
	if ( isset( $settings->slides_to_scroll_medium ) && ! empty( $settings->slides_to_scroll_medium ) ) {
		$slides_to_scroll_tablet = absint( $settings->slides_to_scroll_medium );
	} else {
		$slides_to_scroll_tablet = $slides_to_scroll_large;
	}
	if ( isset( $settings->slides_to_scroll_responsive ) && ! empty( $settings->slides_to_scroll_responsive ) ) {
		$slides_to_scroll_mobile = absint( $settings->slides_to_scroll_responsive );
	} else {
		$slides_to_scroll_mobile = $slides_to_scroll_tablet;
	}
	?>

	var settings = {
		id: '<?php echo $id; ?>',
		type: '<?php echo $settings->carousel_type; ?>',
		initialSlide: 0,
		slidesPerView: {
			desktop: <?php echo absint( $columns ); ?>,
			large: <?php echo absint( $columns_large ); ?>,
			tablet: <?php echo absint( $columns_medium ); ?>,
			mobile: <?php echo absint( $columns_responsive ); ?>,
		},
		slidesToScroll: {
			desktop: <?php echo $slides_to_scroll; ?>,
			large: <?php echo $slides_to_scroll_large; ?>,
			tablet: <?php echo $slides_to_scroll_tablet; ?>,
			mobile: <?php echo $slides_to_scroll_mobile; ?>,
		},
		spaceBetween: {
			desktop: '<?php echo $settings->spacing; ?>',
			large: '<?php echo ! isset( $settings->spacing_large ) || empty( $settings->spacing_large ) ? $settings->spacing : $settings->spacing_large; ?>',
			tablet: '<?php echo $settings->spacing_medium; ?>',
			mobile: '<?php echo $settings->spacing_responsive; ?>',
		},
		isBuilderActive: <?php echo FLBuilderModel::is_builder_active() ? 'true' : 'false'; ?>,
		pagination: '<?php echo $settings->pagination_type; ?>',
		autoplay_speed: <?php echo 'yes' === $settings->autoplay ? $settings->autoplay_speed : 'false'; ?>,
		pause_on_interaction: <?php echo ( 'yes' === $settings->pause_on_interaction ) ? 'true' : 'false'; ?>,
		effect: 'slide',
		speed: <?php echo ! empty( $settings->transition_speed ) ? $settings->transition_speed : 1000; ?>,
		breakpoint: {
			large: <?php echo $global_settings->large_breakpoint; ?>,
			medium: <?php echo $global_settings->medium_breakpoint; ?>,
			responsive: <?php echo $global_settings->responsive_breakpoint; ?>
		},
	};

	<?php if ( isset( $settings->loop ) ) { ?>
	settings.loop = <?php echo 'yes' === $settings->loop ? 'true' : 'false'; ?>;
	<?php } ?>

	<?php if ( 'yes' === $settings->category_grid_slider ) { ?>
	if ( 'undefined' === typeof window['pp_category_slider'] ) {
		window['pp_category_slider'] = {};
	}
	window['pp_category_slider']['<?php echo $id; ?>'] =  new PPCategoryGridSlider(settings);
	<?php } ?>

})(jQuery);
