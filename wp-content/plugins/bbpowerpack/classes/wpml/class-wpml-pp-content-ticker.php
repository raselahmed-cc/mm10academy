<?php

class WPML_PP_Content_Ticker extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->ticker_custom_items;
	}

	public function get_fields() {
		return array( 'item_text', 'link' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'item_text':
				return esc_html__( 'Content Ticker - Title', 'bb-powerpack' );

			case 'link':
				return esc_html__( 'Content Ticker - Link', 'bb-powerpack' );

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

			default:
				return '';
		}
	}

}
