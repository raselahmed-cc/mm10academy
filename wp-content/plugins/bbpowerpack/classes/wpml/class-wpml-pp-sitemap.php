<?php

class WPML_PP_Sitemap extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->list_items;
	}

	public function get_fields() {
		return array( 'sitemap_label' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'sitemap_label':
				return esc_html__( 'Sitemap - Sitemap Label', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'sitemap_label':
				return 'LINE';

			default:
				return '';
		}
	}

}
