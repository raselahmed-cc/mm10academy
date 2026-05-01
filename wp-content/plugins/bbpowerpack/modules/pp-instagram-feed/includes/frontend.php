<?php
$items = $module->get_insta_posts();

if ( is_wp_error( $items ) ) {
	if ( FLBuilderModel::is_builder_active() && isset( $_GET['fl_builder'] ) ) {
		echo $items->get_error_message();
	}
	return;
}

$class = array( 'pp-instagram-feed' );
$attrs = array();
$attr = ' ';

//$class[] = 'pp-instagram-feed-' . $settings->content_visibility;

if ( 'carousel' == $settings->feed_layout ) {
	$class[] = 'pp-instagram-feed-carousel';
} else {
	$class[] = 'pp-instagram-feed-grid';
}

if ( 'grid' == $settings->feed_layout && $settings->grid_columns ) {
	$class[] = 'pp-instagram-feed-' . esc_attr( $settings->grid_columns );
}

if ( 'yes' == $settings->image_hover_grayscale ) {
	$class[] = 'pp-instagram-feed-hover-gray';
}

$inner_class = array( 'pp-instagram-feed-inner' );
$feed_container_class = array();

if ( 'carousel' == $settings->feed_layout ) {
	$inner_class[] = 'swiper-container-wrap';
	$feed_container_class[] = 'swiper-container';
}

if ( 'yes' == $settings->infinite_loop ) {
	$attrs['data-loop'] = 1;
}

if ( 'yes' == $settings->grab_cursor ) {
	$attrs['data-grab-cursor'] = 1;
}

$attrs['data-layout'] = esc_attr( $settings->feed_layout );

foreach ( $attrs as $key => $value ) {
	$attr .= $key . '=' . $value . ' ';
}

$item_class = array( 'pp-feed-item' );

if ( 'carousel' === $settings->feed_layout ) {
	$item_class[] = 'swiper-slide';
}

if ( in_array( $settings->feed_layout, array( 'square-grid', 'carousel' ) ) && ! empty( $settings->image_custom_size ) ) {
	$item_class[] = 'has-custom-size';
}
?>
<div class="<?php echo implode( ' ', $class ); ?>"<?php echo $attr; ?>>
	<?php if ( 'yes' == $settings->profile_link ) { ?>
		<?php if ( ! empty( $settings->insta_link_title ) ) { ?>
			<span class="pp-instagram-feed-title-wrap">
				<a href="<?php echo esc_url( do_shortcode( $settings->insta_profile_url ) ); ?>" target="_blank" rel="nofollow noopener">
					<span class="pp-instagram-feed-title">
						<?php if ( ! empty( $settings->insta_title_icon ) ) { ?>
							<?php if ( 'before_title' == $settings->insta_title_icon_position ) { ?>
								<span class="<?php echo esc_attr( $settings->insta_title_icon ); ?>" aria-hidden="true"></span>
							<?php } ?>
						<?php } ?>
						<?php echo $settings->insta_link_title; ?>
						<?php if ( ! empty( $settings->insta_title_icon ) ) { ?>
							<?php if ( 'after_title' == $settings->insta_title_icon_position ) { ?>
								<span class="<?php echo esc_attr( $settings->insta_title_icon ); ?>" aria-hidden="true"></span>
							<?php } ?>
						<?php } ?>
					</span>
				</a>
			</span>
		<?php } ?>
	<?php } ?>
	<div class="<?php echo implode( ' ', $inner_class ); ?>">
		<div class="<?php echo implode( ' ', $feed_container_class ); ?>">
			<div id="pp-instagram-<?php echo $id; ?>" class="pp-instagram-feed-items<?php if ( 'carousel' == $settings->feed_layout ) { ?> swiper-wrapper<?php } ?>" data-items="<?php echo count( $items ); ?>">
				<?php foreach ( $items as $item ) {
					$caption = ! empty( $item['caption'] ) ? preg_replace( '/\"|\'/', '', $item['caption'] ) : '';
					$alt = empty( $caption ) ? 'instagram-feed-image' : $caption;
					?>
				<div class="<?php echo implode( ' ', $item_class ); ?>">
					<div class="pp-feed-item-inner">
						<?php if ( 'no' !== $settings->image_popup ) { // Anchor wrapper start. ?>
							<a href="<?php echo ( 'yes' === $settings->image_popup ) ? $module->get_insta_image_url( $item ) : $item['link']; ?>" target="_blank" rel="nofollow noopener">
						<?php } ?>
						<div class="pp-overlay-container"><span class="fas fa-search" style="display: none;"></span></div>
						<img src="<?php echo $module->get_insta_image_url( $item, $module->get_insta_image_size() ); ?>" alt="<?php echo htmlspecialchars( $alt ); ?>" class="pp-instagram-feed-img" />
						<?php if ( 'no' !== $settings->image_popup ) { // Anchor wrapper end. ?>
							</a>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php if ( 'carousel' == $settings->feed_layout ) : ?>
			<?php if ( 'yes' == $settings->pagination ) { ?>
			<!-- pagination -->
			<div class="swiper-pagination"></div>
			<?php } ?>

			<?php if ( 'yes' == $settings->navigation ) { ?>
			<!-- navigation arrows -->
			<div class="pp-swiper-button swiper-button-prev"><span class="fa fa-angle-left"></span></div>
			<div class="pp-swiper-button swiper-button-next"><span class="fa fa-angle-right"></span></div>
			<?php } ?>
		<?php endif; ?>
		</div>
	</div>
</div>
