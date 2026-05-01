
<?php
$use_css_grid   = $module->use_css_grid( $settings );
$columns 		= $module->get_post_columns( $settings );
$column_desktop = $columns['xl'];
$column_laptop  = $columns['lg'];
$column_tablet  = $columns['md'];
$column_mobile  = $columns['sm'];
$spacing		= isset( $settings->post_spacing ) && '' !== $settings->post_spacing ? $settings->post_spacing : 0;
$spacing_large	= isset( $settings->post_spacing_large ) && '' !== $settings->post_spacing_large ? $settings->post_spacing_large : $spacing;
$spacing_tablet	= isset( $settings->post_spacing_medium ) && '' !== $settings->post_spacing_medium ? $settings->post_spacing_medium : $spacing_large;
$spacing_mobile	= isset( $settings->post_spacing_responsive ) && '' !== $settings->post_spacing_responsive ? $settings->post_spacing_responsive : $spacing_tablet;
$spacing_unit	= isset( $settings->post_spacing_unit ) ? $settings->post_spacing_unit : 'px';
$spacing_unit_large	 = $spacing_unit;
$spacing_unit_tablet = $spacing_unit_large;
$spacing_unit_mobile = $spacing_unit_tablet;
$space_desktop	= ( $column_desktop - 1 ) * $spacing;
$space_large	= ( $column_laptop - 1 ) * $spacing_large;
$space_tablet 	= ( $column_tablet - 1 ) * $spacing_tablet;
$space_mobile 	= ( $column_mobile - 1 ) * $spacing_mobile;
$post_columns_desktop = ( 100 - $space_desktop ) / $column_desktop;
$post_columns_large = ( 100 - $space_large ) / $column_laptop;
$post_columns_tablet = ( 100 - $space_tablet ) / $column_tablet;
$post_columns_mobile = ( 100 - $space_mobile ) / $column_mobile;
$responsive_filter = $settings->responsive_filter;
?>
<?php
// Image Effects
if ( isset( $settings->show_image_effect ) && 'yes' === $settings->show_image_effect ){
	pp_image_effect_render_style(
		$settings,
		".fl-node-$id .pp-content-grid-image img, .fl-node-$id .pp-content-grid-image .pp-post-featured-img"
	);
	pp_image_effect_render_style(
		$settings,
		".fl-node-$id .pp-content-post:hover .pp-content-grid-image img, .fl-node-$id .pp-content-post:hover .pp-content-grid-image .pp-post-featured-img",
		true
	);
}
?>

/* Fix for Box module */
.fl-node-<?php echo $id; ?> {
	min-width: 1px;
}

<?php if ( isset( $settings->post_grid_filters_display ) && $settings->post_grid_filters_display == 'yes' ) { ?>
.fl-node-<?php echo $id; ?> .pp-content-post {
    position: relative;
    float: left;
}
.fl-node-<?php echo $id; ?> ul.pp-post-filters {
	text-align: <?php echo $settings->filter_alignment; ?>;
}
<?php $filter_margin = (float) $settings->filter_margin; ?>
.fl-node-<?php echo $id; ?> ul.pp-post-filters li {
	<?php if ( isset( $settings->filter_bg_color ) && ! empty( $settings->filter_bg_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->filter_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->filter_text_color ) && ! empty( $settings->filter_text_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->filter_text_color ); ?>;
	<?php } ?>
	border-color: transparent;

	margin-right: <?php echo $filter_margin; ?>px;
	margin-bottom: <?php echo ($filter_margin / 2); ?>px;
}
.rtl .fl-node-<?php echo $id; ?> ul.pp-post-filters li {
	margin-left: <?php echo $filter_margin; ?>px;
	margin-right: 0;
}
<?php
// Filter Border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'filter_border_group',
	'selector' 		=> ".fl-node-$id ul.pp-post-filters li",
) );

// Filter Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'filter_padding',
	'selector' 		=> ".fl-node-$id ul.pp-post-filters li",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'filter_padding_top',
		'padding-right' 	=> 'filter_padding_right',
		'padding-bottom' 	=> 'filter_padding_bottom',
		'padding-left' 		=> 'filter_padding_left',
	),
) );

// Filter typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'filter_typography',
	'selector'		=> ".fl-node-$id .pp-post-filters-wrapper",
) );
?>
.fl-node-<?php echo $id; ?> ul.pp-post-filters li {
	outline: 0;
}
.fl-node-<?php echo $id; ?> ul.pp-post-filters li:hover,
.fl-node-<?php echo $id; ?> ul.pp-post-filters li:focus,
.fl-node-<?php echo $id; ?> ul.pp-post-filters li.pp-filter-active {
	<?php if ( isset( $settings->filter_bg_color_active ) && ! empty( $settings->filter_bg_color_active ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->filter_bg_color_active ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->filter_text_color_active ) && ! empty( $settings->filter_text_color_active ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->filter_text_color_active ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->filter_border_hover_color ) && ! empty( $settings->filter_border_hover_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->filter_border_hover_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-post-grid.pp-is-filtering:after {
	background-image: url(<?php echo BB_POWERPACK_URL; ?>assets/images/spinner.gif);
}

.fl-node-<?php echo $id; ?> .pp-post-filters-toggle {
	background: <?php echo ( isset( $settings->filter_toggle_bg ) && ! empty( $settings->filter_toggle_bg ) ) ? pp_get_color_value( $settings->filter_toggle_bg ) : 'none'; ?>;
	<?php if ( isset( $settings->filter_toggle_color ) && ! empty( $settings->filter_toggle_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->filter_toggle_color ); ?>;
	<?php } ?>
}
<?php
// Filter Toggle Border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'filter_toggle_border_group',
	'selector' 		=> ".fl-node-$id .pp-post-filters-toggle",
) );
?>

<?php } ?>

.fl-node-<?php echo $id; ?> .pp-content-grid-pagination {
	<?php if ( isset( $settings->pagination_align ) ) { ?>
		text-align: <?php echo $settings->pagination_align; ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-grid-pagination.fl-builder-pagination {
    padding-top: <?php echo $settings->pagination_spacing_v; ?>px;
    padding-bottom: <?php echo $settings->pagination_spacing_v; ?>px;
}
<?php
// Pagination Border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'pagination_border_group',
	'selector' 		=> ".fl-node-$id .pp-content-grid-pagination li a.page-numbers, .fl-node-$id .pp-content-grid-pagination li span.page-numbers, .fl-node-$id .pp-content-grid-load-more a",
) );

// Pagination Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'pagination_padding',
	'selector' 		=> ".fl-node-$id .pp-content-grid-pagination li a.page-numbers, .fl-node-$id .pp-content-grid-pagination li span.page-numbers, .fl-node-$id .pp-content-grid-load-more a",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'pagination_padding_top',
		'padding-right' 	=> 'pagination_padding_right',
		'padding-bottom' 	=> 'pagination_padding_bottom',
		'padding-left' 		=> 'pagination_padding_left',
	),
) );

// Pagination font-size
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'pagination_font_size',
	'selector'		=> ".fl-node-$id .pp-content-grid-pagination li a.page-numbers, .fl-node-$id .pp-content-grid-pagination li span.page-numbers, .fl-node-$id .pp-content-grid-load-more a",
	'prop'			=> 'font-size',
	'unit'			=> 'px'
) );
?>
.fl-node-<?php echo $id; ?> .pp-content-grid-pagination li a.page-numbers,
.fl-node-<?php echo $id; ?> .pp-content-grid-pagination li span.page-numbers {
	<?php if ( isset( $settings->pagination_bg_color ) && ! empty( $settings->pagination_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->pagination_color ) && ! empty( $settings->pagination_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->pagination_color ); ?>;
	<?php } ?>
	margin-right: <?php echo $settings->pagination_spacing; ?>px;
}

.fl-node-<?php echo $id; ?> .pp-content-grid-pagination li a.page-numbers:hover,
.fl-node-<?php echo $id; ?> .pp-content-grid-pagination li span.current,
.fl-node-<?php echo $id; ?> .pp-content-grid-pagination li span[aria-current] {
	<?php if ( isset( $settings->pagination_bg_color_hover ) && ! empty( $settings->pagination_bg_color_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_color_hover ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->pagination_color_hover ) && ! empty( $settings->pagination_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->pagination_color_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-grid-load-more {
	margin-top: <?php echo $settings->pagination_spacing_v; ?>px;
	<?php if ( isset( $settings->pagination_align ) ) { ?>
		text-align: <?php echo $settings->pagination_align; ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-grid-load-more a {
	<?php if ( isset( $settings->pagination_bg_color ) && ! empty( $settings->pagination_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->pagination_color ) && ! empty( $settings->pagination_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->pagination_color ); ?>;
	<?php } ?>
	text-align: center;
	text-decoration: none;
	transition: all 0.2s ease-in-out;
}

.fl-node-<?php echo $id; ?> .pp-content-grid-load-more a:hover {
	<?php if ( isset( $settings->pagination_bg_color_hover ) && ! empty( $settings->pagination_bg_color_hover ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->pagination_bg_color_hover ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->pagination_color_hover ) && ! empty( $settings->pagination_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->pagination_color_hover ); ?>;
	<?php } ?>
}

<?php /* Post Title */ ?>
.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-title {
	<?php if ( isset( $settings->show_title ) && 'no' == $settings->show_title ) { ?>
		display: none;
	<?php } ?>
	<?php if( $settings->title_margin['top'] >= 0 ) { ?>
		margin-top: <?php echo $settings->title_margin['top']; ?>px;
	<?php } ?>
	<?php if( $settings->title_margin['bottom'] >= 0 ) { ?>
		margin-bottom: <?php echo $settings->title_margin['bottom']; ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-title,
.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-title a {
	<?php if( $settings->title_font_color ) { ?>
		color: <?php echo pp_get_color_value( $settings->title_font_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post:hover .pp-post-title,
.fl-node-<?php echo $id; ?> .pp-content-post:hover .pp-post-title a {
	<?php if( isset( $settings->title_font_hover_color ) && ! empty( $settings->title_font_hover_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->title_font_hover_color ); ?>;
	<?php } ?>
}
<?php
// Post Title typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'title_typography',
	'selector'		=> ".fl-node-$id .pp-content-post .pp-post-title, .fl-node-$id .pp-content-post .pp-post-title a"
) );
?>

<?php if ( isset( $settings->post_type ) && 'tribe_events' == $settings->post_type && 'style-9' == $settings->post_grid_style_select ) { ?>
	<?php if ( ( isset( $settings->event_date ) && 'yes' == $settings->event_date )
		|| ( isset( $settings->event_venue ) && 'yes' == $settings->event_venue ) 
		|| ( isset( $settings->event_cost ) && 'yes' == $settings->event_cost ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-title {
			margin-bottom: auto;
		}
	<?php } ?>
	.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-event-calendar-meta {
		<?php if( $settings->title_margin['bottom'] >= 0 ) { ?>
			margin-bottom: <?php echo $settings->title_margin['bottom']; ?>px;
		<?php } ?>
	}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-content {
	<?php if( $settings->content_font_color ) { ?>
		color: <?php echo pp_get_color_value( $settings->content_font_color ); ?>;
	<?php } ?>
	<?php if( $settings->description_margin['top'] >= 0 ) { ?>
		margin-top: <?php echo $settings->description_margin['top']; ?>px;
	<?php } ?>
	<?php if( $settings->description_margin['bottom'] >= 0 ) { ?>
		margin-bottom: <?php echo $settings->description_margin['bottom']; ?>px;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post:hover .pp-post-content {
	<?php if ( isset( $settings->content_font_hover_color ) && ! empty( $settings->content_font_hover_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->content_font_hover_color ); ?>;
	<?php } ?>
}
<?php
// Content typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'content_typography',
	'selector'		=> ".fl-node-$id .pp-content-post .pp-post-content"
) );
?>

<?php /* The Events Calendar */ ?>
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-date,
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-date span {
	<?php if ( isset( $settings->event_date_color ) && ! empty( $settings->event_date_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->event_date_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->event_date_case ) && 'default' != $settings->event_date_case ) { ?>
		text-transform: <?php echo $settings->event_date_case; ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-venue,
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-venue span.tribe-address {
	<?php if ( isset( $settings->event_venue_color ) && ! empty( $settings->event_venue_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->event_venue_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-cost,
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-cost span.ticket-cost {
	<?php if ( isset( $settings->event_cost_color ) && ! empty( $settings->event_cost_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->event_cost_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-cost form {
	margin-top: 10px;
}

.fl-node-<?php echo $id; ?> .pp-content-post .pp-more-link-button,
.fl-node-<?php echo $id; ?> .pp-content-post .pp-more-link-button:visited,
.fl-node-<?php echo $id; ?> .pp-content-post .pp-add-to-cart a,
.fl-node-<?php echo $id; ?> .pp-content-post .pp-add-to-cart a:visited,
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-cost form .tribe-button,
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-cost form .tribe-button:visited {
	<?php if((isset( $settings->more_link_type ) && $settings->more_link_type == 'button') || (isset($settings->product_button) && $settings->product_button == 'yes') ) { ?>
		
		<?php if ( isset( $settings->button_bg_color ) && ! empty( $settings->button_bg_color ) ) { ?>
			background: <?php echo pp_get_color_value( $settings->button_bg_color ); ?>;
		<?php } ?>

		<?php if( $settings->button_width == 'full' ) { ?>
	 	   width: 100%;
	    <?php } ?>
	<?php } ?>

	<?php if ( isset( $settings->button_text_color ) && ! empty( $settings->button_text_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->button_text_color ); ?>;
	<?php } ?>

   cursor: pointer;
}
<?php
// Button border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'button_border_group',
	'selector' 		=> ".fl-node-$id .pp-content-post .pp-more-link-button, .fl-node-$id .pp-content-post .pp-add-to-cart a, .fl-node-$id .pp-post-event-calendar-cost form .tribe-button",
) );

// Button padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'button_padding',
	'selector' 		=> ".fl-node-$id .pp-content-post .pp-more-link-button, .fl-node-$id .pp-content-post .pp-add-to-cart a, .fl-node-$id .pp-post-event-calendar-cost form .tribe-button",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'button_padding_top',
		'padding-right' 	=> 'button_padding_right',
		'padding-bottom' 	=> 'button_padding_bottom',
		'padding-left' 		=> 'button_padding_left',
	),
) );

// Button typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'button_typography',
	'selector'		=> ".fl-node-$id .pp-content-post .pp-more-link-button, .fl-node-$id .pp-content-post .pp-add-to-cart a, .fl-node-$id .pp-post-event-calendar-cost form .tribe-button",
) );
?>

<?php if ( $settings->match_height == 'yes' ) { ?>
.fl-node-<?php echo $id; ?> .pp-content-post-data.pp-content-relative {
	position: relative;
}

.fl-node-<?php echo $id; ?> .pp-content-post-data.pp-content-relative .pp-more-link-button {
	position: absolute;
	bottom: 0;
	<?php if( $settings->post_content_alignment == 'center' ) { ?>
	left: 50%;
    transform: translateX(-50%);
	<?php } else if( $settings->post_content_alignment == 'left' ) { ?>
	left: 0;
	<?php } else { ?>
	right: 0;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-grid-style-5 .pp-content-post-data.pp-content-relative .pp-more-link-button {
	left: 0;
	transform: none;
}

.fl-node-<?php echo $id; ?> .pp-grid-style-6 .pp-content-post-data.pp-content-relative .pp-more-link-button {
	left: 50%;
    transform: translateX(-50%);
}
<?php } ?>


.fl-node-<?php echo $id; ?> .pp-content-post .pp-content-grid-more:hover,
.fl-node-<?php echo $id; ?> .pp-content-post .pp-add-to-cart a:hover,
.fl-node-<?php echo $id; ?> .pp-post-event-calendar-cost form .tribe-button:hover {
	<?php if ( isset( $settings->button_bg_hover_color ) && ! empty( $settings->button_bg_hover_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->button_bg_hover_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->button_text_color ) && ! empty( $settings->button_text_hover_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->button_text_hover_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->button_border_hover_color ) && ! empty( $settings->button_border_hover_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->button_border_hover_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-title-divider {
	background-color: <?php echo pp_get_color_value( $settings->post_title_divider_color ); ?>;
}

<?php if ( $settings->post_grid_style_select == 'style-8' && $settings->show_image == 'yes' ) { ?>
.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-image {
    float: left;
	<?php if ( isset( $settings->post_content_width ) && '' !== $settings->post_content_width ) { ?>
    width: calc( 100% - <?php echo $settings->post_content_width; ?>% );
	<?php } else { ?>
	width: 40%;
	<?php } ?>
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-image .pp-content-category-list {
	background-color: <?php echo pp_get_color_value( $settings->post_category_bg_color ); ?>;
	color: <?php echo pp_get_color_value( $settings->post_category_text_color ); ?>;
	<?php if($settings->post_category_position == 'right') { ?>
	right: 0;
	left: auto;
	<?php } else { ?>
	right: auto;
	left: 0;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-image .pp-content-category-list a {
	color: <?php echo pp_get_color_value( $settings->post_category_text_color ); ?>;
}

.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-5 .pp-content-post-date span.pp-post-day {
	background-color: <?php echo pp_get_color_value( $settings->post_date_day_bg_color ); ?>;
	color: <?php echo pp_get_color_value( $settings->post_date_day_text_color ); ?>;
	border-top-left-radius: <?php echo $settings->post_date_border_radius; ?>px;
	border-top-right-radius: <?php echo $settings->post_date_border_radius; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-5 .pp-content-post-date span.pp-post-month {
	background-color: <?php echo pp_get_color_value( $settings->post_date_month_bg_color ); ?>;
	color: <?php echo pp_get_color_value( $settings->post_date_month_text_color ); ?>;
	border-bottom-left-radius: <?php echo $settings->post_date_border_radius; ?>px;
	border-bottom-right-radius: <?php echo $settings->post_date_border_radius; ?>px;
}

.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-6 .pp-post-image .pp-content-post-date {
	background-color: <?php echo pp_get_color_value( $settings->post_date_bg_color ); ?>;
	color: <?php echo pp_get_color_value( $settings->post_date_text_color ); ?>;
}

.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-image .pp-post-title {
	background: <?php echo ($settings->post_title_overlay_color) ? pp_hex2rgba(pp_get_color_value( $settings->post_title_overlay_color ), ($settings->post_title_overlay_opacity/ 100)) : 'transparent'; ?>;
	text-align: <?php echo $settings->post_content_alignment; ?>;
}


.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-meta {
	<?php if ( ! empty( $settings->post_meta_font_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->post_meta_font_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post:hover .pp-post-meta {
	<?php if ( isset( $settings->post_meta_font_hover_color ) && ! empty( $settings->post_meta_font_hover_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->post_meta_font_hover_color ); ?>;
	<?php } ?>
}
<?php
// Content typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'meta_typography',
	'selector'		=> ".fl-node-$id .pp-content-post .pp-post-meta"
) );
?>
.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-meta span {
	<?php if ( 'style-9' == $settings->post_grid_style_select && isset( $settings->post_meta_bg_color ) && ! empty( $settings->post_meta_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->post_meta_bg_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-meta .pp-post-meta-term {
	<?php if ( ! empty( $settings->post_meta_font_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->post_meta_font_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post:hover .pp-post-meta .pp-post-meta-term {
	<?php if ( isset( $settings->post_meta_font_hover_color ) && ! empty( $settings->post_meta_font_hover_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->post_meta_font_hover_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-grid-post .pp-content-category-list,
.fl-node-<?php echo $id; ?> .pp-content-carousel-post .pp-content-category-list {
	<?php if ( isset( $settings->post_bg_color ) && ! empty( $settings->post_bg_color ) ) { ?>
	border-top-color: <?php echo pp_get_color_value( FLBuilderColor::adjust_brightness( $settings->post_bg_color, 12, 'darken' ) ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-7 .pp-content-post-meta {
	<?php if ( isset( $settings->post_bg_color ) && ! empty( $settings->post_bg_color ) ) { ?>
	border-bottom-color: <?php echo pp_get_color_value( FLBuilderColor::adjust_brightness( $settings->post_bg_color, 12, 'darken' ) ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-7:hover .pp-content-post-meta {
	<?php if ( isset( $settings->post_bg_color_hover ) && ! empty( $settings->post_bg_color_hover ) ) { ?>
	border-bottom-color: <?php echo pp_get_color_value( FLBuilderColor::adjust_brightness( $settings->post_bg_color_hover, 12, 'darken' ) ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-theme .owl-dots .owl-dot span {
	opacity: 1;
    <?php if( $settings->post_slider_dot_bg_color ) { ?>
    background: <?php echo pp_get_color_value( $settings->post_slider_dot_bg_color ); ?>;
    <?php } ?>
    <?php if( $settings->post_slider_dot_width >= 0 ) { ?>
    width: <?php echo $settings->post_slider_dot_width; ?>px;
    <?php } ?>
    <?php if( $settings->post_slider_dot_width >= 0 ) { ?>
    height: <?php echo $settings->post_slider_dot_width; ?>px;
    <?php } ?>
    <?php if( $settings->post_slider_dot_border_radius >= 0 ) { ?>
    border-radius: <?php echo $settings->post_slider_dot_border_radius; ?>px;
    <?php } ?>
    box-shadow: none;
}

.fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-theme .owl-dots .owl-dot.active span,
.fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-theme .owl-dots .owl-dot:hover span,
.fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-theme .owl-dots .owl-dot:focus span {
    <?php if( $settings->post_slider_dot_bg_hover ) { ?>
	background: <?php echo pp_get_color_value( $settings->post_slider_dot_bg_hover ); ?>;
    <?php } ?>
	opacity: 1;
    box-shadow: none;
}

<?php
FLBuilderCSS::responsive_rule( array(
	'settings'	   => $settings,
	'setting_name' => 'arrow_spacing',
	'selector'     => ".fl-node-$id .pp-content-post-carousel .owl-nav button.owl-prev",
	'unit'         => $settings->arrow_spacing_unit,
	'prop'         => 'left',
	'enabled'      => '' !== $settings->arrow_spacing,
) );

FLBuilderCSS::responsive_rule( array(
	'settings'	   => $settings,
	'setting_name' => 'arrow_spacing',
	'selector'     => ".fl-node-$id .pp-content-post-carousel .owl-nav button.owl-next",
	'unit'         => $settings->arrow_spacing_unit,
	'prop'         => 'right',
	'enabled'      => '' !== $settings->arrow_spacing,
) );
?>

.fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button {
	<?php if ( '' !== $settings->post_slider_arrow_font_size ) { ?>
	width: <?php echo ( floatval( $settings->post_slider_arrow_font_size ) + 10 ); ?>px;
	height: <?php echo ( floatval( $settings->post_slider_arrow_font_size ) + 10 ); ?>px;
	<?php } ?>
}
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button[class*=owl],
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button[class*=owl]:visited,
.fl-page .fl-builder-content .fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button[class*=owl],
.fl-page .fl-builder-content .fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button[class*=owl]:visited {
	<?php if ( isset( $settings->arrow_bg_color ) && ! empty( $settings->arrow_bg_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->arrow_bg_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button svg {
	height: <?php echo $settings->post_slider_arrow_font_size; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button svg path {
	<?php if ( isset( $settings->arrow_color ) && ! empty( $settings->arrow_color ) ) { ?>
		fill: <?php echo pp_get_color_value( $settings->arrow_color ); ?>;
	<?php } ?>
}
<?php
// Arrow border
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'arrow_border',
	'selector' 		=> ".fl-node-$id .pp-content-post-carousel .owl-nav button",
) );

// Arrow padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'arrow_padding',
	'selector' 		=> ".fl-node-$id .pp-content-post-carousel .owl-nav button",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'arrow_padding_top',
		'padding-right' 	=> 'arrow_padding_right',
		'padding-bottom' 	=> 'arrow_padding_bottom',
		'padding-left' 		=> 'arrow_padding_left',
	),
) );
?>

.fl-builder-content .fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button[class*=owl]:hover,
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button[class*=owl]:focus,
.fl-page .fl-builder-content .fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button[class*=owl]:hover,
.fl-page .fl-builder-content .fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button[class*=owl]:focus {
	<?php if ( isset( $settings->arrow_bg_hover_color ) && ! empty( $settings->arrow_bg_hover_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->arrow_bg_hover_color ); ?>;
	<?php } ?>
	<?php if ( isset( $settings->arrow_border_hover_color ) && ! empty( $settings->arrow_border_hover_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->arrow_border_hover_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post-carousel .owl-nav button:hover svg path {
	<?php if ( isset( $settings->arrow_hover_color ) && ! empty( $settings->arrow_hover_color ) ) { ?>
		fill: <?php echo pp_get_color_value( $settings->arrow_hover_color ); ?>;
	<?php } ?>
}

<?php
/* Grid & Carousel Setting Layout */
if($settings->layout == 'grid' || $settings->layout == 'carousel') { // GRID ?>

<?php
// Padding.
FLBuilderCSS::dimension_field_rule( array(
	'settings'	=> $settings,
	'setting_name'	=> 'post_grid_padding',
	'selector' 		=> ".fl-node-$id .pp-content-post",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'post_grid_padding_top',
		'padding-right' 	=> 'post_grid_padding_right',
		'padding-bottom' 	=> 'post_grid_padding_bottom',
		'padding-left' 		=> 'post_grid_padding_left',
	),
) );
?>

.fl-node-<?php echo $id; ?> .pp-content-post {
    opacity: 1;
	text-align: <?php echo 'style-6' != $settings->post_grid_style_select ? $settings->post_content_alignment : 'center'; ?>;
}

.fl-node-<?php echo $id; ?> .pp-content-post:hover {
	<?php if ( isset( $settings->post_bg_color_hover ) && ! empty( $settings->post_bg_color_hover ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->post_bg_color_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-7 .pp-content-body {
	<?php if ( isset( $settings->post_bg_color ) && ! empty( $settings->post_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->post_bg_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-7:hover .pp-content-body {
	<?php if ( isset( $settings->post_bg_color_hover ) && ! empty( $settings->post_bg_color_hover ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->post_bg_color_hover ); ?>;
	<?php } ?>
}

.woocommerce .fl-node-<?php echo $id; ?> .pp-content-post {
	<?php if( 'grid' == $settings->layout ) { ?>
	margin-bottom: <?php echo $spacing . $spacing_unit; ?>;
	<?php } ?>
}

<?php if ( $use_css_grid ) { ?>
.fl-node-<?php echo $id; ?> .pp-content-post-grid {
	display: grid;
	grid-template-columns: repeat(var(--column-xl, <?php echo $column_desktop; ?>), 1fr);
	grid-gap: <?php echo '%' == $spacing_unit && '' != $spacing ? ( $spacing * 10 ) : $spacing; ?>px;
}
.fl-node-<?php echo $id; ?>.cg-grid-center-align .pp-content-post-grid {
	grid-template-columns: repeat(min(var(--items-count),var(--column-xl)), min(calc(100%/var(--items-count)),calc(100%/var(--column-xl))));
	justify-content: center;
}
.fl-node-<?php echo $id; ?> .pp-content-post {
	width: 100%;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-content-post {
	position: relative;
	<?php if ( 'grid' == $settings->layout && ! $use_css_grid ) { ?>
	float: left;
	margin-bottom: <?php echo $spacing . $spacing_unit; ?>;
	width: calc((100% - <?php echo $space_desktop + 0.1; ?><?php echo $spacing_unit; ?>) / <?php echo $column_desktop; ?>);/*<?php echo $post_columns_desktop - 0.1; ?>%;*/
	<?php } ?>
	<?php if ( 'carousel' == $settings->layout ) { ?>
	margin-left: <?php echo ($spacing / 2); ?><?php echo $spacing_unit; ?>;
	margin-right: <?php echo ($spacing / 2); ?><?php echo $spacing_unit; ?>;
	<?php } ?>
	<?php if ( isset( $settings->post_bg_color ) && ! empty( $settings->post_bg_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->post_bg_color ); ?>;
	<?php } ?>
}
.fl-node-<?php echo $id; ?>.cg-static-grid .pp-content-post.pp-content-grid-post {
	margin-right: <?php echo $spacing . $spacing_unit; ?>;
}

@media only screen and (min-width: 768px) {
	.fl-node-<?php echo $id; ?>.cg-css-grid .pp-content-post-grid.pp-equal-height {
		grid-column-gap: <?php echo $spacing . $spacing_unit; ?>;
		grid-row-gap: <?php echo $spacing; ?>ch;
	}
}

<?php
// Border.
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'post_border_group',
	'selector' 		=> ".fl-node-$id .pp-content-post",
) );
?>

.fl-node-<?php echo $id; ?> .pp-grid-space {
	width: <?php echo $spacing . $spacing_unit; ?>;
}

.fl-node-<?php echo $id; ?> .pp-content-post .pp-content-grid-more-link,
.fl-node-<?php echo $id; ?> .pp-content-post .pp-add-to-cart {
	margin-top: <?php echo $settings->button_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->button_margin['bottom']; ?>px;
    position: relative;
    z-index: 2;
}

<?php if ( $settings->match_height == 'yes' ) { ?>
.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_desktop; ?>n) {
    margin-right: 0;
}
.fl-node-<?php echo $id; ?> .pp-content-post-grid.pp-filters-active .pp-content-grid-post {
	margin-right: 0;
}
	<?php if ( 'load_more' == $settings->pagination || 'scroll' == $settings->pagination ) { ?>
		.fl-node-<?php echo $id; ?> .pp-content-post-grid .pp-content-grid-post {
			margin-right: 0;
		}
	<?php } ?>
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-content-post .pp-content-body {
    <?php if ( $settings->post_grid_style_select == 'style-8' ) { ?>
        float: left;
        <?php if ( $settings->show_image !== 'yes' ) { ?>
			width: 100%;
		<?php } ?>
    <?php } ?>
}
<?php
FLBuilderCSS::responsive_rule( array(
	'settings'	=> $settings,
	'setting_name'	=> 'post_content_width',
	'selector' => ".fl-node-$id .pp-content-post .pp-content-body",
	'unit' => '%',
	'prop' => 'width',
	'enabled' => ( $settings->post_grid_style_select == 'style-8' && $settings->show_image == 'yes' ),
) );

FLBuilderCSS::dimension_field_rule( array(
	'settings'	=> $settings,
	'setting_name'	=> 'post_content_padding',
	'selector' 		=> ".fl-node-$id .pp-content-post .pp-content-body",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'post_content_padding_top',
		'padding-right' 	=> 'post_content_padding_right',
		'padding-bottom' 	=> 'post_content_padding_bottom',
		'padding-left' 		=> 'post_content_padding_left',
	),
) );
?>

/* Woocommerce Style */

.fl-node-<?php echo $id; ?> .pp-content-post .star-rating {
    <?php if( $settings->post_content_alignment == 'left' ) { ?>
        margin-left: 0;
    <?php } else if( $settings->post_content_alignment == 'right' ) { ?>
        margin-right: 0;
    <?php } else { ?>
        margin: 0 auto;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-5 .star-rating {
    margin-left: 0;
}

.fl-node-<?php echo $id; ?> .pp-content-post .star-rating:before,
.fl-node-<?php echo $id; ?> .pp-content-post .star-rating span:before {
    color: <?php echo pp_get_color_value( isset( $settings->product_rating_color ) ? $settings->product_rating_color : '' ); ?>;
}

.fl-node-<?php echo $id; ?> .pp-content-post .pp-product-price,
.fl-node-<?php echo $id; ?> .pp-content-post .pp-product-price span.price {
    color: <?php echo pp_get_color_value( isset( $settings->product_price_color ) ? $settings->product_price_color : '' ); ?>;
    <?php if ( isset( $settings->meta_typography ) && isset( $settings->meta_typography['font_size'] ) ) { ?>
	font-size: <?php echo $settings->meta_typography['font_size']['length']; ?><?php echo $settings->meta_typography['font_size']['unit']; ?>;
	<?php } ?>
}

<?php } ?>

<?php
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'custom_height',
	'selector'     => ".fl-node-$id .pp-content-post.pp-grid-style-9",
	'prop'         => 'height',
	'unit'         => 'px'
) );
?>

.fl-node-<?php echo $id; ?>.cg-square-layout .pp-content-post.pp-grid-style-9 {
	height: auto !important;
}

.fl-node-<?php echo $id; ?>.cg-square-layout .pp-content-post-grid.pp-filters-active .pp-content-grid-post .pp-post-image:after {
    content: "";
    display: block;
    padding-bottom: 100%;
}

.fl-node-<?php echo $id; ?>.cg-square-layout .pp-content-post.pp-grid-style-9 .pp-post-featured-img {
   width: 100%;
   height: 100%;
   position: absolute;
}

<?php if ('all' !== $responsive_filter) {?>
	.fl-node-<?php echo $id; ?> .pp-post-filters-sidebar .pp-content-posts {
		width: 100%;
	}
	.fl-node-<?php echo $id; ?> .pp-post-filters-sidebar.pp-posts-wrapper {
		display: flex;
		flex-direction: row;
	}
	.fl-node-<?php echo $id; ?> .pp-post-filters-sidebar-right.pp-posts-wrapper {
		flex-direction: row-reverse;
	}
	.fl-node-<?php echo $id; ?> .pp-post-filters-sidebar .pp-post-filters-wrapper {
		flex: 1 0 0;
	}
	.fl-node-<?php echo $id; ?> .pp-post-filters-sidebar .pp-post-filters li {
		display: block;
		<?php if ( isset( $settings->filter_margin_vertical ) && '' !== $settings->filter_margin_vertical ) { ?>
			margin-bottom: <?php echo $settings->filter_margin_vertical; ?>px;
		<?php } ?>
	}
	<?php if ( '' !== $settings->filter_margin ) { ?>
	.fl-node-<?php echo $id; ?> .pp-post-filters-sidebar-right .pp-post-filters li {
		margin-right: 0;
		margin-left: <?php echo $settings->filter_margin; ?>px;
	}
	<?php } ?>
<?php } ?>

<?php if ('no' != $responsive_filter) {?>
<?php if ('all' == $responsive_filter) {?>
<?php } elseif ('large' == $responsive_filter) {?>
	@media screen and (min-width: <?php echo intval($global_settings->medium_breakpoint) - 1; ?>px) {
<?php } elseif ('large_medium' == $responsive_filter) {?>
	@media screen and (min-width: <?php echo intval($global_settings->responsive_breakpoint) - 1; ?>px) {
<?php } elseif ('medium' == $responsive_filter) {?>
	@media screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) and (min-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
<?php } elseif ('medium_small' == $responsive_filter) {?>
	@media screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
<?php } elseif ('yes' == $responsive_filter) { // small devices. ?>
	@media screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
<?php }?>
		.fl-node-<?php echo $id; ?> .pp-post-filters-toggle {
			display: block;
		}
		.fl-node-<?php echo $id; ?> ul.pp-post-filters {
			display: none;
		}
		.fl-node-<?php echo $id; ?> ul.pp-post-filters li {
			display: block;
			float: none;
			margin: 0 !important;
			text-align: left;
		}
	<?php if ('all' != $responsive_filter) {?>
	}
	<?php }?>
<?php }?>

<?php if ( isset( $global_settings->large_breakpoint ) ) { ?>
	@media screen and (max-width: <?php echo $global_settings->large_breakpoint; ?>px) {
		<?php if ( $settings->post_grid_style_select == 'style-8' && $settings->show_image == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-image {
			<?php if ( isset( $settings->post_content_width_large ) && '' !== $settings->post_content_width_large ) { ?>
			width: calc( 100% - <?php echo $settings->post_content_width_large; ?>% );
			<?php } ?>
		}
		<?php } ?>

		<?php if ( $use_css_grid ) { ?>
		.fl-node-<?php echo $id; ?> .pp-content-post-grid {
			grid-template-columns: repeat(var(--column-lg, <?php echo $column_laptop; ?>), 1fr);
			grid-gap: <?php echo '%' == $spacing_unit_large && '' != $spacing_large ? ( $spacing_large * 10 ) : $spacing_large; ?>px;
		}
		.fl-node-<?php echo $id; ?>.cg-grid-center-align .pp-content-post-grid {
			grid-template-columns: repeat(min(var(--items-count),var(--column-lg)), min(calc(100%/var(--items-count)),calc(100%/var(--column-lg))));
		}
		<?php } ?>

		.fl-node-<?php echo $id; ?> .pp-content-post {
			<?php if ( 'grid' == $settings->layout && ! $use_css_grid ) { ?>
			margin-bottom: <?php echo $spacing_large . $spacing_unit; ?>;
			width: <?php echo $post_columns_large; ?>%;
			width: calc((100% - <?php echo $space_large; ?><?php echo $spacing_unit; ?>) / <?php echo $column_laptop; ?>);
			<?php } ?>
			<?php if ( 'carousel' == $settings->layout ) { ?>
			margin-left: <?php echo ($spacing_large / 2); ?><?php echo $spacing_unit; ?>;
			margin-right: <?php echo ($spacing_large / 2); ?><?php echo $spacing_unit; ?>;
			<?php } ?>
		}

		.fl-node-<?php echo $id; ?> .pp-grid-space {
			width: <?php echo $spacing_large . $spacing_unit; ?>;
		}
	}
<?php } ?>

@media screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
	<?php if ( $settings->post_grid_style_select == 'style-8' && $settings->show_image == 'yes' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-image {
		<?php if ( isset( $settings->post_content_width_medium ) && '' !== $settings->post_content_width_medium ) { ?>
		width: calc( 100% - <?php echo $settings->post_content_width_medium; ?>% );
		<?php } ?>
	}
	<?php } ?>

	<?php if ( $use_css_grid ) { ?>
		.fl-node-<?php echo $id; ?> .pp-content-post-grid {
			grid-template-columns: repeat(var(--column-md, <?php echo $column_tablet; ?>), 1fr);
			grid-gap: <?php echo '%' == $spacing_unit_tablet && '' != $spacing_tablet ? ( $spacing_tablet * 10 ) : $spacing_tablet; ?>px;
		}
		.fl-node-<?php echo $id; ?>.cg-grid-center-align .pp-content-post-grid {
			grid-template-columns: repeat(min(var(--items-count),var(--column-md)), min(calc(100%/var(--items-count)),calc(100%/var(--column-md))));
		}
	<?php } ?>

	.fl-node-<?php echo $id; ?> .pp-content-post {
		<?php if ( 'grid' == $settings->layout && ! $use_css_grid ) { ?>
		margin-bottom: <?php echo $spacing_tablet . $spacing_unit; ?>;
		width: <?php echo $post_columns_tablet; ?>%;
		width: calc((100% - <?php echo $space_tablet; ?><?php echo $spacing_unit; ?>) / <?php echo $column_tablet; ?>);
		<?php } ?>
		<?php if ( 'carousel' == $settings->layout ) { ?>
		margin-left: <?php echo ($spacing_tablet / 2); ?><?php echo $spacing_unit; ?>;
		margin-right: <?php echo ($spacing_tablet / 2); ?><?php echo $spacing_unit; ?>;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-grid-space {
		width: <?php echo $spacing_tablet . $spacing_unit; ?>;
	}

	.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_desktop; ?>n+1) {
	    clear: none;
	}

	.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_tablet; ?>n+1) {
	    clear: left;
	}

	/*
	.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_desktop; ?>n) {
	    margin-right: <?php echo $spacing . $spacing_unit; ?>;
	}
	*/

	.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_tablet; ?>n) {
	    margin-right: 0;
	}
}

@media screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	<?php if ( $settings->post_grid_style_select == 'style-8' && $settings->show_image == 'yes' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-content-post .pp-post-image {
		<?php if ( isset( $settings->post_content_width_responsive ) && '' !== $settings->post_content_width_responsive ) { ?>
		width: calc( 100% - <?php echo $settings->post_content_width_responsive; ?>% );
		<?php } ?>
	}
	<?php } ?>

	<?php if ( $use_css_grid ) { ?>
		.fl-node-<?php echo $id; ?> .pp-content-post-grid {
			grid-template-columns: repeat(var(--column-sm, <?php echo $column_mobile; ?>), 1fr);
			grid-gap: <?php echo '%' == $spacing_unit_mobile && '' != $spacing_mobile ? ( $spacing_mobile * 10 ) : $spacing_mobile; ?>px;
		}
		.fl-node-<?php echo $id; ?>.cg-grid-center-align .pp-content-post-grid {
			grid-template-columns: repeat(min(var(--items-count),var(--column-sm)), min(calc(100%/var(--items-count)),calc(100%/var(--column-sm))));
		}
	<?php } ?>

	.fl-node-<?php echo $id; ?> .pp-content-post {
		<?php if ( 'grid' == $settings->layout && ! $use_css_grid ) { ?>
		margin-bottom: <?php echo $spacing_mobile . $spacing_unit; ?>;
		width: <?php echo $post_columns_mobile; ?>%;
		width: calc((100% - <?php echo $space_mobile; ?><?php echo $spacing_unit; ?>) / <?php echo $column_mobile; ?>);
		<?php } ?>
		<?php if ( 'carousel' == $settings->layout ) { ?>
		margin-left: <?php echo ($spacing_mobile / 2); ?><?php echo $spacing_unit; ?>;
		margin-right: <?php echo ($spacing_mobile / 2); ?><?php echo $spacing_unit; ?>;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-grid-space {
		width: <?php echo $spacing_mobile . $spacing_unit; ?>;
	}

	.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_tablet; ?>n+1) {
	    clear: none;
	}

	.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_mobile; ?>n+1) {
	    clear: left;
	}

	/*
	.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_tablet; ?>n) {
	    margin-right: <?php echo $spacing . $spacing_unit; ?>;
	}
	*/

	.fl-node-<?php echo $id; ?> .pp-content-grid-post:nth-of-type(<?php echo $column_mobile; ?>n) {
	    margin-right: 0;
	}

	.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-8 .pp-post-image,
	.fl-node-<?php echo $id; ?> .pp-content-post.pp-grid-style-8 .pp-content-body {
		float: none;
		width: 100%;
	}
}