<?php
$items = $module->get_slider_items();
$has_lightbox = $module->has_lightbox();

if ( ! is_array( $items ) || empty( $items ) ) {
	$msg = isset( $settings->not_found_msg ) && ! empty( $settings->not_found_msg ) ? $settings->not_found_msg : __( 'No items found.', 'bb-powerpack' );
	echo $msg;
	return;
}
?>
<div class="pp-card-slider-container">
	<div class="pp-card-slider">
		<div class="swiper-wrapper">
		<?php
		foreach ( $items as $item ) {
			$link_attrs = $module->get_item_link_attrs( $item );
			?>
			<div class="pp-card-slider-item swiper-slide">

			<?php if ( 'box' === $settings->link_type ) { ?>
				<a <?php echo $link_attrs; ?> class="pp-card-slider-box-link"></a>
			<?php } ?>

				<?php if ( 'yes' === $settings->show_image && ! empty( $item['image'] ) ) { ?>
					<div class="pp-card-slider-image<?php echo $has_lightbox ? ' has-lightbox' : ''; ?>">
					<?php if ( ! empty( $item['link'] ) && ( 'image' === $settings->link_type ) ) { ?>
						<a <?php echo $link_attrs; ?>></a>
					<?php } ?>
						<?php if ( $has_lightbox ) { ?>
						<a href="<?php echo $item['image_full']; ?>" data-has-lightbox="true">
						<?php } ?>
						<img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" />
						<?php if ( $has_lightbox ) { ?>
						</a>
						<?php } ?>
					</div>
				<?php } // End if() ?>

				<div class="pp-card-slider-content-wrap">

					<?php
					if ( 'yes' === $settings->show_title && ! empty( $item['title'] ) ) {
						?>
						<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-card-slider-title">
						<?php if ( 'title' === $settings->link_type ) { ?>
							<a <?php echo $link_attrs; ?>><?php echo esc_html( $item['title'] ); ?></a>
							<?php
						} else {
							echo esc_html( $item['title'] );
						} // End if else
						?>

						</<?php echo esc_attr( $settings->title_tag ); ?>>
						<?php
					} // End if()

					if ( ( 'posts' === $settings->source ) && ( 'yes' === $settings->show_date || 'yes' === $settings->show_author ) ) {
						?>

					<div class="pp-card-slider-meta">

						<?php if ( 'yes' === $settings->show_date && ! empty( $item['date'] ) ) { ?>

							<div class="pp-card-slider-date">
								<?php if ( ! empty( $settings->date_icon ) ) { ?>
								<span class="pp-card-slider-meta-icon <?php echo esc_attr( $settings->date_icon ); ?>"></span>
								<?php } ?>
								<span class="pp-card-slider-meta-text"><?php echo esc_html( $item['date'] ); ?></span>
							</div>
							<?php
						} // End if()
						?>

						<?php if ( 'yes' === $settings->show_author && ! empty( $item['author'] ) ) { ?>

							<div class="pp-content-author">
								<?php if ( ! empty( $settings->author_icon ) ) { ?>
								<span class="pp-card-slider-meta-icon <?php echo esc_attr( $settings->author_icon ); ?>"></span>
								<?php } ?>
								<span class="pp-card-slider-meta-text"><?php echo esc_html( $item['author'] ); ?></span>
							</div>
						<?php } // End if() ?>

					</div>
						<?php
					} // End if()

					if ( 'yes' === $settings->show_excerpt ) {
						?>

						<div class="pp-card-slider-content"><?php echo $item['content']; ?></div>

						<?php
					}

					if ( 'button' === $settings->link_type ) {
						?>
					<div class="pp-card-slider-button-wrap">
						<a <?php echo $link_attrs; ?> class="pp-card-slider-button">
							<?php echo $settings->card_button_text; ?>
						</a>
					</div>
					<?php } ?>

				</div>

			</div>
		<?php } // End foreach() ?>
		</div>
		<?php if ( 'yes' === $settings->show_pagination ) { ?>
		<div class="swiper-pagination"></div>
		<?php } ?>
	</div>
</div>
