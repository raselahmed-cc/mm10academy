<?php
	$layout = $settings->feed_layout;
	$has_aspect_ratio = isset( $settings->aspect_ratio ) && 'yes' === $settings->aspect_ratio;
	$columns = empty( $settings->grid_columns ) ? 3 : $settings->grid_columns;
	$columns_medium = empty( $settings->grid_columns_medium ) ? $columns : $settings->grid_columns_medium;
	$columns_responsive = empty( $settings->grid_columns_responsive ) ? $columns_medium : $settings->grid_columns_responsive;
	$spacing = empty( $settings->spacing ) ? 0 : $settings->spacing;
	$spacing_medium = empty( $settings->spacing_medium ) ? $spacing : $settings->spacing_medium;
	$spacing_responsive = empty( $settings->spacing_responsive ) ? $spacing_medium : $settings->spacing_responsive;
?>

<?php
/*
.fl-node-<?php echo $id; ?> .pp-instagram-feed-carousel .pp-instagram-feed-inner {
	<?php if ( ! empty( $settings->image_custom_size ) && ! empty( $settings->visible_items ) ) { ?>
	max-width: <?php echo $settings->image_custom_size * $settings->visible_items; ?>px;
	margin: 0 auto;
	<?php } ?>
}
*/
?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed-carousel .pp-feed-item {
	<?php if ( ! empty( $settings->image_custom_size ) ) { ?>
	max-width: <?php echo $settings->image_custom_size; ?>px;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item {
	width: calc( 100% / <?php echo $columns; ?> );
	<?php if ( ( 'grid' == $layout || 'square-grid' == $layout ) && ! empty( $spacing ) ) { ?>
		width: calc( ( 100% - <?php echo $spacing * ( $columns - 1 ); ?>px ) / <?php echo $columns; ?> );
		margin-right: <?php echo $spacing; ?>px;
		margin-bottom: <?php echo $spacing; ?>px;
	<?php } ?>
	float: left;
}
.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item:nth-of-type(<?php echo $columns; ?>n) {
	margin-right: 0px;
}
.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item:last-of-type {
	margin-right: 0px !important;
}

<?php if ( ! $has_aspect_ratio && 'square-grid' === $layout ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-items {
		display: flex;
    	flex-wrap: wrap;
	}
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item {
		display: flex;
	}
<?php } ?>
<?php if ( ! $has_aspect_ratio && ( 'square-grid' === $layout || 'carousel' === $layout ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item .pp-feed-item-inner {
		display: flex;
		flex-basis: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item a {
		display: flex;
		height: 100%;
		flex-grow: 1;
	}
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item img {
		object-fit: cover;
		width: 100%;
	}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-feed-item img,
.fl-node-<?php echo $id; ?> .pp-feed-item .pp-feed-item-inner {
	-webkit-transition: filter 0.3s ease-in;
	transition: filter 0.3s ease-in;
}

<?php if ( 'yes' == $settings->image_grayscale ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed[data-layout="square-grid"] .pp-feed-item .pp-feed-item-inner,
	.fl-node-<?php echo $id; ?> .pp-feed-item img {
		-webkit-filter: grayscale(100%);
		filter: grayscale(100%);
	}
<?php } ?>

<?php if ( 'yes' == $settings->image_hover_grayscale ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed[data-layout="square-grid"] .pp-feed-item:hover .pp-feed-item-inner,
	.fl-node-<?php echo $id; ?> .pp-feed-item:hover img {
		-webkit-filter: grayscale(100%);
		filter: grayscale(100%);
	}
<?php } ?>

<?php if ( 'no' == $settings->image_hover_grayscale ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed[data-layout="square-grid"] .pp-feed-item:hover .pp-feed-item-inner,
	.fl-node-<?php echo $id; ?> .pp-feed-item:hover img {
		filter: none;
	}
<?php } ?>


<?php if( ( 'square-grid' == $layout || 'carousel' == $layout ) && ! empty( $settings->image_custom_size ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item-inner {
		<?php if ( isset( $settings->aspect_ratio ) && 'yes' !== $settings->aspect_ratio ) { ?>
		width: <?php echo $settings->image_custom_size; ?>px;
		<?php } ?>
		height: <?php echo $settings->image_custom_size; ?>px;
		background-position: center;
		background-repeat: no-repeat;
		background-size: cover;
		position: relative;
	}
<?php } ?>

<?php if ( 'grid' == $layout ) { ?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item:before {
<?php } else { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item .pp-feed-item-inner:before {
<?php } ?>
	content: "";
	position: absolute;
	height: 100%;
	width: 100%;
	z-index: 1;
	opacity: 0;
	-webkit-transition: all 0.25s ease-in-out;
	transition: all 0.25s ease-in-out;
}

<?php if ( 'solid' == $settings->image_overlay_type ) { ?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item .pp-overlay-container {
	background-color: <?php echo ( $settings->image_overlay_color ) ? pp_get_color_value( $settings->image_overlay_color ) : 'transparent'; ?>;
	opacity: 1;
}
<?php } ?>

<?php if ( 'gradient' == $settings->image_overlay_type ) { ?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item .pp-overlay-container {
	background-color: transparent;
	<?php if ( 'linear' == $settings->image_overlay_gradient_type ) { ?>
	background-image: linear-gradient(<?php echo $settings->image_overlay_angle; ?>deg, <?php echo pp_get_color_value( $settings->image_overlay_color ); ?> 0%, <?php echo pp_get_color_value( $settings->image_overlay_secondary_color ); ?> 100%);
	<?php } ?>
	<?php if ( 'radial' == $settings->image_overlay_gradient_type ) { ?>
	background-image: radial-gradient(at <?php echo $settings->image_overlay_gradient_position; ?>, <?php echo pp_get_color_value( $settings->image_overlay_color ); ?> 0%, <?php echo pp_get_color_value( $settings->image_overlay_secondary_color ); ?> 100%);
	<?php } ?>
	opacity: 1;
}
<?php } ?>

/*
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item .pp-overlay-container {
	color: <?php //echo '#' . $settings->likes_comments_color; ?>;
}
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item:hover .pp-overlay-container {
	color: <?php //echo '#' . $settings->likes_comments_hover_color; ?>;
}
*/

<?php if ( 'none' == $settings->image_hover_overlay_type ) { ?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item .pp-overlay-container {
	opacity: 0;
}
<?php } ?>

<?php if ( 'solid' == $settings->image_hover_overlay_type ) { ?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item:hover .pp-overlay-container {
	background-color: <?php echo ( $settings->image_hover_overlay_color ) ? pp_get_color_value( $settings->image_hover_overlay_color ) : 'transparent'; ?>;
	opacity: 1;
}
<?php } ?>

<?php if ( 'gradient' == $settings->image_hover_overlay_type ) { ?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item:hover .pp-overlay-container {
	background-color: transparent;
	<?php if ( 'linear' == $settings->image_hover_overlay_gradient_type ) { ?>
	background-image: linear-gradient(<?php echo $settings->image_hover_overlay_angle; ?>deg, <?php echo pp_get_color_value( $settings->image_hover_overlay_color ); ?> 0%, <?php echo pp_get_color_value( $settings->image_hover_overlay_secondary_color ); ?> 100%);
	<?php } ?>
	<?php if ( 'radial' == $settings->image_hover_overlay_gradient_type ) { ?>
	background-image: radial-gradient(at <?php echo $settings->image_hover_overlay_gradient_position; ?>, <?php echo pp_get_color_value( $settings->image_hover_overlay_color ); ?> 0%, <?php echo pp_get_color_value( $settings->image_hover_overlay_secondary_color ); ?> 100%);
	<?php } ?>
	opacity: 1;
}
<?php } ?>

<?php if ( 'top' == $settings->feed_title_position ) { ?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-title-wrap {
	top: 0;
	position: absolute;
	left: 50%;
	-webkit-transform: translateX(-50%);
	-ms-transform: translateX(-50%);
	transform: translate(-50%);
}
<?php } ?>

<?php if ( 'bottom' == $settings->feed_title_position ) { ?>
.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-title-wrap {
	bottom: 0;
	top: auto;
	position: absolute;
	left: 50%;
	-webkit-transform: translateX(-50%);
	-ms-transform: translateX(-50%);
	transform: translate(-50%);
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-title-wrap {
	<?php if ( isset( $settings->feed_title_bg_color ) && ! empty( $settings->feed_title_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->feed_title_bg_color ); ?>;
	<?php } ?>
	<?php if ( 0 <= $settings->feed_title_horizontal_padding ) { ?>
		padding-left: <?php echo $settings->feed_title_horizontal_padding; ?>px;
		padding-right: <?php echo $settings->feed_title_horizontal_padding; ?>px;
	<?php } ?>
	<?php if ( 0 <= $settings->feed_title_vertical_padding ) { ?>
		padding-top: <?php echo $settings->feed_title_vertical_padding; ?>px;
		padding-bottom: <?php echo $settings->feed_title_vertical_padding; ?>px;
	<?php } ?>
	transition: all 0.3s ease-in;
}
<?php
	// Title Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'title_typography',
		'selector' 		=> ".fl-node-$id .pp-instagram-feed .pp-instagram-feed-title-wrap",
	) );

	// Title - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'feed_title_border_group',
		'selector' 		=> ".fl-node-$id .pp-instagram-feed .pp-instagram-feed-title-wrap",
	) );
?>

.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-title-wrap .pp-instagram-feed-title {
	color: <?php echo pp_get_color_value( $settings->feed_title_text_color ); ?>;
}

.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-title-wrap:hover {
	<?php if ( isset( $settings->feed_title_bg_hover ) && ! empty( $settings->feed_title_bg_hover ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->feed_title_bg_hover ); ?>;
	<?php } ?>
	<?php if ( $settings->feed_title_border_hover ) { ?> border-color: <?php echo pp_get_color_value( $settings->feed_title_border_hover ); ?>; <?php } ?>
	transition: all 0.3s ease-in;
}

.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-title-wrap:hover .pp-instagram-feed-title {
	color: <?php echo pp_get_color_value( $settings->feed_title_text_hover ); ?>;
}

.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-swiper-button {
	font-size: <?php echo $settings->arrow_font_size; ?>px;
	<?php if ( $settings->arrow_color ) { ?>
	color: <?php echo pp_get_color_value( $settings->arrow_color ); ?>;
	<?php } ?>
	background: <?php echo ( $settings->arrow_bg_color ) ? pp_get_color_value( $settings->arrow_bg_color ) : 'transparent'; ?>;
	<?php if ( 0 <= $settings->arrow_border_radius ) { ?>
	border-radius: <?php echo $settings->arrow_border_radius; ?>px;
	<?php } ?>
	<?php if ( 0 <= $settings->arrow_vertical_padding ) { ?>
		padding-top: <?php echo $settings->arrow_vertical_padding; ?>px;
	<?php } ?>
	<?php if ( 0 <= $settings->arrow_vertical_padding ) { ?>
		padding-bottom: <?php echo $settings->arrow_vertical_padding; ?>px;
	<?php } ?>
	<?php if ( 0 <= $settings->arrow_horizontal_padding ) { ?>
		padding-left: <?php echo $settings->arrow_horizontal_padding; ?>px;
	<?php } ?>
	<?php if ( 0 <= $settings->arrow_horizontal_padding ) { ?>
		padding-right: <?php echo $settings->arrow_horizontal_padding; ?>px;
	<?php } ?>
	<?php if ( $settings->arrow_border_style ) { ?>
		border-style: <?php echo $settings->arrow_border_style; ?>;
	<?php } ?>
	<?php if ( 0 <= $settings->arrow_border_width ) { ?>
		border-width: <?php echo $settings->arrow_border_width; ?>px;
	<?php } ?>
	<?php if ( $settings->arrow_border_color ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->arrow_border_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-swiper-button:hover {
	<?php if ( $settings->arrow_color_hover ) { ?>
		color: <?php echo pp_get_color_value( $settings->arrow_color_hover ); ?>;
	<?php } ?>
	<?php if ( $settings->arrow_bg_hover ) { ?>
		background: <?php echo pp_get_color_value( $settings->arrow_bg_hover ); ?>;
	<?php } ?>
	<?php if ( $settings->arrow_border_hover ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->arrow_border_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-instagram-feed .swiper-pagination-bullet {
	opacity: 1;
	<?php if ( $settings->dot_bg_color ) { ?>
		background: <?php echo pp_get_color_value( $settings->dot_bg_color ); ?>;
	<?php } ?>
	<?php if ( 0 <= $settings->dot_width ) { ?>
		width: <?php echo $settings->dot_width; ?>px;
	<?php } ?>
	<?php if ( 0 <= $settings->dot_width ) { ?>
		height: <?php echo $settings->dot_width; ?>px;
	<?php } ?>
	<?php if ( 0 <= $settings->dot_border_radius ) { ?>
		border-radius: <?php echo $settings->dot_border_radius; ?>px;
	<?php } ?>
	box-shadow: none;
}

.fl-node-<?php echo $id; ?> .pp-instagram-feed .swiper-pagination-bullet:hover,
.fl-node-<?php echo $id; ?> .pp-instagram-feed .swiper-pagination-bullet-active {
	<?php if ( $settings->dot_bg_hover ) { ?>
		background: <?php echo pp_get_color_value( $settings->dot_bg_hover ); ?>;
	<?php } ?>
	opacity: 1;
	box-shadow: none;
}

<?php if ( 'outside' == $settings->dot_position ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed-carousel .swiper-container {
		padding-bottom: 40px;
	}
	.fl-node-<?php echo $id; ?> .pp-instagram-feed-carousel .swiper-pagination {
		bottom: 0;
	}
<?php } ?> 

@media only screen and ( max-width: <?php echo $global_settings->medium_breakpoint; ?>px ) {

	.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item {
		width: calc( 100% / <?php echo $columns_medium; ?> );
		<?php if ( ( 'grid' == $layout || 'square-grid' == $layout ) && ! empty( $spacing_medium ) ) { ?>
			width: calc( ( 100% - <?php echo $spacing_medium * ( $columns_medium - 1 ); ?>px ) / <?php echo $columns_medium; ?> );
			margin-right: <?php echo $spacing_medium; ?>px;
			margin-bottom: <?php echo $spacing_medium; ?>px;
		<?php } ?>
		float: left;
	}

	.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item:nth-of-type(<?php echo $columns; ?>n) {
		margin-right: <?php echo $spacing; ?>px;
	}

	.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item:nth-of-type(<?php echo $columns_medium; ?>n) {
		margin-right: 0px;
	}

	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-title-wrap {
		<?php if ( 0 <= $settings->feed_title_horizontal_padding_medium ) { ?>
			padding-left: <?php echo $settings->feed_title_horizontal_padding_medium; ?>px;
			padding-right: <?php echo $settings->feed_title_horizontal_padding_medium; ?>px;
		<?php } ?>
		<?php if ( 0 <= $settings->feed_title_vertical_padding_medium ) { ?>
			padding-top: <?php echo $settings->feed_title_vertical_padding_medium; ?>px;
			padding-bottom: <?php echo $settings->feed_title_vertical_padding_medium; ?>px;
		<?php } ?>
	}
	<?php if( ( 'square-grid' == $settings->feed_layout || 'carousel' == $settings->feed_layout ) && '' != $settings->image_custom_size_medium ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item-inner {
		<?php if ( isset( $settings->aspect_ratio ) && 'yes' !== $settings->aspect_ratio ) { ?>
		width: <?php echo $settings->image_custom_size_medium; ?>px;
		<?php } ?>
		height: <?php echo $settings->image_custom_size_medium; ?>px;
	}
	<?php } ?>
	<?php if ( 'carousel' == $settings->feed_layout && $settings->visible_items_medium == '1' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-instagram-feed-carousel .swiper-container {
			max-width: <?php echo $settings->image_custom_size_medium; ?>px;
		}
	<?php } ?>
}

@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {

	.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item {
		width: calc( 100% / <?php echo $columns_responsive; ?> );
		<?php if ( ( 'grid' == $layout || 'square-grid' == $layout ) && ! empty( $spacing_responsive ) ) { ?>
			width: calc( ( 100% - <?php echo $spacing_responsive * ( $columns_responsive - 1 ); ?>px ) / <?php echo $columns_responsive; ?> );
			margin-right: <?php echo $spacing_responsive; ?>px;
			margin-bottom: <?php echo $spacing_responsive; ?>px;
		<?php } ?>
		float: left;
	}
	.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item:nth-of-type(<?php echo $columns_medium; ?>n) {
		margin-right: <?php echo $spacing_medium; ?>px;
	}

	.fl-node-<?php echo $id; ?> .pp-instagram-feed-grid .pp-feed-item:nth-of-type(<?php echo $columns_responsive; ?>n) {
		margin-right: 0px;
	}

	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-instagram-feed-title-wrap {
		<?php if ( 0 <= $settings->feed_title_horizontal_padding_responsive ) { ?>
			padding-left: <?php echo $settings->feed_title_horizontal_padding_responsive; ?>px;
			padding-right: <?php echo $settings->feed_title_horizontal_padding_responsive; ?>px;
		<?php } ?>
		<?php if ( 0 <= $settings->feed_title_vertical_padding_responsive ) { ?>
			padding-top: <?php echo $settings->feed_title_vertical_padding_responsive; ?>px;
			padding-bottom: <?php echo $settings->feed_title_vertical_padding_responsive; ?>px;
		<?php } ?>
	}
	<?php if( ( 'square-grid' == $settings->feed_layout || 'carousel' == $settings->feed_layout ) && '' != $settings->image_custom_size_responsive ) { ?>
	.fl-node-<?php echo $id; ?> .pp-instagram-feed .pp-feed-item-inner {
		<?php if ( isset( $settings->aspect_ratio ) && 'yes' !== $settings->aspect_ratio ) { ?>
		width: <?php echo '-1' === $settings->image_custom_size_responsive ? '100%' : $settings->image_custom_size_responsive . 'px'; ?>;
		<?php } ?>
		height: <?php echo $settings->image_custom_size_responsive; ?>px;
	}
	<?php } ?>
	<?php if ( 'carousel' == $settings->feed_layout && $settings->visible_items_responsive == '1' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-instagram-feed-carousel .swiper-container {
			max-width: <?php echo $settings->image_custom_size_responsive; ?>px;
		}
	<?php } ?>
}
