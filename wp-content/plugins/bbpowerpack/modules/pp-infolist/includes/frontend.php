<?php
$title_tag   = isset( $settings->title_tag ) ? esc_attr( $settings->title_tag ) : 'h3';
$source      = $module->get_data_source();
$list_items  = $module->get_list_items();

if ( ! is_array( $list_items ) || empty( $list_items ) ) {
	return;
}

$items_count = count( $list_items );
$layout      = absint( $settings->layouts );
$classes     = array(
	'pp-infolist',
	'layout-' . $layout,
);
?>
<div class="pp-infolist-wrap">
	<div class="<?php echo implode( ' ', $classes ); ?>">
		<ul class="pp-list-items">
		<?php
		for ( $i = 0; $i < $items_count; $i++ ) {
			if ( ! is_object( $list_items[ $i ] ) ) {
				continue;
			}
			$item = $list_items[ $i ];
			$classes = '';
			if ( 'manual' === $source && $item->icon_animation ) {
				$classes = $item->icon_animation;
			} else {
				$classes = '';
			}
		?>
			<li class="pp-list-item pp-list-item-<?php echo $i; ?>">
				<?php include $module->dir . 'includes/layout.php'; ?>
			</li>
		<?php } ?>
		</ul>
	</div>
</div>
