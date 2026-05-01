<?php
$sub_text = isset( $settings->sub_text ) ? $settings->sub_text : '';
?>
<div class="<?php echo $module->get_classname(); ?>">
	<a <?php echo $module->get_attributes(); ?>>
		<?php if ( ! empty( $settings->icon ) && ( 'before' == $settings->icon_position || ! isset( $settings->icon_position ) ) && $settings->display_icon == 'yes' ) : ?>
		<i class="pp-button-icon pp-button-icon-before <?php echo esc_attr( $settings->icon ); ?>"></i>
		<?php endif; ?>
		<?php if ( ! empty( $sub_text ) ) { ?>
			<span class="pp-button-has-subtext">
		<?php } ?>
		<span class="pp-button-text"><?php echo $settings->text; ?></span>
		<?php if ( ! empty( $sub_text ) ) { ?>
			<span class="pp-button-subtext"><?php echo $sub_text; ?></span>
			</span>
		<?php } ?>
		<?php if ( ! empty( $settings->icon ) && 'after' == $settings->icon_position && $settings->display_icon == 'yes' ) : ?>
		<i class="pp-button-icon pp-button-icon-after <?php echo esc_attr( $settings->icon ); ?>"></i>
		<?php endif; ?>
	</a>
</div>
