<?php

FLBuilderModel::default_settings($settings, array(
	'post_type' 			=> 'post',
	'order_by'  			=> 'date',
	'order'     			=> 'DESC',
	'offset'    			=> 0,
	'no_results_message'	=> __('No result found.', 'bb-powerpack'),
	'users'     			=> '',
	'show_author'			=> '1',
	'show_date'				=> '1',
	'date_format'			=> 'default',
	'show_post_taxonomies'	=> '1',
	'post_taxonomies'		=> 'category',
	'meta_separator'		=> ' / ',
	'title_margin'			=> array(
		'top'					=> '0',
		'bottom'				=> '0'
	)
));

$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $image_size );
$featured_image = '';
$class_prefix = 'pp-post-tile';
$show_date_author_all_tiles = isset( $settings->show_date_author_all_tiles ) && $settings->show_date_author_all_tiles ? true : false;
$date_author = $show_date_author_all_tiles ? true : false;

if ( ! $show_date_author_all_tiles && $count == 1 ) {
	$date_author = true;
}


if ( ! is_array( $image ) ) {
	if ( isset( $settings->fallback_image ) ) {
		if ( 'placeholder' == $settings->fallback_image ) {
			$featured_image = BB_POWERPACK_URL . 'assets/images/placeholder-600.jpg';
		}
		if ( 'custom' == $settings->fallback_image ) {
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $settings->fallback_image_custom ), $image_size );
			if ( ! $featured_image || empty( $featured_image ) ) {
				$featured_image = esc_url( $settings->fallback_image_custom_src );
			}
		}
	}
} else {
	$featured_image = $image[0];
}
?>
<div class="<?php echo $class_prefix; ?>-post <?php echo $class_prefix; ?>-post-<?php echo $count; ?><?php echo $module->get_post_class($count, $settings->layout); ?>"<?php BB_PowerPack_Post_Helper::print_schema( ' itemscope itemtype="' . PPContentTilesModule::schema_itemtype() . '"' ); ?>>

	<?php PPContentTilesModule::schema_meta(); ?>

	<?php if ( ! empty( $featured_image ) ) : ?>
		<div class="<?php echo $class_prefix; ?>-image" style="background-image: url(<?php echo $featured_image; ?>)">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" aria-label="<?php echo get_the_title(); ?>"></a>
		</div>
	<?php endif; ?>

	<div class="<?php echo $class_prefix; ?>-text">

		<div class="<?php echo $class_prefix; ?>-info">
			<?php
				if ( $settings->show_post_taxonomies == '1' && $settings->post_taxonomies != 'none' ) {
					$terms = wp_get_post_terms( get_the_ID(), $settings->post_taxonomies );
					$show_terms = array();
					foreach ( $terms as $term ) {
						$show_terms[] = $term->name;
					}
			?>
				<div class="<?php echo $class_prefix; ?>-category"><span class="pp-category-<?php echo strtolower(implode( '-', $show_terms )); ?>"><?php echo implode( $settings->meta_separator, $show_terms ); ?></span></div>
			<?php } ?>
			<h3 class="<?php echo $class_prefix; ?>-title" itemprop="headline">
				<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
			</h3>

			<?php do_action( 'pp_tiles_after_title', $settings, $count ); ?>
		</div>

		<?php if ( $settings->show_author || $settings->show_date ) : ?>
		<div class="<?php echo $class_prefix; ?>-meta">
			<?php if ( $settings->show_author && $date_author ) : ?>
				<span class="<?php echo $class_prefix; ?>-author">
				<?php

				printf(
					_x( '%s', '%s stands for author name.', 'bb-powerpack' ),
					'<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '"><span>' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '</span></a>'
				);

				?>
				</span>
			<?php endif; ?>
			<?php if ( $settings->show_date && $date_author ) : ?>
				<?php if ( $settings->show_author ) : ?>
					<span class="pp-meta-separator"> <?php echo $settings->meta_separator; ?> </span>
				<?php endif; ?>
				<span class="<?php echo $class_prefix; ?>-date">
					<?php FLBuilderLoop::post_date( $settings->date_format ); ?>
				</span>
			<?php endif; ?>

			<?php do_action( 'pp_tiles_after_meta', $settings, $count ); ?>
		</div>
		<?php endif; ?>

	</div>

</div>
