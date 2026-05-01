<?php
$durations = apply_filters( 'pp_animated_headline_durations', array(
	'animationDelay' => isset( $settings->animation_delay ) && ! empty( $settings->animation_delay ) ? absint( $settings->animation_delay ) : 2500,
	'lettersDelay' => isset( $settings->letters_delay ) && ! empty( $settings->letters_delay ) ? absint( $settings->letters_delay ) : 50,
	'typeLettersDelay' => isset( $settings->type_letters_delay ) && ! empty( $settings->type_letters_delay ) ? absint( $settings->type_letters_delay ) : 150,
	'selectionDuration' => isset( $settings->selection_duration ) && ! empty( $settings->selection_duration ) ? absint( $settings->selection_duration ) : 500,
	'revealDuration' => isset( $settings->reveal_duration ) && ! empty( $settings->reveal_duration ) ? absint( $settings->reveal_duration ) : 600,
	'revealAnimationDelay' => isset( $settings->reveal_animation_delay ) && ! empty( $settings->reveal_animation_delay ) ? absint( $settings->reveal_animation_delay ) : 1500,
), $settings );
?>

if ( 'object' !== typeof pp_animated_headlines ) {
	var pp_animated_headlines = {};
}

;(function($) {

	pp_animated_headlines['<?php echo $id; ?>'] = new PPAnimatedHeadlines({
        id: '<?php echo $id; ?>',
        headline_style: '<?php echo $settings->headline_style; ?>',
        headline_shape: '<?php echo $settings->headline_shape; ?>',
        animation_type: '<?php echo $settings->animation_type; ?>',
		durations: <?php echo json_encode( $durations ); ?>,
		isBuilderActive: '<?php echo pp_is_builder_active(); ?>'
    });

})(jQuery);
