var carousel_<?php echo $id; ?> = '';

(function($) {
	<?php if($settings->click_action == 'lightbox') : ?>
		$(function() {
			var gallery_selector = $( '.fl-node-<?php echo $id; ?> .pp-image-carousel' );
			if( gallery_selector.length && typeof $.fn.magnificPopup !== 'undefined') {
				gallery_selector.magnificPopup({
					delegate: '.pp-image-carousel-item:not(.swiper-slide-duplicate) a, .pp-image-carousel-item.swiper-slide-duplicate a',
					closeBtnInside: false,
					type: 'image',
					gallery: {
						enabled: true,
						navigateByImgClick: true,
						tCounter: ''
					},
					mainClass: 'mfp-<?php echo $id; ?>',
					<?php if ( isset( $settings->lightbox_caption ) && 'yes' == $settings->lightbox_caption ) { ?>
					'image': {
						titleSrc: function(item) {
							var caption = item.el.data('caption') || '';
							return caption;
						}
					}
					<?php } ?>
				});

				$( '.fl-node-<?php echo $id; ?> .pp-image-carousel .swiper-slide' ).on( 'keyup', function(e) {
					if ( 13 === e.keyCode || 13 === e.which ) {
						$( '.fl-node-<?php echo $id; ?> .pp-image-carousel .swiper-slide a' ).trigger( 'click' );
					}
				} );
			}
		});
	<?php endif; ?>

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
			desktop: <?php echo absint( $settings->columns ); ?>,
			large: <?php echo isset( $settings->columns_large ) ? absint( $settings->columns_large ) : absint( $settings->columns ); ?>,
			tablet: <?php echo absint( $settings->columns_medium ); ?>,
			mobile: <?php echo absint( $settings->columns_responsive ); ?>,
		},
		slidesRows: {
			desktop: <?php echo absint( $settings->rows ); ?>,
			large: <?php echo absint( $settings->rows_large ); ?>,
			tablet: <?php echo absint( $settings->rows_medium ); ?>,
			mobile: <?php echo absint( $settings->rows_responsive ); ?>,
		},
		slidesToScroll: {
			desktop: <?php echo $slides_to_scroll; ?>,
			large: <?php echo $slides_to_scroll_large; ?>,
			tablet: <?php echo $slides_to_scroll_tablet; ?>,
			mobile: <?php echo $slides_to_scroll_mobile; ?>,
		},
		slideshow_slidesPerView: {
			desktop: <?php echo absint($settings->thumb_columns); ?>,
			large: <?php echo isset( $settings->thumb_columns_large ) ? absint($settings->thumb_columns_large) : absint($settings->thumb_columns); ?>,
			tablet: <?php echo absint($settings->thumb_columns_medium); ?>,
			mobile: <?php echo absint($settings->thumb_columns_responsive); ?>,
		},
		spaceBetween: {
			desktop: '<?php echo $settings->spacing; ?>',
			large: '<?php echo isset( $settings->spacing_large ) ? $settings->spacing_large : $settings->spacing; ?>',
			tablet: '<?php echo $settings->spacing_medium; ?>',
			mobile: '<?php echo $settings->spacing_responsive; ?>',
		},
		direction: '<?php echo isset( $settings->direction ) ? $settings->direction : 'horizontal'; ?>',
		isBuilderActive: <?php echo FLBuilderModel::is_builder_active() ? 'true' : 'false'; ?>,
		pagination: '<?php echo 'progress' == $settings->pagination_type ? 'progressbar' : $settings->pagination_type; ?>',
		dynamic_bullets: <?php echo isset( $settings->dynamic_bullets ) && 'yes' == $settings->dynamic_bullets ? 'true' : 'false'; ?>,
		autoplay: <?php echo $settings->autoplay == 'yes' ? 'true' : 'false'; ?>,
		autoplay_delay: <?php echo $settings->autoplay == 'yes' ? '' == $settings->autoplay_delay ? 3000 : absint( $settings->autoplay_delay ) : 'false'; ?>,
		reverseDirection: <?php echo isset( $settings->reverse_direction ) && 'yes' == $settings->reverse_direction ? 'true' : 'false'; ?>,
		pause_on_interaction: <?php echo $settings->pause_on_interaction == 'yes' ? 'true' : 'false'; ?>,
		effect: '<?php echo $settings->effect; ?>',
		speed: <?php echo $settings->transition_speed; ?>,
		centered_slides: <?php echo isset( $settings->centered_slides ) && 'yes' === $settings->centered_slides ? 'true' : 'false'; ?>,
		lazy_load: <?php echo 'yes' === $settings->lazy_load ? 'true' : 'false'; ?>,
		<?php if ( $settings->mousewheel_control ) { ?>
		mousewheel: {
			enabled: <?php echo 'yes' == $settings->mousewheel_control ? 'true' : 'false'; ?>,
			sensitivity: '<?php echo $settings->sensitivity; ?>',
			forceToAxis: <?php echo 'yes' == $settings->force_to_axis ? 'true' : 'false'; ?>,
			invert: <?php echo 'yes' == $settings->invert_scrolling ? 'true' : 'false'; ?>,
			releaseOnEdges: <?php echo 'yes' == $settings->release_on_edges ? 'true' : 'false'; ?>,
		},
		<?php } ?>
		breakpoint: {
			large: <?php echo isset( $global_settings->large_breakpoint ) ? $global_settings->large_breakpoint : 1200; ?>,
			medium: <?php echo $global_settings->medium_breakpoint; ?>,
			responsive: <?php echo $global_settings->responsive_breakpoint; ?>
		},
		navText: {
			prev: '<?php echo isset( $settings->prev_nav_sr_text ) && ! empty( $settings->prev_nav_sr_text ) ? htmlspecialchars( $settings->prev_nav_sr_text ) : ''; ?>',
			next: '<?php echo isset( $settings->next_nav_sr_text ) && ! empty( $settings->next_nav_sr_text ) ? htmlspecialchars( $settings->next_nav_sr_text ) : ''; ?>',
		}
	};

	<?php if ( isset( $settings->loop ) ) { ?>
	settings.loop = <?php echo 'yes' === $settings->loop ? 'true' : 'false'; ?>;
	<?php } ?>
	<?php if ( isset( $settings->stop_last_slide ) && 'yes' === $settings->stop_last_slide ) { ?>
	settings.stopOnLastSlide = true;
	<?php } ?>

	carousel_<?php echo $id; ?> = new PPImageCarousel(settings);

	function updateCarousel() {
		setTimeout(function() {
			if ( 'number' !== typeof carousel_<?php echo $id; ?>.swipers.main.length ) {
				carousel_<?php echo $id; ?>.swipers.main.update();
				if ( 'object' === typeof carousel_<?php echo $id; ?>.swipers.thumbs ) {
					carousel_<?php echo $id; ?>.swipers.thumbs.update();
				}
			} else {
				carousel_<?php echo $id; ?>.swipers.main.forEach(function(item) {
					if ( 'undefined' !== typeof item ) {
						item.update();
					}
				});
			}
		}, 10);
	}

	// Modal Box fix
	$(document).on('pp_modal_box_rendered', function(e, selector) {
		if ( selector.find('.fl-node-<?php echo $id; ?>').length > 0 ) {
			updateCarousel();
		}
	});
	
	$(document).on('fl-builder.pp-accordion-toggle-complete', function(e) {
		if ( $(e.target).find('.fl-node-<?php echo $id; ?>').length > 0 ) {
			updateCarousel();
		}
	});

	$(document).on('pp-tabs-switched', function(e, selector) {
		if ( selector.find('.fl-node-<?php echo $id; ?>').length > 0 ) {
			updateCarousel();
		}
	});

	// Beaver Builder Tabs module.
	$('.fl-tabs').find('.fl-tabs-label').on('click', function() {
		var index = $(this).data('index');
		var panel = $(this).parents('.fl-tabs').find('.fl-tabs-panel-content[data-index="' + index + '"]');
		if ( panel.find('.fl-node-<?php echo $id; ?>').length > 0 ) {
			updateCarousel();
		}
	});

	// expandable row fix.
	var state = 0;
	$(document).on('pp_expandable_row_toggle', function(e, selector) {
		if ( selector.is('.pp-er-open') && state === 0 && selector.parent().find( '.fl-node-<?php echo $id; ?>' ).length > 0 ) {
			updateCarousel();
			state = 1;
		}
	});
	
})(jQuery);