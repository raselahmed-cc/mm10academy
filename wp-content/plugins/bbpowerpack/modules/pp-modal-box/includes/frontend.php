<?php
$has_overlay_animation = ! isset( $settings->overlay_animation ) || 'yes' === $settings->overlay_animation;
$title_tag = isset( $settings->title_tag ) ? esc_attr( $settings->title_tag ) : 'h2';
?>
<?php if ( 'onclick' == $settings->modal_load ) { ?>
<div class="pp-modal-button">
	<a href="#" id="trigger-<?php echo $module->node; ?>" class="pp-modal-trigger modal-<?php echo $module->node; ?>" role="button" data-modal="<?php echo $module->node; ?>" data-node="<?php echo $module->node; ?>">
		<?php if ( 'button' == $settings->button_type ) { ?>
			<?php if ( '' != $settings->button_icon_src && 'before' == $settings->button_icon_pos ) { ?>
				<span class="pp-button-icon pp-button-icon-before <?php echo esc_attr( $settings->button_icon_src ); ?>"></span>
			<?php } ?>
				<span class="pp-modal-trigger-text"><?php echo $settings->button_text; ?></span>
			<?php if ( '' != $settings->button_icon_src && 'after' == $settings->button_icon_pos ) { ?>
				<span class="pp-button-icon pp-button-icon-after <?php echo esc_attr( $settings->button_icon_src ); ?>"></span>
			<?php } ?>
		<?php } ?>
		<?php if ( 'image' == $settings->button_type ) { ?>
			<img src="<?php echo esc_url( $settings->image_source_src ); ?>" class="pp-modal-trigger-image" alt="<?php echo pp_get_image_alt( $settings->image_source ); ?>" />
		<?php } ?>
		<?php if ( 'icon' == $settings->button_type ) { ?>
			<span class="<?php echo esc_attr( $settings->icon_source ); ?> pp-modal-trigger-icon"></span>
		<?php } ?>
		<?php if ( ( 'image' == $settings->button_type || 'icon' == $settings->button_type ) && isset( $settings->sr_text ) && ! empty( $settings->sr_text ) ) { ?>
			<span class="sr-only"><?php echo $settings->sr_text; ?></span>
		<?php } ?>
	</a>
</div>
<?php } else { ?>
	<?php if ( pp_is_builder_active() ) { ?>
	<div class="pp-helper" style="text-align: center;">
		<h4>
			<span><?php echo $module->name; ?></span>
			<?php if ( isset( $settings->node_label ) && ! empty( $settings->node_label ) ) { ?>
				- <span><?php echo $settings->node_label; ?></span>
			<?php } ?>
		</h4>
		<h5>modal-<?php echo $module->node; ?></h5>
		<?php _e('Click here to edit the "modal-box" settings. This text is only for editing and will not appear after you publish the changes.', 'bb-powerpack'); ?>
	</div>
	<?php } ?>
<?php } ?>
<?php
$ariaLabelledby = ( 'yes' == $settings->modal_title_toggle ) ? ' aria-labelledby="modal-title-' . $id . '"' : '';
?>
<div id="modal-<?php echo $module->node; ?>" class="pp-modal-wrap<?php echo $has_overlay_animation ? ' has-overlay-animation' : ''; ?>" role="dialog"<?php echo $ariaLabelledby; ?>>
	<div class="pp-modal-container">
		<?php if ( 'win-top-right' == $settings->close_btn_position || 'win-top-left' == $settings->close_btn_position ) { ?>
			<div class="pp-modal-close <?php echo esc_attr( $settings->close_btn_position ); ?>" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Close', 'bb-powerpack' ); ?>">
				<div class="bar-wrap" aria-hidden="true">
					<span class="bar-1"></span>
					<span class="bar-2"></span>
				</div>
			</div>
		<?php } ?>
		<div class="pp-modal layout-<?php echo esc_attr( $settings->modal_layout ); ?>">
			<?php if ( FLBuilderModel::is_builder_active() ) { ?>
			<div style="position: absolute; top: -35px; color: #235425; text-transform: uppercase; background: #c6e4a4; border-radius: 2px; padding: 3px 10px;">Preview</div>
			<div style="position: absolute; top: -35px; left: 85px; color: #000000; text-transform: lowercase; background: #dedede; border-radius: 2px; padding: 3px 10px;">modal-<?php echo $module->node; ?></div>
			<?php } ?>
			<div class="pp-modal-body">
				<?php if ( 'no' == $settings->modal_title_toggle ) { ?>
					<?php if ( 'win-top-right' != $settings->close_btn_position && 'win-top-left' != $settings->close_btn_position ) { ?>
						<div class="pp-modal-close <?php echo esc_attr( $settings->close_btn_position ); ?> no-modal-header" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Close', 'bb-powerpack' ); ?>">
							<div class="bar-wrap" aria-hidden="true">
								<span class="bar-1"></span>
								<span class="bar-2"></span>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				<?php if ( 'yes' == $settings->modal_title_toggle ) { ?>
				<div class="pp-modal-header">
					<?php if ( 'box-top-right' == $settings->close_btn_position ) { ?>
						<?php echo sprintf( '<%s id="modal-title-%s" class="pp-modal-title">%s</%s>', $title_tag, $id, $settings->modal_title, $title_tag ); ?>
					<?php } ?>
					<?php if ( 'win-top-right' != $settings->close_btn_position && 'win-top-left' != $settings->close_btn_position ) { ?>
						<div class="pp-modal-close <?php echo esc_attr( $settings->close_btn_position ); ?>" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Close', 'bb-powerpack' ); ?>">
							<div class="bar-wrap" aria-hidden="true">
								<span class="bar-1"></span>
								<span class="bar-2"></span>
							</div>
						</div>
					<?php } ?>
					<?php if ( 'box-top-left' == $settings->close_btn_position ) { ?>
						<?php echo sprintf( '<%s id="modal-title-%s" class="pp-modal-title">%s</%s>', $title_tag, $id, $settings->modal_title, $title_tag ); ?>
					<?php } ?>
				</div>
				<?php } ?>
				<div class="pp-modal-content<?php echo ('url' == $settings->modal_type || 'video' == $settings->modal_type) ? ' pp-modal-frame' : '' ;?>">
					<div class="pp-modal-content-inner">
						<?php
							$settings->module_id = $id;
							$load_in_builder = apply_filters( 'pp_modal_box_load_content_in_builder', true );
							if ( ! $load_in_builder && pp_is_builder_active() ) {
								_e( 'Content will be displayed on front-end.', 'bb-powerpack' );
							} else {
								if ( 'templates' === $settings->modal_type && ! empty( $settings->modal_type_templates ) ) {
									$module->render_post_content( $settings->modal_type_templates );
								} else {
									echo $module->get_modal_content( $settings );
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="pp-modal-overlay"></div>
</div>
