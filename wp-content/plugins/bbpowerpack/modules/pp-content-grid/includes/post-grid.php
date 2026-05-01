<?php
if ( in_array( 'product', (array) $post_type ) ) {
	global $post, $product;
}

$alternate_class = '';

if ( $count % 2 === 0 ) {
	$alternate_class = ' pp-post-2n';
}

$post_classes = join( ' ', get_post_class() );
$html_classes = "pp-content-post pp-content-grid-post$alternate_class pp-grid-{$settings->post_grid_style_select}";
$html_classes .= " $post_classes";

$is_product      = in_array( 'product', (array) $post_type ) || in_array( 'download', (array) $post_type );
$is_wc_product   = in_array( 'product', (array) $post_type ) && class_exists( 'WooCommerce' );
$is_edd_download = in_array( 'download', (array) $post_type ) && class_exists( 'Easy_Digital_Downloads' );

do_action( 'pp_cg_before_render_post', $settings, $count, $html_classes );
?>
<div class="<?php echo $html_classes; ?>"<?php BB_PowerPack_Post_Helper::print_schema( ' itemscope itemtype="' . PPContentGridModule::schema_itemtype() . '"' ); ?> data-id="<?php echo $post_id; ?>">

	<?php PPContentGridModule::schema_meta(); ?>

	<?php if ( 'style-9' == $settings->post_grid_style_select ) {
		PPContentGridModule::render_template( 'post-tile' );
	} else { ?>

		<?php if ( $settings->more_link_type == 'box' ) { ?>
			<a class="pp-post-link" href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>"<?php echo $link_target; ?>></a>
		<?php } ?>

		<?php if ( 'style-1' == $settings->post_grid_style_select ) { ?>

			<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-content-grid-title pp-post-title" itemprop="headline">
				<?php if ( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
					<a href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>"<?php echo $link_target; ?>>
				<?php } ?>
						<?php the_title(); ?>
				<?php if ( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
					</a>
				<?php } ?>
			</<?php echo esc_attr( $settings->title_tag ); ?>>

			<div class="pp-content-post-meta pp-post-meta">
				<?php if ( $settings->show_author == 'yes' ) : ?>
					<?php
					// Show post author.
					PPContentGridModule::render_template( 'post-author' );
					?>
				<?php endif; ?>
				<?php if ( $settings->show_date == 'yes' ) : ?>
					<?php if ( $settings->show_author == 'yes' ) : ?>
						<span> <?php echo $settings->meta_separator; ?> </span>
					<?php endif; ?>
					<?php
					// Show post date.
					PPContentGridModule::render_template( 'post-date' );
					?>
				<?php endif; ?>

			</div>

		<?php } ?>

		<?php if ( in_array( $settings->post_grid_style_select, array( 'default', 'style-2', 'style-3', 'style-5', 'style-8' ) ) ) {
			if ( isset( $settings->alternate_content ) && 'yes' === $settings->alternate_content ) { ?>
			<div class="pp-content-alternate-wrap">
		<?php } } ?>

		<?php if ( $settings->show_image == 'yes' ) : // Featured Image ?>
			<?php PPContentGridModule::render_template( 'post-image' ); ?>
		<?php endif; ?>

		<div class="pp-content-grid-inner pp-content-body clearfix">
			<?php do_action( 'pp_cg_post_body_open', $post_id, $settings ); ?>

			<?php if ( 'style-5' == $settings->post_grid_style_select && 'yes' == $settings->show_date ) : ?>
			<div class="pp-content-post-date pp-post-meta">
				<?php if ( pp_is_tribe_events_post( $post_id ) && function_exists( 'tribe_get_start_date' ) ) { ?>
					<span class="pp-post-day"><?php echo tribe_get_start_date( null, false, 'd' ); ?></span>
					<span class="pp-post-month"><?php echo tribe_get_start_date( null, false, 'M' ); ?></span>
				<?php } else { ?>
					<span class="pp-post-day"><?php echo get_the_date('d'); ?></span>
					<span class="pp-post-month"><?php echo get_the_date('M'); ?></span>
				<?php } ?>
			</div>
			<?php endif; ?>

			<div class="pp-content-post-data">
				<?php if ( 'style-1' != $settings->post_grid_style_select && 'style-4' != $settings->post_grid_style_select ) { ?>
					<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-content-grid-title pp-post-title" itemprop="headline">
						<?php if ( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
							<a href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>"<?php echo $link_target; ?>>
						<?php } ?>
								<?php the_title(); ?>
						<?php if ( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
							</a>
						<?php } ?>
					</<?php echo esc_attr( $settings->title_tag ); ?>>
					<?php if ( 'style-2' == $settings->post_grid_style_select ) { ?>
						<span class="pp-post-title-divider"></span>
					<?php } ?>
				<?php } ?>

				<?php if ( ( $settings->show_author == 'yes' || $settings->show_date == 'yes' || $settings->show_categories == 'yes' )
						&& ( 'style-1' != $settings->post_grid_style_select ) ) : ?>
				<div class="pp-content-post-meta pp-post-meta">
					<?php if ( $settings->show_author == 'yes' ) : ?>
						<?php
						// Show post author.
						PPContentGridModule::render_template( 'post-author' );
						?>
					<?php endif; ?>

					<?php if ( $settings->show_date == 'yes' && 'style-5' != $settings->post_grid_style_select && 'style-6' != $settings->post_grid_style_select ) : ?>
						<?php if ( $settings->show_author == 'yes' ) : ?>
							<span> <?php echo $settings->meta_separator; ?> </span>
						<?php endif; ?>
						<?php
						// Show post date.
						PPContentGridModule::render_template( 'post-date' );
						?>
					<?php endif; ?>

					<?php if ( 'style-6' == $settings->post_grid_style_select || 'style-5' == $settings->post_grid_style_select ) : ?>
						<?php if ( $settings->show_author == 'yes' && $settings->show_categories == 'yes' && ! empty( $terms_list ) ) : ?>
							<span> <?php echo $settings->meta_separator; ?> </span>
						<?php endif; ?>
						<?php if ( $settings->show_categories == 'yes' ) { ?>
							<?php PPContentGridModule::render_template( 'post-terms', array( 'include_wrapper' => false ) ); ?>
						<?php } ?>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ( $is_wc_product && $settings->product_rating == 'yes' ) { ?>
					<?php PPContentGridModule::render_template( 'product-rating' ); ?>
				<?php } ?>

				<?php if ( in_array( 'tribe_events', (array) $post_type ) && ( class_exists( 'Tribe__Events__Main' ) && class_exists( 'FLThemeBuilderLoader' ) ) ) { ?>
					<?php PPContentGridModule::render_template( 'event-content' ); ?>
				<?php } ?>

				<?php do_action( 'pp_cg_before_post_content', $post_id, $settings ); ?>

				<?php if ( $settings->show_content == 'yes' || $settings->show_content == 'custom' ) : ?>
					<?php PPContentGridModule::render_template( 'post-content' ); ?>
				<?php endif; ?>

				<?php do_action( 'pp_cg_after_post_content', $post_id, $settings ); ?>

				<?php if ( $settings->more_link_text != '' && $settings->more_link_type == 'button' && ! $is_wc_product && ! $is_edd_download ) :
					PPContentGridModule::render_template( 'custom-button' );
				endif; ?>

				<?php if ( $is_wc_product || $is_edd_download ) {
					if ( 'yes' == $settings->product_price ) {
						PPContentGridModule::render_template( 'product-price' );
					}
					if ( 'yes' == $settings->product_button ) {
						PPContentGridModule::render_template( 'cart-button' );
					} else {
						if ( $settings->more_link_text != '' && $settings->more_link_type == 'button' ) {
							PPContentGridModule::render_template( 'custom-button' );
						}
					}
				} ?>

			</div>
			<?php if ( ( $settings->show_categories == 'yes' && ! empty( $terms_list ) ) && ( 'style-3' != $settings->post_grid_style_select && 'style-5' != $settings->post_grid_style_select && 'style-6' != $settings->post_grid_style_select ) ) : ?>
				<?php PPContentGridModule::render_template( 'post-terms' ); ?>
			<?php endif; ?>

			<?php do_action( 'pp_cg_post_body_close', $post_id, $settings ); ?>
		</div>

		<?php if ( in_array( $settings->post_grid_style_select, array( 'default', 'style-2', 'style-3', 'style-5', 'style-8' ) ) ) {
			if ( isset( $settings->alternate_content ) && 'yes' === $settings->alternate_content ) { ?>
			</div>
		<?php } } ?>
	<?php } ?>
</div>
