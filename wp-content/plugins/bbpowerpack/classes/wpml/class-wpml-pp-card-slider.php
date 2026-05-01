<?php

class WPML_PP_Card_Slider extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->card_custom_items;
	}

	public function get_fields() {
		return array( 'item_text', 'link', 'item_content' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'item_text':
				return esc_html__( 'Card Slider - Title', 'bb-powerpack' );

			case 'link':
				return esc_html__( 'Card Slider - Link', 'bb-powerpack' );

			case 'item_content':
				return esc_html__( 'Card Slider - Content', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'item_text':
				return 'LINE';

			case 'link':
				return 'LINK';

			case 'item_content':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
