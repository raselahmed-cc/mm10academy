<?php if ( ! empty( $settings->border_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-default .pp-tabs-label.pp-tab-active,
.fl-node-<?php echo $id; ?> .pp-tabs-default .pp-tabs-panels,
.fl-node-<?php echo $id; ?> .pp-tabs-default .pp-tabs-panel {
	border-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
}
<?php } ?>

<?php
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'tab_icon_size',
	'selector'     => ".fl-node-$id .pp-tabs-label .pp-tab-icon, .fl-node-$id .pp-tabs-label .pp-tab-icon:before",
	'prop'         => 'font-size',
	'unit'         => 'px',
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'tab_icon_size',
	'selector'     => ".fl-node-$id .pp-tabs-label .pp-tab-icon img",
	'prop'         => 'width',
	'unit'         => 'px',
) );
?>
.fl-node-<?php echo $id; ?> .pp-tabs-label .pp-tab-icon {
	<?php if ( $settings->tab_icon_position == 'left' ) { ?>
		margin-right: 15px;
	<?php } ?>
	<?php if ( $settings->tab_icon_position == 'right' ) { ?>
		margin-left: 15px;
	<?php } ?>
	<?php if ( $settings->tab_icon_position == 'top' ) { ?>
		margin-bottom: 10px;
	<?php } ?>
	<?php if ( $settings->tab_icon_position == 'bottom' ) { ?>
		margin-top: 10px;
	<?php } ?>
	<?php if ( isset( $settings->tab_icon_color ) && ! empty( $settings->tab_icon_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->tab_icon_color ); ?>;
	<?php } ?>
}

<?php
if ( isset( $settings->label_typography ) ) {
	?>
	.fl-node-<?php echo $id; ?> .pp-tabs-vertical .pp-tabs-label {
		<?php if ( isset( $settings->label_typography ) && is_array( $settings->label_typography ) && isset( $settings->label_typography['text_align'] ) ) { ?>
		text-align: <?php echo $settings->label_typography['text_align']; ?>;
		<?php } ?>
	}
	<?php
}
?>

<?php
// Label typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'label_typography',
	'selector'		=> ".fl-node-$id .pp-tabs-labels .pp-tabs-label .pp-tab-title, .fl-node-$id .pp-tabs-panels .pp-tabs-label .pp-tab-title"
) );

// Description typography.
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'description_typography',
	'selector'		=> ".fl-node-$id .pp-tabs-labels .pp-tabs-label .pp-tab-description, .fl-node-$id .pp-tabs-panels .pp-tabs-label .pp-tab-description"
) );
?>

<?php
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'content_typography',
	'selector'		=> ".fl-node-$id .pp-tabs-panels .pp-tabs-panel-content"
) );

// Content Padding.
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'content_padding',
	'selector'		=> ".fl-node-$id .pp-tabs-panels .pp-tabs-panel-content",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top'		=> 'content_padding_top',
		'padding-right'		=> 'content_padding_right',
		'padding-bottom'	=> 'content_padding_bottom',
		'padding-left'		=> 'content_padding_left',
	)
) );
?>
.fl-node-<?php echo $id; ?> .pp-tabs-labels .pp-tabs-label .pp-tab-label-flex {
	<?php if ( is_array( $settings->label_typography ) && isset( $settings->label_typography['text_align'] ) ) {
		$prop = 'top' === $settings->tab_icon_position || 'bottom' === $settings->tab_icon_position ? 'align-items' : 'justify-content';
		?>
		<?php if ( 'left' === $settings->label_typography['text_align'] ) { ?>
			<?php echo $prop; ?>: flex-start;
		<?php } ?>
		<?php if ( 'center' === $settings->label_typography['text_align'] ) { ?>
			<?php echo $prop; ?>: center;
		<?php } ?>
		<?php if ( 'right' === $settings->label_typography['text_align'] ) { ?>
			<?php echo $prop; ?>: flex-end;
		<?php } ?>
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-tabs-panels .pp-tabs-panel-content {
	<?php if ( ! empty( $settings->content_bg_color ) ) { ?>
		background-color: <?php echo pp_get_color_value( $settings->content_bg_color ); ?>;
	<?php } ?>
	<?php if ( $settings->content_bg_type == 'image' && $settings->content_bg_image ) { ?>
		background-image: url( <?php echo $settings->content_bg_image_src; ?> );
		background-size: <?php echo $settings->content_bg_size; ?>;
		background-repeat: <?php echo $settings->content_bg_repeat; ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->content_text_color ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->content_text_color ); ?>;
	<?php } ?>
	<?php if ( $settings->tab_style != 'default' ) { ?>
		border-style: solid;
		<?php if ( ! empty( $settings->content_border_color ) ) { ?>
		border-color: <?php echo pp_get_color_value( $settings->content_border_color ); ?>;
		<?php } ?>
		<?php if ( '' !== $settings->content_border_width['top'] && $settings->content_border_width['top'] >= 0 ) { ?>
		border-top-width: <?php echo $settings->content_border_width['top']; ?>px;
		<?php } ?>
		<?php if ( '' !== $settings->content_border_width['right'] && $settings->content_border_width['right'] >= 0 ) { ?>
		border-right-width: <?php echo $settings->content_border_width['right']; ?>px;
		<?php } ?>
		<?php if ( '' !== $settings->content_border_width['bottom'] && $settings->content_border_width['bottom'] >= 0 ) { ?>
		border-bottom-width: <?php echo $settings->content_border_width['bottom']; ?>px;
		<?php } ?>
		<?php if ( '' !== $settings->content_border_width['left'] && $settings->content_border_width['left'] >= 0 ) { ?>
		border-left-width: <?php echo $settings->content_border_width['left']; ?>px;
		<?php } ?>
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label {
	<?php if ( ! empty( $settings->label_background_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->label_background_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->label_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_text_color ); ?>;
	<?php } ?>
}
<?php
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'label_padding',
	'selector'		=> ".fl-node-$id .pp-tabs-labels .pp-tabs-label, .fl-node-$id .pp-tabs-panel .pp-tabs-label, .fl-node-$id .pp-tabs-style-5 .pp-tabs-label .pp-tab-label-inner, .fl-node-$id .pp-tabs-style-7 .pp-tabs-label .pp-tab-label-inner, .fl-node-$id .pp-tabs-style-8 .pp-tabs-label .pp-tab-label-inner",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top'		=> 'label_padding_top',
		'padding-right'		=> 'label_padding_right',
		'padding-bottom'	=> 'label_padding_bottom',
		'padding-left'		=> 'label_padding_left',
	)
) );
?>

.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label.pp-tab-active,
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label.pp-tab-active:hover,
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label:hover,
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label:focus {
	<?php if ( ! empty( $settings->label_background_active_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->label_background_active_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->label_active_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_active_text_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label .pp-tab-description {
	<?php if ( isset( $settings->description_color ) && ! empty( $settings->description_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->description_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label.pp-tab-active .pp-tab-description,
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label.pp-tab-active:hover .pp-tab-description,
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label:hover .pp-tab-description {
	<?php if ( isset( $settings->description_active_color ) && ! empty( $settings->description_active_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->description_active_color ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label.pp-tab-active .pp-tab-icon,
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label.pp-tab-active:hover .pp-tab-icon,
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label:hover .pp-tab-icon {
	<?php if ( isset( $settings->tab_icon_color_hover ) && !empty( $settings->tab_icon_color_hover ) ) { ?>
		color: <?php echo pp_get_color_value( $settings->tab_icon_color_hover ); ?>;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-tabs-panel-label .pp-toggle-icon {
	<?php if ( '' !== $settings->tab_toggle_icon_size ) { ?>
	font-size: <?php echo $settings->tab_toggle_icon_size; ?>px;
	<?php } ?>
	<?php if ( ! empty( $settings->tab_toggle_icon_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->tab_toggle_icon_color ); ?>;
	<?php } ?>
}

<?php if ( ! empty( $settings->label_active_text_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-label.pp-tab-active .pp-toggle-icon {
	color: <?php echo pp_get_color_value( $settings->label_active_text_color ); ?>;
}
<?php } ?>

<?php if ( ! empty( $settings->label_background_active_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-horizontal.pp-tabs-default .pp-tabs-label.pp-tab-active {
	top: 0;
}
<?php } ?>

/*  Style 1
------------------------------------ */

<?php if ( ! empty( $settings->border_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-1 .pp-tabs-labels {
	background-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
	border-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
}
<?php } ?>
<?php if ( ! empty( $settings->label_active_text_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-1 .pp-tabs-label:hover {
	color: <?php echo pp_get_color_value( $settings->label_active_text_color ); ?>;
}
<?php } ?>

/*  Style 2
------------------------------------ */

<?php if ( ! empty( $settings->label_background_active_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-2 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:after {
	border-top-color: <?php echo pp_get_color_value( $settings->label_background_active_color ); ?>;
}
<?php } ?>
<?php if ( ! empty( $settings->border_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-2 .pp-tabs-label:first-child:before,
.fl-node-<?php echo $id; ?> .pp-tabs-style-2 .pp-tabs-label::after {
	background: <?php echo pp_hex2rgba( pp_get_color_value( $settings->border_color ), '0.7' ); ?>;
}
<?php } ?>

/*  Style 3
------------------------------------ */
<?php if ( ! empty( $settings->label_active_text_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-3 .pp-tabs-label:after {
	background-color: <?php echo pp_get_color_value( $settings->label_active_text_color ); ?>;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-tabs-style-3 .pp-tabs-label:hover {
	<?php if ( ! empty( $settings->label_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_text_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->label_background_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->label_background_color ); ?>;
	<?php } ?>
}

/*  Style 4
------------------------------------ */
<?php if ( ! empty( $settings->label_active_text_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-4 .pp-tabs-label:before {
	background-color: <?php echo pp_get_color_value( $settings->label_active_text_color ); ?>;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-tabs-style-4 .pp-tabs-label:hover {
	<?php if ( ! empty( $settings->label_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_text_color ); ?>;
	<?php } ?>
	<?php if ( ! empty( $settings->label_background_color ) ) { ?>
	background-color: <?php echo pp_get_color_value( $settings->label_background_color ); ?>;
	<?php } ?>
}

/*  Style 5
------------------------------------ */
<?php if ( ! empty( $settings->label_background_active_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-5 .pp-tabs-label .pp-tab-label-inner:after,
.fl-node-<?php echo $id; ?> .pp-tabs-style-5 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:after {
	background-color: <?php echo pp_get_color_value( $settings->label_background_active_color ); ?>;
}
<?php } ?>
<?php if ( ! empty( $settings->label_text_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-5 .pp-tabs-label:hover {
	color: <?php echo pp_get_color_value( $settings->label_text_color ); ?>;
}
<?php } ?>

/*  Style 6
------------------------------------ */

<?php $percent = ( count( $settings->items ) - 1 ) * 100; ?>
<?php for ( $i = 1; $i <= count( $settings->items ); $i++ ) { ?>
	<?php if ( $i == count( $settings->items ) ) { break; } ?>
	<?php if ( $i == 1) { ?>
		.fl-node-<?php echo $id; ?> .pp-tabs-style-6 .pp-tabs-label:first-child.pp-tab-active ~ .pp-tabs-label:last-child::before {
			-webkit-transform: translate3d(-<?php echo $percent; ?>%,0,0);
			transform: translate3d(-<?php echo $percent; ?>%,0,0);
		}
	<?php } ?>
	<?php if( $i > 1) {
		$percent = $percent - 100; ?>
		.fl-node-<?php echo $id; ?> .pp-tabs-style-6 .pp-tabs-label:nth-child(<?php echo $i; ?>).pp-tab-active ~ .pp-tabs-label:last-child::before {
			-webkit-transform: translate3d(-<?php echo $percent; ?>%,0,0);
			transform: translate3d(-<?php echo $percent; ?>%,0,0);
		}
	<?php } ?>
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-tabs-style-6 .pp-tabs-label,
.fl-node-<?php echo $id; ?> .pp-tabs-style-6 .pp-tabs-label.pp-tab-active,
.fl-node-<?php echo $id; ?> .pp-tabs-style-6 .pp-tabs-label.pp-tab-active:hover,
.fl-node-<?php echo $id; ?> .pp-tabs .pp-tabs-style-6 .pp-tabs-label:hover {
	background-color: transparent !important;
	<?php if ( ! empty( $settings->label_text_color ) ) { ?>
	color: <?php echo pp_get_color_value( $settings->label_text_color ); ?> !important;
	<?php } ?>
}
<?php if ( ! empty( $settings->label_active_text_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-6 .pp-tabs-label:last-child:before {
	background-color: <?php echo pp_get_color_value( $settings->label_active_text_color ); ?>;
}
<?php } ?>

/*  Style 7
------------------------------------ */
<?php if ( ! empty( $settings->border_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-7 .pp-tabs-label .pp-tab-label-inner {
	border-bottom-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
}
.fl-node-<?php echo $id; ?> .pp-tabs-style-7 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:after,
.fl-node-<?php echo $id; ?> .pp-tabs-style-7 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:before {
	border-top-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
}
<?php } ?>

/*  Style 8
------------------------------------ */
<?php if ( ! empty( $settings->border_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-8 .pp-tabs-label .pp-tab-label-inner:after {
	background-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
}
<?php } ?>
<?php if ( ! empty( $settings->label_background_active_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-8 .pp-tabs-label:hover .pp-tab-label-inner:after,
.fl-node-<?php echo $id; ?> .pp-tabs-style-8 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:after {
	background-color: <?php echo pp_get_color_value( $settings->label_background_active_color ); ?>;
}
<?php } ?>
<?php if ( ! empty( $settings->label_text_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-style-8 .pp-tabs-label:hover {
	color: <?php echo pp_get_color_value( $settings->label_text_color ); ?>;
}
<?php } ?>
<?php if ( '' !== $settings->label_margin ) { ?>
.fl-node-<?php echo $id; ?> .pp-tabs-horizontal.pp-tabs-style-8 .pp-tabs-label {
	margin-left: <?php echo $settings->label_margin; ?>px;
	margin-right: <?php echo $settings->label_margin; ?>px;
}
<?php } ?>

@media only screen and (min-width: 769px) {
	<?php if ( ! empty( $settings->label_background_active_color ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-tabs-vertical.pp-tabs-style-2 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:after {
		border-left-color: <?php echo pp_get_color_value( $settings->label_background_active_color ); ?>;
	}
	<?php } ?>
	.fl-node-<?php echo $id; ?> .pp-tabs-vertical.pp-tabs-style-6 .pp-tabs-label {
		border-bottom: 4px solid transparent;
	}
	<?php if ( ! empty( $settings->label_active_text_color ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-tabs-vertical.pp-tabs-style-6 .pp-tabs-label.pp-tab-active {
		border-bottom: 4px solid <?php echo pp_get_color_value( $settings->label_active_text_color ); ?>;
	}
	<?php } ?>
	<?php if ( ! empty( $settings->border_color ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-tabs-vertical.pp-tabs-style-7 .pp-tabs-label .pp-tab-label-inner {
		border-right-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
	}
	.fl-node-<?php echo $id; ?> .pp-tabs-vertical.pp-tabs-vertical-right.pp-tabs-style-7 .pp-tabs-label .pp-tab-label-inner {
		border-left-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
	}
	.fl-node-<?php echo $id; ?> .pp-tabs-vertical.pp-tabs-style-7 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:before,
	.fl-node-<?php echo $id; ?> .pp-tabs-vertical.pp-tabs-style-7 .pp-tabs-label.pp-tab-active .pp-tab-label-inner:after {
		border-left-color: <?php echo pp_get_color_value( $settings->border_color ); ?>;
	}
	<?php } ?>
}

@media only screen and (max-width: 768px) {
	.fl-node-<?php echo $id; ?> .pp-tabs-style-1 .pp-tabs-label {
		<?php if ( ! empty( $settings->border_color ) ) { ?>
		border: 4px solid <?php echo pp_get_color_value( $settings->border_color ); ?>;
		<?php } ?>
		margin: 2px 0;
	}
	<?php if ( ! empty( $settings->label_background_active_color ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-tabs-style-5 .pp-tabs-label.pp-tab-active {
		background-color: <?php echo pp_get_color_value( $settings->label_background_active_color ); ?> !important;
	}
	.fl-node-<?php echo $id; ?> .pp-tabs-style-8 .pp-tabs-label.pp-tab-active {
		background-color: <?php echo pp_get_color_value( $settings->label_background_active_color ); ?> !important;
	}
	<?php } ?>
}