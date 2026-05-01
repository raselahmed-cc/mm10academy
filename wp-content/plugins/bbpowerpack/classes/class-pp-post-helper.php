<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BB_PowerPack_Post_Helper {
	static public $post_slides = array();

	static public function get_template_file( $file_name, $module_slug = '', $path = '' ) {
		$file_name = wp_unslash( trim( $file_name ) );

		if ( empty( trim( $path ) ) && ! empty( $module_slug ) ) {
			$module_slug = wp_unslash( trim( $module_slug ) );
			$dir = pp_get_module_dir( $module_slug );
			if ( file_exists( $dir . 'templates/' . $file_name . '.php' ) ) {
				$path = $dir . 'templates/';
			} else {
				$path = BB_POWERPACK_DIR . "modules/$module_slug/templates/";
			}
		}
		$file = $path . $file_name . '.php';

		return file_exists( $file ) ? $file : false;
	}

	static public function render_template( $file_name, $args = array(), $path = '' ) {
		$file = self::get_template_file( $file_name, $path );
		if ( $file ) {
			if ( ! empty( $args ) && is_array( $args ) ) {
				extract( $args );
			}
			include $file;
		}
	}

	static public function render_post_image( $settings, $post_id, $has_image ) {
		$post_image_settings = self::post_image_get_settings(
			$post_id,
			$settings->image_thumb_crop,
			$settings,
			$has_image
		);

		ob_start();

		FLBuilder::render_module_html(
			'photo',
			$post_image_settings
		);

		$post_image = ob_get_clean();
		
		$css_class = 'pp-post-img';

		if ( isset( $post_image_settings['attributes'] ) && isset( $post_image_settings['attributes']['data-no-lazy'] ) ) {
			// no-lazyload for excluding image from Smush Lazy Load.
			$css_class .= ' no-lazyload';
		}

		echo self::img_inject_css_class( $post_image, $css_class );
	}

	static public function img_inject_css_class( $img, $class ) {
		$pattern = "/<img(.*?)class=\"(.*?)\"(.*?)>/i";
		$replacement = '<img$1class="$2 ' . $class . '"$3>';
		$image = preg_replace( $pattern, $replacement, $img );

		return $image;
	}

	static public function post_catch_image( $content, $size = 'large' ) {
		$first_img = '';
		$id = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );
		if ( isset( $matches[1][0] ) ) {
			$first_img = $matches[1][0];
		}
		// get the image of the given size.
		if ( ! empty( $first_img ) ) {
			$id = attachment_url_to_postid( $first_img );
			if ( $id ) {
				$src = wp_get_attachment_image_src( $id, $size );
				if ( is_array( $src ) ) {
					$first_img = $src[0];
				}
			}
		}

		return array(
			'id' => $id,
			'src' => $first_img
		);
	}

	static public function post_image_get_settings( $id, $crop, $settings, $has_featured_image ) {
		if ( $has_featured_image ) {
			// get image source and data
			$src = self::post_image_get_full_src( $id, $settings );
			$photo_data = self::post_image_get_data( $id );
		} else {
			$has_fallback_image = isset( $settings->fallback_image ) && 'custom' == $settings->fallback_image && ! empty( $settings->fallback_image_custom );
			$src = $has_fallback_image ? self::post_image_get_full_src( $id, $settings, $settings->fallback_image_custom ) : '';
			$src = empty( $src ) && isset( $settings->fallback_image_custom_src ) ? $settings->fallback_image_custom_src : $src;
			$photo_data = self::post_image_get_data( $id, $settings->fallback_image_custom );
		}

		// set params
		$photo_settings = array(
			'crop'          => $crop,
			'link_type'     => '',
			'link_url'      => '',
			'photo'         => $photo_data,
			'photo_src'     => ! empty( $src ) ? $src : apply_filters( 'pp_cg_placeholder_img', BB_POWERPACK_URL . 'assets/images/placeholder.jpg' ),
			'photo_source'  => 'library',
			'attributes'	=> array(
				'data-no-lazy'	=> 1,
			),
		);

		if ( in_array( $settings->more_link_type, array( 'button', 'thumb', 'title_thumb' ) ) ) {
			$photo_settings['link_type'] = 'url';
			if ( isset( $settings->post_permalink ) ) {
				$photo_settings['link_url'] = $settings->post_permalink;
			} else {
				$photo_settings['link_url'] = get_the_permalink( $id );
			}
		}

		if ( isset( $settings->link_target_new ) && 'yes' === $settings->link_target_new ) {
			$photo_settings['link_url_target'] = '_blank';
		}

		return apply_filters( 'pp_post_image_settings_data', $photo_settings, $settings );
	}

	static public function post_image_get_full_src( $id, $settings, $attachment_id = false ) {
		$attachment_id = $attachment_id ? $attachment_id : get_post_thumbnail_id( $id );
		$size = isset( $settings->image_thumb_size ) ? $settings->image_thumb_size : 'medium';
		$img = wp_get_attachment_image_src( $attachment_id, $size );
		return $img[0];
	}

	static protected function post_image_get_data( $id, $attachment_id = false ) {
		$attachment_id = $attachment_id ? $attachment_id : get_post_thumbnail_id( $id );
		return FLBuilderPhoto::get_attachment_data( $attachment_id );
	}

	static public function post_build_array( $settings ) {

		// checks if the post_slides array is cached
		if ( ! is_array( self::$post_slides ) ) {

			// if not, create it
			self::$post_slides = array();

			// check if we have selected posts
			if ( empty( $settings->posts_post ) ) {

				// if not, create a default query with it
				$settings = ! empty( $settings ) ? $settings : new stdClass();
				// set WP_Query "fields" arg as 'ids' to return less information
				$settings->fields = 'ids';

				// Get the query data.
				$query = FLBuilderLoop::query( $settings );

				// build the post_slides array with post id's and featured image url's
				foreach ( $query->posts as $key => $id ) {
					self::$post_slides[ $id ] = self::post_image_get_full_src( $id, $settings );
				}
			} else {

				// if yes, get the selected posts and build the post_slides array
				$slides = explode( ',', $settings->posts_post );

				foreach ( $slides as $key => $id ) {
					self::$post_slides[ $id ] = self::post_image_get_full_src( $id, $settings );
				}
			}
		}

		return self::$post_slides;
	}

	public function post_get_uncropped_url( $id, $settings ) {
		$posts = self::post_build_array( $settings );
		return $posts[ $id ];
	}

	/**
	 * Get post properties and build data for accordions.
	 *
	 * @param object $settings  The module settings object.
	 * @param string $keys  Data keys to map.
	 * @since 2.33.x
	 * @return array
	 */
	static public function get_posts_properties_as_data( $settings, $keys = array() ) {
		$data = array();

		// Relevanssi shows no result on search page when a post contains the
		// module with the $query. So workaround is to return empty array.
		if ( is_search() && function_exists( 'relevanssi_do_query' ) ) {
			return $data;
		}

		$title_key   = isset( $keys['title'] ) ? $keys['title'] : 'title';
		$content_key = isset( $keys['content'] ) ? $keys['content'] : 'content';
		$image_key   = isset( $keys['image'] ) ? $keys['image'] : 'image';

		global $post;
		$initial_current_post = $post;

		$query = FLBuilderLoop::query( $settings );

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();
				$item 			= new stdClass;
				$item->post_id 	= get_the_ID();

				if ( isset( $settings->item_title_source ) && ! empty( trim( $settings->item_title_source ) ) ) {
					$item->{$title_key} = do_shortcode( $settings->item_title_source );
				} else {
					$item->{$title_key} = get_the_title();
				}

				if ( isset( $settings->item_content_source ) && ! empty( trim( $settings->item_content_source ) ) ) {
					$item->{$content_key} = do_shortcode( $settings->item_content_source );
				} else {
					$item->{$content_key} = pp_get_post_content( get_post() );
				}

				if ( isset( $settings->item_image_source ) ) {
					$size = isset( $settings->item_image_size ) ? $settings->item_image_size : 'thumbnail';
					$image = wp_get_attachment_image_src( $settings->item_image_source, $size );
					$default = isset( $settings->item_image_source_src ) ? $settings->item_image_source_src : '';
					$item->{$image_key} = ! empty( $image ) ? $image[0] : $default;
				}

				$data[] = $item;
			}

			wp_reset_query();
			wp_reset_postdata();

			$post = $initial_current_post;
			setup_postdata( $initial_current_post );
		}

		return $data;
	}

	/**
	 * Build base URL for our custom pagination.
	 *
	 * @param string $permalink_structure  The current permalink structure.
	 * @param string $base  The base URL to parse
	 * @since 1.3.1
	 * @return string
	 */
	static public function build_base_url( $permalink_structure, $base ) {
		// Check to see if we are using pretty permalinks
		if ( ! empty( $permalink_structure ) ) {

			if ( strrpos( $base, 'paged-' ) ) {
				$base = substr_replace( $base, '', strrpos( $base, 'paged-' ), strlen( $base ) );
			}

			// Remove query string from base URL since paginate_links() adds it automatically.
			// This should also fix the WPML pagination issue that was added since 1.10.2.
			if ( count( $_GET ) > 0 ) {
				$base = strtok( $base, '?' );
			}

			// Add trailing slash when necessary.
			if ( '/' == substr( $permalink_structure, -1 ) ) {
				$base = trailingslashit( $base );
			} else {
				$base = untrailingslashit( $base );
			}
		} else {
			$url_params = wp_parse_url( $base, PHP_URL_QUERY );

			if ( empty( $url_params ) ) {
				$base = trailingslashit( $base );
			}
		}

		return $base;
	}

	/**
	 * Build the custom pagination format.
	 *
	 * @param string $permalink_structure
	 * @param string $base
	 * @since 1.3.1
	 * @return string
	 */
	static public function paged_format( $permalink_structure, $base ) {
		if ( FLBuilderLoop::$loop_counter > 1 ) {
			$page_prefix = 'paged-' . FLBuilderLoop::$loop_counter;
		} else {
			$page_prefix = empty( $permalink_structure ) ? 'paged' : 'page';
		}

		if ( ! empty( $permalink_structure ) ) {
			$format = substr( $base, -1 ) != '/' ? '/' : '';
			$format .= $page_prefix . '/';
			$format .= '%#%';
			$format .= substr( $permalink_structure, -1 ) == '/' ? '/' : '';
		} elseif ( empty( $permalink_structure ) || is_search() ) {
			$parse_url = wp_parse_url( $base, PHP_URL_QUERY );
			$format = empty( $parse_url ) ? '?' : '&';
			$format .= $page_prefix . '=%#%';
		}

		return $format;
	}

	static public function pagination( $query, $settings ) {
		$total               = 0;
		$page                = 0;
		$paged               = FLBuilderLoop::get_paged();
		$per_page            = 'main_query' !== $settings->data_source ? absint( $settings->posts_per_page ) : get_option( 'posts_per_page' );
		$total_posts_count   = absint( $settings->total_posts_count );
		$posts_aval          = $query->found_posts;
		$permalink_structure = get_option( 'permalink_structure' );
		$base                = html_entity_decode( get_pagenum_link() );

		if ( ! $per_page && 'main_query' !== $settings->data_source ) {
			$per_page = get_option( 'posts_per_page' );
		}

		if ( 'custom' == $settings->total_post && $total_posts_count && $total_posts_count != $posts_aval ) {

			if ( $total_posts_count > $posts_aval ) {
				$page = $posts_aval / $per_page;
				$total = $posts_aval % $per_page;
			}
			if ( $total_posts_count < $posts_aval ) {
				$page = $total_posts_count / $per_page;
				$total = $total_posts_count % $per_page;
			}

			if ( $total > 0 ) {
				$page = $page + 1;
			}
		} else {
			$page = $query->max_num_pages;
			//FLBuilderLoop::pagination($query);
		}

		if ( $page > 1 ) {
			if ( ! $current_page = $paged ) { // @codingStandardsIgnoreLine
				$current_page = 1;
			}

			$base = self::build_base_url( $permalink_structure, $base );
			$format = self::paged_format( $permalink_structure, $base );

			$paginate_links_args = array(
				'base'	   => $base . '%_%',
				'format'   => $format,
				'current'  => $current_page,
				'total'	   => $page,
				'type'	   => 'list',
			);

			if ( 'load_more' === $settings->pagination || 'scroll' === $settings->pagination ) {
				$paginate_links_args['prev_next'] = false;
				$paginate_links_args['show_all'] = true;
			}

			$links = paginate_links( $paginate_links_args );

			if ( isset( $settings->pagination_nofollow ) && 'yes' == $settings->pagination_nofollow ) {
				$links = str_replace( '<a', '<a rel="nofollow" ', $links );
			}

			echo $links;
		}
	}

	/**
	 * Build pagination.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	static public function ajax_pagination( $query, $settings, $current_url = '', $paged = 1, $filter = '', $node_id = '' ) {
		$total_pages         = $query->max_num_pages;
		$per_page            = 'main_query' !== $settings->data_source ? absint( $settings->posts_per_page ) : get_option( 'posts_per_page' );
		$total_posts_count   = absint( $settings->total_posts_count );
		$permalink_structure = get_option( 'permalink_structure' );
		$current_url         = empty( $current_url ) ? get_pagenum_link() : $current_url;
		$base                = untrailingslashit( html_entity_decode( $current_url ) );

		if ( ! $per_page && 'main_query' !== $settings->data_source ) {
			$per_page = get_option( 'posts_per_page' );
		}

		if ( 'custom' == $settings->total_post && ! empty( $total_posts_count ) && $total_posts_count != $query->found_posts ) {
			$total = 0;

			if ( $total_posts_count > $query->found_posts ) {
				$total_pages = $query->found_posts / $per_page;
				$total = $query->found_posts % $per_page;
			}
			if ( $total_posts_count < $query->found_posts ) {
				$total_pages = $total_posts_count / $per_page;
				$total = $total_posts_count % $per_page;
			}

			if ( $total > 0 ) {
				$total_pages = $total_pages + 1;
			}
		}

		if ( $total_pages > 1 ) {

			if ( ! $current_page = $paged ) { // @codingStandardsIgnoreLine
				$current_page = 1;
			}

			$base = FLBuilderLoop::build_base_url( $permalink_structure, $base );
			$format = FLBuilderLoop::paged_format( $permalink_structure, $base );

			if ( '' != $filter ) {
				$format .= '?filter_term=' . $filter . '&node_id=' . $node_id;
			}

			$paginate_links_args = array(
				'base'	   => $base . '%_%',
				'format'   => $format,
				'current'  => $current_page,
				'total'	   => $total_pages,
				'type'	   => 'list',
			);

			if ( 'load_more' === $settings->pagination || 'scroll' === $settings->pagination ) {
				$paginate_links_args['prev_next'] = false;
				$paginate_links_args['show_all'] = true;
			}

			$links = paginate_links( $paginate_links_args );

			if ( isset( $settings->pagination_nofollow ) && 'yes' == $settings->pagination_nofollow ) {
				$links = str_replace( '<a', '<a rel="nofollow" ', $links );
			}

			echo $links;
		}
	}

	/**
	 * Renders the schema structured data for the current
	 * post in the loop.
	 *
	 * @return void
	 */
	static public function schema_meta() {
		/**
		 * Disable all post-grid schema markup
		 * @see pp_post_disable_schema
		 */
		if ( ! self::is_schema_enabled() ) {
			return false;
		}

		do_action( 'pp_post_before_schema_meta' );

		// General Schema Meta
		ob_start();
		echo '<meta itemscope itemprop="mainEntityOfPage" itemtype="https://schema.org/WebPage" itemid="' . esc_url( get_the_ID() ) . '" content="' . the_title_attribute( array(
			'echo' => false,
		) ) . '" />';
		echo '<meta itemprop="datePublished" content="' . get_the_time( 'Y-m-d' ) . '" />';
		echo '<meta itemprop="dateModified" content="' . get_the_modified_date( 'Y-m-d' ) . '" />';
		echo apply_filters( 'pp_post_schema_meta_general', ob_get_clean() );

		// Publisher Schema Meta
		ob_start();
		echo '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
		echo '<meta itemprop="name" content="' . get_bloginfo( 'name' ) . '">';

		// Fetch logo from theme or filter.
		$image = '';
		if ( class_exists( 'FLTheme' ) && 'image' == FLTheme::get_setting( 'fl-logo-type' ) ) {
			$image = FLTheme::get_setting( 'fl-logo-image' );
		} elseif ( has_custom_logo() ) {
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			$logo           = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			$image          = $logo[0];
		}
		$image = apply_filters( 'pp_post_schema_meta_publisher_image_url', $image );
		if ( $image ) {
			echo '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
			echo '<meta itemprop="url" content="' . $image . '">';
			echo '</div>';
		}

		echo '</div>';
		echo apply_filters( 'pp_post_schema_meta_publisher', ob_get_clean() );

		// Author Schema Meta
		ob_start();
		echo '<div itemscope itemprop="author" itemtype="https://schema.org/Person">';
		echo '<meta itemprop="url" content="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" />';
		echo '<meta itemprop="name" content="' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '" />';
		echo '</div>';
		echo apply_filters( 'pp_post_schema_meta_author', ob_get_clean() );

		// Image Schema Meta
		if ( has_post_thumbnail() ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

			if ( is_array( $image ) ) {
				echo '<div itemscope itemprop="image" itemtype="https://schema.org/ImageObject">';
				echo '<meta itemprop="url" content="' . $image[0] . '" />';
				echo '<meta itemprop="width" content="' . $image[1] . '" />';
				echo '<meta itemprop="height" content="' . $image[2] . '" />';
				echo '</div>';
			}
		}

		// Comment Schema Meta
		ob_start();
		echo '<div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter">';
		echo '<meta itemprop="interactionType" content="https://schema.org/CommentAction" />';
		echo '<meta itemprop="userInteractionCount" content="' . wp_count_comments( get_the_ID() )->approved . '" />';
		echo '</div>';
		echo apply_filters( 'pp_post_schema_meta_comments', ob_get_clean() );

		do_action( 'pp_post_after_schema_meta' );
	}

	/**
	 * Renders the schema itemtype for the current
	 * post in the loop.
	 *
	 * @return void
	 */
	static public function schema_itemtype() {
		global $post;

		if ( ! self::is_schema_enabled() ) {
			return false;
		}

		$schema = 'https://schema.org/BlogPosting';
		if ( ! is_object( $post ) || ! isset( $post->post_type ) || 'post' != $post->post_type ) {
			$schema = 'https://schema.org/CreativeWork';
		}

		return $schema;
	}

	/**
	 * Renders the schema itemtype for the collection
	 *
	 * @return string
	 */
	static public function schema_collection_type( $data_source = 'custom_query', $post_type = 'post' ) {
		$schema = '';
		$post_type = (array) $post_type;

		if ( ! self::is_schema_enabled() ) {
			return $schema;
		}

		if ( is_archive() && 'main_query' === $data_source ) {
			$schema = is_post_type_archive( 'post' ) ? 'https://schema.org/Blog' : 'https://schema.org/Collection';
		} else {
			$schema = ( in_array( 'post', $post_type ) ) ? 'https://schema.org/Blog' : 'https://schema.org/Collection';
		}

		return $schema;
	}

	static public function print_schema( $schema ) {
		if ( self::is_schema_enabled() ) {
			echo $schema;
		}
	}

	static public function is_schema_enabled() {

		/**
		 * Disable all schema.
		 * @see pp_post_disable_schema
		 */
		if ( false !== apply_filters( 'pp_post_disable_schema', false ) ) {
			return false;
		} else {
			return true;
		}
	}	

	static public function additional_image_sizes( $attrs ) {
		if ( ! function_exists( 'wp_get_additional_image_sizes' ) ) {
			return $attrs;
		}

		// Get an array of custom image sizes
		$allImageSizes = wp_get_additional_image_sizes();
		$imageSizes = [];
	
		// Loop through the custom images, make a suitable array
		foreach ( $allImageSizes as $key => $value ) {
			$imageSizes[ $key ] = $key;
		}
	
		// Merge the new array with the existing, and return
		$attrs = array_merge( $attrs, $imageSizes );
		return $attrs;
	}
}
