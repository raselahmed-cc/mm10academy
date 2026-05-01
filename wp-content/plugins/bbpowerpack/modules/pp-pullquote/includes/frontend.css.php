<?php
// Pullquote background.
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .pp-pullquote .pp-pullquote-wrapper",
	'enabled'  => ! empty( $settings->quote_background ),
	'props'    => array(
		'background' => pp_get_color_value( $settings->quote_background )
	)
) );

// Pullquote overall align.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'quote_alignment',
	'selector'     => ".fl-node-$id .pp-pullquote .pp-pullquote-wrapper",
	'prop'         => 'float'
) );

// Pullquote width.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'quote_width',
	'prop'         => 'width',
	'unit'         => 'px',
	'selector'     => ".fl-node-$id .pp-pullquote .pp-pullquote-wrapper"
) );

// Pullquote text align.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'quote_text_alignment',
	'prop'         => 'text-align',
	'selector'     => ".fl-node-$id .pp-pullquote .pp-pullquote-wrapper"
) );

// Border.
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'quote_border',
	'selector'     => ".fl-node-$id .pp-pullquote .pp-pullquote-wrapper"
) );

// Padding.
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'quote_padding',
	'selector'     => ".fl-node-$id .pp-pullquote .pp-pullquote-wrapper",
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'quote_padding_top',
		'padding-right'  => 'quote_padding_right',
		'padding-bottom' => 'quote_padding_bottom',
		'padding-left'   => 'quote_padding_left',
	),
) );

// Name typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'name_typography',
	'selector'     => ".fl-node-$id .pp-pullquote .pp-pullquote-name"
) );

// Name color.
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .pp-pullquote .pp-pullquote-name",
	'enabled'  => ! empty( $settings->name_color ),
	'props'    => array(
		'color' => pp_get_color_value( $settings->name_color )
	)
) );

// Text typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'text_typography',
	'selector'     => ".fl-node-$id .pp-pullquote .pp-pullquote-content p"
) );

// Text color.
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .pp-pullquote .pp-pullquote-content p",
	'enabled'  => ! empty( $settings->text_color ),
	'props'    => array(
		'color' => pp_get_color_value( $settings->text_color )
	)
) );

// Icon size.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'icon_font_size',
	'selector'     => ".fl-node-$id .pp-pullquote .pp-pullquote-icon .pp-icon",
	'prop'         => 'font-size',
	'unit'         => 'px',
	'enabled'      => 'yes' === $settings->show_icon
) );

// Icon color.
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .pp-pullquote .pp-pullquote-icon .pp-icon",
	'enabled'  => ( 'yes' === $settings->show_icon && ! empty( $settings->icon_color ) ),
	'props'    => array(
		'color' => pp_get_color_value( $settings->icon_color )
	)
) );
?>