<?php
$photo    = $module->get_data();
$classes  = $module->get_classes();
$src      = $module->get_src();
$link     = $module->get_link();
$alt      = $module->get_alt();
$attrs    = $module->get_attributes();
$rel 	  = $module->get_rel();
$caption  = $module->get_caption();
$width    = isset( $photo->main->width ) && ! empty( $photo->main->width ) ? $photo->main->width : false;

$is_rollover = ( isset( $settings->rollover_photo ) && ! empty( $settings->rollover_photo ) );

if ( $is_rollover ) {
	$module->set_photo_type( 'rollover' );
	$photo_rollover    = $module->get_data();
	$classes_rollover  = $module->get_classes();
	$src_rollover      = $module->get_src();
	$link_rollover     = $module->get_link();
	$alt_rollover      = $module->get_alt();
	$attrs_rollover    = $module->get_attributes();
	$caption_rollover  = $module->get_caption();
	$width_rollover    = isset( $photo_rollover->rollover->width ) && ! empty( $photo_rollover->rollover->width ) ? $photo_rollover->rollover->width : false;
}

$class    = '';
if ( 'hover' == $settings->show_caption ) {
	$class = ' pp-overlay-wrap';
}
?>
<div class="pp-photo-container">
	<div class="pp-photo<?php echo $is_rollover ? ' pp-photo-rollover' : ''; ?><?php if ( ! empty( $settings->crop ) ) echo ' pp-photo-crop-' . esc_attr( $settings->crop ) ; ?> pp-photo-align-<?php echo esc_attr( $settings->align ); ?> pp-photo-align-responsive-<?php echo esc_attr( $settings->align_responsive ); ?>" itemscope itemtype="http://schema.org/ImageObject">
		<div class="pp-photo-content<?php echo $class; ?>">
			<div class="pp-photo-content-inner">
				<?php if ( ! empty( $link ) ) { ?>
				<a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $settings->link_target ); ?>" itemprop="url"<?php echo $rel; ?>>
				<?php } ?>
					<img class="<?php echo $classes; ?>" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" itemprop="image" <?php echo $attrs; ?> />
					<div class="pp-overlay-bg"></div>
					<?php if ( ! empty( $caption ) && 'never' != $settings->show_caption ) { ?>
						<div class="pp-photo-caption pp-photo-caption-<?php echo esc_attr( $settings->show_caption ); ?>" itemprop="caption"<?php echo $width ? ' style="max-width: ' . $width . 'px;"' : ''; ?>><?php echo $caption; ?></div>
					<?php } ?>
				<?php if ( ! empty( $link ) ) { ?>
				</a>
				<?php } ?>
			</div>
			<?php if ( $is_rollover ) { ?>
				<div class="pp-photo-content-inner">
					<?php if ( ! empty( $link_rollover ) ) { ?>
					<a href="<?php echo esc_url( $link_rollover ); ?>" target="<?php echo esc_attr( $settings->link_target ); ?>" itemprop="url"<?php echo $rel; ?>>
					<?php } ?>
						<img class="<?php echo $classes_rollover; ?>" src="<?php echo esc_url( $src_rollover ); ?>" alt="<?php echo esc_attr( $alt_rollover ); ?>" itemprop="image" <?php echo $attrs_rollover; ?> />
						<div class="pp-overlay-bg"></div>
						<?php if ( ! empty( $caption_rollover ) && 'never' != $settings->show_caption ) { ?>
							<div class="pp-photo-caption pp-photo-caption-<?php echo esc_attr( $settings->show_caption ); ?>" itemprop="caption"<?php echo $width_rollover ? ' style="max-width: ' . $width_rollover . 'px;"' : ''; ?>><?php echo $caption_rollover; ?></div>
						<?php } ?>
					<?php if ( ! empty( $link_rollover ) ) { ?>
					</a>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
