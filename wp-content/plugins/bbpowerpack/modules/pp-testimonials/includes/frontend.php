<?php
$layout             = isset( $settings->layout ) ? esc_attr( $settings->layout ) : 'slider';
$testimonial_layout = esc_attr( $settings->testimonial_layout );
$layout_path        = apply_filters( 'pp_testimonial_layout_path', $module->dir . 'includes/layout-' . $testimonial_layout . '.php', $settings );
$testimonials       = $module->get_testimonials();
$heading_tag        = isset( $settings->heading_tag ) ? esc_attr( $settings->heading_tag ) : 'h2';
$testimonial_title_tag = isset( $settings->testimonial_title_tag ) ? esc_attr( $settings->testimonial_title_tag ) : 'h3';
$title_tag          = isset( $settings->title_tag ) ? esc_attr( $settings->title_tag ) : 'div';
$subtitle_tag       = isset( $settings->subtitle_tag ) ? esc_attr( $settings->subtitle_tag ) : 'div';
$is_carousel        = absint( $settings->min_slides ) > 1;

if ( $layout_path && ! file_exists( $layout_path ) ) {
	return;
}

if ( empty( $testimonials ) || ! is_array( $testimonials ) ) {
	return;
}

if ( isset( $settings->order ) ) {
	if( 'random' == $settings->order ) {
		shuffle( $testimonials );
	}

	if( 'desc' == $settings->order ) {
		krsort( $testimonials );
	}
}

$testimonials_class  = 'pp-testimonials-wrap';
$testimonials_class .= ' pp-testimonials-' . $layout;

if ( 'grid' == $layout ) {
	$testimonials_class .= ' pp-testimonials-grid-' . absint( $settings->grid_columns );

	if ( $settings->grid_columns_large ) {
		$testimonials_class .= ' pp-testimonials-grid-lg-' . absint( $settings->grid_columns_large );
	}
	if ( $settings->grid_columns_medium ) {
		$testimonials_class .= ' pp-testimonials-grid-md-' . absint( $settings->grid_columns_medium );
	}
	if ( $settings->grid_columns_responsive ) {
		$testimonials_class .= ' pp-testimonials-grid-sm-' . absint( $settings->grid_columns_responsive );
	}
}

if ( '' == $settings->heading ) {
	$testimonials_class .= ' pp-testimonials-no-heading';
}
?>

<div class="<?php echo $testimonials_class; ?>">
	<?php if ( '4' == $settings->testimonial_layout ) { ?>
		<div class="layout-4-container<?php echo ( 'slider' == $layout && $is_carousel ) ? ' carousel-enabled' : ''; ?>">
	<?php } ?>
	<?php if ( '' != $settings->heading ) { ?>
		<<?php echo $heading_tag; ?> class="pp-testimonials-heading"><?php echo $settings->heading; ?></<?php echo $heading_tag; ?>>
	<?php } ?>

	<div class="pp-testimonials">
		<?php
		$classes = '';
		if ( 'slider' == $layout ) {
			$classes = $is_carousel ? ' carousel-enabled' : '';
			echo '<div class="owl-carousel owl-theme' . ( 'no' === $settings->adaptive_height ? ' owl-height' : '' ) . '" tabindex="0"' . ( '' != $settings->heading ? ' aria-label="' . esc_attr( $settings->heading ) . '"' : '' ) . '>';
		}

		foreach ( $testimonials as $testimonial ) {
			include $layout_path;
		}

		if ( 'slider' == $layout ) {
			echo '</div>';
			if ( $settings->arrows ) {
				$arrow_position = isset( $settings->arrow_position ) ? esc_attr( $settings->arrow_position ) : 'bottom';
				?>
				<div class="owl-nav pp-testimonials-nav position-<?php echo $arrow_position; ?>"></div>
			<?php }
		}
		?>
	</div><!-- /.pp-testimonials -->
	<?php if( $settings->testimonial_layout == '4' ) { ?>
	</div>
	<?php } ?>
</div>
