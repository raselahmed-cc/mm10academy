<?php
$source         = isset( $settings->source ) ? $settings->source : 'manual';
$tableheaders   = array();
$tablerows      = array();
$sortable_attrs = $module->get_sortable_attrs();

if ( 'csv_import' === $source ) {
	if ( isset( $settings->csv_import ) && ! empty( $settings->csv_import ) ) {
		$csv_import = (array) $settings->csv_import;
		if ( isset( $csv_import['filename'] ) ) {
			$upload_dir = pp_get_upload_dir();
			$csv_filepath = $upload_dir['path'] . $csv_import['filename'];
			if ( file_exists( $csv_filepath ) ) {
				$csv_content = file_get_contents( $csv_filepath );
				if ( ! empty( trim( $csv_content ) ) ) {
					$csv_rows 	  = explode( "\n", trim( $csv_content ) );
					$tableheaders = str_getcsv( $csv_rows[0] );
					$tablerows 	  = array();

					if ( isset( $settings->first_row_header ) && 'yes' === $settings->first_row_header ) {
						$i = 1;
					} else {
						$i = 0;
					}

					for ( ; $i < count( $csv_rows ); $i++ ) {
						$row 		= new stdClass();
						$row->cell 	= str_getcsv( $csv_rows[ $i ] );
						$tablerows[] = $row;
					}
				}
			}
		}
	}
} elseif ( 'post' === $source || 'acf_relationship' === $source ) {
	$columns = $settings->post_items;

	if ( ! is_array( $columns ) || empty( $columns ) ) {
		$module->render_message( esc_html__( 'Add columns for the table to populate.', 'bb-powerpack' ) );
		return;
	}

	global $post;
	$initial_current_post = $post;

	if ( 'acf_relationship' === $source ) {
		if ( empty( $settings->acf_relational_key ) ) {
			return;
		}
		$query = $module->get_acf_relationship_query();
	} else {
		$query = $module->get_post_query();
	}

	if ( $query instanceof WP_Query && $query->have_posts() ) {

		while ( $query->have_posts() ) {
			$query->the_post();

			$post_id = get_the_ID();
			$cells = array();

			for ( $i = 0; $i < count( $columns ); $i++ ) {
				if ( ! is_object( $columns[ $i ] ) ) {
					continue;
				}

				$column = FLThemeBuilderFieldConnections::connect_settings( $columns[ $i ] );

				if ( ! isset( $tableheaders[ 'col_' . $i ] ) ) {
					$tableheaders[ 'col_' . $i ] = FLThemeBuilderFieldConnections::parse_shortcodes( $column->col_heading );
				}

				$content = FLThemeBuilderFieldConnections::parse_shortcodes( $column->col_content );

				$cells[ 'col_' . $i ] = $content;
			}

			$row = new stdClass;
			$row->cell = $cells;

			$tablerows[] = $row;
		}

		wp_reset_postdata();
	}

	$post = $initial_current_post;
	setup_postdata( $initial_current_post );

} elseif ( 'acf_repeater' === $source ) {
	$repeater_name = isset( $settings->acf_repeater_name ) ? $settings->acf_repeater_name : '';
	$post_id = empty( $settings->acf_repeater_post_id ) ? get_the_ID() : absint( $settings->acf_repeater_post_id );

	if ( ! class_exists( 'acf' ) ) {
		$module->render_message( esc_html__( 'ACF Pro plugin is not active.', 'bb-powerpack' ) );
		return;
	} elseif ( empty( $repeater_name ) ) {
		$module->render_message( esc_html__( 'Provide ACF Repeater Name.', 'bb-powerpack' ) );
		return;
	} elseif ( ! $post_id ) {
		$module->render_message( esc_html__( 'Invalid post ID.', 'bb-powerpack' ) );
		return;
	}

	$field = get_field_object( $repeater_name, $post_id );

	// Check whether it is ACF repeater field or not.
	if ( empty( $field ) || ! is_array( $field ) || 'repeater' !== $field['type'] ) {
		$module->render_message( sprintf( esc_html__( '"%s" ACF Repeater field does not exist.', 'bb-powerpack' ), $repeater_name ) );
		return;
	}

	$sub_fields    = $field['sub_fields'];
	$repeater_rows = $field['value'];
	$image_fields  = array();
	$url_fields    = array();

	// Check if the field is empty.
	if ( ( empty( $sub_fields ) || empty( $repeater_rows ) ) && isset( $_GET['fl_builder'] ) ) {
		esc_html_e( 'ACF Repeater field is empty.', 'bb-powerpack' );
		return;
	}

	foreach ( $sub_fields as $sub_field ) {
		$field_name = $sub_field['name'];

		if ( ( 'image' === $sub_field['type'] || 'file' === $sub_field['type'] ) && isset( $sub_field['return_format'] ) ) {
			$image_fields[ $field_name ] = array(
				'type'          => $sub_field['type'],
				'return_format' => $sub_field['return_format']
			);
		}
		if ( 'url' === $sub_field['type'] ) {
			$url_fields[ $field_name ] = array(
				'type' => $sub_field['type'],
			);
		}

		$tableheaders[] = $sub_field['label'];
	}

	foreach ( $repeater_rows as $index => $repeater_row ) {
		$row_cell = $repeater_row;
		foreach ( $repeater_row as $key => $value ) {
			if ( isset( $image_fields[ $key ] ) ) {
				$url = 'url' === $image_fields[ $key ]['return_format'] ? $value : '';
				$url = 'array' === $image_fields[ $key ]['return_format'] ? $value['url'] : $value;

				if ( 'image' === $image_fields[ $key ]['type'] ) {
					$row_cell[ $key ] = '<img src="' . $url . '" alt="' . basename( $url ) . '" />';
				} else {
					$row_cell[ $key ] = sprintf( '<a href="%s" target="_blank" rel="nofollow noopener">%s</a>', $url, $url );
				}
			} elseif ( isset( $url_fields[ $key ] ) ) {
				$row_cell[ $key ] = sprintf( '<a href="%s" target="_blank" rel="nofollow noopener">%s</a>', $value, $value );
			}
		}

		$row = new stdClass();
		$row->cell = $row_cell;

		$tablerows[] = $row;
	}

} else {
	$tableheaders = $settings->header;
	$tablerows = $settings->rows;
}

$tableheaders = apply_filters( 'pp_table_headers', $tableheaders, $settings );
$tablerows    = apply_filters( 'pp_table_rows', $tablerows, $settings );
	
?>
<div class="pp-table-wrap">

<?php do_action( 'pp_before_table_module', $settings ); ?>

<table class="pp-table-<?php echo $id; ?> pp-table-content tablesaw" <?php echo $sortable_attrs; ?> data-tablesaw-minimap>
	<?php $module->render_colgroup(); ?>
	<?php if ( ! empty( $tableheaders ) && ( in_array( $source, array( 'manual', 'post', 'acf_repeater', 'acf_relationship' ) ) || ( 'csv_import' === $source && 'yes' === $settings->first_row_header ) ) ) { ?>
	<thead>
		<tr>
			<?php
			$i = 1;
			foreach ( $tableheaders as $tableheader ) {
				$tablesaw_attrs = 'data-tablesaw-sortable-col';
				if ( apply_filters( 'pp_table_column_is_numeric', false, $tableheader, $settings ) ) {
					$tablesaw_attrs .= ' data-tablesaw-sortable-numeric';
				}
				echo '<th id="pp-table-col-' . $i++ . '" class="pp-table-col" scope="col" '. $tablesaw_attrs .'>';
					?>
					<span class="pp-table-header-inner">
						<?php if ( 'manual' === $source && isset( $settings->header_icon ) && ! empty( $settings->header_icon ) ) { ?>
							<i class="pp-table-header-icon <?php echo esc_attr( $settings->header_icon ); ?>"></i>
						<?php } ?>
						<?php echo sprintf( '<span class="pp-table-header-text">%s</span>', do_shortcode( trim( $tableheader ) ) ); ?>
					</span>
					<?php
				echo '</th>';
			}
			$i = 0;
			?>
		</tr>
	</thead>
	<?php } ?>
	<tbody>
		<?php
		if ( ! empty( $tablerows[0] ) ) {
			$row_count = 1;
			foreach ( $tablerows as $tablerow ) {
				echo '<tr class="pp-table-row" data-row-index="' . $row_count . '">';
				if ( 'manual' !== $source ) {
					foreach ( $tablerow->cell as $tablecell ) {
						echo '<td>';
						if ( '' !== $tablecell ) {
							echo do_shortcode( trim( $tablecell ) );
						}
						echo '</td>';
					}
				} else {
					$module->render_cells( $tablerow );
				}
				echo '</tr>';
				$row_count++;
			}
		}
		?>
	</tbody>
</table>
<?php
do_action( 'pp_after_table_module', $settings );
?>
</div>
