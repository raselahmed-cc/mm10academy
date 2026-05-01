<?php
$photo_border_width  = ( isset( $settings->photo_border_group ) && ! empty( $settings->photo_border_group['width'] ) ) ? $settings->photo_border_group['width'] : 0;
$photo_border_radius = ( isset( $settings->photo_border_group ) && ! empty( $settings->photo_border_group['radius'] ) ) ? $settings->photo_border_group['radius'] : 0;
$photo_border        = ( isset( $settings->photo_border_group ) && ! empty( $settings->photo_border_group['style'] ) ) ? $settings->photo_border_group['style'] : 'none';

$columns            = '' === $settings->photo_grid_count ? 3 : (int) $settings->photo_grid_count;
$columns_large      = '' === $settings->photo_grid_count_large ? $columns : (int) $settings->photo_grid_count_large;
$columns_medium     = '' === $settings->photo_grid_count_medium ? $columns_large : (int) $settings->photo_grid_count_medium;
$columns_responsive = '' === $settings->photo_grid_count_responsive ? $columns_medium : (int) $settings->photo_grid_count_responsive;

$spacing            = '' === $settings->photo_spacing ? 0 : $settings->photo_spacing;
$spacing_large      = isset( $settings->photo_spacing_large ) && '' === $settings->photo_spacing_large ? $spacing : $settings->photo_spacing_large;
$spacing_medium     = isset( $settings->photo_spacing_medium ) && '' === $settings->photo_spacing_medium ? $spacing_large : $settings->photo_spacing_medium;
$spacing_responsive = isset( $settings->photo_spacing_responsive ) && '' === $settings->photo_spacing_responsive ? $spacing_medium : $settings->photo_spacing_responsive;

$item_width            = ( 100 - ( ( $columns - 1 ) * $spacing ) ) / $columns;
$item_width_large      = ( 100 - ( ( $columns_large - 1 ) * $spacing_large ) ) / $columns_large;
$item_width_medium     = ( 100 - ( ( $columns_medium - 1 ) * $spacing_medium ) ) / $columns_medium;
$item_width_responsive = ( 100 - ( ( $columns_responsive - 1 ) * $spacing_responsive ) ) / $columns_responsive;
?>

<?php if($settings->click_action == 'lightbox' && !empty($settings->show_captions)) : ?>
.mfp-<?php echo $id; ?>.mfp-gallery img.mfp-img {
	padding: 40px 0 40px;
}

.mfp-<?php echo $id; ?> .mfp-counter {
	display: block !important;
}
<?php endif; ?>

<?php if ( '' !== $spacing ) { ?>
.fl-node-<?php echo $id; ?> .pp-photo-gallery {
	margin: -<?php echo $spacing / 2; ?>px;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-grid-sizer {
	width: <?php echo $item_width; ?>%;
}

.fl-node-<?php echo $id; ?> .pp-photo-space {
	width: <?php echo $spacing >= 0 ? $spacing : 0; ?>%;
}

<?php if ( $settings->gallery_layout == 'grid' ) { ?>
	<?php if ( $settings->photo_grid_count > 1 ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $settings->photo_grid_count; ?>n) {
		margin-right: 0;
	}
	<?php } ?>
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-gallery-item {
	width: <?php echo $item_width; ?>%;
	<?php if ( '' !== $spacing ) { ?>
	margin-bottom: <?php echo $spacing; ?>%;
	<?php } ?>
	<?php if ( 0 == $spacing && 'grid' == $settings->gallery_layout ) { ?>
		margin-right: <?php echo $spacing - ( 'none' != $photo_border ? $photo_border_width['left'] : 0 ); ?>px;
		margin-bottom: <?php echo $spacing - ( 'none' != $photo_border ? $photo_border_width['top'] : 0 ); ?>px;
	<?php } ?>
	overflow: hidden;
}
<?php
	// Photo - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'photo_border_group',
		'selector' 		=> ".fl-node-$id .pp-gallery-item",
	) );

	// gallery Items - Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'photo_padding',
		'selector' 		=> ".fl-node-$id .pp-gallery-item",
		'unit'			=> 'px',
		'props'			=> array(
			'padding-top' 		=> 'photo_padding_top',
			'padding-right' 	=> 'photo_padding_right',
			'padding-bottom' 	=> 'photo_padding_bottom',
			'padding-left' 		=> 'photo_padding_left',
		),
	) );
?>

.fl-node-<?php echo $id; ?> .pp-gallery-item .pp-photo-gallery-content > a {
	display: block;
	line-height: 0;
}

<?php
/************************************
 * Overlay and Caption
 ************************************/
?>

<?php if( $settings->overlay_effects != 'none' || $settings->show_captions == 'hover' ) : ?>
.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
	<?php if ( isset( $settings->overlay_color ) && ! empty( $settings->overlay_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->overlay_color ); ?>;
	<?php } ?>
}
<?php endif; ?>
.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-icon span {
	color: <?php echo pp_get_color_value( $settings->overlay_icon_color ); ?>;
	<?php if ( isset( $settings->overlay_icon_bg_color ) && ! empty( $settings->overlay_icon_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->overlay_icon_bg_color ); ?>;
	<?php } ?>
}

<?php
	// Overlay Icon - Font Size
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'overlay_icon_size',
		'selector'		=> ".fl-node-$id .pp-gallery-overlay .pp-overlay-icon span, .fl-node-$id .pp-gallery-overlay .pp-overlay-icon span:before",
		'prop'			=> 'font-size',
		'unit'			=> 'px',
	) );

	// Overlay Icon - Border Radius
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'overlay_icon_radius',
		'selector'		=> ".fl-node-$id .pp-gallery-overlay .pp-overlay-icon span",
		'prop'			=> 'border-radius',
		'unit'			=> 'px',
	) );

	// Overlay Icon - Padding
	FLBuilderCSS::responsive_rule( array(
		'settings'		=> $settings,
		'setting_name'	=> 'overlay_icon_padding',
		'selector'		=> ".fl-node-$id .pp-gallery-overlay .pp-overlay-icon span",
		'prop'			=> 'padding',
		'unit'			=> 'px',
	) );
?>

<?php if ( $settings->show_captions == 'below' ) { ?>
.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption {
	<?php if ( isset( $settings->caption_bg_color ) && ! empty( $settings->caption_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->caption_bg_color ); ?>;
	<?php } ?>
	text-align: <?php echo $settings->caption_alignment; ?>;
}
<?php } ?>

<?php
	// Caption - Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'caption_padding',
		'selector' 		=> ".fl-node-$id .pp-photo-gallery-caption",
		'unit'			=> 'px',
		'props'			=> array(
			'padding-top' 		=> 'caption_padding_top',
			'padding-right' 	=> 'caption_padding_right',
			'padding-bottom' 	=> 'caption_padding_bottom',
			'padding-left' 		=> 'caption_padding_left',
		),
	) );
?>

.fl-node-<?php echo $id; ?> .pp-photo-gallery-caption {
	color: <?php echo pp_get_color_value( $settings->caption_color ); ?>;
}

<?php
// Caption Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'caption_typography',
	'selector' 		=> ".fl-node-$id .pp-photo-gallery-caption",
) );
?>

<?php
/************************************
 * Filters
 ************************************/
?>
.fl-node-<?php echo $id; ?> .pp-gallery-filters {
	text-align: <?php echo $settings->filter_alignment; ?>;
	<?php if ( 'center' == $settings->filter_alignment ) { ?>
		justify-content: center;
	<?php } ?>
	<?php if ( 'left' == $settings->filter_alignment ) { ?>
		justify-content: flex-start;
	<?php } ?>
	<?php if ( 'right' == $settings->filter_alignment ) { ?>
		justify-content: flex-end;
	<?php } ?>
	margin-bottom: <?php echo $settings->filter_margin_bottom; ?>px;
}

<?php
// Filter Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'filter_typography',
	'selector' 		=> ".fl-node-$id .pp-gallery-filters li",
) );

// Filter - Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'filter_padding',
	'selector' 		=> ".fl-node-$id .pp-gallery-filters li",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'filter_padding_top',
		'padding-right' 	=> 'filter_padding_right',
		'padding-bottom' 	=> 'filter_padding_bottom',
		'padding-left' 		=> 'filter_padding_left',
	),
) );
// Filter - Border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'filter_border_group',
	'selector' 		=> ".fl-node-$id .pp-gallery-filters li",
) );
?>

.fl-node-<?php echo $id; ?> .pp-gallery-filters li {
	<?php if ( isset( $settings->filter_bg_color ) && ! empty( $settings->filter_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->filter_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->filter_text_color ) && ! empty( $settings->filter_text_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->filter_text_color ); ?>;
	<?php } ?>
	<?php if ( '' !== $settings->filter_margin ) { ?>
		margin-right: <?php echo $settings->filter_margin; ?>px;
		margin-bottom: <?php echo ($settings->filter_margin / 2); ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-gallery-filters li:hover,
.fl-node-<?php echo $id; ?> .pp-gallery-filters li.pp-filter-active {
	<?php if ( isset( $settings->filter_bg_hover ) && ! empty( $settings->filter_bg_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->filter_bg_hover ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->filter_text_hover ) && ! empty( $settings->filter_text_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->filter_text_hover ); ?>;
	<?php } ?>
	border-color: <?php echo ( $settings->filter_border_color_hover ) ? pp_get_color_value( $settings->filter_border_color_hover ) : 'transparent'; ?>;
}

<?php if ( $settings->overlay_effects == 'none' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		height: 100%;
		width: 100%;
		opacity: 0;
		transition: none;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		opacity: 1;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-caption {
		line-height: 1;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'fade' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		height: 100%;
		width: 100%;
		opacity: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		opacity: 1;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'from-left' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		bottom: 0;
		left: 0;
		right: 0;
		width: 0;
		height: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		white-space: nowrap;
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		width: 100%;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'from-right' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		bottom: 0;
		left: 100%;
		right: 0;
		width: 0;
		height: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		white-space: nowrap;
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		width: 100%;
		left: 0;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'from-top' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		bottom: 100%;
		left: 0;
		right: 0;
		width: 100%;
		height: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		white-space: nowrap;
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		height: 100%;
		bottom: 0;
	}
<?php } ?>

<?php if( $settings->overlay_effects == 'from-bottom' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		bottom: 0;
		left: 0;
		right: 0;
		width: 100%;
		height: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		white-space: nowrap;
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		height: 100%;
	}
<?php } ?>

<?php if( $settings->hover_effects == 'zoom-in' || $settings->hover_effects == 'zoom-out' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay {
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		opacity: 0;
		overflow: hidden;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-overlay .pp-overlay-inner {
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		-webkit-flex-direction: column;
		-ms-flex-direction: column;
		flex-direction: column;
		-webkit-box-pack: center;
		-webkit-justify-content: center;
		-ms-flex-pack: center;
		justify-content: center;
		display: -webkit-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		height: 100%;
		width: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-gallery-content:hover .pp-gallery-overlay {
		opacity: 1;
		transform: translate(0);
	}
<?php } ?>

<?php if ( $settings->hover_effects == 'zoom-in' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img {
		transform: scale(1.1);
	}
<?php } ?>

<?php if ( $settings->hover_effects == 'zoom-out' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery .pp-photo-gallery-content:hover .pp-gallery-img {
		transform: scale(1,1);
	}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-filterable-gallery-pagination a {
	<?php if ( isset( $settings->pagination_bg_color ) && ! empty( $settings->pagination_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->pagination_color ) && ! empty( $settings->pagination_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->pagination_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-filterable-gallery-pagination a:hover {
	<?php if ( isset( $settings->pagination_bg_hover_color ) && ! empty( $settings->pagination_bg_hover_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_hover_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->pagination_hover_color ) && ! empty( $settings->pagination_hover_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->pagination_hover_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->pagination_border_hover_color ) && ! empty( $settings->pagination_border_hover_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->pagination_border_hover_color ); ?>;
	<?php } ?>
}

<?php
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'pagination_align',
	'selector'     => ".fl-node-$id .pp-filterable-gallery-pagination",
	'prop'         => 'text-align'
) );

FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'pagination_border',
	'selector'     => ".fl-node-$id .pp-filterable-gallery-pagination a"
) );

FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'pagination_padding',
	'selector'     => ".fl-node-$id .pp-filterable-gallery-pagination a",
	'unit'         => 'px',
	'props' => array(
		'padding-top'    => 'pagination_padding_top',
		'padding-right'  => 'pagination_padding_right',
		'padding-bottom' => 'pagination_padding_bottom',
		'padding-left'   => 'pagination_padding_left',
	)
) );

FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'pagination_margin',
	'selector'     => ".fl-node-$id .pp-filterable-gallery-pagination",
	'unit'         => 'px',
	'props' => array(
		'margin-top'    => 'pagination_margin_top',
		'margin-right'  => 'pagination_margin_right',
		'margin-bottom' => 'pagination_margin_bottom',
		'margin-left'   => 'pagination_margin_left',
	)
) );

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'pagination_typography',
	'selector'     => ".fl-node-$id .pp-filterable-gallery-pagination a",
) );
?>

@media only screen and ( max-width: <?php echo $global_settings->large_breakpoint; ?>px ) {
	<?php if ( '' !== $spacing_large ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery {
		margin: -<?php echo $spacing_large / 2; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-space {
		width: <?php echo $spacing_large >= 0 ? $spacing_large : 0; ?>%;
	}
	<?php } ?>

	.fl-node-<?php echo $id; ?> .pp-grid-sizer {
		width: <?php echo $item_width_large; ?>%;
	}

	.fl-node-<?php echo $id; ?> .pp-gallery-item {
		width: <?php echo $item_width_large; ?>%;
		<?php if ( '' !== $spacing_large ) { ?>
		margin-bottom: <?php echo $spacing_large; ?>%;
		<?php } ?>
		<?php if ( 0 == $spacing_large && 'grid' == $settings->gallery_layout ) { ?>
			margin-right: <?php echo $spacing_large - ( 'none' != $photo_border ? $photo_border_width['left'] : 0 ); ?>px;
			margin-bottom: <?php echo $spacing_large - ( 'none' != $photo_border ? $photo_border_width['top'] : 0 ); ?>px;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns; ?>n+1){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns; ?>n+0){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns_large; ?>n){
		margin-right: 0;
	}
}

@media only screen and ( max-width: <?php echo $global_settings->medium_breakpoint; ?>px ) {
	<?php if ( '' !== $spacing_medium ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery {
		margin: -<?php echo $spacing_medium / 2; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-space {
		width: <?php echo $spacing_medium >= 0 ? $spacing_medium : 0; ?>%;
	}
	<?php } ?>

	.fl-node-<?php echo $id; ?> .pp-grid-sizer {
		width: <?php echo $item_width_medium; ?>%;
	}

	.fl-node-<?php echo $id; ?> .pp-gallery-item {
		width: <?php echo $item_width_medium; ?>%;
		<?php if ( '' !== $spacing_medium ) { ?>
		margin-bottom: <?php echo $spacing_medium; ?>%;
		<?php } ?>
		<?php if ( 0 == $spacing_medium && 'grid' == $settings->gallery_layout ) { ?>
			margin-right: <?php echo $spacing_medium - ( 'none' != $photo_border ? $photo_border_width['left'] : 0 ); ?>px;
			margin-bottom: <?php echo $spacing_medium - ( 'none' != $photo_border ? $photo_border_width['top'] : 0 ); ?>px;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns_large; ?>n+1){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns_large; ?>n+0){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns_medium; ?>n){
		margin-right: 0;
	}
}

@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {
	<?php if ( '' !== $spacing_responsive ) { ?>
	.fl-node-<?php echo $id; ?> .pp-photo-gallery {
		margin: -<?php echo $spacing_responsive / 2; ?>px;
	}
	.fl-node-<?php echo $id; ?> .pp-photo-space {
		width: <?php echo $spacing_responsive >= 0 ? $spacing_responsive : 0; ?>%;
	}
	<?php } ?>

	.fl-node-<?php echo $id; ?> .pp-grid-sizer {
		width: <?php echo $item_width_responsive; ?>%;
	}

	.fl-node-<?php echo $id; ?> .pp-gallery-item {
		width: <?php echo $item_width_responsive; ?>%;
		<?php if ( '' !== $spacing_responsive ) { ?>
		margin-bottom: <?php echo $spacing_responsive; ?>%;
		<?php } ?>
		<?php if ( 0 == $spacing_responsive && 'grid' == $settings->gallery_layout ) { ?>
			margin-right: <?php echo $spacing_responsive - ( 'none' != $photo_border ? $photo_border_width['left'] : 0 ); ?>px;
			margin-bottom: <?php echo $spacing_responsive - ( 'none' != $photo_border ? $photo_border_width['top'] : 0 ); ?>px;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns_medium; ?>n+1){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns_medium; ?>n+0){
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-grid-item:nth-child(<?php echo $columns_responsive; ?>n){
		margin-right: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters-toggle {
		display: block;
		<?php if ( isset( $settings->filter_toggle_bg ) && ! empty( $settings->filter_toggle_bg ) ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->filter_toggle_bg ); ?>;
		<?php } ?>
		<?php if ( isset( $settings->filter_toggle_border ) && $settings->filter_toggle_border ) { ?>
			border: <?php echo $settings->filter_toggle_border; ?>px solid <?php echo pp_get_color_value( $settings->filter_toggle_border_color ); ?>;
		<?php } ?>
		<?php if ( isset( $settings->filter_toggle_radius ) && $settings->filter_toggle_radius ) { ?>
			border-radius: <?php echo $settings->filter_toggle_radius; ?>px;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters-toggle .toggle-text {
		<?php if( $settings->filter_toggle_color ) { ?>color: <?php echo pp_get_color_value( $settings->filter_toggle_color ); ?>;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters-toggle:after {
		<?php if( $settings->filter_toggle_icon_color ) { ?>color: <?php echo pp_get_color_value( $settings->filter_toggle_icon_color ); ?>;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters {
		flex-direction: column;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters:not(.pp-gallery-filters-open) {
		visibility: hidden;
		opacity: 0;
		font-size: 0;
		line-height: 0;
		margin: 0;
		padding: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters:not(.pp-gallery-filters-open) li {
		margin: 0;
		padding: 0;
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters li {
		display: block;
		float: none;
		margin: 0 !important;
		text-align: left;

		<?php if ( isset( $settings->filter_res_bg_color ) && ! empty( $settings->filter_res_bg_color ) ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->filter_res_bg_color ); ?>;
		<?php } ?>
		<?php if ( isset( $settings->filter_res_text_color ) && ! empty( $settings->filter_res_text_color ) ) { ?>
			color: <?php echo pp_get_color_value( $settings->filter_res_text_color ); ?>;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-gallery-filters li:hover,
	.fl-node-<?php echo $id; ?> .pp-gallery-filters li.pp-filter-active {
		<?php if ( isset( $settings->filter_res_bg_hover ) && ! empty( $settings->filter_res_bg_hover ) ) { ?>
			background-color: <?php echo pp_get_color_value( $settings->filter_res_bg_hover ); ?>;
		<?php } ?>
		<?php if ( isset( $settings->filter_res_text_hover ) && ! empty( $settings->filter_res_text_hover ) ) { ?>
			color: <?php echo pp_get_color_value( $settings->filter_res_text_hover ); ?>;
		<?php } ?>
	}
}
