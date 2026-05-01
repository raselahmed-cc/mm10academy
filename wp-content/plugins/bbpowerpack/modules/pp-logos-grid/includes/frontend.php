<?php
$logos = $module->get_images();
$styles = [];
$style_attr = '';

if ( 'ticker' == $settings->logos_layout ) {
	$styles['--ticker-animation'] = 'prev' == $settings->ticker_direction ? 'pp-ticker' : 'pp-ticker-reverse';
	$styles['--ticker-speed'] = '' !== $settings->ticker_speed ? floatval( $settings->ticker_speed ) * 100 : 100;
	$styles['--ticker-speed'] .= 's';
}

foreach ( $styles as $prop => $value ) {
	$style_attr .= "$prop:$value;";
}
?>
<div class="pp-logos-content is-<?php echo esc_attr( $settings->logos_layout ); ?>">
    <div class="pp-logos-wrapper pp-logos-<?php echo esc_attr( $settings->logos_layout ); ?>" style="<?php echo $style_attr; ?>">
		<?php foreach ( $logos as $index => $logo ) { ?>
		<div class="pp-logo pp-logo-<?php echo $index; ?>">
        	<?php $module->render_image_link_open( $logo['link'] ); ?>
            <div class="pp-logo-inner">
                <div class="pp-logo-inner-wrap">
					<div class="logo-image-wrapper">
						<?php $module->render_image( $logo ); ?>
					</div>
                    <?php if ( ! empty( $logo['title'] ) ) { ?>
                        <div class="title-wrapper">
                            <p class="logo-title"><?php echo $logo['title']; ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php $module->render_image_link_close( $logo['link'] ); ?>
		</div>
		<?php } ?>
	</div>
	<?php if ( 'carousel' === $settings->logos_layout ) { ?>
		<button class="logo-slider-nav logo-slider-next"><?php pp_next_icon_svg( esc_html__( 'Next', 'bb-powerpack' ) ); ?></button>
		<button class="logo-slider-nav logo-slider-prev"><?php pp_prev_icon_svg( esc_html__( 'Previous', 'bb-powerpack' ) ); ?></button>
	<?php } ?>
</div>
