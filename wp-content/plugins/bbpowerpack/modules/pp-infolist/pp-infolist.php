<?php

/**
 * @class PPInfoListModule
 */
class PPInfoListModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Info List', 'bb-powerpack'),
            'description'   => __('Addon to display info list.', 'bb-powerpack'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-infolist/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-infolist/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh'   => true,
        ));

        add_filter( 'pp_module_ui_setting_fields', array( $this, 'extend_setting_fields' ), 10, 2 );
    }

	public function enqueue_icon_styles() {
		$enqueue = false;
		$settings = $this->settings;
		$items = $settings->list_items;

		if ( is_array( $items ) && count( $items ) ) {
			foreach ( $items as $item ) {
				if ( ! is_object( $item ) ) {
					continue;
				}

				if ( 'icon' === $item->icon_type && ! empty( $item->icon_select ) ) {
					$enqueue = true;
					break;
				}
			}
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function filter_settings( $settings, $helper ) {
		// Handle old link, link_target, link_nofollow fields.
		$settings = PP_Module_Fields::handle_link_field( $settings, array(
			'link'			=> array(
				'type'			=> 'link'
			),
			'link_target'	=> array(
				'type'			=> 'target'
			),
		), 'link' );

		// Handle title's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'title_font'	=> array(
				'type'			=> 'font'
			),
			'title_font_size'	=> array(
				'type'			=> 'font_size',
			),
		), 'title_typography' );

		// Handle text's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'text_font'	=> array(
				'type'			=> 'font'
			),
			'text_font_size'	=> array(
				'type'			=> 'font_size',
			),
		), 'text_typography' );

		return $settings;
	}

    public function extend_setting_fields( $fields, $settings ) {
        if ( ! isset( $settings->list_items ) ) {
            return $fields;
        }

        if ( class_exists( 'acf' ) ) {
            $fields['data_source']['toggle']['acf']['fields'][] = 'acf_repeater_link';

            $fields['acf_repeater_link']     = array(
                'type'        => 'text',
                'label'       => __( 'ACF Repeater Sub Field Name (URL)', 'bb-powerpack' ),
                'connections' => array( 'string', 'url' ),
            );
        }

        if ( function_exists( 'acf_add_options_page' ) ) {
			$fields['data_source']['toggle']['acf_options_page']['fields'][] = 'acf_options_page_repeater_link';

			$fields['acf_options_page_repeater_link']   = array(
				'type'        => 'text',
				'label'       => __( 'ACF Repeater Sub Field Name (URL)', 'bb-powerpack' ),
				'connections' => array( 'string', 'url' ),
			);
		}

        /* TODO
        $fields['item_image_source'] = array(
            'type' => 'photo',
            'label' => __( 'Item image source', 'bb-powerpack' ),
            'show_remove' => true,
            'connections' => array( 'photo' )
        );

        $fields['data_source']['toggle']['post']['fields'][] = 'item_image_source';
        $fields['data_source']['toggle']['acf']['fields'][] = 'item_image_source';
        $fields['data_source']['toggle']['acf_options_page']['fields'][] = 'item_image_source';
        $fields['data_source']['toggle']['acf_relationship']['fields'][] = 'item_image_source';
        $fields['data_source']['toggle']['pods_relationship']['fields'][] = 'item_image_source';
        */

        return $fields;
    }

    public function get_data_source() {
		if ( ! isset( $this->settings->data_source ) || empty( $this->settings->data_source ) ) {
			return 'manual';
		}

		return $this->settings->data_source;
	}

	public function get_acf_data( $post_id = false ) {
		if ( ! isset( $this->settings->acf_repeater_name ) || empty( $this->settings->acf_repeater_name ) ) {
			return;
		}

		$data = array();

        if ( is_tax() || is_category() ) {
			$post_id = get_queried_object();
		}

		$post_id = apply_filters( 'pp_infolist_acf_post_id', $post_id, $this->settings );

		$repeater_name    = $this->settings->acf_repeater_name;
		$repeater_label   = $this->settings->acf_repeater_label;
		$repeater_content = $this->settings->acf_repeater_content;
		$repeater_link    = $this->settings->acf_repeater_link;

		$repeater_rows = get_field( $repeater_name, $post_id );

		if ( ! $repeater_rows ) {
			return;
		}

		global $wp_embed;

		if ( have_rows( $repeater_name, $post_id ) ) {
			while ( have_rows( $repeater_name, $post_id ) ) {
				the_row();

				$title       = get_sub_field( $repeater_label );
				$content_obj = get_sub_field_object( $repeater_content );
				$content     = $content_obj['value'];
                $link        = get_sub_field( $repeater_link );

				if ( 'file' === $answer_obj['type'] ) {
					$answer = sprintf( '<a href="%s" target="_blank" rel="nofollow">%s</a>', $content, $content );
				}
				if ( 'image' === $answer_obj['type'] ) {
					$answer = sprintf( '<img src="%s" alt="%s" />', $content, $content );
				}

				$item          = new stdClass;
				$item->post_id = $post_id;
				$item->title   = $title;
				$item->content = wpautop( $wp_embed->autoembed( $content ) );
                $item->link    = $link;

				$data[] = $item;
			}
		}

		return $data;
	}

	public function get_acf_relationship_data() {
		if ( ! isset( $this->settings->acf_relational_key ) || empty( $this->settings->acf_relational_key ) ) {
			return;
		}

		$data     = array();
		$settings = new stdClass;

		$settings->data_source = 'acf_relationship';
		$settings->data_source_acf_relational_key = $this->settings->acf_relational_key;
		$settings->data_source_acf_relational_type = $this->settings->acf_relational_type;
		$settings->data_source_acf_order_by = $this->settings->acf_order_by;
		$settings->data_source_acf_order = $this->settings->acf_order;
		$settings->data_source_acf_order_by_meta_key = $this->settings->acf_order_by_meta_key;
		$settings->posts_per_page = '-1';
		$settings->id = $this->settings->id;
		$settings->class = $this->settings->class;

		$settings = apply_filters( 'pp_infolist_acf_relationship_data_settings', $settings, $this->settings );

		$data = BB_PowerPack_Post_Helper::get_posts_properties_as_data( $settings, array( 'title' => 'title', 'content' => 'content' ) );

		return $data;
	}

	public function get_acf_options_page_data( $post_id = false ) {
		if ( ! isset( $this->settings->acf_options_page_repeater_name ) || empty( $this->settings->acf_options_page_repeater_name ) ) {
			return;
		}

		$data    = array();
		$post_id = apply_filters( 'pp_infolist_acf_options_page_post_id', $post_id );

		$repeater_name    = $this->settings->acf_options_page_repeater_name;
		$repeater_label   = $this->settings->acf_options_page_repeater_label;
		$repeater_content = $this->settings->acf_options_page_repeater_content;
		$repeater_link    = $this->settings->acf_options_page_repeater_link;

		$repeater_rows = get_field( $repeater_name, 'option' );

		if ( ! $repeater_rows ) {
			return;
		}

		foreach ( $repeater_rows as $row ) {
			$item          = new stdClass;
			$item->title   = isset( $row[ $repeater_label ] ) ? $row[ $repeater_label ] : '';
			$item->content = isset( $row[ $repeater_content ] ) ? $row[ $repeater_content ] : '';
            $item->link    = isset( $row[ $repeater_link ] ) ? $row[ $repeater_link ] : '';

			$data[] = $item;
		}

		return $data;
	}

	public function get_cpt_data() {
		$data = array();

		if ( ! isset( $this->settings->post_type ) || empty( $this->settings->post_type ) ) {
			return $data;
		}

		$data = BB_PowerPack_Post_Helper::get_posts_properties_as_data(
            $this->settings,
            array(
                'title'   => 'title',
                'content' => 'content',
                'image'   => 'image_select_src'
            )
        );

		return $data;
	}

	public function get_list_items() {
		$items = array();
		$source = $this->get_data_source();

		if ( 'manual' === $source ) {
			$items = $this->settings->list_items;
		} elseif ( 'acf' === $source ) {
			$items = $this->get_acf_data();
		} elseif ( 'acf_relationship' === $source ) {
			$items = $this->get_acf_relationship_data();
		} elseif ( 'acf_options_page' === $source ) {
			$items = $this->get_acf_options_page_data();
		} elseif ( 'post' === $source || 'pods_relationship' === $source ) {
			$items = $this->get_cpt_data();
		}

        if ( ! empty( $items ) ) {
            foreach ( $items as $i => $item ) {
                if ( isset( $item->content ) && ! isset( $item->description ) ) {
                    $item->description = $item->content;
                }
                if ( ! isset( $item->link ) ) {
                    $link = ( 'post' === $source || 'acf_relationship' === $source ) ? get_permalink( $item->post_id ) : '';
                    $item->link = $link;
                }
                if ( ! isset( $item->link_type ) ) {
                    $item->link_type = isset( $this->settings->link_type ) ? $this->settings->link_type : '';
                }
                if ( ! isset( $item->link_target ) ) {
                    $item->link_target = isset( $this->settings->link_target ) ? $this->settings->link_target : '_self';
                }
                if ( ! isset( $item->read_more_text ) ) {
                    $item->read_more_text = isset( $this->settings->read_more_text ) ? $this->settings->read_more_text : '';
                }
                if ( ! isset( $item->icon_type ) ) {
                    $item->icon_type = '';
                }
                if ( ! isset( $item->icon_select ) ) {
                    $item->icon_select = '';
                }
                if ( ! isset( $item->image_select ) ) {
                    $item->image_select = '';
                }
                if ( ! isset( $item->image_select_src ) ) {
                    $item->image_select_src = '';
                }
                if ( 'manual' !== $source && isset( $this->settings->item_image_source ) ) {
                    $item->icon_type = 'image';
                }
                $items[ $i ] = $item;
            }
        }

		return apply_filters( 'pp_infolist_items', $items, $this->settings );
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module('PPInfoListModule', array(
	'general'      => array( // Tab
		'title'         => __('General', 'bb-powerpack'), // Tab title
		'sections'      => array(
            'layouts'   => array(
                'title' => '',
                'fields'    => array(
                    'layouts'   => array(
                        'type'  => 'select',
                        'default'   => '1',
                        'label'     => __('Layout', 'bb-powerpack'),
                        'options'   => array(
                            '1'     => __('Left', 'bb-powerpack'),
                            '2'     => __('Right', 'bb-powerpack'),
                            '3'     => __('Top', 'bb-powerpack'),
						),
						'responsive' => true,
						'preview'	=> array(
							'type'	=> 'refresh',
						),
						'toggle' => array(
							'1' => array(
								'fields' => array( 'icon_position' ),
							),
							'2' => array(
								'fields' => array( 'icon_position' ),
							),
						),
                    ),
					'icon_position' => array(
						'type'	=> 'select',
						'label'	=> __( 'Icon Position', 'bb-powerpack' ),
						'default' => 'with_content',
						'options' => array(
							'with_heading' => __( 'With Heading', 'bb-powerpack' ),
							'with_content' => __( 'With Content', 'bb-powerpack' ),
						),
					),
                ),
            ),
            'connector'         => array(
                'title'             => __('Connector Line', 'bb-powerpack'),
                'fields'            => array(
                    'connector_type'    => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Style', 'bb-powerpack'),
                        'default'           => 'dashed',
                        'options'           => array(
                            'none'              => __('None', 'bb-powerpack'),
                            'solid'             => __('Solid', 'bb-powerpack'),
                            'dashed'            => __('Dashed', 'bb-powerpack'),
                            'dotted'            => __('Dotted', 'bb-powerpack'),
                        ),
                        'toggle'  => array(
                            'solid'  => array(
                                'fields'    => array('connector_width', 'connector_color')
                            ),
                            'dashed'  => array(
                                'fields'    => array('connector_width', 'connector_color')
                            ),
                            'dotted'  => array(
                                'fields'    => array('connector_width', 'connector_color')
                            )
                        )
                    ),
                    'connector_width'   => array(
                        'type'              => 'unit',
                        'label'             => __('Width', 'bb-powerpack'),
                        'default'           => '1',
						'slider'			=> true,
						'units'				=> array('px')
                    ),
                    'connector_color'   => array(
                        'type'              => 'color',
                        'label'             => __('Color', 'bb-powerpack'),
                        'default'           => '000000',
						'show_reset'        => true,
						'connections'		=> array('color'),
                    ),
                ),
            ),
            'link' => array(
                'title'  => __( 'Link', 'bb-powerpack' ),
                'fields' => array(
                    'link_type'     => array(
                        'type'      => 'select',
                        'label'     => __('Link Type', 'bb-powerpack'),
                        'default'     => 'none',
                        'options'   => array(
                            'none'  => __('None', 'bb-powerpack'),
                            'box'  => __('Complete Box', 'bb-powerpack'),
                            'title'  => __('Title Only', 'bb-powerpack'),
                            'read_more'  => __('Read More', 'bb-powerpack'),
                        ),
                        'toggle'    => array(
                            'read_more'     => array(
                                'fields'    => array('read_more_text', 'read_more_color', 'read_more_color_hover', 'read_more_font', 'read_more_font_size')
                            ),
                        )
                    ),
                    'link_target' => array(
                        'type' => 'select',
                        'label' => __( 'Target', 'bb-powerpack' ),
                        'default' => '_self',
                        'options' => array(
                            '_self' => __( 'Self Window' ),
                            '_blank' => __( 'New Window' ),
                        ),
                    ),
                    'read_more_text'     => array(
                        'type'          => 'text',
                        'label'         => __('Text', 'bb-powerpack'),
                        'default'       => __('Read More', 'bb-powerpack'),
                        'connections'   => array( 'string' ),
                        'preview'       => array(
                            'type'      => 'text',
                            'selector'  => '.pp-more-link'
                        ),
                    ),
                    'read_more_color'    => array(
                        'type'      => 'color',
                        'label'     => __('Link Color', 'bb-powerpack'),
                        'default'   => '',
                        'show_reset'    => true,
                        'connections'	=> array('color'),
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-more-link',
                            'property'  => 'color'
                        ),
                    ),
                    'read_more_color_hover'    => array(
                        'type'      => 'color',
                        'label'     => __('Link Hover Color', 'bb-powerpack'),
                        'default'   => '',
                        'show_reset'    => true,
                        'connections'	=> array('color'),
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-more-link:hover',
                            'property'  => 'color'
                        ),
                    ),
                )
            ),
		)
	),
    'list_items'    => array(
        'title'     => __('List Items', 'bb-powerpack'),
        'sections'  => array(
            'list_source' => array(
                'title' => __( 'Source', 'bb-powerpack' ),
                'file'  => BB_POWERPACK_DIR . 'includes/ui-setting-fields.php',
            ),
            'post_content'  => array(
                'title' => __( 'Content', 'bb-powerpack' ),
                'file'  => BB_POWERPACK_DIR . 'includes/ui-loop-settings-simple.php',
            ),
            'items'   => array(
                'title'     => __( 'Items', 'bb-powerpack' ),
                'fields'    => array(
                    'list_items'    => array(
                        'type'          => 'form',
						'label'         => __('List Item', 'bb-powerpack'),
						'form'          => 'pp_list_item', // ID from registered form below
						'preview_text'  => 'title', // Name of a field to use for the preview text
						'multiple'      => true
                    ),
                ),
            ),
        ),
    ),
    'style'     => array(
        'title' => __('Style', 'bb-powerpack'),
        'sections'  => array(
			'list_item' => array(
				'title' => __( 'List Item', 'bb-powerpack' ),
				'fields' => array(
					'item_bg' => array(
						'type'  => 'color',
						'label' => __( 'Background Color', 'bb-powerpack' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.pp-list-item-content',
							'property' => 'background-color',
						)
					),
					'item_bg_hover' => array(
						'type'  => 'color',
						'label' => __( 'Background Hover Color', 'bb-powerpack' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.pp-list-item:hover .pp-list-item-content',
							'property' => 'background-color',
						)
					),
					'item_padding' => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'bb-powerpack' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.pp-list-item-content',
							'property' => 'padding'
						),
					),
					'item_border' => array(
						'type'     => 'border',
						'label'   => __( 'Border', 'bb-powerpack' ),
						'preview' => array(
							'type'     => 'css',
							'selector' => '.pp-list-item-content',
							'property' => 'padding'
						),
					),
					'item_border_hover' => array(
						'type'  => 'color',
						'label' => __( 'Border Hover Color', 'bb-powerpack' ),
						'show_reset'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.pp-list-item-content:hover',
							'property' => 'border-color',
						)
					),
				),
			),
            'icon_size' => array(
                'title' => __('Icon Size', 'bb-powerpack'),
				'collapsed' => true,
                'fields'    => array(
                    'icon_font_size'    => array(
                        'type'          => 'unit',
                        'default'       => '16',
                        'label'         => __('Icon Size', 'bb-powerpack'),
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon-inner span',
                                    'property'      => 'font-size',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner span:before',
                                    'property'      => 'font-size',
                                    'unit'          => 'px'
                                ),
								array(
                                    'selector'      => '.pp-infolist-icon-inner img',
                                    'property'      => 'width',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img',
                                    'property'      => 'height',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                    'icon_box_width'    => array(
                        'type'      	=> 'unit',
                        'label'     	=> __('Icon Box Size', 'bb-powerpack'),
                        'default'       => '40',
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon-inner',
                                    'property'      => 'height',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner',
                                    'property'      => 'width',
                                    'unit'          => 'px'
                                ),
                            ),
                        ),
                    ),
                )
            ),
			'icon_styles'   => array(
				'title'     => __( 'Icon Colors', 'bb-powerpack' ),
				'collapsed' => true,
				'fields'    => array(
					'icon_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infolist-icon-inner',
							'property'      => 'color',
						)
					),
					'icon_color_hover'    => array(
						'type'          => 'color',
						'label'         => __('Hover Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
					),
					'icon_background'    => array(
						'type'          => 'color',
						'label'         => __('Background Color', 'bb-powerpack'),
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infolist-icon-inner .pp-icon',
							'property'      => 'background',
						)
					),
					'icon_background_hover'    => array(
						'type'          => 'color',
						'label'         => __('Background Hover Color', 'bb-powerpack'),
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
					),
				),
			),
            'icon_border'   => array(
                'title'         => __('Icon Border', 'bb-powerpack'),
				'collapsed'			=> true,
                'fields'        => array(
                    'show_border'   => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Show Border', 'bb-powerpack'),
                        'default'   => 'no',
                        'options'   => array(
                            'yes'    => __('Yes', 'bb-powerpack'),
                            'no'    => __('No', 'bb-powerpack'),
                        ),
                        'toggle'    => array(
                            'yes'   => array(
                                'fields'    => array ('icon_border_width', 'icon_border_color', 'icon_border_color_hover', 'icon_border_style', 'icon_box_size')
                            )
                        ),
                    ),
                    'icon_border_style'     => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Border Style', 'bb-powerpack'),
                        'default'   => 'solid',
                        'options'   => array(
                            'solid'      => __('Solid', 'bb-powerpack'),
                            'dotted'      => __('Dotted', 'bb-powerpack'),
                            'dashed'      => __('Dashed', 'bb-powerpack'),
                            'double'      => __('Double', 'bb-powerpack'),
                        ),
                    ),
                    'icon_border_width'    => array(
                        'type'          => 'unit',
                        'label'         => __('Border Width', 'bb-powerpack'),
                        'default'       => 1,
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon',
                                    'property'      => 'border-width',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img',
                                    'property'      => 'border-width',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                    'icon_border_color'    => array(
                        'type'          => 'color',
                        'label'         => __('Border Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon',
                                    'property'      => 'border-color',
                                ),
                            ),
                        )
                    ),
                    'icon_border_color_hover'    => array(
                        'type'          => 'color',
                        'label'         => __('Border Hover Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon:hover',
                                    'property'      => 'border-color',
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img:hover',
                                    'property'      => 'border-color',
                                ),
                            ),
                        )
                    ),
                    'icon_border_radius'    => array(
                        'type'          => 'unit',
                        'label'         => __('Round Corners', 'bb-powerpack'),
                        'default'       => '0',
                        'units'  	 	=> array( 'px' ),
						'slider'		=> true,
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner span.pp-icon',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                ),
            ),
            'icon_spacing'   => array(
                'title'          => __('Spacing', 'bb-powerpack'),
				'collapsed'			=> true,
                'fields'        => array(
                    'list_spacing'  => array(
                        'type'      => 'unit',
                        'label'     => __('List Spacing', 'bb-powerpack'),
                        'default'   => 25,
                        'help'      => __('Spacing between list items.', 'bb-powerpack'),
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-infolist ul.pp-list-items',
                            'property'  => 'gap',
                            'unit'      => 'px'
                        ),
                    ),
                    'icon_gap'  => array(
                        'type'      => 'unit',
                        'label'     => __('Icon Spacing', 'bb-powerpack'),
                        'default'   => 20,
                        'help'   => __('Distance between icon and content.', 'bb-powerpack'),
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'     => array(
                                array(
                                    'selector'  => '.pp-infolist-wrap .layout-1 .pp-icon-wrapper',
                                    'property'  => 'margin-right',
                                    'unit'      => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-infolist-wrap .layout-2 .pp-icon-wrapper',
                                    'property'  => 'margin-left',
                                    'unit'      => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-infolist-wrap .layout-3 .pp-icon-wrapper',
                                    'property'  => 'margin-bottom',
                                    'unit'      => 'px'
                                ),
                            ),
                        ),
                    ),
                    'icon_box_size'     => array(
                        'type'          => 'unit',
                        'default'     => '0',
                        'label'         => __('Inside Spacing', 'bb-powerpack'),
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'help'      => __('Space between icon and the border', 'bb-powerpack'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'           => array(
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img',
                                    'property'     => 'padding',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon',
                                    'property'     => 'padding',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                )
            ),
        ),
    ),
    'typography'      => array( // Tab
		'title'         => __('Typography', 'bb-powerpack'), // Tab title
		'sections'      => array( // Tab Sections
            'general'     => array(
                'title'     => __('Title', 'bb-powerpack'),
                'fields'    => array(
					'title_tag'	=> array(
						'type'		=> 'select',
						'label'		=> __('HTML Tag', 'bb-powerpack'),
						'default'	=> 'h3',
						'sanitize' => array( 'pp_esc_tags', 'h3' ),
						'options'	=> array(
							'h1'		=> 'h1',
							'h2'		=> 'h2',
							'h3'		=> 'h3',
							'h4'		=> 'h4',
							'h5'		=> 'h5',
							'h6'		=> 'h6',
							'p'			=> 'p',
							'span'		=> 'span',
							'div'		=> 'div'
						)
					),
                    'title_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-title .pp-infolist-title-text',
                                    'property'      => 'color',
                                ),
                                array(
                                    'selector'      => '.pp-infolist-title a .pp-infolist-title-text',
                                    'property'      => 'color',
                                ),
                            ),
                        )
					),
					'title_hover_color'    => array(
						'type'          => 'color',
						'label'         => __('Hover Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'none',
                        )
					),
                   'title_typography'	=> array(
						'type'			=> 'typography',
						'label'			=> __('Typography', 'bb-powerpack'),
						'responsive'  	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-infolist-title .pp-infolist-title-text',
						),
					),
                    'title_margin'      => array(
                        'type'              => 'pp-multitext',
                        'label'             => __('Margin', 'bb-powerpack'),
                        'description'       => 'px',
                        'default'           => array(
                            'top'               => 0,
                            'bottom'            => 0
                        ),
                        'options'           => array(
                            'top'               => array(
                                'placeholder'       => __('Top', 'bb-powerpack'),
                                'tooltip'           => __('Top', 'bb-powerpack'),
                                'icon'              => 'fa-long-arrow-up',
                                'preview'           => array(
                                    'selector'          => '.pp-infolist-title .pp-infolist-title-text',
                                    'property'          => 'margin-top',
                                    'unit'              => 'px'
                                ),
                            ),
                            'bottom'            => array(
                                'placeholder'       => __('Bottom', 'bb-powerpack'),
                                'tooltip'           => __('Bottom', 'bb-powerpack'),
                                'icon'              => 'fa-long-arrow-down',
                                'preview'           => array(
                                    'selector'          => '.pp-infolist-title .pp-infolist-title-text',
                                    'property'          => 'margin-bottom',
                                    'unit'              => 'px'
                                ),
                            )
                        )
                    ),
                ),
            ),
            'text_typography'   => array(
                'title'     => __('Description', 'bb-powerpack'),
				'collapsed'			=> true,
                'fields'    => array(
                    'text_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-infolist-description',
                            'property'      => 'color',
                        )
					),
                   'text_typography'	=> array(
						'type'			=> 'typography',
						'label'			=> __('Typography', 'bb-powerpack'),
						'responsive'  	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-infolist-description',
						),
					),
                ),
            ),
		)
	)
));

FLBuilder::register_settings_form('pp_list_item', array(
	'title' => __('Add Item', 'bb-powerpack'),
	'tabs'  => array(
        'general'      => array( // Tab
			'title'         => __('General', 'bb-powerpack'), // Tab title
			'sections'      => array( // Tab Sections
                'type'      => array(
                    'title'     => __('Icon', 'bb-powerpack'),
                    'fields'    => array(
                        'icon_type'      => array(
                            'type'      => 'select',
                            'label'     => __('Icon Type', 'bb-powerpack'),
                            'default'   => 'icon',
                            'options'   => array(
                                'icon'      => __('Icon', 'bb-powerpack'),
                                'image'      => __('Image', 'bb-powerpack'),
                            ),
                            'toggle'        => array(
                                'icon'      => array(
                                    'fields'        => array('icon_select', 'icon_color', 'icon_color_hover', 'icon_background', 'icon_background_hover'),
                                    'tabs'          => array('icon_styles'),
                                ),
                                'image'      => array(
                                    'fields'        => array('image_select'),
                                ),
                            ),
                        ),
                        'icon_select'       => array(
                            'type'      => 'icon',
                            'label'     => __('Icon', 'bb-powerpack'),
                            'show_remove'    => true,
                        ),
                        'image_select'       => array(
                            'type'      => 'photo',
                            'label'     => __('Image Icon', 'bb-powerpack'),
                            'show_remove'    => true,
                            'connections'   => array( 'photo' ),
                        ),
                        'icon_animation'     => array(
                            'type'      => 'select',
                            'label'     => __('Animation', 'bb-powerpack'),
                            'default'     => 'none',
                            'options'       => array(
    							'none'          => __('None', 'bb-powerpack'),
    							'swing'          => __('Swing', 'bb-powerpack'),
    							'pulse'          => __('Pulse', 'bb-powerpack'),
    							'flash'          => __('Flash', 'bb-powerpack'),
    							'fadeIn'          => __('Fade In', 'bb-powerpack'),
    							'fadeInUp'          => __('Fade In Up', 'bb-powerpack'),
    							'fadeInDown'          => __('Fade In Down', 'bb-powerpack'),
    							'fadeInLeft'          => __('Fade In Left', 'bb-powerpack'),
    							'fadeInRight'          => __('Fade In Right', 'bb-powerpack'),
                                'slideInUp'          => __('Slide In Up', 'bb-powerpack'),
    							'slideInDown'          => __('Slide In Down', 'bb-powerpack'),
                                'slideInLeft'          => __('Slide In Left', 'bb-powerpack'),
    							'slideInRight'          => __('Slide In Right', 'bb-powerpack'),
    							'bounceIn'          => __('Bounce In', 'bb-powerpack'),
                                'bounceInUp'          => __('Bounce In Up', 'bb-powerpack'),
    							'bounceInDown'          => __('Bounce In Down', 'bb-powerpack'),
    							'bounceInLeft'          => __('Bounce In Left', 'bb-powerpack'),
    							'bounceInRight'          => __('Bounce In Right', 'bb-powerpack'),
    							'flipInX'          => __('Flip In X', 'bb-powerpack'),
    							'FlipInY'          => __('Flip In Y', 'bb-powerpack'),
    							'lightSpeedIn'          => __('Light Speed In', 'bb-powerpack'),
    							'rotateIn'          => __('Rotate In', 'bb-powerpack'),
                                'rotateInUpLeft'          => __('Rotate In Up Left', 'bb-powerpack'),
                                'rotateInUpRight'          => __('Rotate In Up Right', 'bb-powerpack'),
    							'rotateInDownLeft'          => __('Rotate In Down Left', 'bb-powerpack'),
    							'rotateInDownRight'          => __('Rotate In Down Right', 'bb-powerpack'),
    							'rollIn'          => __('Roll In', 'bb-powerpack'),
    							'zoomIn'          => __('Zoom In', 'bb-powerpack'),
                                'slideInUp'          => __('Slide In Up', 'bb-powerpack'),
    							'slideInDown'          => __('Slide In Down', 'bb-powerpack'),
    							'slideInLeft'          => __('Slide In Left', 'bb-powerpack'),
    							'slideInRight'          => __('Slide In Right', 'bb-powerpack'),
    						)
                        ),
                        'animation_duration'    => array(
                            'type'      => 'text',
                            'label'     => __('Animation Duration', 'bb-powerpack'),
                            'default'     => '1000',
                            'maxlength'     => '4',
                            'size'      => '5',
                            'description'   => _x( 'ms', 'Value unit for animation duration. Such as: "1s"', 'bb-powerpack' ),
                            'preview'       => array(
                                'type'      => 'css',
                                'selector'  => '.animated',
                                'property'  => 'animation-duration'
                            ),
                        ),
                    ),
                ),
                'title'     => array(
                    'title'     => __('Title', 'bb-powerpack'),
                    'fields'    => array(
                        'title'     => array(
                            'type'      => 'text',
                            'label'     => '',
                            'default'     => '',
                            'connections'   => array( 'string', 'html', 'url' ),
                        ),
                    ),
                ),
                'description'    => array(
                    'title'         => __('Description', 'bb-powerpack'),
                    'fields'        => array(
                        'description'   => array(
                            'type'      => 'editor',
                            'label'     => '',
                            'default'   => '',
                            'media_buttons' => false,
                            'rows'      => 4,
                            'connections'   => array( 'string', 'html', 'url' ),
                        ),
                    ),
                ),
                'link_type'     => array(
                    'title'     => __('Link', 'bb-powerpack'),
                    'fields'    => array(
                        'link_type'     => array(
                            'type'      => 'select',
                            'label'     => __('Link Type', 'bb-powerpack'),
                            'default'     => 'none',
                            'options'   => array(
                                ''      => __( 'Default', 'bb-powerpack' ),
                                'none'  => __('None', 'bb-powerpack'),
                                'box'  => __('Complete Box', 'bb-powerpack'),
                                'title'  => __('Title Only', 'bb-powerpack'),
                                'read_more'  => __('Read More', 'bb-powerpack'),
                            ),
                            'toggle'    => array(
                                'box'     => array(
                                    'fields'    => array('link')
                                ),
                                'title'     => array(
                                    'fields'    => array('link')
                                ),
                                'read_more'     => array(
                                    'fields'    => array('read_more_text', 'read_more_color', 'read_more_color_hover', 'link', 'read_more_font', 'read_more_font_size')
                                ),
                            )
                        ),
                        'link'  => array(
							'type'          => 'link',
							'label'         => __('Link', 'bb-powerpack'),
							'placeholder'   => 'http://www.example.com',
							'show_target'	=> true,
							'connections'   => array( 'url' ),
							'preview'       => array(
								'type'          => 'none'
							)
						),
                        'read_more_text'     => array(
                            'type'      => 'text',
                            'label'         => __('Text', 'bb-powerpack'),
                            'default'       => __('Read More', 'bb-powerpack'),
                            'preview'       => array(
                                'type'      => 'text',
                                'selector'  => '.pp-more-link'
                            ),
                        ),
                        'read_more_color'    => array(
                            'type'      => 'color',
                            'label'     => __('Link Color', 'bb-powerpack'),
                            'default'   => '000000',
							'show_reset'    => true,
							'connections'	=> array('color'),
                            'preview'   => array(
                                'type'  => 'css',
                                'selector'  => '.pp-more-link',
                                'property'  => 'color'
                            ),
                        ),
                        'read_more_color_hover'    => array(
                            'type'      => 'color',
                            'label'     => __('Link Hover Color', 'bb-powerpack'),
                            'default'   => 'dddddd',
							'show_reset'    => true,
							'connections'	=> array('color'),
                            'preview'   => array(
                                'type'  => 'css',
                                'selector'  => '.pp-more-link:hover',
                                'property'  => 'color'
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'icon_styles'   => array(
            'title'     => __('Icon Style', 'bb-powerpack'),
            'sections'  => array(
                'icon_styles'   => array(
                    'title'     => '',
                    'fields'    => array(
                        'icon_color'    => array(
    						'type'          => 'color',
    						'label'         => __('Color', 'bb-powerpack'),
							'show_reset'    => true,
							'connections'	=> array('color'),
                            'preview'       => array(
                                'type'          => 'css',
                                'selector'      => '.pp-infolist-icon-inner',
                                'property'      => 'color',
                            )
    					),
                        'icon_color_hover'    => array(
    						'type'          => 'color',
    						'label'         => __('Hover Color', 'bb-powerpack'),
							'show_reset'    => true,
							'connections'	=> array('color'),
    					),
                        'icon_background'    => array(
    						'type'          => 'color',
    						'label'         => __('Background Color', 'bb-powerpack'),
    						'show_reset'    => true,
							'show_alpha'	=> true,
							'connections'	=> array('color'),
                            'preview'       => array(
                                'type'          => 'css',
                                'selector'      => '.pp-infolist-icon-inner .pp-icon',
                                'property'      => 'background',
                            )
    					),
                        'icon_background_hover'    => array(
    						'type'          => 'color',
    						'label'         => __('Background Hover Color', 'bb-powerpack'),
    						'show_reset'    => true,
							'show_alpha'	=> true,
							'connections'	=> array('color'),
    					),
                    ),
                ),
            ),
        ),
    ),
));
