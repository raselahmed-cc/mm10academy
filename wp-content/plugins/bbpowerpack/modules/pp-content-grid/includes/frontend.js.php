
<?php
$columns     = $module->get_post_columns( $settings );
$speed       = ! empty( $settings->transition_speed ) ? $settings->transition_speed * 1000 : 3000;
$slide_speed = ( isset( $settings->slides_speed ) && ! empty( $settings->slides_speed ) ) ? $settings->slides_speed * 1000 : 1000;

// TODO.
$slide_spacing_xl = isset( $settings->slides_spacing ) && '' !== $settings->slides_spacing ? absint( $settings->slides_spacing ) : 0;
$slide_spacing_lg = isset( $settings->slides_spacing_large ) && '' !== $settings->slides_spacing_large ? absint( $settings->slides_spacing_large ) : $slide_spacing_xl;
$slide_spacing_md = isset( $settings->slides_spacing_medium ) && '' !== $settings->slides_spacing_medium ? absint( $settings->slides_spacing_medium ) : $slide_spacing_lg;
$slide_spacing_sm = isset( $settings->slides_spacing_responsive ) && '' !== $settings->slides_spacing_responsive ? absint( $settings->slides_spacing_responsive ) : $slide_spacing_md;

$breakpoints = array(
	'large'	 => empty( $global_settings->large_breakpoint ) ? '1200' : $global_settings->large_breakpoint,
	'medium' => empty( $global_settings->medium_breakpoint ) ? '980' : $global_settings->medium_breakpoint,
	'small'	 => empty( $global_settings->responsive_breakpoint ) ? '768' : $global_settings->responsive_breakpoint,
);
$scrollTo  = apply_filters( 'pp_cg_scroll_to_grid_on_filter', true );
$js_fields = $module->get_fields_for_js( $module->form, $settings );

$nav_arrows	= apply_filters( 'pp_cg_carousel_nav_arrows', array(
	'left'  => pp_prev_icon_svg( __( 'Previous', 'bb-powerpack' ), false ),
	'right' => pp_next_icon_svg( __( 'Next', 'bb-powerpack' ), false ),
), $settings );
?>

var ppcg_<?php echo $id; ?> = '';

;(function($) {
	var left_arrow_svg  = '<?php echo $nav_arrows['left']; ?>';
	var right_arrow_svg = '<?php echo $nav_arrows['right']; ?>';

	var PPContentGridOptions = {
		id: '<?php echo $id ?>',
		layout: '<?php echo $settings->layout; ?>',
		style: '<?php echo $settings->post_grid_style_select; ?>',
		scrollTo: <?php echo $scrollTo ? 'true' : 'false'; ?>,
		fields: <?php echo json_encode( $js_fields ); ?>,
		pagination: '<?php echo $settings->pagination; ?>',
		postSpacing: '<?php echo $settings->post_spacing; ?>',
		postColumns: <?php echo json_encode( $columns ); ?>,
		breakpoints: <?php echo json_encode( $breakpoints ); ?>,
		matchHeight: '<?php echo $settings->match_height; ?>',
		<?php echo ( isset( $settings->post_grid_filters_display ) && 'yes' == $settings->post_grid_filters_display ) ? 'filters: true' : 'filters: false'; ?>,
		defaultFilter: '<?php echo isset( $settings->post_grid_filters_default ) && ! empty( $settings->post_grid_filters_default ) ? $settings->post_grid_filters_default : ''; ?>',
		<?php if ( isset( $settings->post_grid_filters ) && 'none' != $settings->post_grid_filters ) { ?>
			filterTax: '<?php echo $settings->post_grid_filters; ?>',
		<?php } ?>
		filterType: '<?php echo isset( $settings->post_grid_filters_type ) ? $settings->post_grid_filters_type : 'static'; ?>',
		<?php if ( 'grid' == $settings->layout && 'no' == $settings->match_height && 'style-9' != $settings->post_grid_style_select ) { ?>
		masonry: 'yes',
		<?php } ?>
		<?php if ( 'carousel' == $settings->layout ) { ?>
			carousel: {
				items: <?php echo $columns['xl']; ?>,
				responsive: {
					0: {
						items: <?php echo $columns['sm']; ?>,
						margin: <?php echo $slide_spacing_sm; ?>,
					},
					<?php echo $breakpoints['large'] + 1; ?>: {
						items: <?php echo $columns['xl']; ?>,
						margin: <?php echo $slide_spacing_xl; ?>,
					},
					<?php echo $breakpoints['medium'] + 1; ?>: {
						items: <?php echo $columns['lg']; ?>,
						margin: <?php echo $slide_spacing_lg; ?>,
					},
					<?php echo $breakpoints['small'] + 1; ?>: {
						items: <?php echo $columns['md']; ?>,
						margin: <?php echo $slide_spacing_md; ?>,
					},
				},
			<?php if ( isset( $settings->slide_by ) && absint( $settings->slide_by ) ) : ?>
				slideBy: <?php echo absint( $settings->slide_by ); ?>,
			<?php endif; ?>
			<?php if ( isset( $settings->slider_pagination ) && 'no' === $settings->slider_pagination ) : ?>
				dots: false,
			<?php endif; ?>
			<?php if ( isset( $settings->auto_play ) ) : ?>
				<?php echo 'yes' === $settings->auto_play && ! FLBuilderModel::is_builder_active() ? 'autoplay: true' : 'autoplay: false'; ?>,
				autoplayTimeout: <?php echo $speed ?>,
				autoplaySpeed: <?php echo $slide_speed ?>,
				<?php echo 'yes' === $settings->stop_on_hover ? 'autoplayHoverPause: true' : 'autoplayHoverPause: false'; ?>,
			<?php endif; ?>
				navSpeed: <?php echo $slide_speed ?>,
				dotsSpeed: <?php echo $slide_speed ?>,
				<?php echo 'yes' === $settings->slider_navigation ? 'nav: true' : 'nav: false'; ?>,
				<?php echo 'yes' === $settings->lazy_load ? 'lazyLoad: true' : 'lazyLoad: false'; ?>,
				navText : [left_arrow_svg, right_arrow_svg],
				navContainer: '.fl-node-<?php echo $id; ?> .pp-carousel-nav',
				navElement:'button type="button"',
				responsiveRefreshRate: 200,
				responsiveBaseWidth: window,
				loop: <?php echo isset( $settings->slide_loop ) && 'yes' === $settings->slide_loop ? 'true' : 'false'; ?>,
				center: <?php echo ( isset( $settings->slides_center_align ) && 'yes' == $settings->slides_center_align ) ? 'true' : 'false'; ?>,
				autoHeight: <?php echo isset( $settings->auto_height ) && 'yes' === $settings->auto_height ? 'true' : 'false'; ?>,
				URLhashListener: <?php echo isset( $settings->url_hash_listener ) && 'yes' === $settings->url_hash_listener ? 'true' : 'false'; ?>
			}
			<?php } // End if(). ?>
	};

	PPContentGridOptions = $.extend( {}, PPContentGridOptions, bb_powerpack.conditionals );

	$(function() {
		ppcg_<?php echo $id; ?> = new PPContentGrid( PPContentGridOptions );
	});
	
	// expandable row fix.
	var state = 0;
	$(document).on('pp_expandable_row_toggle', function(e, selector) {
		if ( selector.is('.pp-er-open') && state === 0 && selector.parent().find( '.pp-content-post-grid' ).length > 0 ) {
			if ( 'undefined' !== typeof $.fn.isotope && selector.parent().find('.pp-content-post-grid').data('isotope') ) {
				selector.parent().find('.pp-content-post-grid').isotope('layout');
			}
			state = 1;
		}
	});

	// Tabs and Content Grid fix
	$(document).on('pp-tabs-switched', function(e, selector) {
		if ( selector.find('.pp-content-post-grid').length > 0 ) {
			var postsWrapper = selector.find('.pp-content-post-grid');
			ppcg_<?php echo $id; ?>._gridLayoutMatchHeight();
			if ( 'undefined' !== typeof $.fn.isotope && postsWrapper.data('isotope') ) {
				setTimeout(function() {
					postsWrapper.isotope('layout');
				}, 500);
			}
		}
	});

})(jQuery);
