<?php
/**
 * @class PPSearchFormModule
 */
class PPSearchFormModule extends FLBuilderModule {

    /**
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'              => __('Search Form', 'bb-powerpack'),
            'description'       => __('A module for better search form.', 'bb-powerpack'),
            'group'             => pp_get_modules_group(),
            'category'		    => pp_get_modules_cat( 'content' ),
            'dir'               => BB_POWERPACK_DIR . 'modules/pp-search-form/',
            'url'               => BB_POWERPACK_URL . 'modules/pp-search-form/',
            'editor_export'     => true,
            'enabled'           => true,
            'partial_refresh'   => true,
        ));

		add_filter( 'fl_builder_loop_query', __CLASS__ . '::build_query', 10, 2 );
	}

	public function enqueue_icon_styles() {
		$enqueue = false;
		$settings = $this->settings;

		if ( 'classic' === $settings->style && 'icon' === $settings->button_type && ! empty( $settings->icon ) ) {
			$enqueue = true;
		}
		if ( 'minimal' === $settings->style && isset( $settings->input_icon ) && ! empty( $settings->input_icon ) ) {
			$enqueue = true;
		}
		if ( 'full_screen' === $settings->style && isset( $settings->toggle_icon ) && ! empty( $settings->toggle_icon ) ) {
			$enqueue = true;
		}

		if ( $enqueue && is_callable( parent::class . '::enqueue_icon_styles' ) ) {
			parent::enqueue_icon_styles();
		}
	}

	public function render_input_attrs() {
		$attrs = apply_filters( 'pp_search_form_input_attrs', array(
			'placeholder'	=> esc_attr( $this->settings->placeholder ),
			'class' 		=> array( 'pp-search-form__input' ),
			'type' 			=> 'search',
			'name' 			=> 's',
			'title' 		=> __( 'Search', 'bb-powerpack' ),
			'value' 		=> get_search_query(),
		), $this->settings );

		if ( is_array( $attrs['class'] ) ) {
			$attrs['class'] = implode( ' ', $attrs['class'] );
		}

		$attr_str = '';

		foreach ( $attrs as $key => $value ) {
			$attr_str .= ' ' . $key . '="' . $value . '"';
		}

		echo $attr_str;
	}

	public function render_content_inputs() {
		$settings = $this->settings;
		
		if ( isset( $settings->post_type ) && ! empty( $settings->post_type ) ) {
			$post_types = $settings->post_type;
		} elseif ( isset( $_REQUEST['post_types'] ) && ! empty( $_REQUEST['post_types'] ) ) {
			$post_types = explode( ',', esc_attr( wp_unslash( $_REQUEST['post_types'] ) ) );
		}

		if ( isset( $post_types ) && ! empty( $post_types ) ) {
			?>
			<input type="hidden" name="post_types" value="<?php echo implode( ',', $post_types ); ?>" />
			<?php

			$output_taxonomies = array();

			foreach ( $post_types as $post_type ) {
				// Taxonomies
				$taxonomies = FLBuilderLoop::taxonomies( $post_type );

				foreach ( $taxonomies as $tax_slug => $tax ) {
					$setting_key = 'tax_' . $post_type . '_' . $tax_slug;
					$matching = $setting_key . '_matching';

					if ( isset( $settings->{$setting_key} ) && ! empty( $settings->{$setting_key} ) ) {
						$output_taxonomies[ $tax_slug ] = array(
							'terms' => $settings->{$setting_key}
						);
					}
					if ( isset( $settings->{$matching} ) && isset( $output_taxonomies[ $tax_slug ] ) ) {
						$output_taxonomies[ $tax_slug ]['matching'] = wp_validate_boolean( $settings->{$matching} );
					}
				}
			}

			// Check from URL.
			if ( empty( $output_taxonomies ) ) {
				if ( isset( $_REQUEST['taxonomies'] ) && ! empty( $_REQUEST['taxonomies'] ) ) {
					$taxonomies = explode( ',', esc_attr( wp_unslash( $_REQUEST['taxonomies'] ) ) );

					foreach ( $taxonomies as $tax_slug ) {
						if ( isset( $_REQUEST["tax_$tax_slug"] ) && ! empty( $_REQUEST["tax_$tax_slug"] ) ) {
							$output_taxonomies[ $tax_slug ] = array(
								'terms' => esc_attr( wp_unslash( $_REQUEST["tax_$tax_slug"] ) )
							);
						}
						if ( isset( $_REQUEST["{$tax_slug}_exclude"] ) && isset( $output_taxonomies[ $tax_slug ] ) ) {
							$output_taxonomies[ $tax_slug ]['matching'] = 0;
						}
					}
				}
			}

			if ( ! empty( $output_taxonomies ) ) {
				?>
				<input type="hidden" name="taxonomies" value="<?php echo implode( ',', array_keys( $output_taxonomies ) ); ?>" />
				<?php
				foreach ( $output_taxonomies as $tax_slug => $tax_data ) {
					?>
					<input type="hidden" name="tax_<?php echo esc_attr( $tax_slug ); ?>" value="<?php echo esc_attr( $tax_data['terms'] ); ?>" />
					<?php
					if ( isset( $tax_data['matching'] ) && ! $tax_data['matching'] ) {
						?>
						<input type="hidden" name="<?php echo esc_attr( $tax_slug ); ?>_exclude" value="1" />
						<?php
					}
				}
			}
		}
	}

	public static function filter_query( $query ) {
		$post_types = array();

		if ( isset( $_REQUEST['post_types'] ) && ! empty( $_REQUEST['post_types'] ) ) {
			$post_types = explode( ',', esc_attr( wp_unslash( $_REQUEST['post_types'] ) ) );

			if ( empty( $post_types ) ) {
				return;
			}

			$query->set( 'post_type', $post_types );
			$query->is_modified_for_search = true;
		}

		if ( isset( $_REQUEST['taxonomies'] ) && ! empty( $_REQUEST['taxonomies'] ) ) {
			$taxonomies = explode( ',', esc_attr( wp_unslash( $_REQUEST['taxonomies'] ) ) );

			if ( empty( $taxonomies ) ) {
				return;
			}

			$tax_query = $query->get( 'tax_query' );

			if ( ! is_array( $tax_query ) ) {
				$tax_query = array();
			}

			$count = 0;

			foreach ( $taxonomies as $tax_slug ) {
				if ( ! isset( $_REQUEST["tax_$tax_slug"] ) || empty( $_REQUEST["tax_$tax_slug"] ) ) {
					continue;
				}
				$terms = explode( ',', esc_attr( wp_unslash( $_REQUEST["tax_$tax_slug"] ) ) );
				$tax_query_args = array(
					'taxonomy' => $tax_slug,
					'field'    => 'term_id',
					'terms'    => $terms
				);
				$exclude = $tax_slug . '_exclude';
				if ( isset( $_REQUEST[ $exclude ] ) && $_REQUEST[ $exclude ] ) {
					$tax_query_args['operator'] = 'NOT IN';
				}
				$tax_query[] = $tax_query_args;
				$count++;
			}

			if ( $count > 0 ) {
				if ( count( $post_types ) > 1 ) {
					$tax_query['ralation'] = 'OR';
				}
				$query->set( 'tax_query', $tax_query );
				$query->is_modified_for_search = true;
			}
		}

		return $query;
	}

	public static function build_query( $query, $settings ) {
		if ( ! $query instanceof WP_Query ) {
			return $query;
		}

		if ( ! $query->is_search ) {
			return $query;
		}

		if ( ! isset( $settings->pp_content_grid ) && false === strpos( $settings->class, 'pp-search-result' ) ) {
			return $query;
		}

		$query = self::filter_query( $query );

		if ( isset( $query->is_modified_for_search ) && $query->is_modified_for_search ) {
			$query = new WP_Query( $query->query_vars );
		}

		return $query;
	}
}

BB_PowerPack::register_module('PPSearchFormModule', array(
	'general'		=> array(
		'title'			=> __('General', 'bb-powerpack'),
		'sections'		=> array(
			'general'		=> array(
				'title'			=> '',
				'fields'		=> array(
					'style'			=> array(
						'type'			=> 'select',
						'label'			=> __('Layout', 'bb-powerpack'),
						'default'		=> 'classic',
						'options'		=> array(
							'classic'		=> __('Classic', 'bb-powerpack'),
							'minimal'		=> __('Minimal', 'bb-powerpack'),
							'full_screen'	=> __('Full Screen', 'bb-powerpack')
						),
						'toggle'		=> array(
							'classic'		=> array(
								'sections'		=> array('size', 'button', 'button_style', 'button_typography'),
							),
							'minimal'		=> array(
								'sections'		=> array( 'minimal' ),
								'fields'		=> array('size', 'input_icon_size'),
							),
							'full_screen'	=> array(
								'sections'		=> array('toggle_size', 'toggle', 'toggle_style', 'overlay'),
							)
						)
					),
					'placeholder'	=> array(
						'type'			=> 'text',
						'label'			=> __('Placeholder', 'bb-powerpack'),
						'default'		=> __('Search', 'bb-powerpack'),
						'connections'	=> array('string'),
					),
					'toggle_size'		=> array(
						'type'			=> 'unit',
						'label'			=> __('Toggle Size', 'bb-powerpack'),
						'default'		=> '50',
						'units'         => array( 'px' ),
						'slider'		=> true,
						'help'          => __( 'It controls the overall size of the toggle button. The same size will be applied to the icon also.', 'bb-powerpack' ),
					),
					'size'			=> array(
						'type'			=> 'unit',
						'label'			=> __('Form Height', 'bb-powerpack'),
						'default'		=> '50',
						'slider'		=> true,
					),
				)
			),
			'button'	=> array(
				'title'		=> __('Button', 'bb-powerpack'),
				'fields'	=> array(
					'button_type'	=> array(
						'type'			=> 'pp-switch',
						'label'			=> __('Type', 'bb-powerpack'),
						'default'		=> 'icon',
						'options'		=> array(
							'icon'			=> __('Icon', 'bb-powerpack'),
							'text'			=> __('Text', 'bb-powerpack')
						),
						'toggle'		=> array(
							'icon'			=> array(
								'fields'		=> array('icon', 'icon_size')
							),
							'text'			=> array(
								'sections'		=> array('button_typography'),
								'fields'		=> array('button_text')
							)
						)
					),
					'icon'			=> array(
						'type'			=> 'icon',
						'label'			=> __('Icon', 'bb-powerpack'),
						'default'		=> 'fa fa-search',
						'show_remove'	=> true
					),
					'button_text'	=> array(
						'type'			=> 'text',
						'label'			=> __('Text', 'bb-powerpack'),
						'default'		=> __('Search', 'bb-powerpack'),
						'connections'	=> array('string'),
						'preview'		=> array(
							'type'			=> 'text',
							'selector'		=> '.pp-search-form--button-type-text .pp-search-form__submit'
						)
					)
				)
			),
			'toggle'	=> array(
				'title'		=> __('Toggle', 'bb-powerpack'),
				'fields'	=> array(
					'toggle_icon'	=> array(
						'type'			=> 'icon',
						'label'			=> __('Icon', 'bb-powerpack'),
						'default'		=> 'fa fa-search',
						'show_remove'	=> true
					),
					'toggle_align'	=> array(
						'type'			=> 'align',
						'label'			=> __('Alignment', 'bb-powerpack'),
						'default'		=> 'center',
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form--style-full_screen .pp-search-form',
							'property'		=> 'text-align'
						)
					),
				)
			),
			'minimal'	=> array(
				'title'		=> __( 'Minimal', 'bb-powerpack' ),
				'fields'	=> array(
					'input_icon'	=> array(
						'type'			=> 'icon',
						'label'			=> __('Input Icon', 'bb-powerpack'),
						'default'		=> 'fa fa-search',
						'show_remove'	=> true
					),
				),
			),
		)
	),
	'content'    => array(
		'title' => __( 'Content', 'bb-powerpack' ),
		'file'  => BB_POWERPACK_DIR . 'modules/pp-search-form/includes/content-tab.php',
	),
	'style'		=> array(
		'title'		=> __('Style', 'bb-powerpack'),
		'sections'	=> array(
			'input_style'	=> array(
				'title'		=> __('Input', 'bb-powerpack'),
				'fields'	=> array(
					'input_icon_size'	=> array(
						'type'			=> 'unit',
						'label'			=> __('Icon Size', 'bb-powerpack'),
						'default'		=> '',
						'slider'		=> true,
						'responsive'	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__icon i',
							'property'		=> 'font-size',
							'unit'			=> 'px'
						)
					),
					'input_height'		=> array(
						'type'			=> 'unit',
						'label'			=> __('Input Height', 'bb-powerpack'),
						'default'		=> '50',
						'slider'		=> true,
						'responsive'	=> true,
					),
					'input_bg_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Background Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form-wrap:not(.pp-search-form--style-full_screen) .pp-search-form__container:not(.pp-search-form--lightbox)',
							'property'		=> 'background-color'
						)
					),
					'input_focus_bg_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Background Focus Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form-wrap:not(.pp-search-form--style-full_screen) .pp-search-form--focus .pp-search-form__container:not(.pp-search-form--lightbox)',
							'property'		=> 'background-color'
						)
					),
					'input_placeholder_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Placeholder Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'none',
						)
					),
					'input_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Text Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__input',
							'property'		=> 'color'
						)
					),
					'input_focus_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Text Focus Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__input:focus',
							'property'		=> 'color'
						)
					),
					'input_border'	=> array(
						'type'			=> 'border',
						'label'			=> __('Border & Shadow', 'bb-powerpack'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__container:not(.pp-search-form--lightbox)'
						)
					),
					'input_focus_border_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Border Focus Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form--focus .pp-search-form__container:not(.pp-search-form--lightbox)',
							'property'		=> 'border-color'
						)
					)
				)
			),
			'button_style'	=> array(
				'title'			=> __('Button', 'bb-powerpack'),
				'collapsed'		=> true,
				'fields'		=> array(
					'button_bg_color'	=> array(
						'type'				=> 'color',
						'label'				=> __('Background Color', 'bb-powerpack'),
						'default'			=> '',
						'show_reset'		=> true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'property'			=> 'background-color',
							'selector'			=> '.pp-search-form__submit'
						)
					),
					'button_bg_hover_color'	=> array(
						'type'				=> 'color',
						'label'				=> __('Background Hover Color', 'bb-powerpack'),
						'default'			=> '',
						'show_reset'		=> true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'property'			=> 'background-color',
							'selector'			=> '.pp-search-form__submit:hover'
						)
					),
					'button_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Text/Icon Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'property'		=> 'color',
							'selector'		=> '.pp-search-form__submit'
						)
					),
					'button_hover_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Text/Icon Hover Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'		=> array(
							'type'			=> 'css',
							'property'		=> 'color',
							'selector'		=> '.pp-search-form__submit:hover'
						)
					),
					'icon_size'		=> array(
						'type'			=> 'unit',
						'label'			=> __('Icon Size', 'bb-powerpack'),
						'default'		=> '16',
						'slider'		=> true,
						'responsive'	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form--button-type-icon .pp-search-form__submit',
							'property'		=> 'font-size',
							'unit'			=> 'px'
						)
					),
					'button_width'	=> array(
						'type'			=> 'unit',
						'label'			=> __('Width', 'bb-powerpack'),
						'default'		=> '1',
						'slider'		=> array(
							'min'			=> '1',
							'max'			=> '10',
							'step'			=> '.1'
						),
						'responsive'	=> true,
					)
				)
			),
			'toggle_style'		=> array(
				'title'				=> __('Toggle', 'bb-powerpack'),
				'collapsed'			=> true,
				'fields'			=> array(
					'toggle_icon_bg_color'	=> array(
						'type'				=> 'color',
						'label'				=> __('Background Color', 'bb-powerpack'),
						'default'			=> '',
						'show_reset'		=> true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-search-form__toggle i',
							'property'			=> 'background-color'
						)
					),
					'toggle_icon_bg_hover_color'	=> array(
						'type'				=> 'color',
						'label'				=> __('Background Hover Color', 'bb-powerpack'),
						'default'			=> '',
						'show_reset'		=> true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-search-form__toggle:hover i',
							'property'			=> 'background-color'
						)
					),
					'toggle_icon_color'	=> array(
						'type'				=> 'color',
						'label'				=> __('Color', 'bb-powerpack'),
						'default'			=> '',
						'show_reset'		=> true,
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-search-form__toggle i',
							'property'			=> 'color'
						)
					),
					'toggle_icon_hover_color'	=> array(
						'type'				=> 'color',
						'label'				=> __('Hover Color', 'bb-powerpack'),
						'default'			=> '',
						'show_reset'		=> true,
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-search-form__toggle:hover i',
							'property'			=> 'color'
						)
					),
					'toggle_icon_size'	=> array(
						'type'			=> 'unit',
						'label'			=> __('Icon Size', 'bb-powerpack'),
						'default'		=> '',
						'slider'		=> true,
						'help'          => __( 'It controls the size of the icon only inside the toggle button. It uses "em" unit and the value gets divided by 100.', 'bb-powerpack' ),
					),
					'toggle_icon_border_width'	=> array(
						'type'			=> 'unit',
						'label'			=> __('Border Width', 'bb-powerpack'),
						'default'		=> '',
						'units'			=> array('px'),
						'slider'		=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__toggle i',
							'property'		=> 'border-width',
							'unit'			=> 'px'
						)
					),
					'toggle_icon_radius'	=> array(
						'type'			=> 'unit',
						'label'			=> __('Radius', 'bb-powerpack'),
						'default'		=> '',
						'units'			=> array('px'),
						'slider'		=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-search-form__toggle i',
							'property'		=> 'border-radius',
							'unit'			=> 'px'
						)
					),
				)
			),
			'overlay'		=> array(
				'title'			=> __('Overlay', 'bb-powerpack'),
				'collapsed'		=> true,
				'fields'		=> array(
					'overlay_bg_color'	=> array(
						'type'				=> 'color',
						'label'				=> __('Background Color', 'bb-powerpack'),
						'default'			=> '',
						'show_reset'		=> true,
						'show_alpha'		=> true,
						'connections'		=> array('color'),
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-search-form--style-full_screen .pp-search-form__container',
							'property'			=> 'background-color'
						)
					)
				)
			)
		)
	),
	'typography'	=> array(
		'title'			=> __('Typography', 'bb-powerpack'),
		'sections'		=> array(
			'input_typography'	=> array(
				'title'				=> __('Input', 'bb-powerpack'),
				'fields'			=> array(
					'input_typography'	=> array(
						'type'				=> 'typography',
						'label'				=> __('Typography', 'bb-powerpack'),
						'responsive'		=> true,
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> 'input[type="search"].pp-search-form__input'
						)
					)
				)
			),
			'button_typography'	=> array(
				'title'				=> __('Button', 'bb-powerpack'),
				'fields'			=> array(
					'button_typography'	=> array(
						'type'				=> 'typography',
						'label'				=> __('Typography', 'bb-powerpack'),
						'responsive'		=> true,
						'preview'			=> array(
							'type'				=> 'typography',
							'selector'			=> '.pp-search-form--button-type-text .pp-search-form__submit'
						)
					)
				)
			)
		)
	)
));