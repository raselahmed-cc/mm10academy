<?php
$class_prefix = 'pp-grid-tile';

$author_html = apply_filters( 'pp_cg_post_author_html', sprintf(
	_x( 'By %s', '%s stands for author name.', 'bb-powerpack' ),
	'<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '"><span>' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '</span></a>'
), $post_id, $settings );

$link_target = isset( $settings->link_target_new ) && 'yes' === $settings->link_target_new ? ' target="_blank" rel="noopener bookmark"' : '';
?>

<?php PPContentGridModule::render_template( 'post-image' ); ?>

<div class="<?php echo $class_prefix; ?>-text">
	<div class="<?php echo $class_prefix; ?>-info">
		<?php
			if ( $settings->show_categories == 'yes' && ! empty( $terms_list ) ) {
				$terms = wp_get_post_terms( get_the_ID(), $settings->post_taxonomies );
				$show_terms = array();
				foreach ( $terms as $term ) {
					$show_terms[ get_term_link( $term ) ] = $term->name;
				}
		?>
			<div class="<?php echo $class_prefix; ?>-category pp-content-category-list pp-post-meta">
				<span class="pp-category-<?php echo strtolower(implode( '-', $show_terms )); ?>">
				<?php $term_count = 1; foreach ( $show_terms as $link => $term ) { ?>
					<a href="<?php echo $link; ?>" class="pp-post-meta-term"><?php echo $term; ?></a>
					<?php if ( $term_count != count( $show_terms ) ) {
						echo $settings->meta_separator;
					} ?>
				<?php $term_count++; } ?>
				</span>
			</div>
		<?php } ?>
		<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-content-grid-title pp-post-title" itemprop="headline">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"<?php echo $link_target; ?>><?php the_title(); ?></a>
		</<?php echo esc_attr( $settings->title_tag ); ?>>

		<?php if ( in_array( 'tribe_events', (array) $post_type ) && ( class_exists( 'Tribe__Events__Main' ) && class_exists( 'FLThemeBuilderLoader' ) ) ) { ?>
			<?php PPContentGridModule::render_template( 'event-content' ); ?>
		<?php } ?>
	</div>

	<?php if ( $settings->show_author == 'yes' || $settings->show_date == 'yes' ) : ?>
	<div class="<?php echo $class_prefix; ?>-meta">
		<?php if ( $settings->show_author == 'yes' ) : ?>
			<span class="<?php echo $class_prefix; ?>-author">
			<?php echo $author_html; ?>
			</span>
		<?php endif; ?>
		<?php if ( $settings->show_date == 'yes' ) : ?>
			<?php if ( $settings->show_author == 'yes' ) : ?>
				<span class="pp-meta-separator"> <?php echo $settings->meta_separator; ?> </span>
			<?php endif; ?>
			<span class="<?php echo $class_prefix; ?>-date">
				<?php if ( pp_is_tribe_events_post( $post_id ) && function_exists( 'tribe_get_start_date' ) ) { ?>
					<?php echo tribe_get_start_date( null, false, $date_format ); ?>
				<?php } else { ?>
					<?php FLBuilderLoop::post_date(); ?>
				<?php } ?>
			</span>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>