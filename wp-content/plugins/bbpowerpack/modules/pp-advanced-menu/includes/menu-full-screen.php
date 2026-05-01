<div class="pp-advanced-menu<?php if ( $settings->collapse ) echo ' pp-advanced-menu-accordion-collapse'; ?> <?php echo esc_attr( $settings->mobile_menu_type ); ?> pp-menu-position-<?php echo esc_attr( $settings->menu_position ); ?>">
	<div class="pp-clear"></div>
	<nav class="pp-menu-nav pp-menu-overlay pp-overlay-<?php echo esc_attr( $settings->full_screen_effects ); ?>" aria-label="<?php echo $module->get_menu_label(); ?>"<?php echo $schema_attrs; ?>>
		<div class="pp-menu-close-btn"></div>
		<?php $module->render_nav( $id ); ?>
	</nav>
</div>
