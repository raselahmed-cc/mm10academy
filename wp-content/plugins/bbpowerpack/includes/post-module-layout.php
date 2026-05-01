<?php
$class = 'carousel' == $settings->layout ? 'pp-content-carousel-post' : 'pp-content-grid-post';
?>

<div <?php post_class('pp-content-post ' . $class . ' pp-grid-' . $settings->post_grid_style_select); ?><?php BB_PowerPack_Post_Helper::print_schema( ' itemscope itemtype="' . BB_PowerPack_Post_Helper::schema_itemtype() . '"' ); ?> data-id="<?php echo get_the_ID(); ?>">

	<?php
	
	BB_PowerPack_Post_Helper::schema_meta();

	$custom_layout = (object) $settings->custom_layout;
	$custom_layout_html = is_object( $custom_layout->html ) && isset( $custom_layout->html->html ) ? $custom_layout->html->html : $custom_layout->html;
	$custom_layout_html = apply_filters( 'pp_post_custom_layout_html', stripslashes( $custom_layout_html ) );

	do_action( 'pp_post_custom_layout_before_content', $settings );
	echo do_shortcode( FLThemeBuilderFieldConnections::parse_shortcodes( $custom_layout_html ) );
	do_action( 'pp_post_custom_layout_after_content', $settings );
	
	?>

</div>