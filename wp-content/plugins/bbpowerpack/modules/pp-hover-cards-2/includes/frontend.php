<?php
	$title_tag = isset( $settings->hover_card_title_tag ) ? esc_attr( $settings->hover_card_title_tag ) : 'h2';
	$equal_heights = isset( $settings->equal_heights ) && 'yes' === $settings->equal_heights;
?>
<div class="pp-hover-card-wrap<?php echo ! $equal_heights ? ' pp-clearfix' : ' pp-equal-height'; ?>">
	<?php
	for( $i = 0; $i < count( $settings->card_content ); $i++ ) {
		if( !is_object( $settings->card_content[$i] ) ) {
			continue;
		}
		$card = $settings->card_content[$i];
	?>
		<div class="pp-hover-card pp-hover-card-<?php echo $i; ?> <?php echo esc_attr( $settings->style_type ); ?> pp-clearfix" onclick="" tabindex="0">
			<?php if( $card->hover_card_link_type == 'box' ) { ?>
			<a class="pp-more-link-container" href="<?php echo $card->box_link == '#' ? 'javascript:void(0)' : esc_url( do_shortcode( $card->box_link ) ); ?>" target="<?php echo esc_attr( $card->box_link_target ); ?>">
			<?php } ?>
				<?php if ( $card->hover_card_bg_type == 'image' && isset( $card->hover_card_box_image_src ) ) { ?>
					<img src="<?php echo esc_url( $card->hover_card_box_image_src ); ?>" class="pp-hover-card-image" alt="<?php echo $module->get_image_alt( $card, $card->hover_card_box_image ); ?>" />
				<?php } ?>
				<div class="pp-hover-card-inner">
					<div class="pp-hover-card-inner-wrap">
						<div class="pp-hover-card-content">
							<?php if ( $settings->style_type == 'powerpack-style' ) { ?>
								<div class="pp-hover-card-icon-wrap">
									<?php if ( $card->hover_card_image_select == 'icon' ) { ?>
									<span class="pp-hover-card-icon <?php echo esc_attr( $card->hover_card_font_icon ); ?>"></span>
									<?php } ?>
									<?php if ( $card->hover_card_image_select == 'image' && ! empty( $card->hover_card_custom_icon ) ) { ?>
									<span class="pp-hover-card-icon-image"><img src="<?php echo esc_url( $card->hover_card_custom_icon_src ); ?>" alt="<?php echo $module->get_image_alt( $card, $card->hover_card_custom_icon ); ?>" /></span>
									<?php } ?>
								</div>
							<?php } ?>
							<div class="pp-hover-card-title-wrap">
								<<?php echo $title_tag; ?> class="pp-hover-card-title"><?php echo $card->title; ?></<?php echo $title_tag; ?>>
							</div>
							<div class="pp-hover-card-description">
								<div class="pp-hover-card-description-inner">
									<?php echo wpautop( $card->hover_content ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if ( $card->hover_card_bg_type == 'image' ) { ?>
					<div class="pp-hover-card-overlay"></div>
				<?php } ?>
			<?php if( $card->hover_card_link_type == 'box' ) { ?>
			</a>
			<?php } ?>
		</div>
		<?php
	}
	?>
</div>
