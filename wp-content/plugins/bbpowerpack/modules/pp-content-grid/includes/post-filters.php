<?php
if ( empty( $settings->post_grid_filters ) || 'none' === $settings->post_grid_filters ) {
	return;
}
if ( ! in_array( $settings->data_source, array( 'main_query', 'custom_query' ) ) ) {
	return;
}

$all_label			= empty( $settings->all_filter_label ) ? __('All', 'bb-powerpack') : $settings->all_filter_label;
$post_filter_tax 	= $settings->post_grid_filters;
$default_filter		= isset( $settings->post_grid_filters_default ) ? $settings->post_grid_filters_default : '';
$taxonomy           = get_taxonomy( $post_filter_tax );
$terms_to_show		= isset( $settings->post_grid_filters_terms ) ? $settings->post_grid_filters_terms : '';
$parent_term		= isset( $settings->post_grid_filters_parent_term ) ? $settings->post_grid_filters_parent_term : '';
$terms_to_show_archive     = isset( $settings->post_grid_filters_archive_terms ) ? $settings->post_grid_filters_archive_terms : '';
$terms_children_on_archive = ( is_tax( $post_filter_tax ) || is_category() ) && ! empty( $terms_to_show_archive );

if ( $terms_children_on_archive ) {
	$terms_to_show = $terms_to_show_archive;
}

$order_by          = isset( $settings->post_grid_filters_order_by ) ? $settings->post_grid_filters_order_by : 'name';
$order             = isset( $settings->post_grid_filters_order ) ? $settings->post_grid_filters_order : 'ASC';
$order_by_meta_key = isset( $settings->post_grid_filters_order_by_meta_key ) ? $settings->post_grid_filters_order_by_meta_key : '';

$post_filter_args = array(
	'taxonomy' => $post_filter_tax,
	'orderby'  => $order_by,
	'order'    => $order,
);

if ( 'meta_value' === $order_by || 'meta_value_num' === $order_by ) {
	$post_filter_args['meta_key'] = $order_by_meta_key;
}

$post_filter_terms = array();

if ( 'custom_query' === $settings->data_source ) {
	$post_type = isset( $settings->post_type ) ? $settings->post_type : 'post';
	$post_type = (array) $post_type;

	foreach ( $post_type as $type ) {

		$post_filter_field = 'tax_' . $type . '_' . $post_filter_tax;

		if ( isset( $settings->{$post_filter_field} ) ) :

			$post_filter_value	= $settings->{$post_filter_field};
			$post_filter_matching = $settings->{$post_filter_field . '_matching'};

			if ( $post_filter_value ) {
				$post_filter_term_ids = explode( ",", $post_filter_value );
				if ( ! $post_filter_matching ) {
					$post_filter_args['exclude'] = $post_filter_term_ids;
					$post_filter_terms = get_terms( $post_filter_args );
				} else {
					foreach ( $post_filter_term_ids as $post_filter_term_id ) {
						$post_filter_terms[] = get_term_by('id', $post_filter_term_id, $post_filter_tax);
					}
				}
			}

		endif;
	}
}

if ( 'main_query' === $settings->data_source && $terms_children_on_archive ) {
	$current_term = get_queried_object();
	$current_children = get_term_children( $current_term->term_id, $current_term->taxonomy );
	if ( count( $current_children ) === 0 ) {
		return;
	}
	$terms = array( $current_term );
} else {
	$terms = array();

	if ( count( $post_filter_terms ) > 0 ) {
		$terms = $post_filter_terms;
	} elseif ( 'children' === $terms_to_show && ! empty( absint( $parent_term ) ) ) {
		$child_terms_args = array_merge( $post_filter_args, array( 'hide_empty' => true, 'child_of' => absint( $parent_term ) ) );
		$terms = get_terms( $child_terms_args );
	}

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		$terms = get_terms( $post_filter_args );
	}
}

// This might be heavy DB query, use this when there are not a large number of posts.
if ( apply_filters( 'pp_cg_filters_show_available_posts_terms', false, $settings ) ) {

	$sub_query = clone $query;

	$sub_query->set( 'numberposts', '-1' );
	$sub_query->set( 'posts_per_page', '-1' );
	$sub_query->set( 'nopaging', true );

	$rendered_posts = $sub_query->query( $sub_query->query_vars );

	$rendered_terms = array();
		
	foreach ( $rendered_posts as $single ) {
		$post_terms = wp_get_object_terms( $single->ID, $post_filter_tax, array( 'fields' => 'ids' ) );
		$post_terms = apply_filters( 'pp_cg_filters_post_terms', $post_terms, $single, $settings );
		if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {
			foreach ( $post_terms as $term ) {
				$rendered_terms[] = $term;
			}
		}
	}

	$rendered_terms = array_unique( $rendered_terms );
	
	if ( ! empty( $rendered_terms ) ) {
		$new_terms = array();
		
		foreach ( $terms as $term ) {
			if ( in_array( $term->term_id, $rendered_terms ) ) {
				$new_terms[] = $term;
			}
		}
		
		$terms = $new_terms;
	} else {
		$terms = array();
	}

	unset( $sub_query );
	unset( $rendered_posts );
}

$terms = apply_filters( 'pp_cg_filter_terms', $terms, $settings );
?>
<div class="pp-post-filters-wrapper">
	<div class="pp-post-filters-toggle">
		<span class="toggle-text"><?php echo $all_label; ?></span>
	</div>
	<ul class="pp-post-filters">
		<?php
			if ( empty( $default_filter ) ) {
				echo apply_filters( 'pp_cg_filters_all', '<li class="pp-post-filter pp-filter-active" data-filter="*" tabindex="0" aria-label="'. strip_tags( $all_label ) .'">' . $all_label . '</li>', $settings );
			} else {
				echo apply_filters( 'pp_cg_filters_all', '<li class="pp-post-filter" data-filter="*" tabindex="0" aria-label="'. strip_tags( $all_label ) .'">' . $all_label . '</li>', $settings );
			}
			if ( is_array( $terms ) && count( $terms ) ) {
				$filter_terms = array();
				foreach ( $terms as $term ) {
					if ( ! empty( $terms_to_show ) ) {
						if ( 'parent' === $terms_to_show ) {
							if ( $term->parent ) {
								$filter_terms[] = $term->parent;
								continue;
							} else {
								$filter_terms[] = $term->term_id;
							}
						} elseif ( 'children' === $terms_to_show ) {
							if ( ! $term->parent ) {
								$child_terms_args = array_merge( $post_filter_args, array( 'taxonomy' => $term->taxonomy, 'hide_empty' => false, 'child_of' => $term->term_id, 'fields' => 'ids' ) );
								$current_term_children = get_terms( $child_terms_args );
								$filter_terms = array_merge( $filter_terms, $current_term_children );
								continue;	
							} else {
								$term_children = get_term_children( $term->term_id, $term->taxonomy );
                                if ( is_array( $term_children ) && count( $term_children ) > 0 ) {
                                    $filter_terms = array_merge( $filter_terms, $term_children );
                                } else {
                                    $filter_terms[] = $term->term_id;
                                }
							}
						}
					} else {
						$filter_terms[] = $term->term_id;
					}
				}
				if ( ! is_wp_error( $filter_terms ) && count( $filter_terms ) ) {
					$filter_terms = array_unique( $filter_terms );
					$filter_terms = apply_filters( 'pp_cg_filtered_terms', $filter_terms, $terms, $settings );
					foreach ( $filter_terms as $term_id ) {
						$term = apply_filters( 'pp_cg_filtered_term', get_term( $term_id ), $filter_terms, $settings );
						if ( empty( $term ) || 0 == $term->count ) {
							continue;
						}
						$slug = $term->slug;
						$slug = urldecode( $slug ); // Support for non-English letters.
						$filter_active_class = '';
						if ( $slug === $default_filter ) {
							$filter_active_class = ' pp-filter-active';
						}
						if ( $post_filter_tax == 'post_tag' ) {
							echo '<li class="pp-post-filter' . $filter_active_class . '" data-filter=".tag-'.$slug.'" data-term="'.$slug.'" data-item-count="'.$term->count.'" tabindex="0" aria-label="'. strip_tags( $term->name ) .'">'.$term->name.'</li>';
						} else {
							echo '<li class="pp-post-filter' . $filter_active_class . '" data-filter=".'.$taxonomy->name.'-'.$slug.'" data-term="'.$slug.'" data-item-count="'.$term->count.'" tabindex="0" aria-label="'. strip_tags( $term->name ) .'">'.$term->name.'</li>';
						}
					}
				}
			}
		?>
	</ul>
</div>

<?php do_action( 'pp_cg_after_post_filters', $settings ); ?>