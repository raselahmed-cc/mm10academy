<?php
$sr_text = isset( $settings->sr_text ) && ! empty( $settings->sr_text ) ? $settings->sr_text : esc_html__( 'Image Scroll', 'bb-powerpack' );
$img_alt = '';

if ( 'library' === $settings->photo_source ) {
	$img_url = $settings->photo_src;
	$img_alt = ! empty( $settings->photo ) ? get_post_meta( $settings->photo , '_wp_attachment_image_alt', true ) : '';
} else {
	$img_url = $settings->photo_url;
	$img_alt = ! empty( $img_url ) ? basename( $img_url ) : '';
}
$icon_position = '';

if ( 'icon_text' === $settings->overlay_type ) {
	$icon_position = ' pp-icon-' . $settings->icon_position;
}

// if Link is enabled
if ( ! empty( $settings->link ) ) {
	$link  = "<a class='pp-image-scroll-link'";
	$link .= " href='" . esc_url( do_shortcode( $settings->link ) ) . "' target='" . esc_attr( $settings->link_target ) . "'";
	if ( 'yes' === $settings->link_nofollow ) {
		$link .= " rel='nofollow'";
	}
	$link .= '><span class="sr-only">' . esc_html( $sr_text ) . '</span></a>';
}
?>

<div class="pp-image-scroll-wrap">
	<div class="pp-image-scroll-container pp-image-scroll-<?php echo $settings->scroll_dir; ?> pp-image-control-<?php echo $settings->img_trigger; ?>">
		<div class="pp-image-scroll-image">
			<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo htmlspecialchars( $img_alt ); ?>" class="pp-scroll-image">
		</div>
		<?php if ( 'yes' === $settings->image_overlay ) { ?>
			<div class="pp-image-scroll-overlay pp-overlay-<?php echo esc_attr( $settings->scroll_dir ); ?><?php echo esc_attr( $icon_position ); ?>">
			<?php if ( 'hover' === $settings->img_trigger ) { ?>
				<div class="pp-overlay-content">
					<?php if ( 'icon' === $settings->overlay_type ) { ?>
						<i class="<?php echo esc_attr( $settings->overlay_icon ); ?> pp-overlay-icon"></i>
					<?php } elseif ( 'image' === $settings->overlay_type && ! empty( $settings->overlay_image ) ) {
						$alt = ! empty( $settings->overlay_image ) ? get_post_meta( $settings->overlay_image , '_wp_attachment_image_alt', true ) : '';
						?>
						<img src="<?php echo $settings->overlay_image_src; ?>" alt="<?php echo htmlspecialchars( $alt ); ?>" class="pp-overlay-image">
					<?php } elseif ( 'text' === $settings->overlay_type ) { ?>
						<p class="pp-overlay-text"><?php echo $settings->overlay_text; ?></p>
					<?php } elseif ( 'icon_text' === $settings->overlay_type ) { ?>
						<p class="pp-overlay-text"><?php echo $settings->overlay_text; ?></p>
						<i class="<?php echo esc_attr( $settings->overlay_icon ); ?> pp-overlay-icon"></i>
					<?php } ?>
				</div>
			<?php } ?>
			</div>
		<?php } ?>

		<?php
		if ( ! empty( $settings->link ) ) {
			echo $link;
		}
		?>
	</div>
	<?php if ( 'scroll' === $settings->img_trigger && 'yes' === $settings->image_overlay ) { ?>
		<div class="pp-overlay-scroll-content pp-scroll-overlay-<?php echo $settings->scroll_dir; ?>">
			<div class="pp-overlay-content">
				<?php if ( 'icon' === $settings->overlay_type ) { ?>
					<i class="<?php echo esc_attr( $settings->overlay_icon ); ?> pp-overlay-icon"></i>
				<?php } elseif ( 'image' === $settings->overlay_type ) { ?>
					<img src="<?php echo esc_url( $settings->overlay_image_src ); ?>" alt="" class="pp-overlay-image">
				<?php } elseif ( 'text' === $settings->overlay_type ) { ?>
					<p class="pp-overlay-text"><?php echo $settings->overlay_text; ?></p>
				<?php } elseif ( 'icon_text' === $settings->overlay_type ) { ?>
					<p class="pp-overlay-text"><?php echo $settings->overlay_text; ?></p>
					<i class="<?php echo esc_attr( $settings->overlay_icon ); ?> pp-overlay-icon"></i>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>
