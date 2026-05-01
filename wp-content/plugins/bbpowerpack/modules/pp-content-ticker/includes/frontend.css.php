<?php

// ------------------- Dimension -----------------------
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_padding',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-wrap",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'content_padding_top',
			'padding-right'  => 'content_padding_right',
			'padding-bottom' => 'content_padding_bottom',
			'padding-left'   => 'content_padding_left',
		),
	)
);

// Image Margin.
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_image_margin',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-image",
		'unit'         => 'px',
		'props'        => array(
			'margin-top'    => 'content_image_margin_top',
			'margin-right'  => 'content_image_margin_right',
			'margin-bottom' => 'content_image_margin_bottom',
			'margin-left'   => 'content_image_margin_left',
		),
	)
);

FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'arrow_padding',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-navigation",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'arrow_padding_top',
			'padding-right'  => 'arrow_padding_right',
			'padding-bottom' => 'arrow_padding_bottom',
			'padding-left'   => 'arrow_padding_left',
		),
	)
);

FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'heading_padding',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-heading",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'heading_padding_top',
			'padding-right'  => 'heading_padding_right',
			'padding-bottom' => 'heading_padding_bottom',
			'padding-left'   => 'heading_padding_left',
		),
	)
);

// ------------------- Typography -----------------------
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'heading_typography',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-heading",
	)
);

FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_title_typography',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-item-title",
	)
);

FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_meta_typography',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-meta",
	)
);

// ------------------- Border -----------------------
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'heading_border',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-heading",
	)
);

FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_border',
		'selector'     => ".fl-node-$id .pp-content-ticker-container",
	)
);

FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_image_border',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-image img",
	)
);

FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'arrow_border',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .swiper-button-prev, .fl-node-$id .pp-content-ticker-container .swiper-button-next",
	)
);

// --------------------Responsive------------------------

FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'heading_width',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-heading",
		'prop'         => 'width',
	)
);

FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_bottom_spacing',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-item-title",
		'prop'         => 'margin-bottom',
		'unit'         => 'px',
	)
);

FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_meta_spacing',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-date",
		'prop'         => 'margin-right',
		'unit'         => 'px',
	)
);

FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_image_width',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-image",
		'prop'         => 'width',
		'unit'         => isset( $settings->content_image_width_unit ) ? $settings->content_image_width_unit : 'px',
	)
);

FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'arrow_size',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .swiper-button-prev, .fl-node-$id .pp-content-ticker-container .swiper-button-next",
		'prop'         => 'font-size',
		'unit'         => 'px',
	)
);

FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'arrow_spacing',
		'selector'     => ".fl-node-$id .pp-content-ticker-container .swiper-button-prev",
		'prop'         => 'margin-right',
		'unit'         => 'px',
	)
);

// Content Ticker Container Responsive Rule.
FLBuilderCSS::rule(
	array(
		'selector' => ".fl-node-$id .pp-content-ticker-container",
		'media'    => 'responsive',
		'props'    => array(
			'flex-direction' => 'column',
		),
	)
);

// Header Responsive Rule.
FLBuilderCSS::rule(
	array(
		'selector' => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-heading",
		'media'    => 'responsive',
		'props'    => array(
			'width'           => '100%',
			'justify-content' => $settings->heading_alignment,
		),
	)
);

if ( 'yes' === $settings->heading_arrow_enable ) {

	// Header Arrow Responsive Rule.
	FLBuilderCSS::rule(
		array(
			'selector' => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-heading:after",
			'media'    => 'responsive',
			'props'    => array(
				'content'          => '',
				'position'         => 'absolute',
				'bottom'           => '10px',
				'width'            => '10px',
				'height'           => '10px',
				'margin-left'      => '-10px',
				'background-color' => ! empty( $settings->heading_bgcolor ) ? $settings->heading_bgcolor : '333',
				'left'             => '50%',
				'top'              => '80%',
				'transform'        => 'rotate(45deg)',
			),
		)
	);
}


// Content Responsive Rule.
FLBuilderCSS::rule(
	array(
		'selector' => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-content",
		'media'    => 'responsive',
		'props'    => array(
			'flex-direction' => 'column',
			'padding'        => '0',
			'align-items'    => $settings->content_alignment,
		),
	)
);

// Image Responsive Rule.
FLBuilderCSS::rule(
	array(
		'selector' => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-image",
		'media'    => 'responsive',
		'props'    => array(
			'margin' => '10px 0 10px 0',
		),
	)
);

// Navigation Responsive rule.
FLBuilderCSS::rule(
	array(
		'selector' => ".fl-node-$id .pp-content-ticker-container .pp-content-ticker-navigation",
		'media'    => 'responsive',
		'props'    => array(
			'align-content'   => 'center',
			'justify-content' => 'center',
			'align-items'     => 'center',
		),
	)
);

?>

.fl-node-<?php echo $id; ?> .pp-content-ticker-container .pp-content-ticker-heading {
	<?php if ( isset( $settings->heading_bgcolor ) && ! empty( $settings->heading_bgcolor ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->heading_bgcolor ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->heading_txtcolor ) && ! empty( $settings->heading_txtcolor ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->heading_txtcolor ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->heading_alignment ) && ! empty( $settings->heading_alignment ) ) { ?>
		justify-content: <?php echo $settings->heading_alignment; ?>;
	<?php } ?>
	<?php if ( '' !== ( $settings->heading_width ) ) { ?>
		width: <?php echo $settings->heading_width; ?><?php echo $settings->heading_width_unit; ?>;
	<?php } ?>
	<?php if ( 'right' === ( $settings->heading_icon_align ) ) { ?>
		flex-direction: row-reverse;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-ticker-container .pp-content-ticker-heading a {
	<?php if ( isset( $settings->heading_txtcolor ) && ! empty( $settings->heading_txtcolor ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->heading_txtcolor ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->heading_alignment ) && ! empty( $settings->heading_alignment ) ) { ?>
		justify-content: <?php echo $settings->heading_alignment; ?>;
	<?php } ?>
	<?php if ( 'right' === ( $settings->heading_icon_align ) ) { ?>
		flex-direction: row-reverse;
	<?php } ?>
	display: flex;
	text-decoration: none;
	border: none;
	box-shadow: none;
}

.fl-node-<?php echo $id; ?> .pp-content-ticker-container .pp-content-ticker-content {
	<?php if ( isset( $settings->content_alignment ) && ! empty( $settings->content_alignment ) ) { ?>
		justify-content: <?php echo $settings->content_alignment; ?>;
	<?php } ?>
}

<?php if ( 'yes' === ( $settings->heading_arrow_enable ) ) { ?>

.fl-node-<?php echo $id; ?> .pp-content-ticker-container .pp-content-ticker-heading:after {
	content: '';
	position: absolute;
	right: -20px;
	border: 10px solid transparent;
	border-left-color: <?php echo ! empty( $settings->heading_bgcolor ) ? pp_get_color_value( $settings->heading_bgcolor ) : '#333'; ?>;
	top: 50%;
	transform: translateY(-50%);
}

<?php } ?>

<?php if ( 'right' === ( $settings->heading_icon_align ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-content-ticker-container .pp-content-ticker-heading-icon {
	margin-left: 5px;
}
<?php } ?>


.fl-node-<?php echo $id; ?> .pp-content-ticker-container {

	<?php if ( ! empty( $settings->content_bgcolor ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->content_bgcolor ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-ticker-container:hover {

	<?php if ( ! empty( $settings->content_bgcolor_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->content_bgcolor_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-ticker-container .pp-content-ticker-item-title,
.fl-node-<?php echo $id; ?> .pp-content-ticker-container .pp-content-ticker-item-title a {

	<?php if ( ! empty( $settings->content_title_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->content_title_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-ticker-container .pp-content-ticker-meta {

	<?php if ( ! empty( $settings->content_meta_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->content_meta_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-ticker-container .swiper-button-prev,
.fl-node-<?php echo $id; ?> .pp-content-ticker-container .swiper-button-next {

	<?php if ( ! empty( $settings->arrow_bgcolor ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->arrow_bgcolor ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->arrow_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->arrow_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-ticker-container .swiper-button-prev:hover,
.fl-node-<?php echo $id; ?> .pp-content-ticker-container .swiper-button-next:hover {

	<?php if ( ! empty( $settings->arrow_bgcolor_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->arrow_bgcolor_hover ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->arrow_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->arrow_color_hover ); ?>;
	<?php } ?>
}

