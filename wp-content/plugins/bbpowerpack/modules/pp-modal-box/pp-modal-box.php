<?php

/**
 * @class PPModalBoxModule
 */
class PPModalBoxModule extends FLBuilderModule {

	private $cached_content = array();

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Modal Box', 'bb-powerpack'),
            'description'   => __('Custom modal boxes with animation.', 'bb-powerpack'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'lead_gen' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-modal-box/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-modal-box/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));
    }

	public function enqueue_scripts() {
		$this->add_js( 'jquery-cookie' );
	}

	public function enqueue_icon_styles() {
		$enqueue = false;
		$settings = $this->settings;

		if ( 'icon' === $settings->button_type && ! empty( $settings->icon_source ) ) {
			$enqueue = true;
		}

		if ( 'button' === $settings->button_type && ! empty( $settings->button_icon_src ) ) {
			$enqueue = true;
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function filter_settings( $settings, $helper ) {
		// Handle button's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'button_font_family'	=> array(
				'type'			=> 'font'
			),
			'button_font_size'	=> array(
				'type'			=> 'font_size',
			),
		), 'button_typography' );

		// Handle button opacity + color field.
		if ( isset( $settings->button_opacity ) ) {
			$opacity = $settings->button_opacity >= 0 ? $settings->button_opacity : 1;
			$color = $settings->button_color;

			if ( ! empty( $color ) ) {
				$color = pp_hex2rgba( pp_get_color_value( $color ), $opacity );
				$settings->button_color = $color;
			}

			unset( $settings->button_opacity );
		}

		// Handle button hover opacity + color field.
		if ( isset( $settings->button_opacity_hover ) ) {
			$opacity = $settings->button_opacity_hover >= 0 ? $settings->button_opacity_hover : 1;
			$color = $settings->button_color_hover;

			if ( ! empty( $color ) ) {
				$color = pp_hex2rgba( pp_get_color_value( $color ), $opacity );
				$settings->button_color_hover = $color;
			}

			unset( $settings->button_opacity_hover );
		}

		// Handle old button border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'button_border_width'	=> array(
				'type'				=> 'width',
				'condition'			=> ( isset( $settings->button_border ) && 'yes' == $settings->button_border ),
			),
			'button_border_color'	=> array(
				'type'				=> 'color',
				'condition'			=> ( isset( $settings->button_border ) && 'yes' == $settings->button_border ),
			),
			'button_border_radius'	=> array(
				'type'				=> 'radius'
			),
		), 'button_border_group' );

		// Handle button old padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'button_padding', 'padding', 'button_padding' );

		// Handle title's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'title_font_family'	=> array(
				'type'			=> 'font'
			),
			'title_font_size'	=> array(
				'type'			=> 'font_size',
			),
			'title_alignment'	=> array(
				'type'			=> 'text_align'
			)
		), 'title_typography' );

		// Handle modal opacity + color field.
		if ( isset( $settings->modal_bg_opacity ) ) {
			$opacity = $settings->modal_bg_opacity >= 0 ? $settings->modal_bg_opacity : 1;
			$color = $settings->modal_bg_color;

			if ( ! empty( $color ) ) {
				$color = pp_hex2rgba( pp_get_color_value( $color ), $opacity );
				$settings->modal_bg_color = $color;
			}

			unset( $settings->modal_bg_opacity );
		}

		// Handle old box border and shadow fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'modal_border'		=> array(
				'type'				=> 'style',
			),
			'modal_border_width'	=> array(
				'type'				=> 'width',
			),
			'modal_border_color'	=> array(
				'type'				=> 'color',
			),
			'modal_border_radius'	=> array(
				'type'				=> 'radius'
			),
		), 'modal_border_group' );

		if ( isset( $settings->modal_border_position ) && isset( $settings->modal_border_group ) ) {
			$border_width = $settings->modal_border_group['width'];
			if ( 'top' == $settings->modal_border_position ) {
				$border_width['right']  = '';
				$border_width['bottom'] = '';
				$border_width['left']   = '';
			}
			if ( 'right' == $settings->modal_border_position ) {
				$border_width['top']    = '';
				$border_width['bottom'] = '';
				$border_width['left']   = '';
			}
			if ( 'bottom' == $settings->modal_border_position ) {
				$border_width['top']   = '';
				$border_width['right'] = '';
				$border_width['left']  = '';
			}
			if ( 'left' == $settings->modal_border_position ) {
				$border_width['top']    = '';
				$border_width['right']  = '';
				$border_width['bottom'] = '';
			}

			$settings->modal_border_group['width'] = $border_width;
			unset( $settings->modal_border_position );
		}

		if ( isset( $settings->modal_shadow ) && 'yes' == $settings->modal_shadow && isset( $settings->box_shadow_h, $settings->box_shadow_v ) ) {
			$box_shadow = array(
				'horizontal' => $settings->box_shadow_h,
				'vertical'   => $settings->box_shadow_v,
				'blur'       => $settings->box_shadow_blur,
				'spread'     => $settings->box_shadow_spread
			);

			if ( isset( $settings->box_shadow_color ) && ! empty( $settings->box_shadow_color ) ) {
				$opacity = '' !== $settings->box_shadow_opacity ? $settings->box_shadow_opacity : 1;
				$box_shadow['color'] = pp_hex2rgba( pp_get_color_value( $settings->box_shadow_color ), $opacity );

				unset( $settings->box_shadow_color );
				unset( $settings->box_shadow_opacity );
			}

			unset( $settings->box_shadow_h, $settings->box_shadow_v );
			unset( $settings->box_shadow_blur, $settings->box_shadow_spread );
			unset( $settings->modal_shadow );

			$settings->modal_border_group['shadow'] = $box_shadow;
		}

		// Handle old content border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'content_border'		=> array(
				'type'				=> 'style',
			),
			'content_border_width'	=> array(
				'type'				=> 'width',
			),
			'content_border_color'	=> array(
				'type'				=> 'color',
			),
			'content_border_radius'	=> array(
				'type'				=> 'radius'
			),
		), 'content_border_group' );

		// Handle overlay opacity + color field.
		if ( isset( $settings->overlay_opacity ) ) {
			$opacity = $settings->overlay_opacity >= 0 ? $settings->overlay_opacity : 1;
			$color = $settings->overlay_bg_color;

			if ( ! empty( $color ) ) {
				$color = pp_hex2rgba( pp_get_color_value( $color ), $opacity );
				$settings->overlay_bg_color = $color;
			}

			unset( $settings->overlay_opacity );
		}

		if ( isset( $settings->builder_label ) && ! empty( $settings->builder_label ) ) {
			if ( isset( $settings->node_label ) && empty( $settings->node_label ) ) {
				$settings->node_label = $settings->builder_label;
				unset( $settings->builder_label );
			}
		}

		// Combine two cookie fields.
		if ( 'auto' === $settings->modal_load && isset( $settings->display_after_auto ) ) {
			$settings->display_after = $settings->display_after_auto;
			unset( $settings->display_after_auto );
		}

		return $settings;
	}

	public function update( $settings ) {
		if ( ! isset( $settings->cookie_value ) ) {
			$settings->cookie_value = 1;
		}
		if ( isset( $settings->reset_cookie ) && 'yes' === $settings->reset_cookie ) {
			$settings->cookie_value = $settings->cookie_value + 1;
		}

		return $settings;
	}

    public static function get_saved_templates() {
        if ( is_admin() && isset( $_GET['page'] ) && 'ppbb-settings' == $_GET['page'] ) {
            return;
        }

        $templates = get_posts( array(
			'post_type' 				=> 'fl-builder-template',
			'orderby' 					=> 'title',
			'order' 					=> 'ASC',
			'posts_per_page' 			=> '-1'
		) );

        $options = array();

        if ( count( $templates ) ) {
            foreach ( $templates as $template ) {
                $options[ $template->ID ] = $template->post_title;
            }
        }

        return $options;
    }

    public function get_modal_content( $settings ) {
        $modal_type = $settings->modal_type;
		$content    = '';

        switch ( $modal_type ) {
            case 'photo':
                if ( isset( $settings->modal_type_photo_src ) ) {
					$alt = $this->get_alt( $settings->modal_type_photo );
                    $content = '<img src="' . esc_url( $settings->modal_type_photo_src ) . '" class="pp-modal-img" style="max-width: 100%;" alt="' . esc_attr( $alt ) . '" />';
                }
            	break;
            case 'video':
                global $wp_embed;
				$content = '<div class="pp-modal-video-embed">';
                $content .= $wp_embed->autoembed($settings->modal_type_video);
				$content .= '</div>';
            	break;
            case 'url':
				$is_video = $this->is_video_url( $settings->modal_type_url );
				$content = $is_video ? '<div class="pp-modal-video-embed">' : '';
                $content .= '<iframe data-url="' . esc_url( $settings->modal_type_url ) . '" class="pp-modal-iframe" frameborder="0" width="100%" height="100%"></iframe>';
				$content .= $is_video ? '</div>' : '';
            	break;
            case 'content':
                $content = wpautop( $settings->modal_type_content );
            	break;
            case 'html':
                $content = $settings->modal_type_html;
				if ( preg_match( '/<!--([\s\S]*?)pp-modal-post-template([\s\S]*?)\/?-->/', $content ) ) {
					$content = '<div style="text-align: center;">';
					$content .= '<img src="' . BB_POWERPACK_URL . 'assets/images/ajax-loader.gif' . '" alt="loader" />';
					$content .= '</div>';
				}
            	break;
            default:
            	break;
        }

		return apply_filters( 'pp_modal_box_content', $content, $settings );
    }

	public function render_post_content( $post_id ) {
		global $post;

		if ( $post instanceof WP_Post && $post->ID == $post_id ) {
            if ( isset( $_GET['fl_builder'] ) ) {
                echo esc_html__( 'You cannot use the current post as template.', 'bb-powerpack' );
            }
            return;
		}

		pp_render_post_content( $post_id );
	}

	public function is_video_url( $url ) {
		$regex = '/^.*((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube(-nocookie)?\.com|youtu.be|vimeo\.com|dailymotion\.com|wistia\.com|wistia\.net))(\S+)?$/';
		$is_video = false;

		if ( preg_match( $regex, $url ) ) {
			$is_video = true;
		}

		return $is_video;
	}

	public function get_alt( $attachment_id ) {
		$photo = FLBuilderPhoto::get_attachment_data( $attachment_id );

		if ( ! empty( $photo->alt ) ) {
			return htmlspecialchars( $photo->alt );
		}
		elseif ( ! empty( $photo->description ) ) {
			return htmlspecialchars( $photo->description );
		}
		elseif ( ! empty( $photo->caption ) ) {
			return htmlspecialchars( $photo->caption );
		}
		elseif ( ! empty( $photo->title ) ) {
			return htmlspecialchars( $photo->title );
		}
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module('PPModalBoxModule', array(
    'general'       => array( // Tab
        'title'         => __('General', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'modal_box'       => array( // Section
                'title'             => __('Modal', 'bb-powerpack'), // Section Title
                'fields'            => array( // Section Fields
                    'modal_layout'      => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Layout', 'bb-powerpack'),
                        'default'           => 'standard',
                        'options'           => array(
                            'standard'          => __('Standard', 'bb-powerpack'),
                            'fullscreen'        => __('Fullscreen', 'bb-powerpack')
                        ),
                        'toggle'            => array(
                            'standard'          => array(
                                'fields'            => array('modal_border', 'modal_border_radius', 'modal_width', 'modal_height_auto'),
                            ),
                            'fullscreen'        => array(
                                'fields'            => array('modal_margin_top', 'modal_margin_bottom', 'modal_margin_left', 'modal_margin_right')
                            )
                        ),
                        'hide'              => array(
                            'fullscreen'        => array(
                                'fields'            => array('modal_border', 'modal_border_radius')
                            )
                        ),
                        'help'              => __('Stying options are available in Modal Style tab.', 'bb-powerpack')
                    ),
                    'modal_width'       => array(
                        'type'              => 'unit',
                        'label'             => __('Width', 'bb-powerpack'),
                        'units'       		=> array( 'px', '%', 'vw', 'em', 'rem' ),
                        'slider'            => array(
							'min'				=> 1,
							'max'				=> 1000,
							'step'				=> 1
						),
                        'default'           => 550,
                    ),
                    'modal_height_auto' => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Auto Height', 'bb-powerpack'),
                        'default'       => 'yes',
                        'options'       => array(
                            'yes'           => __('Yes', 'bb-powerpack'),
                            'no'            => __('No', 'bb-powerpack')
                        ),
                        'toggle'        => array(
                            'no'            => array(
                                'fields'        => array('modal_height')
                            )
                        )
                    ),
                    'modal_height'      => array(
                        'type'              => 'unit',
                        'label'             => __('Height', 'bb-powerpack'),
                        'default'           => 450,
						'units'       		=> array( 'px' ),
                        'slider'            => true,
                        'responsive'        => true,
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal',
                            'property'          => 'height',
                            'unit'              => 'px'
                        )
                    ),
                    'modal_preview'         => array(
                        'type'                  => 'pp-switch',
                        'label'                 => __('Show Preview', 'bb-powerpack'),
                        'default'               => 'enabled',
                        'options'               => array(
                            'enabled'               => __('Yes', 'bb-powerpack'),
                            'disabled'              => __('No', 'bb-powerpack')
                        ),
                        'help'                  => __('You will be able to see the modal box preview by enabling this option.', 'bb-powerpack')
                    ),
                )
            ),
            'modal_content' => array(
                'title'         => __('Content', 'bb-powerpack'),
                'fields'        => array(
                    'modal_title_toggle'    => array(
                        'type'                  => 'pp-switch',
                        'label'                 => __('Enable Title', 'bb-powerpack'),
                        'default'               => 'yes',
                        'options'               => array(
                            'yes'                   => __('Yes', 'bb-powerpack'),
                            'no'                    => __('No', 'bb-powerpack')
                        ),
                        'toggle'                => array(
                            'yes'                   => array(
                                'fields'                => array('modal_title'),
                                'sections'              => array('modal_title')
                            )
                        ),
                        'trigger'               => array(
                            'yes'                   => array(
                                'fields'                => array('button_type')
                            ),
                            'no'                    => array(
                                'fields'                => array('button_type')
                            )
                        )
                    ),
                    'modal_title'      => array(
                        'type'          => 'text',
                        'label'         => __('Title', 'bb-powerpack'),
                        'default'       => __('Modal Title', 'bb-powerpack'),
                        'connections'   => array( 'string', 'html', 'url' ),
                        'preview'       => array(
                            'type'          => 'text',
                            'selector'      => '.pp-modal-title'
                        )
                    ),
                    'modal_type'       => array(
                        'type'          => 'select',
                        'label'         => __('Type', 'bb-powerpack'),
                        'default'       => 'photo',
                        'options'       => array(
                            'photo'         => __('Photo', 'bb-powerpack'),
                            'video'         => __('Video', 'bb-powerpack'),
                            'url'           => __('URL', 'bb-powerpack'),
                            'content'       => __('Content', 'bb-powerpack'),
                            'html'          => __('Raw HTML', 'bb-powerpack'),
                            'templates'     => __('Saved Templates', 'bb-powerpack')
                        ),
                        'toggle'        => array(
                            'photo'        => array(
                                'fields'        => array('modal_type_photo')
                            ),
                            'video'         => array(
                                'fields'        => array('modal_type_video')
                            ),
                            'url'           => array(
                                'fields'        => array('modal_type_url')
                            ),
                            'content'       => array(
                                'fields'        => array('modal_type_content')
                            ),
                            'html'          => array(
                                'fields'        => array('modal_type_html')
                            ),
                            'templates'     => array(
                                'fields'        => array('modal_type_templates', 'modal_template_edit')
                            )
                        )
                    ),
                    'modal_type_photo'     => array(
                        'type'                  => 'photo',
                        'label'                 => __('Photo', 'bb-powerpack'),
                        'connections'           => array( 'photo' ),
                    ),
                    'modal_type_video'     => array(
                        'type'                  => 'textarea',
                        'label'                 => __('Embed Code / URL', 'bb-powerpack'),
                        'rows'                  => 6,
						'connections' => array( 'string', 'html' )
                    ),
                    'modal_type_url'       => array(
                        'type'                  => 'text',
                        'label'                 => __('URL', 'bb-powerpack'),
                        'placeholder'           => 'http://www.example.com',
                        'help'        => __( 'You can also provide a video embed URL from YouTube, Vimeo, Dailymotion, or Wistia. For performance, it will not load the video until the popup is triggered.', 'bb-powerpack' ),
                        'default'               => '',
                        'connections'           => array( 'url' ),
                    ),
                    'modal_type_content'   => array(
                        'type'                  => 'editor',
                        'label'                 => '',
                        'connections'           => array( 'string', 'html', 'url' ),
                        'preview'               => array(
							'type'                  => 'text',
							'selector'              => '.pp-modal-content-inner'
						)
                    ),
                    'modal_type_html'      => array(
                        'type'                  => 'code',
                        'editor'                => 'html',
                        'label'                 => '',
                        'rows'                  => 15,
                        'connections'           => array( 'string', 'html', 'url' ),
                        'preview'               => array(
							'type'                  => 'text',
							'selector'              => '.pp-modal-content'
						)
                    ),
                    'modal_type_templates'      => array(
                        'type'                  => 'select',
                        'label'                 => __('Select Template', 'bb-powerpack'),
                        'options'               => array()
					),
					'modal_template_edit' => array(
						'type' => 'static',
						'description' => sprintf( __( '%1$sEdit%2$s', 'bb-powerpack' ), '<a class="fl-builder-button content_edit" target="_blank" rel="noopener" href="#">', '</a>' ),
					),
                )
			),
        )
    ),
    'settings'       => array( // Tab
		'title'         => __('Settings', 'bb-powerpack'), // Tab title
		'description'	=> sprintf( __( 'Your unique modal box ID is %s. If you are using any form in the modal box, you can use the following JS to hide the modal box after submission: %s', 'bb-powerpack' ), '<span class="pp-modal-node-id"></span>', '<input type="text" class="pp-modal-hide-js" onclick="this.select()" readonly />' ),
		'sections'      => array( // Tab Sections
            'modal_load'    => array(
                'title'         => __('Trigger', 'bb-powerpack'),
                'fields'        => array(
                    'modal_load'           => array(
                        'type'                  => 'select',
                        'label'                 => __('Trigger', 'bb-powerpack'),
                        'default'               => 'auto',
                        'options'               => array(
							'auto'                  => __('Auto', 'bb-powerpack'),
							'onclick'               => __('On Click', 'bb-powerpack'),
                            'exit_intent'           => __('Exit Intent', 'bb-powerpack'),
                            'other'                 => __('Custom Element Click', 'bb-powerpack')
						),
                        'toggle'            => array(
                            'auto'              => array(
                                'sections'          => array('modal_load_auto', 'modal_cookie'),
                            ),
                            'onclick'           => array(
                                'sections'          => array('modal_load_onclick','modal_button_style'),
                                'tabs'              => array('modal_button_style'),
                            ),
                            'exit_intent'       => array(
                                'sections'  => array('modal_cookie'),
                            ),
                        ),
                        'hide'              => array(
                            'auto'              => array(
                                'sections'          => array('modal_button_style'),
                                'tabs'              => array('modal_button_style'),
                            ),
                            'exit_intent'       => array(
                                'sections'          => array('modal_button_style'),
                                'tabs'              => array('modal_button_style'),
                            ),
                        ),
                        'help'              => __('Custom Element Click - modal can be triggered through any other element(s) on this page by providing CSS class or ID to that element.', 'bb-powerpack')
                    ),
					'modal_custom_class'       => array(
                        'type'                  => 'text',
                        'label'                 => __('Your own Class/ID', 'bb-powerpack'),
                        'description'           => __('Please add a class with . prefix (.my-class) or ID with # prefix (#my-id) here. No spaces.', 'bb-powerpack'),
                        'default'               => '',
                        'help'                  => __('Add this CSS class/ID to the element you want to trigger the modal with. This option can work along with any Trigger type you select above.', 'bb-powerpack'),
                    ),
                )
            ),
            'modal_load_auto'   => array(
                'title'             => __('Auto Load Settings', 'bb-powerpack'),
                'fields'            => array(
                    'modal_delay'       => array(
                        'type'              => 'text',
                        'label'             => __('Delay', 'bb-powerpack'),
                        'description'       => __('seconds', 'bb-powerpack'),
                        'size'             	=> 5,
                        'default'           => 1,
                    ),
					'load_on_scroll'		=> array(
						'type'					=> 'unit',
						'label'					=> __( 'Load After % Scroll', 'bb-powerpack' ),
						'help'					=> __( 'Modal will appear when a visitor scrolls the page by a certain percentage. It can also work in conjunction with the Delay.', 'bb-powerpack' ),
						'units'					=> array( '%' ),
					),
                )
            ),
            'modal_load_onclick'  => array(
                'title'             => __('On Click Settings', 'bb-powerpack'),
                'fields'            => array(
                    'button_type'       => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Type', 'bb-powerpack'),
                        'default'           => 'button',
                        'options'           => array(
                            'button'            => __('Button', 'bb-powerpack'),
                            'image'             => __('Image', 'bb-powerpack'),
                            'icon'              => __('Icon', 'bb-powerpack')
                        ),
                        'toggle'            => array(
                            'button'            => array(
                                'fields'            => array('button_text', 'button_icon_src', 'button_icon_pos', 'button_typography', 'button_text_color', 'button_text_hover', 'button_color', 'button_color_hover', 'button_padding_left_right', 'button_padding_top_bottom', 'button_width'),
                                'sections'          => array('modal_button_bg')
                            ),
                            'icon'              => array(
                                'fields'            => array('icon_source', 'sr_text', 'icon_size', 'button_text_color', 'button_text_hover', 'button_color', 'button_color_hover', 'button_padding_left_right', 'button_padding_top_bottom', 'button_width'),
                                'sections'          => array('modal_button_bg')
                            ),
                            'image'             => array(
                                'fields'            => array('image_source', 'sr_text', 'image_size')
                            )
                        ),
                        'hide'              => array(
                            'button'            => array(
                                'fields'            => array('image_width', 'image_height')
                            ),
                            'icon'              => array(
                                'fields'            => array('image_width', 'image_height')
                            ),
                            'image'             => array(
                                'sections'          => array('modal_button_style', 'modal_button_bg')
                            )
                        ),
                        'help'              => __('Styling options are available in Button Style tab.', 'bb-powerpack')
                    ),
                    'image_source'      => array(
                        'type'              => 'photo',
                        'label'             => __('Source', 'bb-powerpack'),
                        'connections'       => array( 'photo' ),
                    ),
                    'icon_source'       => array(
                        'type'              => 'icon',
                        'label'             => __('Icon Source', 'bb-powerpack')
                    ),
					'sr_text'			=> array(
						'type'				=> 'text',
						'label'				=> __( 'Screen Reader Text', 'bb-powerpack' ),
						'connections'		=> array( 'string' ),
					),
                    'button_text'       => array(
                        'type'              => 'text',
                        'label'             => __('Button Text', 'bb-powerpack'),
                        'default'           => __('Click Here', 'bb-powerpack'),
                        'connections'        => array('string', 'html'),
                        'preview'           => array(
                            'type'              => 'text',
                            'selector'          => '.pp-modal-trigger'
                        )
                    ),
                    'button_icon_src'   => array(
						'type'              => 'icon',
						'label'             => __('Icon', 'bb-powerpack'),
						'show_remove'       => true
					),
					'button_icon_pos'   => array(
						'type'              => 'pp-switch',
						'label'             => __('Icon Position', 'bb-powerpack'),
						'default'           => 'before',
						'options'           => array(
							'before'            => __('Before Text', 'bb-powerpack'),
							'after'             => __('After Text', 'bb-powerpack')
						)
					)
                )
            ),
            'modal_cookie'  => array(
                'title'             => __('Cookie Settings', 'bb-powerpack'),
                'fields'            => array(
                    'display_after'      => array(
                        'type'                  => 'text',
                        'label'                 => __('Display After (cookie)', 'bb-powerpack'),
                        'default'               => 1,
                        'description'           => __('day(s)', 'bb-powerpack'),
                        'help'                  => __('If a user closes the modal box, it will be displayed only after the defined day(s).', 'bb-powerpack'),
                        'class'                 => 'modal-display-after',
						'size'					=> 5
					),
					'reset_cookie'  => array(
						'type'    => 'pp-switch',
						'label'   => __( 'Reset cookie on settings change', 'bb-powerpack' ),
						'help'    => __( 'This option will reset the cookie for visitors when you update any option in the module.', 'bb-powerpack' ),
						'default' => 'no'
					),
                )
            ),
            'modal_esc_exit'    => array(
                'title'             => __('Exit Settings', 'bb-powerpack'),
                'fields'            => array(
                    'modal_esc'         => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Esc to Exit', 'bb-powerpack'),
                        'default'           => 'enabled',
                        'options'           => array(
                            'enabled'           => __('Yes', 'bb-powerpack'),
                            'disabled'          => __('No', 'bb-powerpack')
                        ),
                        'help'              => __('Users can close the modal box by pressing Esc key.', 'bb-powerpack')
                    ),
                    'modal_click_exit'  => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Click to Exit', 'bb-powerpack'),
                        'default'           => 'yes',
                        'options'           => array(
                            'yes'               => __('Yes', 'bb-powerpack'),
                            'no'                => __('No', 'bb-powerpack'),
                        ),
                        'help'              => __('Users can close the modal box by clicking anywhere outside the modal.', 'bb-powerpack')
                    )
                )
            )
        )
    ),
    'modal_button_style' => array( // Tab
        'title'             => __('Button Style', 'bb-powerpack'), // Tab title
        'sections'          => array( // Tab Sections
            'modal_button_style' => array(
                'title'             => __('Button', 'bb-powerpack'),
                'fields'            => array(
					'button_typography'	=> array(
						'type'			=> 'typography',
						'label'			=> __('Typography', 'bb-powerpack'),
						'responsive'  	=> true,
						'preview'       => array(
							'type'     => 'css',
                            'selector' => '.pp-modal-button .pp-modal-trigger',
						),
					),
                    'button_text_color' => array(
                        'type'              => 'color',
                        'label'             => __('Color', 'bb-powerpack'),
						'default'           => 'ffffff',
						'show_reset'		=> true,
						'connections'		=> array('color'),
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-trigger',
                            'property'          => 'color'
                        )
                    ),
                    'button_text_hover' => array(
                        'type'              => 'color',
						'label'             => __('Color Hover', 'bb-powerpack'),
						'show_reset'		=> true,
						'default'           => 'f7f7f7',
						'connections'		=> array('color'),
                    ),
					'button_icon_color' => array(
                        'type'              => 'color',
                        'label'             => __('Icon Color', 'bb-powerpack'),
						'default'           => '',
						'show_reset'		=> true,
						'connections'		=> array('color'),
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-trigger .pp-button-icon',
                            'property'          => 'color'
                        )
                    ),
                    'button_icon_hover' => array(
                        'type'              => 'color',
						'label'             => __('Icon Color Hover', 'bb-powerpack'),
						'show_reset'		=> true,
						'default'           => '',
						'connections'		=> array('color'),
                    ),
                )
            ),
            'modal_button_bg'   => array(
                'title'                 => __('Background', 'bb-powerpack'),
                'fields'                => array(
                    'button_color'      => array(
                        'type'              => 'color',
                        'label'             => __('Background Color', 'bb-powerpack'),
                        'default'           => '428bca',
						'show_alpha'		=> true,
						'show_reset'		=> true,
						'connections'		=> array('color'),
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-trigger',
                            'property'          => 'background-color'
                        )
                    ),
                    'button_color_hover' => array(
                        'type'              => 'color',
                        'label'             => __('Background Color Hover', 'bb-powerpack'),
                        'default'           => '444444',
						'show_alpha'		=> true,
						'show_reset'		=> true,
						'connections'		=> array('color'),
                    ),
                )
            ),
            'modal_button_borders'  => array(
                'title'                 => __('Border', 'bb-powerpack'),
                'fields'                => array(
                    'button_border_group'	=> array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack' ),
						'responsive'	=> true,
						'preview'   	=> array(
                            'type'  		=> 'css',
                            'selector'  	=> '.pp-modal-trigger',
                            'property'  	=> 'border',
                        ),
					),
                    'button_border_color_hover' => array(
                        'type'                      => 'color',
                        'label'                     => __('Border Color Hover', 'bb-powerpack'),
						'default'                   => '222222',
						'show_reset'				=> true,
						'connections'				=> array('color'),
                    ),
                )
            ),
            'modal_button_size' => array(
                'title'             => __('Size & Alignment', 'bb-powerpack'),
                'fields'            => array(
                    'image_size'        => array(
                        'type'              => 'select',
                        'label'             => __('Size', 'bb-powerpack'),
                        'default'           => 'auto',
                        'options'           => array(
                            'auto'              => __('Auto', 'bb-powerpack'),
                            'custom'            => __('Custom', 'bb-powerpack')
                        ),
                        'toggle'            => array(
                            'custom'            => array(
                                'fields'            => array('image_width', 'image_height')
                            )
                        )
                    ),
                    'image_width'       => array(
                        'type'              => 'text',
                        'label'             => __('Width', 'bb-powerpack'),
                        'description'       => 'px',
                        'class'             => 'input-small',
                        'default'           => 200,
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-trigger img',
                            'property'          => 'width',
                            'unit'              => 'px'
                        )
                    ),
                    'image_height'      => array(
                        'type'              => 'text',
                        'label'             => __('Height', 'bb-powerpack'),
                        'description'       => 'px',
                        'class'             => 'input-small',
                        'default'           => 200,
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-trigger img',
                            'property'          => 'height',
                            'unit'              => 'px'
                        )
                    ),
                    'icon_size'    => array(
                        'type'              => 'text',
                        'label'             => __('Icon Size', 'bb-powerpack'),
                        'default'           => 40,
                        'description'       => 'px',
                        'class'             => 'input-small',
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-trigger-icon',
                            'property'          => 'font-size',
                            'unit'              => 'px'
                        )
                    ),
					'button_padding'	=> array(
						'type'				=> 'dimension',
						'label'				=> __('Padding', 'bb-powerpack'),
						'default'			=> '0',
						'units'				=> array('px'),
						'slider'			=> true,
						'responsive'		=> true,
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-modal-trigger',
							'property'			=> 'padding',
							'unit'				=> 'px'
						)
					),
                    'button_width'      => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Width', 'bb-powerpack'),
                        'default'           => 'auto',
                        'options'           => array(
                            'auto'              => __('Auto', 'bb-powerpack'),
                            'full'              => __('Full Width', 'bb-powerpack')
                        )
                    ),
                    'button_alignment'  => array(
                        'type'              => 'align',
                        'label'             => __('Alignment', 'bb-powerpack'),
                        'default'           => 'left',
						'responsive'		=> true,
                    )
                )
            )
        )
    ),
    'style'       => array( // Tab
        'title'         => __('Modal Style', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'modal_title'   => array( // Section
                'title'         => __('Title', 'bb-powerpack'), // Section Title
                'fields'        => array( // Section Fields
					'title_tag' => array(
						'type'          => 'select',
						'label'         => __('HTML Tag', 'bb-powerpack'),
						'default'       => 'h4',
						'sanitize' => array( 'pp_esc_tags', 'h4' ),
						'options'       => array(
							'h1'            => 'H1',
							'h2'            => 'H2',
							'h3'            => 'H3',
							'h4'            => 'H4',
							'h5'            => 'H5',
							'h6'            => 'H6',
							'div'			=> 'div',
							'p'				=> 'p',
							'span'			=> 'span',
						)
					),
                    'title_typography'	=> array(
						'type'			=> 'typography',
						'label'			=> __('Typography', 'bb-powerpack'),
						'responsive'  	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-modal-title',
						),
					),
                    'title_color'       => array(
                        'type'              => 'color',
                        'label'             => __('Color', 'bb-powerpack'),
						'default'           => '444444',
						'show_reset'		=> true,
						'connections'		=> array('color'),
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-title',
                            'property'          => 'color'
                        )
                    ),
                    'title_bg'          => array(
                        'type'              => 'color',
                        'label'             => __('Background Color', 'bb-powerpack'),
                        'default'           => 'ffffff',
                        'show_reset'        => true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-header',
                            'property'          => 'background-color'
                        )
                    ),
                    'title_border'      => array(
                        'type'              => 'unit',
                        'label'             => __('Border Bottom', 'bb-powerpack'),
                        'default'           => 1,
                        'units'       		=> array( 'px' ),
						'slider'			=> true,
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-header',
                            'property'          => 'border-bottom-width',
                            'unit'              => 'px'
                        )
                    ),
                    'title_border_style' => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Border Style', 'bb-powerpack'),
                        'default'           => 'solid',
                        'options'           => array(
                            'solid'         => __('Solid', 'bb-powerpack'),
                            'dashed'        => __('Dashed', 'bb-powerpack'),
                            'dotted'        => __('Dotted', 'bb-powerpack'),
                        ),
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-header',
                            'property'          => 'border-bottom-style'
                        )
                    ),
                    'title_border_color' => array(
                        'type'              => 'color',
                        'label'             => __('Border Color', 'bb-powerpack'),
						'default'           => 'eeeeee',
						'show_reset'		=> true,
						'connections'		=> array('color'),
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-header',
                            'property'          => 'border-bottom-color'
                        )
                    ),
                    'title_padding'     => array(
                        'type'              => 'unit',
                        'label'             => __('Side Padding', 'bb-powerpack'),
                        'default'           => 15,
                        'units'       		=> array( 'px' ),
                        'slider'            => true,
                        'preview'           => array(
                            'type'              => 'css',
                            'rules'             => array(
                                array(
                                    'selector'          => '.pp-modal-title',
                                    'property'          => 'padding-left',
                                    'unit'              => 'px'
                                ),
                                array(
                                    'selector'          => '.pp-modal-title',
                                    'property'          => 'padding-right',
                                    'unit'              => 'px'
                                )
                            )
                        )
                    ),
                )
            ),
            'modal_bg'          => array( // Section
                'title'             => __('Background', 'bb-powerpack'), // Section Title
                'fields'            => array( // Section Fields
                    'modal_background'  => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Background Type', 'bb-powerpack'),
                        'default'           => 'color',
                        'options'           => array(
                            'color'             => __('Color', 'bb-powerpack'),
                            'photo'             => __('Image', 'bb-powerpack')
                        ),
                        'toggle'            => array(
                            'color'             => array(
                                'fields'            => array('modal_bg_color')
                            ),
                            'photo'             => array(
                                'fields'            => array('modal_bg_photo', 'modal_bg_size', 'modal_bg_repeat')
                            )
                        )
                    ),
                    'modal_bg_color'    => array(
                        'type'              => 'color',
                        'label'             => __('Background Color', 'bb-powerpack'),
                        'default'           => 'ffffff',
                        'show_reset'        => true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
                    ),
                    'modal_bg_photo'    => array(
                        'type'              => 'photo',
                        'label'             => __('Background Image', 'bb-powerpack'),
                        'default'           => '',
                    ),
                    'modal_bg_size'     => array(
                        'type'          => 'select',
                        'label'         => __('Background Size', 'bb-powerpack'),
                        'default'       => 'cover',
                        'options'       => array(
                            'contain'   => __('Contain', 'bb-powerpack'),
                            'cover'     => __('Cover', 'bb-powerpack'),
                        ),
                    ),
                    'modal_bg_repeat'   => array(
                        'type'          => 'select',
                        'label'         => __('Background Repeat', 'bb-powerpack'),
                        'default'       => 'no-repeat',
                        'options'       => array(
                            'repeat-x'      => __('Repeat X', 'bb-powerpack'),
                            'repeat-y'      => __('Repeat Y', 'bb-powerpack'),
                            'no-repeat'     => __('No Repeat', 'bb-powerpack'),
                        ),
                    ),
                    'modal_backlight'   => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Backlight Effect', 'bb-powerpack'),
                        'default'           => 'disabled',
                        'options'           => array(
                            'enabled'           => __('Yes', 'bb-powerpack'),
                            'disabled'          => __('No', 'bb-powerpack')
                        ),
                        'help'              => __('A color shadow of background image. It may incompatible with some browsers.', 'bb-powerpack')
                    )
                )
            ),
            'modal_box'         => array( // Section
                'title'             => __('Box', 'bb-powerpack'), // Section Title
                'fields'            => array( // Section Fields
					'modal_border_group' => array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack' ),
						'responsive'	=> true,
					),
					'modal_margin' => array(
						'type'       => 'dimension',
						'label'      => __( 'Margin', 'bb-powerpack' ),
						'responsive' => true,
						'units'      => array( 'px' )
					),
					'modal_padding'     => array(
                        'type'              => 'unit',
                        'label'             => __('Padding', 'bb-powerpack'),
                        'default'           => 10,
						'units'				=> array( 'px' ),
						'slider'			=> true,
                    ),
                )
            ),
            'modal_content'      => array( // Section
                'title'             => __('Content', 'bb-powerpack'), // Section Title
                'fields'            => array( // Section Fields
                    'content_border_group'	=> array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack' ),
						'responsive'	=> true,
					),
                    'content_text_color'     => array(
                        'type'                  => 'color',
                        'label'                 => __('Text Color', 'bb-powerpack'),
						'show_reset'            => true,
						'connections'			=> array('color'),
                    ),
                    'content_padding'        => array(
                        'type'                  => 'unit',
                        'label'                 => __('Padding', 'bb-powerpack'),
                        'default'               => '',
                        'units'					=> array( 'px' ),
						'slider'				=> true,
                    )
                )
            ),
            'modal_overlay'      => array( // Section
                'title'             => __('Overlay', 'bb-powerpack'), // Section Title
                'fields'            => array( // Section Fields
                    'overlay_toggle'    => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Overlay', 'bb-powerpack'),
                        'default'           => 'block',
                        'options'           => array(
                            'block'             => __('Show', 'bb-powerpack'),
                            'none'              => __('Hide', 'bb-powerpack')
                        ),
                        'toggle'            => array(
                            'block'             => array(
                                'fields'            => array('overlay_bg_color', 'overlay_opacity')
                            )
                        )
                    ),
                    'overlay_bg_color'  => array(
                        'type'              => 'color',
                        'label'             => __('Background Color', 'bb-powerpack'),
                        'default'           => 'rgba(0,0,0,0.3)',
						'show_alpha'		=> true,
						'show_reset'		=> true,
						'connections'		=> array('color'),
                        'preview'           => array(
                            'type'              => 'css',
                            'selector'          => '.pp-modal-overlay',
                            'property'          => 'background-color'
                        )
                    ),
                )
            ),
            'modal_close'      => array( // Section
                'title'             => __('Close Button', 'bb-powerpack'), // Section Title
                'fields'            => array( // Section Fields
                    'close_btn_toggle'   => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Button', 'bb-powerpack'),
                        'default'           => 'block',
                        'options'           => array(
                            'block'             => __('Show', 'bb-powerpack'),
                            'none'              => __('Hide', 'bb-powerpack')
                        )
                    ),
                    'close_btn_color'    => array(
                        'type'              => 'color',
                        'label'             => __('Color', 'bb-powerpack'),
						'default'           => 'ffffff',
						'show_reset'		=> true,
						'connections'		=> array('color'),
                    ),
                    'close_btn_color_hover' => array(
                        'type'                  => 'color',
                        'label'                 => __('Color Hover', 'bb-powerpack'),
						'default'               => 'dddddd',
						'show_reset'			=> true,
						'connections'			=> array('color'),
                    ),
                    'close_btn_bg'      => array(
                        'type'              => 'color',
                        'label'             => __('Background Color', 'bb-powerpack'),
						'default'           => '3a3a3a',
						'connections'		=> array('color'),
                        'show_reset'        => true,
						'show_alpha'		=> true
                    ),
                    'close_btn_bg_hover' => array(
                        'type'              => 'color',
                        'label'             => __('Background Color Hover', 'bb-powerpack'),
                        'default'           => 'b53030',
                        'show_reset'        => true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
                    ),
                    'close_btn_border'  => array(
                        'type'              => 'unit',
                        'label'             => __('Border Width', 'bb-powerpack'),
                        'default'           => 1,
                        'units'				=> array( 'px' ),
						'slider'			=> true,
                    ),
                    'close_btn_border_color'    => array(
                        'type'                      => 'color',
                        'label'                     => __('Border Color', 'bb-powerpack'),
						'default'                   => 'ffffff',
						'show_reset'				=> true,
						'connections'				=> array('color'),
                    ),
                    'close_btn_border_radius'   => array(
                        'type'                      => 'unit',
                        'label'                     => __('Round Corners', 'bb-powerpack'),
                        'default'                   => 100,
                        'units'						=> array( 'px' ),
						'slider'					=> true,
                    ),
                    'close_btn_size'          => array(
                        'type'                      => 'unit',
                        'label'                     => __('Size', 'bb-powerpack'),
                        'default'                   => 25,
                        'units'						=> array( 'px' ),
						'slider'					=> true,
                    ),
                    'close_btn_weight'          => array(
                        'type'                      => 'unit',
                        'label'                     => __('Weight', 'bb-powerpack'),
                        'default'                   => 2,
                        'units'						=> array( 'px' ),
						'slider'					=> true,
                    ),
                    'close_btn_position'    => array(
                        'type'                  => 'select',
                        'label'                 => __('Position', 'bb-powerpack'),
                        'default'               => 'box-top-right',
                        'options'               => array(
                            'box-top-right'         => __('Box - Top Right'),
                            'box-top-left'          => __('Box - Top Left'),
                            'win-top-right'         => __('Window - Top Right'),
                            'win-top-left'          => __('Window - Top Left')
                        ),
                        'toggle'                => array(
                            'box-top-right'         => array(
                                'fields'                => array('close_btn_top', 'close_btn_right')
                            ),
                            'box-top-left'          => array(
                                'fields'                => array('close_btn_top', 'close_btn_left')
                            ),
                            'win-top-right'         => array(
                                'fields'                => array('close_btn_top', 'close_btn_right')
                            ),
                            'win-top-left'          => array(
                                'fields'                => array('close_btn_top', 'close_btn_left')
                            )
                        )
                    ),
                    'close_btn_top'        => array(
                        'type'                      => 'unit',
                        'label'                     => __('Top Margin', 'bb-powerpack'),
                        'default'                   => '-10',
                        'units'						=> array( 'px' ),
						'slider'					=> true,
                    ),
                    'close_btn_left'        => array(
                        'type'                      => 'unit',
                        'label'                     => __('Left Margin', 'bb-powerpack'),
                        'default'                   => '-10',
                        'units'						=> array( 'px' ),
						'slider'					=> true,
                    ),
                    'close_btn_right'        => array(
                        'type'                      => 'unit',
                        'label'                     => __('Right Margin', 'bb-powerpack'),
                        'default'                   => '-10',
                        'units'						=> array( 'px' ),
						'slider'					=> true,
                    )
                )
            ),
            'modal_responsive'  => array( // Section
                'title'             => __('Responsive', 'bb-powerpack'), // Section Title
                'fields'            => array( // Section Fields
                    'media_breakpoint'  => array(
                        'type'              => 'unit',
                        'label'             => __('Media Breakpoint', 'bb-powerpack'),
                        'default'           => 0,
                        'units'             => array( 'px' ),
                        'help'              => __('You can set a custom break point and devices with the same or below screen width will always display a full screen modal box.', 'bb-powerpack'),
                    )
                )
            )
        )
    ),
    'animation'     => array( // Tab
        'title'         => __('Animation', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'modal_anim_load' => array( // Section
                'title'         => __('Animation On Load', 'bb-powerpack'), // Section Title
                'fields'        => array( // Section Fields
                    'animation_load'    => array(
                        'type'                  => 'select',
                        'label'                 => __('Animation', 'bb-powerpack'),
                        'default'               => 'fadeIn',
                        'options'               => modal_animations(),
                    ),
                    'animation_load_duration'  => array(
                        'type'                  => 'text',
                        'label'                 => __('Speed', 'bb-powerpack'),
                        'description'           => __('seconds', 'bb-powerpack'),
                        'size'                 	=> 5,
                        'default'               => '0.5',
                    )
                )
            ),
            'modal_anim_exit' => array( // Section
                'title'         => __('Animation On Exit', 'bb-powerpack'), // Section Title
                'fields'        => array( // Section Fields
                    'animation_exit'    => array(
                        'type'                  => 'select',
                        'label'                 => __('Animation', 'bb-powerpack'),
                        'default'               => 'fadeOut',
                        'options'               => modal_animations(),
                    ),
                    'animation_exit_duration'  => array(
                        'type'                  => 'text',
                        'label'                 => __('Speed', 'bb-powerpack'),
                        'description'           => __('seconds', 'bb-powerpack'),
                        'size'                 	=> 5,
                        'default'               => '0.5',
                    )
                )
			),
			'overlay_animation' => array(
				'title'	=> __( 'Overlay', 'bb-powerpack' ),
				'fields' => array(
					'overlay_animation' => array(
						'type' => 'pp-switch',
						'label' => __( 'Overlay Animation', 'bb-powerpack' ),
						'default' => 'yes'
					),
				),
			),
        )
    )
));

function modal_animations() {
    $animations = array(
        'bounce'                => __('Bounce', 'bb-powerpack'),
        'bounceIn'              => __('Bounce In', 'bb-powerpack'),
        'bounceOut'             => __('Bounce Out', 'bb-powerpack'),
        'fadeIn'                => __('Fade In', 'bb-powerpack'),
        'fadeInDown'            => __('Fade In Down', 'bb-powerpack'),
        'fadeInLeft'            => __('Fade In Left', 'bb-powerpack'),
        'fadeInRight'           => __('Fade In Right', 'bb-powerpack'),
        'fadeInUp'              => __('Fade In Up', 'bb-powerpack'),
        'fadeOut'               => __('Fade Out', 'bb-powerpack'),
        'fadeOutDown'           => __('Fade Out Down', 'bb-powerpack'),
        'fadeOutLeft'           => __('Fade Out Left', 'bb-powerpack'),
        'fadeOutRight'          => __('Fade Out Right', 'bb-powerpack'),
        'fadeOutUp'             => __('Fade Out Up', 'bb-powerpack'),
        'pulse'                 => __('Pulse', 'bb-powerpack'),
        'shake'                 => __('Shake', 'bb-powerpack'),
        'slideInDown'           => __('Slide In Down', 'bb-powerpack'),
        'slideInLeft'           => __('Slide In Left', 'bb-powerpack'),
        'slideInRight'          => __('Slide In Right', 'bb-powerpack'),
        'slideInUp'             => __('Slide In Up', 'bb-powerpack'),
        'slideOutDown'          => __('Slide Out Down', 'bb-powerpack'),
        'slideOutLeft'          => __('Slide Out Left', 'bb-powerpack'),
        'slideOutRight'         => __('Slide Out Right', 'bb-powerpack'),
        'slideOutUp'            => __('Slide Out Up', 'bb-powerpack'),
        'swing'                 => __('Swing', 'bb-powerpack'),
        'tada'                  => __('Tada', 'bb-powerpack'),
        'zoomIn'                => __('Zoom In', 'bb-powerpack'),
        'zoomOut'               => __('Zoom Out', 'bb-powerpack'),
    );

    return $animations;
}
