<?php

class WPML_PP_Reviews extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->reviews;
	}

	public function get_fields() {
		return array( 'name', 'title', 'link', 'review' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'name':
				return esc_html__( 'Reviews - Name', 'bb-powerpack' );

			case 'title':
				return esc_html__( 'Reviews - Title', 'bb-powerpack' );

			case 'link':
				return esc_html__( 'Reviews - Link', 'bb-powerpack' );

			case 'review':
				return esc_html__( 'Reviews - Review', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'name':
				return 'LINE';

			case 'title':
				return 'LINE';

			case 'link':
				return 'LINK';

			case 'review':
				return 'TEXTAREA';

			default:
				return '';
		}
	}

}
