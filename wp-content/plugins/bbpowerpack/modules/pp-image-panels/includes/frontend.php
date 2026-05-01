<div class="pp-image-panels-wrap">
	<div class="pp-image-panels-inner">
		<?php
		$title_tag = isset( $settings->title_tag ) ? $settings->title_tag : 'h3';
		$panels_count = count( $settings->image_panels );

		for ( $i = 0; $i < $panels_count; $i++ ) {
			if ( ! is_object( $settings->image_panels[ $i ] ) ) {
				continue;
			}
			$panel = $settings->image_panels[ $i ];
			$link = $panel->link;
			if ( 'lightbox' == $panel->link_type ) {
				$link = $panel->photo_src;
			}
			$has_link = ( 'panel' == $panel->link_type || 'lightbox' == $panel->link_type );
		?>
		<?php if ( $has_link ) { ?>
			<a class="pp-panel pp-panel-<?php echo $i; ?> pp-panel-item pp-panel-link <?php echo ( 'lightbox' == $panel->link_type ) ? ' pp-panel-has-lightbox' : ''; ?>" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $panel->link_target ); ?>" style="width: <?php echo 100 / ( $panels_count ); ?>%;">
		<?php } else { ?>
			<div class="pp-panel pp-panel-<?php echo $i; ?> pp-panel-item" style="width: <?php echo 100 / ( $panels_count ); ?>%">
		<?php } ?>
			<?php if ( $panel->title ) { ?>
				<div class="pp-panel-title">
					<?php if ( $panel->link_type == 'title' ) { ?>
					<a class="pp-panel-link" href="<?php echo $link; ?>" target="<?php echo esc_attr( $panel->link_target ); ?>">
					<?php } ?>
					<<?php echo esc_attr( $title_tag ); ?> class="pp-panel-title-text"><?php echo $panel->title; ?></<?php echo esc_attr( $title_tag ); ?>>
					<?php if ( $panel->link_type == 'title' ) { ?>
					</a>
					<?php } ?>
					<?php if ( isset( $panel->description ) && ! empty( $panel->description ) ) { ?>
						<div class="pp-panel-description"><?php echo $panel->description; ?></div>
					<?php } ?>
				</div>
			<?php } ?>
		<?php if ( $has_link ) { ?>
			</a>
		<?php } else { ?>
			</div>
		<?php } ?>
		<?php
		}
		?>
	</div>
</div>
