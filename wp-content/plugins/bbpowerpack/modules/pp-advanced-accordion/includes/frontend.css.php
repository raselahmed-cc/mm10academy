.fl-node-<?php echo $id; ?> .pp-accordion-item {
	<?php if ( $settings->item_spacing == 0 ) { ?>
	border-bottom: none;
	<?php } else { ?>
	margin-bottom: <?php echo $settings->item_spacing; ?>px;
	<?php } ?>
}

<?php
// Item border.
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'item_border',
	'selector' 		=> ".fl-node-$id .pp-accordion-item",
) );

// Label padding.
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'label_padding',
	'selector'		=> ".fl-node-$id .pp-accordion-item .pp-accordion-button",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top'		=> 'label_padding_top',
		'padding-right'		=> 'label_padding_right',
		'padding-bottom'	=> 'label_padding_bottom',
		'padding-left'		=> 'label_padding_left',
	)
) );

// Label border.
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'label_border',
	'selector' 		=> ".fl-node-$id .pp-accordion-item .pp-accordion-button",
) );
?> 
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button {
	<?php if ( isset( $settings->label_bg_color_default ) && ! empty( $settings->label_bg_color_default ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->label_bg_color_default ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->label_text_color_default ) && ! empty( $settings->label_text_color_default ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_text_color_default ); ?>;
	<?php } ?>
	<?php if ( $settings->item_spacing == 0 ) { ?>
	border-bottom-width: 0;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button:hover,
.fl-node-<?php echo $id; ?> .pp-accordion-item.pp-accordion-item-active .pp-accordion-button {
	<?php if ( isset( $settings->label_bg_color_active ) && ! empty( $settings->label_bg_color_active ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->label_bg_color_active ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->label_text_color_active ) && ! empty( $settings->label_text_color_active ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_text_color_active ); ?>;
	<?php } ?>
}

<?php if ( isset( $settings->label_text_color_active ) && ! empty( $settings->label_text_color_active ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-accordion-item.pp-accordion-item-active .pp-accordion-button-icon,
.fl-node-<?php echo $id; ?> .pp-accordion-item:hover .pp-accordion-button-icon {
	color: <?php echo pp_get_color_value( $settings->label_text_color_active ); ?>;
}
<?php } ?>

<?php if ( '' !== $settings->accordion_icon_spacing ) { ?>
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button-icon.pp-accordion-icon-right {
	padding-left: <?php echo $settings->accordion_icon_spacing; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button-icon.pp-accordion-icon-left {
	padding-right: <?php echo $settings->accordion_icon_spacing; ?>px;
}
<?php } ?> 


<?php if ( $settings->item_spacing == 0 && isset( $settings->label_border['width'] ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button:last-child {
	border-bottom-width: <?php echo $settings->label_border['width']['bottom']; ?>px;
}
<?php } ?>

<?php
// Label typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'label_typography',
	'selector' 		=> ".fl-node-$id .pp-accordion-item .pp-accordion-button .pp-accordion-button-label",
) );
?>

<?php if ( is_array( $settings->label_typography ) && isset( $settings->label_typography['text_align'] ) && ! empty( $settings->label_typography['text_align'] ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button .pp-accordion-button-label {
	<?php
	$text_align = $settings->label_typography['text_align'];
	$text_align = 'left' === $settings->label_typography['text_align'] ? 'flex-start' : $text_align;
	$text_align = 'right' === $settings->label_typography['text_align'] ? 'flex-end' : $text_align;
	?>
	justify-content: <?php echo $text_align; ?>;
}
<?php } ?>

<?php
// Content typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'content_typography',
	'selector' 		=> ".fl-node-$id .pp-accordion-item .pp-accordion-content",
) );
?>

<?php
// Content border.
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'content_border',
	'selector' 		=> ".fl-node-$id .pp-accordion-item .pp-accordion-content",
) );

// Content Padding.
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'content_padding',
	'selector'		=> ".fl-node-$id .pp-accordion-item .pp-accordion-content",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top'		=> 'content_padding_top',
		'padding-right'		=> 'content_padding_right',
		'padding-bottom'	=> 'content_padding_bottom',
		'padding-left'		=> 'content_padding_left',
	)
) );
?>
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-content {
	<?php if ( ! empty( $settings->content_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->content_bg_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->content_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->content_text_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->content_border['radius'] ) && is_array( $settings->content_border['radius'] ) ) { ?>
		<?php if ( '' !== $settings->content_border['radius']['bottom_left'] ) { ?>
		border-bottom-left-radius: <?php echo $settings->content_border['radius']['bottom_left']; ?>px;
		<?php } ?>
		<?php if ( '' !== $settings->content_border['radius']['bottom_right'] ) { ?>
		border-bottom-right-radius: <?php echo $settings->content_border['radius']['bottom_right']; ?>px;
		<?php } ?>
	<?php } ?>
}

<?php if ( ! empty( $settings->accordion_toggle_icon_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button-icon {
	color: <?php echo pp_get_color_value( $settings->accordion_toggle_icon_color ); ?>;
}
<?php } ?>

<?php if ( '' !==  $settings->accordion_toggle_icon_size ) { ?>
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button-icon,
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button-icon:before {
	font-size: <?php echo $settings->accordion_toggle_icon_size; ?>px;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-icon {
	<?php if ( '' !== $settings->accordion_icon_size ) { ?>
	font-size: <?php echo $settings->accordion_icon_size; ?>px;
	width: <?php echo ( $settings->accordion_icon_size * 1.25 ); ?>px;
	<?php } ?>
	<?php if ( isset( $settings->label_text_color_default ) && ! empty( $settings->label_text_color_default ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_text_color_default ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->accordion_icon_custom_spacing ) && '' !== $settings->accordion_icon_custom_spacing ) { ?>
	margin-right: <?php echo $settings->accordion_icon_custom_spacing; ?>px;
	<?php } ?>
}

<?php if ( isset( $settings->label_text_color_active ) && ! empty( $settings->label_text_color_active ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-button:hover .pp-accordion-icon,
.fl-node-<?php echo $id; ?> .pp-accordion-item.pp-accordion-item-active .pp-accordion-icon {
	color: <?php echo pp_get_color_value( $settings->label_text_color_active ); ?>;
}
<?php } ?>

<?php if ( '' !== $settings->accordion_icon_size ) { ?>
.fl-node-<?php echo $id; ?> .pp-accordion-item .pp-accordion-icon:before {
	font-size: <?php echo $settings->accordion_icon_size; ?>px;
}
<?php } ?>

<?php
if ( 'manual' === $module->get_data_source() && ! empty( $settings->items ) ) {
	$count = 0;
	foreach ( $settings->items as $item ) {
		if ( ! is_object( $item ) ) {
			$count++;
			continue;
		}
		?>
		<?php if ( isset( $item->item_label_bg_color_default ) && ! empty( $item->item_label_bg_color_default ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"] .pp-accordion-button {
			background-color: <?php echo pp_get_color_value( $item->item_label_bg_color_default ); ?>;
		}
		<?php } ?>
		<?php if ( isset( $item->item_label_text_color_default ) && ! empty( $item->item_label_text_color_default ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"] .pp-accordion-button,
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"] .pp-accordion-icon {
			color: <?php echo pp_get_color_value( $item->item_label_text_color_default ); ?>;
		}
		<?php } ?>
		<?php if ( isset( $item->item_label_text_color_active ) && ! empty( $item->item_label_text_color_active ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"] .pp-accordion-button:hover,
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"].pp-accordion-item-active .pp-accordion-button,
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"] .pp-accordion-button:hover .pp-accordion-icon,
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"].pp-accordion-item-active .pp-accordion-icon {
			color: <?php echo pp_get_color_value( $item->item_label_text_color_active ); ?>;
		}
		<?php } ?>
		<?php if ( isset( $item->item_label_bg_color_active ) && ! empty( $item->item_label_bg_color_active ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"] .pp-accordion-button:hover,
		.fl-node-<?php echo $id; ?> .pp-accordion-item[data-item="<?php echo $count; ?>"].pp-accordion-item-active .pp-accordion-button {
			background-color: <?php echo pp_get_color_value( $item->item_label_bg_color_active ); ?>;
		}
		<?php } ?>
		<?php
		$count++;
	}
}
?>