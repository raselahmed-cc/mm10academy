<?php
	$menu_type_class = 'pp-menu-default';
	$menu_nav_class = 'pp-menu-nav';

   	if ( $settings->mobile_menu_type !== 'default' ) {
		$menu_type_class = esc_attr( $settings->mobile_menu_type );

		if ( 'off-canvas' === $settings->mobile_menu_type ) {
			$menu_nav_class .= ' pp-off-canvas-menu pp-menu-' . esc_attr( $settings->offcanvas_direction );
		}
		if ( 'full-screen' === $settings->mobile_menu_type ) {
			$menu_nav_class .= ' pp-menu-overlay pp-overlay-' . esc_attr( $settings->full_screen_effects );
		}

		$module->render_toggle_button();
   	}
?>
<div class="pp-advanced-menu<?php if ( $settings->collapse ) echo ' pp-advanced-menu-accordion-collapse'; ?> <?php echo $menu_type_class; ?> pp-menu-align-<?php echo esc_attr( $settings->alignment ); ?> pp-menu-position-<?php echo esc_attr( $settings->menu_position ); ?>">
   	<?php
   	if ( $settings->mobile_menu_type === 'default' ) {
		$module->render_toggle_button();
   	}
   	?>
   	<div class="pp-clear"></div>
	<nav class="<?php echo $menu_nav_class; ?>" aria-label="<?php echo $module->get_menu_label(); ?>"<?php echo $schema_attrs; ?>>
		<?php if ( 'off-canvas' === $settings->mobile_menu_type ) { ?>
			<a href="javascript:void(0)" class="pp-menu-close-btn" aria-label="<?php _e( 'Close the menu', 'bb-powerpack' ); ?>" role="button">×</a>
		<?php } ?>
		<?php if ( 'full-screen' === $settings->mobile_menu_type ) { ?>
			<div class="pp-menu-close-btn" aria-label="<?php _e( 'Close the menu', 'bb-powerpack' ); ?>" role="button" tabindex="0"></div>
		<?php } ?>
		<?php $module->render_nav(); ?>
	</nav>
</div>