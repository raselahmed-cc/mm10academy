<?php

class WPML_PP_Google_Map extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->pp_gmap_addresses;
	}

	public function get_fields() {
		return array( 'map_name', 'map_latitude', 'map_longitude', 'info_window_text' );
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'map_name':
				return esc_html__( 'Google Map - Map Name', 'bb-powerpack' );

			case 'map_latitude':
				return esc_html__( 'Google Map - Map Latitude', 'bb-powerpack' );

			case 'map_longitude':
				return esc_html__( 'Google Map - Map Longitude', 'bb-powerpack' );

			case 'info_window_text':
				return esc_html__( 'Google Map - Map Info Text', 'bb-powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'map_name':
				return 'LINE';

			case 'map_latitude':
				return 'LINE';

			case 'map_longitude':
				return 'LINE';

			case 'info_window_text':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
