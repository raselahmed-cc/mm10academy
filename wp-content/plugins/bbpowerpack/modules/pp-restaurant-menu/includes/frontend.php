<?php
	$enable_tabindex = false;
	$tabindex = -1;
	if ( isset( $settings->card_tabindex ) && 'yes' == $settings->card_tabindex ) {
		$enable_tabindex = true;
		$tabindex = 0;
		if ( isset( $settings->card_custom_tabindex ) && '' != $settings->card_custom_tabindex ) {
			$tabindex = $settings->card_custom_tabindex;
		}
	}

	$heading_tag    = isset( $settings->menu_heading_tag ) ? esc_attr( $settings->menu_heading_tag ) : 'h3';
	$item_title_tag = isset( $settings->items_title_tag ) ? esc_attr( $settings->items_title_tag ) : 'h2';
?>
<div class="pp-restaurant-menu-item-wrap">
	<?php if ( ! empty( $settings->menu_heading ) ) {
		echo sprintf( '<%1$s class="pp-restaurant-menu-heading">%2$s</%1$s>', $heading_tag, $settings->menu_heading );
	} ?>
	<div class="pp-restaurant-menu-item-wrap-in">
		<?php
		foreach ( $settings->menu_items as $key => $menu_item ) {
			$item_title = '' != trim( $menu_item->menu_items_title ) ? trim( $menu_item->menu_items_title ) : '';
			$attr = '';

			if ( $enable_tabindex && $tabindex >= 0 ) {
				$attr .= ' tabindex="'.$tabindex.'"';
			}
			 
			if ( $settings->restaurant_menu_layout == 'stacked' ) {
			 	?>
			 	<div class="pp-restaurant-menu-item pp-restaurant-menu-item-<?php echo $key; ?> pp-menu-item pp-menu-item-<?php echo $key; ?>"<?php echo $attr; ?>>
				 	<?php if ( '' != trim( $menu_item->menu_item_images ) && 'yes' == $menu_item->restaurant_select_images ) {
						$image      = $menu_item->menu_item_images_src;
						$image_full = wp_get_attachment_image_src( $menu_item->menu_item_images, 'full' );
						$image_full = is_array( $image_full ) ? $image_full[0] : $image;
						$href       = ( '' != $menu_item->menu_items_link ) ? esc_url( $menu_item->menu_items_link ) : '';
						$href       = isset( $settings->item_lightbox ) && 'yes' === $settings->item_lightbox ? $image_full : $href;
						?>
						<div class="pp-restaurant-menu-item-images">
							<?php if ( ! empty( $href ) ) { ?>
							<a href="<?php echo $href; ?>" target="<?php echo $menu_item->menu_items_link_target; ?>"<?php if('yes' == $menu_item->menu_items_link_nofollow){ echo " rel='nofollow'"; } ?>>
							<?php } ?>
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo pp_get_image_alt( $menu_item->menu_item_images, $item_title ); ?>" />
							<?php if ( ! empty( $href ) ) { ?>
							</a>
							<?php } ?>
						</div>
					<?php } ?>
					<div class="pp-restaurant-menu-item-left">
						<?php if ( '' != $item_title ) { ?>
							<<?php echo $item_title_tag; ?> class="pp-restaurant-menu-item-header">
								<?php if ( '' != trim( $menu_item->menu_items_link ) ) { ?>
									<a href="<?php echo esc_url( $menu_item->menu_items_link ); ?>" target="<?php echo esc_attr( $menu_item->menu_items_link_target );?>"<?php if('yes' == $menu_item->menu_items_link_nofollow){ echo " rel='nofollow'"; } ?> class="pp-restaurant-menu-item-title"><?php echo $item_title; ?></a>
								<?php } else { ?>
									<span class="pp-restaurant-menu-item-title"><?php echo $item_title; ?></span>
								<?php } ?>
							</<?php echo $item_title_tag; ?>>
						<?php } ?>
						<?php if ( $settings->show_description == 'yes' ) { ?>
							<div class="pp-restaurant-menu-item-description">
								<?php echo $menu_item->menu_item_description; ?>
							</div>
						<?php } ?>
					</div>
					<div class="pp-restaurant-menu-item-right">
						<?php if ( '' != trim( $menu_item->menu_items_price ) && $settings->show_price == 'yes' ) { ?>
							<div class="pp-restaurant-menu-item-price pp-menu-item-currency-<?php echo $settings->currency_symbol_pos; ?>">
								<?php if ( ! isset( $settings->currency_symbol_pos ) || 'left' === $settings->currency_symbol_pos ) { ?>
									<span class="pp-menu-item-currency"><?php echo $settings->currency_symbol; ?></span>
								<?php } ?>
								<?php echo $menu_item->menu_items_price; ?>
								<?php if ( isset( $settings->currency_symbol_pos ) && 'right' === $settings->currency_symbol_pos ) { ?>
									<span class="pp-menu-item-currency"><?php echo $settings->currency_symbol; ?></span> 
								<?php } ?>
								<?php if ( '' != trim( $menu_item->menu_items_unit ) ) { ?>
									<span class="pp-menu-item-unit"> <?php echo trim( $menu_item->menu_items_unit ); ?></span>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php
			 	} else {
			 	?>
			 		<div class="pp-restaurant-menu-item-inline pp-restaurant-menu-item-inline-<?php echo $key; ?> pp-menu-item pp-menu-item-<?php echo $key; ?>"<?php echo $attr; ?>>
				 		<?php if ( '' != trim( $menu_item->menu_item_images ) && 'yes' == $menu_item->restaurant_select_images ) {
							$image      = $menu_item->menu_item_images_src;
							$image_full = wp_get_attachment_image_src( $menu_item->menu_item_images, 'full' );
							$image_full = is_array( $image_full ) ? $image_full[0] : $image;
							$href       = ( '' != $menu_item->menu_items_link ) ? esc_url( $menu_item->menu_items_link ) : '';
							$href       = isset( $settings->item_lightbox ) && 'yes' === $settings->item_lightbox ? $image_full : $href;
							?>
							<div class="pp-restaurant-menu-item-images">
								<?php if ( ! empty( $href ) ) { ?>
								<a href="<?php echo $href; ?>" target="<?php echo $menu_item->menu_items_link_target; ?>"<?php if('yes' == $menu_item->menu_items_link_nofollow){ echo " rel='nofollow'"; } ?>>
								<?php } ?>
									<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo pp_get_image_alt($menu_item->menu_item_images, $item_title); ?>" />
								<?php if ( ! empty( $href ) ) { ?>
								</a>
								<?php } ?>
							</div>
			   	 		<?php } ?>
			   	 		<div class="pp-restaurant-menu-item-inline-right-content pp-menu-item-content">
			   	 			<?php if ( '' != $item_title ) { ?>
					   	 		<<?php echo $item_title_tag; ?> class="pp-restaurant-menu-item-header">
									<?php if ( '' != trim($menu_item->menu_items_link) ) { ?>
										<a href="<?php echo esc_url( $menu_item->menu_items_link ); ?>" target="<?php echo esc_attr( $menu_item->menu_items_link_target );?>"<?php if('yes' == $menu_item->menu_items_link_nofollow){ echo " rel='nofollow'"; } ?> class="pp-restaurant-menu-item-title"><?php echo $item_title; ?></a>
									<?php } else { ?>
										<span class="pp-restaurant-menu-item-title"><?php echo $item_title; ?></span>
									<?php } ?>
								</<?php echo $item_title_tag; ?>>
							<?php } ?>
							<?php if ( $settings->show_description == 'yes' ) { ?>
								<div class="pp-restaurant-menu-item-description">
									<?php echo $menu_item->menu_item_description; ?>
								</div>
							<?php } ?>
						</div>
						<?php if ( '' != trim( $menu_item->menu_items_price ) && $settings->show_price == 'yes' ) { ?>
							<div class="pp-restaurant-menu-item-price pp-menu-item-currency-<?php echo esc_attr( $settings->currency_symbol_pos ); ?>">
								<?php if ( ! isset( $settings->currency_symbol_pos ) || 'left' === $settings->currency_symbol_pos ) { ?>
									<span class="pp-menu-item-currency"><?php echo $settings->currency_symbol; ?></span>
								<?php } ?>
								<?php echo $menu_item->menu_items_price; ?>
								<?php if ( isset( $settings->currency_symbol_pos ) && 'right' === $settings->currency_symbol_pos ) { ?>
									<span class="pp-menu-item-currency"><?php echo $settings->currency_symbol; ?></span> 
								<?php } ?>
								<?php if ( '' != trim( $menu_item->menu_items_unit ) ) { ?>
									<span class="pp-menu-item-unit"> <?php echo trim( $menu_item->menu_items_unit ); ?></span>
								<?php } ?>
							</div>
						<?php } ?>
			 		</div>
			 	<?php
			}
		?>
		<?php } ?>
	</div>
</div>
