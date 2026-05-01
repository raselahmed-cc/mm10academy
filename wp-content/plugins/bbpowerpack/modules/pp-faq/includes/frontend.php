<?php
$css_id        = '';
$qus_tag       = isset( $settings->qus_tag ) ? esc_attr( $settings->qus_tag ) : 'h3';
$items         = $module->get_faq_items();
$icon_position = $settings->faq_toggle_icon_position;

if ( ! is_array( $items ) || empty( $items ) ) {
	return;
}

if ( ! empty( $settings->faq_open_icon ) ) {
	$open_icon_class = 'pp-faq-button-icon pp-faq-open ' . esc_attr( $settings->faq_open_icon ) . ' pp-faq-icon-' . esc_attr( $icon_position );
} else {
	$open_icon_class = 'pp-faq-button-icon pp-faq-open fa fa-plus pp-faq-icon-' . esc_attr( $icon_position );
}
if ( ! empty( $settings->faq_close_icon ) ) {
	$close_icon_class = 'pp-faq-button-icon pp-faq-close ' . esc_attr( $settings->faq_close_icon ) . ' pp-faq-icon-' . esc_attr( $icon_position );
} else {
	$close_icon_class = 'pp-faq-button-icon pp-faq-close fa fa-minus pp-faq-icon-' . esc_attr( $icon_position );
}

$module->maybe_render_schema( true, $items );
?>

<div class="pp-faq <?php echo ( 'yes' === $settings->collapse ) ? 'pp-faq-collapse' : ''; ?>">
	<?php
	for ( $i = 0; $i < count( $items ); $i++ ) :
		if ( ! is_object( $items[ $i ] ) || empty( $items[ $i ] ) ) {
			continue;
		}

		$css_id = ! empty( $settings->faq_id_prefix ) ? esc_attr( $settings->faq_id_prefix ) . '-' . ( $i + 1 ) : 'pp-faq-' . $id . '-' . ( $i + 1 );
		?>
		<div id="<?php echo $css_id; ?>" class="pp-faq-item">
			<div id="pp-faq-<?php echo $id; ?>-tab-<?php echo $i; ?>" class="pp-faq-button" aria-controls="pp-faq-<?php echo $id; ?>-panel-<?php echo $i; ?>" tabindex="0" role="button">
				<?php if ( 'left' === $icon_position ) { ?>
					<span class="<?php echo $open_icon_class; ?>" aria-hidden="true"></span>
					<span class="<?php echo $close_icon_class; ?>" aria-hidden="true"></span>
				<?php } ?>

				<<?php echo $qus_tag; ?> class="pp-faq-button-label"><?php echo $items[ $i ]->faq_question; ?></<?php echo $qus_tag; ?>>

				<?php if ( 'right' === $icon_position ) { ?>
					<span class="<?php echo $open_icon_class; ?>" aria-hidden="true"></span>
					<span class="<?php echo $close_icon_class; ?>" aria-hidden="true"></span>
				<?php } ?>
			</div>
			<div class="pp-faq-content fl-clearfix" id="pp-faq-<?php echo $id; ?>-panel-<?php echo $i; ?>" aria-labelledby="pp-faq-<?php echo $id; ?>-tab-<?php echo $i; ?>" aria-hidden="<?php echo ( $i > 0 || 'first' !== $settings->expand_option ) ? 'true' : 'false'; ?>" aria-live="polite">
				<div class="pp-faq-content-text">
					<?php $module->render_content( $items[ $i ] ); ?>
				</div>
			</div>
		</div>
	<?php endfor; ?>
</div>
