<?php
$padding_top = empty( $settings->logo_grid_padding_top ) ? '0' : $settings->logo_grid_padding_top;
$padding_right = empty( $settings->logo_grid_padding_right ) ? '0' : $settings->logo_grid_padding_right;
$padding_bottom = empty( $settings->logo_grid_padding_bottom ) ? '0' : $settings->logo_grid_padding_bottom;
$padding_left = empty( $settings->logo_grid_padding_left ) ? '0' : $settings->logo_grid_padding_left;
$padding = $padding_top + $padding_right + $padding_bottom + $padding_left;

$carousel_width = isset( $settings->logo_carousel_width ) && ! empty( $settings->logo_carousel_width ) ? $settings->logo_carousel_width : 250;
$carousel_spacing = isset( $settings->logos_carousel_spacing ) && ! empty( $settings->logos_carousel_spacing ) ? $settings->logos_carousel_spacing : 0;
$min_items = isset( $settings->logo_carousel_minimum_grid ) && ! empty( $settings->logo_carousel_minimum_grid ) ? $settings->logo_carousel_minimum_grid : 4;
$min_items_large = isset( $settings->logo_carousel_minimum_grid_large ) && ! empty( $settings->logo_carousel_minimum_grid_large ) ? $settings->logo_carousel_minimum_grid_large : $min_items;
$min_items_med = isset( $settings->logo_carousel_minimum_grid_medium ) && ! empty( $settings->logo_carousel_minimum_grid_medium ) ? $settings->logo_carousel_minimum_grid_medium : $min_items_large;
$min_items_resp = isset( $settings->logo_carousel_minimum_grid_responsive ) && ! empty( $settings->logo_carousel_minimum_grid_responsive ) ? $settings->logo_carousel_minimum_grid_responsive : $min_items_med;

$delay = ! empty( $settings->logo_slider_pause ) ? floatval( $settings->logo_slider_pause ) : 4;
$speed = ! empty( $settings->logo_slider_speed ) ? floatval( $settings->logo_slider_speed ) : 0.5;
?>

(function($) {

	function equalheight() {

		if( window.navigator.userAgent.indexOf( 'MSIE ' ) > 0 ) {
			return;
		}

		var maxHeight = 0;
		$('.fl-node-<?php echo $id; ?> .pp-logos-wrapper .pp-logo').each(function(index) {
			if(($(this).find('.logo-image').outerHeight() + <?php echo floatval( $padding ); ?>) > maxHeight) {
				maxHeight = $(this).find('.logo-image').outerHeight() + <?php echo floatval( $padding ); ?>;
			}
		});
		$('.fl-node-<?php echo $id; ?> .pp-logos-wrapper .pp-logo').css('height', maxHeight + 'px');

		<?php if ( 'carousel' === $settings->logos_layout && 'fade' === $settings->logo_slider_transition ) { ?>
		if($(window).width() <= 768 ){
			$('.fl-node-<?php echo $id; ?> .pp-logos-wrapper .pp-logo').each(function(index) {
				//$(this).css('height', $('.fl-node-<?php echo $id; ?> .pp-logos-content').outerHeight() + 'px');
			});
		}
		<?php } ?>
		return maxHeight;
	}

	$('.fl-node-<?php echo $id; ?> .pp-logos-wrapper').imagesLoaded(function() {
	<?php if ( 'carousel' === $settings->logos_layout ) { ?>
		// Clear the controls in case they were already created.
		//$('.fl-node-<?php echo $id; ?> .logo-slider-next').empty();
		//$('.fl-node-<?php echo $id; ?> .logo-slider-prev').empty();

		var getMinSlides = function() {
			var minSlides = ( $( window ).width() <= 768 ) ? parseInt( $( '.fl-node-<?php echo $id; ?>' ).width() / <?php echo $carousel_width + ( $carousel_spacing * ( $min_items - 1 ) ); ?>) : <?php echo $min_items; ?>;

			<?php if ( isset( $settings->logo_carousel_minimum_grid_medium ) && ! empty( $min_items_large ) ) { ?>
			if ( window.innerWidth <= <?php echo $global_settings->large_breakpoint; ?> ) {
				minSlides = <?php echo $min_items_large; ?>;
			}
			<?php } ?>
			<?php if ( isset( $settings->logo_carousel_minimum_grid_medium ) && ! empty( $min_items_med ) ) { ?>
			if ( window.innerWidth <= <?php echo $global_settings->medium_breakpoint; ?> ) {
				minSlides = <?php echo $min_items_med; ?>;
			}
			<?php } ?>
			<?php if ( isset( $settings->logo_carousel_minimum_grid_responsive ) && ! empty( $min_items_resp ) ) { ?>
			if ( window.innerWidth <= <?php echo $global_settings->responsive_breakpoint; ?> ) {
				minSlides = <?php echo $min_items_resp; ?>;
			}
			<?php } ?>

			minSlides = (minSlides === 0) ? 1 : minSlides;

			return minSlides;
		}

		var minSlides = getMinSlides();

		var maxSlides = minSlides;
		var moveSlides = maxSlides;

		<?php if ( isset( $settings->logo_carousel_move_slide ) && ! empty( $settings->logo_carousel_move_slide ) ) { ?>
			moveSlides = <?php echo $settings->logo_carousel_move_slide; ?>;
		<?php } ?>

		var totalSlides = minSlides - 1;

		$(window).on('resize', function() {
			minSlides = getMinSlides();
			maxSlides = minSlides;
			moveSlides = maxSlides;

			<?php if ( isset( $settings->logo_carousel_move_slide ) && ! empty( $settings->logo_carousel_move_slide ) ) { ?>
			moveSlides = <?php echo $settings->logo_carousel_move_slide; ?>;
			<?php } ?>

			totalSlides = minSlides - 1;
		});

		<?php if ( 'fade' === $settings->logo_slider_transition ) { ?>
		var min_<?php echo $id; ?> = minSlides;
		$('.fl-node-<?php echo $id; ?> .pp-logos-wrapper .pp-logo').each(function(index) {
			//$(this).css('width', 'calc((100% - '+totalSlides * <?php echo $settings->logos_carousel_spacing; ?>+'px) /'+ minSlides + ')');
			$(this).css('width', 'calc((100% - '+totalSlides * <?php echo $settings->logos_carousel_spacing; ?>+'px) /'+ minSlides + ')');
			//$(this).css('width', '<?php echo $carousel_width; ?>px');
			if(index % min_<?php echo $id; ?> == 0) {
				$(this).before('<div class="slide-group clearfix"></div>');
			}
			$(this).appendTo($(this).prev());
		});
		$('.fl-node-<?php echo $id; ?> .pp-logos-wrapper .slide-group .pp-logo:nth-of-type('+minSlides+'n)').css('margin-right', 0);
		<?php } ?>

		<?php if ( 'yes' === $settings->equal_height ) { ?>
		//equalheight();
		<?php } ?>

		var totalSlides = $('.fl-node-<?php echo $id; ?> .pp-logo:not(.bx-clone)').length;

		var options = {
			<?php if ( 'fade' !== $settings->logo_slider_transition ) { ?>
				slideWidth: <?php echo $carousel_width; ?>,
			<?php } ?>
			moveSlides: moveSlides,
			slideMargin: <?php echo ( $settings->logos_carousel_spacing ) ? $settings->logos_carousel_spacing : '0'; ?>,
			minSlides: minSlides,
			maxSlides: maxSlides,
			autoStart : <?php echo $settings->logo_slider_auto_play; ?>,
			auto : true,
			autoHover: <?php echo $settings->logo_slider_pause_hover; ?>,
			adaptiveHeight: false,
			pause : <?php echo $delay * 1000; ?>,
			mode : '<?php echo $settings->logo_slider_transition; ?>',
			speed : <?php echo $speed * 1000; ?>,
			pager : <?php echo $settings->logo_slider_dots; ?>,
			controls: false,
			ariaLive: false,
			onSliderLoad: function() {
				$('.fl-node-<?php echo $id; ?> .pp-logos-wrapper').addClass('pp-logos-wrapper-loaded');
				$('.fl-node-<?php echo $id; ?> .pp-logo').attr('role', 'group');

				var visibleCount = 0;
				$('.fl-node-<?php echo $id; ?> .pp-logo').each(function() {
					if ( ! $(this).hasClass( 'bx-clone' ) ) {
						visibleCount++;
						$(this).attr('aria-label', 'Slide ' + visibleCount + ' of ' + totalSlides );
					}
				});

				setTimeout( function() {
					$(window).trigger('resize');
				}, 200 );

				// Fix keyboard navigation
				var hasItemFocus = false;
				$('.fl-node-<?php echo $id; ?>').off('keyup').on('keyup', function(e) {
					e.stopPropagation();
					if ( $(e.target).hasClass('pp-logos-wrapper') || $(e.target).closest('.pp-logos-wrapper').length ) {
						hasItemFocus = true;
					}
					if ( hasItemFocus && $(e.target).hasClass('logo-slider-nav') ) {
						$(this).find('.pp-logos-wrapper').data('bxSlider').reloadSlider();
						hasItemFocus = false;
					}
				});

				$(document).trigger( 'pp_logos_on_slider_load', [ $('.fl-node-<?php echo $id; ?>') ] );
			},
			onSlideBefore: function( ele, oldIndex, newIndex ) {
				this.stopAuto( true );
				$('.fl-node-<?php echo $id; ?> .logo-slider-nav').addClass('disabled');
				$('.fl-node-<?php echo $id; ?> .bx-controls .bx-pager-link').addClass('disabled');
				<?php if ( $settings->logo_slider_auto_play ) : ?>
				this.startAuto( true );
				<?php endif; ?>

				var visibleCount = 0;
				$('.fl-node-<?php echo $id; ?> .pp-logo').each(function() {
					if ( ! $(this).hasClass( 'bx-clone' ) ) {
						visibleCount++;
						$(this).attr('aria-label', 'Slide ' + visibleCount + ' of ' + totalSlides );
					}
				});
			},
			onSlideAfter: function( ele, oldIndex, newIndex ) {
				$('.fl-node-<?php echo $id; ?> .logo-slider-nav').removeClass('disabled');
				$('.fl-node-<?php echo $id; ?> .bx-controls .bx-pager-link').removeClass('disabled');
			}
		};

		options.onSliderResize = function(currentIndex) {
			options.working = false;
			options.minSlides = minSlides;
			options.maxSlides = maxSlides;
			options.moveSlides = moveSlides;

			this.reloadSlider( options );
		};

		// Create the slider.
		var slider = $('.fl-node-<?php echo $id; ?> .pp-logos-wrapper').bxSlider( options );

		// Store a reference to the slider.
		slider.data('bxSlider', slider);


		<?php if ( $settings->logo_slider_arrows ) { ?>
		$('.fl-node-<?php echo $id; ?> .logo-slider-prev').on( 'click', function( e ){
			e.preventDefault();
			slider.stopAuto( true );
			slider.goToPrevSlide();
			<?php if ( $settings->logo_slider_auto_play ) : ?>
			slider.startAuto( true );
			<?php endif; ?>
		} );

		$('.fl-node-<?php echo $id; ?> .logo-slider-next').on( 'click', function( e ){
			e.preventDefault();
			slider.stopAuto( true );
			slider.goToNextSlide();
			<?php if ( $settings->logo_slider_auto_play ) : ?>
			slider.startAuto( true );
			<?php endif; ?>
		} );
		<?php } ?>

		<?php if ( 'fade' === $settings->logo_slider_transition ) { ?>
		if($(window).width() <= 768 ){
			var viewport_h = $('.fl-node-<?php echo $id; ?> .bx-viewport').outerHeight();
			$('.fl-node-<?php echo $id; ?> .pp-logos-wrapper .pp-logo').css('height', viewport_h + 'px');
		}
		<?php } ?>

	<?php } ?>

		<?php if ( 'ticker' === $settings->logos_layout ) { ?>
			$('.fl-node-<?php echo $id; ?> .pp-logo').clone().addClass('pp-logo-clone').appendTo( $('.fl-node-<?php echo $id; ?> .pp-logos-ticker') );
		<?php } ?>
	});

})(jQuery);
