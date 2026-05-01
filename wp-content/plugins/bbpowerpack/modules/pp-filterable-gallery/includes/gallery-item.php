<?php
$photo_filter_label       = $filter_labels[ $photo->id ];
$final_photo_filter_label = preg_replace( '/[^\sA-Za-z0-9]/', '-', $photo_filter_label );
$dimensions_attrs         = '';

if ( isset( $photo->sizes ) && ! empty( $photo->sizes['width'] ) && ! empty( $photo->sizes['height'] ) ) {
	$dimensions_attrs = ' width="' . $photo->sizes['width'] . '" height="' . $photo->sizes['height'] . '"';
}
?>
<div class="<?php echo $item_class; ?> <?php echo $final_photo_filter_label; ?>" itemprop="associatedMedia" itemscope="itemscope" itemtype="http://schema.org/ImageObject">
	<div class="pp-photo-gallery-content">
		<?php if ( $settings->click_action != 'none' ) {
			$click_action_target = esc_attr( $settings->custom_link_target );
		?>
		<a href="<?php echo $module->get_click_action_link( $photo ); ?>" target="<?php echo $click_action_target; ?>"<?php echo ( '_blank' === $click_action_target && ( ! isset( $settings->custom_link_nofollow ) || 'yes' === $settings->custom_link_nofollow ) ) ? ' rel="nofollow noopener"' : ''; ?>>
		<?php } ?>

		<?php
			$img_attrs = apply_filters( 'pp_filterable_gallery_image_html_attrs', array(
				'class' => 'pp-gallery-img',
				'src' => esc_url( $photo->src ),
				'alt' => esc_attr( $photo->alt ),
				'data-no-lazy' => 1,
			), $photo, $settings );

			if ( isset( $img_attrs['data-no-lazy'] ) && $img_attrs['data-no-lazy'] ) {
				$img_attrs['class'] .= ' no-lazyload skip-lazy'; // exclude from smush and jetpack lazyload.
			}

			$img_attrs_str = '';

			foreach ( $img_attrs as $key => $value ) {
				$img_attrs_str .= ' ' . $key . '=' . '"' . $value . '"';
			}

			$img_attrs_str .= $dimensions_attrs;
		?>
		<img <?php echo trim( $img_attrs_str ); ?> />
		<?php if ( $settings->hover_effects != 'none' || $settings->overlay_effects != 'none' || $settings->show_captions == 'hover' ) { ?>
			<div class="pp-gallery-overlay">
				<div class="pp-overlay-inner">
					<?php if ( $settings->show_captions == 'hover' ) : ?>
					<div class="pp-photo-gallery-caption pp-caption"><?php echo $photo->caption; ?></div>
					<?php endif; ?>
					<?php if ( $settings->icon == '1' && $settings->overlay_icon != '' ) : ?>
					<div class="pp-overlay-icon">
						<span class="<?php echo esc_attr( $settings->overlay_icon ); ?>"></span>
					</div>
					<?php endif; ?>
				</div>
			</div>
		<?php } ?>
		<?php if ( $settings->click_action != 'none' ) { ?>
		</a>
		<?php } ?>
		<?php if ( isset( $photo->sizes ) ) : ?>
			<meta itemprop="width" content="<?php echo $photo->sizes['width']; ?>" />
			<meta itemprop="height" content="<?php echo $photo->sizes['height']; ?>" />
		<?php endif; ?>
		<?php if ( $photo && ! empty( $photo->caption ) && 'below' == $settings->show_captions ) { ?>
		<div class="pp-photo-gallery-caption pp-photo-gallery-caption-below" itemprop="caption"><?php echo $photo->caption; ?></div>
		<?php } ?>
		<?php if ( $photo && ! empty( $photo->caption ) && 'yes' == $settings->lightbox_caption && empty( $settings->show_captions ) ) { ?>
		<div class="pp-photo-gallery-caption pp-photo-gallery-caption-lightbox" itemprop="caption"><?php echo $photo->caption; ?></div>
		<?php } ?>
	</div>
</div>