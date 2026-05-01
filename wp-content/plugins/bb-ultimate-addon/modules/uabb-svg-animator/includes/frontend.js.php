<?php
/**
 * UABB SVG Animator Module – Per-node JS initializer
 *
 * Bootstraps the UABBSvgAnimator class for this specific module instance.
 * The $id variable is unique per module node, scoping the selector correctly.
 *
 * Variables available: $module, $id, $settings.
 *
 * @package UABB SVG Animator Module
 */

?>
(function($) {
	'use strict';

	$(function() {
		var $el = $('.fl-node-<?php echo esc_js( $id ); ?> .uabb-svg-animator');
		if ( $el.length && typeof UABBSvgAnimator !== 'undefined' ) {
			// Avoid double-init on partial refresh.
			if ( ! $el.data('uabb-svg-animator') ) {
				var instance = new UABBSvgAnimator( $el[0] );
				$el.data( 'uabb-svg-animator', instance );
			}
		}
	});

})(jQuery);
