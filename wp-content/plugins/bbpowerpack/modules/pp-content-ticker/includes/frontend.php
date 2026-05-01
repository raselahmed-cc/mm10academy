<?php
	$items = $module->get_ticker_items();

	if ( ! is_array( $items ) || empty( $items ) ) {
		$msg = isset( $settings->not_found_msg ) && ! empty( $settings->not_found_msg ) ? $settings->not_found_msg : __( 'No items found.', 'bb-powerpack' );
		echo $msg;
		return;
	}
?>

<div class="pp-content-ticker-container">

<?php if ( 'yes' === $settings->header_enable ) {

	if ( ! empty( $settings->heading_icon ) || ! empty( $settings->heading_text ) ) {

		?>
	<div class="pp-content-ticker-heading">
		<?php if ( isset( $settings->heading_link ) && ! empty( $settings->heading_link ) ) { ?>
		<a href="<?php echo esc_url( do_shortcode( $settings->heading_link ) ); ?>" target="<?php echo esc_attr( $settings->heading_link_target ); ?>" rel="<?php echo 'yes' === $settings->heading_link_nofollow ? 'nofollow' : 'bookmark'; ?>">
		<?php } ?>
			<?php if ( ! empty( $settings->heading_icon ) ) { ?>
			<span class="pp-content-ticker-heading-icon">
				<span class="<?php echo esc_attr( $settings->heading_icon ); ?>"></span>
			</span>
			<?php } ?>

			<?php if ( ! empty( $settings->heading_text ) ) { ?>
			<span class="pp-content-ticker-heading-text">
				<?php echo $settings->heading_text; ?>
			</span>
			<?php } ?>
		<?php if ( isset( $settings->heading_link ) && ! empty( $settings->heading_link ) ) { ?>
		</a>
		<?php } ?>
	</div>

		<?php
	}
}
?>

	<div class="pp-content-ticker-wrap">
		<div class="pp-content-ticker">
			<div class="swiper-wrapper">

		<?php
		foreach ( $items as $item ) {
			$link_attrs = $module->get_item_link_attrs( $item );
			?>
			<div class="pp-content-ticker-item swiper-slide">
				<div class="pp-content-ticker-content">
					<?php if ( 'yes' === $settings->show_image && ! empty( $item['image'] ) ) { ?>
						<div class="pp-content-ticker-image">
						<?php if ( ! empty( $item['link'] ) && ( 'image' === $settings->link_type || 'both' === $settings->link_type ) ) { ?>
							<a <?php echo $link_attrs; ?>>
								<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" />
							</a>
						<?php } else { ?>
							<img src="<?php echo $item['image']; ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" />
						<?php } ?>
						</div>
					<?php } // End if() ?>
					<div class="pp-content-ticker-item-title-wrap">
						<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-content-ticker-item-title">
							<?php if ( ! empty( $item['link'] ) && ( 'title' === $settings->link_type || 'both' === $settings->link_type ) ) { ?>
								<a <?php echo $link_attrs; ?>><?php echo $item['title']; ?></a>
							<?php } else { ?>
								<?php echo $item['title']; ?>
							<?php } ?>
						</<?php echo esc_attr( $settings->title_tag ); ?>>
						<?php if ( 'posts' === $settings->source ) { ?>
							<?php if ( 'yes' === $settings->post_meta_toggle && ( 'yes' === $settings->date_toggle || 'yes' === $settings->author_toggle ) ) { ?>
							<div class="pp-content-ticker-meta">
								<?php if ( 'yes' === $settings->date_toggle ) { // Date toggle. ?>
								<span class="pp-content-ticker-date">
									<?php if ( ! empty( $settings->date_icon ) ) { ?>
									<span class="pp-content-ticker-meta-icon">
										<span class="<?php echo esc_attr( $settings->date_icon ); ?>"></span>
									</span>
									<?php } ?>
									<span class="pp-content-ticker-meta-text"><?php echo esc_html( $item['date'] ); ?></span>
								</span>
								<?php } // End if() ?>
								<?php if ( 'yes' === $settings->author_toggle ) { // Author toggle. ?>
								<span class="pp-content-ticker-author">
									<?php if ( ! empty( $settings->author_icon ) ) { ?>
									<span class="pp-content-ticker-meta-icon">
										<span class="<?php echo esc_attr( $settings->author_icon ); ?>"></span>
									</span>
									<?php } ?>
									<span class="pp-content-ticker-meta-text"><?php echo esc_html( $item['author'] ); ?></span>
								</span>
								<?php } // End if() ?>
							</div>
							<?php } // End if() ?>
						<?php } // End if() ?>
					</div>
				</div>
			</div>
			<?php
		} // End foreach() ?>

			</div>
		</div>
	</div>

	<?php
	if ( 'yes' === $settings->nav_arrow ) {

		if ( ! empty( $settings->arrow_type ) ) {
			$pa_next_arrow = $settings->arrow_type;
			$pa_prev_arrow = str_replace( 'right', 'left', $settings->arrow_type );
		} else {
			$pa_next_arrow = 'fa fa-angle-right';
			$pa_prev_arrow = 'fa fa-angle-left';
		}
		?>
	<div class="pp-content-ticker-navigation">

		<div class="swiper-button-prev">
			<span class="<?php echo esc_attr( $pa_prev_arrow ); ?>"></span>
		</div>
		<div class="swiper-button-next">
			<span class="<?php echo esc_attr( $pa_next_arrow ); ?>"></span>
		</div>

	</div>

	<?php } ?>

</div>
