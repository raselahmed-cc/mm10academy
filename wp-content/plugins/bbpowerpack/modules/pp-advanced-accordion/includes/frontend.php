<?php
$items = $module->get_accordion_items( $id );

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

$icon_position  = $settings->accordion_icon_position;
$label_html_tag = isset( $settings->label_html_tag ) ? esc_attr( $settings->label_html_tag ) : 'span';

$activeItemIndex = $settings->open_first ? 1 : ( absint( $settings->open_custom ) > 0 ? absint( $settings->open_custom ) : '' );
if ( '' !== $activeItemIndex ) {
	$activeItemIndex = ( $activeItemIndex > count( $items ) || $activeItemIndex < 1 ) ? 1 : $activeItemIndex;
	$activeItemIndex = $activeItemIndex - 1;
}

if ( ! empty( $settings->accordion_id_prefix ) ) {
	$idPrefix        = esc_attr( $settings->accordion_id_prefix );
	$activeItemId    = isset( $_GET[ $idPrefix ] ) ? esc_attr( $_GET[ $idPrefix ] ) : '';
	$activeItemIndex = ! empty( $activeItemId ) ? ( absint( $activeItemId ) - 1 ) : $activeItemIndex;
}

if ( ! empty( $settings->accordion_open_icon ) ) {
	$open_icon_class = 'pp-accordion-button-icon pp-accordion-open ' . esc_attr( $settings->accordion_open_icon ) . ' pp-accordion-icon-' . esc_attr( $icon_position );
} else {
	$open_icon_class = 'pp-accordion-button-icon pp-accordion-open fa fa-plus pp-accordion-icon-' . esc_attr( $icon_position );
}
if ( ! empty( $settings->accordion_close_icon ) ) {
	$close_icon_class = 'pp-accordion-button-icon pp-accordion-close ' . esc_attr( $settings->accordion_close_icon ) . ' pp-accordion-icon-' . esc_attr( $icon_position );
} else {
	$close_icon_class = 'pp-accordion-button-icon pp-accordion-close fa fa-minus pp-accordion-icon-' . esc_attr( $icon_position );
}

$module->maybe_render_schema( true, $items );
?>

<div class="pp-accordion<?php echo ( $settings->collapse ) ? ' pp-accordion-collapse' : ''; ?>">
	<?php
	for ( $i = 0; $i < count( $items ); $i++ ) :
		if ( empty( $items[ $i ] ) ) {
			continue;
		}

		?>
		<div id="<?php echo $items[ $i ]->html_id; ?>" class="pp-accordion-item<?php echo $i === $activeItemIndex ? ' pp-accordion-item-active' : ''; ?>" data-item="<?php echo $i; ?>">
			<div 
				class="pp-accordion-button" 
				id="pp-accordion-<?php echo $module->node; ?>-tab-<?php echo $i; ?>" 
				aria-controls="pp-accordion-<?php echo $module->node; ?>-panel-<?php echo $i; ?>" 
				tabindex="0" 
				role="button">
				<?php if ( 'left' === $icon_position ) { ?>
					<span class="<?php echo $open_icon_class; ?>" aria-hidden="true"></span>
					<span class="<?php echo $close_icon_class; ?>" aria-hidden="true"></span>
				<?php } ?>

				<?php $module->render_accordion_item_icon( $items[ $i ] ); ?>

				<<?php echo $label_html_tag; ?> class="pp-accordion-button-label" itemprop="name description"><?php echo $items[ $i ]->label; ?></<?php echo $label_html_tag; ?>>

				<?php if ( 'right' === $icon_position ) { ?>
					<span class="<?php echo $open_icon_class; ?>" aria-hidden="true"></span>
					<span class="<?php echo $close_icon_class; ?>" aria-hidden="true"></span>
				<?php } ?>
			</div>

			<div 
				class="pp-accordion-content fl-clearfix" 
				id="pp-accordion-<?php echo $module->node; ?>-panel-<?php echo $i; ?>" 
				aria-labelledby="pp-accordion-<?php echo $module->node; ?>-tab-<?php echo $i; ?>" 
				aria-hidden="<?php echo $i !== $activeItemIndex ? 'true' : 'false'; ?>" 
				aria-live="polite" 
				role="region"
				<?php echo $i === $activeItemIndex ? ' style="display: block;"' : ''; ?>>
				<?php
				$module->render_content( $items[ $i ] );
				?>
			</div>
		</div>
	<?php endfor; ?>
</div>
