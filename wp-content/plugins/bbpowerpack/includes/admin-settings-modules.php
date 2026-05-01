<?php
/**
 * Modules settings page.
 *
 * @package bb-powerpack
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php
	$enabled_modules = BB_PowerPack_Modules::get_enabled_modules();
	$enabled_categories = BB_PowerPack_Modules::get_enabled_categories();
	$current_filter = isset( $_GET['show'] ) ? $_GET['show'] : '';
	$used_modules = array();
	if ( ! empty( $current_filter ) ) {
		$used_modules = BB_PowerPack_Modules::get_used_modules();
	}
	$module_deps = BB_PowerPack_Modules::get_module_dependency();
?>
<div class="pp-admin-settings-content-head">
	<h3><?php esc_html_e( 'Modules', 'bb-powerpack' ); ?></h3>
	<p class="description"><?php esc_html_e( 'You can manage the modules for your site from this page.', 'bb-powerpack' ); ?></p>
	<?php if ( ! is_network_admin() && is_multisite() ) : ?>
	<div class="alert alert-info"><?php esc_html_e( 'NOTE: By activating / deactivating any module will override the network settings.', 'bb-powerpack' ); ?></div>
	<?php endif; ?>
</div>
<form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">
	<div class="pp-modules-manager">
		<div class="pp-modules-manager-filters">
			<select class="pp-modules-manager-filter">
				<option value=""><?php esc_html_e( 'Filter: All Modules', 'bb-powerpack' ); ?></option>
				<option value="used"<?php echo 'used' == $current_filter ? ' selected' : ''; ?>><?php esc_html_e( 'Filter: Used Modules', 'bb-powerpack' ); ?></option>
				<option value="notused"<?php echo 'notused' == $current_filter ? ' selected' : ''; ?>><?php esc_html_e( 'Filter: Not Used Modules', 'bb-powerpack' ); ?></option>
			</select>
			<div class="search-form">
				<input type="text" placeholder="<?php esc_html_e( 'Search modules...', 'bb-powerpack' ); ?>" />
				<?php echo file_get_contents( BB_POWERPACK_DIR . 'assets/images/search-icon.svg' ); ?>
			</div>
		</div>
		<div class="pp-modules-manager-sections">
			<?php foreach ( BB_PowerPack_Modules::get_categorized_modules() as $cat => $data ) {
				$is_cat_enabled = in_array( $cat, $enabled_categories );
			?>
			<div class="pp-modules-manager-section">
				<div class="pp-modules-manager-section-header">
					<h3>
						<span for="bb_powerpack_<?php echo $cat; ?>"><?php echo $data['category']; ?></span>
						<label class="pp-admin-field-toggle">
							<input id="bb_powerpack_<?php echo $cat; ?>" name="bb_powerpack_module_categories[]" type="checkbox" value="<?php echo $cat; ?>"<?php echo $is_cat_enabled ? ' checked="checked"' : '' ?> />
							<span class="pp-admin-field-toggle-slider" aria-hidden="true"></span>
						</label>
					</h3>
				</div>
				<div class="pp-modules-manager-section-content">
					<table class="form-table pp-grid-table pp-modules" data-category="<?php echo $cat; ?>">
						<?php foreach ( $data['modules'] as $module ) {
							$is_enabled = in_array( $module['slug'], $enabled_modules ) && $module['enabled'];
							$row_class = ! $is_enabled ? 'pp-module-inactive' : '';
							$row_class .= isset( $module_deps[ $module['slug'] ] ) ? ' pp-module-has-dep' : '';
							$deps = isset( $module_deps[ $module['slug'] ] ) ? $module_deps[ $module['slug'] ] : array();
							if ( 'used' === $current_filter && ! isset( $used_modules[ $module['slug'] ] ) ) {
								$row_class .= ' pp-modules-filter-used';
								//continue;
							}
							if ( 'notused' === $current_filter && isset( $used_modules[ $module['slug'] ] ) ) {
								$row_class .= ' pp-modules-filter-notused';
								//continue;
							}
							$used_on = isset( $used_modules[ $module['slug'] ] ) ? $used_modules[ $module['slug'] ] : false;
							$used_on_text = array();
							if ( $used_on ) {
								foreach ( $used_on as $type => $used ) {
									$type  = str_replace( 'fl-theme-layout', 'Themer Layout', $type );
									$type  = str_replace( 'fl-builder-template', 'Builder Template', $type );
									$used_on_text[] = sprintf( '%s times on %s %ss', $used['total'], count( $used ) - 1, ucfirst( $type ) );
								}
								$used_on_text = implode( ', ', $used_on_text );
							}
							$row_class .= $used_on ? ' pp-module-used' : '';
							?>
							<tr valign="top" class="<?php echo $row_class; ?>" tabindex="0">
								<th scope="row" valign="top">
									<label for="bb_powerpack_modules_<?php echo $module['slug']; ?>"><?php echo $module['name']; ?></label>
									<?php if ( ! empty( $used_on_text ) ) { ?>
									<span class="pp-module-used-description"><?php echo $used_on_text; ?></span>
									<?php } ?>
									<?php if ( ! empty( $deps ) ) { ?>
										<span class="pp-module-tooltip">
											<?php foreach ( $deps as $dep ) { ?>
												<?php echo isset( $data['modules'][ $dep ] ) ?
													sprintf( __( 'Dependent modules: %s', 'bb-powerpack' ), $data['modules'][ $dep ]['name'] )
													: $dep; ?>
											<?php } ?>
										</span>
									<?php } ?>
								</th>
								<td>
									<label class="pp-admin-field-toggle">
										<input
											id="bb_powerpack_modules_<?php echo $module['slug']; ?>" 
											name="bb_powerpack_modules[]" 
											type="checkbox" 
											value="<?php echo $module['slug']; ?>"
											<?php echo $is_enabled ? ' checked="checked"' : '' ?>
											tabindex="-1"
											aria-hidden="true"
										/>
										<span class="pp-admin-field-toggle-slider" aria-hidden="true"></span>
									</label>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
			<?php } // End foreach(). ?>
		</div>
	</div>
	<?php wp_nonce_field( 'pp-modules', 'pp-modules-nonce' ); ?>
	<?php submit_button(); ?>
	<?php do_action( 'pp_admin_settings_before_form_close', $current_tab ); ?>
</form>

<script>
	(function($) {
		// Toggle inactive class.
		$('input[name="bb_powerpack_modules[]"').on('change', function() {
			if ( $(this).is(':checked') ) {
				$(this).parents('tr').removeClass('pp-module-inactive');
			} else {
				$(this).parents('tr').addClass('pp-module-inactive');
			}
		});

		// Toggle all modules in a category.
		$('input[name="bb_powerpack_module_categories[]"').on('change', function() {
			var active = $(this).is(':checked');
			var category = $(this).val();
			var $table = $('.pp-modules-manager').find('table[data-category="' + category + '"]');
			$table.find('input[name="bb_powerpack_modules[]"]').each(function() {
				if ( $(this).parents('.pp-modules-filter-used').length === 0 && $(this).parents('.pp-modules-filter-notused').length === 0 ) {
					if ( active ) {
						$(this).prop('checked', true);
					} else {
						$(this).prop('checked', false);
					}
					$(this).trigger('change');
				}
			});
		});

		// Toggle content.
		$('.pp-modules-manager-section-header h3 > span').on('click', function(e) {
			var $toggle = $(this);
			var $content = $(this).closest('.pp-modules-manager-section-header').next();
			$content.slideToggle(400, function() {
				if ( ! $(this).is(':visible') ) {
					$toggle.addClass( 'is-hidden' );
				} else {
					$toggle.removeClass( 'is-hidden' );
				}
			});
		});

		// Search.
		$('.pp-modules-manager-filters .search-form input').on('keyup', function(e) {
			e.stopPropagation();
			var value = $(this).val().toLowerCase();

			setTimeout(function() {
				if ( value.length < 2 ) {
					$('.pp-modules tr').show();
					return;
				}
				if ( value.length === 0 ) {
					$('.pp-modules tr').show();
					return;
				}
				$('.pp-modules tr').hide();
				$('.pp-modules tr').each(function() {
					var label = $(this).find('label').text().toLowerCase();
					if ( label.search( value ) !== -1 ) {
						$(this).show();
					}
				});
			}, 500);
		});

		// Filter.
		$('.pp-modules-manager-filter').on('change', function() {
			var currentUrl = location.href;
			currentUrl = currentUrl.replace( /&show=.*/g, '' );
			if ( $(this).val() !== '' ) {
				currentUrl = currentUrl + '&show=' + $(this).val();
			}
			location.href = currentUrl;
		});
	})(jQuery);
</script>