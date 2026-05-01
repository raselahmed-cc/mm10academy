<div class="pp-advanced-menu<?php if ( $settings->collapse ) echo ' pp-advanced-menu-accordion-collapse'; ?> <?php echo esc_attr( $settings->mobile_menu_type ); ?> pp-menu-position-<?php echo esc_attr( $settings->menu_position ); ?>">
	<div class="pp-clear"></div>
	<nav class="pp-menu-nav pp-off-canvas-menu pp-menu-<?php echo esc_attr( $settings->offcanvas_direction ); ?>" aria-label="<?php echo $module->get_menu_label(); ?>"<?php echo $schema_attrs; ?>>
		<a href="javascript:void(0)" class="pp-menu-close-btn" aria-label="<?php _e( 'Close the menu', 'bb-powerpack' ); ?>" role="button">×</a>
		<?php $module->render_nav( $id ); ?>
	</nav>
</div>
