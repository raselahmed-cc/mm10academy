<?php
$filter_labels = $module->get_gallery_filter_ids( $settings->gallery_filter, true );
$all_filter    = ( isset( $settings->show_all_filter_btn ) && 'no' == $settings->show_all_filter_btn ) ? false : true;
$all_text      = ( $settings->show_custom_all_text == 'yes' && $settings->custom_all_text != '' ) ? esc_attr( $settings->custom_all_text ) : esc_html__('All', 'bb-powerpack');
$id_prefix     = ( isset( $settings->custom_id_prefix ) && ! empty( $settings->custom_id_prefix ) ) ? esc_attr( $settings->custom_id_prefix ) : 'pp-gallery-' . $id;
$active_filter = ( isset( $settings->active_filter ) && ! empty( $settings->active_filter ) ) ? absint( $settings->active_filter ) : false;
$has_pagination = isset( $settings->pagination ) && 'yes' === $settings->pagination && ! empty( $settings->per_page );

$item_class = 'pp-gallery-item pp-gallery-' . $settings->gallery_layout . '-item';

if ( ! $active_filter && ! $all_filter ) {
	$active_filter = 1;
}

$photos = $module->get_photos();

if ( count( $filter_labels ) ) : ?>

	<div class="pp-gallery-filters-wrapper">
		<div class="pp-gallery-filters-toggle">
			<span class="toggle-text"><?php echo $all_text; ?></span>
		</div>
		<ul class="pp-gallery-filters">
			<?php if ( $all_filter ) { ?>
			<li id="<?php echo $id_prefix; ?>-0" class="pp-gallery-filter-label <?php echo ! $active_filter ? 'pp-filter-active ' : ''; ?>all" data-filter="*"><?php echo urldecode( $all_text ); ?></li>
			<?php } ?>
		<?php
			for ( $i = 0; $i < count( $settings->gallery_filter ); $i++ ) :
				if ( ! is_object( $settings->gallery_filter[ $i ] ) ) {
					continue;
				}
				$filter       = $settings->gallery_filter[ $i ];
				$filter_label = $filter->filter_label;
			
				if ( ! empty( $filter_label ) ) {
					echo '<li id="' . $id_prefix . '-' . ( $i + 1 ) . '" class="pp-gallery-filter-label'. ( ( $i + 1 ) == $active_filter ? ' pp-filter-active ' : '' ) .'" data-filter=".pp-group-' . ($i+1) . '">' . urldecode( $filter_label ) . '</li>';
				}
			endfor;
		?>
		</ul>
	</div>

<div class="pp-filterable-gallery pp-photo-gallery<?php echo ( $settings->hover_effects != 'none' ) ? ' ' . esc_attr( $settings->hover_effects ) : ''; ?>" itemscope="itemscope" itemtype="https://schema.org/ImageGallery">
<?php
	$count = 0;
	foreach ( $photos as $photo ) {
		if ( $has_pagination && $count >= $settings->per_page ) {
			break;
		}
		include $module->dir . 'includes/gallery-item.php';
		$count++;
	} ?>
	<div class="pp-photo-space"></div>
</div>
<?php else: ?>
	<p><?php _e( 'Please add photos to the gallery.', 'bb-powerpack' ); ?></p>
<?php endif; ?>
<?php if ( $has_pagination && count( $photos ) > $settings->per_page ) { ?>
<div class="pp-filterable-gallery-pagination" data-per-page="<?php echo esc_attr( $settings->per_page ); ?>" data-total="<?php echo count( $photos ); ?>">
	<a href="javascript:void(0)"><?php echo esc_attr( $settings->load_more_text ); ?></a>
	<?php
	$offset_photos = array_slice( $photos, $settings->per_page );
	$offset_items = '';
	ob_start();
	foreach ( $offset_photos as $photo ) {
		include $module->dir . 'includes/gallery-item.php';
	}
	$offset_items = ob_get_clean();
	?>
	<script type="text/html" class="pp-filterable-gallery-items">
		<?php echo preg_replace( '/\>\s+\</m', '><', $offset_items ); ?>
	</script>
</div>
<?php } ?>
