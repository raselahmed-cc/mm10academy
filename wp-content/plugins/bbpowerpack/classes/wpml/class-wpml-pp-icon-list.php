<?php

class WPML_PP_Icon_List extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items($settings) {
		return $settings->list_items;
	}

	protected function get_title($key) {
		return "Icon/Number List Item $key";
	}

	protected function get_editor_type($key) {
		return 'LINE';
	}

	public function get($node_id, $settings, $strings) {
		foreach ( $this->get_items( $settings ) as $key => $item ) {
			if ( is_string( $item ) ) {
				$strings[] = new WPML_PB_String(
					$item,
					$this->get_string_name($node_id, $item, $key),
					$this->get_title($key),
					$this->get_editor_type($key)
				);
			}
		}
		return $strings;
	}

	public function update($node_id, $settings, WPML_PB_String $string) {
		foreach ($this->get_items($settings) as $key => &$item) {
			if ($this->get_string_name($node_id, $item, $key) == $string->get_name()) {
				$item = $string->get_value();
			}
		}
	}

	private function get_string_name($node_id, $value, $type, $key = '') {
		return md5($value) . '-' . $type . $key . '-' . $node_id;
	}
}
