<?php
// Panel - Height
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'panel_height',
		'selector'     => ".fl-node-$id .pp-image-panels-wrap .pp-panel",
		'prop'         => 'height',
		'unit'         => 'px',
	)
);

// Panel - Spacing
FLBuilderCSS::responsive_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'spacing',
		'selector'     => ".fl-node-$id .pp-image-panels-inner",
		'prop'         => 'gap',
		'unit'         => 'px',
	)
);

// Panel - Border
FLBuilderCSS::border_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'border',
		'selector'     => ".fl-node-$id .pp-image-panels-wrap .pp-panel",
	)
);
?>
.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel .pp-panel-title {
	<?php if ( 'no' === $settings->show_title ) { ?>
	visibility: hidden;
	<?php } ?>
	<?php if ( isset( $settings->title_bg ) && ! empty( $settings->title_bg ) ) { ?>
		<?php if ( isset( $settings->title_bg_as_gradient ) && 'yes' === $settings->title_bg_as_gradient ) { ?>
			background-image: linear-gradient( 0deg, <?php echo pp_get_color_value( $settings->title_bg ); ?>, transparent );
		<?php } else { ?>
			background-color: <?php echo pp_get_color_value( $settings->title_bg ); ?>;
		<?php } ?>
	<?php } ?>
}
<?php if ( isset( $settings->title_color ) && ! empty( $settings->title_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel .pp-panel-title-text {
	color: <?php echo pp_get_color_value( $settings->title_color ); ?>;
}
<?php } ?>
<?php if ( isset( $settings->description_color ) && ! empty( $settings->description_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel .pp-panel-description {
	color: <?php echo pp_get_color_value( $settings->description_color ); ?>;
}
<?php } ?>
<?php
// Title Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'title_typography',
		'selector'     => ".fl-node-$id .pp-image-panels-wrap .pp-panel .pp-panel-title-text",
	)
);

// Description Typography
FLBuilderCSS::typography_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'description_typography',
		'selector'     => ".fl-node-$id .pp-image-panels-wrap .pp-panel .pp-panel-description",
	)
);

// Title - Padding
FLBuilderCSS::dimension_field_rule(
	array(
		'settings'     => $settings,
		'setting_name' => 'title_padding',
		'selector'     => ".fl-node-$id .pp-image-panels-wrap .pp-panel .pp-panel-title",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'title_padding_top',
			'padding-right'  => 'title_padding_right',
			'padding-bottom' => 'title_padding_bottom',
			'padding-left'   => 'title_padding_left',
		),
	)
);
?>

<?php if ( isset( $settings->show_title_on_expand ) && 'yes' === $settings->show_title_on_expand ) { ?>
	.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel .pp-panel-title {
		opacity: 0;
		transform: translateY(20px);
		transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
	}
	.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel .pp-panel-description {
		opacity: 0;
		transform: translateY(20px);
		transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
	}
	.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel.pp-panel-active .pp-panel-title {
		opacity: 1;
		transform: translateY(0);
	}
	.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel.pp-panel-active .pp-panel-description {
		opacity: 1;
		transform: translateY(0);
		transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
	}
<?php } ?>

<?php if ( isset( $settings->speed ) && ! empty( $settings->speed ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel {
		transition-duration: <?php echo $settings->speed; ?>ms;
	}
<?php } ?>

<?php
$number_panels = count( $settings->image_panels );
for ( $i = 0; $i < $number_panels; $i++ ) {
	$panel = $settings->image_panels[ $i ];
	if ( ! is_object( $panel ) ) {
		continue;
	}
	?>
	.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel-<?php echo $i; ?>:after {
		background-image: url(<?php echo $panel->photo_src; ?>);
		<?php if ( 'custom' === $panel->position ) { ?>
		background-position: <?php echo $panel->custom_position; ?>%;
		<?php } ?>
	}

	<?php if ( isset( $panel->title_bg_color ) && ! empty( $panel->title_bg_color ) ) { ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel.pp-panel-<?php echo $i; ?> .pp-panel-title {
		<?php if ( isset( $settings->title_bg_as_gradient ) && 'yes' === $settings->title_bg_as_gradient ) { ?>
		background-image: linear-gradient( 0deg, <?php echo pp_get_color_value( $settings->title_bg_color ); ?>, transparent );
		<?php } else { ?>
		background-color: <?php echo pp_get_color_value( $settings->title_bg_color ); ?>;
		<?php } ?>
	}
	<?php } ?>
	<?php if ( isset( $panel->title_text_color ) && ! empty( $panel->title_text_color ) ) { ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel.pp-panel-<?php echo $i; ?> .pp-panel-title-text {
		color: <?php echo pp_get_color_value( $panel->title_text_color ); ?>;
	}
	<?php } ?>
	<?php if ( isset( $panel->description_text_color ) && ! empty( $panel->description_text_color ) ) { ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel.pp-panel-<?php echo $i; ?> .pp-panel-description {
		color: <?php echo pp_get_color_value( $panel->description_text_color ); ?>;
	}
	<?php } ?>
<?php } ?>
<?php
if ( 'yes' === $settings->show_image_effect ) {
	pp_image_effect_render_style( $settings, ".fl-node-$id .pp-image-panels-wrap .pp-panel.pp-panel-active:after" );
	pp_image_effect_render_style( $settings, ".fl-node-$id .pp-image-panels-wrap .pp-panel.pp-panel-inactive:after", true );
}
?>
@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {
	<?php if ( isset( $settings->responsive_stack ) && 'yes' === $settings->responsive_stack ) { ?>
	.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-image-panels-inner {
		flex-direction: column;
	}
	<?php } ?>
	<?php
	for ( $i = 0; $i < $number_panels; $i++ ) {
		?>
		.fl-node-<?php echo $id; ?> .pp-image-panels-wrap .pp-panel-<?php echo $i; ?> {
			width: 100% !important;
		}
	<?php } ?>
}
