<?php
$is_social_reviews = isset( $settings->review_source ) && 'default' !== $settings->review_source;
$reviews           = $module->get_reviews();

if ( $is_social_reviews ) {
	if ( is_wp_error( $reviews ) && pp_is_builder_active() ) {
		echo $reviews->get_error_message();
		return;
	}
}

if ( $is_social_reviews && ( ! is_array( $reviews ) || empty( $reviews ) ) ) {
	return;
}
?>

<div class="pp-reviews-wrapper pp-reviews-<?php echo $settings->review_source; ?>">
	<div class="pp-reviews-swiper swiper-container">
		<!-- Slides wrapper -->
		<?php if ( $is_social_reviews ) { ?>
		<div class="swiper-wrapper">
			<?php
			foreach ( $reviews as $key => $review ) {

				$img_src = ! empty( $review['profile_photo_url'] ) ? $review['profile_photo_url'] : BB_POWERPACK_URL . 'assets/images/user.png';

				?>
					<div class="pp-review-item swiper-slide">
						<div class="pp-review">
							<?php do_action( 'pp_reviews_before_review_content', $review, $settings ); ?>

							<?php if ( isset( $settings->header_position ) && 'top' == $settings->header_position ) { ?>
							<div class="pp-review-header">

								<?php if ( ! isset( $settings->show_image ) || 'yes' === $settings->show_image ) { ?>
								<div class="pp-review-image">
									<img src="<?php echo $img_src; ?>" alt="<?php echo $review['author_name']; ?>" />
								</div>
							<?php } ?>

								<cite class="pp-review-cite">
									<span class="pp-review-name"><?php echo $review['author_name']; ?></span>
									<?php
									$rating = (float) $review['rating'] > 5 ? 5 : $review['rating'];
									if ( $rating > 0 ) {
										include $module->dir . 'includes/rating-html.php';
									}
									?>
									<span class="pp-review-title"><?php echo $review['title']; ?></span>
								</cite>
								<div class="pp-review-icon">
									<?php if ( 'yelp' === $review['source'] && ! empty( $module->get_source_icon( 'yelp' ) ) ) { ?>
									<i class="<?php echo $module->get_source_icon( 'yelp' ); ?>" aria-hidden="true"></i>
										<?php
									} else {
										?>
										<i class="<?php echo $module->get_source_icon( 'google' ); ?>" aria-hidden="true"></i>
										<?php
									}
									?>
								</div>
							</div>
							<?php } ?>
							<div class="pp-review-content">
								<div class="pp-review-text">
									<?php echo $module->get_review_text( $review ); ?>
								</div>
								<?php if ( isset( $settings->link_to_review ) && 'yes' === $settings->link_to_review && 'default' !== $settings->review_source ) { ?>
								<div class="pp-review-link">
									<a href="<?php echo $review['review_url']; ?>" target="_blank" rel="noopener nofollow"><?php echo $settings->link_text; ?></a>
								</div>
								<?php } ?>
							</div>
							<?php if ( isset( $settings->header_position ) && 'bottom' == $settings->header_position ) { ?>
							<div class="pp-review-header">

								<?php if ( ! isset( $settings->show_image ) || 'yes' === $settings->show_image ) { ?>
								<div class="pp-review-image">
									<img src="<?php echo $img_src; ?>" alt="<?php echo $review['author_name']; ?>" />
								</div>
								<?php } ?>

								<cite class="pp-review-cite">
									<span class="pp-review-name"><?php echo $review['author_name']; ?></span>
									<?php
									$rating = (float) $review['rating'] > 5 ? 5 : $review['rating'];
									if ( $rating > 0 ) {
										include $module->dir . 'includes/rating-html.php';
									}
									?>
									<span class="pp-review-title"><?php echo $review['title']; ?></span>
								</cite>
								<div class="pp-review-icon">
									<?php if ( 'yelp' === $review['source'] && ! empty( $module->get_source_icon( 'yelp' ) ) ) { ?>
									<i class="<?php echo $module->get_source_icon( 'yelp' ); ?>" aria-hidden="true"></i>
										<?php
									} else {
										?>
										<i class="<?php echo $module->get_source_icon( 'google' ); ?>" aria-hidden="true"></i>
										<?php
									}
									?>
								</div>
							</div>
							<?php } ?>

							<?php do_action( 'pp_reviews_after_review_content', $review, $settings ); ?>
						</div>
					</div>
				<?php
			}
			?>
		</div>
		<?php } else { ?>
			<div class="swiper-wrapper">
			<?php
			for ( $i = 0; $i < count( $settings->reviews ); $i++ ) {
				$review = $settings->reviews[ $i ];
				if ( ! is_object( $review ) ) {
					continue;
				}
				?>
			<div class="pp-review-item pp-review-item-<?php echo $i; ?> swiper-slide">
				<div class="pp-review">
					<?php do_action( 'pp_reviews_before_review_content', $review, $settings ); ?>

					<?php if ( isset( $settings->header_position ) && 'top' == $settings->header_position ) { ?>
					<div class="pp-review-header">

						<?php if ( ! isset( $settings->show_image ) || 'yes' === $settings->show_image ) { ?>
						<div class="pp-review-image">
							<?php
							$img_src = empty( $review->image_src ) ? BB_POWERPACK_URL . 'assets/images/user.png' : esc_url( $review->image_src );
							?>
							<img src="<?php echo $img_src; ?>" alt="<?php echo strip_tags( $review->name ); ?>" />
						</div>
						<?php } ?>
						
						<cite class="pp-review-cite">
							<span class="pp-review-name"><?php echo $review->name; ?></span>
							<?php
							$rating = (float) $review->rating > 5 ? 5 : $review->rating;
							if ( $rating > 0 ) {
								include $module->dir . 'includes/rating-html.php';
							}
							?>
							<span class="pp-review-title"><?php echo $review->title; ?></span>
						</cite>
						<div class="pp-review-icon"><i class="<?php echo esc_attr( $review->icon ); ?>" aria-hidden="true"></i></div>
					</div>
					<?php } ?>
					<div class="pp-review-content">
						<div class="pp-review-text">
							<?php echo $review->review; ?>
						</div>
					</div>
					<?php if ( isset( $settings->header_position ) && 'bottom' == $settings->header_position ) { ?>
					<div class="pp-review-header">

						<?php if ( ! isset( $settings->show_image ) || 'yes' === $settings->show_image ) { ?>
						<div class="pp-review-image">
							<?php
							$img_src = empty( $review->image_src ) ? BB_POWERPACK_URL . 'assets/images/user.png' : esc_url( $review->image_src );
							?>
							<img src="<?php echo $img_src; ?>" alt="<?php echo strip_tags( $review->name ); ?>" />
						</div>
					<?php } ?>

						<cite class="pp-review-cite">
							<span class="pp-review-name"><?php echo $review->name; ?></span>
							<?php
							$rating = (float) $review->rating > 5 ? 5 : $review->rating;
							if ( $rating > 0 ) {
								include $module->dir . 'includes/rating-html.php';
							}
							?>
							<span class="pp-review-title"><?php echo $review->title; ?></span>
						</cite>
						<div class="pp-review-icon"><i class="<?php echo esc_attr( $review->icon ); ?>" aria-hidden="true"></i></div>
					</div>
					<?php } ?>

					<?php do_action( 'pp_reviews_after_review_content', $review, $settings ); ?>
				</div>
			</div>
				<?php
			}
			?>
		</div>
		<?php } ?>
		<!-- Pagination, if required -->
		<div class="swiper-pagination"></div>

	</div>
	<?php if ( 'yes' === $settings->slider_navigation ) { ?>
		<div class="pp-swiper-button pp-swiper-button-prev"><?php pp_prev_icon_svg( esc_html__( 'Previous', 'bb-powerpack' ) ); ?></div>
		<div class="pp-swiper-button pp-swiper-button-next"><?php pp_next_icon_svg( esc_html__( 'Next', 'bb-powerpack' ) ); ?></div>
	<?php } ?>
</div>
