<?php
$items 				= $settings->visible_items;
$items_medium 		= '' === $settings->visible_items_medium ? $items : $settings->visible_items_medium;
$items_responsive 	= '' === $settings->visible_items_responsive ? $items_medium : $settings->visible_items_responsive;
$width 				= $settings->image_custom_size;
$width_medium 		= '' === $settings->image_custom_size_medium ? $width : $settings->image_custom_size_medium;
$width_responsive 	= '' === $settings->image_custom_size_responsive ? $width_medium : $settings->image_custom_size_responsive;
?>

var pp_feed_<?php echo $id; ?>;
(function($) {
	var items = '<?php echo $items; ?>';
	var items_medium = '<?php echo $items_medium; ?>';
	var items_responsive = '<?php echo $items_responsive; ?>';
	var width = '<?php echo $width; ?>';
	var width_medium = '<?php echo $width_medium; ?>';
	var width_responsive = '<?php echo $width_responsive; ?>';

	var perView = function() {
		var perViewItems = items;
		var itemWidth = width;
		if ( window.innerWidth <= <?php echo $global_settings->medium_breakpoint; ?> ) {
			perViewItems = items_medium;
			itemWidth = width_medium;
		}
		if ( window.innerWidth <= <?php echo $global_settings->responsive_breakpoint; ?> ) {
			perViewItems = items_responsive;
			itemWidth = width_responsive;
		}
		if ( '' === perViewItems && '' !== width ) {
			perViewItems = Math.round( window.innerWidth / parseFloat( width ) );
		} else if ( '' === perViewItems ) {
			perViewItems = 'auto';
		}

		return perViewItems;
	};

	var layout 			= '<?php echo $settings->feed_layout; ?>',
		likes 				= '<?php echo isset( $settings->likes ) ? $settings->likes : ''; ?>',
		comments 			= '<?php echo isset( $settings->comments ) ? $settings->comments : ''; ?>',
		popup				= '<?php echo $settings->image_popup; ?>',
		custom_size			= '<?php echo $settings->image_custom_size; ?>',
		carouselOpts		= {
			direction				: 'horizontal',
			slidesPerView			: perView(),
			spaceBetween			: <?php echo $settings->images_gap; ?>,
			autoplay				: <?php echo 'yes' == $settings->autoplay ? $settings->autoplay_speed : 'false'; ?>,
			<?php if ( 'yes' == $settings->autoplay ) { ?>
			autoplay				: {
				delay: <?php echo $settings->autoplay_speed; ?>,
			},
			<?php } else { ?>
			autoplay				: false,
			<?php } ?>
			grabCursor				: <?php echo 'yes' == $settings->grab_cursor ? 'true' : 'false'; ?>,
			loop					: <?php echo 'yes' == $settings->infinite_loop ? 'true' : 'false'; ?>,
			pagination				: {
				el: '.fl-node-<?php echo $id; ?> .swiper-pagination',
				clickable: true
			},
			navigation				: {
				prevEl: '.fl-node-<?php echo $id; ?> .swiper-button-prev',
				nextEl: '.fl-node-<?php echo $id; ?> .swiper-button-next'
			},
			breakpoints: {
				<?php echo $global_settings->medium_breakpoint; ?>: {
					slidesPerView:  <?php echo ( $settings->visible_items_medium ) ? absint( $settings->visible_items_medium ) : 2; ?>,
					spaceBetween:   <?php echo ( '' != $settings->images_gap_medium ) ? $settings->images_gap_medium : 10; ?>,
				},
				<?php echo $global_settings->responsive_breakpoint; ?>: {
					slidesPerView:  <?php echo ( $settings->visible_items_responsive ) ? absint( $settings->visible_items_responsive ) : 1; ?>,
					spaceBetween:   <?php echo ( '' != $settings->images_gap_responsive ) ? $settings->images_gap_responsive : 10; ?>,
				},
			}
		};

	pp_feed_<?php echo $id; ?> = new PPInstagramFeed({
		id: '<?php echo $id; ?>',
		layout: '<?php echo $settings->feed_layout; ?>',
		limit: <?php echo ! empty ( $settings->images_count ) ? $settings->images_count : 8; ?>,
		/*
		likes_count: <?php echo isset( $settings->likes ) && 'yes' == $settings->likes ? 'true' : 'false'; ?>,
		comments_count: <?php echo isset( $settings->comments ) && 'yes' == $settings->comments ? 'true' : 'false'; ?>,
		*/
		on_click: '<?php echo $settings->image_popup; ?>',
		carousel: carouselOpts,
		image_size: <?php echo ! empty( $settings->image_custom_size ) ? $settings->image_custom_size : '0'; ?>,
		isBuilderActive: <?php echo FLBuilderModel::is_builder_active() ? 'true' : 'false'; ?>,
	});

	<?php if ( ( 'square-grid' == $settings->feed_layout || 'carousel' == $settings->feed_layout ) && empty( $settings->image_custom_size ) ) { ?>
		<?php if ( isset( $settings->aspect_ratio ) && 'yes' !== $settings->aspect_ratio ) { ?>
			function auto_square_layout() {
				$('.fl-node-<?php echo $id; ?> .pp-feed-item').each(function() {
					$(this).find( '.pp-feed-item-inner' ).css( 'height', $(this).outerWidth() + 'px' );
				});
			}

			auto_square_layout();

			$(window).on( 'resize', auto_square_layout );
		<?php } ?>
	<?php } ?>

	<?php if ( 'carousel' === $settings->feed_layout ) { ?>
	$(window).on( 'resize', function() {
		if ( pp_feed_<?php echo $id; ?> && pp_feed_<?php echo $id; ?>._swiper ) {
			var perViewItems = perView();
			if ( pp_feed_<?php echo $id; ?>.node.find('.pp-feed-item-inner').length && pp_feed_<?php echo $id; ?>._swiper.params ) {
				pp_feed_<?php echo $id; ?>._swiper.params.slidesPerView = perViewItems;
				pp_feed_<?php echo $id; ?>._swiper.update();
			}
		}
	} );
	<?php } ?>

	<?php if ( 'yes' == $settings->image_popup ) { ?>
		$('.fl-node-<?php echo $id; ?> .pp-instagram-feed').magnificPopup({
			delegate: '.pp-feed-item a',
			gallery: {
				enabled: true,
				navigateByImgClick: true,
				preload: [0, 1]
			},
			type: 'image'
		});
	<?php } ?>

})(jQuery);
