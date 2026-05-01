<div class="pp-add-to-cart pp-post-link">
    <?php if ( in_array( 'product', (array) $post_type ) ) {
		global $product;
        // Updated function woocommerce_get_template to wc_get_template
        // @since 1.2.7
        if( function_exists( 'wc_get_template' ) && is_object( $product ) ) {
            wc_get_template( 'loop/add-to-cart.php', array( 'product' => $product ) );
        }
    } ?>
    <?php if ( in_array( 'download', (array) $post_type ) && class_exists( 'Easy_Digital_Downloads' ) ) {
        if ( ! edd_has_variable_prices( get_the_ID() ) ) { ?>
            <?php echo edd_get_purchase_link( get_the_ID(), 'Add to Cart', 'button' ); ?>
        <?php }
    } ?>
</div>
