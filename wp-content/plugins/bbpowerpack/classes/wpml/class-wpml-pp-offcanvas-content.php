<?php

class WPML_PP_Offcanvas_Content extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->content_form;
	}

	public function get_fields() {
		return array( 'content_title', 'content_description' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'content_title':
				return esc_html__( 'Off-Canvas Content - Custom Title', 'bb-powerpack' );

			case 'content_description':
				return esc_html__( 'Off-Canvas Content - Custom Description', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'content_title':
				return 'LINE';

			case 'content_description':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
