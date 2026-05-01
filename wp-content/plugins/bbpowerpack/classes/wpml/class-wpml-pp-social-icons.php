<?php

class WPML_PP_Social_Icon extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->icons;
	}

	public function get_fields() {
		return array( 'icon_custom_title', 'link' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'icon_custom_title':
				return esc_html__( 'Social Icons - Custom Title', 'bb-powerpack' );

			case 'link':
				return esc_html__( 'Social Icons - Custom Link', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'icon_custom_title':
				return 'LINE';

			case 'link':
				return 'LINK';

			default:
				return '';
		}
	}

}
