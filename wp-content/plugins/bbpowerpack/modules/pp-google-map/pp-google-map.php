<?php

/**
 * @class PPGoogleMapModule
 */
class PPGoogleMapModule extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'          => __( 'Google Map', 'bb-powerpack' ),
				'description'   => __( 'A module for Display Google Map.', 'bb-powerpack' ),
				'group'         => pp_get_modules_group(),
				'category'      => pp_get_modules_cat( 'creative' ),
				'dir'           => BB_POWERPACK_DIR . 'modules/pp-google-map/',
				'url'           => BB_POWERPACK_URL . 'modules/pp-google-map/',
				'editor_export' => true,
				'enabled'       => true,
			)
		);

		add_filter( 'script_loader_tag', array( $this, 'add_async_attribute' ), 10, 2 );
	}

	public function enqueue_scripts() {
		$url = pp_get_google_api_url();
		if ( $url ) {
			$this->add_js(
				'pp-google-map',
				$url,
				array( 'jquery' ),
				'3.0',
				false
			);

			if ( isset( $this->settings->marker_clustering ) && 'yes' === $this->settings->marker_clustering ) {
				$this->add_js( 'pp-cluster' );
			}
		}
	}

	/**
	 * @method  add_async_attribute for the enqueued `pp-google-map` script
	 * @param string $tag    Script tag
	 * @param string $handle Registered script handle
	 */
	public function add_async_attribute( $tag, $handle ) {
		if ( ! in_array( $handle, array( 'pp-google-map' ) ) ) {
			return $tag;
		}

		if ( 'pp-google-map' === $handle ) {
			return str_replace( ' src', ' async="async" src', $tag );
		}

		return $tag;
	}

	public function filter_settings( $settings, $updater ) {
		if ( isset( $settings->post_slug ) ) {
			$settings->post_type = $settings->post_slug;
			unset( $settings->post_slug );
		}
		if ( isset( $settings->post_count ) ) {
			$settings->posts_per_page = $settings->post_count;
			unset( $settings->post_count );
		}
		if ( isset( $settings->post_order_by ) ) {
			$settings->order_by = $settings->post_order_by;
			unset( $settings->post_order_by );
		}
		if ( isset( $settings->post_order_by_meta_key ) ) {
			$settings->order_by_meta_key = $settings->post_order_by_meta_key;
			unset( $settings->post_order_by_meta_key );
		}
		if ( isset( $settings->post_order ) ) {
			$settings->order = $settings->post_order;
			unset( $settings->post_order );
		}
		if ( isset( $settings->post_offset ) ) {
			$settings->offset = $settings->post_offset;
			unset( $settings->post_offset );
		}

		return $settings;
	}

	public static function get_general_fields() {
		$fields = array(
			'map_source'        => array(
				'type'    => 'select',
				'label'   => __( 'Source', 'bb-powerpack' ),
				'default' => 'manual',
				'options' => array(
					'manual' => __( 'Manual', 'bb-powerpack' ),
					'post'   => __( 'Post', 'bb-powerpack' ),
				),
				'toggle'  => array(
					'manual' => array(
						'fields' => array( 'pp_gmap_addresses' ),
					),
					'post'   => array(
						'fields'   => array( 'post_map_name', 'post_map_latitude', 'post_map_longitude', 'post_marker_point' ),
						'sections' => array( 'post', 'location' ),
					),
				),
			),
			'pp_gmap_addresses' => array(
				'type'         => 'form',
				'label'        => __( 'Location', 'bb-powerpack' ),
				'form'         => 'pp_google_map_addresses',
				'preview_text' => 'map_name',
				'multiple'     => true,
			),
		);

		if ( class_exists( 'acf' ) ) {
			$fields['map_source']['options']['acf']          = __( 'ACF Repeater Field', 'bb-powerpack' );
			$fields['map_source']['toggle']['acf']['fields'] = array( 'acf_repeater_name', 'acf_map_name', 'acf_map_latitude', 'acf_map_longitude', 'acf_marker_point', 'acf_marker_img', 'acf_enable_info' );

			$fields['acf_repeater_name']    = array(
				'type'        => 'text',
				'label'       => __( 'ACF Repeater Field Name', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			);

			$fields['acf_map_name']         = array(
				'type'        => 'text',
				'label'       => __( 'Location Name', 'bb-powerpack' ),
				'help'        => __( 'A browser based tooltip will be applied on marker.', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			);

			$fields['acf_map_latitude']     = array(
				'type'        => 'text',
				'label'       => __( 'Latitude (ACF Field)', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			);

			$fields['acf_map_longitude']    = array(
				'type'        => 'text',
				'label'       => __( 'Longitude (ACF Field)', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			);

			$fields['acf_marker_point']     = array(
				'type'    => 'pp-switch',
				'label'   => __( 'Marker Icon', 'bb-powerpack' ),
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'bb-powerpack' ),
					'custom'  => __( 'Custom', 'bb-powerpack' ),
				),
				'toggle'  => array(
					'custom' => array(
						'fields' => array( 'acf_marker_img' ),
					),
				),
			);

			$fields['acf_marker_img']       = array(
				'type'        => 'photo',
				'label'       => __( 'Custom Marker', 'bb-powerpack' ),
				'show_remove' => true,
				'connections' => array( 'photo' ),
			);

			$fields['acf_enable_info']      = array(
				'type'    => 'pp-switch',
				'label'   => __( 'Show Info Window', 'bb-powerpack' ),
				'default' => 'no',
				'toggle'  => array(
					'yes' => array(
						'fields' => array( 'acf_info_window_text' ),
					),
				),
			);

			$fields['acf_info_window_text'] = array(
				'type'          => 'editor',
				'label'         => '',
				'default'       => __( 'IdeaBox Creations', 'bb-powerpack' ),
				'media_buttons' => false,
				'connections'   => array( 'string', 'html', 'url' ),
			);
		}

		if ( function_exists( 'acf_add_options_page' ) ) {
			$fields['map_source']['options']['acf_options_page']          = __( 'ACF Option Page', 'bb-powerpack' );
			$fields['map_source']['toggle']['acf_options_page']['fields'] = array( 'acf_options_page_repeater_name', 'acf_options_map_name', 'acf_options_map_latitude', 'acf_options_map_longitude', 'acf_options_marker_point', 'acf_options_marker_img', 'acf_options_enable_info' );
			$fields['map_source']['help']                                 = __( 'To use the "ACF Option Page" feature, you will need ACF PRO (ACF v5), or the options page add-on (ACF v4)', 'bb-powerpack' );

			$fields['acf_options_page_repeater_name'] = array(
				'type'        => 'text',
				'label'       => __( 'ACF Repeater Field Name', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			);

			$fields['acf_options_map_name']         = array(
				'type'        => 'text',
				'label'       => __( 'Location Name', 'bb-powerpack' ),
				'help'        => __( 'Location Name to identify while editing', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			);

			$fields['acf_options_map_latitude']     = array(
				'type'        => 'text',
				'label'       => __( 'Latitude (ACF Field)', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			);

			$fields['acf_options_map_longitude']    = array(
				'type'        => 'text',
				'label'       => __( 'Longitude (ACF Field)', 'bb-powerpack' ),
				'connections' => array( 'string' ),
			);

			$fields['acf_options_marker_point']     = array(
				'type'    => 'pp-switch',
				'label'   => __( 'Marker Icon', 'bb-powerpack' ),
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'bb-powerpack' ),
					'custom'  => __( 'Custom', 'bb-powerpack' ),
				),
				'toggle'  => array(
					'custom' => array(
						'fields' => array( 'acf_options_marker_img' ),
					),
				),
			);

			$fields['acf_options_marker_img']       = array(
				'type'        => 'photo',
				'label'       => __( 'Custom Marker', 'bb-powerpack' ),
				'show_remove' => true,
				'connections' => array( 'photo' ),
			);

			$fields['acf_options_enable_info']      = array(
				'type'    => 'pp-switch',
				'label'   => __( 'Show Info Window', 'bb-powerpack' ),
				'default' => 'no',
				'options' => array(
					'yes' => __( 'Yes', 'bb-powerpack' ),
					'no'  => __( 'No', 'bb-powerpack' ),
				),
				'toggle'  => array(
					'yes' => array(
						'fields' => array( 'acf_options_info_window_text' ),
					),
				),
			);

			$fields['acf_options_info_window_text'] = array(
				'type'          => 'editor',
				'label'         => '',
				'default'       => __( 'IdeaBox Creations', 'bb-powerpack' ),
				'media_buttons' => false,
				'connections'   => array( 'string', 'html', 'url' ),
			);
		}

		return $fields;
	}

	public function get_cpt_data() {
		$data = array();

		if ( ! isset( $this->settings->post_type ) || empty( $this->settings->post_type ) ) {
			return $data;
		}

		if ( is_callable( 'FLThemeBuilderFieldConnections::connect_settings' ) ) {
			$this->settings = FLThemeBuilderFieldConnections::connect_settings( $this->settings );
		}

		if ( '' === $this->settings->posts_per_page ) {
			$this->settings->posts_per_page = '-1';
		}

		$settings = $this->settings;

		$query = FLBuilderLoop::query( $this->settings );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$item                   = new stdClass;
				$item->map_name         = ! empty( $settings->post_map_name ) ? do_shortcode( $settings->post_map_name ) : get_the_title();
				$item->map_latitude     = ! empty( $settings->post_map_latitude ) ? do_shortcode( $settings->post_map_latitude ) : '';
				$item->map_longitude    = ! empty( $settings->post_map_longitude ) ? do_shortcode( $settings->post_map_longitude ) : '';
				$item->marker_point     = ! empty( $settings->post_marker_point ) ? $settings->post_marker_point : 'default';
				$item->marker_img       = isset( $settings->post_marker_img_src ) && ! empty( $settings->post_marker_img_src ) ? $settings->post_marker_img_src : '';
				$item->enable_info      = ! empty( $settings->post_enable_info ) ? $settings->post_enable_info : 'no';
				$item->info_window_text = ! empty( $settings->post_info_window_text ) ? do_shortcode( $settings->post_info_window_text ) : get_the_title();
				$item->marker_link      = get_permalink();
				$item->marker_link_target = '_blank';

				if ( ! empty( $item->map_latitude ) && ! empty( $item->map_longitude ) ) {
					$data[] = $item;
				}
			}
			wp_reset_postdata();
		}

		return $data;
	}

	public function get_acf_data( $post_id = false ) {
		if ( ( ! isset( $this->settings->acf_repeater_name ) || empty( $this->settings->acf_repeater_name ) ) ) {
			return;
		}

		$settings = $this->settings;
		$data     = array();

		if ( is_tax() || is_category() ) {
			$post_id = get_queried_object();
		}

		$post_id = apply_filters( 'pp_google_map_acf_post_id', $post_id, $settings );

		$repeater_name 	  = $settings->acf_repeater_name;
		$map_name         = $settings->acf_map_name;
		$map_latitude     = $settings->acf_map_latitude;
		$map_longitude    = $settings->acf_map_longitude;
		$marker_point     = $settings->acf_marker_point;
		$marker_img       = $settings->acf_marker_img_src;
		$enable_info      = $settings->acf_enable_info;
		$info_window_text = $settings->acf_info_window_text;

		if ( empty( $repeater_name ) ) {
			return;
		}
		
		$repeater_rows = get_field( $repeater_name, $post_id );

		if ( ! $repeater_rows ) {
			return;
		}

		foreach ( $repeater_rows as $row ) {
			$item                   = new stdClass;
			$item->map_name         = ! empty( $map_name ) ?  ( isset( $row[ $map_name ] ) ? $row[ $map_name ] : $map_name ) : '';
			$item->map_latitude     = ! empty( $map_latitude ) ? ( isset( $row[ $map_latitude ] ) ? $row[ $map_latitude ] : $map_latitude ) : '';
			$item->map_longitude    = ! empty( $map_longitude ) ? ( isset( $row[ $map_longitude ] ) ? $row[ $map_longitude ] : $map_longitude ) : '';
			$item->marker_point     = ! empty( $marker_point ) ? $marker_point : 'default';
			$item->marker_img       = ! empty( $marker_img ) ? $marker_img : '';
			$item->enable_info      = ! empty( $enable_info ) ? $enable_info : 'no';
			$item->info_window_text = ! empty( $info_window_text ) ? ( ! empty( strip_tags( $info_window_text ) ) && isset( $row[ strip_tags( $info_window_text ) ] ) ? $row[ strip_tags( $info_window_text ) ] : $info_window_text ) : '';

			$data[] = $item;
		}

		return $data;
	}

	public function get_acf_options_page_data() {
		$data = array();

		$repeater_name    = $this->settings->acf_options_page_repeater_name;
		$map_name         = $this->settings->acf_options_map_name;
		$map_latitude     = $this->settings->acf_options_map_latitude;
		$map_longitude    = $this->settings->acf_options_map_longitude;
		$marker_point     = $this->settings->acf_options_marker_point;
		$marker_img       = $this->settings->acf_options_marker_img_src;
		$enable_info      = $this->settings->acf_options_enable_info;
		$info_window_text = $this->settings->acf_options_info_window_text;

		if ( empty( $this->settings->acf_options_page_repeater_name ) ) {
			$item                   = new stdClass;
			$item->map_name 	 	= $map_name;
			$item->map_latitude     = ! empty( $map_latitude ) ? get_field( $map_latitude, 'option' ) : '';
			$item->map_longitude    = ! empty( $map_longitude ) ? get_field( $map_longitude, 'option' ) : '';
			$item->marker_point     = ! empty( $marker_point ) ? $marker_point : 'default';
			$item->marker_img       = ! empty( $marker_img ) ? $marker_img : '';
			$item->enable_info      = ! empty( $enable_info ) ? $enable_info : 'no';
			$item->info_window_text = ! empty( $info_window_text ) ? $info_window_text : '';

			$data[] = $item;
		} else {
			$rows = get_field( $repeater_name, 'option' );
			
			if ( ! isset( $rows ) || ! $rows ) {
				return;
			}
	
			foreach ( $rows as $row ) {
				$item                   = new stdClass;
				$item->map_name         = ! empty( $map_name ) ?  ( isset( $row[ $map_name ] ) ? $row[ $map_name ] : $map_name ) : '';
				$item->map_latitude     = ! empty( $map_latitude ) ? ( isset( $row[ $map_latitude ] ) ? $row[ $map_latitude ] : $map_latitude ) : '';
				$item->map_longitude    = ! empty( $map_longitude ) ? ( isset( $row[ $map_longitude ] ) ? $row[ $map_longitude ] : $map_longitude ) : '';
				$item->marker_point     = ! empty( $marker_point ) ? $marker_point : 'default';
				$item->marker_img       = ! empty( $marker_img ) ? $marker_img : '';
				$item->enable_info      = ! empty( $enable_info ) ? $enable_info : 'no';
				$item->info_window_text = ! empty( $info_window_text ) ? ( ! empty( strip_tags( $info_window_text ) ) && isset( $row[ strip_tags( $info_window_text ) ] ) ? $row[ strip_tags( $info_window_text ) ] : $info_window_text ) : '';
	
				$data[] = $item;
			}
		}

		return $data;
	}

	public function get_map_data() {
		$data = $this->settings->pp_gmap_addresses;

		if ( ! isset( $this->settings->map_source ) || empty( $this->settings->map_source ) ) {
			$data = $this->settings->pp_gmap_addresses;
		}

		if ( 'acf' === $this->settings->map_source ) {
			$data = $this->get_acf_data();
		}

		if ( 'acf_options_page' === $this->settings->map_source ) {
			$data = $this->get_acf_options_page_data();
		}

		if ( 'post' === $this->settings->map_source ) {
			$data = $this->get_cpt_data();
		}

		return apply_filters( 'pp_google_map_data', $data, $this->settings );
	}

	public function get_marker_data() {
		$map_source    = ! isset( $this->settings->map_source ) ? 'manual' : $this->settings->map_source;
		$map_addresses = $this->get_map_data();
		$marker_data = array(
			'markerData'	 => array(),
			'markerName'	 => array(),
			'markerPoint'	 => array(),
			'markerImage'	 => array(),
			'infoWindowText' => array(),
			'enableInfo'	 => array(),
		);

		if ( is_array( $map_addresses ) && 0 < count( $map_addresses ) ) {
			foreach ( $map_addresses as $data ) {
				$data->map_latitude  = ( '' !== $data->map_latitude ) ? $data->map_latitude : 24.553311;
				$data->map_longitude = ( '' !== $data->map_longitude ) ? $data->map_longitude : 73.694076;
				
				$marker_image = ( isset( $data->marker_img ) ) ? $data->marker_img : '';
				if ( 'manual' === $map_source ) {
					if ( isset( $data->marker_img_src ) ) {
						$marker_image = $data->marker_img_src;
					}
				}

				$latlong = array(
					'latitude' => do_shortcode( $data->map_latitude ),
					'longitude' => do_shortcode( $data->map_longitude ),
				);
				
				$marker_data['markerName'][] = ( isset( $data->map_name ) ) ? $data->map_name : 'default';
				$marker_data['markerData'][] = $latlong;
				$marker_data['markerPoint'][] = ( isset( $data->marker_point ) ) ? $data->marker_point : 'default';
				$marker_data['markerImage'][] = $marker_image;
				$marker_data['markerImageWidth'][] = isset( $data->marker_width ) && ! empty( $data->marker_width ) ? $data->marker_width : '';
				$marker_data['markerImageHeight'][] = isset( $data->marker_height ) && ! empty( $data->marker_height ) ? $data->marker_height : '';
				$marker_data['infoWindowText'][] = do_shortcode( trim( preg_replace( '/\s+/', ' ', do_shortcode( $data->info_window_text ) ) ) );
				$marker_data['enableInfo'][] = $data->enable_info;

				if ( isset( $data->marker_link ) ) {
					$marker_data['markerLinks'][] = array( $data->marker_link, $data->marker_link_target );
				}
			}
		}

		return $marker_data;
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module(
	'PPGoogleMapModule',
	array(
		'form'      => array(
			'title'    => __( 'Locations', 'bb-powerpack' ),
			'sections' => array(
				'address_form' => array(
					'title'  => '',
					'fields' => PPGoogleMapModule::get_general_fields(),
				),
				'post_content' => array(
					'title' => __( 'Content', 'bb-powerpack' ),
					'file'  => BB_POWERPACK_DIR . 'modules/pp-google-map/includes/loop-settings.php',
				),
			),
		),
		'settings'  => array(
			'title'    => __( 'Settings', 'bb-powerpack' ),
			'sections' => array(
				'gen_control' => array(
					'title'  => '',
					'fields' => array(
						'zoom_type'        => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Zoom Type', 'bb-powerpack' ),
							'default' => 'auto',
							'options' => array(
								'auto'   => __( 'Auto', 'bb-powerpack' ),
								'custom' => __( 'Custom', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'custom' => array(
									'fields' => array( 'map_zoom' ),
								),
							),
						),
						'map_zoom'         => array(
							'type'    => 'select',
							'label'   => __( 'Map Zoom', 'bb-powerpack' ),
							'default' => '12',
							'options' => array(
								'1'  => '1',
								'2'  => '2',
								'3'  => '3',
								'4'  => '4',
								'5'  => '5',
								'6'  => '6',
								'7'  => '7',
								'8'  => '8',
								'9'  => '9',
								'10' => '10',
								'11' => '11',
								'12' => '12',
								'13' => '13',
								'14' => '14',
								'15' => '15',
								'16' => '16',
								'17' => '17',
								'18' => '18',
								'19' => '19',
								'20' => '20',
							),
						),
						'max_zoom'	=> array(
							'type'	=> 'unit',
							'label'	=> __( 'Maximum Zoom', 'bb-powerpack' ),
							'default' => '',
							'slider' => array(
								'min'	=> 1,
								'max'	=> 20,
								'step'	=> 1
							),
						),
						'scroll_zoom'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Disable zoom on scroll', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
						'dragging'         => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Disable Dragging on Mobile', 'bb-powerpack' ),
							'default' => 'false',
							'options' => array(
								'false' => __( 'Yes', 'bb-powerpack' ),
								'true'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'marker_animation' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Marker Animation', 'bb-powerpack' ),
							'default' => 'drop',
							'options' => array(
								''       => __( 'None', 'bb-powerpack' ),
								'drop'   => __( 'Drop', 'bb-powerpack' ),
								'bounce' => __( 'Bounce', 'bb-powerpack' ),
							),
						),
						'marker_clustering'	=> array(
							'type'	=> 'pp-switch',
							'label'	=> __( 'Marker Clustering', 'bb-powerpack' ),
							'default' => 'no',
							'help'	=> __( 'Use marker clustering to display a large number of markers on a map and prevent overlapping.', 'bb-powerpack' ),
						),
					),
				),
				'control'     => array(
					'title'     => __( 'Controls', 'bb-powerpack' ),
					'collapsed' => true,
					'fields'    => array(
						'street_view'        => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Street view control', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'map_type_control'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Map type control', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'zoom'               => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Zoom control', 'bb-powerpack' ),
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'fullscreen_control' => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Full Screen control', 'bb-powerpack' ),
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
						'hide_tooltip'       => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Info Window on Click', 'bb-powerpack' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
						),
					),
				),
			),
		),
		'map_style' => array(
			'title'    => __( 'Style', 'bb-powerpack' ),
			'sections' => array(
				'general'    => array(
					'title'  => '',
					'fields' => array(
						'map_width'      => array(
							'type'       => 'unit',
							'label'      => __( 'Width', 'bb-powerpack' ),
							'default'    => '100',
							'slider'     => array(
								'%'  => array(
									'min' => 0,
									'max' => 100,
								),
								'px' => array(
									'min' => 0,
									'max' => 1000,
								),
							),
							'units'      => array( '%', 'px' ),
							'responsive' => true,
						),
						'map_height'     => array(
							'type'       => 'unit',
							'label'      => __( 'Height', 'bb-powerpack' ),
							'default'    => '400',
							'slider'     => array(
								'px' => array(
									'min'  => 0,
									'max'  => 1000,
									'step' => 10,
								),
							),
							'units'      => array( 'px' ),
							'responsive' => true,
						),
						'map_type'       => array(
							'type'    => 'select',
							'label'   => __( 'Map View', 'bb-powerpack' ),
							'default' => 'roadmap',
							'options' => array(
								'roadmap'   => __( 'Roadmap', 'bb-powerpack' ),
								'satellite' => __( 'Satellite', 'bb-powerpack' ),
								'hybrid'    => __( 'Hybrid', 'bb-powerpack' ),
								'terrain'   => __( 'Terrain', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'roadmap' => array(
									'fields' => array( 'map_skin' ),
								),
								'hybrid'  => array(
									'fields' => array( 'map_skin' ),
								),
								'terrain' => array(
									'fields' => array( 'map_skin' ),
								),
							),
						),
						'map_skin'       => array(
							'type'    => 'select',
							'label'   => __( 'Map Skin', 'bb-powerpack' ),
							'default' => 'standard',
							'options' => array(
								'standard'     => __( 'Standard', 'bb-powerpack' ),
								'aqua'         => __( 'Aqua', 'bb-powerpack' ),
								'aubergine'    => __( 'Aubergine', 'bb-powerpack' ),
								'classic_blue' => __( 'Classic Blue', 'bb-powerpack' ),
								'dark'         => __( 'Dark', 'bb-powerpack' ),
								'earth'        => __( 'Earth', 'bb-powerpack' ),
								'magnesium'    => __( 'Magnesium', 'bb-powerpack' ),
								'night'        => __( 'Night', 'bb-powerpack' ),
								'silver'       => __( 'Silver', 'bb-powerpack' ),
								'retro'        => __( 'Retro', 'bb-powerpack' ),
								'custom'       => __( 'Custom Style', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'custom' => array(
									'fields' => array( 'map_style1', 'map_style_code' ),
								),
							),
						),
						'map_style1'     => array(
							'type'        => 'static',
							'description' => __( '<a target="_blank" rel="noopener" href="https://mapstyle.withgoogle.com/"><b>Click here</b></a> to get JSON style code to style your map.', 'bb-powerpack' ),
						),
						'map_style_code' => array(
							'type'          => 'editor',
							'label'         => '',
							'rows'          => 3,
							'media_buttons' => false,
							'connections'   => array( 'string', 'html' ),
						),
					),
				),
				'info_style' => array(
					'title'  => __( 'Marker Info', 'bb-powerpack' ),
					'fields' => array(
						'info_width'   => array(
							'type'       => 'unit',
							'label'      => __( 'Marker Info Window Width', 'bb-powerpack' ),
							'default'    => '200',
							'units'      => array( 'px' ),
							'slider'     => array(
								'px' => array(
									'min' => 0,
									'max' => 1000,
								),
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gm-style .pp-infowindow-content',
								'property' => 'max-width',
								'unit'     => 'px',
							),
							'responsive' => true,
						),
						'info_padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack' ),
							'slider'     => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.gm-style .pp-infowindow-content',
								'property' => 'padding',
								'unit'     => 'px',
							),
							'responsive' => true,
						),
					),
				),
			),
		),
	)
);

FLBuilder::register_settings_form(
	'pp_google_map_addresses',
	array(
		'title' => __( 'Add Location', 'bb-powerpack' ),
		'tabs'  => array(
			'addr_general' => array(
				'title'    => __( 'General', 'bb-powerpack' ),
				'sections' => array(
					'features' => array(
						'title'  => __( 'Location', 'bb-powerpack' ),
						'fields' => array(
							'map_name'      => array(
								'type'        => 'text',
								'label'       => __( 'Location Name', 'bb-powerpack' ),
								'default'     => 'IdeaBox Creations',
								'help'        => __( 'A browser based tooltip will be applied on marker.', 'bb-powerpack' ),
								'connections' => array( 'string' ),
							),
							'map_latitude'  => array(
								'type'        => 'text',
								'label'       => __( 'Latitude', 'bb-powerpack' ),
								'default'     => '24.553311',
								'description' => __( '<a href="https://www.latlong.net/" target="_blank" rel="noopener"><b>Click here</b></a> to find Latitude and Longitude of a location.', 'bb-powerpack' ),
								'connections' => array( 'string' ),
							),
							'map_longitude' => array(
								'type'        => 'text',
								'label'       => __( 'Longitude', 'bb-powerpack' ),
								'default'     => '73.694076',
								'description' => __( '<a href="https://www.latlong.net/" target="_blank" rel="noopener"><b>Click here</b></a> to find Latitude and Longitude of a location.', 'bb-powerpack' ),
								'connections' => array( 'string' ),
							),
							'marker_point'  => array(
								'type'    => 'pp-switch',
								'label'   => __( 'Marker Icon', 'bb-powerpack' ),
								'default' => 'default',
								'options' => array(
									'default' => __( 'Default', 'bb-powerpack' ),
									'custom'  => __( 'Custom', 'bb-powerpack' ),
								),
								'toggle'  => array(
									'custom' => array(
										'fields' => array( 'marker_img', 'marker_width', 'marker_height' ),
									),
								),
							),
							'marker_img'    => array(
								'type'        => 'photo',
								'label'       => __( 'Custom Marker', 'bb-powerpack' ),
								'show_remove' => true,
								'connections' => array( 'photo' ),
							),
							'marker_width'	=> array(
								'type'	=> 'unit',
								'label'	=> __( 'Custom Marker Width', 'bb-powerpack' ),
								'default' => '',
								'slider' => true
							),
							'marker_height'	=> array(
								'type'	=> 'unit',
								'label'	=> __( 'Custom Marker Height', 'bb-powerpack' ),
								'default' => '',
								'slider' => true
							),
						),
					),
				),
			),
			'info_window'  => array(
				'title'    => __( 'Marker Info & Link', 'bb-powerpack' ),
				'description' => __( 'Link field will be shown when Info Window is disabled.', 'bb-powerpack' ),
				'sections' => array(
					'title' => array(
						'title'  => '',
						'fields' => array(
							'enable_info'      => array(
								'type'    => 'pp-switch',
								'label'   => __( 'Show Info Window', 'bb-powerpack' ),
								'default' => 'yes',
								'options' => array(
									'yes' => __( 'Yes', 'bb-powerpack' ),
									'no'  => __( 'No', 'bb-powerpack' ),
								),
								'toggle'  => array(
									'yes' => array(
										'fields' => array( 'info_window_text' ),
									),
									'no' => array(
										'fields' => array( 'marker_link' ),
									),
								),
							),
							'info_window_text' => array(
								'type'          => 'editor',
								'label'         => '',
								'default'       => __( 'IdeaBox Creations', 'bb-powerpack' ),
								'media_buttons' => false,
								'connections'   => array( 'string', 'html', 'url' ),
							),
							'marker_link' => array(
								'type'  => 'link',
								'label' => __( 'Marker Link', 'bb-powerpack' ),
								'show_nofollow' => false,
								'show_target' => true,
								'connections' => array( 'url' ),
								'preview' => array(
									'type' => 'none'
								),
							),
						),
					),
				),
			),
		),
	)
);
