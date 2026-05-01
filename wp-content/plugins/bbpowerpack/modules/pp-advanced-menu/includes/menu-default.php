<div class="pp-advanced-menu<?php if ( $settings->collapse ) echo ' pp-advanced-menu-accordion-collapse'; ?> pp-menu-default pp-menu-align-<?php echo esc_attr( $settings->alignment ); ?> pp-menu-position-<?php echo esc_attr( $settings->menu_position ); ?>">
   	<?php
   	if ( $settings->mobile_menu_type == 'default' ) {
		$module->render_toggle_button();
   	}
   	?>
   	<div class="pp-clear"></div>
	<nav class="pp-menu-nav" aria-label="<?php echo $module->get_menu_label(); ?>"<?php echo $schema_attrs; ?>>
		<?php $module->render_nav( $id ); ?>
	</nav>
</div>
