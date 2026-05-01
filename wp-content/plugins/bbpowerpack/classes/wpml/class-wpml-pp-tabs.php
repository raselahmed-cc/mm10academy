<?php

class WPML_PP_Tabs extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->items;
	}

	public function get_fields() {
		return array( 'label', 'content' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'label':
				return esc_html__( 'Advanced Tabs - Tab Label', 'bb-powerpack' );

			case 'description':
				return esc_html__( 'Advanced Tabs - Tab Description', 'bb-powerpack' );

			case 'content':
				return esc_html__( 'Advanced Tabs - Tab Content', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'label':
				return 'LINE';
			
			case 'description':
				return 'TEXTAREA';

			case 'content':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
