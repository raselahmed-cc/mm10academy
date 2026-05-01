<?php
FLBuilderModel::default_settings( $settings, array(
	'data_source'		=> is_post_type_archive() ? 'main_query' : 'custom_query',
	'post_type' 		=> 'post',
	'post_status' 		=> 'publish',
	'order_by'  		=> 'date',
	'order'     		=> 'DESC',
	'offset'    		=> 0,
	'users'     		=> '',
	'show_image' 		=> 'yes',
	'show_author'		=> 'yes',
	'show_date'			=> 'yes',
	'show_categories'	=> 'no',
	'meta_separator'	=> ' | ',
	'show_content'		=> 'yes',
	'content_type'		=> 'excerpt',
	'content_length'	=> 300,
	'more_link_type'	=> 'box',
	'more_link_text'	=> __('Read More', 'bb-powerpack'),
	'post_grid_filters_display' => 'no',
	'post_grid_filters'	=> 'none',
	'post_grid_filters_order_by' => 'name',
	'post_grid_filters_order'    => 'ASC',
	'post_grid_filters_type'	=> 'dynamic',
	'all_filter_label'	=> __('All', 'bb-powerpack'),
	'post_taxonomies'	=> 'none',
	'product_rating'	=> 'yes',
	'product_price'		=> 'yes',
	'product_button'	=> 'yes',
	'product_button_text'	=> __('Add to Cart', 'bb-powerpack'),
	'fallback_image'	=> 'default',
	'image_thumb_size'	=> 'large'
) );

$columns = $module->get_post_columns( $settings );

$style_scripts = '';
$css_class = '';

if ('no' == $settings->match_height) {
	if ( 'carousel' !== $settings->layout ) {
    	$css_class .= ' pp-masonry-active';
	}
} else {
    $css_class .= ' pp-equal-height';
}
if ('grid' == $settings->layout && 'yes' == $settings->post_grid_filters_display && !empty($settings->post_grid_filters)) {
    $css_class .= ' pp-filters-active';
}

if ( 'carousel' === $settings->layout && 'yes' === $settings->auto_height ) {
	$css_class .= ' pp-auto-height';
}

if ( in_array( $settings->post_grid_style_select, array( 'default', 'style-2', 'style-3', 'style-5', 'style-8' ) ) ) {
    if ( isset( $settings->alternate_content ) && 'yes' === $settings->alternate_content ) {
        $css_class .= ' pp-content-alternate';
    }
}

if ( ! $module->use_css_grid( $settings ) ) {
	$css_class .= ' clearfix';
} else {
	$css_class .= ' pp-css-grid';
}

$wrapper_class = 'pp-posts-wrapper';

if ( 'grid' === $settings->layout && isset( $settings->filter_position ) && 'top' !== $settings->filter_position ) {
	$wrapper_class .= ' pp-post-filters-sidebar pp-post-filters-sidebar-' . $settings->filter_position;
}

$link_target = isset( $settings->link_target_new ) && 'yes' === $settings->link_target_new ? ' target="_blank" rel="noopener bookmark"' : '';

// Set custom parameteres in module settings to verify
// our module when using filter hooks.
if ( ! isset( $settings->pp_content_grid ) ) {
	$settings->pp_content_grid = true;
}
if ( ! isset( $settings->pp_content_grid_id ) ) {
	$settings->pp_content_grid_id = $id;
}
if ( ! isset( $settings->pp_post_id ) ) {
	$settings->pp_post_id = get_the_ID();
}

if ( ! isset( $settings->offset ) || empty( $settings->offset ) ) {
	$settings->offset = 0;
}

if ( 'acf_relationship' == $settings->data_source ) {
	$settings->post_type = 'any';
}

/**
 * Added fl_builder_loop_query_args filter to get the filtered posts
 * only for the current module when using dyanmic (AJAX) filters
 * and infinite scroll.
 * 
 * We have passed the taxonomy term and node id as parameters in
 * pagination URLs.
 * 
 * This is the only way to get the posts of a taxonomy from the next
 * page.
 */
add_filter( 'fl_builder_loop_query_args', function( $args ) {
	if ( ! isset( $_GET['filter_term'] ) ) {
		return $args;
	}

	if ( ! isset( $_GET['grid'] ) ) {
		if ( ! isset( $_GET['node_id'] ) ) {
			return $args;
		}
	}

	if ( ! empty( $_GET['filter_term'] ) && isset( $args['settings']->pp_content_grid_id ) ) {
		$node_id = $args['settings']->pp_content_grid_id;
		$grid_id = $args['settings']->id;

		if ( ( isset( $_GET['grid'] ) && $_GET['grid'] == $grid_id ) || ( isset( $_GET['node_id'] ) && $_GET['node_id'] == $node_id ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => $args['settings']->post_grid_filters,
				'field'    => 'slug',
				'terms'    => esc_attr( wp_unslash( $_GET['filter_term'] ) ),
			);
		}
	}

	return $args;
} );

// Default filter.
add_filter( 'fl_builder_loop_query_args', function( $args ) {
	if ( ! isset( $args['settings']->pp_content_grid ) ) {
		return $args;
	}

	if ( 'carousel' !== $args['settings']->layout && 'yes' === $args['settings']->post_grid_filters_display ) {
		if ( isset( $args['settings']->post_grid_filters_default ) && ! empty( $args['settings']->post_grid_filters_default ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => $args['settings']->post_grid_filters,
				'field'    => 'slug',
				'terms'    => $args['settings']->post_grid_filters_default
			);
		}
	}

	return $args;
} );

do_action( 'pp_cg_before_wrap', $settings );

// Save the current post, so that it can be restored later (see the end of this file).
global $post;
$initial_current_post = $post;

// Get the query data.
$query = FLBuilderLoop::query( $settings );

?>
<div class="<?php echo $wrapper_class; ?>">
	<?php

	// Render the posts.
	if ( $query->have_posts() ) {

		do_action( 'pp_cg_before_posts', $settings, $query );

		$css_class .= ( FLBuilderLoop::get_paged() > 0 ) ? ' pp-paged-scroll-to' : '';
		$data_source = isset( $settings->data_source ) ? $settings->data_source : 'custom_query';
		$post_type   = isset( $settings->post_type ) ? $settings->post_type : 'post';
		$current_post_type = is_callable( 'get_post_type' ) ? get_post_type() : '';

		if ( 'main_query' === $settings->data_source && ! empty( $current_post_type ) ) {
			$post_type = $current_post_type;
		}

		if ( 'acf_relationship' != $settings->data_source ) {
			// Post filters.
			if ( $settings->layout == 'grid' && $settings->post_grid_filters_display == 'yes' && 'none' != $settings->post_grid_filters ) {
				$module->render_filters( $settings, $query );
			}
		}

	?>
	<?php
	$style_attrs = array(
		'--items-count' => $query->post_count,
		'--column-xl' => $columns['xl'],
		'--column-lg' => $columns['lg'],
		'--column-md' => $columns['md'],
		'--column-sm' => $columns['sm'],
	);
	$style_attr = '';
	foreach ( $style_attrs as $prop => $value ) {
		$style_attr .= "$prop: $value;";
	}
	
	$data_attrs = ' data-paged="' . FLBuilderLoop::get_paged() . '"';

	$data_attrs .= ' data-loop-count="' . esc_attr( FLBuilderLoop::$loop_counter ) . '"';

	if ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) {
		$data_attrs .= ' data-orderby="' . esc_attr( wp_unslash( $_GET['orderby'] ) ) . '"';
	}
	if ( isset( $module->template_id ) && ! empty( $module->template_id ) ) {
		$data_attrs .= ' data-template-id="' . $module->template_id . '"';
		$data_attrs .= ' data-template-node-id="' . $module->template_node_id . '"';
	}
	?>

	<div class="pp-content-posts" style="<?php echo $style_attr; ?>">
		<div class="pp-content-post-<?php echo esc_attr( $settings->layout ); ?><?php echo $css_class; ?>"<?php BB_PowerPack_Post_Helper::print_schema( ' itemscope="itemscope" itemtype="' . PPContentGridModule::schema_collection_type( $data_source, $post_type ) . '"' ); ?><?php echo $data_attrs; ?>>
			<?php if ( $settings->layout == 'carousel' ) { ?>
				<div class="pp-content-posts-inner owl-carousel owl-theme">
			<?php } ?>

				<?php

				$render = true;
				$count = 0;

				while( $query->have_posts() ) {

					$query->the_post();

					$post_id 	 = get_the_ID();
					$post_type   = get_post_type( get_post() );
					$permalink 	 = apply_filters( 'pp_cg_post_permalink', get_permalink(), $post_id, $settings );
					$title_attrs = the_title_attribute( array( 'echo' => false ) );

					$terms_list  = wp_get_post_terms( $post_id, $settings->post_taxonomies );
					$terms_list  = is_wp_error( $terms_list ) ? array() : $terms_list;

					$module->set_template_data( array(
						'settings'    => $settings,
						'post_id'     => $post_id,
						'permalink'   => $permalink,
						'post_type'   => $post_type,
						'title_attrs' => $title_attrs,
						'terms_list'  => $terms_list,
						'link_target' => $link_target
					) );
					
					// if ( in_array( 'product', (array) $post_type ) && function_exists( 'wc_get_product' ) ) {
					// 	$product = wc_get_product( $post_id );
					// 	if ( ! is_object( $product ) ) {
					// 		$render = false;
					// 	}
					// }

					if ( $render ) {
						$count++;

						ob_start();

						include apply_filters( 'pp_cg_module_layout_path', $module->dir . 'includes/post-' . $settings->layout . '.php', $settings->layout, $settings );

						// Do shortcodes here so they are parsed in context of the current post.
						$content = do_shortcode( ob_get_clean() );

						if ( preg_match_all( '/<link\b[^>]*>/is', $content, $link_tags ) ) {
							foreach ( $link_tags[0] as $value ) {
								$style_scripts .= $value;
							}
						}

						if ( preg_match_all( '/<style\b[^>]*>.*?<\/style>/is', $content, $styles ) ) {
							foreach ( $styles[0] as $value ) {
								$style_scripts .= $value;
							}

							$content = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $content);
						}

						if ( apply_filters( 'pp_cg_loop_filter_script_tags', true, $settings ) && preg_match_all( '/<script(?!(?:[^>]*type\s*=\s*["\']application\/(?:json|ld\+json)["\']))\b[^>]*>.*?<\/script>/is', $content, $scripts ) ) {
							foreach ( $scripts[0] as $value ) {
								$style_scripts .= $value;
							}

							$content = preg_replace('/<script(?!(?:[^>]*type\s*=\s*["\']application\/(?:json|ld\+json)["\']))\b[^>]*>.*?<\/script>/is', '', $content);
						}

						echo $content;
					}
				}

				?>

				<?php if ( $settings->layout == 'grid' && ! $module->use_css_grid( $settings ) ) { ?>
				<div class="pp-grid-space"></div>
				<?php } ?>

			<?php if ( $settings->layout == 'carousel' ) { ?>
				</div>
				<?php if ( 'yes' === $settings->slider_navigation && $query->have_posts() && $query->found_posts > 1 ) { ?>
				<div class="owl-nav pp-carousel-nav"></div>
				<?php } ?>
			<?php } ?>
		</div>

		<div class="fl-clear"></div>

		<?php } ?>

		<?php

		do_action( 'pp_cg_after_posts', $settings, $query );

		// Render the pagination.
		if( $settings->layout != 'carousel' && $settings->pagination != 'none' && $query->have_posts() && $query->max_num_pages > 1 ) {
		?>
			<div class="pp-content-grid-pagination fl-builder-pagination"<?php if($settings->pagination == 'scroll' || 'load_more' == $settings->pagination) echo ' style="display:none;"'; ?>>
				<?php
				if ( 'yes' == $settings->post_grid_filters_display && 'dynamic' == $settings->post_grid_filters_type ) {
					BB_PowerPack_Post_Helper::ajax_pagination( $query, $settings, '', FLBuilderLoop::get_paged() );
				} else {
					BB_PowerPack_Post_Helper::pagination( $query, $settings );
				}
				?>
			</div>

			<?php if ( 'load_more' == $settings->pagination ) { ?>
				<div class="pp-content-grid-load-more">
					<a href="#" class="pp-grid-load-more-button">
					<span class="pp-grid-loader-text"><?php echo $settings->load_more_text; ?></span>
					<span class="pp-grid-loader-icon"><img src="<?php echo BB_POWERPACK_URL . 'assets/images/spinner.gif'; ?>" alt="loader" /></span></a>
				</div>
			<?php } ?>
			<?php if ( 'scroll' == $settings->pagination ) { ?>
				<div class="pp-content-grid-loader" style="display: none;">
					<span class="pp-grid-loader-text"><?php _e('Loading...', 'bb-powerpack'); ?></span>
					<span class="pp-grid-loader-icon"><img src="<?php echo BB_POWERPACK_URL . 'assets/images/spinner.gif'; ?>" alt="loader" /></span>
				</div>
			<?php } ?>
		<?php } ?>
		<?php do_action( 'pp_cg_after_pagination', $settings, $query ); ?>

	<?php if ( $query->have_posts() ) {	?>
	</div><!-- .pp-content-posts -->
	<?php } ?>

	<?php

	// Render the empty message.
	if ( ! $query->have_posts() ) {

	?>
	<div class="pp-content-grid-empty">
		<p><?php echo $settings->no_results_message; ?></p>
		<?php if ( $settings->show_search == 'yes' ) { ?>
		<?php get_search_form(); ?>
		<?php } ?>
	</div>

	<?php
	}

	wp_reset_postdata();

	// Restore the original current post.
	//
	// Note that wp_reset_postdata() isn't enough because it resets the current post by using the main
	// query, but it doesn't take into account the possibility that it might have been overridden by a
	// third-party plugin in the meantime.
	//
	// Specifically, this used to cause problems with Toolset Views, when its Content Templates were used.
	$post = $initial_current_post;
	setup_postdata( $initial_current_post );

	if ( ! empty( $style_scripts ) ) {
		echo $style_scripts;
	}

	?>
</div>

<?php do_action( 'pp_cg_after_wrap', $settings ); ?>