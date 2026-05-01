<?php
/**
 *  UABB Heading Module front-end JS php file.
 *
 *  @package UABB Heading Module.
 */

?>
(function($) {
	var document_width, document_height;
	// start the jquery function 
	jQuery(document).ready( function() {
	document_width  = $( document ).width();
	document_height = $( document ).height();
		<?php
		$photo_src = ( 'url' !== $settings->photo_source ) ? ( ( isset( $settings->photo_src ) && '' !== $settings->photo_src ) ? $settings->photo_src : '' ) : ( ( '' !== $settings->photo_url ) ? $settings->photo_url : '' );

		if ( isset( $photo_src ) ) {
			if ( '' !== $photo_src ) {
				if ( 'yes' === $settings->hotspot_tour ) {

					$interval = $settings->tour_interval;
					if ( empty( $interval ) ) {
						$tour_interval = 4000;
					} else {
						$tour_interval = $interval * 1000;
					}
					?>
					new UABB_Hotspot({
						node            : '<?php echo esc_attr( $id ); ?>',
						hotspot_tour	: '<?php echo esc_attr( $settings->hotspot_tour ); ?>',
						repeat			: '<?php echo esc_attr( $settings->hotspot_tour_repeat ); ?>',
						action_autoplay : '<?php echo esc_attr( $settings->autoplay_options ); ?>', 
						autoplay        : '<?php echo esc_attr( $settings->hotspot_tour_autoplay ); ?>',  
						length          : '<?php echo count( $settings->hotspot_marker ); ?>',
						isElEditMode    : '<?php echo esc_attr( FLBuilderModel::is_builder_active() ); ?>',
						tour_interval	: '<?php echo esc_attr( $tour_interval ); ?>',
						overlay     	: '<?php echo ( 'click' === $settings->autoplay_options ) ? 'yes' : 'no'; ?>',
					});
					<?php

				} elseif ( count( $settings->hotspot_marker ) > 0 ) {
					$count = count( $settings->hotspot_marker );
					for ( $i = 0; $i < $count; $i++ ) {

						if ( 'hover' === $settings->hotspot_marker[ $i ]->tooltip_trigger_on ) {

							if ( 'text' !== $settings->hotspot_marker[ $i ]->hotspot_marker_type ) {
								?>
								jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?> .uabb-hotspot-wrap .uabb-imgicon-wrap').hover(function(event){
									event.stopPropagation();

									var selector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>');

									selector.addClass('uabb-hotspot-hover');		

								}, function(event) {
									event.stopPropagation();

									var selector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>');

									selector.removeClass('uabb-hotspot-hover');				
								});
								<?php
							} else {
								?>

								jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?> .uabb-hotspot-wrap').hover(function(event){
									event.stopPropagation();

									var selector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>');
									selector.addClass('uabb-hotspot-hover');

								}, function(event) {
									event.stopPropagation();

									var selector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>');

									selector.removeClass('uabb-hotspot-hover');

								});
								<?php
							}
						} elseif ( 'always' === $settings->hotspot_marker[ $i ]->tooltip_trigger_on ) {
							?>
							var selector = jQuery( '.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>' );

							var modal_iframe 		= selector.find( 'iframe' ),
								modal_video_tag 	= modal_iframe.find( 'video' );

							if ( selector.hasClass( 'uabb-hotspot-hover' ) ) {
								selector.removeClass( 'uabb-hotspot-hover' );
								if ( modal_iframe.length ) {
									var modal_src = modal_iframe.attr( "src" ).replace( "&autoplay=1", "" );
									modal_iframe.attr( "src", '' );
									modal_iframe.attr( "src", modal_src );
								} else if ( modal_video_tag.length ) {
									modal_video_tag[0].pause();
									modal_video_tag[0].currentTime = 0;
								}
							} else {
								selector.addClass('uabb-hotspot-hover');
							}

							<?php
						} elseif ( 'click' === $settings->hotspot_marker[ $i ]->tooltip_trigger_on ) {

							if ( 'text' !== $settings->hotspot_marker[ $i ]->hotspot_marker_type ) {
								?>
								jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?> .uabb-hotspot-wrap .uabb-imgicon-wrap').click(function(event){
									event.stopPropagation();

									var selector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>');

									var modal_iframe 		= selector.find( 'iframe' ),
										modal_video_tag 	= modal_iframe.find( 'video' );



									if ( selector.hasClass( 'uabb-hotspot-hover' ) ) {
										selector.removeClass( 'uabb-hotspot-hover' );
										if ( modal_iframe.length ) {
											var modal_src = modal_iframe.attr( "src" ).replace( "&autoplay=1", "" );
											modal_iframe.attr( "src", '' );
											modal_iframe.attr( "src", modal_src );
										} else if ( modal_video_tag.length ) {
											modal_video_tag[0].pause();
											modal_video_tag[0].currentTime = 0;
										}
									} else {
										selector.addClass('uabb-hotspot-hover');
									}

								});
								<?php
							} else {
								?>
							jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?> .uabb-hotspot-wrap').click(function(event){
								event.stopPropagation();

								var selector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>');

								var modal_iframe 		= selector.find( 'iframe' ),
									modal_video_tag 	= modal_iframe.find( 'video' );

								if ( selector.hasClass( 'uabb-hotspot-hover' ) ) {
									selector.removeClass( 'uabb-hotspot-hover' );
									if ( modal_iframe.length ) {
										var modal_src = modal_iframe.attr( "src" ).replace( "&autoplay=1", "" );
										modal_iframe.attr( "src", '' );
										modal_iframe.attr( "src", modal_src );
									} else if ( modal_video_tag.length ) {
										modal_video_tag[0].pause();
										modal_video_tag[0].currentTime = 0;
									}
								} else {
									selector.addClass('uabb-hotspot-hover');
								}

							});
								<?php
							}
						}
						?>

						/* Code to hide all tooltip when clicked outside the element */
						<?php

						if ( 'always' !== $settings->hotspot_marker[ $i ]->tooltip_trigger_on ) {
							?>
						jQuery( 'body' ).click( function( event ) {
							if(  !jQuery(event.target).is('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item') && !jQuery(event.target).closest('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item').length ) {

								var bselector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item');

								var modal_iframe 		= bselector.find( 'iframe' ),
									modal_video_tag 	= modal_iframe.find( 'video' );


								if ( bselector.hasClass( 'uabb-hotspot-hover' ) ) {
									bselector.removeClass( 'uabb-hotspot-hover' );
									if ( modal_iframe.length ) {
										var modal_src = modal_iframe.attr( "src" ).replace( "&autoplay=1", "" );
										modal_iframe.attr( "src", '' );
										modal_iframe.attr( "src", modal_src );
									} else if ( modal_video_tag.length ) {
										modal_video_tag[0].pause();
										modal_video_tag[0].currentTime = 0;
									}
								}									
							}
						} );

						jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?> .uabb-hotspot-wrap <?php echo ( 'text' !== $settings->hotspot_marker[ $i ]->hotspot_marker_type ) ? '.uabb-imgicon-wrap' : ''; ?>').click(function(event){
							event.stopPropagation();

							var removeSelector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item').not(".fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>");

							var modal_iframe 		= removeSelector.find( 'iframe' ),
								modal_video_tag 	= modal_iframe.find( 'video' );

							removeSelector.removeClass('uabb-hotspot-hover');

							if ( modal_iframe.length ) {
								var modal_src = modal_iframe.attr( "src" ).replace( "&autoplay=1", "" );
								modal_iframe.attr( "src", '' );
								modal_iframe.attr( "src", modal_src );
							} else if ( modal_video_tag.length ) {
								modal_video_tag[0].pause();
								modal_video_tag[0].currentTime = 0;
							}

						});
							<?php
						}
					}
				}
			}
		}
		?>

		responsiveTooltipShift();

		// Add keyboard accessibility to all hotspot modules
		addHotspotKeyboardAccessibility();
	});

	jQuery(document).on("load", function() {
		document_width = $( document ).width();
		document_height = $( document ).height();
		responsiveTooltipShift();
	});

	jQuery(window).resize( function() {
		if( document_width !== $( document ).width() || document_height !== $( document ).height() ) {
			document_width = $( document ).width();
			document_height = $( document ).height();
			responsiveTooltipShift();
		}
	});

	function responsiveTooltipShift() {
		<?php
		$photo_src = ( 'url' !== $settings->photo_source ) ? ( ( isset( $settings->photo_src ) && '' !== $settings->photo_src ) ? $settings->photo_src : '' ) : ( ( '' !== $settings->photo_url ) ? $settings->photo_url : '' );

		if ( isset( $photo_src ) ) {
			if ( '' !== $photo_src ) {
				if ( count( $settings->hotspot_marker ) > 0 ) {
					$count = count( $settings->hotspot_marker );
					for ( $i = 0; $i < $count; $i++ ) {
						?>
						var tooltip_style = '<?php echo esc_attr( $settings->hotspot_marker[ $i ]->tooltip_style ); ?>',
							itemSelector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?>'),
							tooltip_content_position = '<?php echo esc_attr( $settings->hotspot_marker[ $i ]->tooltip_content_position ); ?>',
							itemPosition = itemSelector.offset(),
							outerContainerWidth = window.innerWidth,
							tooltipSelector = jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-item-<?php echo esc_attr( $i ); ?> .uabb-hotspot-tooltip-content');

						var tooltipWidth = tooltipSelector.outerWidth(true);

						if( tooltip_style !== 'round' ) {
							if( 'left' === tooltip_content_position ) {
								//console.log('left - '+itemPosition.left);
								if( itemPosition.left <= ( tooltipWidth + 5 ) ) {
									itemSelector.find('.uabb-hotspot-tooltip').removeClass('uabb-tooltip-left');
									itemSelector.find('.uabb-hotspot-tooltip').addClass('uabb-tooltip-right');
								} else {
									itemSelector.find('.uabb-hotspot-tooltip').removeClass('uabb-tooltip-right');
									itemSelector.find('.uabb-hotspot-tooltip').addClass('uabb-tooltip-left');
								}
							}
							if( tooltip_style === 'curved' ) {
								tooltipWidth += 42;
							}

							if( 'right' === tooltip_content_position ) {
								//console.log(tooltipWidth + 45);
								//console.log('right - '+( outerContainerWidth - itemPosition.left ));
								if( ( outerContainerWidth - itemPosition.left ) <= ( tooltipWidth + 45 ) ) {
									itemSelector.find('.uabb-hotspot-tooltip').removeClass('uabb-tooltip-right');
									itemSelector.find('.uabb-hotspot-tooltip').addClass('uabb-tooltip-left');
								} else {
									itemSelector.find('.uabb-hotspot-tooltip').removeClass('uabb-tooltip-left');
									itemSelector.find('.uabb-hotspot-tooltip').addClass('uabb-tooltip-right');
								}
							}
						}
						itemSelector = '';
						<?php
					}
				}
			}
		}
		?>
	}

	// Add keyboard accessibility function for hotspot markers
	function addHotspotKeyboardAccessibility() {
		// Add keyboard event listeners to all hotspot markers
		jQuery('.fl-node-<?php echo esc_attr( $id ); ?> .uabb-hotspot-tooltip').each(function(index) {
			var $hotspot = jQuery(this);
			var $hotspotItem = $hotspot.closest('.uabb-hotspot-item');
			var nodeClass = '.fl-node-<?php echo esc_attr( $id ); ?>';

			// Handle focus event - show tooltip
			$hotspot.on('focus', function(e) {
				e.preventDefault();
				if (!$hotspotItem.hasClass('uabb-hotspot-hover')) {
					// Hide all other tooltips first
					jQuery(nodeClass + ' .uabb-hotspot-item').removeClass('uabb-hotspot-hover');
					// Show this tooltip
					$hotspotItem.addClass('uabb-hotspot-hover');
					// Update aria-expanded
					$hotspot.attr('aria-expanded', 'true');
				}
			});

			// Handle blur event - hide tooltip after a short delay
			$hotspot.on('blur', function(e) {
				setTimeout(function() {
					// Only hide if focus didn't move to another hotspot
					if (!jQuery(document.activeElement).closest(nodeClass + ' .uabb-hotspot-item').length) {
						$hotspotItem.removeClass('uabb-hotspot-hover');
						$hotspot.attr('aria-expanded', 'false');
					}
				}, 100);
			});

			// Handle Enter and Space key activation
			$hotspot.on('keydown', function(e) {
				if (e.which === 13 || e.which === 32) { // Enter or Space key
					e.preventDefault();

					// If it's a link, follow the link
					if ($hotspot.is('a') && $hotspot.attr('href')) {
						if ($hotspot.attr('target') === '_blank') {
							window.open($hotspot.attr('href'), '_blank');
						} else {
							window.location = $hotspot.attr('href');
						}
					} else {
						// Toggle tooltip visibility
						if ($hotspotItem.hasClass('uabb-hotspot-hover')) {
							$hotspotItem.removeClass('uabb-hotspot-hover');
							$hotspot.attr('aria-expanded', 'false');
						} else {
							// Hide all other tooltips first
							jQuery(nodeClass + ' .uabb-hotspot-item').removeClass('uabb-hotspot-hover');
							jQuery(nodeClass + ' .uabb-hotspot-tooltip').attr('aria-expanded', 'false');
							// Show this tooltip
							$hotspotItem.addClass('uabb-hotspot-hover');
							$hotspot.attr('aria-expanded', 'true');
						}
					}
				}

				// Handle Escape key - hide tooltip
				if (e.which === 27) { // Escape key
					$hotspotItem.removeClass('uabb-hotspot-hover');
					$hotspot.attr('aria-expanded', 'false');
				}
			});
		});
	}

})(jQuery);
