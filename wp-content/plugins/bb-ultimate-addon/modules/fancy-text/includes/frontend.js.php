<?php
/**
 *  UABB Fancy Text Module front-end JS php file
 *
 *  @package UABB Fancy Text Module
 */

$fancy_text = str_replace( array( "\r\n", "\n", "\r", '<br/>', '<br>' ), '|', $settings->fancy_text );

if ( 'type' === $settings->effect_type ) {

	// Build strings array for TranslatePress dynamic string detection.
	$txt_order   = array( "\r\n", "\n", "\r", '<br/>', '<br>' );
	$trp_text    = addslashes( $settings->fancy_text );
	$trp_str     = str_replace( $txt_order, '|', $trp_text );
	$trp_lines   = explode( '|', $trp_str );
	$trp_strings = '[';
	foreach ( $trp_lines as $key => $line ) {
		$trp_strings .= '"' . trim( htmlspecialchars_decode( wp_strip_all_tags( $line ) ) ) . '"';
		if ( ( count( $trp_lines ) - 1 ) !== $key ) {
			$trp_strings .= ',';
		}
	}
	$trp_strings .= ']';

	$type_speed  = ( ! empty( $settings->typing_speed ) ) ? $settings->typing_speed : 35;
	$start_delay = ( ! empty( $settings->start_delay ) ) ? $settings->start_delay : 200;
	$back_speed  = ( ! empty( $settings->back_speed ) ) ? $settings->back_speed : 0;
	$back_delay  = ( ! empty( $settings->back_delay ) ) ? $settings->back_delay : 1500;
	$loop        = ( 'no' === $settings->enable_loop ) ? 'false' : 'true';
	$show_cursor = 'false';
	$cursor_char = '|';

	if ( 'yes' === $settings->show_cursor ) {
		$show_cursor = 'true';
		$cursor_char = ( ! empty( $settings->cursor_text ) ) ? $settings->cursor_text : '|';
	}
	?>

/* TranslatePress dynamic string detection — not used directly, strings are read from the DOM. */
var _uabb_trp_strings_<?php echo esc_attr( $id ); ?> = <?php echo $trp_strings; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;

jQuery( document ).ready(function($) {
	var nodeClass = '.fl-node-<?php echo esc_attr( $id ); ?>';
	var stringsEl = $( nodeClass + ' .uabb-typed-strings' );
	var strings   = [];

	/* Read strings from DOM so translation plugins (e.g. TranslatePress) can translate them server-side. */
	stringsEl.find( '> span' ).each( function() {
		var text = $( this ).text().trim();
		if ( text ) {
			strings.push( text );
		}
	});


	new UABBFancyText({
		id:                 '<?php echo esc_attr( $id ); ?>',
		viewport_position:  90,
		animation:          '<?php echo esc_attr( $settings->effect_type ); ?>',
		strings:            strings,
		typeSpeed:          <?php echo $type_speed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		startDelay:         <?php echo $start_delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		backSpeed:          <?php echo $back_speed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		backDelay:          <?php echo $back_delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		loop:               <?php echo $loop; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		showCursor:         <?php echo $show_cursor; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		cursorChar:         '<?php echo $cursor_char; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>'
	});
});

	<?php
} elseif ( 'slide_up' === $settings->effect_type ) {
	$speed = $pause = $mouse_pause = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found

	$speed       = ( ! empty( $settings->animation_speed ) ) ? $settings->animation_speed : 200;
	$pause       = ( ! empty( $settings->pause_time ) ) ? $settings->pause_time : 3000;
	$mouse_pause = ( 'yes' === $settings->pause_hover ) ? true : false;
	?>
	jQuery( document ).ready(function($) {
	var wrapper = $('.fl-node-<?php echo esc_attr( $id ); ?>'),
	slide_block = wrapper.find('.uabb-slide-main'),
	slide_block_height = slide_block.find('.uabb-slide_text').height();
	slide_block.height(slide_block_height);

	var UABBFancy_<?php echo esc_attr( $id ); ?> = new UABBFancyText({
		id:                 '<?php echo esc_attr( $id ); ?>',
		viewport_position:  90,
		animation:          '<?php echo esc_attr( $settings->effect_type ); ?>',
		speed:              <?php echo $speed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		pause:              <?php echo $pause; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		mousePause:         Boolean( '<?php echo $mouse_pause; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>' ),
		suffix:             "<?php echo addslashes( $settings->suffix ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>",
		prefix:             "<?php echo addslashes( $settings->prefix ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>",
		alignment:          '<?php echo $settings->alignment; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>',
	});

	$( window ).resize(function() {
	UABBFancy_<?php echo esc_attr( $id ); ?>._initFancyText();
	});
	});

	<?php

} else {
	$animation_speed = ( ! empty( $settings->animation_delay ) ) ? $settings->animation_delay : 2500;
	$duration_reveal = ( ! empty( $settings->duration_reveal ) ) ? $settings->duration_reveal : 600;
	$animation_revel = ( ! empty( $settings->animation_revel ) ) ? $settings->letter_delay : 1500;
	$letter_delay    = ( ! empty( $settings->letter_delay ) ) ? $settings->letter_delay : 50;

	?>
	/* TranslatePress dynamic string detection — not used directly, strings are read from the DOM. */
	var _uabb_trp_fancy_<?php echo esc_attr( $id ); ?> = '<?php echo str_replace( "'", "\'", $fancy_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';

	jQuery( document ).ready(function($) {
	var nodeClass = '.fl-node-<?php echo esc_attr( $id ); ?>';
	var stringsEl = $( nodeClass + ' .uabb-fancy-text-strings' );
	var fancyStrings = [];

	/* Read strings from DOM so translation plugins can translate them server-side. */
	stringsEl.find( '> span' ).each( function() {
		var text = $( this ).text().trim();
		if ( text ) {
			fancyStrings.push( text );
		}
	});

	new UABBFancyText({
		id:                 '<?php echo esc_attr( $id ); ?>',
		animation:          '<?php echo esc_attr( $settings->effect_type ); ?>',
		animation_speed:     <?php echo $animation_speed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		duration_reveal:     <?php echo $duration_reveal; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		animation_revel:     <?php echo $animation_revel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		letter_delay:     <?php echo $letter_delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Changing sanitization affecting Fancy Text behavior in the page editor. ?>,
		fancy_text: fancyStrings.join('|'),
	});
});
<?php }
?>
