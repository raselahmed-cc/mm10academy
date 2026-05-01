<?php
/**
 * PowerPack admin settings page.
 *
 * @package bb-powerpack
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php
$license 	 = self::get_option( 'bb_powerpack_license_key' );
$current_tab = self::get_current_tab();
?>

<div class="wrap pp-admin-settings-wrap">
	<div class="pp-admin-settings-header">
		<div class="pp-admin-settings-head">
			<h3>
				<span><?php echo pp_get_admin_label(); ?></span>
				<span class="ver"><?php echo BB_POWERPACK_VER; ?></span>
			</h3>
			<?php self::render_top_nav(); ?>
		</div>
		<div class="pp-admin-settings-tabs">
			<?php self::render_tabs( $current_tab ); ?>
		</div>
	</div>

	<div class="pp-admin-settings-content pp-admin-settings-<?php echo $current_tab; ?>">
		<h2 class="pp-notices-target"></h2>
		<?php self::render_setting_page(); ?>
		<?php self::render_update_message(); ?>
	</div>
</div>
<style>
#wpcontent {
	padding-left: 0;
}
<?php if ( $accent_color = self::get_option( 'ppwl_accent_color' ) ) { ?>
.pp-admin-settings-wrap {
	--pp-primary-color: <?php echo esc_attr( $accent_color ); ?>;
	<?php if ( is_callable( 'FLBuilderColor::adjust_brightness' ) ) { ?>
	--pp-primary-color-dark: #<?php echo FLBuilderColor::adjust_brightness( $accent_color, 25, 'darken' ); ?>;
	--pp-primary-color-light: <?php echo pp_hex2rgba( $accent_color, 0.1 ); ?>;
	<?php } else { ?>
	--pp-primary-color-dark: #333;
	--pp-primary-color-light: #666;
	<?php } ?>
}
<?php } ?>
</style>
<script>
(function($) {
	$('.pp-admin-field-toggle input').on( 'focus', function() {
		$(this).parent().addClass( 'focus' );
	} ).on( 'blur', function() {
		$(this).parent().removeClass( 'focus' );
	} );

	$('.pp-admin-settings-tabs .nav-tab').on( 'click', function() {
		$('.pp-admin-settings-content').html('<div style="display: flex; padding: 20px;"><img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" /> &nbsp;<?php esc_html_e( 'Loading...', 'bb-powerpack' ); ?></div>');
	} );

	$('.pp-admin-settings-wrap').on('click', '.alert .dismiss', function() {
		$(this).closest('.alert').fadeOut( 300, function() {
			$(this).closest('.alert').remove();
		});
	});

	if ( $('.pp-admin-settings-wrap .alert-success').length ) {
		setTimeout( function() {
			$('.pp-admin-settings-wrap .alert-success').fadeOut( 600, function() {
				$(this).closest('.alert').remove();
			});
		}, 5000 );
	}

	$('table tr[tabindex]').on( 'keypress', function(e) {
		if ( e.which == 1 || e.which == 13 || e.which == 32 ) {
			e.preventDefault();
			e.stopPropagation();

			var $input = $(this).find( 'input[type=checkbox]' );
			if ( $input.prop( 'checked' ) ) {
				$input.prop( 'checked', false );
			} else {
				$input.prop( 'checked', true );
			}
		}
	} );
})(jQuery);
</script>