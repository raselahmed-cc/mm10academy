<?php
	$type        = ! empty( $field->field_type ) ? $field->field_type : 'text';
	$value       = ! empty( $field->default_value ) ? $field->default_value : '';
	$class       = isset( $field->class ) ? $field->class : '';
	$placeholder = isset( $field->placeholder ) ? $field->placeholder : '';
?>
<input 
	type="<?php echo esc_attr( $type ); ?>" 
	class="pp-rf-control<?php echo ! empty( $class ) ? ' ' . $class : ''; ?>" name="<?php echo $field_name; ?>" 
	id="<?php echo $field_id; ?>" 
	value="<?php echo $value; ?>"
	<?php echo ! empty( $placeholder ) ? ' placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>
	<?php echo ( 'yes' == $field->required ) ? ' required="required" aria-required="true"' : ''; ?> 
/>