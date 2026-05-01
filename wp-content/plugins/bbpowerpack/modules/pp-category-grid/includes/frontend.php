<?php
$layout = isset( $settings->layout ) ? esc_attr( $settings->layout ) : 'default';

if ( ! isset( $settings->post_type ) ) {
	$post_type = 'post';
} else {
	$post_type = $settings->post_type;
}

$var_tax_type = 'posts_' . $post_type . '_tax_type';
$taxonomy     = isset( $settings->$var_tax_type ) ? $settings->$var_tax_type : '';
$orderby      = isset( $settings->order_by ) ? $settings->order_by : 'name';
$order        = isset( $settings->order ) ? $settings->order : 'ASC';
$show_count   = 1;
$pad_counts   = 1;
$hierarchical = 1;
$title        = '';
$empty        = ( isset( $settings->show_empty ) && 'yes' === $settings->show_empty ) ? false : true;

$taxonomy_thumbnail_enable     = BB_PowerPack_Taxonomy_Thumbnail::$taxonomy_thumbnail_enable;
$taxonomy_thumbnail_taxonomies = BB_PowerPack_Taxonomy_Thumbnail::$taxonomies;

$args = array(
	'taxonomy'     => $taxonomy,
	'orderby'      => $orderby,
	'order'        => $order,
	'show_count'   => $show_count,
	'pad_counts'   => $pad_counts,
	'hierarchical' => $hierarchical,
	'title_li'     => $title,
	'hide_empty'   => $empty,
);

// Order by meta value arg.
if ( strstr( $orderby, 'meta_value' ) && isset( $settings->order_by_meta_key ) ) {
	$args['meta_key'] = $settings->order_by_meta_key;
}

// Matching rules.
if ( ! empty( $taxonomy ) && ! is_array( $taxonomy ) ) {
	$var_cat_matching = $var_cat = '';
	$var_cat          = 'tax_' . $post_type . '_' . $taxonomy;
	$var_cat_matching = $var_cat . '_matching';
	$cat_match    = isset( $settings->$var_cat_matching ) ? $settings->$var_cat_matching : false;
	$ids          = isset( $settings->$var_cat ) ? explode( ',', $settings->$var_cat ) : array();

	if ( $cat_match && ! empty( $ids ) ) {
		if ( isset( $settings->display_data ) && ( 'children_only' === $settings->display_data || 'default' === $settings->display_data ) && ! empty( $ids[0] ) ) {
			//only single value is allowed so we have made new custom function, get_child_categories()
			$args['parent'] = $ids;
		} else {
			$args['include'] = $ids;
		}
	}
	if ( ! $cat_match && ! empty( $ids ) ) {
		if ( isset( $settings->display_data ) && ( 'parent_only' !== $settings->display_data ) && ! empty( $ids[0] ) ) {
	
			foreach ( $ids as $term_id ) {
				$tmp_ids = get_term_children( $term_id, $taxonomy );
				$ids     = ! is_wp_error( $tmp_ids ) ? array_merge( $ids, $tmp_ids ) : $ids;
			}
			$args['exclude'] = $ids;
		} else {
			$args['exclude'] = $ids;
		}
	}
}
if ( ! empty( $taxonomy ) && is_array( $taxonomy ) ) {
	$parent  = array();
	$include = array();
	$exclude = array();

	foreach ( $taxonomy as $tax ) {
		$var_cat          = 'tax_' . $post_type . '_' . $tax;
		$var_cat_matching = $var_cat . '_matching';
		$cat_match    = isset( $settings->$var_cat_matching ) ? $settings->$var_cat_matching : false;
		$ids          = isset( $settings->$var_cat ) && ! empty( $settings->$var_cat ) ? explode( ',', $settings->$var_cat ) : array();

		if ( $cat_match && ! empty( $ids ) ) {
			if ( isset( $settings->display_data ) && ( 'children_only' === $settings->display_data || 'default' === $settings->display_data ) && ! empty( $ids[0] ) ) {
				//only single value is allowed so we have made new custom function, get_child_categories()
				$parent = empty( $parent ) ? $ids : array_merge( $parent, $ids );
			} else {
				$include = empty( $include ) ? $ids : array_merge( $include, $ids );
			}
		}
		if ( ! $cat_match && ! empty( $ids ) ) {
			if ( isset( $settings->display_data ) && ( 'parent_only' !== $settings->display_data ) && ! empty( $ids[0] ) ) {
		
				foreach ( $ids as $term_id ) {
					$tmp_ids = get_term_children( $term_id, $taxonomy );
					$ids     = ! is_wp_error( $tmp_ids ) ? array_merge( $ids, $tmp_ids ) : $ids;
				}
				$exclude = empty( $exclude ) ? $ids : array_merge( $exclude, $ids );
			} else {
				$exclude = empty( $exclude ) ? $ids : array_merge( $exclude, $ids );
			}
		}
	}

	if ( ! empty( $parent ) ) {
		$args['parent'] = $parent;
	}
	if ( ! empty( $include ) ) {
		$args['include'] = $include;
	}
	if ( ! empty( $exclude ) ) {
		$args['exclude'] = $exclude;
	}
}

// Show child terms on taxonomy archive page.
if ( isset( $settings->on_tax_archive ) && ( is_tax() || is_category() || is_tag() ) ) {
	$settings->is_tax_archive = true;
	$current_object = get_queried_object();
	if ( 'children_only' === $settings->on_tax_archive ) {
		$args['child_of'] = $current_object->term_id;
	}
	if ( 'parent_only' === $settings->on_tax_archive && intval( $current_object->parent ) > 0 ) {
		$args['include'] = (array) $current_object->parent;
	}
}

if ( ! empty( $settings->id ) ) {
	$args['pp_category_grid_id'] = $settings->id;
}

$args = apply_filters( 'pp_category_grid_query_args', $args, $settings );

if ( isset( $settings->display_data ) && 'children_only' === $settings->display_data && isset( $args['parent'] ) && ! empty( $args['parent'][0] ) ) {
	$all_categories = PPCategoryGridModule::get_categories( $args, 'children_only' );
} elseif ( isset( $settings->display_data ) && 'default' === $settings->display_data && isset( $args['parent'] ) && ! empty( $args['parent'][0] ) ) {
	$all_categories = PPCategoryGridModule::get_categories( $args, 'default' );
} else {
	$all_categories = get_categories( $args );
}

global $post;

$current_post_terms = array();
$assigned_only = isset( $settings->on_post ) && 'assigned_only' === $settings->on_post;

if ( is_single() && $post && $post->ID ) {
	$current_post_terms = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'slugs' ) );
}

$hide_img            = isset( $settings->category_show_image ) && 'no' === $settings->category_show_image;
$is_tax_archive      = is_tax() || is_category() || is_tag();
$queried_object      = $is_tax_archive ? get_queried_object() : false;
$current_cat         = $queried_object ? $queried_object->term_id : false;
$exclude_current_cat = apply_filters( 'pp_category_grid_exclude_current_category', true, $settings );
$all_categories      = apply_filters( 'pp_category_grid_categories', $all_categories, $settings );

do_action( 'pp_category_grid_before_container', $settings );
?>

<div class="pp-categories-outer">
<div class="pp-categories-container<?php echo 'yes' === $settings->category_grid_slider ? ' swiper swiper-container' : ''; ?>">
	<div class="pp-categories<?php echo 'yes' === $settings->category_grid_slider ? ' swiper-wrapper' : ''; ?> pp-clear">
	<?php

	foreach ( $all_categories as $cat ) {
		// filter categories which are actually assigned to current post.
		if ( $assigned_only && ! empty( $current_post_terms ) && ! in_array( $cat->slug, $current_post_terms ) ) {
			continue;
		}

		// Exclude current term.
		if ( $exclude_current_cat && $queried_object && $cat->term_id === $queried_object->term_id ) {
			continue;
		}

		if ( isset( $settings->display_data ) && 'parent_only' === $settings->display_data ) {
			if ( isset( $args['include'][0] ) && intval( $args['include'][0] ) > 0 ) {
				$inc_array = $args['include'];
				if ( ! in_array( $cat->term_id, $inc_array ) ) {
					continue;
				}
			} elseif ( 0 !== $cat->parent ) {
				continue;
			} elseif ( ! empty( $current_post_terms ) ) {
				// To display only assigned parents on single post.
				$parent = get_term_by( 'id', $cat->parent );
				if ( $parent && ! in_array( $parent->slug, $current_post_terms ) ) {
					continue;
				}
			}
		} elseif ( isset( $settings->display_data ) && 'children_only' === $settings->display_data ) {
			$key = isset( $settings->display_direct_child ) && 'yes' === $settings->display_direct_child ? 'parent' : 'include';
			if ( isset( $args[ $key ][0] ) && intval( $args[ $key ][0] ) > 0 ) {
				$inc_array = $args[ $key ];
				if ( ! in_array( $cat->parent, $inc_array ) ) {
					continue;
				}
			} elseif ( isset( $args['exclude'][0] ) && intval( $args['exclude'][0] ) > 0 ) {
				$exc_array = $args['exclude'];
				if ( in_array( $cat->parent, $exc_array ) || 0 === $cat->parent ) {
					continue;
				}
			} elseif ( 0 === $cat->parent ) {
				continue;
			} elseif ( ! empty( $current_post_terms ) ) {
				// To display only assigned children on single post.
				$parent = get_term_by( 'id', $cat->parent );
				if ( $parent && ! in_array( $parent->slug, $current_post_terms ) ) {
					continue;
				}
			}
		} elseif ( isset( $settings->display_data ) && 'default' === $settings->display_data && isset( $args['exclude'] ) && ! empty( $args['exclude'][0] ) ) {
			$exc_array = $args['exclude'];
			if ( in_array( $cat->parent, $exc_array ) ) {
				continue;
			}
		}

		$layout_path = apply_filters( 'pp_category_grid_layout_path', $module->dir . 'includes/layout-1.php', $cat, $settings );

		if ( ! empty( $layout_path ) ) {
			include $layout_path;
		}
	}
	?>
	</div>

	<?php
	if ( 'yes' === $settings->category_grid_slider ) {
		?>
		<?php if ( 'none' !== $settings->pagination_type ) { ?>
		<div class="swiper-pagination"></div>
		<?php } ?>
	<?php }
	?>

</div>
	<?php
	if ( 'yes' === $settings->category_grid_slider && 'yes' === $settings->slider_navigation ) { ?>
		<div class="pp-swiper-button swiper-button-prev"><?php pp_prev_icon_svg(); ?></div>
		<div class="pp-swiper-button swiper-button-next"><?php pp_next_icon_svg(); ?></div>
		<?php
	}
	?>
</div>
<?php do_action( 'pp_category_grid_after_container', $settings ); ?>