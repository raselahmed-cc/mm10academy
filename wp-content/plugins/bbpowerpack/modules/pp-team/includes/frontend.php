<?php
$photo    = $module->get_data();
$classes  = $module->get_classes();
$src      = '';
if ( '' != $settings->member_image ) {
	$src  = $module->get_src();
}
$link     = $module->get_link();
$alt      = $module->get_alt();
$attrs    = $module->get_attributes();
$filetype = pathinfo( $src, PATHINFO_EXTENSION );

$icon_prefix       = 'fa';
$email_icon_prefix = 'fa';
$enabled_icons     = FLBuilderModel::get_enabled_icons();

if ( in_array( 'font-awesome-5-brands', $enabled_icons ) ) {
	$icon_prefix = 'fab';
}
if ( in_array( 'font-awesome-5-solid', $enabled_icons ) ) {
	$email_icon_prefix = 'fas';
}

?>
<div class="pp-member-wrapper">
    <?php if ( '' != $src ) { ?>
        <div class="pp-member-image pp-image-crop-<?php echo esc_attr( $settings->member_image_crop ); ?>">
            <?php if ( $settings->link && $settings->link_target ) { ?>
            <a href="<?php echo esc_url( do_shortcode( $settings->link ) ); ?>" target="<?php echo esc_attr( $settings->link_target ); ?>">
            <?php } ?>
            <img class="<?php echo $classes; ?>" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" itemprop="image" <?php echo $attrs; ?> />
            <?php if ( $settings->link && $settings->link_target ) { ?>
            </a>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="pp-member-content">
		<?php if ( $settings->content_position == 'hover' || $settings->content_position == 'over' ) { ?>
			<div class="pp-member-content-inner-wrapper">
				<div class="pp-member-content-inner">
		<?php } ?>
        <?php if ( $settings->link && $settings->link_target ) { ?>
        <a href="<?php echo esc_url( do_shortcode( $settings->link ) ); ?>" target="<?php echo esc_attr( $settings->link_target ); ?>">
        <?php } ?>
            <<?php echo esc_attr( $settings->title_tag ); ?> class="pp-member-name"><?php echo $settings->member_name; ?></<?php echo esc_attr( $settings->title_tag ); ?>>
        <?php if ( $settings->link && $settings->link_target ) { ?>
        </a>
        <?php } ?>
        <?php if ( $settings->separator_position == 'below_title' && $settings->separator_display == 'yes' ) { ?>
            <div class="pp-member-separator"></div>
        <?php } ?>
		<?php if ( $settings->member_designation ) { ?>
        	<div class="pp-member-designation"><?php echo do_shortcode( $settings->member_designation ); ?></div>
		<?php } ?>
        <?php if ( $settings->separator_position == 'below_designation' && $settings->separator_display == 'yes' ) { ?>
            <div class="pp-member-separator"></div>
        <?php } ?>
		<?php if ( $settings->member_description ) { ?>
        	<div class="pp-member-description"><?php echo do_shortcode( $settings->member_description ); ?></div>
		<?php } ?>
		<?php if ( $module->has_social_links() ) { ?>
        <div class="pp-member-social-profiles">
            <ul>
				<?php
				$icons = PPTeamModule::get_icon_sources();
				foreach ( $icons as $icon_key => $icon ) {
					$setting_key = 'email' === $icon_key ? 'email' : $icon_key . '_url';
					if ( isset( $settings->{$setting_key} ) && ! empty( $settings->{$setting_key} ) ) {
						$url = 'email' == $setting_key ? 'mailto:' . sanitize_email( do_shortcode( $settings->{$setting_key} ) ) : esc_url( do_shortcode( $settings->{$setting_key} ) );
						?>
						<li class="pp-social-<?php echo esc_attr( $icon_key ); ?>">
							<a href="<?php echo $url; ?>" target="<?php echo esc_attr( $settings->social_link_target ); ?>" aria-label="<?php echo esc_attr( $icon['label'] ); ?>">
								<?php
								if ( isset( $icon['svg'] ) && ! empty( $icon['svg'] ) ) {
									// Check if it's a valid SVG.
									if ( preg_match('/<svg\b[^>]*>(.*?)<\/svg>/is', $icon['svg'], $matches ) ) {
										echo $matches[0];
									}
								} else { ?>
								<span class="<?php echo esc_attr( $icon['icon'] ); ?>"></span>
								<?php } ?>
							</a>
						</li>
						<?php
					}
				}
				?>
            </ul>
        </div>
		<?php } ?>
		<?php if ( $settings->content_position == 'hover' || $settings->content_position == 'over' ) { ?>
				</div>
			</div>
		<?php } ?>
    </div>
</div>
