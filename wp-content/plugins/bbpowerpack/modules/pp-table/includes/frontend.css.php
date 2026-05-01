<?php
// Header Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'header_padding',
	'selector' 		=> ".fl-node-$id .pp-table-content thead tr th,
						.fl-node-$id .pp-table-content.tablesaw-sortable th.tablesaw-sortable-head,
						.fl-node-$id .pp-table-content.tablesaw-sortable tr:first-child th.tablesaw-sortable-head",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'header_padding_top',
		'padding-right' 	=> 'header_padding_right',
		'padding-bottom' 	=> 'header_padding_bottom',
		'padding-left' 		=> 'header_padding_left',
	),
) );
// Rows Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'rows_padding',
	'selector' 		=> ".fl-node-$id .pp-table-content tbody tr th, .fl-node-$id .pp-table-content tbody tr td",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'rows_padding_top',
		'padding-right' 	=> 'rows_padding_right',
		'padding-bottom' 	=> 'rows_padding_bottom',
		'padding-left' 		=> 'rows_padding_left',
	),
) );
// Header Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'header_typography',
	'selector' 		=> ".fl-node-$id .pp-table-content thead tr th,
						.fl-node-$id .pp-table-content.tablesaw-sortable th.tablesaw-sortable-head,
						.fl-node-$id .pp-table-content.tablesaw-sortable tr:first-child th.tablesaw-sortable-head",
) );
// Row Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'row_typography',
	'selector' 		=> ".fl-node-$id .pp-table-content tbody tr th, .fl-node-$id .pp-table-content tbody tr td",
) );
?>
.fl-node-<?php echo $id; ?> .tablesaw-bar .tablesaw-advance a.tablesaw-nav-btn {
	float: none !important;
}

.fl-node-<?php echo $id; ?> .pp-table-content thead th,
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw thead th,
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw-sortable th.tablesaw-sortable-head button {
    background: <?php echo pp_get_color_value($settings->header_background); ?>;
	border: 0;
}

.fl-node-<?php echo $id; ?> .pp-table-content thead tr th,
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw-sortable th.tablesaw-sortable-head,
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw-sortable tr:first-child th.tablesaw-sortable-head button {
    color: <?php echo pp_get_color_value($settings->header_font_color); ?>;
}

.fl-node-<?php echo $id; ?> .pp-table-content thead tr th {
	vertical-align: <?php echo $settings->header_vertical_alignment; ?>;
}

<?php if ( isset( $settings->header_icon ) && ! empty( $settings->header_icon ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-table-content .pp-table-header-inner {
	display: flex;
	<?php if ( 'top' === $settings->header_icon_pos ) { ?>
	flex-direction: column;
	<?php } ?>
	<?php if ( 'right' === $settings->header_icon_pos ) { ?>
	flex-direction: row-reverse;
	<?php } ?>
	<?php if ( is_array( $settings->header_typography ) && isset( $settings->header_typography['text_align'] ) ) { ?>
		<?php if ( 'center' === $settings->header_typography['text_align'] ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'left' === $settings->header_typography['text_align'] ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'right' === $settings->header_typography['text_align'] ) { ?>
			justify-content: flex-end;
		<?php } ?>
	<?php } ?>
}
<?php } ?>

<?php
// Header Icon.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'header_icon_size',
	'selector'     => ".fl-node-$id .pp-table-content .pp-table-header-icon",
	'prop'         => 'font-size',
	'unit'         => 'px',
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'header_icon_spacing',
	'selector'     => ".fl-node-$id .pp-table-content .pp-table-header-icon",
	'prop'         => 'margin-right',
	'unit'         => 'px',
	'enabled'      => 'left' === $settings->header_icon_pos
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'header_icon_spacing',
	'selector'     => ".fl-node-$id .pp-table-content .pp-table-header-icon",
	'prop'         => 'margin-bottom',
	'unit'         => 'px',
	'enabled'      => 'top' === $settings->header_icon_pos
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'header_icon_spacing',
	'selector'     => ".fl-node-$id .pp-table-content .pp-table-header-icon",
	'prop'         => 'margin-left',
	'unit'         => 'px',
	'enabled'      => 'right' === $settings->header_icon_pos
) );
?>

<?php // Header Border - Settings
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'header_border_group',
		'selector' 		=> ".fl-node-$id .pp-table-content thead tr th, .fl-node-$id .pp-table-content.tablesaw thead th",
	) );
?>

<?php if( $settings->is_sortable == 'yes' ) { ?>
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw-sortable th.tablesaw-sortable-head button {
	<?php if( $settings->header_padding_right >= 0 ) { ?>
		padding-right: <?php echo $settings->header_padding_right; ?>px;
	<?php } ?>
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr {
	background: <?php echo pp_get_color_value($settings->rows_background); ?>;
	border-bottom: 0;
}

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr th,
.fl-node-<?php echo $id; ?> .pp-table-content tbody tr td {
	vertical-align: <?php echo $settings->rows_vertical_alignment; ?>;
	color: <?php echo pp_get_color_value($settings->rows_font_color); ?>;
}

<?php if ( isset( $settings->cell_icon_pos ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-table-content .pp-table-cell-inner {
	display: flex;
	align-items: center;
	<?php if ( 'top' === $settings->cell_icon_pos ) { ?>
	flex-direction: column;
	<?php } ?>
	<?php if ( 'right' === $settings->cell_icon_pos ) { ?>
	flex-direction: row-reverse;
	<?php } ?>
	<?php if ( is_array( $settings->row_typography ) && isset( $settings->row_typography['text_align'] ) ) { ?>
		<?php if ( 'center' === $settings->row_typography['text_align'] ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'left' === $settings->row_typography['text_align'] ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'right' === $settings->row_typography['text_align'] ) { ?>
			justify-content: flex-end;
		<?php } ?>
	<?php } ?>
}
<?php } ?>

<?php
// Cell Icon.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'cell_icon_size',
	'selector'     => ".fl-node-$id .pp-table-content .pp-table-cell-icon",
	'prop'         => 'font-size',
	'unit'         => 'px',
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'cell_icon_spacing',
	'selector'     => ".fl-node-$id .pp-table-content .pp-table-cell-icon",
	'prop'         => 'margin-right',
	'unit'         => 'px',
	'enabled'      => 'left' === $settings->cell_icon_pos
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'cell_icon_spacing',
	'selector'     => ".fl-node-$id .pp-table-content .pp-table-cell-icon",
	'prop'         => 'margin-bottom',
	'unit'         => 'px',
	'enabled'      => 'top' === $settings->cell_icon_pos
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'cell_icon_spacing',
	'selector'     => ".fl-node-$id .pp-table-content .pp-table-cell-icon",
	'prop'         => 'margin-left',
	'unit'         => 'px',
	'enabled'      => 'right' === $settings->cell_icon_pos
) );
?>

<?php // Cell Border - Settings
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'cell_border_group',
		'selector' 		=> ".fl-node-$id .pp-table-content tbody tr th, .fl-node-$id .pp-table-content tbody tr td",
	) );
?>

.fl-node-<?php echo $id; ?> .tablesaw-sortable .tablesaw-sortable-head button {
	<?php if ( isset( $settings->header_typography ) && is_array( $settings->header_typography ) ) { ?>
	text-align: <?php echo $settings->header_typography['text_align']; ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr:nth-child(odd) {
    <?php if( $settings->rows_odd_background ) { ?>background: <?php echo pp_get_color_value($settings->rows_odd_background); ?>;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr:nth-child(odd) th,
.fl-node-<?php echo $id; ?> .pp-table-content tbody tr:nth-child(odd) td {
    <?php if( $settings->rows_font_odd ) { ?>color: <?php echo pp_get_color_value($settings->rows_font_odd); ?>;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr:nth-child(even) {
    <?php if( $settings->rows_even_background ) { ?>background: <?php echo pp_get_color_value($settings->rows_even_background); ?>;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr:nth-child(even) th,
.fl-node-<?php echo $id; ?> .pp-table-content tbody tr:nth-child(even) td {
    <?php if( $settings->rows_font_even ) { ?>color: <?php echo pp_get_color_value($settings->rows_font_even); ?>;<?php } ?>
}

@media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-table-content .pp-table-header-inner {
	<?php if ( is_array( $settings->header_typography_medium ) && isset( $settings->header_typography_medium['text_align'] ) ) { ?>
		<?php if ( 'center' === $settings->header_typography_medium['text_align'] ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'left' === $settings->header_typography_medium['text_align'] ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'right' === $settings->header_typography_medium['text_align'] ) { ?>
			justify-content: flex-end;
		<?php } ?>
	<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-table-content .pp-table-cell-inner {
	<?php if ( is_array( $settings->row_typography_medium ) && isset( $settings->row_typography_medium['text_align'] ) ) { ?>
		<?php if ( 'center' === $settings->row_typography_medium['text_align'] ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'left' === $settings->row_typography_medium['text_align'] ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'right' === $settings->row_typography_medium['text_align'] ) { ?>
			justify-content: flex-end;
		<?php } ?>
	<?php } ?>
	}
}

@media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-table-content .pp-table-header-inner {
	<?php if ( is_array( $settings->header_typography_responsive ) && isset( $settings->header_typography_responsive['text_align'] ) ) { ?>
		<?php if ( 'center' === $settings->header_typography_responsive['text_align'] ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'left' === $settings->header_typography_responsive['text_align'] ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'right' === $settings->header_typography_responsive['text_align'] ) { ?>
			justify-content: flex-end;
		<?php } ?>
	<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-table-content .pp-table-cell-inner {
	<?php if ( is_array( $settings->row_typography_responsive ) && isset( $settings->row_typography_responsive['text_align'] ) ) { ?>
		<?php if ( 'center' === $settings->row_typography_responsive['text_align'] ) { ?>
			justify-content: center;
		<?php } ?>
		<?php if ( 'left' === $settings->row_typography_responsive['text_align'] ) { ?>
			justify-content: flex-start;
		<?php } ?>
		<?php if ( 'right' === $settings->row_typography_responsive['text_align'] ) { ?>
			justify-content: flex-end;
		<?php } ?>
	<?php } ?>
	}
}

@media only screen and (max-width: 639px) {
	.fl-node-<?php echo $id; ?> .pp-table-content-cell-label {
		<?php if ( isset( $settings->header_typography_responsive ) ) { ?>
			<?php if ( isset( $settings->header_typography_responsive['font_size'] ) && '' != $settings->header_typography_responsive['font_size'] ) { ?>
				font-size: <?php echo $settings->header_typography_responsive['font_size']['length']; ?><?php echo $settings->header_typography_responsive['font_size']['unit']; ?>;
			<?php } ?>
			<?php if ( isset( $settings->header_typography_responsive['text_transform'] ) ) { ?>
			text-transform: <?php echo $settings->header_typography_responsive['text_transform']; ?>;
			<?php } ?>
		<?php } ?>
	}
}
