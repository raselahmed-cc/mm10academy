<?php

/**
 * @class PPImageModule
 */
class PPImageModule extends FLBuilderModule {

	/**
	 * @property $data
	 */
	public $data = null;

	/**
	 * @property $_editor
	 * @protected
	 */
	protected $_editor = null;

	/**
	 * @property $_photo_type
	 * @protected
	 */
	protected $_photo_type = 'main';

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct( array(
			'name'          	=> __('Image', 'bb-powerpack'),
			'description'   	=> __('Upload a photo or display one from the media library.', 'bb-powerpack'),
			'group'         	=> pp_get_modules_group(),
            'category'			=> pp_get_modules_cat( 'media' ),
			'dir'           	=> BB_POWERPACK_DIR . 'modules/pp-image/',
            'url'           	=> BB_POWERPACK_URL . 'modules/pp-image/',
            'editor_export' 	=> true, // Defaults to true and can be omitted.
            'enabled'       	=> true, // Defaults to true and can be omitted.
			'partial_refresh'	=> true,
		) );
	}

	public function filter_settings( $settings, $helper ) {
		// Handle box old padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'box_padding', 'padding', 'box_padding' );

		// Handle old box border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'box_border'	=> array(
				'type'				=> 'style'
			),
			'border_width'	=> array(
				'type'				=> 'width'
			),
			'border_color'	=> array(
				'type'				=> 'color'
			),
			'border_radius'	=> array(
				'type'				=> 'radius'
			),
			'box_shadow_settings'	=> array(
				'type'				=> 'shadow',
				'condition'			=> ( isset( $settings->box_shadow ) && 'enable' == $settings->box_shadow ),
				'keys'				=> array(
					'horizontal'		=> 'h',
					'vertical'			=> 'v',
					'blur'				=> 'blur',
					'spread'			=> 'spread'
				)
			),
			'box_shadow_color'	=> array(
				'type'				=> 'shadow_color',
				'condition'			=> ( isset( $settings->box_shadow ) && 'enable' == $settings->box_shadow ),
				'opacity'			=> isset( $settings->box_shadow_opacity ) ? $settings->box_shadow_opacity : 1
			)
		), 'box_border_group' );

		// Handle old link, link_target fields.
		$settings = PP_Module_Fields::handle_link_field( $settings, array(
			'link_url'			=> array(
				'type'			=> 'link'
			),
			'link_target'	=> array(
				'type'			=> 'target'
			),
		), 'link' );

		// Handle caption's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'caption_font'	=> array(
				'type'			=> 'font'
			),
			'caption_font_size'	=> array(
				'type'			=> 'font_size',
			),
			'caption_line_height'	=> array(
				'type'			=> 'line_height',
			),
			'caption_alignment'		=> array(
				'type'			=> 'text_align'
			)
		), 'caption_typography' );

		// Handle caption old padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'caption_padding', 'padding', 'caption_padding' );

		// Handle old caption background & text dual color field.
		$settings = PP_Module_Fields::handle_dual_color_field( $settings, 'caption_color', array(
			'primary'	=> 'caption_text_color',
			'secondary'	=> 'caption_bg_color',
			'secondary_opacity' => isset( $settings->caption_opacity ) ? $settings->caption_opacity : 1
		) );

		// Handle caption opacity + color field.
		if ( isset( $settings->caption_opacity ) ) {
			unset( $settings->caption_opacity );
		}

		return $settings;
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		if ( FLBuilderModel::is_builder_active() || ( isset( $this->settings ) && isset( $this->settings->link_type ) && $this->settings->link_type == 'lightbox' ) ) {
			$this->add_js('jquery-magnificpopup');
			$this->add_css('jquery-magnificpopup');
		}
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		// Make sure we have a photo_src property.
		if ( ! isset( $settings->photo_src ) ) {
			$settings->photo_src = '';
		}
		if ( ! isset( $settings->rollover_photo_src ) ) {
			$settings->rollover_photo_src = '';
		}

		// Cache the attachment data.
		$settings->data = new stdClass;
		$settings->data->main = FLBuilderPhoto::get_attachment_data( $settings->photo );

		// Save a crop if necessary.
		$this->crop();

		if ( ! empty( $settings->rollover_photo ) ) {
			$settings->data->rollover = FLBuilderPhoto::get_attachment_data( $settings->rollover_photo );
			$this->set_photo_type( 'rollover' );
			$this->crop();
		}

		return $settings;
	}

	/**
	 * @method delete
	 */
	public function delete() {
		$cropped_path = $this->_get_cropped_path();

		if ( fl_builder_filesystem()->file_exists( $cropped_path['path'] ) ) {
			fl_builder_filesystem()->unlink( $cropped_path['path'] );
		}
	}

	public function set_photo_type( $type ) {
		$this->_photo_type = $type;
	}

	public function get_photo_type() {
		return $this->_photo_type;
	}

	/**
	 * @method crop
	 */
	public function crop() {
		// Delete an existing crop if it exists.
		$this->delete();

		// Do a crop.
		if ( ! empty( $this->settings->crop ) ) {

			$editor = $this->_get_editor();

			if ( ! $editor || is_wp_error( $editor ) ) {
				return false;
			}

			$cropped_path = $this->_get_cropped_path();
			$size         = $editor->get_size();
			$new_width    = $size['width'];
			$new_height   = $size['height'];

			// Get the crop ratios.
			if ( 'landscape' == $this->settings->crop ) {
				$ratio_1 = 1.43;
				$ratio_2 = .7;
			} elseif ( 'panorama' == $this->settings->crop ) {
				$ratio_1 = 2;
				$ratio_2 = .5;
			} elseif ( 'portrait' == $this->settings->crop ) {
				$ratio_1 = .7;
				$ratio_2 = 1.43;
			} elseif ( 'square' == $this->settings->crop ) {
				$ratio_1 = 1;
				$ratio_2 = 1;
			} elseif ( 'circle' == $this->settings->crop ) {
				$ratio_1 = 1;
				$ratio_2 = 1;
			}

			// Get the new width or height.
			if ( $size['width'] / $size['height'] < $ratio_1 ) {
				$new_height = $size['width'] * $ratio_2;
			} else {
				$new_width = $size['height'] * $ratio_1;
			}

			// Make sure we have enough memory to crop.
			try {
				ini_set( 'memory_limit', '300M' );
			} catch ( Exception $e ) {
				//
			}

			// Crop the photo.
			$editor->resize( $new_width, $new_height, true );

			// Save the photo.
			$editor->save( $cropped_path['path'] );

			/**
			 * Let third party media plugins hook in.
			 * @see pp_image_cropped
			 */
			do_action( 'pp_image_cropped', $cropped_path, $editor );

			// Return the new url.
			return $cropped_path['url'];
		}

		return false;
	}

	/**
	 * @method get_data
	 */
	public function get_data() {
		$type = $this->get_photo_type();

		if ( ! $this->data || ( ! isset( $this->data->rollover ) && ! empty( $this->settings->rollover_photo ) ) ) {
			$this->data = ! isset( $this->data->main ) ? new stdClass() : $this->data;
			// Photo source is set to "url".
			if ( 'url' == $this->settings->photo_source ) {
				$this->data->main          = new stdClass();
				$this->data->main->alt     = $this->settings->caption;
				$this->data->main->caption = $this->settings->caption;
				$this->data->main->link    = $this->settings->photo_url;
				$this->data->main->url     = $this->settings->photo_url;
				$this->settings->photo_src = $this->settings->photo_url;
				$this->data->main->title   = ( '' !== $this->settings->url_title ) ? $this->settings->url_title : basename( $this->settings->photo_url );
			} elseif ( 'main' === $type && is_object( $this->settings->photo ) ) {
				$this->data->main = $this->settings->photo;
			} elseif ( 'rollover' === $type && is_object( $this->settings->rollover_photo ) ) {
				$this->data->rollover = $this->settings->rollover_photo;
			} else {
				if ( 'main' === $type ) {
					$this->data->main = FLBuilderPhoto::get_attachment_data( $this->settings->photo );
				}
				if ( 'rollover' === $type ) {
					$this->data->rollover = FLBuilderPhoto::get_attachment_data( $this->settings->rollover_photo );
				}
			}

			// Data object is empty, use the settings cache.
			if ( isset( $this->settings->data ) ) {
				if ( ( ! isset( $this->data->main ) || ! $this->data->main ) && isset( $this->settings->data->main ) ) {
					$this->data->main = $this->settings->data->main;
				}
				if ( ( ! isset( $this->data->rollover ) || ! $this->data->rollover ) && isset( $this->settings->data->rollover ) ) {
					$this->data->rollover = $this->settings->data->rollover;
				}
			}
		}
		/**
		 * Make photo data filterable.
		 * @since 2.2.6
		 * @see pp_image_data
		 */
		return apply_filters( 'pp_image_data', $this->data, $this->settings, $this->node );
	}

	/**
	 * @method get_classes
	 */
	public function get_classes() {
		$classes = array( 'pp-photo-img' );
		$type    = $this->get_photo_type();

		if ( 'library' == $this->settings->photo_source ) {

			$data = self::get_data();

			if ( is_object( $data ) ) {

				if ( 'main' === $type && ! empty( $this->settings->photo ) ) {

					if ( isset( $data->main->id ) ) {
						$classes[] = 'wp-image-' . $data->main->id;
					}

					if ( isset( $data->main->sizes ) ) {

						foreach ( $data->main->sizes as $key => $size ) {

							if ( $size->url == $this->settings->photo_src ) {
								$classes[] = 'size-' . $key;
								break;
							}
						}
					}
				}

				if ( 'rollover' === $type && isset( $this->settings->rollover_photo ) && ! empty( $this->settings->rollover_photo ) ) {
					$classes[] = 'pp-rollover-img';

					if ( isset( $data->rollover->id ) ) {
						$classes[] = 'wp-image-' . $data->rollover->id;
					}

					if ( isset( $data->rollover->sizes ) ) {

						foreach ( $data->rollover->sizes as $key => $size ) {

							if ( $size->url == $this->settings->rollover_photo_src ) {
								$classes[] = 'size-' . $key;
								break;
							}
						}
					}
				}
			}
		}

		$classes = implode( ' ', $classes );

		if ( isset( $this->settings->img_classes ) && ! empty( $this->settings->img_classes ) ) {
			$classes .= $this->settings->img_classes;
		}

		return $classes;
	}

	/**
	 * @method get_src
	 */
	public function get_src() {
		$src = $this->_get_uncropped_url();

		// Return a cropped photo.
		if ( $this->_has_source() && ! empty( $this->settings->crop ) ) {

			$cropped_path = $this->_get_cropped_path();

			if ( fl_builder_filesystem()->file_exists( $cropped_path['path'] ) ) {
				// An existing cropped photo exists.
				$src = $cropped_path['url'];
			} else {

				// A cropped photo doesn't exist, check demo sites then try to create one.
				$post_data    = FLBuilderModel::get_post_data();
				$editing_node = isset( $post_data['node_id'] );
				$demo_domain  = FL_BUILDER_DEMO_DOMAIN;

				if ( ! $editing_node && stristr( $src, $demo_domain ) && ! stristr( $_SERVER['HTTP_HOST'], $demo_domain ) ) {
					$src = $this->_get_cropped_demo_url();
				} elseif ( ! $editing_node && stristr( $src, FL_BUILDER_OLD_DEMO_URL ) ) {
					$src = $this->_get_cropped_demo_url();
				} else {
					$url = $this->crop();
					$src = $url ? $url : $src;
				}
			}
		}

		return $src;
	}

	/**
	 * @method get_link
	 */
	public function get_link() {
		$type  = $this->get_photo_type();
		$photo = $this->get_data();

		if ( 'url' == $this->settings->link_type ) {
			$link = $this->settings->link;
		} elseif ( isset( $photo->{$type} ) && 'lightbox' == $this->settings->link_type ) {
			$link = $photo->{$type}->url;
		} elseif ( isset( $photo->{$type} ) && 'file' == $this->settings->link_type ) {
			$link = $photo->{$type}->url;
		} elseif ( isset( $photo->{$type} ) && 'page' == $this->settings->link_type ) {
			$link = $photo->{$type}->link;
		} else {
			$link = '';
		}

		return $link;
	}

	/**
	 * @method get_alt
	 */
	public function get_alt() {
		$type  = $this->get_photo_type();
		$photo = $this->get_data();

		if ( ! empty( $photo->{$type}->alt ) ) {
			return htmlspecialchars( $photo->{$type}->alt );
		} elseif ( ! empty( $photo->{$type}->description ) ) {
			return htmlspecialchars( $photo->{$type}->description );
		} elseif ( ! empty( $photo->{$type}->caption ) ) {
			return htmlspecialchars( $photo->{$type}->caption );
		} elseif ( ! empty( $photo->{$type}->title ) ) {
			return htmlspecialchars( $photo->{$type}->title );
		}
	}

	/**
	 * @method get_caption
	 */
	public function get_caption() {
		$type    = $this->get_photo_type();
		$photo   = $this->get_data();
		$caption = '';

		if ( $photo && ! empty( $this->settings->show_caption ) && ! empty( $photo->{$type}->caption ) ) {
			if ( isset( $photo->{$type}->id ) ) {
				$caption = wp_kses_post( wp_get_attachment_caption( $photo->{$type}->id ) );
			} else {
				$caption = esc_html( $photo->{$type}->caption );
			}
		}

		return $caption;
	}

	/**
	 * @method get_attributes
	 */
	public function get_attributes() {
		$type  = $this->get_photo_type();
		$photo = $this->get_data();
		$attrs = '';

		if ( isset( $this->settings->attributes ) ) {
			foreach ( $this->settings->attributes as $key => $val ) {
				$attrs .= $key . '="' . $val . '" ';
			}
		}

		if ( is_object( $photo ) && isset( $photo->{$type}->sizes ) ) {
			$current_size = array();
			foreach ( $photo->{$type}->sizes as $size ) {
				$src = 'main' === $type ? $this->settings->photo_src : $this->settings->rollover_photo_src;
				if ( $size->url == $src && isset( $size->width ) && isset( $size->height ) ) {
					$attrs .= 'height="' . $size->height . '" width="' . $size->width . '" ';
					$current_size[] = $size->width;
					$current_size[] = $size->height;
				}
			}

			if ( empty( $current_size ) ) {
				$current_size = 'full';
			}

			if ( '' === $this->settings->crop ) {
				$srcset = wp_get_attachment_image_srcset( $photo->{$type}->id, $current_size );
				$sizes = wp_get_attachment_image_sizes( $photo->{$type}->id, $current_size );

				if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
                    $attrs .= 'srcset="' . $srcset . '" ';
                    $attrs .= 'sizes="' . $sizes . '" ';
                }
			}
		}

		if ( ! empty( $photo->{$type}->title ) ) {
			$attrs .= 'title="' . htmlspecialchars( $photo->{$type}->title ) . '" ';
		}

		if ( FLBuilderModel::is_builder_active() ) {
			$attrs .= 'onerror="this.style.display=\'none\'" ';
		}

		/**
		 * Filter image attributes as a string.
		 * @since 2.18.5
		 * @see pp_image_html_attrs
		 */
		return apply_filters( 'pp_image_html_attrs', $attrs );
	}

	/**
	 * @method _has_source
	 * @protected
	 */
	protected function _has_source() {
		$type = $this->get_photo_type();

		if ( 'url' == $this->settings->photo_source && ! empty( $this->settings->photo_url ) ) {
			return true;
		} elseif ( 'library' == $this->settings->photo_source && 'main' === $type && ! empty( $this->settings->photo_src ) ) {
			return true;
		} elseif ( 'library' == $this->settings->photo_source && 'rollover' === $type && ! empty( $this->settings->rollover_photo_src ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @method _get_editor
	 * @protected
	 */
	protected function _get_editor() {
		$type = $this->get_photo_type();

		if ( $this->_has_source() && ( null === $this->_editor || ( is_object( $this->_editor ) && isset( $this->_editor->_photo_type ) && $type !== $this->_editor->_photo_type ) ) ) {
			$url_path  = $this->_get_uncropped_url();
			$file_path = $this->_get_file_path( $url_path );

			if ( is_multisite() && ! is_subdomain_install() ) {
				// take the original url_path and make a cleaner one, then rebuild file_path

				$subsite_path          = get_blog_details()->path;
				$url_parsed_path       = wp_parse_url( $url_path, PHP_URL_PATH );
				$url_parsed_path_parts = explode( '/', $url_parsed_path );

				if ( isset( $url_parsed_path_parts[1] ) && "/{$url_parsed_path_parts[1]}/" === $subsite_path ) {

					$path_right_half  = wp_make_link_relative( $url_path );
					$path_left_half   = str_replace( $path_right_half, '', $url_path );
					$path_right_half2 = str_replace( $subsite_path, '', $path_right_half );

					// rebuild file_path using a cleaner URL as input
					$url_path2 = $path_left_half . '/' . $path_right_half2;
					$file_path = $this->_get_file_path( $url_path2 );
				}
			}

			if ( file_exists( $file_path ) ) {
				$this->_editor = wp_get_image_editor( $file_path );
			} else {
				if ( ! is_wp_error( wp_safe_remote_head( $url_path, array( 'timeout' => 5 ) ) ) ) {
					$this->_editor = wp_get_image_editor( $url_path );
				}
			}
		}

		if ( ! is_wp_error( $this->_editor ) && ! empty( $this->_editor ) ) {
			$this->_editor->_photo_type = $type;
		}

		return $this->_editor;
	}

	/**
	 * Make path filterable.
	 * @since 2.18.5
	 */
	public static function _get_file_path( $url_path ) {
		return apply_filters( 'pp_image_crop_path', str_ireplace( home_url(), ABSPATH, $url_path ), $url_path );
	}

	/**
	 * @method _get_cropped_path
	 * @protected
	 */
	protected function _get_cropped_path() {
		$crop      = empty( $this->settings->crop ) ? 'none' : $this->settings->crop;
		$url       = $this->_get_uncropped_url();
		$cache_dir = FLBuilderModel::get_cache_dir();

		if ( empty( $url ) ) {
			$filename = uniqid(); // Return a file that doesn't exist.
		} else {

			if ( stristr( $url, '?' ) ) {
				$parts = explode( '?', $url );
				$url   = $parts[0];
			}

			$pathinfo = pathinfo( $url );

			if ( isset( $pathinfo['extension'] ) ) {
				$dir      = $pathinfo['dirname'];
				$ext      = $pathinfo['extension'];
				$name     = wp_basename( $url, ".$ext" );
				$new_ext  = strtolower( $ext );
				$filename = "{$name}-{$crop}.{$new_ext}";
			} else {
				$filename = $pathinfo['filename'] . "-{$crop}.png";
			}
		}

		return array(
			'filename' => $filename,
			'path'     => $cache_dir['path'] . $filename,
			'url'      => $cache_dir['url'] . $filename,
		);
	}

	/**
	 * @method _get_uncropped_url
	 * @protected
	 */
	protected function _get_uncropped_url() {
		$type = $this->get_photo_type();

		if ( 'url' == $this->settings->photo_source ) {
			$url = $this->settings->photo_url;
		} elseif ( 'main' === $type && ! empty( $this->settings->photo_src ) ) {
			$url = $this->settings->photo_src;
		} elseif ( 'rollover' === $type && ! empty( $this->settings->rollover_photo_src ) ) {
			$url = $this->settings->rollover_photo_src;
		} else {
			$url = apply_filters( 'pp_image_noimage', FL_BUILDER_URL . 'img/pixel.png' );
		}

		return $url;
	}

	/**
	 * @method _get_cropped_demo_url
	 * @protected
	 */
	protected function _get_cropped_demo_url() {
		$type = $this->get_photo_type();
		$info = $this->_get_cropped_path();
		$src  = $this->settings->photo_src;

		if ( 'rollover' === $type ) {
			$src = $this->settings->rollover_photo_src;
		}

		// Pull from a demo subsite.
		if ( stristr( $src, '/uploads/sites/' ) ) {
			$url_parts  = explode( '/uploads/sites/', $src );
			$site_parts = explode( '/', $url_parts[1] );
			return $url_parts[0] . '/uploads/sites/' . $site_parts[0] . '/bb-plugin/cache/' . $info['filename'];
		}

		// Pull from the demo main site.
		return FL_BUILDER_DEMO_CACHE_URL . $info['filename'];
	}

	/**
	 * Returns button link rel based on settings
	 * @since 2.6.9
	 */
	public function get_rel() {
		$rel = array();
		if ( '_blank' == $this->settings->link_target ) {
			$rel[] = 'noopener';
		}
		if ( isset( $this->settings->link_nofollow ) && 'yes' == $this->settings->link_nofollow ) {
			$rel[] = 'nofollow';
		}

		$rel = apply_filters( 'pp_link_rel_attrs', $rel, $this->settings );
		$rel = implode( ' ', $rel );

		if ( $rel ) {
			$rel = ' rel="' . $rel . '" ';
		}
		return $rel;
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module('PPImageModule', array(
	'general'       => array( // Tab
		'title'         => __('General', 'bb-powerpack'), // Tab title
		'sections'      => array( // Tab Sections
			'general'       => array( // Section
				'title'         => '', // Section Title
				'fields'        => array( // Section Fields
					'photo_source'  => array(
						'type'          => 'pp-switch',
						'label'         => __('Image Source', 'bb-powerpack'),
						'default'       => 'library',
						'options'       => array(
							'library'       => __('Media Library', 'bb-powerpack'),
							'url'           => __('URL', 'bb-powerpack')
						),
						'toggle'        => array(
							'library'       => array(
								'fields'        => array('photo', 'rollover_photo')
							),
							'url'           => array(
								'fields'        => array('photo_url', 'caption', 'url_title'),
							)
						)
					),
					'photo'         => array(
						'type'          => 'photo',
						'label'         => __('Image', 'bb-powerpack'),
						'show_remove'   => true,
						'connections'   => array( 'photo' ),
					),
					'rollover_photo'         => array(
						'type'          => 'photo',
						'label'         => __('Rollover Image', 'bb-powerpack'),
						'show_remove'   => true,
						'connections'   => array( 'photo' ),
					),
					'photo_url'     => array(
						'type'          => 'text',
						'label'         => __('Image URL', 'bb-powerpack'),
						'placeholder'   => __( 'http://www.example.com/my-photo.jpg', 'bb-powerpack' ),
						'connections'   => array( 'url' ),
					),
					'url_title'    => array(
						'type'        => 'text',
						'label'       => __( 'Image title attribute', 'bb-powerpack' ),
						'default'     => '',
						'placeholder' => __( 'Use image filename if left blank', 'bb-powerpack' ),
					),
					'photo_size'	=> array(
						'type'			=> 'unit',
						'label'			=> __('Custom Photo Size', 'bb-powerpack'),
						'default'		=> '',
						'units'			=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-photo-container .pp-photo-content .pp-photo-content-inner img',
							'property'		=> 'width',
							'unit'			=> 'px'
						)
					),
					'crop'          => array(
						'type'          => 'select',
						'label'         => __('Crop', 'bb-powerpack'),
						'default'       => '',
						'options'       => array(
							''              => _x( 'None', 'Photo Crop.', 'bb-powerpack' ),
							'landscape'     => __('Landscape', 'bb-powerpack'),
							'panorama'      => __('Panorama', 'bb-powerpack'),
							'portrait'      => __('Portrait', 'bb-powerpack'),
							'square'        => __('Square', 'bb-powerpack'),
							'circle'        => __('Circle', 'bb-powerpack')
						)
					),
					'align'         => array(
						'type'          => 'pp-switch',
						'label'         => __('Alignment', 'bb-powerpack'),
						'default'       => 'center',
						'options'       => array(
							'left'          => __('Left', 'bb-powerpack'),
							'center'        => __('Center', 'bb-powerpack'),
							'right'         => __('Right', 'bb-powerpack')
						),
					),
					'align_responsive'  => array(
						'type'          => 'pp-switch',
						'label'         => __('Responsive Alignment', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'		=> __('Default', 'bb-powerpack'),
							'left'          => __('Left', 'bb-powerpack'),
							'center'        => __('Center', 'bb-powerpack'),
							'right'         => __('Right', 'bb-powerpack')
						),
					)
				)
			),
			'caption'       => array(
				'title'         => __('Caption', 'bb-powerpack'),
				'fields'        => array(
					'caption'       => array(
						'type'          => 'text',
						'label'         => __('Custom Caption', 'bb-powerpack'),
						'connections'   => array( 'string' ),
						'help' => __( 'Add caption here when you choose URL as Photo Source above.', 'bb-powerpack' ),
					),
					'show_caption'  => array(
						'type'          => 'pp-switch',
						'label'         => __('Show Caption', 'bb-powerpack'),
						'description'   => __( 'Please note that "On Hover" and "Overlay" will NOT work when Rollover Image is provided.', 'bb-powerpack' ),
						'default'       => 'never',
						'options'       => array(
							'never'         => __('Never', 'bb-powerpack'),
							'hover'         => __('On Hover', 'bb-powerpack'),
							'below'         => __('Below Photo', 'bb-powerpack'),
							'overlay'       => __('Overlay', 'bb-powerpack')
						),
						'toggle'		=> array(
							'hover'		=> array(
								'fields'	=> array('hover_margin')
							),
						),
					),
				)
			),
			'link'          => array(
				'title'         => __('Link', 'bb-powerpack'),
				'fields'        => array(
					'link_type'     => array(
						'type'          => 'select',
						'label'         => __('Link Type', 'bb-powerpack'),
						'options'       => array(
							''              => _x( 'None', 'Link type.', 'bb-powerpack' ),
							'url'           => __('URL', 'bb-powerpack'),
							'lightbox'      => __('Lightbox', 'bb-powerpack'),
							'file'          => __('Photo File', 'bb-powerpack'),
							'page'          => __('Photo Page', 'bb-powerpack')
						),
						'toggle'        => array(
							''              => array(),
							'url'           => array(
								'fields'        => array('link')
							),
							'file'          => array(),
							'page'          => array()
						),
						'help'          => __('Link type applies to how the image should be linked on click. You can choose a specific URL, the individual photo or a separate page with the photo.', 'bb-powerpack'),
						'preview'         => array(
							'type'            => 'none'
						)
					),
					'link'  => array(
						'type'          => 'link',
						'label'         => __('Link', 'bb-powerpack'),
						'placeholder'   => 'http://www.example.com',
						'show_target'	=> true,
						'show_nofollow'	=> true,
						'connections'   => array( 'url' ),
						'preview'       => array(
							'type'          => 'none'
						)
					),
				)
			)
		)
	),
	'style_tab'		=> array(
		'title'			=> __('Style', 'bb-powerpack'),
		'sections'		=> array(
			'image_box_style' 	=> array(
				'title'		=> __('Image Box', 'bb-powerpack'),
				'fields'	=> array(
					'box_padding'	=> array(
						'type'				=> 'dimension',
						'label'				=> __('Padding', 'bb-powerpack'),
						'default'			=> '0',
						'units'				=> array('px'),
						'slider'			=> true,
						'responsive'		=> true,
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-photo-container .pp-photo-content',
							'property'			=> 'padding',
							'unit'				=> 'px'
						)
					),
					'box_background' 		=> array(
						'type'			=> 'color',
						'label'			=> __('Background Color', 'bb-powerpack'),
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'help'			=> __('Background color will be visible between the image and outside edge of the container when you increase the padding.', 'bb-powerpack'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-photo-container .pp-photo-content',
							'property'		=> 'background-color'
						),
					),
					'box_border_group'	=> array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack' ),
						'responsive'	=> true,
					),
					'box_border_hover_color'	=> array(
						'type'				=> 'color',
						'label'				=> __('Border Hover Color', 'bb-powerpack'),
						'default'			=> '',
						'show_reset'		=> true,
						'connections'		=> array('color'),
					)
				),
			),
			'image_border'		=> array(
				'title'			=> __('Image', 'bb-powerpack'),
				'fields'		=> array(
					'image_border_type'	=> array(
						'type'		=> 'pp-switch',
						'label'		=> __('Border Style', 'bb-powerpack'),
						'default'	=> 'outside',
						'options'	=> array(
							'inside'	=> __('Inside', 'bb-powerpack'),
							'outside'	=> __('Outside', 'bb-powerpack'),
						),
						'toggle'	=> array(
							'inside'	=> array(
								'fields'	=> array('image_spacing')
							)
						)
					),
					'image_border_style'	=> array(
						'type'		=> 'pp-switch',
						'label'		=> __('Border Style', 'bb-powerpack'),
						'default'	=> 'none',
						'options'	=> array(
							'none'	=> __('None', 'bb-powerpack'),
							'solid'	=> __('Solid', 'bb-powerpack'),
							'dashed'	=> __('Dashed', 'bb-powerpack'),
							'dotted'	=> __('Dotted', 'bb-powerpack'),
						),
						'toggle'	=> array(
							'solid'	=> array(
								'fields'	=> array('image_border_width', 'image_border_color')
							),
							'dashed'	=> array(
								'fields'	=> array('image_border_width', 'image_border_color')
							),
							'dotted'	=> array(
								'fields'	=> array('image_border_width', 'image_border_color')
							),
						)
					),
					'image_border_width'	=> array(
						'type'		=> 'unit',
						'label'		=> __('Border Width', 'bb-powerpack'),
						'default'	=> 1,
						'units'		=> array( 'px' ),
						'slider'	=> true,
						'preview'	=> array(
							'type'	=> 'css',
							'rules'	=> array(
								array(
									'selector'	=> '.pp-photo-container .pp-photo-content .pp-photo-content-inner:before',
									'property'	=> 'border-width',
									'unit'		=> 'px'
								),
								array(
									'selector'	=> '.pp-photo-container .pp-photo-content .pp-photo-content-inner a:before',
									'property'	=> 'border-width',
									'unit'		=> 'px'
								),
							)
						)
					),
					'image_border_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Border Color', 'bb-powerpack'),
						'show_reset'	=> true,
						'default'		=> '000000',
						'connections'	=> array('color'),
						'preview'	=> array(
							'type'	=> 'css',
							'rules'	=> array(
								array(
									'selector'	=> '.pp-photo-container .pp-photo-content .pp-photo-content-inner:before',
									'property'	=> 'border-color'
								),
								array(
									'selector'	=> '.pp-photo-container .pp-photo-content .pp-photo-content-inner a:before',
									'property'	=> 'border-color'
								),
							)
						)
					),
					'image_spacing'			=> array(
						'type'		=> 'unit',
						'label'		=> __('Spacing', 'bb-powerpack'),
						'default'	=> 10,
						'units'		=> array( 'px' ),
						'slider'	=> true,
						'preview'	=> array(
							'type'	=> 'css',
							'selector'	=> '.pp-photo-content-inner:before',
							'property'	=> 'margin',
							'unit'		=> 'px'
						)
					),
					'show_image_effect'		=> array(
						'type'					=> 'pp-switch',
						'label'					=> __('Show Image Effects', 'bb-powerpack'),
						'default'				=> 'no',
						'options'				=> array(
							'yes'					=> __('Yes', 'bb-powerpack'),
							'no'					=> __('No', 'bb-powerpack'),
						),
						'toggle'				=> array(
							'yes'				=> array(
								'sections'				=> array('image_effects','image_hover_effects')
							)
						)
					),
				)
			),
			'image_effects'		=> array(
				'title'				=> __('Image Effects', 'bb-powerpack'),
				'collapsed'			=> true,
				'fields'			=> pp_image_effect_fields(),
			),
			'image_hover_effects'=> array(
				'title'				=> __('Image Effects on Hover', 'bb-powerpack'),
				'collapsed'			=> true,
				'fields'			=> pp_image_effect_fields(true),
			)
		),
	),
	'caption'	=> array(
		'title'	=> __('Caption', 'bb-powerpack'),
		'sections'	=> array(
			'colors'		=> array(
				'title'		=> __('General', 'bb-powerpack'),
				'fields'	=> array(
					'caption_text_color' => array(
						'type'              => 'color',
						'label'             => __('Text Color', 'bb-powerpack'),
						'default'           => '000000',
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-photo-caption',
							'property'			=> 'color',
						)
					),
					'caption_bg_color' => array(
						'type'              => 'color',
						'label'             => __('Background Color', 'bb-powerpack'),
						'default'           => 'dddddd',
						'show_reset'		=> true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-photo-caption',
							'property'			=> 'background-color',
						)
					),
					'caption_padding'	=> array(
						'type'				=> 'dimension',
						'label'				=> __('Padding', 'bb-powerpack'),
						'default'			=> '10',
						'units'				=> array('px'),
						'slider'			=> true,
						'responsive'		=> true,
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-photo-caption',
							'property'			=> 'padding',
							'unit'				=> 'px'
						)
					),
					'hover_margin'	=> array(
						'type'			=> 'unit',
						'label'			=> __('Hover Overlay Margin', 'bb-powerpack'),
						'default'		=> '0',
						'units'				=> array('px'),
						'slider'			=> true,
					)
				),
			),
			'typography'	=> array(
				'title'	=> __('Typography', 'bb-powerpack'),
				'fields'	=> array(
					'caption_typography'	=> array(
						'type'			=> 'typography',
						'label'			=> __('Typography', 'bb-powerpack'),
						'responsive'  	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-photo-caption',
						),
					),
				),
			),
		),
	),
));
