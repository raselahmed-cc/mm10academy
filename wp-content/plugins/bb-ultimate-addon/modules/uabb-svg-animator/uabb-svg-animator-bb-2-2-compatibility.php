<?php
/**
 * UABB SVG Animator – Form settings for Beaver Builder 2.2+
 *
 * @package UABB SVG Animator Module
 */

defined( 'ABSPATH' ) || exit;

FLBuilder::register_module(
	'UABBSvgAnimatorModule',
	array(

		// =========================================================
		// TAB: Content
		// =========================================================
		'general'       => array(
			'title'    => __( 'Content', 'uabb' ),
			'sections' => array(

				// ----- Source Type -----
				'source_section' => array(
					'title'  => __( 'Choose Icon / Image', 'uabb' ),
					'fields' => array(

						'image_type' => array(
							'type'    => 'select',
							'label'   => __( 'Type', 'uabb' ),
							'default' => 'icon',
							'options' => array(
								'icon'  => __( 'Icon', 'uabb' ),
								'photo' => __( 'SVG', 'uabb' ),
							),
							'toggle'  => array(
								'icon'  => array(
									'sections' => array( 'icon_section', 'fill_section' ),
								),
								'photo' => array(
									'sections' => array( 'img_section', 'fill_section' ),
								),
							),
						),

					),
				),

				// ----- Icon -----
				'icon_section'   => array(
					'title'  => __( 'Icon', 'uabb' ),
					'fields' => array(

						'svg_icon' => array(
							'type'    => 'select',
							'label'   => __( 'Choose Icon', 'uabb' ),
							'default' => 'fas fa-rocket',
							'class'   => 'uabb-svg-icon-select',
							'options' => UABBSvgAnimatorModule::get_available_icons(),
							'help'    => __( 'Only icons with SVG path data are listed — every icon shown can be stroke-animated.', 'uabb' ),
						),

					),
				),

				// ----- SVG -----
				'img_section'    => array(
					'title'  => __( 'SVG Image', 'uabb' ),
					'fields' => array(

						'photo' => array(
							'type'        => 'photo',
							'label'       => __( 'SVG Image', 'uabb' ),
							'show_remove' => true,
							'connections' => array( 'photo' ),
							'help'        => __( 'Upload an SVG file for stroke animation. Only SVG files are accepted. Files are automatically sanitized for security.', 'uabb' ),
						),

					),
				),

				// ----- Fill Settings (icon/SVG only) -----
				'fill_section'   => array(
					'title'  => __( 'Fill Settings', 'uabb' ),
					'fields' => array(

						'fill_mode'     => array(
							'type'    => 'select',
							'label'   => __( 'Fill Mode', 'uabb' ),
							'default' => 'none',
							'options' => array(
								'none'   => __( 'No Fill', 'uabb' ),
								'before' => __( 'Before Stroke', 'uabb' ),
								'after'  => __( 'After Stroke', 'uabb' ),
								'always' => __( 'Always Visible', 'uabb' ),
							),
							'help'    => __( 'Control when the fill color appears relative to the stroke animation.', 'uabb' ),
							'toggle'  => array(
								'before' => array( 'fields' => array( 'fill_color', 'fill_duration' ) ),
								'after'  => array( 'fields' => array( 'fill_color', 'fill_duration' ) ),
								'always' => array( 'fields' => array( 'fill_color', 'fill_duration' ) ),
							),
						),

						'fill_color'    => array(
							'type'       => 'color',
							'label'      => __( 'Fill Color', 'uabb' ),
							'default'    => '',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.uabb-svg-icon path, .uabb-svg-icon circle, .uabb-svg-icon rect, .uabb-svg-icon line, .uabb-svg-icon polyline, .uabb-svg-icon polygon, .uabb-svg-icon ellipse',
								'property' => 'fill',
							),
						),

						'fill_duration' => array(
							'type'    => 'unit',
							'label'   => __( 'Fill Duration (s)', 'uabb' ),
							'default' => '1',
							'help'    => __( 'Duration of the fill animation in seconds.', 'uabb' ),
							'slider'  => array(
								'min'  => 0.1,
								'max'  => 10,
								'step' => 0.1,
							),
						),

					),
				),

			),
		),

		// =========================================================
		// TAB: Animation
		// =========================================================
		'animation_tab' => array(
			'title'    => __( 'Animation', 'uabb' ),
			'sections' => array(

				'anim_general'  => array(
					'title'  => '',
					'fields' => array(

						'lazy_load'          => array(
							'type'    => 'select',
							'label'   => __( 'Lazy Load', 'uabb' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'uabb' ),
								'no'  => __( 'No', 'uabb' ),
							),
							'help'    => __( 'Start animation only when the element enters the viewport.', 'uabb' ),
						),

						'advanced_animation' => array(
							'type'    => 'select',
							'label'   => __( 'Advanced Animation', 'uabb' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Enable', 'uabb' ),
								'no'  => __( 'Disable', 'uabb' ),
							),
							'toggle'  => array(
								'yes' => array( 'sections' => array( 'anim_advanced' ) ),
							),
						),

					),
				),

				'anim_advanced' => array(
					'title'  => __( 'Advanced Settings', 'uabb' ),
					'fields' => array(

						'animation_type'     => array(
							'type'    => 'select',
							'label'   => __( 'Animation Style', 'uabb' ),
							'default' => 'sync',
							'options' => array(
								'sync'       => __( 'All Together', 'uabb' ),
								'delayed'    => __( 'Slowed (Staggered)', 'uabb' ),
								'one-by-one' => __( 'One by One', 'uabb' ),
							),
							'toggle'  => array(
								'delayed'    => array( 'fields' => array( 'stagger_delay' ) ),
								'one-by-one' => array( 'fields' => array( 'stagger_delay' ) ),
							),
						),

						'animation_duration' => array(
							'type'    => 'unit',
							'label'   => __( 'Duration (s)', 'uabb' ),
							'default' => '3',
							'slider'  => array(
								'min'  => 0.1,
								'max'  => 20,
								'step' => 0.1,
							),
						),

						'animation_trigger'  => array(
							'type'    => 'select',
							'label'   => __( 'Start Trigger', 'uabb' ),
							'default' => 'viewport',
							'options' => array(
								'auto'     => __( 'On Page Load', 'uabb' ),
								'viewport' => __( 'On Scroll Into View', 'uabb' ),
								'hover'    => __( 'On Hover', 'uabb' ),
								'click'    => __( 'On Click', 'uabb' ),
								'delay'    => __( 'After Delay', 'uabb' ),
							),
							'toggle'  => array(
								'delay' => array( 'fields' => array( 'animation_delay' ) ),
							),
						),

						'animation_delay'    => array(
							'type'    => 'unit',
							'label'   => __( 'Start Delay (s)', 'uabb' ),
							'default' => '2',
							'help'    => __( 'Only applies to the "After Delay" trigger.', 'uabb' ),
							'slider'  => array(
								'min'  => 0,
								'max'  => 30,
								'step' => 0.1,
							),
						),

						'auto_start'         => array(
							'type'    => 'select',
							'label'   => __( 'Auto Start', 'uabb' ),
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'uabb' ),
								'no'  => __( 'No', 'uabb' ),
							),
						),

						'replay_on_click'    => array(
							'type'    => 'select',
							'label'   => __( 'Replay on Click', 'uabb' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'uabb' ),
								'no'  => __( 'No', 'uabb' ),
							),
						),

						'looping'            => array(
							'type'    => 'select',
							'label'   => __( 'Looping', 'uabb' ),
							'default' => 'none',
							'options' => array(
								'none'     => __( 'No Loop', 'uabb' ),
								'infinite' => __( 'Loop Forever', 'uabb' ),
								'count'    => __( 'Loop Count', 'uabb' ),
							),
							'toggle'  => array(
								'count' => array( 'fields' => array( 'loop_count' ) ),
							),
						),

						'loop_count'         => array(
							'type'    => 'unit',
							'label'   => __( 'Loop Count', 'uabb' ),
							'default' => '3',
							'slider'  => array(
								'min'  => 2,
								'max'  => 100,
								'step' => 1,
							),
						),

						'direction'          => array(
							'type'    => 'select',
							'label'   => __( 'Direction', 'uabb' ),
							'default' => 'forward',
							'options' => array(
								'forward'  => __( 'Forward (Start to End)', 'uabb' ),
								'backward' => __( 'Backward (End to Start)', 'uabb' ),
							),
						),

						'path_timing'        => array(
							'type'    => 'select',
							'label'   => __( 'Motion (Easing)', 'uabb' ),
							'default' => 'ease-out',
							'options' => array(
								'linear'   => __( 'Linear', 'uabb' ),
								'ease'     => __( 'Ease', 'uabb' ),
								'ease-in'  => __( 'Ease In', 'uabb' ),
								'ease-out' => __( 'Ease Out', 'uabb' ),
								'bounce'   => __( 'Bounce', 'uabb' ),
							),
						),

						'stagger_delay'      => array(
							'type'    => 'unit',
							'label'   => __( 'Stagger Delay (ms)', 'uabb' ),
							'default' => '100',
							'help'    => __( 'Delay between each path for staggered / one-by-one animation.', 'uabb' ),
							'slider'  => array(
								'min'  => 0,
								'max'  => 2000,
								'step' => 10,
							),
						),

					),
				),

			),
		),

		// =========================================================
		// TAB: Style
		// =========================================================
		'style'         => array(
			'title'    => __( 'Style', 'uabb' ),
			'sections' => array(

				'svg_style' => array(
					'title'  => __( 'SVG / Image Styles', 'uabb' ),
					'fields' => array(

						'svg_size'      => array(
							'type'       => 'unit',
							'label'      => __( 'Size', 'uabb' ),
							'default'    => '300',
							'responsive' => true,
							'units'      => array( 'px', '%', 'em', 'rem', 'vw' ),
							'slider'     => array(
								'px' => array(
									'min'  => 10,
									'max'  => 1000,
									'step' => 1,
								),
								'%'  => array(
									'min'  => 1,
									'max'  => 100,
									'step' => 1,
								),
							),
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.uabb-svg-icon, .uabb-svg-photo',
								'property' => 'width',
							),
						),

						'svg_alignment' => array(
							'type'       => 'align',
							'label'      => __( 'Alignment', 'uabb' ),
							'default'    => 'center',
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.uabb-svg-wrapper',
								'property' => 'text-align',
							),
						),

						'stroke_width'  => array(
							'type'    => 'unit',
							'label'   => __( 'Stroke Width', 'uabb' ),
							'default' => '1',
							'units'   => array( 'px' ),
							'slider'  => array(
								'px' => array(
									'min'  => 0.1,
									'max'  => 20,
									'step' => 0.1,
								),
							),
							'preview' => array(
								'type'     => 'css',
								'selector' => '.uabb-svg-icon path, .uabb-svg-icon circle, .uabb-svg-icon rect, .uabb-svg-icon line, .uabb-svg-icon polyline, .uabb-svg-icon polygon, .uabb-svg-icon ellipse',
								'property' => 'stroke-width',
							),
						),

						'stroke_color'  => array(
							'type'       => 'color',
							'label'      => __( 'Stroke Color', 'uabb' ),
							'default'    => '333333',
							'show_reset' => true,
							'show_alpha' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.uabb-svg-icon path, .uabb-svg-icon circle, .uabb-svg-icon rect, .uabb-svg-icon line, .uabb-svg-icon polyline, .uabb-svg-icon polygon, .uabb-svg-icon ellipse',
								'property' => 'stroke',
							),
						),

					),
				),

			),
		),

	)
);
