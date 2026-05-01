<?php

/**
 * @class PPTableModule
 */
class PPTableModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Table', 'bb-powerpack'),
            'description'   => __('A module for table.', 'bb-powerpack'),
			'group'			=> pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-table/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-table/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh' => true,
        ));
	}

	public function enqueue_scripts() {
		$this->add_css( 'tablesaw' );
		$this->add_js( 'tablesaw' );
	}
	
	public function filter_settings( $settings, $helper ) {
		// Header old padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'header_padding', 'padding', 'header_padding' );
		// Rows old padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'rows_padding', 'padding', 'rows_padding' );

		// Handle Header's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'header_font'	=> array(
				'type'			=> 'font'
			),
			'header_custom_font_size'	=> array(
				'type'          => 'font_size',
				'condition'     => ( isset( $settings->header_font_size ) && 'custom' == $settings->header_font_size )
			),
			'header_text_alignment'	=> array(
				'type'			=> 'text_align',
			),
			'header_text_transform'	=> array(
				'type'			=> 'text_transform',
			),
		), 'header_typography' );
		// Handle Row's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'row_font'	=> array(
				'type'			=> 'font'
			),
			'row_custom_font_size'	=> array(
				'type'          => 'font_size',
				'condition'     => ( isset( $settings->row_font_size ) && 'custom' == $settings->row_font_size )
			),
			'rows_text_alignment'	=> array(
				'type'			=> 'text_align',
			),
			'rows_text_transform'	=> array(
				'type'			=> 'text_transform',
			),
		), 'row_typography' );

		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'header_border'	=> array(
				'type'				=> 'color',
			),
		), 'header_border_group' );

		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'rows_border'	=> array(
				'type'				=> 'color',
			),
		), 'cell_border_group' );

		if ( isset( $settings->cells_border ) ) {
			if ( ! isset( $settings->cell_border_group ) ) {
				$settings->cell_border_group = array(
					'style' => '',
					'width' => array(
						'top' => 1,
						'right' => 1,
						'bottom' => 1,
						'left' => 1,
					),
					'color' => ''
				);
			}

			$settings->cell_border_group['style'] = 'solid';

			if ( 'horizontal' == $settings->cells_border ) {
				$settings->cell_border_group['width'] = array(
					'top' => '1',
					'right' => '0',
					'bottom' => '1',
					'left' => '0',
				);
			}
			if ( 'vertical' == $settings->cells_border ) {
				$settings->cell_border_group['width'] = array(
					'top' => '0',
					'right' => '1',
					'bottom' => '0',
					'left' => '1',
				);
			}

			unset( $settings->cells_border );
		}

		if ( isset( $settings->sortable ) ) {
			if ( 'data-tablesaw-sortable data-tablesaw-sortable-switch' === $settings->sortable ) {
				$settings->is_sortable = 'yes';
			}
			if ( '' === $settings->sortable ) {
				$settings->is_sortable = 'no';
			}

			unset( $settings->sortable );
		}

		// Cell grouping.
		if ( 'manual' == $settings->source && ! empty( $settings->rows ) ) {
			$rows = $settings->rows;
			for ( $i = 0; $i < count( $rows ); $i++ ) {
				$row = $rows[ $i ];

				if ( ! is_object( $row ) ) {
					continue;
				}
				// Convert 'cell' field to 'cells'.
				$cell_empty       = $this->is_cell_empty( $row, 'cell' );
				$cell_group_empty = $this->is_cell_empty( $row, 'cells' );

				if ( ! $cell_empty && $cell_group_empty ) {

					$cells = array();

					foreach ( $row->cell as $cell ) {
						$cell_obj              = new stdClass;
						$cell_obj->content     = $cell;
						$cell_obj->icon        = '';
						$cell_obj->colspan     = '';

						$cells[] = $cell_obj;
					}

					$settings->rows[ $i ]->cells = $cells;

				}
			}
		}

		// Clean csv_import field if it's value is not a valid object.
		$csv_import = $settings->csv_import;

		if ( ! is_object( $csv_import ) || ! isset( $csv_import->filepath ) ) {
			$settings->csv_import = '';
		}

		return $settings;
	}

	public static function sanitize_tag( $value ) {
		if ( 'td' === $value || 'th' === $value ) {
			return $value;
		}
		return 'td';
	}

	public function is_cell_empty( $row, $key = 'cell' ) {
		$is_empty = true;

		if ( ! empty( $row->{ $key } ) && 'array' === gettype( $row->{ $key } ) ) {
			$is_empty = ( 1 === count( $row->{ $key } ) && empty( $row->{ $key }[0] ) );
		} else {
			$is_empty = empty( $row->{ $key } );
		}

		return $is_empty;
	}

	public static function get_general_fields() {
		$fields = array(
			'source'		=> array(
				'type'			=> 'select',
				'label'			=> __('Source', 'bb-powerpack'),
				'default'		=> 'manual',
				'options'		=> array(
					'manual'		=> __('Manual', 'bb-powerpack'),
					'csv_import'	=> __('CSV Import', 'bb-powerpack'),
				),
				'toggle'		=> array(
					'manual'		=> array(
						'tabs'			=> array('header', 'row'),
						'fields'        => array('header_icon_size', 'header_icon_spacing', 'cell_icon_pos', 'cell_icon_size', 'cell_icon_spacing'),
					),
					'csv_import'	=> array(
						'fields'		=> array('csv_import', 'first_row_header')
					)
				)
			),
			'csv_import'	=> array(
				'type'			=> 'pp-file',
				'label'			=> __('Upload CSV', 'bb-powerpack'),
				'default'		=> '',
				'accept'		=> '.csv',
				'preview'		=> array(
					'type'			=> 'none'
				)
			),
			'first_row_header'	=> array(
				'type'				=> 'pp-switch',
				'label'				=> __( 'Make first row as Header?', 'bb-powerpack' ),
				'default'			=> 'yes',
				'options'			=> array(
					'yes'				=> __( 'Yes', 'bb-powerpack' ),
					'no'				=> __( 'No', 'bb-powerpack' ),
				),
			),
		);

		if ( class_exists( 'FLThemeBuilderLoader' ) ) {
			$fields['source']['options']['post'] = __( 'Dynamic (Post)', 'bb-powerpack' );
			$fields['source']['toggle']['post'] = array(
				'tabs'     => array( 'post_content' ),
				'sections' => array( 'post_content' )
			);
		}

		if ( class_exists( 'acf' ) ) {
			$fields['source']['options']['acf_repeater'] = __( 'ACF Repeater', 'bb-powerpack' );
			$fields['source']['toggle']['acf_repeater'] = array(
				'fields'	=> array( 'acf_repeater_name', 'acf_repeater_post_id' )
			);
			$fields['acf_repeater_name'] = array(
				'type'	=> 'text',
				'label'	=> __( 'ACF Repeater Name', 'bb-powerpack' ),
				'default' => '',
			);
			$fields['acf_repeater_post_id'] = array(
				'type'	=> 'text',
				'label'	=> __( 'Post ID (optional)', 'bb-powerpack' ),
				'default' => '',
				'help'	=> __( 'You can enter the ID of the page or post where your ACF Repeater field belongs to. Or leave it empty for current post/page ID.', 'bb-powerpack' ),
			);
		}

		if ( class_exists( 'FLThemeBuilderLoader' ) ) {
			$fields['source']['options']['acf_relationship'] = __( 'ACF Relationship Field', 'bb-powerpack' );

			$fields['source']['toggle']['acf_relationship'] = array(
				'tabs'     => array( 'post_content' ),
				'fields' => array( 'acf_relational_type', 'acf_relational_key', 'acf_order', 'acf_order_by' )
			);

			$fields['acf_relational_type'] = array(
				'type'		=> 'select',
				'label'		=> __( 'Type', 'bb-powerpack' ),
				'default'       => 'relationship',
				'options'       => array(
					'relationship'  => __( 'Relationship', 'bb-powerpack' ),
					'user'          => __( 'User', 'bb-powerpack' ),
				),
			);

			$fields['acf_relational_key'] = array(
				'type'          => 'text',
				'label'         => __( 'Key', 'bb-powerpack' ),
			);

			// Order
			$fields['acf_order'] = array(
				'type'    => 'select',
				'label'   => __( 'Order', 'bb-powerpack' ),
				'options' => array(
					'DESC' => __( 'Descending', 'bb-powerpack' ),
					'ASC'  => __( 'Ascending', 'bb-powerpack' ),
				),
			);

			// Order by
			$fields['acf_order_by'] = array(
				'type'    => 'select',
				'label'   => __( 'Order By', 'bb-powerpack' ),
				'default' => 'post__in',
				'options' => array(
					'author'         => __( 'Author', 'bb-powerpack' ),
					'comment_count'  => __( 'Comment Count', 'bb-powerpack' ),
					'date'           => __( 'Date', 'bb-powerpack' ),
					'modified'       => __( 'Date Last Modified', 'bb-powerpack' ),
					'ID'             => __( 'ID', 'bb-powerpack' ),
					'menu_order'     => __( 'Menu Order', 'bb-powerpack' ),
					'meta_value'     => __( 'Meta Value (Alphabetical)', 'bb-powerpack' ),
					'meta_value_num' => __( 'Meta Value (Numeric)', 'bb-powerpack' ),
					'rand'           => __( 'Random', 'bb-powerpack' ),
					'title'          => __( 'Title', 'bb-powerpack' ),
					'name'          => __( 'Slug', 'bb-powerpack' ),
					'post__in'       => __( 'Selection Order', 'bb-powerpack' ),
				),
				'toggle'  => array(
					'meta_value'     => array(
						'fields' => array( 'acf_order_by_meta_key' ),
					),
					'meta_value_num' => array(
						'fields' => array( 'acf_order_by_meta_key' ),
					),
				),
			);

			// Meta Key
			$fields['acf_order_by_meta_key'] = array(
				'type'  => 'text',
				'label' => __( 'Meta Key', 'bb-powerpack' ),
			);
		}

		return $fields;
	}

	public static function get_general_sections() {
		$sections = array(
			'general'    => array(
				'title'	 => '',
				'fields' => PPTableModule::get_general_fields()
			),
		);

		if ( class_exists( 'FLThemeBuilderLoader' ) ) {
			$sections['post_content'] = array(
				'title' => __( 'Content', 'bb-powerpack' ),
				'file'  => BB_POWERPACK_DIR . 'includes/ui-loop-settings-simple.php',
			);
		}

		$sections['sort'] = array(
			'title'         => __('Sortable Table', 'bb-powerpack'),
			'fields'        => array( // Section Fields
				'is_sortable'     => array(
					'type'          => 'pp-switch',
					'label'         => __('Sort', 'bb-powerpack'),
					'default'       => 'yes',
					'options'       => array(
						'yes'	=> __('Yes', 'bb-powerpack'),
						'no'    => __('No', 'bb-powerpack'),
					),
				),
			)
		);

		$sections['scroll'] = array(
			'title'         => __('Scrollable Table', 'bb-powerpack'),
			'fields'        => array( // Section Fields
				'scrollable'     => array(
					'type'          => 'pp-switch',
					'label'         => __('Scroll', 'bb-powerpack'),
					'default'       => 'swipe',
					'options'       => array(
						'swipe'     => __('Yes', 'bb-powerpack'),
						'stack'     => __('No', 'bb-powerpack')
					),
					'toggle'        => array(
						'swipe'         => array(
							'fields'        => array('custom_breakpoint')
						)
					),
					'help'         => __('This will disable stacking and enable swipe/scroll when below the breakpoint', 'bb-powerpack'),
				),
				'custom_breakpoint' => array(
					'type'              => 'unit',
					'label'             => __('Define Custom Breakpoint', 'bb-powerpack'),
					'default'           => '',
					'slider'            => true,
					'help'              => __('Devices equal or below the defined screen width will have this feature.', 'bb-powerpack')
				)
			)
		);

		return $sections;
	}

	public function get_post_query() {
		if ( ! isset( $this->settings->post_type ) || empty( $this->settings->post_type ) ) {
			$this->settings->post_type = 'post';
		}

		$settings = $this->settings;

		$settings->data_source = 'custom_query';
		$settings->post_type  = ! empty( $this->settings->post_type ) ? $this->settings->post_type : 'post';
		$settings->posts_per_page = ! empty( $this->settings->posts_per_page ) || '-1' !== $this->settings->posts_per_page ? $this->settings->posts_per_page : '-1';
		$settings->order = ! empty( $this->settings->order ) ? $this->settings->order : 'DESC';

		$query = FLBuilderLoop::query( $settings );
		return $query;
	}

	public function get_acf_relationship_query() {
		if ( ! isset( $this->settings->acf_relational_key ) || empty( $this->settings->acf_relational_key ) ) {
			return;
		}

		$data = array();
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

		$settings = apply_filters( 'pp_table_acf_relationship_data_settings', $settings, $this->settings );

		$query = FLBuilderLoop::query( $settings );

		return $query;
	}

	public function get_sortable_attrs() {
		$sortable_attrs = $this->settings->is_sortable;

		if ( '' === $sortable_attrs || 'no' === $sortable_attrs ) {
			return '';
		}

		$sortable_attrs = 'data-tablesaw-sortable data-tablesaw-sortable-switch';

		return $sortable_attrs;
	}

	public function render_colgroup() {
		$settings = $this->settings;
		$source   = $settings->source;

		if ( 'post' === $source || 'acf_relationship' === $source ) {
			$columns = $settings->post_items;
	
			if ( is_array( $columns ) && ! empty( $columns ) ) {
				echo '<colgroup>';
				foreach ( $columns as $column ) {
					echo '<col';
					$col_attrs = array();
					$col_attrs_str = '';
					$col_span = 1;
					$col_styles = '';
					if ( isset( $column->col_width ) && ! empty( $column->col_width ) ) {
						$width_unit = isset( $column->col_width_unit ) ? $column->col_width_unit : 'px';
						$col_styles .= 'width: ' . esc_attr( $column->col_width ) . esc_attr( $width_unit ) . ';';
					}
					if ( isset( $column->col_bg_color ) && ! empty( $column->col_bg_color ) ) {
						$col_styles .= ' background-color: ' . pp_get_color_value( $column->col_bg_color ) . ';';
					}
					if ( isset( $column->col_span ) && ! empty( absint( $column->col_span ) ) ) {
						$col_span = $column->col_span;
					}
					$col_attrs['span'] = $col_span;
	
					if ( ! empty( $col_styles ) ) {
						$col_attrs['style'] = $col_styles;
					}
	
					if ( ! empty( $col_attrs ) ) {
						foreach ( $col_attrs as $attr => $val ) {
							$col_attrs_str .= ' ' . $attr . '="' . $val . '"';
						}
	
						echo $col_attrs_str;
					}
					echo '>';
				}
				echo '</colgroup>';
			}
		}
	}

	public function render_cells( $row ) {
		$cells = (array) $row->cells;
		$html  = '';

		if ( empty( $cells ) ) {
			return;
		}

		foreach ( $cells as $cell ) {
			$cell = (array) $cell;

			$tag     = isset( $cell['tag'] ) ? esc_attr( $cell['tag'] ) : 'td';
			$icon    = isset( $cell['icon'] ) ? $cell['icon'] : '';
			$content = isset( $cell['content'] ) ? $cell['content'] : '';
			$colspan = isset( $cell['colspan'] ) ? absint( $cell['colspan'] ) : '';
			$colspan = ! empty( $colspan ) ? ' colspan="' . $colspan . '"' : '';
			$class   = empty( $icon ) && empty( $content ) ? ' class="is-empty"' : '';

			if ( 'td' !== $tag && 'th' !== $tag ) {
				$tag = 'td';
			}

			$html .= "<$tag$colspan$class>";

			if ( ! empty( $icon ) ) {
				$html .= '<div class="pp-table-cell-inner">';

				FLBuilderIcons::enqueue_styles_for_icon( $icon );
				$html .= '<i class="pp-table-cell-icon ' . esc_attr( $icon ) . '"></i>';

				if ( ! empty( $content ) ) {
					$html .= '<div class="pp-table-cell-content">';
					$html .= do_shortcode( trim( $content ) );
					$html .= '</div>';
				}

				$html .= '</div>';
			} else {
				$html .= do_shortcode( trim( $content ) );
			}

			$html .= "</$tag>";
		}

		echo $html;
	}

	public function render_message( $msg ) {
		if ( isset( $_GET['fl_builder'] ) ) {
			echo '<div class="pp-builder-message">';
			echo '<h4>' . sprintf( esc_html__( '[%s] - couldn\'t populate the data', 'bb-powerpack' ), $this->name ) . '</h4>';
			echo $msg;
			echo '</div>';
		}
	}
}

/**
 * Register the module and its form settings.
 */
BB_PowerPack::register_module('PPTableModule', array(
	'general'		=> array(
		'title'			=> __('General', 'bb-powerpack'),
		'sections'		=> PPTableModule::get_general_sections()
	),
	'post_content' => array(
		'title' => __( 'Content', 'bb-powerpack' ),
		'sections'      => array(
            'post_columns'       => array(
                'title'         => __('Columns', 'bb-powerpack'),
                'fields'        => array( // Section Fields
                    'post_items'     => array(
                        'type'          => 'form',
                        'label'        => __('Column', 'bb-powerpack'),
                        'form'          => 'pp_post_content_table_row',
                        'preview_text'  => 'col_heading',
                        'multiple'      => true
                    ),
                )
            ),

        )
	),
	'header'		=> array(
        'title'         => __('Table Header', 'bb-powerpack'),
        'sections'      => array(
			'header_icon'   => array(
				'title'  => __( 'Icon', 'bb-powerpack' ),
				'fields' => array(
					'header_icon' => array(
						'type'        => 'icon',
						'label'       => __( 'Icon', 'bb-pwerpack' ),
						'show_remove' => true
					),
					'header_icon_pos' => array(
						'type'    => 'pp-switch',
						'label'   => __( 'Icon Position', 'bb-powerpack' ),
						'default' => 'left',
						'options' => array(
							'left'  => __( 'Left', 'bb-powerpack' ),
							'top'   => __( 'Top', 'bb-powerpack' ),
							'right' => __( 'Right', 'bb-powerpack' ),
						),
					),
				)
			),
            'headers'       => array(
                'title'         => __('Header Items', 'bb-powerpack'),
                'fields'        => array( // Section Fields
                    'header'     => array(
                        'type'          => 'text',
                        'label'         => __('Header', 'bb-powerpack'),
                        'multiple'       => true,
                    ),
                )
            ),
        )
    ),
	'row'			=> array(
        'title'         => __('Table Rows', 'bb-powerpack'),
        'sections'      => array(
            'Cells'       => array(
                'title'         => __('Rows', 'bb-powerpack'),
                'fields'        => array( // Section Fields
                    'rows'     => array(
                        'type'          => 'form',
                        'label'        => __('Row', 'bb-powerpack'),
                        'form'          => 'pp_content_table_row',
                        'preview_text'  => 'label',
                        'multiple'      => true
                    ),
                )
            ),

        )
    ),
	'style'			=> array(
		'title'	=> __( 'Style', 'bb-powerpack' ),
		'sections'	=> array(
			'header_style'	=> array(
				'title'	=> __('Header', 'bb-powerpack'),
				'fields'	=> array(
					'header_background'			=> array(
                        'type'          => 'color',
                        'default'          => '404040',
                        'label'         => __('Background Color', 'bb-powerpack'),
                        'help'          => __('Change the table header background color', 'bb-powerpack'),
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead',
							'property'	=> 'background'
						)
                    ),
					'header_font_color'     => array(
                        'type'          => 'color',
                        'default'       => 'ffffff',
						'label'         => __('Text Color', 'bb-powerpack'),
						'connections'	=> array('color'),
                        'help'          => __('Change the table header font color', 'bb-powerpack'),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead tr th,
											.pp-table-content.tablesaw-sortable th.tablesaw-sortable-head,
											.pp-table-content.tablesaw-sortable tr:first-child th.tablesaw-sortable-head',
							'property'	=> 'color',
						)
                    ),
					'header_border_group' => array(
						'type'   => 'border',
						'label'  => __( 'Border', 'bb-powerpack' ),
						'default' => array(
							'style' => 'solid',
							'color' => 'rgba(0,0,0,0)',
							'width' => array(
								'top' => 1,
								'right' => 1,
								'bottom' => 1,
								'left' => 1,
							),
						),
						'disabled' => array(
							'default' => array( 'radius', 'shadow' ),
							'medium' => array( 'radius', 'shadow' ),
							'responsive' => array( 'radius', 'shadow' ),
						),
						'preview' => array(
							'type' => 'css',
							'selector' => '.pp-table-content thead tr th'
						),
					),
					'header_vertical_alignment'	=> array(
						'type'		=> 'pp-switch',
						'label'		=> __('Vertical Alignment', 'bb-powerpack'),
						'default'	=> 'middle',
						'options'       => array(
							'top'          => __('Top', 'bb-powerpack'),
							'middle'         => __('Center', 'bb-powerpack'),
							'bottom'         => __('Bottom', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead tr th',
							'property'	=> 'vertical-align'
						)
					),
					'header_padding'	=> array(
                        'type'				=> 'dimension',
						'label'				=> __('Padding', 'bb-powerpack'),
						'default'			=> 10,
						'slider'			=> true,
						'units'				=> array( 'px' ),
                        'preview'			=> array(
                            'type'				=> 'css',
                            'selector'			=> '.pp-table-content thead tr th,
													.pp-table-content.tablesaw-sortable th.tablesaw-sortable-head,
													.pp-table-content.tablesaw-sortable tr:first-child th.tablesaw-sortable-head',
                            'property'			=> 'padding',
                            'unit'				=> 'px'
                        ),
                        'responsive'		=> true,
					),
					'header_icon_size' => array(
						'type'       => 'unit',
						'label'      => __( 'Icon Size', 'bb-powerpack' ),
						'default'    => '',
						'slider'     => true,
						'responsive' => true,
						'units'      => array( 'px' ),
					),
					'header_icon_spacing' => array(
						'type'       => 'unit',
						'label'      => __( 'Icon Spacing', 'bb-powerpack' ),
						'default'    => 10,
						'slider'     => true,
						'responsive' => true,
						'units'      => array( 'px' ),
					),
				)
			),
			'row_style'	=> array(
				'title'		=> __( 'Rows', 'bb-powerpack' ),
				'collapsed'	=> true,
				'fields'	=> array(
					'rows_background'     => array(
                        'type'          => 'color',
                        'default'          => 'ffffff',
                        'label'         => __('Background Color', 'bb-powerpack'),
                        'help'          => __('Change row background color', 'bb-powerpack'),
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content tbody tr',
							'property'	=> 'background'
						)
                    ),
					'rows_font_color'     => array(
                        'type'          => 'color',
                        'default'       => '',
                        'label'         => __('Text Color', 'bb-powerpack'),
                        'help'          => __('Change row text color', 'bb-powerpack'),
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content tbody tr td',
							'property'	=> 'color'
						)
                    ),
                    'rows_even_background'     => array(
                        'type'          => 'color',
                        'default'          => 'ffffff',
                        'label'         => __('Even Rows Background Color', 'bb-powerpack'),
                        'help'          => __('Change even rows background color', 'bb-powerpack'),
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content .even',
							'property'	=> 'background'
						)
                    ),
					'rows_font_even'     => array(
                        'type'          => 'color',
                        'default'       => '',
                        'label'         => __('Even Rows Text Color', 'bb-powerpack'),
                        'help'          => __('Change even rows text color', 'bb-powerpack'),
						'show_reset'	=> true,
						'connections'	=> array('color'),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content .even td',
							'property'	=> 'color'
						)
					),
                    'rows_odd_background'     => array(
                        'type'          => 'color',
                        'default'          => 'ffffff',
                        'label'         => __('Odd Rows Background Color', 'bb-powerpack'),
                        'help'          => __('Change odd rows background color', 'bb-powerpack'),
						'show_reset'	=> true,
						'show_alpha'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content .odd',
							'property'	=> 'background'
						)
                    ),
					'rows_font_odd'     => array(
                        'type'          => 'color',
                        'default'       => '',
						'label'         => __('Odd Rows Text Color', 'bb-powerpack'),
						'connections'	=> array('color'),
                        'help'          => __('Change odd rows text color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content .odd td',
							'property'	=> 'color'
						)
                    ),
					'rows_vertical_alignment' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Vertical Alignment', 'bb-powerpack'),
						'default'	=> 'middle',
						'options'       => array(
							'top'          => __('Top', 'bb-powerpack'),
							'middle'       => __('Center', 'bb-powerpack'),
							'bottom'       => __('Bottom', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content tbody tr td',
							'property'	=> 'vertical-align'
						)
					),
					'rows_padding'	=> array(
                        'type'				=> 'dimension',
                        'label'				=> __('Padding', 'bb-powerpack'),
						'slider'			=> true,
						'units'				=> array( 'px' ),
                        'preview'			=> array(
                            'type'				=> 'css',
                            'selector'			=> '.pp-table-content tbody tr td',
                            'property'			=> 'padding',
                            'unit'				=> 'px'
                        ),
                        'responsive'		=> true,
					),
				)
			),
			'cells_style'	=> array(
				'title'		=> __('Cell', 'bb-powerpack'),
				'collapsed'	=> true,
				'fields'	=> array(
					'cell_border_group' => array(
						'type'   => 'border',
						'label'  => __( 'Border', 'bb-powerpack' ),
						'default' => array(
							'style' => 'solid',
							'color' => 'rgba(0,0,0,0)',
							'width' => array(
								'top' => 1,
								'right' => 1,
								'bottom' => 1,
								'left' => 1,
							),
						),
						'disabled' => array(
							'default' => array( 'radius', 'shadow' ),
							'medium' => array( 'radius', 'shadow' ),
							'responsive' => array( 'radius', 'shadow' ),
						),
						'preview' => array(
							'type' => 'css',
							'selector' => '.pp-table-content tbody tr td'
						),
					),
					'cell_icon_pos' => array(
						'type'    => 'pp-switch',
						'label'   => __( 'Icon Position', 'bb-powerpack' ),
						'default' => 'left',
						'options' => array(
							'left'  => __( 'Left', 'bb-powerpack' ),
							'top'   => __( 'Top', 'bb-powerpack' ),
							'right' => __( 'Right', 'bb-powerpack' ),
						),
					),
					'cell_icon_size' => array(
						'type'       => 'unit',
						'label'      => __( 'Icon Size', 'bb-powerpack' ),
						'default'    => '',
						'slider'     => true,
						'responsive' => true,
						'units'      => array( 'px' ),
					),
					'cell_icon_spacing' => array(
						'type'       => 'unit',
						'label'      => __( 'Icon Spacing', 'bb-powerpack' ),
						'default'    => 10,
						'slider'     => true,
						'responsive' => true,
						'units'      => array( 'px' ),
					),
				)
			)
		)
	),
	'typography'	=> array(
		'title'	=> __('Typography', 'bb-powerpack'),
		'sections'	=> array(
			'header_typography'	=> array(
				'title'	=>	__('Header', 'bb-powerpack'),
				'fields'	=> array(
					'header_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack' ),
						'responsive'  	   => true,
					),
				)
			),
			'rows_typography'	=> array(
				'title'		=> __('Rows', 'bb-powerpack'),
				'collapsed'	=> true,
				'fields'	=> array(
					'row_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack' ),
						'responsive'  	   => true,
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-table-content tbody tr td'
						),
					),
				)
			)

		)
	)
));

FLBuilder::register_settings_form('pp_content_table_row', array(
	'title' => __('Row Settings', 'bb-powerpack'),
	'tabs'  => array(
        'general'	=> array( // Tab
			'title'         => __('Content', 'bb-powerpack'), // Tab title
			'sections'      => array( // Tab Sections
				'general'       => array(
					'title'     => '',
					'fields'    => array(
						'label'         => array(
							'type'          => 'text',
							'label'         => __('Row Label', 'bb-powerpack'),
							'help'          => __('A label to identify this panel.', 'bb-powerpack'),
							'connections'	=> array('string')
						),
					)
				),
				'cells' => array(
					'title'     => __( 'Cells', 'bb-powerpack' ),
					'fields'    => array(
						// 'cell'         => array(
						// 	'type'          => 'textarea',
						// 	'label'         => __('Cell', 'bb-powerpack'),
                        //     'multiple'      => true,
						// ),
						'cells' => array(
							'type' => 'pp-group',
							'label' => __( 'Cell', 'bb-powerpack' ),
							'multiple' => true,
							'fields' => array(
								'tag' => array(
									'type'    => 'select',
									'label'   => __( 'HTML Tag', 'bb-powerpack' ),
									'default' => 'td',
									'sanitize' => 'PPTableModule::sanitize_tag',
									'options' => array(
										'td' => 'td',
										'th' => 'th'
									),
								),
								'content' => array(
									'type'  => 'textarea',
									'label' => __( 'Content', 'bb-powerpack' ),
								),
								'icon'  => array(
									'type'        => 'icon',
									'label'       => __( 'Icon', 'bb-powerpack' ),
									'show_remove' => true
								),
								'colspan' => array(
									'type'  => 'unit',
									'label' => __( 'Column Span', 'bb-powerpack' ),
									'units' => array( 'colspan' )
								),
							),
						),
					)
				),
			)
		),
	)
));

FLBuilder::register_settings_form('pp_post_content_table_row', array(
	'title' => __('Post Content', 'bb-powerpack'),
	'tabs'  => array(
        'general'	=> array( // Tab
			'title'         => __('Columns', 'bb-powerpack'), // Tab title
			'sections'      => array( // Tab Sections
				'general'       => array(
					'title'     => __( 'Content', 'bb-powerpack' ),
					'fields'    => array(
						'col_heading' => array(
							'type'        => 'text',
							'label'       => __('Column Heading', 'bb-powerpack'),
							'connections' => array('string', 'custom'),
							'preview'     => array(
								'type' => 'none'
							),
						),
                        'col_content' => array(
							'type'        => 'textarea',
							'label'       => __('Content', 'bb-powerpack'),
                            'connections' => array('string', 'html', 'custom'),
							'preview'     => array(
								'type' => 'none'
							),
						),
					)
				),
				'style' => array(
					'title' => __( 'Style', 'bb-powerpack' ),
					'fields' => array(
						// 'col_span' => array(
						// 	'type'     => 'unit',
						// 	'label'    => __('Column Span', 'bb-powerpack'),
						// ),
						'col_width' => array(
							'type'     => 'unit',
							'label'    => __('Column Width', 'bb-powerpack'),
							'units'    => array( 'px', '%' ),
						),
						'col_bg_color' => array(
							'type'     => 'color',
							'label'    => __('Background Color', 'bb-powerpack'),
							'show_alpha' => true,
							'show_reset' => true,
							'connections' => array( 'color' ),
						),
					),
				),
			)
		),
	)
));
