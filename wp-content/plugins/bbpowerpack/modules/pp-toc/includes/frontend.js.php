<?php
$head       = (array)$settings->anchor_tag;
$head_value = '';
foreach ( $head as $h ) {
	$head_value .= $h;
}
$headings       = str_split( $head_value, 2 );
$select_heading = implode( ',', $headings );

$additional_offset = ! empty( absint( $settings->additional_offset ) ) ? $settings->additional_offset : '0';
$additional_offset_tablet = isset( $settings->additional_offset_medium ) && ! empty( absint( $settings->additional_offset_medium ) ) ? $settings->additional_offset_medium : $additional_offset;
$additional_offset_mobile = isset( $settings->additional_offset_responsive ) && ! empty( absint( $settings->additional_offset_responsive ) ) ? $settings->additional_offset_responsive : $additional_offset_tablet;
?>

;(function($) {
	$(function() {

		window['pp_toc_<?php echo $id; ?>'] = new PPToCModule({
			id: '<?php echo esc_attr( $id ); ?>',
			headData: '<?php echo esc_attr( $select_heading ); ?>',
			additionalOffset: {
				desktop: <?php echo $additional_offset; ?>,
				tablet: <?php echo $additional_offset_tablet; ?>,
				mobile: <?php echo $additional_offset_mobile; ?>
			},
			container: '<?php echo esc_html( $settings->include_container ); ?>',
			exclude: '<?php echo esc_html( $settings->exclude_container ); ?>',
			collapsableToc: '<?php echo esc_attr( $settings->collapsable_toc ); ?>',
			collapseOn: '<?php echo esc_attr( $settings->collapse_on ); ?>',
			hierarchicalView: '<?php echo esc_attr( $settings->hierarchical_view ); ?>',
			stickyOn: '<?php echo $settings->sticky_on; ?>',
			stickyType: '<?php echo esc_attr( $settings->sticky_type ); ?>',
			stickyFixedOffset: <?php echo empty( $settings->fixed_offset_position ) ? '0' : $settings->fixed_offset_position; ?>,
			stickyBuilderOff: '<?php echo $settings->sticky_builder_off; ?>',
			scrollTop: '<?php echo $settings->scroll_top; ?>',
			scrollTo: '<?php echo esc_attr( $settings->scroll_to ); ?>',
			scrollAlignment: '<?php echo esc_attr( $settings->scroll_alignment ); ?>',
			breakpoints: {
				large: '<?php echo esc_attr( $global_settings->large_breakpoint ); ?>',
				tablet: '<?php echo esc_attr( $global_settings->medium_breakpoint ); ?>',
				mobile: '<?php echo esc_attr( $global_settings->responsive_breakpoint ); ?>',
			},
			listIcon: '<?php echo esc_attr( $settings->list_icon_field ); ?>',
			listStyle: '<?php echo esc_attr( $settings->list_style ); ?>',
			anchorId: '<?php echo isset( $settings->anchor_id ) ? $settings->anchor_id : 'auto'; ?>',
			hideTo: '<?php echo isset( $settings->hide_toc_to ) && ! empty( $settings->hide_toc_to ) ? $settings->hide_toc_to : ''; ?>'
		});

	});
})(jQuery);
