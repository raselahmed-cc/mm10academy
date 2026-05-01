<?php

// -------------------Dimension-----------------------------

// Card Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'card_padding',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'card_padding_top',
			'padding-right'  => 'card_padding_right',
			'padding-bottom' => 'card_padding_bottom',
			'padding-left'   => 'card_padding_left',
		),
	)
);

// Card Content Margin.
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'card_content_margin',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-content-wrap",
		'unit'         => 'px',
		'props'        => array(
			'margin-top'    => 'card_content_margin_top',
			'margin-right'  => 'card_content_margin_right',
			'margin-bottom' => 'card_content_margin_bottom',
			'margin-left'   => 'card_content_margin_left',
		),
	)
);

// Card Content Padding.
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'card_content_padding',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-content-wrap",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'card_content_padding_top',
			'padding-right'  => 'card_content_padding_right',
			'padding-bottom' => 'card_content_padding_bottom',
			'padding-left'   => 'card_content_padding_left',
		),
	)
);

// Image margin
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_image_margin',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-image",
		'unit'         => 'px',
		'props'        => array(
			'margin-top'    => 'content_image_margin_top',
			'margin-right'  => 'content_image_margin_right',
			'margin-bottom' => 'content_image_margin_bottom',
			'margin-left'   => 'content_image_margin_left',
		),
	)
);

// Button Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'button_padding',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-button",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'button_padding_top',
			'padding-right'  => 'button_padding_right',
			'padding-bottom' => 'button_padding_bottom',
			'padding-left'   => 'button_padding_left',
		),
	)
);

// Dots Margin
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'dots_margin',
		'selector'     => ".fl-node-$id .pp-card-slider-container .swiper-pagination",
		'unit'         => 'px',
		'props'        => array(
			'margin-top'    => 'dots_margin_top',
			'margin-right'  => 'dots_margin_right',
			'margin-bottom' => 'dots_margin_bottom',
			'margin-left'   => 'dots_margin_left',
		),
	)
);

// -------------------Typography-----------------------------

// Title Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'title_typography',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-title, .fl-node-$id .pp-card-slider-container .pp-card-slider-title a",
	)
);

// Button Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'button_typography',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-button",
	)
);

// Content Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_typography',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-content",
	)
);

// Meta Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'meta_typography',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-meta",
	)
);

// -------------------Border---------------------------

// Card Border
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'card_border',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider",
	)
);

// Image Border
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_image_border',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-image, .fl-node-$id .pp-card-slider-container .pp-card-slider-image img, .fl-node-$id .pp-card-slider-container .pp-card-slider-image:not(.has-lightbox):after, .fl-node-$id .pp-card-slider-container .pp-card-slider-image.has-lightbox a:after",
	)
);

// Button Border
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'button_border',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-button",
	)
);

// Dots Border
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'dots_border',
		'selector'     => ".fl-node-$id .pp-card-slider-container .swiper-pagination .swiper-pagination-bullet",
	)
);

// -------------------Responsive-----------------------------

// Container Max Width
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'card_max_width',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider",
		'prop'         => 'max-width',
	)
);

// Container Width
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'card_width',
		'selector'     => ".fl-node-$id .pp-card-slider-container",
		'prop'         => 'width',
	)
);

// Title Spacing
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'title_bottom_spacing',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-title",
		'prop'         => 'margin-bottom',
		'unit'         => 'px',
	)
);

// Content Spacing
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'content_bottom_spacing',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-content",
		'prop'         => 'margin-bottom',
		'unit'         => 'px',
	)
);

// Meta Spacing
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'meta_bottom_spacing',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-meta",
		'prop'         => 'margin-bottom',
		'unit'         => 'px',
	)
);

// Image Width
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'card_image_width',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-image",
		'prop'         => 'width',
	)
);

// Image Height
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'card_image_height',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-image",
		'prop'         => 'height',
	)
);

// Button width
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'button_size',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-button",
		'prop'         => 'width',
	)
);

// Button Spacing
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'button_spacing',
		'selector'     => ".fl-node-$id .pp-card-slider-container .pp-card-slider-button",
		'prop'         => 'margin-top',
		'unit'         => 'px',
	)
);

// Dots Width
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'dots_width',
		'selector'     => ".fl-node-$id .pp-card-slider-container .swiper-pagination .swiper-pagination-bullet",
		'prop'         => 'width',
		'unit'         => 'px',
	)
);

// Dots Height
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'dots_height',
		'selector'     => ".fl-node-$id .pp-card-slider-container .swiper-pagination .swiper-pagination-bullet",
		'prop'         => 'height',
		'unit'         => 'px',
	)
);
if ( 'vertical' === $settings->slide_direction ) {

	FLBuilderCSS::responsive_rule(
		array(
			'settings'     => $settings,
			'setting_name' => 'dots_spacing',
			'selector'     => ".fl-node-$id .pp-card-slider-container .swiper-pagination .swiper-pagination-bullet",
			'prop'         => 'margin-top',
			'unit'         => 'px',
		)
	);
} else {
	FLBuilderCSS::responsive_rule(
		array(
			'settings'     => $settings,
			'setting_name' => 'dots_spacing',
			'selector'     => ".fl-node-$id .pp-card-slider-container .swiper-pagination .swiper-pagination-bullet",
			'prop'         => 'margin-right',
			'unit'         => 'px',
		)
	);
}

// Responsive rule for Card Content.
FLBuilderCSS::rule(
	array(
		'selector' => ".fl-node-$id .pp-card-slider-container .pp-card-slider-item, .fl-node-$id .pp-card-slider-container .swiper-slide",
		'media'    => 'responsive',
		'props'    => array(
			'flex-direction'  => 'column',
			'align-items'     => 'center',
			'justify-content' => 'center',
		),
	)
);

// Image Responsive rule
// FLBuilderCSS::rule(
// 	array(
// 		'selector' => ".fl-node-$id .pp-card-slider-container .pp-card-slider-image",
// 		'media'    => 'responsive',
// 		'props'    => array(
// 			'margin' => '10px',
// 			'width'  => ( '' !== $settings->card_image_width && 90 > $settings->card_image_width ) ? $settings->card_image_width . '%' : '90%',
// 			'height' => '250px',
// 		),
// 	)
// );

// Content Wrap
FLBuilderCSS::rule(
	array(
		'selector' => ".fl-node-$id .pp-card-slider-container .pp-card-slider-content-wrap",
		'media'    => 'responsive',
		'props'    => array(
			'margin'  => '10px 0',
			'padding' => '10px 20px',
		),
	)
);

// Content Text Alignment Responsive Rule.
FLBuilderCSS::rule(
	array(
		'selector' => ".fl-node-$id .pp-card-slider-container .pp-card-slider-title, .fl-node-$id .pp-card-slider-container .pp-card-slider-meta, .fl-node-$id .pp-card-slider-container .pp-card-slider-content",
		'media'    => 'responsive',
		'props'    => array(
			'text-align' => 'center',
		),
	)
);
?>

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider {

	<?php if ( isset( $settings->card_bgcolor ) && ! empty( $settings->card_bgcolor ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->card_bgcolor ); ?>;
	<?php } ?>

}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider:hover {

	<?php if ( isset( $settings->card_bgcolor_hover ) && ! empty( $settings->card_bgcolor_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->card_bgcolor_hover ); ?>;
	<?php } ?>

}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-title, 
.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-title a {

	<?php if ( ! empty( $settings->title_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->title_color ); ?>;
	<?php } ?>

	<?php if ( 'above' === ( $settings->meta_placement ) ) { ?>
		order: 2;
	<?php } ?>

}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-title:hover, 
.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-title a:hover {

	<?php if ( ! empty( $settings->title_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->title_color_hover ); ?>;
	<?php } ?>

}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-content {

	<?php if ( ! empty( $settings->content_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->content_color ); ?>;
	<?php } ?>

}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-content:hover {

	<?php if ( ! empty( $settings->content_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->content_color_hover ); ?>;
	<?php } ?>

}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-meta {

	<?php if ( ! empty( $settings->meta_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->meta_color ); ?>;
	<?php } ?>
	<?php if ( 'above' === ( $settings->meta_placement ) ) { ?>
		order: 1;
	<?php } ?>

}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-meta:hover {

	<?php if ( ! empty( $settings->meta_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->meta_color_hover ); ?>;
	<?php } ?>

}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-image:not(.has-lightbox):after,
.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-image.has-lightbox:after {

	<?php
	if ( 'classic' === $settings->overlay_type ) {

		if ( isset( $settings->overlay_color ) && ! empty( $settings->overlay_color ) ) {
			?>
		background-color: <?php echo pp_get_color_value( $settings->overlay_color ); ?>;
			<?php
		}
	} else {
		?>
		background-image: <?php echo FLBuilderColor::gradient( $settings->image_gradient ); ?>
		<?php
	}
	?>
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-button {

	<?php if ( ! empty( $settings->button_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->button_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->button_bgcolor ) && ! empty( $settings->button_bgcolor ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->button_bgcolor ); ?>;
	<?php } ?>
	<?php if ( '' !== ( $settings->button_size ) ) { ?>
		height: auto;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-button:hover {

	<?php if ( ! empty( $settings->button_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->button_color_hover ); ?>;
	<?php } ?>

	<?php if ( isset( $settings->button_bgcolor_hover ) && ! empty( $settings->button_bgcolor_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->button_bgcolor_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .swiper-pagination-bullet {

	<?php if ( isset( $settings->dots_color ) && ! empty( $settings->dots_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->dots_color ); ?>;
	<?php } ?>
	transition: all 0.4s ease;
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .swiper-pagination-bullet:hover {

	<?php if ( isset( $settings->dots_color_hover ) && ! empty( $settings->dots_color_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->dots_color_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .swiper-container-horizontal .swiper-pagination-bullet.swiper-pagination-bullet-active {
	width: calc( <?php echo $settings->dots_width; ?>px + 30px );
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .swiper-container-vertical .swiper-pagination-bullet.swiper-pagination-bullet-active {
	height: calc( <?php echo $settings->dots_height; ?>px + 30px );
}

<?php

if ( 'right' === $settings->image_direction ) {
	?>

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-item {

	flex-direction: row-reverse;
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider {

	flex-direction: row-reverse;
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-image {

	margin-right: -80px;
	margin-left: 40px;
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .swiper-container-vertical .swiper-pagination {

	left: 20px;
	right: auto;
}

.fl-node-<?php echo $id; ?> .pp-card-slider-container .pp-card-slider-content-wrap {
	margin-left: 40px;
}

	<?php
}
?>
