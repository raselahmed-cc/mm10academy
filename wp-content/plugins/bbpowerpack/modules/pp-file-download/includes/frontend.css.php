<?php
FLBuilder::render_module_css( 'pp-smart-button', $id, array(
	'style'						=> $settings->style,
	'display_icon'				=> $settings->display_icon,
	'icon'						=> $settings->icon,
	'icon_position'				=> $settings->icon_position,
	'icon_size'					=> $settings->icon_size,
	'icon_spacing'				=> $settings->icon_spacing,
	'button_effect'				=> $settings->button_effect,
	'button_effect_duration' 	=> $settings->button_effect_duration,
	'bg_color' 					=> $settings->bg_color,
	'bg_hover_color' 			=> $settings->bg_hover_color,
	'bg_color_primary' 			=> $settings->bg_color_primary,
	'bg_color_secondary' 		=> $settings->bg_color_secondary,
	'gradient_hover' 			=> $settings->gradient_hover,
	'text_color' 				=> $settings->text_color,
	'text_hover_color' 			=> $settings->text_hover_color,
	'width' 					=> $settings->width,
	'custom_width' 				=> $settings->custom_width,
	'align' 					=> $settings->align,
	'padding_top' 				=> $settings->padding_top,
	'padding_right' 			=> $settings->padding_right,
	'padding_bottom' 			=> $settings->padding_bottom,
	'padding_bottom' 			=> $settings->padding_bottom,
	'padding_left' 				=> $settings->padding_left,
	'padding_unit' 				=> 'px',
	'border' 					=> $settings->border,
	'border_hover_color' 		=> $settings->border_hover_color,
	'typography' 				=> $settings->typography,
) );
?>

<?php
// Select Width.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'custom_width',
	'selector'     => ".fl-node-$id .pp-files-dropdown select",
	'prop'         => 'width',
	'unit'         => $settings->custom_width_unit,
	'enabled'	   => 'custom' === $settings->width,
) );

// Select padding.
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'padding',
		'selector'     => ".fl-node-$id .pp-files-dropdown select",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'padding_top',
			'padding-right'  => 'padding_right',
			'padding-bottom' => 'padding_bottom',
			'padding-left'   => 'padding_left',
		),
	)
);

// Select Align.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'align',
	'selector'     => ".fl-node-$id .pp-files-dropdown",
	'prop'         => 'text-align',
) );

?>

.fl-node-<?php echo $id; ?> .pp-files-dropdown select {
	<?php if ( 'full' === $settings->width ) { ?>
		width: 100%;
	<?php } ?>
}

.fl-node-<?php echo $id; ?>.pp-style-inline .pp-files-dropdown select {
	width: 100%;
}
.fl-node-<?php echo $id; ?>.pp-style-inline .pp-button-wrap a.pp-button,
.fl-node-<?php echo $id; ?>.pp-style-inline .pp-button-wrap a.pp-button:visited {
	width: auto;
}
.fl-node-<?php echo $id; ?>.pp-style-inline .pp-files-wrapper {
	display: flex;
}
.fl-node-<?php echo $id; ?>.pp-style-inline .pp-files-dropdown {
	flex: 1 1 auto;
	margin-right: 10px;
}