<?php if ( isset( $settings->use_image_as ) && 'img' === $settings->use_image_as ) { ?>
	.fl-node-<?php echo $id; ?> .pp-image-carousel,
	.fl-node-<?php echo $id; ?> .pp-image-carousel.slider-type-slideshow {
		height: auto;
	}
	.fl-node-<?php echo $id; ?> .swiper-slide-inner {
		display: inline-flex;
		flex-direction: column;
		height: 100%;
		align-items: center;
		justify-content: center;
		position: relative;
	}
	.fl-node-<?php echo $id; ?> .swiper-slide-image {
		object-fit: contain;
		height: 100%;
	}
<?php } ?>

<?php
FLBuilderCSS::responsive_rule( array(
	'settings' => $settings,
	'setting_name' => 'carousel_height',
	'prop'     => 'height',
	'unit'     => 'px',
	'selector' => ".fl-node-$id .pp-image-carousel.slider-type-slideshow, .fl-node-$id .pp-image-carousel"
) );
?>

.fl-node-<?php echo $id; ?> .pp-image-carousel.slider-type-slideshow {
	<?php if ( ! isset( $settings->thumb_position ) || ( isset( $settings->thumb_position ) && 'below' == $settings->thumb_position ) ) { ?>
		margin-bottom: <?php echo $settings->spacing; ?>px;
	<?php } ?>
	<?php if ( isset( $settings->thumb_position ) && 'above' == $settings->thumb_position ) { ?>
		margin-top: <?php echo $settings->spacing; ?>px;
	<?php } ?>
}

<?php
	// Icon - Width
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'image_padding',
		'selector'		=> ".fl-node-$id .pp-image-carousel-item",
		'prop'			=> 'padding',
		'unit'			=> 'px',
	) );
		
	// Box - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'image_border_group',
		'selector' 		=> ".fl-node-$id:not(.caption-bottom) .pp-image-carousel-item",
	) );

	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'image_border_group',
		'selector' 		=> ".fl-node-$id.caption-bottom .pp-image-carousel-item img",
	) );
?>
<?php if ( $module->is_multi_row_slider( $settings ) && ! empty( $settings->rows ) && $settings->rows > 1 ) {
	$spacing = '' !== $settings->spacing ? $settings->spacing : 20;
	?>
.fl-node-<?php echo $id; ?> .pp-image-carousel-item {
	height: calc((90% - <?php echo $spacing; ?>px) / <?php echo $settings->rows; ?>);
}
<?php } ?>
@media only screen and (max-width: <?php echo $global_settings->large_breakpoint; ?>px) {
	<?php if ( $module->is_multi_row_slider( $settings ) && ! empty( $settings->rows_large ) && $settings->rows_large > 1 ) {
	$spacing_large = '' !== $settings->spacing_large ? $settings->spacing_large : 20;
	?>
	.fl-node-<?php echo $id; ?> .pp-image-carousel-item {
		height: calc((90% - <?php echo $spacing_large; ?>px) / <?php echo $settings->rows_large; ?>);
	}
	<?php } ?>
}
@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
	<?php if ( $module->is_multi_row_slider( $settings ) && ! empty( $settings->rows_medium ) && $settings->rows_medium > 1 ) {
	$spacing_medium = '' !== $settings->spacing_medium ? $settings->spacing_medium : 20;
	?>
	.fl-node-<?php echo $id; ?> .pp-image-carousel-item {
		height: calc((90% - <?php echo $spacing_medium; ?>px) / <?php echo $settings->rows_medium; ?>);
	}
	<?php } ?>
}
@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	<?php if ( $module->is_multi_row_slider( $settings ) && ! empty( $settings->rows_responsive ) && $settings->rows_responsive > 1 ) {
	$spacing_responsive = '' !== $settings->spacing_responsive ? $settings->spacing_responsive : 20;
	?>
	.fl-node-<?php echo $id; ?> .pp-image-carousel-item {
		height: calc((90% - <?php echo $spacing_responsive; ?>px) / <?php echo $settings->rows_responsive; ?>);
	}
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-image-carousel .pp-carousel-image-container {
	background-size: <?php echo $settings->image_fit; ?>;
}
.fl-node-<?php echo $id; ?> .pp-image-carousel .pp-carousel-image-container figure.use-as-background {
	<?php if ( 'cover' == $settings->image_fit ) { ?>
		display: flex;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-image-carousel .pp-carousel-image-container figure.use-as-background .swiper-slide-image {
	object-fit: <?php echo 'auto' == $settings->image_fit ? 'none' : $settings->image_fit; ?>;
	<?php if ( 'contain' == $settings->image_fit ) { ?>
		object-position: center;
	<?php } ?>
	<?php if ( 'cover' == $settings->image_fit ) { ?>
		width: 100%;
		height: 100%;
		aspect-ratio: auto;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-carousel-image-container figure.use-as-background picture.swiper-slide-image img {
	<?php if ( 'cover' == $settings->image_fit ) { ?>
		height: 100%;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-image-carousel .swiper-pagination-bullet {
	opacity: 1;
	<?php if ( isset( $settings->pagination_bg_color ) && ! empty( $settings->pagination_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_color ); ?> !important;
	<?php } ?>
    <?php if( $settings->bullets_width >= 0 ) { ?>
    width: <?php echo $settings->bullets_width; ?>px;
    <?php } ?>
    <?php if( $settings->bullets_width >= 0 ) { ?>
    height: <?php echo $settings->bullets_width; ?>px;
    <?php } ?>
    <?php if( $settings->bullets_border_radius >= 0 ) { ?>
    border-radius: <?php echo $settings->bullets_border_radius; ?>px !important;
    <?php } ?>
	box-shadow: none !important;
	<?php if ( isset( $settings->bullets_spacing_h ) && ! empty( $settings->bullets_spacing_h ) ) { ?>
		margin-left: 0;
		margin-right: <?php echo $settings->bullets_spacing_h; ?>px;
	<?php } ?>
}
<?php if ( isset( $settings->bullets_spacing_v ) && ! empty( $settings->bullets_spacing_v ) && 'slideshow' !== $settings->carousel_type ) { ?>
	.fl-node-<?php echo $id; ?> .pp-image-carousel .swiper-wrapper {
		margin-bottom: <?php echo $settings->bullets_spacing_v; ?>px;
	}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-image-carousel.swiper-horizontal>.swiper-pagination-progressbar,
.fl-node-<?php echo $id; ?> .pp-image-carousel.swiper-container-horizontal>.swiper-pagination-progressbar {
	<?php if ( isset( $settings->pagination_bg_color ) && ! empty( $settings->pagination_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_color ); ?> !important;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-image-carousel .swiper-pagination-bullet:hover,
.fl-node-<?php echo $id; ?> .pp-image-carousel .swiper-pagination-bullet:focus,
.fl-node-<?php echo $id; ?> .pp-image-carousel .swiper-pagination-bullet-active,
.fl-node-<?php echo $id; ?> .pp-image-carousel .swiper-pagination-progressbar .swiper-pagination-progressbar-fill {
	<?php if ( isset( $settings->pagination_bg_hover ) && ! empty( $settings->pagination_bg_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_hover ); ?> !important;
	<?php } ?>
	opacity: 1;
    box-shadow: none !important;
}

<?php
	// Arrow - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'arrow_border',
		'selector' 		=> ".fl-node-$id .pp-image-carousel-wrapper .pp-swiper-button",
	) );
?>

.fl-node-<?php echo $id; ?> .pp-image-carousel-wrapper .pp-swiper-button {
	<?php if ( ! empty( $settings->arrow_font_size ) ) { ?>
	height: <?php echo $settings->arrow_font_size + 10; ?>px;
	width: <?php echo $settings->arrow_font_size + 10; ?>px;
	<?php } ?>
	<?php if( $settings->arrow_color ) { ?>
	color: <?php echo pp_get_color_value( $settings->arrow_color ); ?> !important;
    <?php } ?>
	<?php if ( isset( $settings->arrow_bg_color ) && ! empty( $settings->arrow_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->arrow_bg_color ); ?> !important;
	<?php } ?>
    <?php if( $settings->arrow_vertical_padding >= 0 ) { ?>
    padding-top: <?php echo $settings->arrow_vertical_padding; ?>px;
    <?php } ?>
    <?php if( $settings->arrow_vertical_padding >= 0 ) { ?>
    padding-bottom: <?php echo $settings->arrow_vertical_padding; ?>px;
    <?php } ?>
    <?php if( $settings->arrow_horizontal_padding >= 0 ) { ?>
    padding-left: <?php echo $settings->arrow_horizontal_padding; ?>px;
    <?php } ?>
    <?php if( $settings->arrow_horizontal_padding >= 0 ) { ?>
    padding-right: <?php echo $settings->arrow_horizontal_padding; ?>px;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-image-carousel-wrapper .pp-swiper-button svg {
	height: <?php echo $settings->arrow_font_size; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-image-carousel-wrapper .pp-swiper-button svg path {
	<?php if( $settings->arrow_color ) { ?>
	fill: <?php echo pp_get_color_value( $settings->arrow_color ); ?> !important;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-image-carousel-wrapper .pp-swiper-button:hover,
.fl-node-<?php echo $id; ?> .pp-image-carousel-wrapper .pp-swiper-button:focus {
    <?php if( $settings->arrow_color_hover ) { ?>
    color: <?php echo pp_get_color_value( $settings->arrow_color_hover ); ?> !important;
    <?php } ?>
	<?php if ( isset( $settings->arrow_bg_hover ) && ! empty( $settings->arrow_bg_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->arrow_bg_hover ); ?> !important;
	<?php } ?>
    <?php if( $settings->arrow_border_hover ) { ?>
    border-color: <?php echo pp_get_color_value( $settings->arrow_border_hover ); ?> !important;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-image-carousel-wrapper .pp-swiper-button:hover svg path,
.fl-node-<?php echo $id; ?> .pp-image-carousel-wrapper .pp-swiper-button:focus svg path {
	<?php if( $settings->arrow_color_hover ) { ?>
    fill: <?php echo pp_get_color_value( $settings->arrow_color_hover ); ?> !important;
    <?php } ?>
}

<?php if( 'bullets' == $settings->pagination_type || 'fraction' == $settings->pagination_type ) { ?>
	.fl-node-<?php echo $id; ?> .pp-image-carousel-wrapper.pp-nav-outside .pp-image-carousel {
		padding-bottom: 30px;
	}
<?php } ?>


<?php if($settings->click_action == 'lightbox') : ?>
.mfp-<?php echo $id; ?>.mfp-gallery img.mfp-img {
	padding: 40px 0 40px;
}

.mfp-<?php echo $id; ?> .mfp-counter {
	display: block !important;
}
<?php endif; ?>

<?php if( $settings->overlay_effects != 'none' ) : ?>
.fl-node-<?php echo $id; ?> .pp-image-overlay {
	<?php if( $settings->overlay_type == 'solid' ) { ?>
		background: <?php echo ($settings->overlay_color != '' ) ? pp_hex2rgba(pp_get_color_value( $settings->overlay_color ), ($settings->overlay_color_opacity/ 100)) : 'rgba(0,0,0,.5)'; ?>;
	<?php } ?>

	<?php if( $settings->overlay_type == 'gradient' ) : ?>
		background: -moz-linear-gradient(top,  <?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_primary_color ), ($settings->overlay_color_opacity/ 100)); ?> 0%, <?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_secondary_color ), ($settings->overlay_color_opacity/ 100)); ?> 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_primary_color ), ($settings->overlay_color_opacity/ 100)); ?>), color-stop(100%,<?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_secondary_color ), ($settings->overlay_color_opacity/ 100)); ?>)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  <?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_primary_color ), ($settings->overlay_color_opacity/ 100)); ?> 0%,<?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_secondary_color ), ($settings->overlay_color_opacity/ 100)); ?> 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  <?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_primary_color ), ($settings->overlay_color_opacity/ 100)); ?> 0%,<?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_secondary_color ), ($settings->overlay_color_opacity/ 100)); ?> 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  <?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_primary_color ), ($settings->overlay_color_opacity/ 100)); ?> 0%,<?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_secondary_color ), ($settings->overlay_color_opacity/ 100)); ?> 100%); /* IE10+ */
		background: linear-gradient(to bottom,  <?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_primary_color ), ($settings->overlay_color_opacity/ 100)); ?> 0%,<?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_secondary_color ), ($settings->overlay_color_opacity/ 100)); ?> 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_primary_color ), ($settings->overlay_color_opacity/ 100)); ?>', endColorstr='<?php echo pp_hex2rgba(pp_get_color_value( $settings->overlay_secondary_color ), ($settings->overlay_color_opacity/ 100)); ?>',GradientType=0 ); /* IE6-9 */
	<?php endif; ?>
	-webkit-transition: opacity <?php echo ($settings->overlay_animation_speed/1000); ?>s,-webkit-transform <?php echo ($settings->overlay_animation_speed/1000); ?>s;
    transition: opacity <?php echo ($settings->overlay_animation_speed/1000); ?>s,-webkit-transform <?php echo ($settings->overlay_animation_speed/1000); ?>s;
    -o-transition: transform <?php echo ($settings->overlay_animation_speed/1000); ?>s,opacity <?php echo ($settings->overlay_animation_speed/1000); ?>s;
    transition: transform <?php echo ($settings->overlay_animation_speed/1000); ?>s,opacity <?php echo ($settings->overlay_animation_speed/1000); ?>s;
    transition: transform <?php echo ($settings->overlay_animation_speed/1000); ?>s,opacity <?php echo ($settings->overlay_animation_speed/1000); ?>s,-webkit-transform <?php echo ($settings->overlay_animation_speed/1000); ?>s;
}
<?php endif; ?>

.fl-node-<?php echo $id; ?> .pp-image-overlay .pp-overlay-icon {
	<?php $overlay_icon_size = ! empty($settings->overlay_icon_size) ? $settings->overlay_icon_size : 0; ?>
	<?php $overlay_icon_padding = ! empty($settings->overlay_icon_padding) ? $settings->overlay_icon_padding : 0; ?>
	width: <?php echo ( $overlay_icon_size + ( $overlay_icon_padding * 2) ); ?>px;
	height: <?php echo ( $overlay_icon_size + ( $overlay_icon_padding * 2) ); ?>px;
}

.fl-node-<?php echo $id; ?> .pp-image-overlay .pp-overlay-icon span {
	color: <?php echo pp_get_color_value( $settings->overlay_icon_color ); ?>;
	font-size: <?php echo $settings->overlay_icon_size; ?>px;
	<?php if ( isset( $settings->overlay_icon_bg_color ) && ! empty( $settings->overlay_icon_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->overlay_icon_bg_color ); ?>;
	<?php } ?>
	<?php if( $settings->overlay_icon_radius ) { ?>border-radius: <?php echo $settings->overlay_icon_radius; ?>px;<?php } ?>
	<?php if( $settings->overlay_icon_padding ) { ?>padding: <?php echo $settings->overlay_icon_padding; ?>px;<?php } ?>
}

<?php if ( ! empty( $settings->caption_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-image-overlay .pp-caption  {
	color: <?php echo pp_get_color_value( $settings->caption_color ); ?>;
}
<?php } ?>

<?php
	// Caption Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'caption_typography',
		'selector' 		=> ".fl-node-$id .pp-image-overlay .pp-caption",
	) );
?>

<?php if( $settings->overlay_effects == 'framed' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-image-overlay:before,
	.fl-node-<?php echo $id; ?> .pp-image-overlay:after {
		content: '';
	    display: block;
	    position: absolute;
	    top: <?php echo ( $settings->overlay_spacing ) ? $settings->overlay_spacing . 'px' : '30px'; ?>;
	    left: <?php echo ( $settings->overlay_spacing ) ? $settings->overlay_spacing . 'px' : '30px'; ?>;
	    bottom: <?php echo ( $settings->overlay_spacing ) ? $settings->overlay_spacing . 'px' : '30px'; ?>;
	    right: <?php echo ( $settings->overlay_spacing ) ? $settings->overlay_spacing . 'px' : '30px'; ?>;
	    -webkit-transition: -webkit-transform .35s ease 0s;
	    transition: transform .35s ease 0s;
	}
	.fl-node-<?php echo $id; ?> .pp-image-overlay:before {
		border-style: solid;
		border-width: 0;
		border-color: <?php echo ( $settings->overlay_border_color ) ? pp_get_color_value( $settings->overlay_border_color ) : '#ffffff'; ?>;
		border-top-width: <?php echo ( $settings->overlay_border_width ) ? $settings->overlay_border_width . 'px' : '1px'; ?>;
		border-bottom-width: <?php echo ( $settings->overlay_border_width ) ? $settings->overlay_border_width . 'px' : '1px'; ?>;
		-webkit-transform: scale(0,1);
		-ms-transform: scale(0,1);
		transform: scale(0,1);
	}
	.fl-node-<?php echo $id; ?> .pp-image-overlay:after {
		border-style: solid;
		border-width: 0;
		border-color: <?php echo ( $settings->overlay_border_color ) ? pp_get_color_value( $settings->overlay_border_color ) : '#ffffff'; ?>;
		border-left-width: <?php echo ( $settings->overlay_border_width ) ? $settings->overlay_border_width . 'px' : '1px'; ?>;
		border-right-width: <?php echo ( $settings->overlay_border_width ) ? $settings->overlay_border_width . 'px' : '1px'; ?>;
	    -webkit-transform: scale(1,0);
	    -ms-transform: scale(1,0);
	    transform: scale(1,0);
	}

	.fl-node-<?php echo $id; ?> .pp-image-carousel-item:hover .pp-image-overlay:before,
	.fl-node-<?php echo $id; ?> .pp-image-carousel-item:hover .pp-image-overlay:after,
	.fl-node-<?php echo $id; ?> .pp-image-carousel-item:focus .pp-image-overlay:before,
	.fl-node-<?php echo $id; ?> .pp-image-carousel-item:focus .pp-image-overlay:after {
		-webkit-transform: scale(1);
	    -ms-transform: scale(1);
	    transform: scale(1);
	}

	.fl-node-<?php echo $id; ?> .pp-image-carousel-content:hover .pp-image-overlay,
	.fl-node-<?php echo $id; ?> .pp-image-carousel-content:focus .pp-image-overlay {
		opacity: 1;
	}
<?php } ?>