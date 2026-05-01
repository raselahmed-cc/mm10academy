<?php
$schema_attrs = apply_filters( 'pp_advanced_menu_nav_render_schema_attrs', true, $settings ) ? ' itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement"' : '';

if ( 'default' !== $settings->mobile_menu_type ) {
	$module->render_toggle_button();
}

if ( $settings->mobile_breakpoint == 'always' ) {
	if ( 'default' !== $settings->mobile_menu_type ) {
		echo '<div id="pp-menu-' . $id . '">';
		include $module->dir . 'includes/menu-' . $settings->mobile_menu_type . '.php';
		echo '</div>';
	} else {
		include $module->dir . 'includes/menu-default.php';
	}
} else {
	include $module->dir . 'includes/menu-default.php';
	if ( 'default' !== $settings->mobile_menu_type ) {
		echo '<script type="text/html" id="pp-menu-' . $id . '">';
		include $module->dir . 'includes/menu-' . $settings->mobile_menu_type . '.php';
		echo '</script>';
	}
}
