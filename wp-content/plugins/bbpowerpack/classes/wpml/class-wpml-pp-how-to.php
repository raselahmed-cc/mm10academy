<?php

class WPML_PP_How_To extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->step_data;
	}

	public function get_fields() {
		return array( 'step_title', 'step_description', 'step_link' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'step_title':
				return esc_html__( 'How To - Step Title', 'bb-powerpack' );

			case 'step_description':
				return esc_html__( 'How To - Step Description', 'bb-powerpack' );

			case 'step_link':
				return esc_html__( 'How To - Step Link', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'step_title':
				return 'LINE';

			case 'step_description':
				return 'VISUAL';

			case 'step_link':
				return 'LINK';

			default:
				return '';
		}
	}
}
