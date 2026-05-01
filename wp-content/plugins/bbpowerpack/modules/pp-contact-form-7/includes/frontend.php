<div class="pp-cf7-content">
	<?php
	if ( $settings->custom_title ) {
		echo '<h3 class="pp-cf7-form-title">';
	 	echo $settings->custom_title;
		echo '</h3>';
	}
	if ( $settings->custom_description ) {
		echo '<p class="pp-cf7-form-description">';
		echo $settings->custom_description;
		echo '</p>';
	}
    if ( $settings->select_form_field ) {
        echo do_shortcode( '[contact-form-7 id='.absint( $settings->select_form_field ).' ajax=true]' );
    }
    ?>
</div>
