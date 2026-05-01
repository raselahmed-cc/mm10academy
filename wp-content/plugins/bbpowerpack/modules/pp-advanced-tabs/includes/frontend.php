<?php
$activeTabIndex = absint( do_shortcode( $settings->tab_default ) );
$activeTabIndex = $activeTabIndex > count( $settings->items ) ? 0 : $activeTabIndex;
$activeTabIndex = $activeTabIndex < 1 ? 0 : $activeTabIndex - 1;

if ( ! empty( $settings->tab_id_prefix ) ) {
	$idPrefix       = esc_attr( $settings->tab_id_prefix );
	$activeTabId    = isset( $_GET[ $idPrefix ] ) ? esc_attr( $_GET[ $idPrefix ] ) : '';
	$activeTabIndex = ! empty( $activeTabId ) ? ( absint( $activeTabId ) - 1 ) : $activeTabIndex;
}

$items = $module->get_tabs_items( $id );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}
?>

<div class="pp-tabs pp-tabs-<?php echo esc_attr( $settings->layout ); ?><?php echo isset( $settings->vertical_position ) ? ' pp-tabs-vertical-' . esc_attr( $settings->vertical_position ) : ''; ?> pp-tabs-<?php echo esc_attr( $settings->tab_style ); ?> pp-clearfix">

	<div class="pp-tabs-labels pp-clearfix" role="tablist">
		<?php
		for ( $i = 0; $i < count( $items ); $i++ ) :
			if ( ! is_object( $items[ $i ] ) ) {
				continue;
			}
			$item = $items[ $i ];
		?>
		<div id="<?php echo $item->html_id; ?>" class="pp-tabs-label<?php echo ( $i == $activeTabIndex ) ? ' pp-tab-active' : ''; ?> <?php echo 'pp-tab-icon-' . esc_attr( $settings->tab_icon_position ); ?>" data-index="<?php echo $i; ?>" role="tab" tabindex="-1" aria-selected="<?php echo ( $i == $activeTabIndex ) ? 'true' : 'false'; ?>" aria-controls="<?php echo $item->html_id; ?>-content">
			<div class="pp-tab-label-inner">
				<div class="pp-tab-label-flex">
				<?php if ( $settings->tab_icon_position == 'left' || $settings->tab_icon_position == 'top' ) { ?>
					<?php $module->render_tab_item_icon( $item ); ?>
				<?php } ?>

				<div class="pp-tab-title"><?php echo $item->label; ?></div>

				<?php if ( $settings->tab_icon_position == 'right' || $settings->tab_icon_position == 'bottom' ) { ?>
					<?php $module->render_tab_item_icon( $item ); ?>
				<?php } ?>
				</div>
				<?php if ( isset( $item->description ) && ! empty( $item->description ) ) { ?>
					<div class="pp-tab-description">
						<?php echo $item->description; ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php endfor; ?>
	</div>

	<div class="pp-tabs-panels pp-clearfix">
		<?php
		for ( $i = 0; $i < count( $items ); $i++ ) :
			if ( ! is_object( $items[ $i ] ) ) {
				continue;
			}
			$item = $items[ $i ];
		?>
		<div class="pp-tabs-panel"<?php if ( ! empty( $settings->id ) ) { echo ' id="' . sanitize_html_class( $settings->id ) . '-' . $i . '"';} ?>>
			<div class="pp-tabs-label pp-tabs-panel-label<?php if ( $i == $activeTabIndex ) { echo ' pp-tab-active';} ?> <?php echo 'pp-tab-icon-' . esc_attr( $settings->tab_icon_position ); ?>" data-index="<?php echo $i; ?>">
				<div class="pp-tab-label-inner">
					<div class="pp-tab-label-flex">
						<?php if ( $settings->tab_icon_position == 'left' || $settings->tab_icon_position == 'top' ) { ?>
							<?php $module->render_tab_item_icon( $item ); ?>
						<?php } ?>
						<div class="pp-tab-label-wrap">
							<div class="pp-tab-title"><?php echo $item->label; ?></div>
							<?php if ( $settings->tab_icon_position == 'right' || $settings->tab_icon_position == 'bottom' ) { ?>
								<?php $module->render_tab_item_icon( $item ); ?>
							<?php } ?>
							<?php if ( isset( $item->description ) && ! empty( $item->description ) ) { ?>
								<div class="pp-tab-description">
									<?php echo $item->description; ?>
								</div>
							<?php } ?>
						</div>
					</div>

					<?php if ( $settings->tab_open_icon != '' ) { ?>
						<span class="pp-toggle-icon pp-tab-open <?php echo $settings->tab_open_icon; ?>"></span>
					<?php } else { ?>
						<i class="pp-toggle-icon pp-tab-open fa fa-plus"></i>
					<?php } ?>

					<?php if ( $settings->tab_close_icon != '' ) { ?>
						<span class="pp-toggle-icon pp-tab-close <?php echo $settings->tab_close_icon; ?>"></span>
					<?php } else { ?>
						<i class="pp-toggle-icon pp-tab-close fa fa-minus"></i>
					<?php } ?>
				</div>
			</div>
			<div id="<?php echo $item->html_id; ?>-content" class="pp-tabs-panel-content pp-clearfix<?php if ( $i == $activeTabIndex ) { echo ' pp-tab-active';} ?>" data-index="<?php echo $i; ?>" role="tabpanel" aria-labelledby="<?php echo $item->html_id; ?>">
				<?php $module->render_content( $item ); ?>
			</div>
		</div>
		<?php endfor; ?>
	</div>

</div>
