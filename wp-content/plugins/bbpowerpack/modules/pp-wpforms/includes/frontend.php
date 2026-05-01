<?php
$title_tag = isset( $settings->title_tag ) ? esc_attr( $settings->title_tag ) : 'h3';
?>
<div class="pp-wpforms-content">
	<?php if ( $settings->custom_title && ! empty( $settings->custom_title ) ) { ?>
		<<?php echo esc_attr( $title_tag ); ?> class="pp-form-title"><?php echo $settings->custom_title; ?></<?php echo esc_attr( $title_tag ); ?>>
	<?php } ?>
	<?php if ( $settings->custom_description && ! empty( $settings->custom_description ) ) { ?>
		<p class="pp-form-description"><?php echo $settings->custom_description; ?></p>
	<?php } ?>

    <?php if ( $settings->select_form_field ) {
        echo do_shortcode( '[wpforms id='.$settings->select_form_field.' title='.$settings->title_field.' description='.$settings->description_field.']' );
    } ?>
</div>
