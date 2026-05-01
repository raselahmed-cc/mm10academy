<?php
$layout = $module->get_layout();
$videos = $module->get_videos();
$filters_enabled = $module->filters_enabled();
$has_pagination = isset( $settings->pagination ) && 'yes' === $settings->pagination && ! empty( $settings->per_page );

$container_classes = array(
	'pp-video-gallery',
);

$wrapper_classes = array(
	'pp-video-gallery-items'
);

if ( empty( $videos ) ) {
	return;
}

if ( 'carousel' === $layout ) {
	$container_classes[] = 'pp-video-carousel';
	$container_classes[] = 'swiper';
	$container_classes[] = 'swiper-container';
	$wrapper_classes[] = 'swiper-wrapper';
}

$index = 0;
?>
<div class="<?php echo implode( ' ', $container_classes ); ?>">

	<?php
	// Render filters.
	if ( 'gallery' === $layout ) {
		$module->render_filters();
	}
	?>

	<div class="<?php echo implode( ' ', $wrapper_classes ); ?>">
		<?php
		foreach ( $videos as $video ) {
			if ( ! is_object( $video ) ) {
				continue;
			}
			if ( $has_pagination && $index >= $settings->per_page ) {
				break;
			}
			include $module->dir . 'includes/video-item.php';
			$index++;
		} // End foreach().
		?>
		<?php if ( 'gallery' === $layout ) { ?>
			<div class="pp-video-gallery--spacer"></div>
		<?php } ?>
	</div>
	<?php if ( 'gallery' === $layout && $has_pagination && count( $videos ) > $settings->per_page ) { ?>
	<div class="pp-video-gallery-pagination" data-per-page="<?php echo esc_attr( $settings->per_page ); ?>" data-total="<?php echo count( $videos ); ?>">
		<a href="javascript:void(0)"><?php echo esc_attr( $settings->load_more_text ); ?></a>
		<?php
		$offset_videos = array_slice( $videos, $settings->per_page );
		$offset_items = '';
		ob_start();
		foreach ( $offset_videos as $video ) {
			if ( ! is_object( $video ) ) {
				continue;
			}
			include $module->dir . 'includes/video-item.php';
		}
		$offset_items = ob_get_clean();
		?>
		<script type="text/html" class="pp-video-gallery-pagination-items">
			<?php echo preg_replace( '/\>\s+\</m', '><', $offset_items ); ?>
		</script>
	</div>
	<?php } ?>
	<?php if ( 'carousel' === $layout && 1 < count( $videos ) ) { ?>
		<?php if ( $settings->pagination_type ) { ?>
			<div class="swiper-pagination"></div>
		<?php } ?>

		<?php if ( 'yes' === $settings->slider_navigation ) { ?>
			<div class="pp-video-carousel-nav pp-video-carousel-nav-prev swiper-button-prev">
				<?php
				echo apply_filters( 'pp_video_gallery_nav_prev', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M11.28 15.7l-1.34 1.37L5 12l4.94-5.07 1.34 1.38-2.68 2.72H19v1.94H8.6z"/></svg>', $settings );
				?>
			</div>
			<div class="pp-video-carousel-nav pp-video-carousel-nav-next swiper-button-next">
				<?php
				echo apply_filters( 'pp_video_gallery_nav_next', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M15.4 12.97l-2.68 2.72 1.34 1.38L19 12l-4.94-5.07-1.34 1.38 2.68 2.72H5v1.94z"/></svg>', $settings );
				?>
			</div>
		<?php } ?>
	<?php } ?>
</div>
