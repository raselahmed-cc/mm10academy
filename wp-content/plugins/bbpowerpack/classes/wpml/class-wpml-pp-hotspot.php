<?php

class WPML_PP_Hotspot extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->markers_content;
	}

	public function get_fields() {
		return array( 'marker_title', 'marker_text', 'marker_link', 'tooltip_content' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'marker_title':
				return esc_html__( 'Hotspot - Admin Title', 'bb-powerpack' );

			case 'marker_text':
				return esc_html__( 'Hotspot - Marker Text', 'bb-powerpack' );

			case 'marker_link':
				return esc_html__( 'Hotspot - Marker Link', 'bb-powerpack' );

			case 'tooltip_content':
				return esc_html__( 'Hotspot - Tooltip Content', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'marker_title':
				return 'LINE';

			case 'marker_text':
				return 'LINE';

			case 'marker_link':
				return 'LINK';

			case 'tooltip_content':
				return 'VISUAL';

			default:
				return '';
		}
	}
}
