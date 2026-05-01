<?php
$remove_child = false;
$terms_separator = isset( $settings->terms_separator ) ? $settings->terms_separator : ' / ';
if ( $remove_child ) : // use this code to display only parent terms. ?>
<div class="pp-content-category-list pp-post-meta">
    <?php $terms_html = array();
	foreach ( $terms_list as $term ) :
		if ( isset( $term->parent ) && $term->parent > 0 ) {
			continue;
		}
		$terms_html[] = '<a href="' . get_term_link( $term ) . '" class="pp-post-meta-term term-' . $term->slug . '">' . $term->name . '</a>';
	endforeach;
    ?>
    <?php echo implode( $terms_separator, $terms_html ); ?>
</div>
<?php else: ?>
<?php if ( ! isset( $include_wrapper ) || $include_wrapper ) { ?>
<div class="pp-content-category-list pp-post-meta">
<?php } ?>
    <?php $i = 1;
	foreach ( $terms_list as $term ) :
		$class = ( isset( $term->parent ) && $term->parent > 0 ) ? ' child-term' : ' parent-term';
		?>
		<?php if ( apply_filters( 'pp_cg_post_term_render_link', true, $term, $post_id, $settings ) ) { ?>
			<a href="<?php echo get_term_link( $term ); ?>" class="pp-post-meta-term term-<?php echo $term->slug; ?><?php echo $class; ?>"><?php echo $term->name; ?></a>
		<?php } else { ?>
			<span class="pp-post-meta-term term-<?php echo $term->slug; ?><?php echo $class; ?>"><?php echo $term->name; ?></span>
		<?php } ?>
		<?php if ( $i != count( $terms_list ) ) { ?>
			<span class="pp-post-meta-separator"><?php echo $terms_separator; ?></span>
		<?php } ?>
    <?php $i++; endforeach; ?>
<?php if ( ! isset( $include_wrapper ) || $include_wrapper ) { ?>
</div>
<?php } ?>
<?php endif; ?>