<?php
/**
 * Handles logic for AJAX.
 *
 * @package BB_PowerPack
 * @since 1.0.0
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PowerPack AJAX handler.
 */
class BB_PowerPack_Ajax {
	static public $cg_settings = array();
	/**
	 * Initializes actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function init() {
		add_action( 'wp', 										__CLASS__ . '::handle_ajax' );
		add_action( 'pp_post_grid_ajax_before_query', 			__CLASS__ . '::loop_fake_actions' );
		add_action( 'wp_ajax_pp_get_posts', 					__CLASS__ . '::get_ajax_posts' );
		add_action( 'wp_ajax_nopriv_pp_get_posts', 		     	__CLASS__ . '::get_ajax_posts' );
		add_action( 'wp_ajax_pp_get_taxonomies', 				__CLASS__ . '::get_post_taxonomies' );
		add_action( 'wp_ajax_nopriv_pp_get_taxonomies', 		__CLASS__ . '::get_post_taxonomies' );
		add_action( 'wp_ajax_pp_get_saved_templates', 			__CLASS__ . '::get_saved_templates' );
		add_action( 'wp_ajax_nopriv_pp_get_saved_templates', 	__CLASS__ . '::get_saved_templates' );
		add_action( 'wp_ajax_pp_modal_dynamic_content', 		__CLASS__ . '::modal_dynamic_content' );
		add_action( 'wp_ajax_nopriv_pp_modal_dynamic_content', 	__CLASS__ . '::modal_dynamic_content' );
		add_action( 'wp_ajax_pp_notice_close', 					__CLASS__ . '::close_notice' );
		add_filter( 'found_posts', 								__CLASS__ . '::found_posts', 1, 2 );
	}

	/**
	 * Hooks for fake loop.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	static public function loop_fake_actions() {
		if ( apply_filters( 'pp_post_grid_ajax_fake_loop', false ) ) {
			add_action( 'loop_start', __CLASS__ . '::fake_loop_true' );
			add_action( 'loop_end', __CLASS__ . '::fake_loop_false' );
		}
	}

	/**
	 * Fake loop.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	static public function fake_loop_true() {
		global $wp_query;
		// Fake being in the loop.
		$wp_query->in_the_loop = true;
	}

	/**
	 * Reset fake loop.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	static public function fake_loop_false() {
		global $wp_query;
		// Stop faking being in the loop.
		$wp_query->in_the_loop = false;

		remove_action( 'loop_start', __CLASS__ . '::fake_loop_true' );
		remove_action( 'loop_end', __CLASS__ . '::fake_loop_false' );
	}

	/**
	 * Execute method based on action passed.
	 *
	 * @return void
	 */
	static public function handle_ajax() {
		if ( ! isset( $_POST['pp_action'] ) || empty( $_POST['pp_action'] ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_POST['pp_action'] ) );

		if ( ! method_exists( __CLASS__, $action ) ) {
			return;
		}

		// Tell WordPress this is an AJAX request.
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$method = $action;

		self::$method();
	}

	/**
	 * Logic to upload CSV file using Table module.
	 *
	 * @return void
	 */
	static public function table_csv_upload() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( __( 'Error uploading file.', 'bb-powerpack' ) );
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'pp_table_csv' ) ) { // input var okay.
			wp_send_json_error( __( 'Invalid request.', 'bb-powerpack' ) );
		}

		if ( ! isset( $_FILES['file'] ) ) {
			wp_send_json_error( __( 'Please provide CSV file.', 'bb-powerpack' ) );
		}

		$file = $_FILES['file'];

		// validate file type.
		if ( 'csv' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
			wp_send_json_error( __( 'Invalid file type. Please provide CSV file.', 'bb-powerpack' ) );
		}

		$upload_dir = pp_get_upload_dir();

		$source_path = $file['tmp_name'];
		$target_path = $upload_dir['path'] . $file['name'];

		if ( file_exists( $target_path ) ) {
			unlink( $target_path );
		}

		if ( move_uploaded_file( $source_path, $target_path ) ) {
			wp_send_json_success( array(
				'filename' 		=> $file['name'],
				'filepath'		=> $target_path,
				'upload_time'	=> isset( $_POST['time'] ) ? esc_attr( $_POST['time'] ) : current_time( 'timestamp' ),
			) );
		}

		wp_send_json_error( __( 'Error uploading file.', 'bb-powerpack' ) );
	}

	/**
	 * Logic to query posts for Content Grid.
	 *
	 * @return void
	 */
	static public function get_ajax_posts() {
		$is_error = false;

		$node_id            = isset( $_POST['node_id'] ) ? sanitize_text_field( $_POST['node_id'] ) : false;
		$template_id        = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : false;
		$template_node_id   = isset( $_POST['template_node_id'] ) ? sanitize_text_field( $_POST['template_node_id'] ) : false;

		if ( apply_filters( 'pp_post_grid_ajax_force_module_settings', false ) ) {
			if ( isset( $_POST['settings'] ) ) {
				unset( $_POST['settings'] );
			}
		}

		// Get the module.
		if ( $template_id ) {
			$post_id  = FLBuilderModel::get_node_template_post_id( $template_id );
			$data     = FLBuilderModel::get_layout_data( 'published', $post_id );
			$module   = FLBuilderModel::get_module( $data[ $template_node_id ] );
		} else {
			$module   = FLBuilderModel::get_module( $node_id );
		}

		if ( ! empty( self::$cg_settings ) && isset( self::$cg_settings[ $node_id ] ) ) {
			$settings = self::$cg_settings[ $node_id ];
		} elseif ( ! isset( $_POST['settings'] ) || empty( $_POST['settings'] ) ) {
			$settings = is_object( $module ) ? $module->settings : false;
		} else {
			$settings = (object) $_POST['settings'];
		}

		if ( isset( $settings ) ) {
			if ( class_exists( 'FLThemeBuilderFieldConnections' ) ) {
				$settings = FLThemeBuilderFieldConnections::connect_settings( $settings );
			}
			self::$cg_settings[ $node_id ] = $settings;
		} else {
			wp_send_json_error();
		}

		if ( isset( $_POST['post_id'] ) ) {
			unset( $_POST['post_id'] );
		}

		$settings = apply_filters( 'fl_builder_loop_before_query_settings', $settings );

		$args['settings'] = $settings;

		$settings->pp_content_grid = true;

		$module_dir = pp_get_module_dir( 'pp-content-grid' );
		$module_url = pp_get_module_url( 'pp-content-grid' );

		global $wp_scripts;
		global $wp_styles;

		$styles_scripts = '';

		$response = array(
			'data'  => '',
			'styles_scripts' => '',
			'pagination' => false,
		);

		global $post;
		global $wp_query;

		$post_type = 'main_query' === $settings->data_source ? get_post_type() : $settings->post_type;
		$offset    = isset( $settings->offset ) ? intval( $settings->offset ) : 0;

		$args = array(
			'post_type'             => $post_type,
			'post_status'           => 'publish',
			'tax_query'             => array(
				'relation' => 'AND',
			),
			'ignore_sticky_posts'   => true,
			'pp_original_offset'    => $offset,
			'pp_content_grid'       => true,
			'pp_node_id'			=> $node_id,
			'pp_node_html_id'		=> isset( $settings->id ) ? $settings->id : '',
			'settings'				=> $settings,
		);

		if ( 'custom_query' === $settings->data_source ) {

			// author filter.
			if ( isset( $settings->users ) ) {

				$users = $settings->users;
				$arg = 'author__in';

				// Set to NOT IN if matching is present and set to 0.
				if ( isset( $settings->users_matching ) && ! $settings->users_matching ) {
					$arg = 'author__not_in';
				}

				if ( ! empty( $users ) ) {
					if ( is_string( $users ) ) {
						$users = explode( ',', $users );
					}

					$args[ $arg ] = $users;
				}
			}

			// handle current/logged in user
			if ( isset( $settings->users_matching ) && 'loggedin' === $settings->users_matching ) {
				if ( isset( $args['author__in'] ) ) {
					unset( $args['author__in'] );
				}
				if ( isset( $args['author__not_in'] ) ) {
					unset( $args['author__not_in'] );
				}
				$args['author'] = get_current_user_id();
			}
			if ( isset( $settings->users_matching ) && 'author' === $settings->users_matching ) {
				if ( isset( $args['author__in'] ) ) {
					unset( $args['author__in'] );
				}
				if ( isset( $args['author__not_in'] ) ) {
					unset( $args['author__not_in'] );
				}
				$args['author'] = get_the_author_meta( 'ID' );
			}
		} // End if().

		if ( isset( $_POST['author_id'] ) && ! empty( $_POST['author_id'] ) ) {
			$args['author__in'] = array( absint( wp_unslash( $_POST['author_id'] ) ) );
		}

		if ( isset( $_POST['search_term'] ) && ! empty( $_POST['search_term'] ) ) {
			$args['s'] = wp_unslash( $_POST['search_term'] );
		}

		if ( 'no' !== $settings->post_grid_filters_display && 'none' !== $settings->post_grid_filters && isset( $_POST['term'] ) && ! isset( $_POST['is_tax'] ) ) {
			$args['tax_query'] = array(
				'relation'	=> 'AND',
				array(
					'taxonomy' => $settings->post_grid_filters,
					'field'    => 'slug',
					'terms'    => array( sanitize_text_field( wp_unslash( $_POST['term'] ) ) ),
				),
			);
			$args['_filters_query'] = $args['tax_query'];
		} else {
			$query_posts_by_term = apply_filters( 'pp_post_grid_ajax_query_posts_by_term', false, $settings );
			if ( ( 'custom_query' !== $settings->data_source || $query_posts_by_term ) && isset( $_POST['taxonomy'] ) && isset( $_POST['term'] ) ) {
				$args['tax_query'] = array(
					'relation'	=> 'AND',
					array(
						'taxonomy' => sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ),
						'field'    => 'slug',
						'terms'    => array( sanitize_text_field( wp_unslash( $_POST['term'] ) ) ),
					),
				);
			}
		}

		if ( 'custom_query' === $settings->data_source ) {

			foreach ( (array) $post_type as $type ) {

				$taxonomies = FLBuilderLoop::taxonomies( $type );

				foreach ( $taxonomies as $tax_slug => $tax ) {

					$tax_value = '';
					$term_ids  = array();
					$operator  = 'IN';

					// Get the value of the suggest field.
					if ( isset( $settings->{'tax_' . $type . '_' . $tax_slug} ) ) {
						// New style slug.
						$tax_value = $settings->{'tax_' . $type . '_' . $tax_slug};
					} elseif ( isset( $settings->{'tax_' . $tax_slug} ) ) {
						// Old style slug for backwards compat.
						$tax_value = $settings->{'tax_' . $tax_slug};
					}

					// Get the term IDs array.
					if ( ! empty( $tax_value ) ) {
						$term_ids = explode( ',', $tax_value );
					}

					// Handle matching settings.
					if ( isset( $settings->{'tax_' . $type . '_' . $tax_slug . '_matching'} ) ) {

						$tax_matching = $settings->{'tax_' . $type . '_' . $tax_slug . '_matching'};

						if ( ! $tax_matching ) {
							// Do not match these terms.
							$operator = 'NOT IN';
						} elseif ( 'related' === $tax_matching ) {
							// Match posts by related terms from the global post.
							global $post;
							$terms 	 = wp_get_post_terms( $post->ID, $tax_slug );
							$related = array();

							foreach ( $terms as $term ) {
								if ( ! in_array( $term->term_id, $term_ids ) ) {
									$related[] = $term->term_id;
								}
							}

							if ( empty( $related ) ) {
								// If no related terms, match all except those in the suggest field.
								$operator = 'NOT IN';
							} else {

								// Don't include posts with terms selected in the suggest field.
								$args['tax_query'][] = array(
									'taxonomy'	=> $tax_slug,
									'field'		=> 'id',
									'terms'		=> $term_ids,
									'operator'  => 'NOT IN',
								);

								// Set the term IDs to the related terms.
								$term_ids = $related;
							}
						}
					} // End if().

					if ( ! empty( $term_ids ) ) {

						$args['tax_query'][] = array(
							'taxonomy'	=> $tax_slug,
							'field'		=> 'id',
							'terms'		=> $term_ids,
							'operator'  => $operator,
						);
					}
				} // End foreach().
			} // End foreach().
		}
		
		if ( 'main_query' !== $settings->data_source ) {
			if ( isset( $settings->posts_per_page ) ) {
				$args['posts_per_page'] = $settings->posts_per_page;
			}

			$post__in = $post__not_in = array();

			foreach ( (array) $post_type as $type ) {
				// Post in/not in query.
				if ( isset( $settings->{'posts_' . $type} ) ) {

					$ids = $settings->{'posts_' . $type};
					$arg = 'post__in';

					// Set to NOT IN if matching is present and set to 0.
					if ( isset( $settings->{'posts_' . $type . '_matching'} ) ) {
						if ( ! $settings->{'posts_' . $type . '_matching'} ) {
							$arg = 'post__not_in';
						}
					}

					// Add the args if we have IDs.
					if ( ! empty( $ids ) && 'post__in' === $arg ) {
						$post__in = array_merge( $post__in, explode( ',', $ids ) );
					}
					if ( ! empty( $ids ) && 'post__not_in' === $arg ) {
						$post__not_in = array_merge( $post__not_in, explode( ',', $ids ) );
					}
				}
			}
			
			if ( ! empty( $post__in ) ) {
				$args['post__in'] = $post__in;
			}
			
			if ( ! empty( $post__not_in ) ) {
				$args['post__not_in'] = $post__not_in;
			}
			
			// Exclude current post.
			if ( $post && isset( $settings->exclude_current_post ) && 'yes' === $settings->exclude_current_post ) {
				$args['post__not_in'][] = $post->ID;
			}
		}

		// Do not query WooCommerce out of stock products.
		// Also, making sure this applies to product post type only.
		if (
			in_array( 'product', (array) $post_type ) && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && 
			( ! isset( $settings->post_grid_filters_post_type ) || 'product' === $settings->post_grid_filters_post_type )
		) {
			if ( apply_filters( 'pp_post_grid_ajax_wc_hide_out_of_stock', true, $args ) ) {
				$args['meta_query'][] = array(
					'key'       => '_stock_status',
					'value'     => 'instock',
					'compare'   => '=',
				);
			}
		}

		if ( isset( $_POST['paged'] ) ) {
			$args['paged'] = absint( wp_unslash( $_POST['paged'] ) );
		}

		if ( 'main_query' !== $settings->data_source && 'acf_relationship' !== $settings->data_source ) {
			// Offset.
			if ( isset( $settings->offset ) ) {
				$page = isset( $args['paged'] ) ? $args['paged'] : 1;
				$per_page = ( isset( $args['posts_per_page'] ) && $args['posts_per_page'] > 0 ) ? $args['posts_per_page'] : 10;
				if ( $page < 2 ) {
					$args['offset'] = absint( $settings->offset );
				} else {
					$args['offset'] = absint( $settings->offset ) + ( ( $page - 1 ) * $per_page );
				}
			}

			// Order by author.
			if ( 'author' === $settings->order_by ) {
				$args['orderby'] = array(
					'author' => $settings->order,
					'date' => $settings->order,
				);
			} else {
				$args['orderby'] = $settings->order_by;

				// Order by meta value arg.
				if ( strstr( $settings->order_by, 'meta_value' ) ) {
					$args['meta_key'] = $settings->order_by_meta_key;
				}

				if ( isset( $_POST['orderby'] ) ) {
					$orderby = esc_attr( wp_unslash( $_POST['orderby'] ) );

					$args = self::get_conditional_args( $orderby, $args );
				}

				if ( isset( $settings->order ) ) {
					$args['order'] = $settings->order;
				}
			}
		} // End if().

		// ACF Relationship post order.
		if ( 'acf_relationship' === $settings->data_source && is_callable( 'FLThemeBuilderACF::loop_query_args' ) ) {
			$acf_args = FLThemeBuilderACF::loop_query_args( $args );
			if ( ! empty( $acf_args ) ) {
				$args = $acf_args;
			}
		}

		// Filter by meta key
		if ( ( isset( $settings->custom_field ) && is_array( $settings->custom_field ) && count( $settings->custom_field ) > 0 ) && 'custom_query' == $settings->data_source ) {
			if ( count( $settings->custom_field ) == 1 ) {
				$field = (object) $settings->custom_field[0];
				if ( isset( $field->filter_meta_key ) && ! empty( $field->filter_meta_key ) ) {
					$args['meta_key'] = untrailingslashit( $field->filter_meta_key );
					if ( 'EXISTS' != $field->filter_meta_compare && 'NOT EXISTS' != $field->filter_meta_compare ) {
						$args['meta_value'] = do_shortcode( untrailingslashit( $field->filter_meta_value ) );
					}
					$args['meta_type']    = $field->filter_meta_type;
					$args['meta_compare'] = $field->filter_meta_compare;
					
				}
			} else {
				if ( isset( $settings->custom_field_relation ) ) {
					$args['meta_query']['relation'] = $settings->custom_field_relation;
					foreach ( $settings->custom_field as $field ) {
						if ( ! empty( $field ) ) {
							$field            = (object) $field;
							$filter_arr        = array();
							$filter_arr['key'] = untrailingslashit( $field->filter_meta_key );
							if ( 'EXISTS' != $field->filter_meta_compare && 'NOT EXISTS' != $field->filter_meta_compare ) {
								$filter_arr['value'] = do_shortcode( untrailingslashit( $field->filter_meta_value ) );
							}
							$filter_arr['type']    = $field->filter_meta_type;
							$filter_arr['compare'] = $field->filter_meta_compare;
							$args['meta_query'][]  = $filter_arr;
						}
					}
				}
			}
		}

		if ( in_array( 'tribe_events', (array) $post_type ) ) {

			if ( isset( $settings->event_orderby ) && '' !== $settings->event_orderby ) {
				$orderby = $settings->event_orderby;
			} else {
				$orderby = 'EventStartDate';
			}

			if ( isset( $settings->event_order ) && '' !== $settings->event_order ) {
				$order = $settings->event_order;
			} else {
				$order = 'ASC';
			}

			$args['meta_key']     = '_' . $orderby;
			$args['orderby']      = 'meta_value';
			$args['order']        = $order;
			$args['eventDisplay'] = 'custom';

			if ( 'custom_query' === $settings->data_source ) {
				if ( isset( $settings->event_orderby ) && empty( $settings->event_orderby ) ) {
					$args['orderby'] = $settings->order_by;
					$args['order']   = $settings->order;
				}
			}

			if ( 'all' !== $settings->show_events || 'custom_query' === $settings->data_source ) {
				$args['meta_query'] = self::get_events_meta_query( $settings->show_events );
			}
		}

		/**
		 * Filter query arguments.
		 */
		$args = apply_filters( 'pp_post_grid_ajax_query_args', $args );

		/**
		 * Before query is performed.
		 * 
		 * @param $settings Module settings object.
		 */
		do_action( 'pp_post_grid_ajax_before_query', $settings );

		/**
		 * Custom Content Workaround for Pods fields.
		 *
		 * Before query is performed.
		 * @see fl_builder_loop_before_query
		 * 
		 * @since 2.14.0
		 */
		do_action( 'fl_builder_loop_before_query', $settings );

		if ( isset( $args['settings'] ) ) {
			unset( $args['settings'] );
		}

		if ( 'main_query' !== $settings->data_source ) {
			$query = new WP_Query( $args );
		} else {
			$query = $wp_query;
			if ( method_exists( 'WC_Query', 'pre_get_posts' ) ) {
				WC()->query->pre_get_posts( $query );

				if ( isset( $query->query_vars['wc_query'] ) ) {
					unset( $query->query_vars['wc_query'] );
				}
			}

			if ( isset( $_POST['post_type'] ) && ! empty( $_POST['post_type'] ) ) {
				$post_type = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );
				$query->set( 'post_type', $post_type );
			}

			$tax_query = $query->get( 'tax_query' );

			if ( ! is_array( $tax_query ) ) {
				$tax_query = array();
			}
			
			if ( isset( $args['tax_query'] ) ) {
				$query->set( 'tax_query', array_merge( $tax_query, $args['tax_query'] ) );
			}

			// if ( isset( $args['orderby'] ) ) {
			// 	$query->set( 'orderby', $args['orderby'] );
			// }
			// if ( isset( $args['order'] ) ) {
			// 	$query->set( 'order', $args['order'] );
			// }
	
			if ( isset( $_POST['paged'] ) ) {
				$query->set('paged', absint( wp_unslash( $_POST['paged'] ) ) );
			}

			if ( isset( $_POST['author_id'] ) && ! empty( $_POST['author_id'] ) ) {
				$query->set( 'author__in', array( absint( wp_unslash( $_POST['author_id'] ) ) ) );
			}

			if ( isset( $_POST['search_term'] ) && ! empty( $_POST['search_term'] ) ) {
				$search_post_type = isset( $_POST['search_post_type'] ) && ! empty( $_POST['search_post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['search_post_type'] ) ) : 'any';
				$query->is_search = true;
				$query->set( 's', wp_unslash( $_POST['search_term'] ) );
				$query->set( 'p', 0 );
				$query->set( 'page_id', 0 );
				$query->set( 'post_type', $search_post_type );
			}

			$query = new WP_Query( $query->query_vars );
		}

		// Add compatibility for native search form module.
		if ( $query->is_search() && is_callable( 'PPSearchFormModule::build_query' ) ) {
			$query->is_search = true;
			$query = PPSearchFormModule::build_query( $query, $settings );
		}

		// Add compatibility for Relevanssi.
		if ( $query->is_search() && function_exists( 'relevanssi_do_query' ) ) {
			relevanssi_do_query( $query );
		}

		/**
		 * After query is performed.
		 * 
		 * @param $settings Module settings object.
		 * @param $query    WP_Query instance.
		 */
		do_action( 'pp_post_grid_ajax_after_query', $settings, $query );

		if ( $query->have_posts() ) :

			// create pagination.
			if ( $query->max_num_pages > 1 && 'none' !== $settings->pagination ) {
				if ( isset( $_POST['loop_count'] ) ) {
					// Reset loop counter for accurate pagination URL generation.
					FLBuilderLoop::$loop_counter = absint( wp_unslash( $_POST['loop_count'] ) );
				}
				$style = ( 'scroll' === $settings->pagination || 'load_more' === $settings->pagination ) ? ' style="display: none;"' : '';
				ob_start();

				echo '<div class="pp-content-grid-pagination pp-ajax-pagination fl-builder-pagination"' . $style . '>';
				if ( ('scroll' === $settings->pagination || 'load_more' === $settings->pagination ) && isset( $_POST['term'] ) ) {
					BB_PowerPack_Post_Helper::ajax_pagination(
						$query,
						$settings,
						esc_attr( wp_unslash( $_POST['current_page'] ) ),
						esc_attr( wp_unslash( $_POST['paged'] ) ),
						sanitize_text_field( wp_unslash( $_POST['term'] ) ),
						esc_attr( wp_unslash( $_POST['node_id'] ) )
					);
				} else {
					BB_PowerPack_Post_Helper::ajax_pagination(
						$query,
						$settings,
						esc_attr( wp_unslash( $_POST['current_page'] ) ),
						esc_attr( wp_unslash( $_POST['paged'] ) )
					);
				}
				echo '</div>';
				if ( 'load_more' == $settings->pagination ) { ?>
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
				<?php }

				$response['pagination'] = ob_get_clean();
			}
			if ( $query->max_num_pages < 1 ) {
				$response['last'] = true;
			}

			$count = 0;
			$link_target = isset( $settings->link_target_new ) && 'yes' === $settings->link_target_new ? ' target="_blank" rel="noopener bookmark"' : '';

			// posts query.
			while ( $query->have_posts() ) {

				$query->the_post();

				$post_id 	 = get_the_ID();
				$post_type   = get_post_type( get_post() );
				$permalink 	 = apply_filters( 'pp_cg_post_permalink', get_permalink(), $post_id, $settings );
				$title_attrs = the_title_attribute( array( 'echo' => false ) );

				$terms_list  = wp_get_post_terms( $post_id, $settings->post_taxonomies );
				$terms_list  = is_wp_error( $terms_list ) ? array() : $terms_list;

				$template_data = array(
					'settings'    => $settings,
					'post_id'     => $post_id,
					'permalink'   => $permalink,
					'post_type'   => $post_type,
					'title_attrs' => $title_attrs,
					'terms_list'  => $terms_list,
					'link_target' => $link_target
				);

				if ( is_object( $module ) ) {
					$module->set_template_data( $template_data );
				}

				$count++;

				ob_start();

				if ( 'custom' === $settings->post_grid_style_select ) {
					include BB_POWERPACK_DIR . 'includes/post-module-layout.php';
				} else {
					$layout_path = file_exists( $module_dir . 'includes/post-grid.php' ) ? $module_dir . 'includes/post-grid.php' : BB_POWERPACK_DIR . 'modules/pp-content-grid/includes/post-grid.php';
					include apply_filters( 'pp_cg_module_layout_path', $layout_path, $settings->layout, $settings );
				}

				$response['data'] .= do_shortcode( wp_unslash( ob_get_clean() ) );
			}

			wp_reset_postdata();

		else :
			$no_posts_found = apply_filters( 'pp_post_grid_ajax_not_found_text', esc_html__( 'No posts found.', 'bb-powerpack' ), $settings, $query );
			$response['data'] = '<div class="pp-content-post pp-posts-not-found-text">' . $no_posts_found . '</div>';
		endif;

		$layout_shortcode_ids = array();

		if ( preg_match_all( '/(?<=fl_builder_insert_layout).*[id]=[\'"]?([0-9]+)/i', $response['data'], $matches ) ) {
			$layout_shortcode_ids = $matches[1];
			$layout_shortcode_ids = array_unique( $layout_shortcode_ids );
		}

		ob_start();

		echo do_shortcode( $response['data'] );

		if ( ! empty( $layout_shortcode_ids ) ) {
			foreach ( $layout_shortcode_ids as $layout_id ) {
				pp_enqueue_layout_assets( $layout_id );
			}
		}

		// if ( isset( $wp_scripts ) ) {
		// 	$wp_scripts->done[] = 'jquery';
		// 	wp_print_scripts( $wp_scripts->queue );
		// }
		if ( isset( $wp_styles ) ) {
			wp_print_styles( $wp_styles->queue );
		}

		$content = ob_get_clean();

		if ( preg_match_all( '/<link\b[^>]*>/is', $content, $link_tags ) ) {
			foreach ( $link_tags[0] as $value ) {
				$styles_scripts .= $value;
			}

			$content = preg_replace('/<link\b[^>]*>/is', '', $content);
		}
		if ( preg_match_all( '/<style\b[^>]*>.*?<\/style>/is', $content, $styles ) ) {
			foreach ( $styles[0] as $value ) {
				$styles_scripts .= $value;
			}

			$content = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $content);
		}
		if ( apply_filters( 'pp_cg_loop_filter_script_tags', true, $settings ) && preg_match_all( '/<script(?!(?:[^>]*type\s*=\s*["\']application\/(?:json|ld\+json)["\']))\b[^>]*>.*?<\/script>/is', $content, $scripts ) ) {
			foreach ( $scripts[0] as $value ) {
				$style_scripts .= $value;
			}

			$content = preg_replace('/<script(?!(?:[^>]*type\s*=\s*["\']application\/(?:json|ld\+json)["\']))\b[^>]*>.*?<\/script>/is', '', $content);
		}

		$response['data'] = $content;
		$response['styles_scripts'] = $styles_scripts;

		$response = apply_filters( 'pp_post_grid_ajax_response', $response, $settings, $query );

		wp_reset_query();

		wp_send_json( $response );
	}

	/**
	 * Determine the Post Meta Query to use by computing today's date based on the timezone settings.
	 *
	 * @since 2.21
	 * @param string $show_events
	 * @return array
	 */
	static private function get_events_meta_query( $show_events ) {
		$meta_query = array();

		if ( function_exists( 'current_datetime' ) ) {
			$local_time = current_datetime();
		} else {
			$tz = get_option( 'timezone_string' );

			if ( empty( $tz ) ) {
				$offset  = (float) get_option( 'gmt_offset' );
				$hours   = (int) $offset;
				$minutes = ( $offset - $hours );

				$sign     = ( $offset < 0 ) ? '-' : '+';
				$abs_hour = abs( $hours );
				$abs_mins = abs( $minutes * 60 );
				$tz       = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
			}

			$local_time = new DateTimeImmutable( 'now', new DateTimeZone( $tz ) );
		}

		$current_time = $local_time->getTimestamp() + $local_time->getOffset();
		$today        = gmdate( 'Y-m-d 00:00:00', $current_time );
		$now          = gmdate( 'Y-m-d H:i:s', $current_time );

		if ( 'today' === $show_events ) {

			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => '_EventStartDate',
					'compare' => '<=',
					'value'   => $today,
					'type'    => 'DATE',
				),
				array(
					'key'     => '_EventEndDate',
					'compare' => '>=',
					'value'   => $today,
					'type'    => 'DATE',
				),
			);

		} elseif ( 'past' === $show_events ) {

			$meta_query = array(
				array(
					'key'     => '_EventEndDate',
					'compare' => '<',
					'value'   => $now,
					'type'    => 'DATETIME',
				),
			);

		} elseif ( 'future' === $show_events ) {

			$meta_query = array(
				array(
					'key'     => '_EventEndDate',
					'compare' => '>',
					'value'   => $now,
					'type'    => 'DATETIME',
				),
			);

		} elseif ( 'featured' === $show_events ) {

			$meta_query = array(
				array(
					'key'     => '_tribe_featured',
					'compare' => 'EXISTS',
				),
			);

		} else {

			$meta_query = array(
				array(
					'key'     => '_EventStartDate',
					'compare' => 'EXISTS',
				),
			);

		}

		if ( ! isset( $meta_query['relation'] ) ) {
			$meta_query['relation'] = 'AND';
		}

		$meta_query[] = array(
			'key'     => '_EventHideFromUpcoming',
			'compare' => 'NOT EXISTS',
		);

		return $meta_query;
	}

	static public function found_posts( $found_posts, $query ) {
		if ( isset( $query->query ) && isset( $query->query['pp_content_grid'] ) ) {
			return (int) $found_posts - (int) $query->query['pp_original_offset'];
		}

		return $found_posts;
	}

	/**
	 * Get conditional arguments for meta data.
	 *
	 * @param string $type	Type of the meta key.
	 * @param array  $args	WP query args.
	 * @return array
	 */
	static public function get_conditional_args( $type, $args ) {
		switch ( $type ) :
			case 'date':
				$args['orderby'] = 'date ID';
				$args['order'] = 'DESC';
				break;

			case 'price':
				$args['meta_key'] = '_price';
				$args['order'] = 'ASC';
				$args['orderby'] = 'meta_value_num';
				break;

			case 'price-desc':
				$args['meta_key'] = '_price';
				$args['order'] = 'DESC';
				$args['orderby'] = 'meta_value_num';
				break;

			default:
				break;

		endswitch;

		return $args;
	}

	/**
	 * Get taxonomies of a post type.
	 *
	 * @param string $post_type Post type.
	 */
	static public function get_post_taxonomies( $post_type = 'post' ) {
		if ( ! isset( $_POST['post_type'] ) || empty( $_POST['post_type'] ) ) {
			die;
		}

		$post_type = wp_unslash( $_POST['post_type'] );
		$post_type = (array) $post_type;
		$post_type = array_map( 'sanitize_text_field', $post_type );
		$html      = '';

		if ( 'all' === $post_type[0] ) {
			$post_types = FLBuilderLoop::post_types();
			$post_type  = array();

			foreach ( $post_types as $type ) {
				$post_type[] = $type->name;
			}
		}

		foreach ( $post_type as $type ) {
			$object     = get_post_type_object( $type );
			$taxonomies = FLBuilderLoop::taxonomies( $type );

			if ( ! empty( $taxonomies ) ) {
				$html .= '<optgroup label="' . $object->label . '">';
				foreach ( $taxonomies as $key => $tax ) {
					$selected = '';

					if ( isset( $_POST['value'] ) ) {
						$value = wp_unslash( $_POST['value'] );
						$value = (array) $value;

						if ( in_array( $key, $value ) ) {
							$selected = ' selected="selected"';
						}
					}

					$html .= '<option value="' . $key . '" data-post-type="' . $type . '"' . $selected . '>' . $tax->label . ' (' . $tax->name . ')' . '</option>';
				}
				$html .= '</optgroup>';
			}
		}

		echo $html;
		die;
	}

	/**
	 * Get saved templates.
	 *
	 * @since 1.4
	 */
	static public function get_saved_templates() {
		$response = array(
			'success' => false,
			'data'	=> array(),
		);

		$args = array(
			'post_type' 		=> 'fl-builder-template',
			'orderby' 			=> 'title',
			'order' 			=> 'ASC',
			'posts_per_page' 	=> '-1',
		);

		if ( isset( $_POST['type'] ) && ! empty( $_POST['type'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy'		=> 'fl-builder-template-type',
					'field'			=> 'slug',
					'terms'			=> sanitize_text_field( wp_unslash( $_POST['type'] ) ),
				),
			);
		}

		$currentPost = 0;
		if ( isset( $_POST['currentPost'] ) ) {
			$currentPost = absint( $_POST['currentPost'] );
		}

		$posts = get_posts( $args );

		$options = '';

		if ( count( $posts ) ) {
			foreach ( $posts as $post ) {
				if ( $post->ID === $currentPost ) {
					continue;
				}
				$options .= '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
			}

			$response = array(
				'success' => true,
				'data' => $options,
			);
		} else {
			$response = array(
				'success' => true,
				'data' => '<option value="" disabled>' . __( 'No templates found!', 'bb-powerpack' ) . '</option>',
			);
		}

		echo json_encode( $response );
		die;
	}

	static public function modal_dynamic_content() {
		if ( ! isset( $_POST['content'] ) ) {
			wp_send_json_error();
		}
		if ( ! isset( $_POST['postId'] ) ) {
			wp_send_json_error();
		}

		$content = base64_decode( wp_unslash( $_POST['content'] ) );
		$post_id = absint( wp_unslash( $_POST['postId'] ) );

		if ( ! $post_id ) {
			wp_send_json_error();
		}

		wp( 'p=' . $post_id . '&post_status=publish&post_type=' . get_post_type( $post_id ) );

		$GLOBALS['post'] = get_post( $post_id ); // @codingStandardsIgnoreLine

		ob_start();
		if ( is_callable( 'FLThemeBuilderFieldConnections::parse_shortcodes' ) ) {
			$content = FLThemeBuilderFieldConnections::parse_shortcodes( stripslashes( $content ) );
		}
		echo do_shortcode( $content );
		$content = ob_get_clean();

		wp_send_json_success( $content );
	}

	static public function close_notice() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'pp_notice' ) ) {
			wp_send_json_error( esc_html__( 'Action failed. Please refresh the page and retry.', 'bb-powerpack' ) );
		}
		if ( ! isset( $_POST['notice'] ) || empty( $_POST['notice'] ) ) {
			wp_send_json_error( esc_html__( 'Action failed. Please refresh the page and retry.', 'bb-powerpack' ) );
		}

		try {
			update_user_meta( get_current_user_id(), 'bb_powerpack_dismissed_latest_update_notice', true );
			wp_send_json_success();
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}
}

BB_PowerPack_Ajax::init();
