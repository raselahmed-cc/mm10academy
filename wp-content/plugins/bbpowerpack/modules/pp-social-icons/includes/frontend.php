<?php
$icon_prefix   = $email_icon_prefix = $rss_icon_prefix = 'fa';
$enabled_icons = $module->_enabled_icons;

if ( in_array( 'font-awesome-5-brands', $enabled_icons ) ) {
	$icon_prefix = 'fab';
}
if ( in_array( 'font-awesome-5-solid', $enabled_icons ) ) {
	$rss_icon_prefix   = 'fas';
	$email_icon_prefix = 'fas';
}

$labels = $module->get_labels();
$empty_link_hide_icon = apply_filters( 'pp_social_icons_empty_link_hide_icon', true, $settings );
?>

<div class="pp-social-icons pp-social-icons-<?php echo esc_attr( $settings->direction ); ?>">
<?php
foreach ( $settings->icons as $icon ) {

	if ( ! is_object( $icon ) ) {
		continue;
	}
	if ( empty( $icon->link ) && $empty_link_hide_icon ) {
		continue;
	}

	$title = '';

	if ( isset( $labels[ $icon->icon ] ) ) {
		$title = $labels[ $icon->icon ];
	}
	if ( 'custom' == $icon->icon && isset( $icon->icon_custom_title ) ) {
		$title = $icon->icon_custom_title;
	}
	$link_target   = isset( $icon->link_target ) ? esc_attr( $icon->link_target ) : '_blank';
	$link_nofollow = isset( $icon->link_nofollow ) ? esc_attr( $icon->link_nofollow ) : 'no';
	?>
	<span class="pp-social-icon" itemscope itemtype="https://schema.org/Organization">
		<link itemprop="url" href="<?php echo site_url(); ?>">
		<a itemprop="sameAs" href="<?php echo esc_url( do_shortcode( $icon->link ) ); ?>" target="<?php echo $link_target; ?>"<?php echo isset( $labels[ $icon->icon ] ) ? ' title="' . strip_tags( $title ) . '" aria-label="' . strip_tags( $title ) . '"' : '' ; ?> role="button"<?php echo $module->get_rel( $link_target, $link_nofollow ); ?>>
			<?php if ( $icon->icon == 'custom' ) { ?>
				<i class="<?php echo esc_attr( $icon->icon_custom ); ?>"></i>
			<?php } elseif ( 'fa-envelope' == $icon->icon ) { ?>
				<i class="<?php echo $email_icon_prefix; ?> <?php echo $icon->icon; ?>"></i>
			<?php } elseif ( 'fa-twitter' == $icon->icon ) { ?>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"></path></svg>
				<!--<i class="fab pp-x-icon">𝕏</i>-->
			<?php } elseif ( 'fa-rss' == $icon->icon ) { ?>
				<i class="<?php echo $rss_icon_prefix; ?> <?php echo $icon->icon; ?>"></i>
			<?php } else { ?>
				<i class="<?php echo $icon_prefix; ?> <?php echo $icon->icon; ?>"></i>
			<?php } ?>
		</a>
	</span>
	<?php
}
?>
</div>
