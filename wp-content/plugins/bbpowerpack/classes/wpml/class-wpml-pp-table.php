<?php

class WPML_PP_Table extends WPML_Beaver_Builder_Module_With_Items {

    public function &get_items($settings) {
        return $settings->rows;
    }

    public function get_fields() {   
        return array('header', 'label', 'cell', 'cells');
    }

    protected function get_title($field) {
        switch ($field) {
            case 'header':
                return esc_html__('Table - Header', 'bb-powerpack');

            case 'label':
                return esc_html__('Table - Row Label', 'bb-powerpack');

            case 'cell':
                return esc_html__('Table - Cell', 'bb-powerpack');

            default:
                return '';
        }
    }

    protected function get_editor_type($field) {
        switch ($field) {
            case 'header':
            case 'label':
            case 'cell':
                return 'LINE';

            default:
                return 'LINE';
        }
    }

    public function get($node_id, $settings, $strings) {
        $items = $this->get_items($settings);
        foreach ($items as $item) {
            $strings = $this->processItem($node_id, $item, $strings);
        }
        return $strings;
    }
    
    private function processItem($node_id, $item, $strings) {
        $fields = $this->get_fields();
        foreach ($fields as $field) {
            if (!isset($item->$field)) {
                continue;
            }
    
            if (is_object($item->$field) || is_array($item->$field)) {
                $strings = $this->processComplexField($node_id, $item->$field, $field, $strings);
            } else {
                $strings = $this->addString($strings, $node_id, $item->$field, $field);
            }
        }
        return $strings;
    }
    
    private function processComplexField($node_id, $field, $fieldName, $strings) {
        foreach ($field as $key => $value) {
            if (is_object($value) && isset($value->content)) {
                $strings = $this->addString($strings, $node_id, $value->content, $fieldName, $key);
            } elseif (is_string($value)) {
                $strings = $this->addString($strings, $node_id, $value, $fieldName, $key);
            }
        }
        return $strings;
    }
    
    private function addString($strings, $node_id, $value, $field, $key = '') {
        $strings[] = new WPML_PB_String(
            $value,
            $this->get_string_name($node_id, $value, $field, $key),
            $this->get_title($field),
            $this->get_editor_type($field)
        );
        return $strings;
    }   


    public function update($node_id, $settings, WPML_PB_String $string) {
        foreach ($this->get_items($settings) as &$item) {
            $this->updateItem($node_id, $item, $string);
        }
    }
    
    private function updateItem($node_id, &$item, WPML_PB_String $string) {
        $fields = $this->get_fields();
        foreach ($fields as $field) {
            if (!isset($item->$field)) {
                continue;
            }
    
            $stringName = $this->get_string_name($node_id, $item->$field, $field);
    
            if ($stringName == $string->get_name()) {
                $item->$field = $string->get_value();
                return;
            }
    
            if (is_array($item->$field) || is_object($item->$field)) {
                $this->updateComplexField($node_id, $item->$field, $field, $string);
            }
        }
    }
    
    private function updateComplexField($node_id, &$field, $fieldName, WPML_PB_String $string) {
        foreach ($field as $key => &$value) {
            if (is_object($value) && isset($value->content)) {
                $stringName = $this->get_string_name($node_id, $value->content, $fieldName, $key);
    
                if ($stringName == $string->get_name()) {
                    $value->content = $string->get_value();
                    return;
                }
            } elseif (is_string($value)) {
                $stringName = $this->get_string_name($node_id, $value, $fieldName, $key);
    
                if ($stringName == $string->get_name()) {
                    $value = $string->get_value();
                    return;
                }
            }
        }
    }

    private function get_string_name($node_id, $value, $type, $key = '') {
		if ( ! is_string( $value ) ) {
			return;
		}
        return md5($value) . '-' . $type . $key . '-' . $node_id;
    }
}