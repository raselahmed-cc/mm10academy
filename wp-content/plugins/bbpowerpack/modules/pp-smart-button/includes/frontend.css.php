<?php
// Old Background Gradient Setting
if ( isset( $settings->three_d ) && $settings->three_d ) {
	$settings->style = 'gradient';
}

$has_sub_text = isset( $settings->sub_text ) && ! empty( $settings->sub_text );
?>

<?php
// Alignment
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'align',
	'selector'		=> ".fl-node-$id .pp-button-wrap",
	'prop'			=> 'text-align',
) );

// Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'padding',
	'selector' 		=> ".fl-node-$id a.pp-button",
	'unit'			=> 'px',
	'props'			=> array(
		'padding-top' 		=> 'padding_top',
		'padding-right' 	=> 'padding_right',
		'padding-bottom' 	=> 'padding_bottom',
		'padding-left' 		=> 'padding_left',
	),
) );

// Border - Settings
FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'border',
	'selector' 		=> ".fl-node-$id .pp-button-wrap a.pp-button, .fl-node-$id .pp-button-wrap a.pp-button:visited",
) );

// Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'		=> $settings,
	'setting_name' 	=> 'typography',
	'selector' 		=> ".fl-node-$id .pp-button-wrap a.pp-button, .fl-node-$id .pp-button-wrap a.pp-button:visited",
) );

// Default background color for gradient styles.
if ( empty( $settings->bg_color_primary ) && 'gradient' === $settings->style ) {
	$settings->bg_color_primary = 'a3a3a3';
}
?>

<?php if ( $has_sub_text ) { ?>
	.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button {
		display: inline-flex;
		align-items: center;
	}

	<?php if ( ! empty( $settings->subtext_color ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button .pp-button-subtext {
		color: <?php echo pp_get_color_value( $settings->subtext_color ); ?>;
	}
	<?php } ?>

	<?php if ( ! empty( $settings->subtext_hover_color ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover .pp-button-subtext {
		color: <?php echo pp_get_color_value( $settings->subtext_hover_color ); ?>;
	}
	<?php } ?>

	<?php
	// Sub Text Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'subtext_typography',
		'selector' 		=> ".fl-node-$id .pp-button-wrap a.pp-button .pp-button-subtext",
	) );
	?>
<?php } ?>

<?php

if ( 'custom' === $settings->width ) {
	foreach ( array( '', 'large', 'medium', 'responsive' ) as $device ) {

		$key      = empty( $device ) ? 'custom_width' : "custom_width_{$device}";
		$unit_key = "{$key}_unit";

		$size_unit = $settings->{ $unit_key };

		// Button Custom Width.
		FLBuilderCSS::rule( array(
			'media'    => $device,
			'enabled'  => empty( $device ) ? true : ! empty( $settings->{ $key } ),
			'selector' => ".fl-node-$id .pp-button-wrap a.pp-button, .fl-node-$id .pp-button-wrap a.pp-button:visited",
			'props'    => array(
				'width' => $settings->{ $key } . $size_unit,
			),
		) );
	}
}
?>

.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	text-decoration: none;

	<?php if ( 'full' == $settings->width ) { ?>
		width: 100%;
		<?php if ( $has_sub_text ) { ?>
			justify-content: center;
		<?php } ?>
	<?php } ?>

	<?php if ( isset( $settings->bg_color ) && ! empty( $settings->bg_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->bg_color ); ?>;
	<?php } ?>

	<?php if ( 'gradient' == $settings->style ) { // Gradient ?>
		background: -moz-linear-gradient(top,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?>), color-stop(100%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?>)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* IE10+ */
		background: linear-gradient(to bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo pp_get_color_value( $settings->bg_color_primary ); ?>', endColorstr='<?php echo pp_get_color_value( $settings->bg_color_secondary ); ?>', GradientType=0 ); /* IE6-9 */
	<?php } ?>
	background-clip: border-box;
}

.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover,
.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:focus {
	text-decoration: none;

	<?php if ( 'gradient' != $settings->style ) { ?>
		<?php if ( ! empty( $settings->bg_hover_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;
		<?php } ?>
	<?php } ?>

	<?php if ( 'gradient' == $settings->style ) { // Gradient ?>
		<?php if( $settings->gradient_hover == 'reverse' ) { ?>
			background: -moz-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left bottom, left bottom, color-stop(0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?>), color-stop(100%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?>)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* IE10+ */
			background: linear-gradient(to top,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo pp_get_color_value( $settings->bg_color_primary ); ?>', endColorstr='<?php echo pp_get_color_value( $settings->bg_color_secondary ); ?>', GradientType=0 ); /* IE6-9 */
		<?php } else if( $settings->gradient_hover == 'primary' ) { ?>
			background: -moz-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left bottom, left bottom, color-stop(0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?>), color-stop(100%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?>)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 100%); /* IE10+ */
			background: linear-gradient(to top,  <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?> 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo pp_get_color_value( $settings->bg_color_primary ); ?>', endColorstr='<?php echo pp_get_color_value( $settings->bg_color_primary ); ?>', GradientType=0 ); /* IE6-9 */
		<?php } else if( $settings->gradient_hover == 'secondary' ) { ?>
			background: -moz-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left bottom, left bottom, color-stop(0%, <?php echo pp_get_color_value( $settings->bg_color_primary ); ?>), color-stop(100%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?>)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(bottom,  <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* IE10+ */
			background: linear-gradient(to top,  <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 0%, <?php echo pp_get_color_value( $settings->bg_color_secondary ); ?> 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo pp_get_color_value( $settings->bg_color_secondary ); ?>', endColorstr='<?php echo pp_get_color_value( $settings->bg_color_secondary ); ?>', GradientType=0 ); /* IE6-9 */
		<?php } ?>
	<?php } ?>
}

<?php
// Border - Hover Settings
if ( ! empty( $settings->border_hover_color ) && is_array( $settings->border ) ) {
	$settings->border['color'] = $settings->border_hover_color;
}

FLBuilderCSS::border_field_rule( array(
	'settings' 		=> $settings,
	'setting_name' 	=> 'border',
	'selector' 		=> ".fl-node-$id .pp-button-wrap a.pp-button:hover, .fl-node-$id .pp-button-wrap a.pp-button:focus",
) );

// Icon Spacing - Before
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'icon_spacing',
	'selector'		=> ".fl-node-$id .pp-button .pp-button-icon",
	'prop'			=> 'margin-right',
	'unit'          => 'px',
	'enabled'       => 'before' === $settings->icon_position
) );

// Icon Spacing - After
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'icon_spacing',
	'selector'		=> ".fl-node-$id .pp-button .pp-button-icon",
	'prop'			=> 'margin-left',
	'unit'          => 'px',
	'enabled'       => 'after' === $settings->icon_position
) );

// Icon Size
FLBuilderCSS::responsive_rule( array(
	'settings'		=> $settings,
	'setting_name'	=> 'icon_size',
	'selector'		=> ".fl-node-$id .pp-button .pp-button-icon",
	'prop'			=> 'font-size',
	'unit'          => 'px',
) );
?>

<?php if ( isset( $settings->icon_color ) && ! empty( $settings->icon_color ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button .pp-button-icon {
		color: <?php echo pp_get_color_value( $settings->icon_color ); ?>;
	}
<?php } ?>
<?php if ( isset( $settings->icon_hover_color ) && ! empty( $settings->icon_hover_color ) ) { ?>
.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover .pp-button-icon {
	color: <?php echo pp_get_color_value( $settings->icon_hover_color ); ?>;
}
<?php } ?>

<?php if ( isset( $settings->text_color ) && ! empty( $settings->text_color ) ) : ?>
.fl-node-<?php echo $id; ?> a.pp-button {
	-webkit-transition: all .3s ease 0s;
	-moz-transition: all .3s ease 0s;
	-o-transition: all .3s ease 0s;
	-ms-transition: all .3s ease 0s;
	transition: all .3s ease 0s;
}
.fl-node-<?php echo $id; ?> a.pp-button,
.fl-node-<?php echo $id; ?> a.pp-button * {
	color: <?php echo pp_get_color_value( $settings->text_color ); ?>;
}
<?php endif; ?>

<?php if ( isset( $settings->text_hover_color ) && ! empty( $settings->text_hover_color ) ) : ?>
.fl-node-<?php echo $id; ?> a.pp-button:hover,
.fl-node-<?php echo $id; ?> a.pp-button:focus,
.fl-node-<?php echo $id; ?> a.pp-button:hover *,
.fl-node-<?php echo $id; ?> a.pp-button:focus * {
	color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;
}
<?php endif; ?>

<?php
$btn_effect = $settings->button_effect;
if( $settings->style == 'flat' ) {
	switch( $btn_effect ) {
		case 'none': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
				-webkit-transition: none;
				-moz-transition: none;
				-o-transition: none;
				-ms-transition: none;
				transition: none;
			}
			<?php
			break;

	    case 'fade': ?>
	    .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
		.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
			<?php if($settings->button_effect_duration) { ?>
				transition-duration: <?php echo $settings->button_effect_duration; ?>ms;
			<?php } ?>
	    }
	    <?php
	    break;

	    case 'sweep_right': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleX(0);
	            -moz-transform: scaleX(0);
	            -o-transform: scaleX(0);
	            -ms-transform: scaleX(0);
	            transform: scaleX(0);
	            -webkit-transform-origin: 0 50%;
	            -moz-transform-origin: 0 50%;
	            -o-transform-origin: 0 50%;
	            -ms-transform-origin: 0 50%;
	            transform-origin: 0 50%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleX(1);
	            -moz-transform: scaleX(1);
	            -o-transform: scaleX(1);
	            -ms-transform: scaleX(1);
	            transform: scaleX(1);
	        }
	    <?php
	    break;

	    case 'sweep_left': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleX(0);
	            -moz-transform: scaleX(0);
	            -o-transform: scaleX(0);
	            -ms-transform: scaleX(0);
	            transform: scaleX(0);
	            -webkit-transform-origin: 100% 50%;
	            -moz-transform-origin: 100% 50%;
	            -o-transform-origin: 100% 50%;
	            -ms-transform-origin: 100% 50%;
	            transform-origin: 100% 50%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleX(1);
	            -moz-transform: scaleX(1);
	            -o-transform: scaleX(1);
	            -ms-transform: scaleX(1);
	            transform: scaleX(1);
	        }
	    <?php
	    break;

	    case 'sweep_bottom': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleY(0);
	            -moz-transform: scaleY(0);
	            -o-transform: scaleY(0);
	            -ms-transform: scaleY(0);
	            transform: scaleY(0);
	            -webkit-transform-origin: 50% 0;
	            -moz-transform-origin: 50% 0;
	            -o-transform-origin: 50% 0;
	            -ms-transform-origin: 50% 0;
	            transform-origin: 50% 0;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleY(1);
	            -moz-transform: scaleY(1);
	            -o-transform: scaleY(1);
	            -ms-transform: scaleY(1);
	            transform: scaleY(1);
	        }
	    <?php
	    break;

	    case 'sweep_top': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleY(0);
	            -moz-transform: scaleY(0);
	            -o-transform: scaleY(0);
	            -ms-transform: scaleY(0);
	            transform: scaleY(0);
	            -webkit-transform-origin: 50% 100%;
	            -moz-transform-origin: 50% 100%;
	            -o-transform-origin: 50% 100%;
	            -ms-transform-origin: 50% 100%;
	            transform-origin: 50% 100%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleY(1);
	            -moz-transform: scaleY(1);
	            -o-transform: scaleY(1);
	            -ms-transform: scaleY(1);
	            transform: scaleY(1);
	        }
	    <?php
	    break;

	    case 'bounce_right': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleX(0);
	            -moz-transform: scaleX(0);
	            -o-transform: scaleX(0);
	            -ms-transform: scaleX(0);
	            transform: scaleX(0);
	            -webkit-transform-origin: 0 50%;
	            -moz-transform-origin: 0 50%;
	            -o-transform-origin: 0 50%;
	            -ms-transform-origin: 0 50%;
	            transform-origin: 0 50%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleX(1);
	            -moz-transform: scaleX(1);
	            -o-transform: scaleX(1);
	            -ms-transform: scaleX(1);
	            transform: scaleX(1);
	            transition-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);
	        }
	    <?php
	    break;

	    case 'bounce_left': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleX(0);
	            -moz-transform: scaleX(0);
	            -o-transform: scaleX(0);
	            -ms-transform: scaleX(0);
	            transform: scaleX(0);
	            -webkit-transform-origin: 100% 50%;
	            -moz-transform-origin: 100% 50%;
	            -o-transform-origin: 100% 50%;
	            -ms-transform-origin: 100% 50%;
	            transform-origin: 100% 50%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleX(1);
	            -moz-transform: scaleX(1);
	            -o-transform: scaleX(1);
	            -ms-transform: scaleX(1);
	            transform: scaleX(1);
	            transition-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);
	        }
	    <?php
	    break;

	    case 'bounce_bottom': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleY(0);
	            -moz-transform: scaleY(0);
	            -o-transform: scaleY(0);
	            -ms-transform: scaleY(0);
	            transform: scaleY(0);
	            -webkit-transform-origin: 50% 0;
	            -moz-transform-origin: 50% 0;
	            -o-transform-origin: 50% 0;
	            -ms-transform-origin: 50% 0;
	            transform-origin: 50% 0;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleY(1);
	            -moz-transform: scaleY(1);
	            -o-transform: scaleY(1);
	            -ms-transform: scaleY(1);
	            transform: scaleY(1);
	            transition-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);
	        }
	    <?php
	    break;

	    case 'bounce_top': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleY(0);
	            -moz-transform: scaleY(0);
	            -o-transform: scaleY(0);
	            -ms-transform: scaleY(0);
	            transform: scaleY(0);
	            -webkit-transform-origin: 50% 100%;
	            -moz-transform-origin: 50% 100%;
	            -o-transform-origin: 50% 100%;
	            -ms-transform-origin: 50% 100%;
	            transform-origin: 50% 100%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleY(1);
	            -moz-transform: scaleY(1);
	            -o-transform: scaleY(1);
	            -ms-transform: scaleY(1);
	            transform: scaleY(1);
	            transition-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);
	        }
	    <?php
	    break;

	    case 'radial_out': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            overflow: hidden;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            border-radius: 100%;
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color:<?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color:<?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scale(0);
	            -moz-transform: scale(0);
	            -o-transform: scale(0);
	            -ms-transform: scale(0);
	            transform: scale(0);
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scale(2);
	            -moz-transform: scale(2);
	            -o-transform: scale(2);
	            -ms-transform: scale(2);
	            transform: scale(2);
	        }
	    <?php
	    break;

	    case 'radial_in': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            overflow: hidden;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            border-radius: 100%;
	            content: "";
	            <?php if( $settings->bg_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scale(2);
	            -moz-transform: scale(2);
	            -o-transform: scale(2);
	            -ms-transform: scale(2);
	            transform: scale(2);
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scale(0);
	            -moz-transform: scale(0);
	            -o-transform: scale(0);
	            -ms-transform: scale(0);
	            transform: scale(0);
	        }
	    <?php
	    break;

	    case 'rectangle_out': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            overflow: hidden;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scale(0);
	            -moz-transform: scale(0);
	            -o-transform: scale(0);
	            -ms-transform: scale(0);
	            transform: scale(0);
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scale(1);
	            -moz-transform: scale(1);
	            -o-transform: scale(1);
	            -ms-transform: scale(1);
	            transform: scale(1);
	        }
	    <?php
	    break;

	    case 'rectangle_in': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button {
				<?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
			}
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
	            overflow: hidden;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scale(1);
	            -moz-transform: scale(1);
	            -o-transform: scale(1);
	            -ms-transform: scale(1);
	            transform: scale(1);
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scale(0);
	            -moz-transform: scale(0);
	            -o-transform: scale(0);
	            -ms-transform: scale(0);
	            transform: scale(0);
	        }
	    <?php
	    break;

	    case 'shutter_in_horizontal': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button {
				<?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
			}
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
				overflow: hidden;
				<?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
			}
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleX(1);
	            -moz-transform: scaleX(1);
	            -o-transform: scaleX(1);
	            -ms-transform: scaleX(1);
	            transform: scaleX(1);
	            -webkit-transform-origin: 50%;
	            -moz-transform-origin: 50%;
	            -o-transform-origin: 50%;
	            -ms-transform-origin: 50%;
	            transform-origin: 50%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleX(0);
	            -moz-transform: scaleX(0);
	            -o-transform: scaleX(0);
	            -ms-transform: scaleX(0);
	            transform: scaleX(0);
	        }
	    <?php
	    break;

	    case 'shutter_out_horizontal': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
				overflow: hidden;
				<?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
			}
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleX(0);
	            -moz-transform: scaleX(0);
	            -o-transform: scaleX(0);
	            -ms-transform: scaleX(0);
	            transform: scaleX(0);
	            -webkit-transform-origin: 50%;
	            -moz-transform-origin: 50%;
	            -o-transform-origin: 50%;
	            -ms-transform-origin: 50%;
	            transform-origin: 50%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleX(1);
	            -moz-transform: scaleX(1);
	            -o-transform: scaleX(1);
	            -ms-transform: scaleX(1);
	            transform: scaleX(1);
	        }
	    <?php
	    break;

	    case 'shutter_in_vertical': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button {
				<?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
			}
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
				overflow: hidden;
				<?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
			}
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleY(1);
	            -moz-transform: scaleY(1);
	            -o-transform: scaleY(1);
	            -ms-transform: scaleY(1);
	            transform: scaleY(1);
	            -webkit-transform-origin: 50%;
	            -moz-transform-origin: 50%;
	            -o-transform-origin: 50%;
	            -ms-transform-origin: 50%;
	            transform-origin: 50%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleY(0);
	            -moz-transform: scaleY(0);
	            -o-transform: scaleY(0);
	            -ms-transform: scaleY(0);
	            transform: scaleY(0);
	        }
	    <?php
	    break;

	    case 'shutter_out_vertical': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
				overflow: hidden;
				<?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
			}
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:before {
	            content: "";
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: scaleY(0);
	            -moz-transform: scaleY(0);
	            -o-transform: scaleY(0);
	            -ms-transform: scaleY(0);
	            transform: scaleY(0);
	            -webkit-transform-origin: 50%;
	            -moz-transform-origin: 50%;
	            -o-transform-origin: 50%;
	            -ms-transform-origin: 50%;
	            transform-origin: 50%;
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:before {
	            -webkit-transform: scaleY(1);
	            -moz-transform: scaleY(1);
	            -o-transform: scaleY(1);
	            -ms-transform: scaleY(1);
	            transform: scaleY(1);
	        }
	    <?php
	    break;

	    case 'shutter_out_diagonal': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
				overflow: hidden;
				<?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
			}
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:after {
	            content: "";
	            position: absolute;
	            left: 50%;
	            top: 50%;
	            <?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            -moz-transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            -o-transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            -ms-transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	            height: 0;
	            width: 0;
	            z-index: -1;
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:after {
	            height: 4000%;
	            width: 100%;
	        }
	    <?php
	    break;

	    case 'shutter_in_diagonal': ?>
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button {
				<?php if( $settings->bg_hover_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_hover_color ); ?>;<?php } ?>
			}
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button,
			.fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:visited {
				overflow: hidden;
				<?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
			}
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:after {
	            content: "";
	            position: absolute;
	            left: 50%;
	            top: 50%;
	            <?php if( $settings->bg_color) { ?>background: <?php echo pp_get_color_value( $settings->bg_color ); ?>;<?php } ?>
	            <?php if( $settings->text_hover_color) { ?>color: <?php echo pp_get_color_value( $settings->text_hover_color ); ?>;<?php } ?>
	            <?php if( $settings->border_hover_color) { ?>border-color: <?php echo pp_get_color_value( $settings->border_hover_color ); ?>;<?php } ?>
	            -webkit-transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            -moz-transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            -o-transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            -ms-transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            transform: translateX(-50%) translateY(-50%) rotate(45deg) translateZ(0);
	            <?php if($settings->button_effect_duration >= 0) { ?>transition-duration: <?php echo $settings->button_effect_duration; ?>ms;<?php } ?>
	            height: 4000%;
	            width: 100%;
	            z-index: -1;
	        }
	        .fl-node-<?php echo $id; ?> .pp-button-wrap a.pp-button:hover:after {
	            height: 4000%;
	            width: 0;
	        }
	    <?php
	    break;
	}
}
?>