<?php
	$cookie_time = isset( $settings->display_after ) ? absint( $settings->display_after ) : 0;
    $responsive_display = $settings->responsive_display;
    $medium_device = $global_settings->medium_breakpoint;
    $small_device = $global_settings->responsive_breakpoint;
    $breakpoint = '';
    if ( $responsive_display == 'desktop' ) {
        $breakpoint = '> ' . $medium_device;
    }
    if ( $responsive_display == 'desktop-medium' ) {
        $breakpoint = '>= ' . $medium_device;
    }
    if ( $responsive_display == 'medium' ) {
        $breakpoint = '=== ' . $medium_device;
    }
    if ( $responsive_display == 'medium-mobile' ) {
        $breakpoint = '<= ' . $medium_device;
    }
    if ( $responsive_display == 'mobile' ) {
        $breakpoint = '<= ' . $small_device;
    }
?>

<?php if ( ! FLBuilderModel::is_builder_active() ) { ?>
;(function($) {

	if ( 'undefined' === typeof $.cookie ) {
		<?php echo file_get_contents( BB_POWERPACK_DIR . 'assets/js/jquery.cookie.min.js' ); ?>
	}

	function set_cookie() {
		var cookieKey = 'pp_annoucement_bar_<?php echo $id; ?>';
		if ( parseInt( <?php echo $cookie_time; ?> ) > 0 ) {
			return $.cookie( cookieKey, <?php echo $cookie_time; ?>, {expires: <?php echo $cookie_time; ?>, path: '/'} );
		} else {
			return $.cookie( cookieKey, 0, {expires: 0, path: '/'} );
		}
	}

	function is_cookie_set()
	{
		var oldCookie = $.cookie( 'pp_annoucement_bar' );
		var newCookie = $.cookie( 'pp_annoucement_bar_<?php echo $id; ?>' );
		if ( oldCookie && parseInt( oldCookie ) > 0 && ! newCookie ) {
			return true;
		}
		if ( newCookie && parseInt( newCookie ) > 0 ) {
			return true;
		}

		return false;
	}

    $(window).on('load', function() {
        <?php if ( $responsive_display != '' && $breakpoint != '' ) { ?>
        if ( $(window).width() <?php echo $breakpoint; ?> ) {
        <?php } ?>

        setTimeout(function() {
			if ( ! is_cookie_set() ) {
				if( $('.fl-node-<?php echo $id; ?>:visible .pp-announcement-bar-wrap').hasClass('pp-announcement-bar-bottom') ) {
					$('html').addClass('pp-bottom-bar');
				}
				if( $('.fl-node-<?php echo $id; ?>:visible .pp-announcement-bar-wrap').hasClass('pp-announcement-bar-top') ) {
					var thisHeight = $('.fl-node-<?php echo $id; ?> .pp-announcement-bar-wrap').outerHeight();

					if( $('body').hasClass('admin-bar') ) {
						$('html').addClass('pp-top-bar-admin');
						thisHeight = thisHeight + $('#wpadminbar').outerHeight();
					}

					var style = '<style id="pp-style"> .pp-announcement-bar.pp-top-bar { margin-top: ' + thisHeight + 'px !important; } .fl-fixed-header .fl-page { padding: 0 !important; } .pp-announcement-bar.pp-top-bar .fl-builder-content[data-type="header"].fl-theme-builder-header-sticky { top: ' + thisHeight + 'px; transform: none !important; } </style>';
					if ( $( '#pp-style' ).length === 0 ) {
						$('head').append(style);
					}

					$('html').addClass('pp-announcement-bar pp-top-bar');
				}

				
				$('.fl-node-<?php echo $id; ?>:visible .pp-announcement-bar-close-button').on('click keypress', function(e) {
					if (e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined) {
						<?php if( $settings->announcement_bar_position == 'top' ) { ?>
							$('html').removeClass('pp-top-bar');
							$('html').removeClass('pp-top-bar-admin');
						<?php } else { ?>
							$('html').removeClass('pp-bottom-bar');
						<?php } ?>
						$('#pp-style').remove();
						set_cookie();
					}
				});
				
			}
        }, 1000);

        <?php if ( $responsive_display != '' && $breakpoint != '' ) { ?>
        }
        <?php } ?>

    });

})(jQuery);
<?php } ?>
