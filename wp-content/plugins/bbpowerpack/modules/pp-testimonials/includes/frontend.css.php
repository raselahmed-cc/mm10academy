<?php if ( 'slider' == $settings->layout ) { ?>

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .pp-arrow-wrapper {
		<?php if ( $settings->arrow_alignment ) { ?>text-align: <?php echo $settings->arrow_alignment; ?>;<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-dots .owl-dot span {
		opacity: 1;
		<?php if ( ! empty( $settings->dot_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->dot_color ); ?>;
		<?php } ?>
		box-shadow: none;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-dots .owl-dot.active span,
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-dots .owl-dot:hover span,
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-dots .owl-dot:focus span {
		<?php if ( ! empty( $settings->active_dot_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->active_dot_color ); ?>;
		<?php } ?>
		opacity: 1;
		box-shadow: none;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav {
		justify-content: center;
		<?php if ( 'left' === $settings->arrow_alignment ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'right' === $settings->arrow_alignment ) { ?>
			justify-content: flex-end;
		<?php } ?>
	}

	<?php if ( isset( $settings->arrow_spacing ) && '' !== $settings->arrow_spacing ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-bottom {
		gap: <?php echo $settings->arrow_spacing; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-side button.owl-prev {
		left: <?php echo $settings->arrow_spacing; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-side button.owl-next {
		right: <?php echo $settings->arrow_spacing; ?>px;
	}
	<?php } ?>

	<?php if ( isset( $settings->arrow_size ) && '' !== $settings->arrow_size ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button {
		width: <?php echo floatval( $settings->arrow_size ) + 10; ?>px;
		height: <?php echo floatval( $settings->arrow_size ) + 10; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button svg {
		height: <?php echo $settings->arrow_size; ?>px;
	}
	<?php } ?>

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button {
		<?php if ( isset( $settings->arrow_color ) && ! empty( $settings->arrow_color ) ) { ?>
			color: <?php echo pp_get_color_value( $settings->arrow_color ); ?> !important;
		<?php } ?>
		<?php if ( isset( $settings->arrow_bg_color ) && ! empty( $settings->arrow_bg_color ) ) { ?>
			background: <?php echo pp_get_color_value( $settings->arrow_bg_color ); ?> !important;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button svg path {
		<?php if ( isset( $settings->arrow_color ) && ! empty( $settings->arrow_color ) ) { ?>
			fill: <?php echo pp_get_color_value( $settings->arrow_color ); ?> !important;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button:hover {
		<?php if ( isset( $settings->arrow_hover_color ) && ! empty( $settings->arrow_hover_color ) ) { ?>
			color: <?php echo pp_get_color_value( $settings->arrow_hover_color ); ?> !important;
		<?php } ?>
		<?php if ( isset( $settings->arrow_bg_hover_color ) && ! empty( $settings->arrow_bg_hover_color ) ) { ?>
			background: <?php echo pp_get_color_value( $settings->arrow_bg_hover_color ); ?> !important;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button:hover svg path {
		<?php if ( isset( $settings->arrow_hover_color ) && ! empty( $settings->arrow_hover_color ) ) { ?>
			fill: <?php echo pp_get_color_value( $settings->arrow_hover_color ); ?> !important;
		<?php } ?>
	}

	<?php
	FLBuilderCSS::border_field_rule(
		array(
			'settings'     => $settings,
			'setting_name' => 'arrow_border',
			'selector'     => ".fl-node-$id .pp-testimonials-wrap .owl-nav button",
		)
	);
	?>

<?php } ?>
<?php
FLBuilderCSS::responsive_rule( array(
	'settings'	=> $settings,
	'setting_name'	=> 'spacing',
	'selector'	=> ".fl-node-$id .pp-testimonials-grid .pp-testimonials",
	'prop'		=> 'grid-gap',
	'unit'		=> 'px',
) );
?>

.fl-node-<?php echo $id; ?> .pp-testimonial {
	<?php if ( isset( $settings->full_box_bg ) && ! empty( $settings->full_box_bg ) ) { ?>
	background: <?php echo pp_get_color_value( $settings->full_box_bg ); ?>;
	<?php } ?>
}
<?php
	// Box - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'full_box_border',
		'selector' 		=> ".fl-node-$id .pp-testimonial",
	) );

	// Box - Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'full_box_padding',
		'selector' 		=> ".fl-node-$id .pp-testimonial",
		'unit'			=> 'px',
		'props'			=> array(
			'padding-top' 		=> 'full_box_padding_top',
			'padding-right' 	=> 'full_box_padding_right',
			'padding-bottom' 	=> 'full_box_padding_bottom',
			'padding-left' 		=> 'full_box_padding_left',
		),
	) );
?>

<?php if ( $settings->layout_4_content_bg || ( isset( $settings->box_border['width'] ) && 0 != $settings->box_border['width']['top'] ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials .pp-content-wrapper {
		padding: 20px;
	}
<?php } ?>

<?php if ( $settings->testimonial_layout == '1' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-1 .pp-content-wrapper {
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
	<?php if ( $settings->show_arrow == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-testimonial.layout-1 .pp-arrow-top {
			<?php if ( $settings->layout_4_content_bg ) { ?>border-bottom-color: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
		}
	<?php } ?>
<?php } ?>
<?php if ( $settings->testimonial_layout == '2' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-2 .pp-content-wrapper {
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
	<?php if ( $settings->show_arrow == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-testimonial.layout-2 .pp-arrow-bottom {
			<?php if ( $settings->layout_4_content_bg ) { ?>border-top-color: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
		}
	<?php } ?>
<?php } ?>
<?php if ( $settings->testimonial_layout == '3' ) {
	$wd = floatval($settings->image_size) + 30; ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-3 .pp-content-wrapper {
		width: calc(100% - <?php echo $wd; ?>px);
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
	<?php if ( $settings->show_arrow == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-testimonial.layout-3 .pp-arrow-left {
			<?php if ( $settings->layout_4_content_bg ) { ?>border-right-color: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
		}
	<?php } ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials .layout-3 .pp-testimonials-image {
		max-height: <?php echo $settings->image_size; ?>px;
		max-width: <?php echo $settings->image_size; ?>px;
	}

<?php } ?>
<?php if ( $settings->testimonial_layout == '4' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-4 .layout-4-content {
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
<?php } ?>
<?php if ( $settings->testimonial_layout == '5' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-5 .pp-content-wrapper {
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
	<?php if ( $settings->show_arrow == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-testimonial.layout-5 .pp-arrow-top {
			<?php if ( $settings->layout_4_content_bg ) { ?>border-bottom-color: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
		}
	<?php } ?>
<?php } ?>

<?php
	// Content Box - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'box_border',
		'selector' 		=> ".fl-node-$id .pp-testimonial.layout-1 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-2 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-3 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-4 .layout-4-content, .fl-node-$id .pp-testimonial.layout-5 .pp-content-wrapper",
	) );

	// Content Box - Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'box_padding',
		'selector' 		=> ".fl-node-$id .pp-testimonial.layout-1 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-2 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-3 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-4 .layout-4-content, .fl-node-$id .pp-testimonial.layout-5 .pp-content-wrapper",
		'unit'			=> 'px',
		'props'			=> array(
			'padding-top' 		=> 'box_padding_top',
			'padding-right' 	=> 'box_padding_right',
			'padding-bottom' 	=> 'box_padding_bottom',
			'padding-left' 		=> 'box_padding_left',
		),
	) );
?>

.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .pp-testimonials-heading {
	<?php if ( $settings->heading_color ) { ?>color: <?php echo pp_get_color_value( $settings->heading_color ); ?>;<?php } ?>
}
<?php
	// Heading Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'heading_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonials-wrap .pp-testimonials-heading",
	) );
?>
.fl-node-<?php echo $id; ?> .pp-testimonial .pp-title-wrapper .pp-testimonials-name {
	<?php if ( ! empty( $settings->title_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->title_color ); ?>;
	<?php } ?>
	<?php if ( '' !== $settings->title_margin['top'] ) { ?>
	margin-top: <?php echo $settings->title_margin['top']; ?>px;
	<?php } ?>
	<?php if ( '' !== $settings->title_margin['bottom'] ) { ?>
	margin-bottom: <?php echo $settings->title_margin['bottom']; ?>px;
	<?php } ?>
}
<?php
	// Title Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'title_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-title-wrapper .pp-testimonials-name",
	) );
?>
.fl-node-<?php echo $id; ?> .pp-testimonial .pp-title-wrapper .pp-testimonials-designation {
	<?php if ( ! empty( $settings->subtitle_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->subtitle_color ); ?>;
	<?php } ?>
	<?php if ( '' !== $settings->subtitle_margin['top'] ) { ?>
	margin-top: <?php echo $settings->subtitle_margin['top']; ?>px;
	<?php } ?>
	<?php if ( '' !== $settings->subtitle_margin['bottom'] ) { ?>
	margin-bottom: <?php echo $settings->subtitle_margin['bottom']; ?>px;
	<?php } ?>
}
<?php
	// Sub Title Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'subtitle_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-title-wrapper .pp-testimonials-designation",
	) );
?>

.fl-node-<?php echo $id; ?> .pp-testimonial .pp-testimonials-title {
	<?php if ( ! empty( $settings->testimonial_title_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->testimonial_title_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->testimonial_title_margin ) ) { ?>
		<?php if ( '' !== $settings->testimonial_title_margin['top'] ) { ?>
		margin-top: <?php echo $settings->testimonial_title_margin['top']; ?>px;
		<?php } ?>
		<?php if ( '' !== $settings->testimonial_title_margin['bottom'] ) { ?>
		margin-bottom: <?php echo $settings->testimonial_title_margin['bottom']; ?>px;
		<?php } ?>
	<?php } ?>
}
<?php
	// Testimonial Title Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'testimonial_title_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-testimonials-title",
	) );
?>

.fl-node-<?php echo $id; ?> .pp-testimonial .pp-testimonials-content {
	<?php if ( ! empty( $settings->text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->text_color ); ?>;
	<?php } ?>
	<?php if ( '' !== $settings->content_margin['top'] ) { ?>
	margin-top: <?php echo $settings->content_margin['top']; ?>px;
	<?php } ?>
	<?php if ( '' !== $settings->content_margin['bottom'] ) { ?>
	margin-bottom: <?php echo $settings->content_margin['bottom']; ?>px;
	<?php } ?>
}
<?php
	// Testimonial Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'text_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-testimonials-content",
	) );
?>
<?php if ( '' !== $settings->image_size ) { ?>
.fl-node-<?php echo $id; ?> .pp-testimonial .pp-testimonials-image img {
	max-height: <?php echo $settings->image_size; ?>px;
	max-width: <?php echo $settings->image_size; ?>px;
}
<?php } ?>
<?php
	// Image - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'image_border',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-testimonials-image img",
	) );
?>

.fl-node-<?php echo $id; ?>.pp-masonry-grid .pp-testimonials {
	<?php if ( isset( $settings->grid_columns ) && ! empty( $settings->grid_columns ) ) { ?>
	column-count: <?php echo $settings->grid_columns; ?>;
	<?php } ?>
}

<?php if ( isset( $settings->padding_top ) && '' !== $settings->padding_top ) { ?>
.fl-node-<?php echo $id; ?> .pp-testimonials .layout-4 {
	padding-top: <?php echo $settings->padding_top; ?>px;
}
<?php } ?>

@media only screen and ( max-width: <?php echo $global_settings->large_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-lg-1 .pp-testimonials {
		grid-template-columns: 100%;
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-lg-2 .pp-testimonials {
		grid-template-columns: repeat(2,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-lg-3 .pp-testimonials {
		grid-template-columns: repeat(3,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-lg-4 .pp-testimonials {
		grid-template-columns: repeat(4,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-lg-5 .pp-testimonials {
		grid-template-columns: repeat(5,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-lg-6 .pp-testimonials {
		grid-template-columns: repeat(6,1fr);
		display: grid;
	}
	.fl-node-<?php echo $id; ?>.pp-masonry-grid .pp-testimonials {
		<?php if ( isset( $settings->grid_columns_large ) && ! empty( $settings->grid_columns_large ) ) { ?>
		column-count: <?php echo $settings->grid_columns_large; ?>;
		<?php } ?>
	}

	<?php if ( isset( $settings->arrow_spacing_large ) && '' !== $settings->arrow_spacing_large ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-bottom {
		gap: <?php echo $settings->arrow_spacing_large; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-side button.owl-prev {
		left: <?php echo $settings->arrow_spacing_large; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-side button.owl-next {
		right: <?php echo $settings->arrow_spacing_large; ?>px;
	}
	<?php } ?>

	<?php if ( isset( $settings->arrow_size_large ) && '' !== $settings->arrow_size_large ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button {
		width: <?php echo floatval( $settings->arrow_size_large ) + 10; ?>px;
		height: <?php echo floatval( $settings->arrow_size_large ) + 10; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button svg {
		height: <?php echo $settings->arrow_size_large; ?>px;
	}
	<?php } ?>
}

@media only screen and ( max-width: <?php echo $global_settings->medium_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-1 .pp-testimonials {
		grid-template-columns: 100%;
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-2 .pp-testimonials {
		grid-template-columns: repeat(2,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-3 .pp-testimonials {
		grid-template-columns: repeat(3,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-4 .pp-testimonials {
		grid-template-columns: repeat(4,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-5 .pp-testimonials {
		grid-template-columns: repeat(5,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-6 .pp-testimonials {
		grid-template-columns: repeat(6,1fr);
		display: grid;
	}
	.fl-node-<?php echo $id; ?>.pp-masonry-grid .pp-testimonials {
		<?php if ( isset( $settings->grid_columns_medium ) && ! empty( $settings->grid_columns_medium ) ) { ?>
		column-count: <?php echo $settings->grid_columns_medium; ?>;
		<?php } ?>
	}

	<?php if ( isset( $settings->arrow_spacing_medium ) && '' !== $settings->arrow_spacing_medium ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-bottom {
		gap: <?php echo $settings->arrow_spacing_medium; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-side button.owl-prev {
		left: <?php echo $settings->arrow_spacing_medium; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-side button.owl-next {
		right: <?php echo $settings->arrow_spacing_medium; ?>px;
	}
	<?php } ?>

	<?php if ( isset( $settings->arrow_size_medium ) && '' !== $settings->arrow_size_medium ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button {
		width: <?php echo floatval( $settings->arrow_size_medium ) + 10; ?>px;
		height: <?php echo floatval( $settings->arrow_size_medium ) + 10; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button svg {
		height: <?php echo $settings->arrow_size_medium; ?>px;
	}
	<?php } ?>
}

@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-1 .pp-testimonials {
		grid-template-columns: 100%;
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-2 .pp-testimonials {
		grid-template-columns: repeat(2,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-3 .pp-testimonials {
		grid-template-columns: repeat(3,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-4 .pp-testimonials {
		grid-template-columns: repeat(4,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-5 .pp-testimonials {
		grid-template-columns: repeat(5,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-6 .pp-testimonials {
		grid-template-columns: repeat(6,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?>.pp-masonry-grid .pp-testimonials {
		<?php if ( isset( $settings->grid_columns_responsive ) && ! empty( $settings->grid_columns_responsive ) ) { ?>
		column-count: <?php echo $settings->grid_columns_responsive; ?>;
		<?php } ?>
	}

	<?php if ( isset( $settings->arrow_spacing_responsive ) && '' !== $settings->arrow_spacing_responsive ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-bottom {
		gap: <?php echo $settings->arrow_spacing_responsive; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-side button.owl-prev {
		left: <?php echo $settings->arrow_spacing_responsive; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav.position-side button.owl-next {
		right: <?php echo $settings->arrow_spacing_responsive; ?>px;
	}
	<?php } ?>

	<?php if ( isset( $settings->arrow_size_responsive ) && '' !== $settings->arrow_size_responsive ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button {
		width: <?php echo floatval( $settings->arrow_size_responsive ) + 10; ?>px;
		height: <?php echo floatval( $settings->arrow_size_responsive ) + 10; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-nav button svg {
		height: <?php echo $settings->arrow_size_responsive; ?>px;
	}
	<?php } ?>
}
