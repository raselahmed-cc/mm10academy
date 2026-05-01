<?php
/**
 * UABB SVG Animator Module – Dynamic per-node CSS
 *
 * Variables available: $module, $id, $settings, $global_settings (BB-injected).
 *
 * @package UABB SVG Animator Module
 */

// ---- Size (responsive) ----
$svg_size_desktop = ! empty( $settings->svg_size ) ? (float) $settings->svg_size : 300;
$svg_unit_desktop = ! empty( $settings->svg_size_unit ) ? $settings->svg_size_unit : 'px';

$svg_size_tablet = ! empty( $settings->svg_size_medium ) ? (float) $settings->svg_size_medium : $svg_size_desktop;
$svg_unit_tablet = ! empty( $settings->svg_size_medium_unit ) ? $settings->svg_size_medium_unit : $svg_unit_desktop;

$svg_size_mobile = ! empty( $settings->svg_size_responsive ) ? (float) $settings->svg_size_responsive : $svg_size_tablet;
$svg_unit_mobile = ! empty( $settings->svg_size_responsive_unit ) ? $settings->svg_size_responsive_unit : $svg_unit_tablet;

// ---- Alignment (responsive) ----
$align_desktop = ! empty( $settings->svg_alignment ) ? $settings->svg_alignment : 'center';
$align_tablet  = ! empty( $settings->svg_alignment_medium ) ? $settings->svg_alignment_medium : $align_desktop;
$align_mobile  = ! empty( $settings->svg_alignment_responsive ) ? $settings->svg_alignment_responsive : $align_tablet;

// ---- Stroke ----
$stroke_width      = ! empty( $settings->stroke_width ) ? (float) $settings->stroke_width : 1;
$stroke_width_unit = ! empty( $settings->stroke_width_unit ) ? $settings->stroke_width_unit : 'px';
$stroke_color      = UABBSvgAnimatorModule::normalize_color(
	! empty( $settings->stroke_color ) ? $settings->stroke_color : '',
	'#333333'
);

// ---- Fill ----
// Separate the fill color from the fill visibility.
// fill-opacity is set here so the page renders at the correct initial state
// without waiting for JS — preventing a flash of the fill color on page load.
$fill_mode  = ! empty( $settings->fill_mode ) ? $settings->fill_mode : 'none';
$fill_color = ! empty( $settings->fill_color )
	? UABBSvgAnimatorModule::normalize_color( $settings->fill_color )
	: '';

if ( 'none' === $fill_mode ) {
	// No fill — strip all original SVG fills.
	$fill_css         = 'none';
	$fill_opacity_css = '1';
} elseif ( ! empty( $fill_color ) && 'after' === $fill_mode ) {
	// Custom color, revealed after stroke animation — start hidden.
	$fill_css         = $fill_color;
	$fill_opacity_css = '0';
} elseif ( ! empty( $fill_color ) ) {
	// Custom color, 'before' or 'always' — visible from the start.
	$fill_css         = $fill_color;
	$fill_opacity_css = '1';
} elseif ( 'after' === $fill_mode ) {
	// No custom color, 'after' — preserve originals but hide initially.
	$fill_css         = null;
	$fill_opacity_css = '0';
} else {
	// No custom color, 'before' or 'always' — preserve originals, visible.
	$fill_css         = null;
	$fill_opacity_css = '1';
}

// ---- Allowlist validation -------------------------------------------------
// Sanitize CSS unit values against the declared field options. If an unexpected
// value arrives (e.g. via a tampered BB settings export), fall back to a safe
// default rather than emitting an arbitrary string into the stylesheet.
$allowed_units = array( 'px', '%', 'em', 'rem', 'vw' );
if ( ! in_array( $svg_unit_desktop, $allowed_units, true ) ) {
	$svg_unit_desktop = 'px';
}
if ( ! in_array( $svg_unit_tablet, $allowed_units, true ) ) {
	$svg_unit_tablet = 'px';
}
if ( ! in_array( $svg_unit_mobile, $allowed_units, true ) ) {
	$svg_unit_mobile = 'px';
}

// Only 'px' is defined for stroke-width in the form field.
if ( 'px' !== $stroke_width_unit ) {
	$stroke_width_unit = 'px';
}

// Validate alignment values — BB align field emits left/center/right only.
$allowed_alignments = array( 'left', 'center', 'right' );
if ( ! in_array( $align_desktop, $allowed_alignments, true ) ) {
	$align_desktop = 'center';
}
if ( ! in_array( $align_tablet, $allowed_alignments, true ) ) {
	$align_tablet = 'center';
}
if ( ! in_array( $align_mobile, $allowed_alignments, true ) ) {
	$align_mobile = 'center';
}

// Breakpoints — $global_settings is auto-injected by BB into CSS templates.
$medium_bp = isset( $global_settings->medium_breakpoint ) ? absint( $global_settings->medium_breakpoint ) : 992;
$small_bp  = isset( $global_settings->responsive_breakpoint ) ? absint( $global_settings->responsive_breakpoint ) : 768;
?>

/* === UABB SVG Animator: <?php echo esc_attr( $id ); ?> === */

.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-wrapper {
	text-align: <?php echo esc_attr( $align_desktop ); ?>;
}

/* Size: applies to inline SVG (icon or inlined photo SVG) and <img> photos */
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon,
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-photo {
	width: <?php echo esc_attr( $svg_size_desktop . $svg_unit_desktop ); ?>;
	height: auto;
}

/* Stroke + Fill: applies to all SVG shape elements (icon AND inlined photo SVG) */
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon path,
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon circle,
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon rect,
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon line,
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon polyline,
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon polygon,
.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon ellipse {
	stroke: <?php echo esc_attr( $stroke_color ); ?>;
	stroke-width: <?php echo esc_attr( $stroke_width . $stroke_width_unit ); ?>;
<?php if ( null !== $fill_css ) : ?>
	fill: <?php echo esc_attr( $fill_css ); ?>;
<?php endif; ?>
	fill-opacity: <?php echo esc_attr( $fill_opacity_css ); ?>;
}

@media ( max-width: <?php echo absint( $medium_bp ); ?>px ) {
	.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-wrapper {
		text-align: <?php echo esc_attr( $align_tablet ); ?>;
	}
	.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon,
	.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-photo {
		width: <?php echo esc_attr( $svg_size_tablet . $svg_unit_tablet ); ?>;
	}
}

@media ( max-width: <?php echo absint( $small_bp ); ?>px ) {
	.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-wrapper {
		text-align: <?php echo esc_attr( $align_mobile ); ?>;
	}
	.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-icon,
	.fl-node-<?php echo esc_attr( $id ); ?> .uabb-svg-photo {
		width: <?php echo esc_attr( $svg_size_mobile . $svg_unit_mobile ); ?>;
	}
}
