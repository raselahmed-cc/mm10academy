<?php
/**
 *  UABB Image Carousel Module front-end JS php file
 *
 *  @package UABB Image Carousel Module
 */

?>
<?php
// Only generate JavaScript if there are photos to display.
$photos = $module->get_photos();
if ( empty( $photos ) ) {
	return;
}
?>
jQuery(document).ready(function( $ ) {
	var args = {
			id: '<?php echo esc_attr( $id ); ?>',
			infinite: <?php echo esc_attr( ( 'yes' === $settings->infinite_loop ) ? 'true' : 'false' ); ?>,
			arrows: <?php echo esc_attr( ( 'yes' === $settings->enable_arrow ) ? 'true' : 'false' ); ?>,

			desktop: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->grid_column ) ? $settings->grid_column : 3 ) : 1 ); ?>,
			extraLarge: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect && isset( $settings->grid_column_extra_large ) ) ? ( ! empty( $settings->grid_column_extra_large ) ? $settings->grid_column_extra_large : 3 ) : ( ! empty( $settings->grid_column ) ? $settings->grid_column : 3 ) ); ?>,
			medium: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->medium_grid_column ) ? $settings->medium_grid_column : 2 ) : 1 ); ?>,
			small: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->responsive_grid_column ) ? $settings->responsive_grid_column : 1 ) : 1 ); ?>,

			slidesToScroll: <?php echo esc_attr( ( '' !== $settings->slides_to_scroll && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll : 1 ); ?>,
			slidesToScrollMedium: <?php echo esc_attr( ( isset( $settings->slides_to_scroll_medium ) && '' !== $settings->slides_to_scroll_medium && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll_medium : ( isset( $settings->slides_to_scroll ) && '' !== $settings->slides_to_scroll ? $settings->slides_to_scroll : 1 ) ); ?>,
			slidesToScrollSmall: <?php echo esc_attr( ( isset( $settings->slides_to_scroll_responsive ) && '' !== $settings->slides_to_scroll_responsive && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll_responsive : ( isset( $settings->slides_to_scroll ) && '' !== $settings->slides_to_scroll ? $settings->slides_to_scroll : 1 ) ); ?>,
			photoSpacing: <?php echo esc_attr( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ); ?>,
			photoSpacingMedium: <?php echo esc_attr( isset( $settings->photo_spacing_medium ) && '' !== $settings->photo_spacing_medium ? $settings->photo_spacing_medium : ( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ) ); ?>,
			photoSpacingSmall: <?php echo esc_attr( isset( $settings->photo_spacing_responsive ) && '' !== $settings->photo_spacing_responsive ? $settings->photo_spacing_responsive : ( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ) ); ?>,
			autoplay: <?php echo esc_attr( ( 'yes' === $settings->autoplay ) ? 'true' : 'false' ); ?>,
			onhover: <?php echo esc_attr( ( 'yes' === $settings->pause_on_hover ) ? 'true' : 'false' ); ?>,
			autoplaySpeed: <?php echo esc_attr( ( '' !== $settings->animation_speed ) ? $settings->animation_speed : '1000' ); ?>,
			small_breakpoint: <?php echo esc_attr( isset( $global_settings->responsive_breakpoint ) && $global_settings->responsive_breakpoint ? $global_settings->responsive_breakpoint : 768 ); ?>,
			medium_breakpoint: <?php echo esc_attr( isset( $global_settings->medium_breakpoint ) && $global_settings->medium_breakpoint ? $global_settings->medium_breakpoint : 1024 ); ?>,
			next_arrow: '<?php echo esc_attr( apply_filters( 'uabb_image_carousel_next_arrow_icon', 'fas fa-angle-right' ) ); ?>',
			prev_arrow: '<?php echo esc_attr( apply_filters( 'uabb_image_carousel_previous_arrow_icon', 'fas fa-angle-left' ) ); ?>',
			enable_fade: <?php echo esc_attr( ( 'fade' === $settings->scroll_effect ) ? 'true' : 'false' ); ?>,
			enable_dots: <?php echo esc_attr( ( 'yes' === $settings->enable_dots ) ? 'true' : 'false' ); ?>
		};

	UABBImageCarousel_<?php echo esc_attr( $id ); ?> = new UABBImageCarousel( args );

	$(window).on("load", function() {
		UABBImageCarousel_<?php echo esc_attr( $id ); ?>._adaptiveImageHeight();
	});

	var UABBImageCarouselResize_<?php echo esc_attr( $id ); ?>;
	$( window ).resize(function() {

		clearTimeout( UABBImageCarouselResize_<?php echo esc_attr( $id ); ?> );
		UABBImageCarouselResize_<?php echo esc_attr( $id ); ?> = setTimeout( UABBImageCarousel_<?php echo esc_attr( $id ); ?>._adaptiveImageHeight, 500);
	});

	/* Content Toggle Trigger */
	UABBTrigger.addHook( 'uabb-toggle-click', function( argument, selector ) {
		var img_carousels = jQuery(selector+' .fl-module-uabb-image-carousel');
		img_carousels.each(function( index ) {
			var child_id = jQuery(this).data('node');
			if( null !== child_id ) {
				jQuery( '.fl-node-' + child_id ).find( '.uabb-image-carousel' ).uabbslick('unslick');
				var child_args = {
					id: child_id,
					infinite: <?php echo esc_attr( ( 'yes' === $settings->infinite_loop ) ? 'true' : 'false' ); ?>,
					arrows: <?php echo esc_attr( ( 'yes' === $settings->enable_arrow ) ? 'true' : 'false' ); ?>,

					desktop: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->grid_column ) ? $settings->grid_column : 3 ) : 1 ); ?>,
					extraLarge: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect && isset( $settings->grid_column_extra_large ) ) ? ( ! empty( $settings->grid_column_extra_large ) ? $settings->grid_column_extra_large : 3 ) : ( ! empty( $settings->grid_column ) ? $settings->grid_column : 3 ) ); ?>,
					medium: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->medium_grid_column ) ? $settings->medium_grid_column : 2 ) : 1 ); ?>,
					small: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->responsive_grid_column ) ? $settings->responsive_grid_column : 1 ) : 1 ); ?>,

					slidesToScroll: <?php echo esc_attr( ( '' !== $settings->slides_to_scroll && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll : 1 ); ?>,
					slidesToScrollMedium: <?php echo esc_attr( ( isset( $settings->slides_to_scroll_medium ) && '' !== $settings->slides_to_scroll_medium && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll_medium : ( isset( $settings->slides_to_scroll ) && '' !== $settings->slides_to_scroll ? $settings->slides_to_scroll : 1 ) ); ?>,
					slidesToScrollSmall: <?php echo esc_attr( ( isset( $settings->slides_to_scroll_responsive ) && '' !== $settings->slides_to_scroll_responsive && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll_responsive : ( isset( $settings->slides_to_scroll ) && '' !== $settings->slides_to_scroll ? $settings->slides_to_scroll : 1 ) ); ?>,
					photoSpacing: <?php echo esc_attr( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ); ?>,
					photoSpacingMedium: <?php echo esc_attr( isset( $settings->photo_spacing_medium ) && '' !== $settings->photo_spacing_medium ? $settings->photo_spacing_medium : ( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ) ); ?>,
					photoSpacingSmall: <?php echo esc_attr( isset( $settings->photo_spacing_responsive ) && '' !== $settings->photo_spacing_responsive ? $settings->photo_spacing_responsive : ( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ) ); ?>,
					autoplay: <?php echo esc_attr( ( 'yes' === $settings->autoplay ) ? 'true' : 'false' ); ?>,
					onhover: <?php echo esc_attr( ( 'yes' === $settings->pause_on_hover ) ? 'true' : 'false' ); ?>,
					autoplaySpeed: <?php echo esc_attr( ( '' !== $settings->animation_speed ) ? $settings->animation_speed : '1000' ); ?>,
					small_breakpoint: <?php echo esc_attr( isset( $global_settings->responsive_breakpoint ) && $global_settings->responsive_breakpoint ? $global_settings->responsive_breakpoint : 768 ); ?>,
					medium_breakpoint: <?php echo esc_attr( isset( $global_settings->medium_breakpoint ) && $global_settings->medium_breakpoint ? $global_settings->medium_breakpoint : 1024 ); ?>,
					next_arrow: '<?php echo esc_attr( apply_filters( 'uabb_image_carousel_next_arrow_icon', 'fas fa-angle-right' ) ); ?>',
					prev_arrow: '<?php echo esc_attr( apply_filters( 'uabb_image_carousel_previous_arrow_icon', 'fas fa-angle-left' ) ); ?>',
					enable_fade: <?php echo esc_attr( ( 'fade' === $settings->scroll_effect ) ? 'true' : 'false' ); ?>,
					enable_dots: <?php echo esc_attr( ( 'yes' === $settings->enable_dots ) ? 'true' : 'false' ); ?>
				};
				new UABBImageCarousel( child_args );
			}
		});

	});

	/* Tab Click Trigger */
	UABBTrigger.addHook( 'uabb-tab-click', function( argument, selector ) {
		var img_carousels = jQuery(selector+' .fl-module-uabb-image-carousel');
		img_carousels.each(function( index ) {
			var child_id = jQuery(this).data('node');
			if( null !== child_id ) {
				jQuery( '.fl-node-' + child_id ).find( '.uabb-image-carousel' ).uabbslick('unslick');
				var child_args = {
					id: child_id,
					infinite: <?php echo esc_attr( ( 'yes' === $settings->infinite_loop ) ? 'true' : 'false' ); ?>,
					arrows: <?php echo esc_attr( ( 'yes' === $settings->enable_arrow ) ? 'true' : 'false' ); ?>,

					desktop: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->grid_column ) ? $settings->grid_column : 3 ) : 1 ); ?>,
					extraLarge: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect && isset( $settings->grid_column_extra_large ) ) ? ( ! empty( $settings->grid_column_extra_large ) ? $settings->grid_column_extra_large : 3 ) : ( ! empty( $settings->grid_column ) ? $settings->grid_column : 3 ) ); ?>,
					medium: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->medium_grid_column ) ? $settings->medium_grid_column : 2 ) : 1 ); ?>,
					small: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->responsive_grid_column ) ? $settings->responsive_grid_column : 1 ) : 1 ); ?>,

					slidesToScroll: <?php echo esc_attr( ( '' !== $settings->slides_to_scroll && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll : 1 ); ?>,
					slidesToScrollMedium: <?php echo esc_attr( ( isset( $settings->slides_to_scroll_medium ) && '' !== $settings->slides_to_scroll_medium && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll_medium : ( isset( $settings->slides_to_scroll ) && '' !== $settings->slides_to_scroll ? $settings->slides_to_scroll : 1 ) ); ?>,
					slidesToScrollSmall: <?php echo esc_attr( ( isset( $settings->slides_to_scroll_responsive ) && '' !== $settings->slides_to_scroll_responsive && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll_responsive : ( isset( $settings->slides_to_scroll ) && '' !== $settings->slides_to_scroll ? $settings->slides_to_scroll : 1 ) ); ?>,
					photoSpacing: <?php echo esc_attr( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ); ?>,
					photoSpacingMedium: <?php echo esc_attr( isset( $settings->photo_spacing_medium ) && '' !== $settings->photo_spacing_medium ? $settings->photo_spacing_medium : ( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ) ); ?>,
					photoSpacingSmall: <?php echo esc_attr( isset( $settings->photo_spacing_responsive ) && '' !== $settings->photo_spacing_responsive ? $settings->photo_spacing_responsive : ( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ) ); ?>,
					autoplay: <?php echo esc_attr( ( 'yes' === $settings->autoplay ) ? 'true' : 'false' ); ?>,
					onhover: <?php echo esc_attr( ( 'yes' === $settings->pause_on_hover ) ? 'true' : 'false' ); ?>,
					autoplaySpeed: <?php echo esc_attr( ( '' !== $settings->animation_speed ) ? $settings->animation_speed : '1000' ); ?>,
					small_breakpoint: <?php echo esc_attr( isset( $global_settings->responsive_breakpoint ) && $global_settings->responsive_breakpoint ? $global_settings->responsive_breakpoint : 768 ); ?>,
					medium_breakpoint: <?php echo esc_attr( isset( $global_settings->medium_breakpoint ) && $global_settings->medium_breakpoint ? $global_settings->medium_breakpoint : 1024 ); ?>,
					next_arrow: '<?php echo esc_attr( apply_filters( 'uabb_image_carousel_next_arrow_icon', 'fas fa-angle-right' ) ); ?>',
					prev_arrow: '<?php echo esc_attr( apply_filters( 'uabb_image_carousel_previous_arrow_icon', 'fas fa-angle-left' ) ); ?>',
					enable_fade: <?php echo esc_attr( ( 'fade' === $settings->scroll_effect ) ? 'true' : 'false' ); ?>,
					enable_dots: <?php echo esc_attr( ( 'yes' === $settings->enable_dots ) ? 'true' : 'false' ); ?>
				};
				new UABBImageCarousel( child_args );
			}
		});
	});

	/* Accordion Click Trigger */
		UABBTrigger.addHook( 'uabb-accordion-click', function( argument, selector ) {
			var img_carousels = jQuery(selector+' .fl-module-uabb-image-carousel');
			img_carousels.each(function( index ) {
			var child_id = jQuery(this).data('node');
			if( child_id !== null ) {
				jQuery( '.fl-node-' + child_id ).find( '.uabb-image-carousel' ).uabbslick('unslick');
				var child_args = {
					id: child_id,
					infinite: <?php echo esc_attr( ( 'yes' === $settings->infinite_loop ) ? 'true' : 'false' ); ?>,
					arrows: <?php echo esc_attr( ( 'yes' === $settings->enable_arrow ) ? 'true' : 'false' ); ?>,

					desktop: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->grid_column ) ? $settings->grid_column : 3 ) : 1 ); ?>,
					extraLarge: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect && isset( $settings->grid_column_extra_large ) ) ? ( ! empty( $settings->grid_column_extra_large ) ? $settings->grid_column_extra_large : 3 ) : ( ! empty( $settings->grid_column ) ? $settings->grid_column : 3 ) ); ?>,
					medium: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->medium_grid_column ) ? $settings->medium_grid_column : 2 ) : 1 ); ?>,
					small: <?php echo esc_attr( ( 'slide' === $settings->scroll_effect ) ? ( ! empty( $settings->responsive_grid_column ) ? $settings->responsive_grid_column : 1 ) : 1 ); ?>,

					slidesToScroll: <?php echo esc_attr( ( '' !== $settings->slides_to_scroll && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll : 1 ); ?>,
					slidesToScrollMedium: <?php echo esc_attr( ( isset( $settings->slides_to_scroll_medium ) && '' !== $settings->slides_to_scroll_medium && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll_medium : ( isset( $settings->slides_to_scroll ) && '' !== $settings->slides_to_scroll ? $settings->slides_to_scroll : 1 ) ); ?>,
					slidesToScrollSmall: <?php echo esc_attr( ( isset( $settings->slides_to_scroll_responsive ) && '' !== $settings->slides_to_scroll_responsive && 'slide' === $settings->scroll_effect ) ? $settings->slides_to_scroll_responsive : ( isset( $settings->slides_to_scroll ) && '' !== $settings->slides_to_scroll ? $settings->slides_to_scroll : 1 ) ); ?>,
					photoSpacing: <?php echo esc_attr( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ); ?>,
					photoSpacingMedium: <?php echo esc_attr( isset( $settings->photo_spacing_medium ) && '' !== $settings->photo_spacing_medium ? $settings->photo_spacing_medium : ( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ) ); ?>,
					photoSpacingSmall: <?php echo esc_attr( isset( $settings->photo_spacing_responsive ) && '' !== $settings->photo_spacing_responsive ? $settings->photo_spacing_responsive : ( ! empty( $settings->photo_spacing ) ? $settings->photo_spacing : 20 ) ); ?>,
					autoplay: <?php echo esc_attr( ( 'yes' === $settings->autoplay ) ? 'true' : 'false' ); ?>,
					onhover: <?php echo esc_attr( ( 'yes' === $settings->pause_on_hover ) ? 'true' : 'false' ); ?>,
					autoplaySpeed: <?php echo esc_attr( ( '' !== $settings->animation_speed ) ? $settings->animation_speed : '1000' ); ?>,
					small_breakpoint: <?php echo esc_attr( isset( $global_settings->responsive_breakpoint ) && $global_settings->responsive_breakpoint ? $global_settings->responsive_breakpoint : 768 ); ?>,
					medium_breakpoint: <?php echo esc_attr( isset( $global_settings->medium_breakpoint ) && $global_settings->medium_breakpoint ? $global_settings->medium_breakpoint : 1024 ); ?>,
					next_arrow: '<?php echo esc_attr( apply_filters( 'uabb_image_carousel_next_arrow_icon', 'fas fa-angle-right' ) ); ?>',
					prev_arrow: '<?php echo esc_attr( apply_filters( 'uabb_image_carousel_previous_arrow_icon', 'fas fa-angle-left' ) ); ?>',
					enable_fade: <?php echo esc_attr( ( 'fade' === $settings->scroll_effect ) ? 'true' : 'false' ); ?>,
					enable_dots: <?php echo esc_attr( ( 'yes' === $settings->enable_dots ) ? 'true' : 'false' ); ?>
				};
				obj = new UABBImageCarousel( child_args );
				jQuery(window).trigger('resize');

				var selector = $(selector).find('.slick-track');
				if( selector ){
					setTimeout(function() {
						selector.imagesLoaded( function() {
							obj._adaptiveImageHeight();
						});

					}, 100);
				}
			}
		});
		});

	<?php if ( 'lightbox' === $settings->click_action ) : ?>
	$('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-image-carousel').magnificPopup({
		delegate: '.uabb-image-carousel-content a',
		closeBtnInside: false,
		type: 'image',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
		},
		'image': {
			titleSrc: function(item) {
				<?php if ( 'below' === $settings->show_captions ) : ?>
					return item.el.data('caption');
				<?php elseif ( 'hover' === $settings->show_captions ) : ?>
					return item.el.data('caption');
				<?php endif; ?>
			}
		}
	});
	<?php endif; ?>
	$(function() {
		$( '.fl-node-<?php echo esc_attr( $id ); ?> .uabb-gallery-img' )
			.on( 'mouseenter', function( e ) {
				$( this ).data( 'title', $( this ).attr( 'title' ) ).removeAttr( 'title' );
			} )
			.on( 'mouseleave', function( e ){
				$( this ).attr( 'title', $( this ).data( 'title' ) ).data( 'title', null );
			} );
	});

});
