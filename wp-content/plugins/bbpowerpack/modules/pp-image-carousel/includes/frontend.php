<?php
$photos = $module->get_photos();

if ( empty( $photos ) ) {
	return;
}

$aria_label = isset( $settings->sr_text ) && ! empty( $settings->sr_text ) ? esc_attr( $settings->sr_text ) : __( 'Slider', 'bb-powerpack' );
$lazy_load = isset( $settings->lazy_load ) && 'yes' === $settings->lazy_load && ! FLBuilderModel::is_builder_active();
$captions = array();
$count = 1;
?>
<div class="pp-image-carousel-wrapper<?php echo ($settings->pagination_position && $settings->carousel_type != 'slideshow') ? ' pp-nav-' . esc_attr( $settings->pagination_position ) : ''; ?>">
	<?php
	if ( isset( $settings->thumb_position ) && 'above' == $settings->thumb_position ) {
		include $module->dir . 'includes/thumbnails.php';
	}
	?>
	<div class="pp-image-carousel swiper swiper-container slider-type-<?php echo $settings->carousel_type; ?>" role="region" aria-label="<?php echo $aria_label; ?>">
		<div class="swiper-wrapper">
			<?php foreach( $photos as $photo ) :
				if ( ! is_object( $photo ) ) {
					continue;
				}
				$caption = strip_tags( preg_replace( '/\"|\'/', '', $photo->caption ) );
				$caption = empty( trim( $caption ) ) ? sprintf( __( 'Slide %d', 'bb-powerpack' ), $count ) : $caption;
				$captions[] = $caption;
				$dimensions_attrs = '';

				if ( isset( $photo->sizes ) && ! empty( $photo->sizes['width'] ) && ! empty( $photo->sizes['height'] ) ) {
					$dimensions_attrs = ' width="' . $photo->sizes['width'] . '" height="' . $photo->sizes['height'] . '"';
				}
				?>
				<div class="pp-image-carousel-item<?php echo ( ( $settings->click_action != 'none' ) && !empty( $photo->link ) ) ? ' pp-image-carousel-link' : ''; ?> swiper-slide" role="group" aria-label="<?php echo $caption; ?>">
					<?php if( $settings->click_action != 'none' ) : ?>
							<?php $click_action_link = 'javascript:void(0)';
								$click_action_target = $settings->custom_link_target;
								$click_action_rel = ( '_blank' === $click_action_target ) ? ' rel="nofollow noopener"' : '';

								if ( $settings->click_action == 'custom-link' ) {
									if ( ! empty( $photo->cta_link ) ) {
										$click_action_link = $photo->cta_link;
									}
								}

								if ( $settings->click_action == 'lightbox' ) {
									$click_action_link = $photo->link;
								}

							?>
					<a href="<?php echo esc_url( $click_action_link ); ?>" target="<?php echo esc_attr( $click_action_target ); ?>"<?php echo $click_action_rel; ?> data-caption="<?php echo htmlspecialchars( $photo->caption ); ?>" aria-label="<?php echo htmlspecialchars( $photo->caption ); ?>">
					<?php endif; ?>

					<div class="pp-carousel-image-container">
						<figure class="swiper-slide-inner<?php echo ( ! isset( $settings->use_image_as ) || 'background' === $settings->use_image_as ) ? ' use-as-background' : ''; ?>">
							<?php
							if ( isset( $photo->srcset ) ) {
								$srcset = apply_filters( 'pp_image_carousel_output_image_srcset', true ) ? esc_attr( $photo->srcset ) : '';
							}
							$img_attrs = array(
								'class' => 'swiper-slide-image',
								'src' => esc_url( $photo->src ),
								'alt' => esc_attr( $photo->alt ),
							);

							if ( isset( $srcset ) && ! empty( $srcset ) ) {
								$img_attrs['srcset'] = $srcset;
							}

							if ( $lazy_load ) {
								$img_attrs['class'] .= ' swiper-lazy';
								$img_attrs['data-src'] = $img_attrs['src'];
								//$img_attrs['loading'] = 'lazy';
								unset( $img_attrs['src'] );
								if ( isset( $img_attrs['srcset'] ) ) {
									$img_attrs['data-srcset'] = $img_attrs['srcset'];
									unset( $img_attrs['srcset'] );
								}
							}
				
							$img_attrs = apply_filters( 'pp_image_carousel_image_html_attrs', $img_attrs, $photo, $settings );

							$img_attrs_str = '';

							foreach ( $img_attrs as $key => $value ) {
								$img_attrs_str .= ' ' . $key . '=' . '"' . $value . '"';
							}

							$img_attrs_str .= $dimensions_attrs;
							?>
							
							<img <?php echo trim( $img_attrs_str ); ?> />
							
							<?php if ( $lazy_load ) { ?>
							<div class="swiper-lazy-preloader"></div>
							<?php } ?>

							<?php if ( $settings->overlay != 'none' && false === strpos( $settings->class, 'caption-bottom' ) ) : ?>
								<!-- Overlay Wrapper -->
								<div class="pp-image-overlay <?php echo $settings->overlay_effects; ?>">
									<?php if ( $settings->overlay == 'text' ) : ?>
										<div class="pp-caption">
											<span><?php echo $photo->caption; ?></span>
										</div>
									<?php endif; ?>

									<?php if ( $settings->overlay == 'icon' ) : ?>
									<div class="pp-overlay-icon">
										<span class="<?php echo esc_attr( $settings->overlay_icon ); ?>" aria-hidden="true"></span>
									</div>
									<?php endif; ?>
								</div> <!-- Overlay Wrapper Closed -->
							<?php endif; ?>
						</figure>
						<?php if ( false !== strpos( $settings->class, 'caption-bottom' ) ) { ?>
							<div class="pp-caption">
								<span><?php echo $photo->caption; ?></span>
							</div>
						<?php } ?>
					</div>

					<?php if( $settings->click_action != 'none' ) : ?>
					</a>
					<?php endif; ?>
				</div>
				<?php
				$count++;
			endforeach;
			?>
		</div>
		<?php if ( 1 < count( $photos ) ) { ?>
			<?php if ( $settings->slider_navigation == 'yes' && 'slideshow' === $settings->carousel_type ) { ?>
			<!-- navigation arrows -->
			<button class="pp-swiper-button swiper-button-prev" aria-label="<?php echo isset( $settings->prev_nav_sr_text ) && ! empty( $settings->prev_nav_sr_text ) ? htmlspecialchars( $settings->prev_nav_sr_text ) : __( 'Previous slide', 'bb-powerpack' ); ?>" role="button" tabindex="0">
				<?php pp_prev_icon_svg(); ?>
			</button>
			<button class="pp-swiper-button swiper-button-next" aria-label="<?php echo isset( $settings->next_nav_sr_text ) && ! empty( $settings->next_nav_sr_text ) ? htmlspecialchars( $settings->next_nav_sr_text ) : __( 'Next slide', 'bb-powerpack' ); ?>" role="button" tabindex="0">
				<?php pp_next_icon_svg(); ?>
			</button>
			<?php } ?>
			<?php if ( 'none' !== $settings->pagination_type ) { ?>
			<div class="swiper-pagination" data-captions="<?php echo htmlspecialchars( json_encode( $captions ) ); ?>"></div>
			<?php } ?>
		<?php } ?>
	</div>
	<?php if ( 1 < count( $photos ) ) : ?>
		<?php if ( $settings->slider_navigation == 'yes' && 'slideshow' !== $settings->carousel_type ) { ?>
		<!-- navigation arrows -->
		<button class="pp-swiper-button swiper-button-prev" aria-label="<?php echo isset( $settings->prev_nav_sr_text ) && ! empty( $settings->prev_nav_sr_text ) ? htmlspecialchars( $settings->prev_nav_sr_text ) : __( 'Previous slide', 'bb-powerpack' ); ?>" role="button" tabindex="0">
			<?php pp_prev_icon_svg(); ?>
		</button>
		<button class="pp-swiper-button swiper-button-next" aria-label="<?php echo isset( $settings->next_nav_sr_text ) && ! empty( $settings->next_nav_sr_text ) ? htmlspecialchars( $settings->next_nav_sr_text ) : __( 'Next slide', 'bb-powerpack' ); ?>" role="button" tabindex="0">
			<?php pp_next_icon_svg(); ?>
		</button>
		<?php } ?>
	<?php endif; ?>
	<?php
	if ( 1 < count( $photos ) ) {
		if ( isset( $settings->thumb_position ) && 'below' == $settings->thumb_position ) {
			include $module->dir . 'includes/thumbnails.php';
		}
		if ( ! isset( $settings->thumb_position ) ) {
			include $module->dir . 'includes/thumbnails.php';
		}
	}
	?>
</div>