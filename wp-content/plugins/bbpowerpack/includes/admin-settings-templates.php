<?php
/**
 * Templates settings page.
 *
 * @since 1.0.0
 * @package bb-powerpack
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php
	$navigate = ( isset( $_GET['navigate'] ) && ! empty( $_GET['navigate'] ) ) ? esc_attr( $_GET['navigate'] ) : 'page-templates';
	$template_type = ( $navigate == 'page-templates' ) ? 'page' : 'row';
?>
<div class="pp-admin-settings-content-head">
	<h3><?php esc_html_e( 'Templates', 'bb-powerpack' ); ?></h3>
	<p><?php esc_html_e( 'You can manage the templates for your site from this page.', 'bb-powerpack' ); ?></p>
	<?php if ( ! is_network_admin() && is_multisite() ) { ?>
		<div class="alert alert-info"><?php esc_html_e( 'NOTE: By activating / deactivating any template will override the network settings.', 'bb-powerpack' ); ?></div>	
	<?php } ?>
</div>
<form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">
	<div class="pp-page-templates">
		<div class="wp-filter pp-template-filter hide-if-no-js">
			<div>
				<div class="filter-count">
					<span class="count theme-count">0</span>
				</div>
				<ul class="filter-links">
					<li><a href="<?php echo self::get_form_action( '&tab=templates&navigate=page-templates' ); ?>" class="<?php echo ( 'page-templates' == $navigate ) ? 'current' : ''; ?>" data-type="page-templates"><?php esc_html_e( 'Page Templates', 'bb-powerpack' ); ?></a></li>
					<li><a href="<?php echo self::get_form_action( '&tab=templates&navigate=row-templates' ); ?>" class="<?php echo ( 'row-templates' == $navigate ) ? 'current' : ''; ?>" data-type="row-templates"><?php esc_html_e( 'Row Templates', 'bb-powerpack' ); ?></a></li>
				</ul>
			</div>
			<div class="pp-refresh-panel">
				<a href="<?php echo self::get_form_action( '&tab=templates&navigate='.$navigate.'&refresh=1' ); ?>" title="<?php _e( 'Sync Templates', 'bb-powerpack' ); ?>" aria-label="<?php _e( 'Sync Templates', 'bb-powerpack' ); ?>">
					<span class="dashicons dashicons-update"></span>
				</a>
			</div>
		</div>

		<div class="pp-templates-grid pp-page-templates-grid wp-clearfix">
			<span class="ajax-spinner"><img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class="loader-image" /></span>
		</div>

	</div>
	<div class="pp-template-overlay">
		<div class="pp-template-backdrop"></div>
		<div class="pp-template-wrap wp-clearfix">
			<div class="pp-template-header">
				<button class="left dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e('Show previous template', 'bb-powerpack'); ?></span></button>
				<button class="right dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e('Show next template', 'bb-powerpack'); ?></span></button>
				<button class="close dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e('Close details dialog', 'bb-powerpack'); ?></span></button>
			</div>
			<div class="pp-template-info wp-clearfix">
				<span class="ajax-spinner"><img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class="loader-image" /></span>
				<iframe class="pp-template-preview-frame" src="" frameborder="0" height="100%" width="100%" seamless></iframe>
			</div>
			<div class="pp-template-actions">
				<span class="ajax-spinner"><img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="loader-image" /></span>
				<a class="button button-primary pp-activate-template" href="<?php echo self::get_form_action( '&tab=templates&action=pp_activate_template&pp_template_cat=' ); ?>" data-template-type="<?php echo $template_type; ?>" data-template-cat=""><?php esc_html_e('Activate', 'bb-powerpack'); ?></a>
				<a class="button button-secondary pp-deactivate-template" href="<?php echo self::get_form_action( '&tab=templates&action=pp_deactivate_template&pp_template_cat=' ); ?>" data-template-type="<?php echo $template_type; ?>" data-template-cat=""><?php esc_html_e('Deactivate', 'bb-powerpack'); ?></a>
			</div>
		</div>
	</div>
<?php wp_nonce_field( 'pp-templates', 'pp-templates-nonce' ); ?>
</form>

<script type="text/template" id="tmpl-pp-templates-view">
	<div class="pp-templates-actions">
	<# if ( 'row' === data.templates_type ) { #>
	<p class="description"><?php _e( 'Row templates are grouped into different categories. Activating a category set will activate all the templates available in the respective category.', 'bb-powerpack' ); ?></p>
	<# } #>
	{{{data.actions}}}
	</div>
	<div class="pp-templates">{{{data.templates}}}</div>
</script>

<script type="text/template" id="tmpl-pp-template">
	<div class="pp-template pp-{{{data.type}}}-template is-visible<# if ( data.is_active ) { #> active<# } #><# if ( '' !== data.preview_url ) { #> pp-preview-enabled<# } #>" tabindex="0" data-filter="{{{data.filter}}}" aria-describedby="pp-{{{data.slug}}}">
		<div class="pp-template-screenshot"><img src="{{{data.screenshot}}}" alt="{{{data.slug}}}" /></div>
		<# if ( '' !== data.preview_url ) { #>
		<span class="pp-template-preview" data-preview-src="{{{data.preview_url}}}" data-template-cat="{{{data.category}}}" role="button" aria-label="<?php esc_html_e( 'Show Preview', 'bb-powerpack' ); ?>"><?php esc_html_e( 'Preview', 'bb-powerpack' ); ?></span>
		<# } #>
		<h2 class="pp-template-category" id="pp-{{{data.slug}}}">{{{data.title}}}<# if ( '' !== data.count ) { #> - {{{data.count}}}<# } #><# if ( 'row' === data.type ) { #> <?php esc_html_e( 'Templates', 'bb-powerpack' ); ?><# } #><span class="dashicons dashicons-yes"></span></h2>
		<div class="pp-template-actions">
			<span class="ajax-spinner"><img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="loader-image" /></span>
			<a class="button button-primary pp-activate-template" href="javascript:void(0)" data-template-type="{{{data.type}}}" data-template-cat="{{{data.category}}}"><?php esc_html_e('Activate', 'bb-powerpack'); ?></a>
			<a class="button button-secondary pp-deactivate-template" href="javascript:void(0)" data-template-type="{{{data.type}}}" data-template-cat="{{{data.category}}}"><?php esc_html_e('Deactivate', 'bb-powerpack'); ?></a>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-pp-template-filters">
	<div class="filter-sublinks" tabindex="0" aria-haspopup="true" aria-label="<?php _e( 'Filter Templates', 'bb-powerpack' ); ?>" aria-expanded="false">
		<label><?php esc_html_e( 'Filter:', 'bb-powerpack' ); ?></label>
		<div class="filter-dropdown">
			<span class="filter-dropdown-placeholder"><?php esc_html_e( 'All', 'bb-powerpack' ); ?></span>
			<ul class="filter-page-templates" role="menu">
				<li role="menuitem"><a href="javascript:void(0)" data-filter="all">all</a></li>
				<#
					var filters = {};
					data.templates.forEach(function(item) {
						if ( 'undefined' !== typeof item ) {
							if ( 'undefined' === typeof filters[ item.filter ] ) {
								filters[ item.filter ] = {
									key: item.filter,
									label: item.filter.replace( /-/g, ' ' )
								};
							}
						}
					});

					filters = _(filters).sortBy('key');

					_.each(filters, function(filter) {
						#>
						<li role="menuitem"><a href="javascript:void(0)" data-filter="{{{filter.key}}}">{{{filter.label}}}</a></li>
						<#
					});
				#>
			</ul>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-pp-template-actions">
	{{{data.filters}}}
	<div class="search-form">
		<label class="screen-reader-text" for="wp-filter-search-input"><?php esc_html_e( 'Search Templates', 'bb-powerpack' ); ?></label>
		<span class="hidden" aria-hidden="true" id="live-search-desc"><?php esc_html_e( 'The search results will be updated as you type.', 'bb-powerpack' ); ?></span>
		<input placeholder="Search templates..." type="text" aria-describedby="live-search-desc" id="wp-filter-search-input" class="wp-filter-search" />
		<?php echo file_get_contents( BB_POWERPACK_DIR . 'assets/images/search-icon.svg' ); ?>
	</div>
</script>

<script>
	var pp_templates = [];
	var pp_templates_type = '<?php echo $template_type; ?>';

	;(function($) {
		var ajaxSpinner = '<span class="ajax-spinner"><img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class="loader-image" /></span>';
		var activeFilter = 'all';
		var searchTerm = '';

		var request = function( data, callback ) {
			$.post( location.href, data, function( response ) {
				if ( 'function' === typeof callback ) {
					callback( response );
				}
			} );
		};

		var renderCount = function() {
			if ( pp_templates.length === 0 ) {
				return;
			}
			var $count = $('.filter-count > .count');
			if ( 'page' === pp_templates_type ) {
				$count.html( pp_templates.length );
			} else {
				var count = 0;
				pp_templates.forEach(function(item) {
					if ( 'undefined' !== typeof item && ! isNaN( parseInt( item.count ) ) ) {
						count += parseInt( item.count );
					}
				});
				$count.html( count );
			}
		};

		var getActions = function() {
			var filters = '';

			if ( 'page' === pp_templates_type ) {
				filters = wp.template( 'pp-template-filters' )({
					templates_type: 'page',
					templates: pp_templates
				});
			}

			var actions = wp.template( 'pp-template-actions' )({
				filters: filters
			});

			return actions;
		};

		var renderActions = function() {
			if ( pp_templates.length === 0 ) {
				return;
			}

			var actions = getActions();

			$('.pp-templates-actions').html( actions );
		};

		var getTemplates = function() {
			var data = [];
			var templates = '';

			pp_templates.forEach(function(item) {
				if ( 'undefined' !== typeof item && ( 'all' === activeFilter || activeFilter === item.filter ) ) {
					if ( '' !== searchTerm && item.title.toLowerCase().search( searchTerm ) !== -1 ) {
						data.push( item );
					} else {
						if ( '' === searchTerm ) {
							data.push( item );
						}
					}
				}
			});

			data.forEach(function(item) {
				if ( 'undefined' !== typeof item ) {
					var template = wp.template( 'pp-template' )( item );
					templates += template;
				}
			});

			return templates;
		}

		var renderTemplates = function() {
			var templates = getTemplates();

			$( '.pp-templates' ).html( templates );
		};

		var renderTemplatesView = function() {
			if ( pp_templates.length === 0 ) {
				return;
			}
			var view = wp.template( 'pp-templates-view' )({
				templates_type: pp_templates_type,
				actions: getActions(),
				templates: getTemplates(),
			});

			$('.pp-templates-grid').html( view );
		};

		var render = function() {
			request( {
				pp_action: 'get_templates_data',
				templates_type: '<?php echo $template_type; ?>',
			}, function( response ) {
				$('.pp-templates-grid').find('.ajax-spinner').hide();
				if ( response.success ) {
					pp_templates = response.data;
					if ( pp_templates.length === 0 ) {
						$('.pp-templates-grid').append('<?php _e( 'Something went wrong. Please reload the page.', 'bb-powerpack' ); ?>');
						return;
					}
					renderCount();
					renderTemplatesView();
				}
			} );
		};

		$(function($) {

			if ( history.pushState ) {
				if ( document.location.search.search( '&refresh' ) > -1 ) {
					var url = document.location.href.split('&refresh')[0];
					window.history.pushState( { path:url }, '', url );
				}
			}

			render();

			$('.pp-refresh-panel a').on('click', function(e) {
				e.preventDefault();

				var $this = $(this);

				$(this).addClass( 'is-syncing disabled' );

				request( {
					pp_action: 'sync_templates_data'
				}, function( response ) {
					$this.removeClass( 'is-syncing disabled' );
					if ( response.page.error && 'page' === pp_templates_type ) {
						alert( response.page.error );
						return;
					}
					if ( response.row.error && 'row' === pp_templates_type ) {
						alert( response.row.error );
						return;
					}
					$('.pp-templates-grid').html('');
					$('.pp-templates-grid').append( ajaxSpinner );
					activeFilter = 'all';
					searchTerm = '';
					render();
				} );
			});

			$('body').on('click keyup', '.pp-templates-actions .filter-sublinks', function(e) {
				var showFilters = 'click' === e.type || ( 'keyup' === e.type && ( 13 === e.keyCode || 13 === e.which ) )
				if ( ! showFilters ) {
					return;
				}
				$(this).find('.filter-page-templates').toggleClass( 'is-active' );
				if ( $(this).find('.filter-page-templates').hasClass( 'is-active' ) ) {
					$(this).attr('aria-expanded', 'true');
				} else {
					$(this).attr('aria-expanded', 'false');
				}
			});

			$('body').on('click', '.pp-templates-actions .filter-sublinks a', function(e) {

				e.preventDefault();

				$('.pp-template').removeClass('is-visible');

				activeFilter = $(this).data('filter');

				$(this).parents('.filter-dropdown').find('.filter-dropdown-placeholder').text( $(this).text() );

				$(this).parents('.filter-sublinks').find('li').removeClass('current');
				$(this).parent().addClass('current');

				$('.pp-template').remove();

				renderTemplates();
			});

			$(document).on( 'click', function(e) {
				if ( $(e.target).is( '.filter-sublinks' ) || $(e.target).closest( '.filter-sublinks' ).length > 0 ) {
					return;
				}
				$('.pp-templates-actions .filter-sublinks').attr( 'aria-expanded', 'false' );
				$('.pp-templates-actions .filter-page-templates').removeClass( 'is-active' );
			} );

			/* Search */
			$('body').on('keyup', '#wp-filter-search-input', function(e) {
				var keyCode = e.which || e.keyCode;
				if ( keyCode === 9 ) {
					return;
				}
				if ( $(this).val().length >= 3 ) {
					searchTerm = $(this).val().toLowerCase().trim();
					setTimeout(function() {
						$('.pp-template').remove();
						renderTemplates();
					}, 250);
				} else {
					if ( $(this).val().length === 0 ) {
						searchTerm = '';
						setTimeout(function() {
							$('.pp-template').remove();
							renderTemplates();
						}, 250);
					}
				}
			});

		});

		$(document).on('click', '.pp-activate-template', function(e) {

			e.preventDefault();
			e.stopPropagation();

			var button = jQuery(this);
			var parent = button.parents('.pp-template, .pp-template-overlay');
			parent.addClass('activating');

			console.log('Template is downloading...');

			jQuery.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type: 'post',
				data: {
					action: 'pp_activate_template',
					nonce: '<?php echo wp_create_nonce( 'pp-activate-template' ); ?>',
					pp_template_cat: button.data('template-cat'),
					pp_template_type: button.data('template-type'),
				},
				success: function(response) {
					if('activated' === response) {
						if(parent.hasClass('pp-template-overlay')) {
							location.reload();
						}
						parent.removeClass('activating').addClass('active');
						//parent.find('.pp-template-category span').html('Active: ');
						console.info('Template has downloaded and activated successfully.');
					} else {
						console.error(response);
					}
				}
			});
		});

		jQuery(document).on('click', '.pp-deactivate-template', function(e) {

			e.preventDefault();
			e.stopPropagation();

			var button = jQuery(this);
			var parent = button.parents('.pp-template, .pp-template-overlay');
			parent.addClass('activating').removeClass('active');

			console.log('Template is deactivating...');

			jQuery.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type: 'post',
				data: {
					action: 'pp_deactivate_template',
					nonce: '<?php echo wp_create_nonce( 'pp-deactivate-template' ); ?>',
					pp_template_cat: button.data('template-cat'),
					pp_template_type: button.data('template-type'),
				},
				success: function(response) {
					if('deactivated' === response) {
						if(parent.hasClass('pp-template-overlay')) {
							location.reload();
						}
						parent.removeClass('activating').removeClass('active');
						parent.find('.pp-template-category span').html('');
						console.info('Template has deactivated successfully.');
					} else {
						parent.addClass('active')
						console.error(response);
					}
				}
			});
		});

		jQuery(document).on('click keyup', '.pp-template.pp-preview-enabled', function(e) {
			var showPreview = 'click' === e.type || ( 'keyup' === e.type && ( 13 === e.keyCode || 13 === e.which ) )
			if ( ! showPreview ) {
				return;
			}

			e.preventDefault();

			var preview = jQuery(this).find('.pp-template-preview');
			var previewSrc = preview.data('preview-src');
			var templateCat = preview.data('template-cat');
			var activateLink = jQuery(this).find('.pp-activate-template').attr('href');
			var deactivateLink = jQuery(this).find('.pp-deactivate-template').attr('href');
			var scrollPos = jQuery(window).scrollTop();

			jQuery('.pp-template-overlay').show().find('.pp-template-preview-frame').attr('src', previewSrc);
			jQuery('.pp-template-overlay').find('.pp-activate-template').attr('data-template-cat', templateCat).attr('href', activateLink);
			jQuery('.pp-template-overlay').find('.pp-deactivate-template').attr('data-template-cat', templateCat).attr('href', deactivateLink);

			if(jQuery(this).hasClass('active')) {
				jQuery('.pp-template-overlay').addClass('active');
			}

			jQuery('.pp-template-overlay').find('button.close').on('click', function() {
				jQuery(window).scrollTop(scrollPos);
			});
		});

		jQuery('.pp-template-overlay .pp-template-header .close').on('click', function(e) {

			e.preventDefault();

			var overlay = jQuery(this).parents('.pp-template-overlay');
			overlay.fadeOut(100).find('.pp-template-preview-frame').attr('src', '');

			setTimeout(function() {
				overlay.removeClass('active');
			}, 100);

		});
	})(jQuery);
</script>
