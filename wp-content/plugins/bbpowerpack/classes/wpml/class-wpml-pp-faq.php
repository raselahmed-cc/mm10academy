<?php

class WPML_PP_FAQ extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->items;
	}

	public function get_fields() {
		return array( 'faq_question', 'answer' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'faq_question':
				return esc_html__( 'FAQ - Manual Question', 'bb-powerpack' );

			case 'answer':
				return esc_html__( 'FAQ - Manual Answer', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'faq_question':
				return 'LINE';

			case 'answer':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
