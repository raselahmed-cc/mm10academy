<?php

class WPML_PP_Social_Share extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->social_icons;
	}

	public function get_fields() {
		return array( 'text' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'text':
				return esc_html__( 'Social Share - Custom Label', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'text':
				return 'LINE';

			default:
				return '';
		}
	}

}
