<?php
$class = 'pp-headline';
$loop = isset( $settings->loop ) && $settings->loop == 'yes' ? ' pp-headline-loop' : '';

if ( 'rotate' == $settings->headline_style ) {
	$class .= ' pp-headline-animation-type-' . $settings->animation_type;
	if ( in_array( $settings->animation_type, array( 'typing', 'swirl', 'blinds', 'wave' ) ) ) {
		$class .= ' pp-headline-letters';
	}
}

$class .= ' pp-headline-' . $settings->alignment;

$rotating_text = str_replace( array("\r\n", "\n", "\r", "<br/>", "<br>"), '|', do_shortcode( $settings->rotating_text ) );
$rotating_text = str_replace( "'", "\'", $rotating_text );
$highlighted_text = do_shortcode( $settings->highlighted_text );
$highlighted_text = str_replace( "'", "\'", $highlighted_text );
$animated_text = 'rotate' === $settings->headline_style ? $rotating_text : $highlighted_text;
?>
<div class="pp-animated-headlines pp-headline--style-<?php echo $settings->headline_style; ?><?php echo $loop; ?>" data-text="<?php echo rawurldecode( $animated_text ); ?>">
	<<?php echo esc_attr( $settings->headline_tag ); ?> class="<?php echo $class; ?>">
		<?php if ( isset( $settings->link ) && ! empty( $settings->link ) ) : ?>
		<a <?php $module->render_link_attrs(); ?>>
		<?php endif; ?>

		<?php if ( ! empty( $settings->before_text ) ) : ?>
			<span class="pp-headline-plain-text pp-headline-text-wrapper"><?php echo $settings->before_text; ?></span>
		<?php endif; ?>

		<?php if ( 'rotate' == $settings->headline_style && ! empty( $settings->rotating_text ) ) : ?>
			<span class="pp-headline-dynamic-wrapper pp-headline-text-wrapper">
				<?php if ( pp_is_builder_active() ) {
					_e( 'Preview text', 'bb-powerpack' );
				} ?>
			</span>
		<?php endif; ?>
		
		<?php if ( 'highlight' == $settings->headline_style && ! empty( $settings->highlighted_text ) ) : ?>
			<span class="pp-headline-dynamic-wrapper pp-headline-text-wrapper">
				<span class="pp-headline-dynamic-text pp-headline-text-active"><?php echo $settings->highlighted_text; ?></span>
			</span>
		<?php endif; ?>

		<?php if ( ! empty( $settings->after_text ) ) : ?>
			<span class="pp-headline-plain-text pp-headline-text-wrapper"><?php echo $settings->after_text; ?></span>
		<?php endif; ?>

		<?php if ( isset( $settings->link ) && ! empty( $settings->link ) ) : ?>
		</a>
		<?php endif; ?>
	</<?php echo esc_attr( $settings->headline_tag ); ?>>
</div>
