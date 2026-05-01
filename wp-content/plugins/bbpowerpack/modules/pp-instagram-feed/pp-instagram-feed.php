<?php

/**
 * @class PPInstagramFeedModule
 */
class PPInstagramFeedModule extends FLBuilderModule {
	/**
	 * Official Instagram API URL.
	 *
	 * @since 2.14
	 * @var   string
	 */
	private $instagram_api_url = 'https://graph.instagram.com/';
	private $instagram_url = 'https://www.instagram.com/';
	private $access_token = null;

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          	=> __( 'Instagram Feed', 'bb-powerpack' ),
			'description'   	=> __( 'A module to display feed from Instagram.', 'bb-powerpack' ),
			'group'         	=> pp_get_modules_group(),
			'category'			=> pp_get_modules_cat( 'social' ),
			'dir'           	=> BB_POWERPACK_DIR . 'modules/pp-instagram-feed/',
			'url'           	=> BB_POWERPACK_URL . 'modules/pp-instagram-feed/',
			'editor_export' 	=> true, // Defaults to true and can be omitted.
			'enabled'       	=> true, // Defaults to true and can be omitted.
		));

		$this->access_token = $this->get_access_token();
	}

	public function enqueue_scripts() {
		$this->add_js( 'jquery-magnificpopup' );
		$this->add_css( 'jquery-magnificpopup' );

		$this->add_js( 'imagesloaded' );

		$is_builder_active = FLBuilderModel::is_builder_active();

		if ( $is_builder_active || ( isset( $this->settings ) && isset( $this->settings->feed_layout ) && 'carousel' === $this->settings->feed_layout ) ) {
			$this->add_css( 'jquery-swiper' );
			$this->add_js( 'jquery-swiper' );
		}
		if ( $is_builder_active || ( isset( $this->settings ) && isset( $this->settings->feed_layout ) && 'grid' === $this->settings->feed_layout ) ) {
			$this->add_js( 'jquery-masonry' );
		}
	}

	public function enqueue_icon_styles() {
		$enqueue = false;
		$settings = $this->settings;

		if ( 'yes' === $settings->profile_link && ! empty( $settings->insta_title_icon ) ) {
			$enqueue = true;
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function filter_settings( $settings, $helper )
	{
		// Handle title's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'feed_title_font'	=> array(
				'type'			=> 'font'
			),
			'feed_title_custom_font_size'	=> array(
				'type'			=> 'font_size',
				'condition'		=> ( isset( $settings->feed_title_font_size ) && 'custom' == $settings->feed_title_font_size )
			),
			'feed_title_line_height'	=> array(
				'type'			=> 'line_height',
			),
			'feed_title_transform'	=> array(
				'type'			=> 'text_transform',
			),
			'feed_title_letter_spacing'	=> array(
				'type'			=> 'letter_spacing',
			),
		), 'title_typography' );

		// Handle old title border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'feed_title_border'	=> array(
				'type'				=> 'style'
			),
			'feed_title_border_width'	=> array(
				'type'				=> 'width'
			),
			'feed_title_border_color'	=> array(
				'type'				=> 'color'
			),
			'feed_title_border_radius'	=> array(
				'type'				=> 'radius'
			),
		), 'feed_title_border_group' );

		// Handle Overlay Bg Hover color field.
		if ( isset( $settings->image_overlay_opacity ) ) {
			$opacity    = '' === $settings->image_overlay_opacity ? 1 : $settings->image_overlay_opacity;
			$overlay_color = $settings->image_overlay_color;

			if ( ! empty( $overlay_color ) ) {
				$overlay_color              = pp_hex2rgba( pp_get_color_value( $overlay_color ), ( $opacity / 100 ) );
				$settings->image_overlay_color = $overlay_color;
			}

			unset( $settings->image_overlay_opacity );
		}

		// Handle Overlay Bg Hover color field.
		if ( isset( $settings->image_hover_overlay_opacity ) ) {
			$opacity    = '' === $settings->image_hover_overlay_opacity ? 1 : $settings->image_hover_overlay_opacity;
			$overlay_h_color = $settings->image_hover_overlay_color;

			if ( ! empty( $overlay_h_color ) ) {
				$overlay_h_color              = pp_hex2rgba( pp_get_color_value( $overlay_h_color ), ( $opacity / 100 ) );
				$settings->image_hover_overlay_color = $overlay_h_color;
			}

			unset( $settings->image_hover_overlay_opacity );
		}

		if ( ! isset( $settings->feed_by_tags ) || 'no' !== $settings->feed_by_tags ) {
			$settings->feed_by_tags = 'no';
			$settings->tag_name = '';
		}

		return $settings;
	}

	/**
	 * Retrieve a URL for photos by hashtag.
	 *
	 * @since  2.14
	 * @return string
	 */
	public function get_tags_endpoint() {
		return $this->instagram_url . 'graphql/query/?query_hash=298b92c8d7cad703f7565aa892ede943&variables={"tag_name":"%s","first":12,"after":"XXXXXXXX"}';
	}

	/**
	 * Retrieve a URL for own photos.
	 *
	 * @since  2.14
	 * @return string
	 */
	public function get_feed_endpoint() {
		return $this->instagram_api_url . 'me/media/';
	}

	public function get_user_endpoint() {
		return $this->instagram_api_url . 'me/';
	}

	public function get_user_media_endpoint() {
		return $this->instagram_api_url . '%s/media/';
	}

	public function get_media_endpoint() {
		return $this->instagram_api_url . '%s/';
	}

	public function get_user_url() {
		$url = $this->get_user_endpoint();
		$url = add_query_arg( array(
			'access_token' => $this->get_access_token(),
			// 'fields' => 'media.limit(10){comments_count,like_count,likes,likes_count,media_url,permalink,caption}',
		), $url );

		return $url;
	}

	public function get_user_media_url( $user_id ) {
		$url = sprintf( $this->get_user_media_endpoint(), $user_id );
		$url = add_query_arg( array(
			'access_token' => $this->get_access_token(),
			'fields' => 'id,like_count',
		), $url );

		return $url;
	}

	public function get_media_url( $media_id ) {
		$url = sprintf( $this->get_media_endpoint(), $media_id );
		$url = add_query_arg( array(
			'access_token' => $this->get_access_token(),
			'fields' => 'id,media_type,media_url,timestamp,like_count',
		), $url );

		return $url;
	}

	public function get_insta_user_id() {
		$result = $this->get_api_response( $this->get_user_url() );
		return $result;
	}

	public function get_insta_user_media( $user_id ) {
		$result = $this->get_api_response( $this->get_user_media_url( $user_id ) );

		return $result;
	}

	public function get_insta_media( $media_id ) {
		$result = $this->get_api_response( $this->get_media_url( $media_id ) );

		return $result;
	}

	/**
	 * Endpoint URL.
	 *
	 * @since  2.14
	 * @return string
	 */
	public function get_endpoint_url() {
		$settings = $this->settings;
		$images_count = ! empty( $settings->images_count ) ? $settings->images_count : 8;

		if ( 'yes' === $settings->feed_by_tags ) {
			$url = sprintf( $this->get_tags_endpoint(), $settings->tag_name );
			$url = add_query_arg( array( '__a' => 1 ), $url );

		} else {
			$url = $this->get_feed_endpoint();
			$url = add_query_arg( array(
				'fields'       => 'id,media_type,media_url,thumbnail_url,permalink,caption,timestamp,children%7Bmedia_url,id,media_type,timestamp,permalink,thumbnail_url%7D',
				'access_token' => $this->get_access_token(),
				'limit'        => $images_count,
			), $url );
		}

		return $url;
	}

	/**
	 * Get data from response
	 *
	 * @param  $response
	 * @since  2.14
	 *
	 * @return array
	 */
	public function get_insta_feed_response_data( $response ) {
		$settings = $this->settings;

		if ( ! array_key_exists( 'data', $response ) ) { // Avoid PHP notices
			return;
		}

		$response_posts = $response['data'];

		if ( empty( $response_posts ) ) {
			return array();
		}

		$return_data  = array();
		$images_count = ! empty( $settings->images_count ) ? $settings->images_count : 8;
		$posts = array_slice( $response_posts, 0, $images_count, true );

		foreach ( $posts as $post ) {
			$_post              = array();

			$_post['id']        = $post['id'];
			$_post['link']      = $post['permalink'];
			$_post['caption']   = $this->get_caption( $post );
			$_post['image']     = 'VIDEO' === $post['media_type'] ? $post['thumbnail_url'] : $post['media_url'];
			$_post['comments']  = ! empty( $post['comments_count'] ) ? $post['comments_count'] : 0;
			$_post['likes']     = ! empty( $post['likes_count'] ) ? $post['likes_count'] : 0;

			$_post['thumbnail'] = $this->get_insta_feed_thumbnail_data( $post );
			$_post['time']		= iso8601_to_datetime( $post['timestamp'] );

			if ( $_post['time'] ) {
				$_post['time'] = strtotime( $_post['time'] );
			}

			// Fallback for the first image in carousel album children if the main image is not set.
			if ( empty( $_post['image'] ) && $post['media_type'] === 'CAROUSEL_ALBUM' && ! empty( $post['children']['data'][0]['media_url'] ) ) {
				$_post['image'] = $post['children']['data'][0]['media_url'];
			}

			// if ( ! empty( $post['caption'] ) ) {
			// 	$_post['caption'] = wp_html_excerpt( $post['caption'], $settings->caption_length, '&hellip;' );
			// }

			$return_data[ $_post['time'] ] = $_post;
		}

		return $return_data;
	}

	/**
	 * Get thumbnail data from API response.
	 *
	 * @param array $data
	 * @since 2.14
	 *
	 * @return array
	 */
	public function get_insta_feed_thumbnail_data( $data ) {
		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'      => false,
		);

		if ( ! empty( $data['images'] ) && is_array( $data['images'] ) ) {
			$data = $data['images'];

			$thumbnail['thumbnail'] = array(
				'src'           => $data['thumbnail']['url'],
				'config_width'  => $data['thumbnail']['width'],
				'config_height' => $data['thumbnail']['height'],
			);

			$thumbnail['low'] = array(
				'src'           => $data['low_resolution']['url'],
				'config_width'  => $data['low_resolution']['width'],
				'config_height' => $data['low_resolution']['height'],
			);

			$thumbnail['standard'] = array(
				'src'           => $data['standard_resolution']['url'],
				'config_width'  => $data['standard_resolution']['width'],
				'config_height' => $data['standard_resolution']['height'],
			);
		}

		return $thumbnail;
	}

	/**
	 * Get data from response
	 *
	 * @param  array $response
	 * @since  2.14
	 *
	 * @return array
	 */
	public function get_insta_tags_response_data( $response ) {
		$settings = $this->settings;
		$data = isset( $response['graphql'] ) ? $response['graphql'] : $response['data'];
		$response_posts = $data['hashtag']['edge_hashtag_to_media']['edges'];

		if ( empty( $response_posts ) ) {
			$response_posts = $data['hashtag']['edge_hashtag_to_top_posts']['edges'];
		}

		$return_data  = array();
		$images_count = ! empty( $settings->images_count ) ? $settings->images_count : 8;
		$posts = array_slice( $response_posts, 0, $images_count, true );

		foreach ( $posts as $post ) {
			$_post              = array();

			$_post['link']      = sprintf( $this->instagram_api_url . 'p/%s/', $post['node']['shortcode'] );
			$_post['caption']   = '';
			$_post['comments']  = $post['node']['edge_media_to_comment']['count'];
			$_post['likes']     = $post['node']['edge_liked_by']['count'];
			$_post['thumbnail'] = $this->get_insta_tags_thumbnail_data( $post );

			if ( isset( $post['node']['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
				//$caption_length = isset( $settings->caption_length ) ? $settings->caption_length : 30;
				//$_post['caption'] = wp_html_excerpt( $post['node']['edge_media_to_caption']['edges'][0]['node']['text'], $caption_length, '&hellip;' );
				$_post['caption'] = $post['node']['edge_media_to_caption']['edges'][0]['node']['text'];
			}

			$return_data[] = $_post;
		}

		return $return_data;
	}

	/**
	 * Generate thumbnail resources.
	 *
	 * @since 2.14
	 * @param array $data
	 *
	 * @return array
	 */
	public function get_insta_tags_thumbnail_data( $data ) {
		$data = $data['node'];

		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'		=> false,
		);

		if ( is_array( $data['thumbnail_resources'] ) && ! empty( $data['thumbnail_resources'] ) ) {
			foreach ( $data['thumbnail_resources'] as $key => $resources_data ) {

				if ( 150 === $resources_data['config_width'] ) {
					$thumbnail['thumbnail'] = $resources_data;
					continue;
				}

				if ( 320 === $resources_data['config_width'] ) {
					$thumbnail['low'] = $resources_data;
					continue;
				}

				if ( 640 === $resources_data['config_width'] ) {
					$thumbnail['standard'] = $resources_data;
					continue;
				}
			}
		}

		if ( ! empty( $data['display_url'] ) ) {
			$thumbnail['high'] = array(
				'src'           => $data['display_url'],
				'config_width'  => $data['dimensions']['width'],
				'config_height' => $data['dimensions']['height'],
			);
		}

		return $thumbnail;
	}

	/**
	 * Retrieve response from API
	 *
	 * @since  2.14
	 * @return array|WP_Error
	 */
	private function get_api_response( $url ) {
		$response       = wp_remote_get( $url, array(
			'timeout'   => 60,
			'sslverify' => false,
		) );

		$response_code  = wp_remote_retrieve_response_code( $response );
		$result         = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $response_code ) {
			$message = is_array( $result ) && isset( $result['error']['message'] ) ? $result['error']['message'] : __( 'No posts found', 'bb-powerpack' );

			return new \WP_Error( $response_code, $message );
		}

		if ( ! is_array( $result ) ) {
			return new \WP_Error( 'error', __( 'Data Error', 'bb-powerpack' ) );
		}

		return $result;
	}

	/**
	 * Retrieve Instagram posts.
	 *
	 * @since  2.14
	 * @param  array $settings
	 * @return array
	 */
	public function get_insta_posts() {
		$settings = $this->settings;

		$transient_key = md5( $this->get_transient_key() );

		$data = get_transient( $transient_key );

		$cache_duration = pp_get_instagram_cache_duration();

		if ( ! empty( $data ) && 'none' !== $cache_duration ) {
			return $this->get_sorted_data( $data );
		}

		// $user = $this->get_insta_user_id();
		// $user_media = $this->get_insta_user_media( $user['id'] );

		// foreach( $user_media['data'] as $media ) {
		// 	$media_object = $this->get_insta_media( $media['id'] );
		// }

		$response = $this->get_api_response( $this->get_endpoint_url() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$data = ( 'yes' === $settings->feed_by_tags ) ? $this->get_insta_tags_response_data( $response ) : $this->get_insta_feed_response_data( $response );

		if ( empty( $data ) ) {
			return array();
		}

		set_transient( $transient_key, $data, $this->get_cache_duration() );

		return $this->get_sorted_data( $data );
	}

	public function get_sorted_data( $data ) {
		$settings = $this->settings;
		$data = (array) $data;

		if ( 'least-recent' === $settings->sort_by ) {
			ksort( $data, 1 );
		} elseif ( 'most-recent' === $settings->sort_by ) {
			krsort( $data, 1 );
		} elseif ( 'random' === $settings->sort_by ) {
			shuffle( $data );
		}

		return $data;
	}

	/**
	 * Get transient key.
	 *
	 * @since  2.14
	 * @return string
	 */
	private function get_transient_key() {
		$settings = $this->settings;

		$endpoint = 'yes' === $settings->feed_by_tags ? 'tags' : 'feed';
		$target = ( 'tags' === $endpoint ) ? sanitize_text_field( $settings->tag_name ) : 'users';
		$images_count = $settings->images_count;

		return sprintf( 'ppe_instagram_%s_%s_posts_count_%s_token_',
			$endpoint,
			$target,
			$images_count,
			$this->access_token
		);
	}

	private function get_cache_duration() {
		$cache_duration = pp_get_instagram_cache_duration();
		$duration = 0;

		switch ( $cache_duration ) {
			case 'minute':
				$duration = MINUTE_IN_SECONDS;
				break;
			case 'hour':
				$duration = HOUR_IN_SECONDS;
				break;
			case 'day':
				$duration = DAY_IN_SECONDS;
				break;
			case 'week':
				$duration = WEEK_IN_SECONDS;
				break;
			default:
				break;
		}

		return $duration;
	}

	private function get_access_token() {
		if ( empty( $this->access_token ) ) {
			$this->access_token = pp_get_instagram_token();
		}

		return $this->access_token;
	}

	/**
	 * Get Insta Thumbnail Image URL
	 *
	 * @since  2.14
	 * @return string   The resolution of image size.
	 */
	public function get_insta_image_size() {
		$settings = $this->settings;

		$size = $settings->image_resolution;

		switch ( $size ) {
			case 'thumbnail':
				return 'thumbnail';
			case 'low_resolution':
				return 'low';
			case 'standard_resolution':
				return 'standard';
			default:
				return 'low';
		}
	}

	/**
	 * Get Insta Thumbnail Image URL
	 *
	 * @since  2.14
	 * @return string   The url of the instagram post image
	 */
	public function get_insta_image_url( $item, $size = 'standard' ) {
		$thumbnail  = $item['thumbnail'];

		if ( ! empty( $thumbnail[ $size ] ) ) {
			$image_url = $thumbnail[ $size ]['src'];
		} else {
			$image_url = isset( $item['image'] ) ? $item['image'] : '';
		}

		return $this->parse_insta_image_url( $image_url );
	}

	private function parse_insta_image_url( $image_url ) {
		if ( 'yes' === $this->settings->feed_by_tags ) {
			// convert to base64 to prevent CORS issue from Instagram.
			$response = wp_remote_fopen( $image_url );
			if ( $response ) {
				$image_url = 'data:image/jpg;base64,' . base64_encode( $response );
			}
		}

		return $image_url;
	}

	public function get_caption( $item, $default = '' ) {
		$caption = $default;
		if ( ! isset( $item['caption'] ) ) {
			return $caption;
		}
		if ( ! is_array( $item['caption'] ) && ! empty( $item['caption'] ) ) {
			$caption = $item['caption'];
		} elseif ( ! empty( $item['caption']['text'] ) ) {
			$caption = $item['caption']['text'];
		}

		return $caption;
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module('PPInstagramFeedModule', array(
	'general'       => array( // Tab
		'title'         => __( 'General', 'bb-powerpack' ), // Tab title
		'description' => empty( pp_get_instagram_token() ) ? 
			sprintf(
				__( 'Your Instagram Access Token is missing, %1$sclick here%2$s to configure.', 'bb-powerpack' ),
				'<a href="' . BB_PowerPack_Admin_Settings::get_form_action( '&tab=integration' ) . '"><strong>',
				'</strong></a>' ) :
			'',
		'sections'      => array( // Tab Sections
			'feed_settings'	=> array(
				'title'			=> '',
				'collapsed'		=> false,
				'fields'        => array(
					'images_count'		=> array(
						'type'          => 'unit',
						'label'         => __( 'Max Images Count', 'bb-powerpack' ),
						'default'       => '12',
						'slider'        => true,
					),
					'image_resolution'  => array(
						'type'          => 'select',
						'label'         => __( 'Image Resolution', 'bb-powerpack' ),
						'default'       => 'standard_resolution',
						'options'       => array(
							'thumbnail'             => __( 'Thumbnail', 'bb-powerpack' ),
							'low_resolution'        => __( 'Low Resolution', 'bb-powerpack' ),
							'standard_resolution'   => __( 'Standard Resolution', 'bb-powerpack' ),
						),
					),
					'sort_by'	=> array(
						'type'			=> 'select',
						'label'         => __( 'Sort By', 'bb-powerpack' ),
						'default'       => 'none',
						'options'       => array(
							'none'              => __( 'None', 'bb-powerpack' ),
							'most-recent'       => __( 'Most Recent', 'bb-powerpack' ),
							'least-recent'      => __( 'Least Recent', 'bb-powerpack' ),
							'random'            => __( 'Random', 'bb-powerpack' ),
						),
					),
				),
			),
			'layout'	=> array(
				'title'		=> __( 'Layout', 'bb-powerpack' ),
				'collapsed'		=> true,
				'fields'    => array(
					'feed_layout'  => array(
						'type'          => 'select',
						'label'         => __( 'Layout', 'bb-powerpack' ),
						'default'       => 'grid',
						'options'       => array(
							'grid'           => __( 'Masonry', 'bb-powerpack' ),
							'square-grid'    => __( 'Columns', 'bb-powerpack' ),
							'carousel'       => __( 'Carousel', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'grid'  => array(
								'fields'    => array( 'grid_columns', 'spacing' ),
							),
							'square-grid'  => array(
								'fields'    => array( 'grid_columns', 'spacing', 'image_custom_size' ),
							),
							'carousel'  => array(
								'tabs'		=> array( 'carousel' ),
								'fields'	=> array( 'image_custom_size' ),
							),
						),
					),
					'image_custom_size'		=> array(
						'type'			=> 'unit',
						'label' 		=> __( 'Custom Height', 'bb-powerpack' ),
						'default'       => '',
						'units'			=> array('px'),
						'slider'		=> array(
							'min'			=> '150',
							'max'			=> '1000',
							'step'			=> '1',
						),
						'responsive' 	=> array(
							'placeholder'	=> array(
								'default'		=> '',
								'medium'		=> '',
								'responsive' 	=> '',
							),
						),
					),
					'aspect_ratio' => array(
						'type' => 'pp-switch',
						'label' => __( 'Maintain Aspect Ratio', 'bb-powerpack' ),
						'default' => 'yes',
						'help' => __( 'If you want to keep the item square, set this option to No.', 'bb-powerpack' ),
					),
					'grid_columns'	=> array(
						'type'			=> 'unit',
						'label' 		=> __( 'Columns', 'bb-powerpack' ),
						'slider'        => true,
						'default'       => '3',
						'responsive' 	=> array(
							'placeholder'	=> array(
								'default'		=> '3',
								'medium'		=> '2',
								'responsive' 	=> '1',
							),
						),
					),
					'spacing' => array(
						'type' 			=> 'unit',
						'label' 		=> __('Spacing', 'bb-powerpack'),
						'default'		=> '',
						'units'			=> array( 'px' ),
						'slider'        => true,
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
					),
					// 'likes'	=> array(
					// 	'type'		=> 'pp-switch',
					// 	'label'     => __( 'Show Likes Count', 'bb-powerpack' ),
					// 	'default'   => 'no',
					// 	'options'   => array(
					// 		'yes'		=> __( 'Yes', 'bb-powerpack' ),
					// 		'no'		=> __( 'No', 'bb-powerpack' ),
					// 	),
					// ),
					// 'comments'	=> array(
					// 	'type'		=> 'pp-switch',
					// 	'label'     => __( 'Show Comments Count', 'bb-powerpack' ),
					// 	'default'  	=> 'no',
					// 	'options'   => array(
					// 		'yes'		=> __( 'Yes', 'bb-powerpack' ),
					// 		'no'		=> __( 'No', 'bb-powerpack' ),
					// 	),
					// ),
					// 'content_visibility'  => array(
					// 	'type'          => 'pp-switch',
					// 	'label'         => __( 'Content Visibility', 'bb-powerpack' ),
					// 	'default'       => 'always',
					// 	'options'       => array(
					// 		'always'		=> __( 'Always', 'bb-powerpack' ),
					// 		'hover'         => __( 'Hover', 'bb-powerpack' ),
					// 	),
					// ),
				),
			),
			'additional' => array(
				'title' => __( 'Additional Options', 'bb-powerpack' ),
				'collapsed' => true,
				'fields' => array(
					'image_popup'  => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Image Link Type', 'bb-powerpack' ),
						'default'       => 'no',
						'options'       => array(
							'no'            => __( 'None', 'bb-powerpack' ),
							'yes'           => __( 'Popup', 'bb-powerpack' ),
							'link'			=> __( 'Link', 'bb-powerpack' )
						),
					),
					'profile_link'  => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Show Link to Instagram Profile?', 'bb-powerpack' ),
						'default'       => 'no',
						'options'       => array(
							'yes'           => __( 'Yes', 'bb-powerpack' ),
							'no'            => __( 'No', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'yes'		=> array(
								'tabs'		=> array( 'typography' ),
								'sections'	=> array( 'feed_title' ),
								'fields'	=> array( 'insta_link_title', 'insta_profile_url', 'insta_title_icon', 'insta_title_icon_position' ),
							),
						),
					),
					'insta_link_title'	=> array(
						'type'				=> 'text',
						'label'         	=> __( 'Link Text', 'bb-powerpack' ),
						'default'       	=> __( 'Follow @example on Instagram', 'bb-powerpack' ),
						'connections'		=> array('string')
					),
					'insta_profile_url'	=> array(
						'type'          	=> 'link',
						'label'         	=> __( 'Instagram Profile URL', 'bb-powerpack' ),
						'connections'		=> array( 'url' ),
						'preview'       	=> array(
							'type'      	=> 'none',
						),
					),
					'insta_title_icon'  => array(
						'type'          	=> 'icon',
						'label'         	=> __( 'Icon', 'bb-powerpack' ),
						'preview'			=> 'none',
						'show_remove' 		=> true,
					),
					'insta_title_icon_position'  => array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Icon Position', 'bb-powerpack' ),
						'default'       => 'before_title',
						'options'       => array(
							'before_title'		=> __( 'Before Title', 'bb-powerpack' ),
							'after_title'       => __( 'After Title', 'bb-powerpack' ),
						),
					),
				),
			),
		),
	),
	'carousel'  => array(
		'title'     => __( 'Carousel', 'bb-powerpack' ),
		'sections'  => array(
			'carousel_settings'     => array(
				'title'     => __( 'Image', 'bb-powerpack' ),
				'fields'    => array(
					'visible_items'		=> array(
						'type' 				=> 'unit',
						'label' 			=> __( 'Visible Items', 'bb-powerpack' ),
						'help'				=> __( 'Leave blank if you are trying to display full viewport width carousel. But make sure you have entered the Custom Height under Layout section.', 'bb-powerpack' ),
						'default'       	=> '3',
						'responsive' 		=> true,
					),
					'images_gap'     => array(
						'type' 			=> 'unit',
						'label' 		=> __( 'Items Spacing', 'bb-powerpack' ),
						'default'       => '10',
						'description'	=> 'px',
						'responsive' 	=> array(
							'placeholder'		=> array(
								'default'		=> '10',
								'medium'		=> '10',
								'responsive'	=> '10',
							),
						),
					),
					'autoplay'	=> array(
						'type'		=> 'pp-switch',
						'label'		=> __( 'Auto Play', 'bb-powerpack' ),
						'default'   => 'yes',
						'options'   => array(
							'yes'		=> __( 'Yes', 'bb-powerpack' ),
							'no'        => __( 'No', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'yes'	=> array(
								'fields'	=> array( 'autoplay_speed' ),
							),
						),
					),
					'autoplay_speed'	=> array(
						'type'          => 'text',
						'label'         => __( 'Auto Play Speed', 'bb-powerpack' ),
						'default'       => '5000',
						'size'          => '5',
						'description'   => _x( 'ms', 'Value unit for form field of time in mili seconds. Such as: "5000 ms"', 'bb-powerpack' ),
					),
					'infinite_loop'		=> array(
						'type'          => 'pp-switch',
						'label'         => __( 'Infinite Loop', 'bb-powerpack' ),
						'default'       => 'no',
						'options'       => array(
							'yes'			=> __( 'Yes', 'bb-powerpack' ),
							'no'            => __( 'No', 'bb-powerpack' ),
						),
					),
					'grab_cursor'  => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Grab Cursor', 'bb-powerpack' ),
						'default'       => 'no',
						'options'        => array(
							'yes'           => __( 'Yes', 'bb-powerpack' ),
							'no'            => __( 'No', 'bb-powerpack' ),
						),
					),
				),
			),
			'controls'		=> array(
				'title'         => __( 'Controls', 'bb-powerpack' ),
				'collapsed'		=> true,
				'fields'        => array(
					'navigation'     => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Arrows', 'bb-powerpack' ),
						'default'       => 'yes',
						'options'       => array(
							'yes'        	=> __( 'Yes', 'bb-powerpack' ),
							'no'            => __( 'No', 'bb-powerpack' ),
						),
						'toggle'		=> array(
							'yes'			=> array(
								'sections'		=> array( 'arrow_style' ),
							),
						),
					),
					'pagination'	=> array(
						'type'          => 'pp-switch',
						'label'         => __( 'Dots', 'bb-powerpack' ),
						'default'       => 'yes',
						'options'       => array(
							'yes'       	=> __( 'Yes', 'bb-powerpack' ),
							'no'			=> __( 'No', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'yes'	=> array(
								'sections'	=> array( 'dot_style' ),
							),
						),
					),
				),
			),
			'arrow_style'   => array( // Section
				'title' => __( 'Arrow Settings', 'bb-powerpack' ), // Section Title
				'collapsed'		=> true,
				'fields' => array( // Section Fields
					'arrow_font_size'   => array(
						'type'          => 'text',
						'label'         => __( 'Arrow Size', 'bb-powerpack' ),
						'description'   => 'px',
						'size'      	=> 5,
						'maxlength' 	=> 3,
						'default'       => '24',
						'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-instagram-feed .pp-swiper-button',
							'property'        => 'font-size',
							'unit'            => 'px',
						),
					),
					'arrow_bg_color'	=> array(
						'type'			=> 'color',
						'label'     	=> __( 'Background Color', 'bb-powerpack' ),
						'show_reset' 	=> true,
						'default'   	=> 'eaeaea',
						'connections'	=> array('color'),
						'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-instagram-feed .pp-swiper-button',
							'property'        => 'background-color',
						),
					),
					'arrow_bg_hover'	=> array(
						'type'      	=> 'color',
						'label'     	=> __( 'Background Hover Color', 'bb-powerpack' ),
						'show_reset' 	=> true,
						'default'   	=> '4c4c4c',
						'connections'	=> array('color'),
						'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-instagram-feed .pp-swiper-button:hover',
							'property'        => 'background-color',
						),
					),
					'arrow_color'	=> array(
						'type'			=> 'color',
						'label'			=> __( 'Arrow Color', 'bb-powerpack' ),
						'show_reset' 	=> true,
						'default'   	=> '000000',
						'connections'	=> array('color'),
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .pp-swiper-button',
							'property'      => 'color',
						),
					),
					'arrow_color_hover'	=> array(
						'type'			=> 'color',
						'label'     	=> __( 'Arrow Hover Color', 'bb-powerpack' ),
						'show_reset' 	=> true,
						'default'   	=> 'eeeeee',
						'connections'	=> array('color'),
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .pp-swiper-button:hover',
							'property'      => 'color',
						),
					),
					'arrow_border_style'	=> array(
						'type'      	=> 'pp-switch',
						'label'     	=> __( 'Border Style', 'bb-powerpack' ),
						'default'     	=> 'none',
						'options'       => array(
							'none'          => __( 'None', 'bb-powerpack' ),
							'solid'         => __( 'Solid', 'bb-powerpack' ),
							'dashed'        => __( 'Dashed', 'bb-powerpack' ),
							'dotted'        => __( 'Dotted', 'bb-powerpack' ),
						),
						'toggle'   => array(
							'solid'    => array(
								'fields'	=> array( 'arrow_border_width', 'arrow_border_color', 'arrow_border_hover' ),
							),
							'dashed'    => array(
								'fields'	=> array( 'arrow_border_width', 'arrow_border_color', 'arrow_border_hover' ),
							),
							'dotted'	=> array(
								'fields'	=> array( 'arrow_border_width', 'arrow_border_color', 'arrow_border_hover' ),
							),
							'double'    => array(
								'fields'   	=> array( 'arrow_border_width', 'arrow_border_color', 'arrow_border_hover' ),
							),
						),
						'preview'	=> array(
							'type'            => 'css',
							'selector'        => '.pp-instagram-feed .pp-swiper-button',
							'property'        => 'border-style',
							'unit'            => 'px',
						),
					),
					'arrow_border_width'	=> array(
						'type'          	=> 'text',
						'label'         	=> __( 'Border Width', 'bb-powerpack' ),
						'description'   	=> 'px',
						'size'      		=> 5,
						'maxlength' 		=> 3,
						'default'       	=> '1',
						'preview'         	=> array(
							'type'				=> 'css',
							'selector'        	=> '.pp-instagram-feed .pp-swiper-button',
							'property'        	=> 'border-width',
							'unit'            	=> 'px',
						),
					),
					'arrow_border_color'	=> array(
						'type'			=> 'color',
						'label'     	=> __( 'Border Color', 'bb-powerpack' ),
						'show_reset' 	=> true,
						'default'   	=> '',
						'connections'	=> array('color'),
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .pp-swiper-button',
							'property'      => 'border-color',
						),
					),
					'arrow_border_hover'	=> array(
						'type'			=> 'color',
						'label'     	=> __( 'Border Hover Color', 'bb-powerpack' ),
						'show_reset' 	=> true,
						'default'   	=> '',
						'connections'	=> array('color'),
						'preview'      	=> array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .pp-swiper-button:hover',
							'property'      => 'border-color',
						),
					),
					'arrow_border_radius'   => array(
						'type'          => 'text',
						'label'         => __( 'Round Corners', 'bb-powerpack' ),
						'description'   => 'px',
						'size'      	=> 5,
						'maxlength' 	=> 3,
						'default'       => '100',
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .pp-swiper-button',
							'property'      => 'border-radius',
							'unit'          => 'px',
						),
					),
					'arrow_horizontal_padding' 	=> array(
						'type'          => 'text',
						'label'         => __( 'Horizontal Padding', 'bb-powerpack' ),
						'default'   	=> '13',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'		=> array(
							'type'			=> 'css',
							'rules'			=> array(
								array(
									'selector'	=> '.pp-image-carousel .pp-swiper-button',
									'property'	=> 'padding-left',
									'unit'		=> 'px',
								),
								array(
									'selector'	=> '.pp-instagram-feed .pp-swiper-button',
									'property'	=> 'padding-right',
									'unit'		=> 'px',
								),
							),
						),
					),
					'arrow_vertical_padding'	=> array(
						'type'          => 'text',
						'label'         => __( 'Vertical Padding', 'bb-powerpack' ),
						'default'   	=> '5',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'		=> array(
							'type'			=> 'css',
							'rules'			=> array(
								array(
									'selector'	=> '.pp-instagram-feed .pp-swiper-button',
									'property'	=> 'padding-top',
									'unit'		=> 'px',
								),
								array(
									'selector'	=> '.pp-instagram-feed .pp-swiper-button',
									'property'	=> 'padding-bottom',
									'unit'		=> 'px',
								),
							),
						),
					),
				),
			),
			'dot_style'	=> array( // Section
				'title' 	=> __( 'Dot Settings', 'bb-powerpack' ), // Section Title
				'collapsed'		=> true,
				'fields' 	=> array( // Section Fields
					'dot_position'	=> array(
						'type'          => 'pp-switch',
						'label'         => __( 'Position', 'bb-powerpack' ),
						'default'       => 'outside',
						'options'       => array(
							'outside'        	=> __( 'Outside', 'bb-powerpack' ),
							'inside'            => __( 'Inside', 'bb-powerpack' ),
						),
					),
					'dot_bg_color'  => array(
						'type'          => 'color',
						'label'         => __( 'Background Color', 'bb-powerpack' ),
						'default'       => '666666',
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .swiper-pagination-bullet',
							'property'      => 'background-color',
						),
					),
					'dot_bg_hover'      => array(
						'type'          => 'color',
						'label'         => __( 'Active Color', 'bb-powerpack' ),
						'default'       => '000000',
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .swiper-pagination-bullet:hover, .pp-instagram-feed .swiper-pagination-bullet-active',
							'property'      => 'background-color',
						),
					),
					'dot_width'   => array(
						'type'          => 'text',
						'label'         => __( 'Size', 'bb-powerpack' ),
						'description'   => 'px',
						'size'      	=> 5,
						'maxlength' 	=> 3,
						'default'       => '10',
						'preview'       => array(
							'type'            => 'css',
							'rules'           => array(
								array(
									'selector'        => '.pp-instagram-feed .swiper-pagination-bullet',
									'property'        => 'width',
									'unit'            => 'px',
								),
								array(
									'selector'        => '.pp-instagram-feed .swiper-pagination-bullet',
									'property'        => 'height',
									'unit'            => 'px',
								),
							),
						),
					),
					'dot_border_radius'	=> array(
						'type'				=> 'text',
						'label'         	=> __( 'Round Corners', 'bb-powerpack' ),
						'description'   	=> 'px',
						'size'      		=> 5,
						'maxlength' 		=> 3,
						'default'       	=> '100',
						'preview'         	=> array(
							'type'				=> 'css',
							'selector'        	=> '.pp-instagram-feed .swiper-pagination-bullet',
							'property'        	=> 'border-radius',
							'unit'            	=> 'px',
						),
					),
				),
			),
		),
	),
	'style' => array(
		'title'     => __( 'Style', 'bb-powerpack' ),
		'description' => __( 'For smooth transition effect, please do not use grayscale feature with overlay.', 'bb-powerpack' ),
		'sections'  => array(
			'image'		=> array(
				'title'		=> __( 'Image', 'bb-powerpack' ),
				'fields'    => array(
					'image_grayscale'	=> array(
						'type'          => 'pp-switch',
						'label'         => __( 'Grayscale Image', 'bb-powerpack' ),
						'default'       => 'no',
						'options'       => array(
							'yes'        	=> __( 'Yes', 'bb-powerpack' ),
							'no'            => __( 'No', 'bb-powerpack' ),
						),
						'help'	=> __( 'For smooth transition effect, please do not use this feature with overlay.', 'bb-powerpack' ),
					),
					'image_overlay_type'	=> array(
						'type'          	=> 'pp-switch',
						'label'         	=> __( 'Image Overlay Type', 'bb-powerpack' ),
						'default'       	=> 'none',
						'options'       	=> array(
							'none'        		=> __( 'None', 'bb-powerpack' ),
							'solid'        		=> __( 'Solid', 'bb-powerpack' ),
							'gradient'      	=> __( 'Gradient', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'solid'		=> array(
								'fields'	=> array( 'image_overlay_color' ),
							),
							'gradient'	=> array(
								'fields'	=> array( 'image_overlay_angle', 'image_overlay_color', 'image_overlay_secondary_color', 'image_overlay_gradient_type' ),
							),
						),
					),
					'image_overlay_color'	=> array(
						'type'          		=> 'color',
						'label'         		=> __( 'Overlay Color', 'bb-powerpack' ),
						'default'       		=> '',
						'show_reset'    		=> true,
						'show_alpha'	=> true,
						'connections'			=> array('color'),
					),
					'image_overlay_secondary_color'	=> array(
						'type'			=> 'color',
						'label'     	=> __( 'Overlay Secondary Color', 'bb-powerpack' ),
						'default'		=> '',
						'show_reset' 	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
					),
					'image_overlay_gradient_type'	=> array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Type', 'bb-powerpack' ),
						'default'       => 'linear',
						'options'       => array(
							'linear'		=> __( 'Linear', 'bb-powerpack' ),
							'radial'        => __( 'Radial', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'linear'	=> array(
								'fields'	=> array( 'image_overlay_angle' ),
							),
							'radial'	=> array(
								'fields'	=> array( 'image_overlay_gradient_position' ),
							),
						),
					),
					'image_overlay_angle'	=> array(
						'type'			=> 'text',
						'label'       	=> __( 'Angle', 'bb-powerpack' ),
						'default'     	=> '180',
						'maxlength'   	=> '3',
						'size'        	=> '5',
						'description'	=> __('degree', 'bb-powerpack')
					),
					'image_overlay_gradient_position'	=> array(
						'type'			=> 'select',
						'label'         => __( 'Position', 'bb-powerpack' ),
						'default'       => 'center center',
						'options'       => array(
							'center center'			=> __( 'Center Center', 'bb-powerpack' ),
							'center left'           => __( 'Center Left', 'bb-powerpack' ),
							'center right'          => __( 'Center Right', 'bb-powerpack' ),
							'top center'            => __( 'Top Center', 'bb-powerpack' ),
							'top left'            	=> __( 'Top Left', 'bb-powerpack' ),
							'top right'            	=> __( 'Top Right', 'bb-powerpack' ),
							'bottom center'         => __( 'Bottom Center', 'bb-powerpack' ),
							'bottom left'           => __( 'Bottom Left', 'bb-powerpack' ),
							'bottom right'          => __( 'Bottom Right', 'bb-powerpack' ),
						),
					),
					// 'likes_comments_color'	=> array(
					// 	'type'			=> 'color',
					// 	'label'     	=> __( 'Overlay Content Color', 'bb-powerpack' ),
					// 	'default'		=> '',
					// 	'show_reset' 	=> true,
					// 	'connections'	=> array('color'),
					// 	'preview'       => array(
					// 		'type'			=> 'css',
					// 		'selector'      => '.pp-instagram-feed .pp-feed-item .pp-overlay-container',
					// 		'property'      => 'color',
					// 	),
					// ),
				),
			),
			'image_hover'	=> array(
				'title'     	=> __( 'Image Hover', 'bb-powerpack' ),
				'collapsed'		=> true,
				'fields'    	=> array(
					'image_hover_grayscale'	=> array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Grayscale Image', 'bb-powerpack' ),
						'default'       => 'no',
						'options'       => array(
							'yes'			=> __( 'Yes', 'bb-powerpack' ),
							'no'            => __( 'No', 'bb-powerpack' ),
						),
						'help'	=> __( 'For smooth transition effect, please do not use this feature with overlay.', 'bb-powerpack' ),
					),
					'image_hover_overlay_type'	=> array(
						'type'          => 'pp-switch',
						'label'         => __( 'Image Overlay Type', 'bb-powerpack' ),
						'default'       => 'none',
						'options'       => array(
							'none'        	=> __( 'None', 'bb-powerpack' ),
							'solid'        	=> __( 'Solid', 'bb-powerpack' ),
							'gradient'      => __( 'Gradient', 'bb-powerpack' ),
						),
						'toggle'    => array(
							'solid' 	=> array(
								'fields'    => array( 'image_hover_overlay_color' ),
							),
							'gradient' => array(
								'fields'    => array( 'image_hover_overlay_color', 'image_hover_overlay_secondary_color', 'image_hover_overlay_gradient_type' ),
							),
						),
					),
					'image_hover_overlay_color'	=> array(
						'type'			=> 'color',
						'label'         => __( 'Overlay Color', 'bb-powerpack' ),
						'default'       => '',
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
					),
					'image_hover_overlay_secondary_color'	=> array(
						'type'       	=> 'color',
						'label'     	=> __( 'Overlay Secondary Color', 'bb-powerpack' ),
						'default'		=> '',
						'show_reset' 	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
					),
					'image_hover_overlay_gradient_type'	=> array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Type', 'bb-powerpack' ),
						'default'       => 'linear',
						'options'       => array(
							'linear'        	=> __( 'Linear', 'bb-powerpack' ),
							'radial'            => __( 'Radial', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'linear'	=> array(
								'fields'	=> array( 'image_hover_overlay_angle' ),
							),
							'radial'	=> array(
								'fields'	=> array( 'image_hover_overlay_gradient_position' ),
							),
						),
					),
					'image_hover_overlay_angle'	=> array(
						'type'			=> 'text',
						'label'       	=> __( 'Angle', 'bb-powerpack' ),
						'default'     	=> '180',
						'maxlength'   	=> '3',
						'size'        	=> '5',
						'description'	=> __('degree', 'bb-powerpack')
					),
					'image_hover_overlay_gradient_position'	=> array(
						'type'			=> 'select',
						'label'         => __( 'Position', 'bb-powerpack' ),
						'default'       => 'center center',
						'options'       => array(
							'center center'			=> __( 'Center Center', 'bb-powerpack' ),
							'center left'           => __( 'Center Left', 'bb-powerpack' ),
							'center right'          => __( 'Center Right', 'bb-powerpack' ),
							'top center'            => __( 'Top Center', 'bb-powerpack' ),
							'top left'            	=> __( 'Top Left', 'bb-powerpack' ),
							'top right'            	=> __( 'Top Right', 'bb-powerpack' ),
							'bottom center'         => __( 'Bottom Center', 'bb-powerpack' ),
							'bottom left'           => __( 'Bottom Left', 'bb-powerpack' ),
							'bottom right'          => __( 'Bottom Right', 'bb-powerpack' ),
						),
					),
					// 'likes_comments_hover_color'	=> array(
					// 	'type'			=> 'color',
					// 	'label'     	=> __( 'Overlay Content Color', 'bb-powerpack' ),
					// 	'default'		=> '',
					// 	'show_reset' 	=> true,
					// 	'connections'	=> array('color'),
					// ),
				),
			),
			'feed_title'	=> array(
				'title'			=> __( 'Link to Profile Text', 'bb-powerpack' ),
				'collapsed'		=> true,
				'fields'		=> array(
					'feed_title_position'	=> array(
						'type'          => 'select',
						'label'         => __( 'Position', 'bb-powerpack' ),
						'default'       => 'middle',
						'options'       => array(
							'top'			=> __( 'Top', 'bb-powerpack' ),
							'middle'        => __( 'Middle', 'bb-powerpack' ),
							'bottom'        => __( 'Bottom', 'bb-powerpack' ),
						),
					),
					'feed_title_bg_color'	=> array(
						'type'			=> 'color',
						'label'         => __( 'Background Color', 'bb-powerpack' ),
						'default'       => '',
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .pp-instagram-feed-title-wrap',
							'property'      => 'background-color',
						),
					),
					'feed_title_bg_hover'	=> array(
						'type'          => 'color',
						'label'         => __( 'Background Hover Color', 'bb-powerpack' ),
						'default'       => '',
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-instagram-feed .pp-instagram-feed-title-wrap:hover',
							'property'      => 'background-color',
						),
					),
					'feed_title_text_color'	=> array(
						'type'          => 'color',
						'label'         => __( 'Text Color', 'bb-powerpack' ),
						'default'       => '',
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-instagram-feed .pp-instagram-feed-title-wrap .pp-instagram-feed-title',
							'property'      => 'color',
						),
					),
					'feed_title_text_hover'	=> array(
						'type'			=> 'color',
						'label'         => __( 'Text Hover Color', 'bb-powerpack' ),
						'default'       => '',
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .pp-instagram-feed-title-wrap:hover .pp-instagram-feed-title',
							'property'      => 'color',
						),
					),
					'feed_title_border_group'	=> array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack' ),
						'responsive'	=> true,
						'preview'   	=> array(
                            'type'  		=> 'css',
                            'selector'  	=> '.pp-instagram-feed .pp-instagram-feed-title-wrap',
                            'property'  	=> 'border',
                        ),
					),
					'feed_title_border_hover'	=> array(
						'type'			=> 'color',
						'label'     	=> __( 'Border Hover Color', 'bb-powerpack' ),
						'show_reset' 	=> true,
						'default'   	=> '',
						'connections'	=> array('color'),
						'preview'       => array(
							'type'			=> 'css',
							'selector'      => '.pp-instagram-feed .pp-instagram-feed-title-wrap:hover',
							'property'      => 'border-color',
						),
					),
					'feed_title_horizontal_padding'	=> array(
						'type'			=> 'unit',
						'label' 		=> __( 'Horizontal Padding', 'bb-powerpack' ),
						'units'			=> array( 'px' ),
						'slider'        => true,
						'responsive' 	=> array(
							'placeholder'	=> array(
								'default'		=> '',
								'medium'		=> '',
								'responsive'	=> '',
							),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-instagram-feed .pp-instagram-feed-title-wrap',
									'property'	=> 'padding-left',
									'unit' 		=> 'px',
								),
								array(
									'selector'	=> '.pp-instagram-feed .pp-instagram-feed-title-wrap',
									'property'	=> 'padding-right',
									'unit' 		=> 'px',
								),
							),
						),
					),
					'feed_title_vertical_padding'	=> array(
						'type' 			=> 'unit',
						'label' 		=> __( 'Vertical Padding', 'bb-powerpack' ),
						'units'			=> array( 'px' ),
						'slider'        => true,
						'responsive'	=> array(
							'placeholder'	=> array(
								'default'		=> '',
								'medium'		=> '',
								'responsive'	=> '',
							),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-instagram-feed .pp-instagram-feed-title-wrap',
									'property'	=> 'padding-top',
									'unit' 		=> 'px',
								),
								array(
									'selector'	=> '.pp-instagram-feed .pp-instagram-feed-title-wrap',
									'property'	=> 'padding-bottom',
									'unit' 		=> 'px',
								),
							),
						),
					),
				),
			),
		),
	),
	'typography'	=> array(
		'title'			=> __( 'Typography', 'bb-powerpack' ),
		'sections'  	=> array(
			'feed_title_typography'	=> array(
				'title'		=> __( 'Link to Profile Text', 'bb-powerpack' ),
				'fields'	=> array(
					'title_typography'	=> array(
						'type'			=> 'typography',
						'label'			=> __('Typography', 'bb-powerpack'),
						'responsive'  	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-instagram-feed .pp-instagram-feed-title-wrap',
						),
					),
				),
			),
		),
	),
));
