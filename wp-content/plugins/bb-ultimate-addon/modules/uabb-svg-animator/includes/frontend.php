<?php
/**
 * UABB SVG Animator Module – Frontend template
 *
 * Variables available: $module, $id, $settings.
 *
 * @package UABB SVG Animator Module
 */

// Resolve defaults for data attributes.
$animation_type     = ! empty( $settings->animation_type ) ? $settings->animation_type : 'sync';
$animation_trigger  = ! empty( $settings->animation_trigger ) ? $settings->animation_trigger : 'viewport';
$animation_duration = ! empty( $settings->animation_duration ) ? $settings->animation_duration : '3';
$animation_delay    = ! empty( $settings->animation_delay ) ? $settings->animation_delay : '0';
$path_timing        = ! empty( $settings->path_timing ) ? $settings->path_timing : 'ease-out';
$auto_start         = ! empty( $settings->auto_start ) ? $settings->auto_start : 'yes';
$replay_on_click    = ! empty( $settings->replay_on_click ) ? $settings->replay_on_click : 'no';
$looping            = ! empty( $settings->looping ) ? $settings->looping : 'none';
$loop_count         = ! empty( $settings->loop_count ) ? $settings->loop_count : '1';
$direction          = ! empty( $settings->direction ) ? $settings->direction : 'forward';
$fill_mode          = ! empty( $settings->fill_mode ) ? $settings->fill_mode : 'none';
$fill_duration      = ! empty( $settings->fill_duration ) ? $settings->fill_duration : '1';
$fill_color_attr    = ! empty( $settings->fill_color )
	? UABBSvgAnimatorModule::normalize_color( $settings->fill_color )
	: '';

$stroke_color_attr = UABBSvgAnimatorModule::normalize_color(
	! empty( $settings->stroke_color ) ? $settings->stroke_color : '',
	'#333333'
);
$stroke_width_val  = ! empty( $settings->stroke_width ) ? (float) $settings->stroke_width : 1;
$stroke_width_unit = ! empty( $settings->stroke_width_unit ) ? $settings->stroke_width_unit : 'px';
$stroke_width_attr = $stroke_width_val . $stroke_width_unit;
$stagger_delay     = ! empty( $settings->stagger_delay ) ? $settings->stagger_delay : '100';
$lazy_load         = ! empty( $settings->lazy_load ) ? $settings->lazy_load : 'no';
$advanced          = ! empty( $settings->advanced_animation ) ? $settings->advanced_animation : 'no';

// Fall back to simple defaults when advanced animation is disabled.
if ( 'no' === $advanced ) {
	$animation_type     = 'sync';
	$animation_trigger  = 'viewport';
	$animation_duration = '3';
	$animation_delay    = '0';
	$path_timing        = 'ease-out';
	$auto_start         = 'yes';
	$replay_on_click    = 'no';
	$looping            = 'none';
	$loop_count         = '1';
	$direction          = 'forward';
	$stagger_delay      = '100';
}
?>
<div class="uabb-module-content uabb-svg-animator"
	data-animation-type="<?php echo esc_attr( $animation_type ); ?>"
	data-animation-trigger="<?php echo esc_attr( $animation_trigger ); ?>"
	data-animation-duration="<?php echo esc_attr( $animation_duration ); ?>"
	data-animation-delay="<?php echo esc_attr( $animation_delay ); ?>"
	data-path-timing="<?php echo esc_attr( $path_timing ); ?>"
	data-auto-start="<?php echo esc_attr( $auto_start ); ?>"
	data-replay-on-click="<?php echo esc_attr( $replay_on_click ); ?>"
	data-looping="<?php echo esc_attr( $looping ); ?>"
	data-loop-count="<?php echo esc_attr( $loop_count ); ?>"
	data-direction="<?php echo esc_attr( $direction ); ?>"
	data-fill-mode="<?php echo esc_attr( $fill_mode ); ?>"
	data-fill-color="<?php echo esc_attr( $fill_color_attr ); ?>"
	data-fill-duration="<?php echo esc_attr( $fill_duration ); ?>"
	data-stroke-color="<?php echo esc_attr( $stroke_color_attr ); ?>"
	data-stroke-width="<?php echo esc_attr( $stroke_width_attr ); ?>"
	data-stagger-delay="<?php echo esc_attr( $stagger_delay ); ?>"
	data-lazy-load="<?php echo esc_attr( $lazy_load ); ?>">

	<div class="uabb-svg-wrapper">
		<div class="uabb-svg-container">
			<?php $module->render_content(); ?>
		</div>
	</div>

</div>
