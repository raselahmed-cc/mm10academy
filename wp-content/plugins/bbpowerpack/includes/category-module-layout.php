<?php $GLOBALS['pp_category'] = $cat; ?>

<div class="pp-category pp-category-<?php echo $cat->term_id; ?><?php echo 'yes' === $settings->category_grid_slider ? ' swiper-slide' : ''; ?> pp-clear layout-<?php echo $layout; ?>" title="<?php echo $cat->name; ?>">
	<div class="category-inner">
	<?php

	$custom_layout = (object) $settings->custom_layout;
	$custom_layout_html = is_object( $custom_layout->html ) && isset( $custom_layout->html->html ) ? $custom_layout->html->html : $custom_layout->html;
	$custom_layout_html = apply_filters( 'pp_category_custom_layout_html', stripslashes( $custom_layout_html ) );

	do_action( 'pp_category_custom_layout_before_content', $settings );
	echo do_shortcode( FLThemeBuilderFieldConnections::parse_shortcodes( $custom_layout_html ) );
	do_action( 'pp_category_custom_layout_after_content', $settings );
	
	?>
	</div>
</div>

<?php unset( $GLOBALS['pp_category'] ); ?>