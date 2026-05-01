<?php
	$style        = 'style-0';
	$category_id  = $cat->term_id;
	$current_class = isset( $current_cat ) && $current_cat === $cat->term_id ? ' pp-category__current' : '';
	$cat_thumb_id = 0;
	if ( 'enabled' === $taxonomy_thumbnail_enable && ! empty( $taxonomy_thumbnail_taxonomies ) && in_array( $cat->taxonomy, (array) $taxonomy_thumbnail_taxonomies ) ) {
		$taxonomy_thumbnail_id = get_term_meta( $cat->term_id, 'taxonomy_thumbnail_id', true );
		if ( empty( $cat_thumb_id ) ) {
			$cat_thumb_id = $taxonomy_thumbnail_id;
		}
	} else {
		$cat_thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
	}
	if ( empty( $cat_thumb_id ) && 'product_cat' === $cat->taxonomy ) {
		$cat_thumb_id = get_term_meta( $cat->term_id, 'product_cat_thumbnail_id', true );
	}
	$category_image = wp_get_attachment_image_src( $cat_thumb_id, $settings->category_image_size );
	$term_link      = apply_filters( 'pp_category_term_link', get_term_link( $cat, $cat->taxonomy ), $cat, $settings );
?>
<div class="pp-category pp-category-<?php echo $category_id; ?><?php echo $current_class; ?><?php echo 'yes' === $settings->category_grid_slider ? ' swiper-slide' : ''; ?> pp-clear<?php echo $hide_img ? ' pp-category__no-image' : ''; ?> layout-<?php echo $layout; ?>" title="<?php echo $cat->name; ?>">
	<div class="category-inner category-<?php echo $style; ?>">
		<a href="<?php echo $term_link; ?>" target="<?php echo esc_attr( $settings->category_link_target ); ?>" class="pp-category__link">
			<?php if ( ! $hide_img ) { ?>
			<div class="pp-category__img">
				<?php if ( is_array( $category_image ) && ! empty( $category_image ) ) { ?>
					<img src="<?php echo $category_image[0]; ?>" alt="<?php echo $cat->name; ?>">
					<?php
				} elseif ( ! empty( $settings->category_fallback_image ) && ! empty( $settings->category_fallback_image_src ) ) {
					?>
						<img src="<?php echo esc_url( $settings->category_fallback_image_src ); ?>" alt="<?php echo $cat->name; ?>">
					<?php
				} else {
					?>
						<img src="<?php echo BB_POWERPACK_URL; ?>assets/images/placeholder-300.jpg" alt="<?php echo $cat->name; ?>">
				<?php } ?>
			</div>
			<?php } ?>
			<div class="pp-category__content">
				<div class='pp-category__title_wrapper'>
					<?php if ( 'style-0' === $style ) { ?>
						<<?php echo esc_attr( $settings->category_title_tag ); ?> class="pp-category__title">
							<?php echo $cat->name; ?>
						</<?php echo esc_attr( $settings->category_title_tag ); ?>>
						<?php if ( 'yes' === $settings->category_show_counter ) { ?>
							<span class="pp-category-count">
								<?php $category_count_text = ( 0 === $cat->count || $cat->count > 1 ) ? $settings->category_count_text_plural : $settings->category_count_text; ?>
								<?php echo $cat->count; ?> <?php echo  $category_count_text; ?> </span>
						<?php } ?>
					<?php } ?>
				</div>
				<?php if ( 'yes' === $settings->category_show_description ) : ?>
					<div class='pp-category__description_wrapper'>
						<p class="pp-category__description"><?php echo $cat->category_description; ?></p>
					</div>
				<?php endif; ?>
				<?php if ( 'yes' === $settings->category_show_button ) : ?>
					<div class="pp-category__button_wrapper">
						<button type="button" name="button" class="pp-category__button">
							<?php
							if ( '' !== $settings->category_button_text ) {
								echo $settings->category_button_text;
							} else {
								esc_html_e( 'Shop Now', 'bb-powerpack' );
							}
							?>
						</button>
					</div>
				<?php endif; ?>
			</div>
		</a>
	</div>
</div>
