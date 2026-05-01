(function($) {
	var fixedHeight = <?php echo 'yes' === $settings->adaptive_height ? 'true' : 'false'; ?>;
	function equalheight() {
		if ( ! fixedHeight ) {
			return;
		}
		var maxHeight = 0;
		$('.fl-node-<?php echo $id; ?> .pp-testimonial .pp-content-wrapper').each(function(index) {
			if(($(this).outerHeight()) > maxHeight) {
				maxHeight = $(this).outerHeight();
			}
		});
		$('.fl-node-<?php echo $id; ?> .pp-testimonial .pp-content-wrapper').css('height', maxHeight + 'px');
	}

<?php if ( count( $settings->testimonials ) >= 1 && isset( $settings->layout ) && 'slider' === $settings->layout ) : ?>
	var left_arrow_svg = '<?php pp_prev_icon_svg( __( 'Previous', 'bb-powerpack' ) ); ?>';
	var right_arrow_svg = '<?php pp_next_icon_svg( __( 'Next', 'bb-powerpack' ) ); ?>';

	<?php
	$breakpoints = array(
		'large'  => empty( $global_settings->large_breakpoint ) ? '1200' : $global_settings->large_breakpoint,
		'medium' => empty( $global_settings->medium_breakpoint ) ? '1024' : $global_settings->medium_breakpoint,
		'small' => empty( $global_settings->responsive_breakpoint ) ? '768' : $global_settings->responsive_breakpoint,
	);
	$items = empty( absint( $settings->min_slides ) ) ? 3 : absint( $settings->min_slides );
	$items_large = ! isset( $settings->min_slides_large ) || empty( $settings->min_slides_large ) ? $items : $settings->min_slides_large;
	$items_medium = ! isset( $settings->min_slides_medium ) || empty( $settings->min_slides_medium ) ? $items_large : $settings->min_slides_medium;
	$items_responsive = ! isset( $settings->min_slides_responsive ) || empty( $settings->min_slides_responsive ) ? $items_medium : $settings->min_slides_responsive;

	?>
	var setAriaHidden = function(e) {
		var currentIndex = e.item.index; // Get the index of the first visible slide
        var visibleItems = e.page.size;  // Get the number of visible slides

		// Loop through all slides
        $(e.target).find('.owl-item').each(function(index) {
            // Set aria-hidden="false" for visible slides, aria-hidden="true" for non-visible slides
            if (index >= currentIndex && index < currentIndex + visibleItems) {
                $(this).attr('aria-hidden', 'false');
            } else {
                $(this).attr('aria-hidden', 'true');
            }
        });
	};
	var onChange = function( e ) {
		setTimeout(function() {
			$( e.target ).find( '.owl-item' ).removeClass( 'pp-testimonial--center' );
			var actives = $( e.target ).find( '.owl-item.active' );
			if ( actives.length === 3 ) {
				$( actives[1] ).addClass( 'pp-testimonial--center' );
			}
		}, 200);

		$(e.target).find('.owl-dot').each(function() {
			if ( $(this).hasClass( 'active' ) ) {
				$(this).attr('aria-current', 'true');
			} else {
				$(this).attr('aria-current', 'false');
			}
		});

		//var currentIndex = e.item.index + 1;
    	//$(e.target).find('.owl-item').eq(currentIndex).focus();

		var visibleSlides = 0; // Counter for real visible slides
		$(e.target).find('.owl-item').each(function(index) {
			// Check if the item is not a clone
			if (!$(this).hasClass('cloned')) {
				visibleSlides++;
				$(this).attr('aria-label', 'Testimonial ' + visibleSlides + ' of ' + e.item.count);
			}
		});

		setAriaHidden(e);
	};

	var options = {
		items: <?php echo $items; ?>,
		responsive: {
			0: {
				items: <?php echo $items_responsive; ?>,
				<?php if ( isset( $settings->move_slides_responsive ) && ! empty( $settings->move_slides_responsive ) ) { ?>
					slideBy: <?php echo absint( $settings->move_slides_responsive ); ?>,
					dotsEach: <?php echo 1 == $settings->move_slides_responsive ? 'true' : 'false'; ?>,
				<?php } ?>
			},
			<?php echo $breakpoints['small'] + 1; ?>: {
				items: <?php echo $items_medium; ?>,
				<?php if ( isset( $settings->move_slides_medium ) && ! empty( $settings->move_slides_medium ) ) { ?>
					slideBy: <?php echo absint( $settings->move_slides_medium ); ?>,
					dotsEach: <?php echo 1 == $settings->move_slides_medium ? 'true' : 'false'; ?>,
				<?php } ?>
			},
			<?php echo $breakpoints['medium'] + 1; ?>: {
				items: <?php echo $items_large; ?>,
				<?php if ( isset( $settings->move_slides_large ) && ! empty( $settings->move_slides_large ) ) { ?>
					slideBy: <?php echo absint( $settings->move_slides_large ); ?>,
					dotsEach: <?php echo 1 == $settings->move_slides_large ? 'true' : 'false'; ?>,
				<?php } ?>
			},
			<?php echo $breakpoints['large'] + 1; ?>: {
				items: <?php echo $items; ?>,
			}
		},
		dots: <?php echo 1 == $settings->dots ? 'true' : 'false'; ?>,
		<?php if ( 1 == $settings->move_slides ) { ?> 
		dotsEach: true,
		<?php } ?>
		autoplay: <?php echo 1 == $settings->autoplay ? 'true' : 'false'; ?>,
		autoplayHoverPause: <?php echo 1 == $settings->hover_pause ? 'true' : 'false'; ?>,
		autoplayTimeout: <?php echo absint( $settings->pause ) * 1000; ?>,
		autoplaySpeed: <?php echo $settings->speed * 1000; ?>,
		navSpeed: <?php echo $settings->speed * 1000; ?>,
		dotsSpeed: <?php echo $settings->speed * 1000; ?>,
		navText: [left_arrow_svg, right_arrow_svg],
		navContainer: '.fl-node-<?php echo $id; ?> .pp-testimonials-nav',
		navElement:'button type="button"',
		loop: <?php echo 1 == $settings->loop ? 'true' : 'false'; ?>,
		autoHeight: ! fixedHeight,
		<?php if ( 'vertical' === $settings->transition ) { ?>
			items: 1,
			responsive: {},
			animateOut: 'slideOutUp',
  			animateIn: 'slideInUp',
		<?php } elseif ( 'fade' === $settings->transition ) { ?>
			animateOut: 'fadeOut',
  			animateIn: 'fadeIn',
		<?php } ?>
		slideBy: <?php echo ! empty( $settings->move_slides ) ? $settings->move_slides : 1; ?>,
		mouseDrag: <?php echo isset( $settings->disable_mouse_drag ) && 1 == $settings->disable_mouse_drag ? 'false' : 'true'; ?>,
		responsiveRefreshRate: 200,
		responsiveBaseWidth: window,
		margin: <?php echo ! empty( $settings->slide_margin ) ? $settings->slide_margin : '0'; ?>,
		rtl: $('body').hasClass( 'rtl' ),
		onInitialized: function(e) {
			onChange(e);
			setAriaHidden(e);
			equalheight();

			var count = 1;
			$(e.target).find('.owl-dot').each(function() {
				$(this).append( '<span class="sr-only">Testimonial Slide ' + count + '</span>' );
				if ( $(this).hasClass( 'active' ) ) {
					$(this).attr('aria-current', 'true');
				} else {
					$(this).attr('aria-current', 'false');
				}
				count++;
			});

			$(e.target).find( '.owl-item' ).attr('role','group'); 

			var visibleSlides = 0; // Counter for real visible slides
			$(e.target).find('.owl-item').each(function(index) {
				// Check if the item is not a clone
				if (!$(this).hasClass('cloned')) {
					visibleSlides++;
					$(this).attr('aria-label', 'Testimonial ' + visibleSlides + ' of ' + e.item.count);
				}
			});

			$(e.target).parent().find('.owl-prev').attr('title','Previous testimonial');
			$(e.target).parent().find('.owl-next').attr('title','Next testimonial');
			
			var carousel = this;
			carousel.$element.on( 'focus', function() {
				carousel.$element.on( 'keyup', function(e) {
					if ( 37 === e.keyCode || 37 === e.which ) {
						carousel.prev();
					}
					if ( 39 === e.keyCode || 39 === e.which ) {
						carousel.next();
					}
				} );
			} ).on( 'blur', function() {
				carousel.$element.off( 'keyup' );
			} );
		},
		onResized: equalheight,
		onRefreshed: equalheight,
		onLoadedLazy: equalheight,
		onChanged: onChange,
		onDragged: function(e) {
			var autoplay = <?php echo 1 == $settings->autoplay ? 'true' : 'false'; ?>;

			if ( ! autoplay ) {
				$(e.target).trigger( 'stop.owl.autoplay' );
			}
		}
	};

	if ( $.fn.imagesLoaded ) {
		$('.fl-node-<?php echo $id; ?>').imagesLoaded(function() {
			$('.fl-node-<?php echo $id; ?> .owl-carousel').owlCarousel( options );
		});
	} else {
		$('.fl-node-<?php echo $id; ?> .owl-carousel').owlCarousel( options );
	}

	if ( $('.fl-node-<?php echo $id; ?> .owl-carousel').parents( '.pp-tabs-panel-content:not(:visible)' ).length > 0 ) {
		$('.fl-node-<?php echo $id; ?> .owl-carousel').addClass( 'owl-hidden' );
	}

<?php endif; ?>

})(jQuery);
