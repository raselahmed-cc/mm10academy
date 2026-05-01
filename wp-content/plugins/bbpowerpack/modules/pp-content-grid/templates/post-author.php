<span class="pp-content-post-author pp-post-author">
<?php
	echo apply_filters( 'pp_cg_post_author_html', sprintf(
		_x( 'By %s', '%s stands for author name.', 'bb-powerpack' ),
		'<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '"><span>' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '</span></a>'
	), $post_id, $settings );
?>
</span>