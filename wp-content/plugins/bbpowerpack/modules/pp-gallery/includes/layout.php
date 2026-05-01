<?php
$dimensions_attrs = '';

if ( isset( $photo->sizes ) && ! empty( $photo->sizes['width'] ) && ! empty( $photo->sizes['height'] ) ) {
	$dimensions_attrs = ' width="' . $photo->sizes['width'] . '" height="' . $photo->sizes['height'] . '"';
}
?>
<div class="<?php echo $item_class; ?>" data-item-id="<?php echo $photo->id; ?>" itemprop="associatedMedia" itemscope="itemscope" itemtype="http://schema.org/ImageObject">
	<div class="pp-photo-gallery-content">
		<?php
		if ( 'none' != $settings->click_action ) :
			$click_action_link = 'javascript:void(0)';
			if ( 'custom-link' == $settings->click_action ) {
				$click_action_target = $settings->custom_link_target;
				if ( ! empty( $photo->cta_link ) ) {
					$click_action_link = $photo->cta_link;
				}
			}

			if ( 'lightbox' == $settings->click_action ) {
				$click_action_link = $photo->link;
			}
		?>
		<a
			href="<?php echo esc_url( $click_action_link ); ?>"
			<?php if ( 'custom-link' == $settings->click_action ) { ?> 
			target="<?php echo esc_attr( $click_action_target ); ?>" 
			<?php if ( '_blank' === $click_action_target && ( ! isset( $settings->custom_link_nofollow ) || 'yes' === $settings->custom_link_nofollow ) ) { ?>
			rel="nofollow noopener"
			<?php } ?>
			<?php } ?> 
			<?php if ( 'lightbox' == $settings->click_action ) { ?>
			data-fancybox="images" 
			<?php } ?> 
			title="<?php echo $photo->title; ?>" 
			<?php if ( isset( $settings->lightbox_caption ) && 'yes' == $settings->lightbox_caption ) { ?>
			data-caption="<?php echo $photo->caption; ?>"
			<?php } ?>
			data-description="<?php echo $photo->description; ?>" 
			itemprop="contentUrl"
		>
		<?php endif; ?>

		<?php 
			$srcset = apply_filters( 'pp_gallery_output_image_srcset', false ) ? esc_attr( $photo->srcset ) : '';
			$img_attrs = array(
				'class' => 'pp-gallery-img',
				'src' => $photo->src,
				'alt' => $photo->alt,
				'data-no-lazy' => 1,
				'itemprop' => 'thumbnail',
			);

			if ( ! empty( $srcset ) ) {
				$img_attrs['srcset'] = $srcset;
			}

			$img_attrs = apply_filters( 'pp_gallery_image_html_attrs', $img_attrs, $photo, $settings );

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

			<!-- Overlay Wrapper -->
			<div class="pp-gallery-overlay">
				<div class="pp-overlay-inner">

					<?php if ( 'hover' == $settings->show_captions || ( 'justified' == $settings->gallery_layout && 'no' != $settings->show_captions ) ) : ?>
						<div class="pp-caption" itemprop="caption description">
							<?php echo htmlspecialchars_decode( $photo->caption ); ?>
						</div>
					<?php endif; ?>

					<?php if ( '1' == $settings->icon && '' != $settings->overlay_icon ) : ?>
					<div class="pp-overlay-icon">
						<span class="<?php echo esc_attr( $settings->overlay_icon ); ?>"></span>
					</div>
					<?php endif; ?>

				</div>
			</div> <!-- Overlay Wrapper Closed -->

		<?php if ( 'none' != $settings->click_action ) : ?>
		</a>
		<?php endif; ?>
		<?php if ( isset( $photo->sizes ) ) : ?>
			<meta itemprop="width" content="<?php echo $photo->sizes['width']; ?>" />
			<meta itemprop="height" content="<?php echo $photo->sizes['height']; ?>" />
		<?php endif; ?>
	</div>
	<?php if ( $photo && ! empty( $photo->caption ) && 'below' == $settings->show_captions && 'justified' != $settings->gallery_layout ) : ?>
	<div class="pp-photo-gallery-caption pp-photo-gallery-caption-below" itemprop="caption description"><?php echo htmlspecialchars_decode( $photo->caption ); ?></div>
	<?php endif; ?>
</div>