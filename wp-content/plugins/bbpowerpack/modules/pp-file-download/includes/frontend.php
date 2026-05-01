<?php
$type = isset( $settings->download_type ) ? $settings->download_type : 'button';
$filename = 'button' === $type ? $module->get_filename( $settings->file, $settings->file_name ) : '';
$fileurl  = 'button' === $type ? $settings->file : '';

$button_args = array(
	'style'			=> $settings->style,
	'text'			=> $settings->text,
	'icon'			=> $settings->icon,
	'icon_position'	=> $settings->icon_position,
	'display_icon'	=> $settings->display_icon,
	'button_effect'	=> $settings->button_effect,
	'width'			=> $settings->width,
	'link'			=> $fileurl,
	'download' 		=> esc_attr( $filename ),
);

if ( 'dropdown' === $type ) {
	if ( isset( $settings->files ) && ! empty( $settings->files ) ) {
		$files = $settings->files;
		?>
		<div class="pp-files-wrapper">
			<div class="pp-files-dropdown">
				<select class="pp-files">
				<?php
				for ( $i = 0; $i < count( $files ); $i++ ) {
					if ( ! is_object( $files[ $i ] ) ) {
						continue;
					}

					$fileurl = $files[ $i ]->file;
					$filename = $files[ $i ]->file_name;
					$label = $files[ $i ]->file_label;

					if ( empty( $fileurl ) ) {
						?>
						<option value=""><?php echo $label; ?></option>
						<?php
					} else {
						$filename = $module->get_filename( $files[ $i ]->file, $files[ $i ]->file_name );
						?>
						<option value="<?php echo $fileurl; ?>" data-filename="<?php echo $filename; ?>"><?php echo $label; ?></option>
						<?php
						if ( empty( $button_args['link'] ) ) {
							$button_args['link'] = $fileurl;
							$button_args['download'] = esc_attr( $filename );
						}
					}
				}
				?>
				</select>
			</div>
			<?php FLBuilder::render_module_html( 'pp-smart-button', $button_args, $module ); ?>
		</div>
		<?php
	}
} else {
	FLBuilder::render_module_html( 'pp-smart-button', $button_args, $module );
}
if ( isset( $settings->additional_content ) && ! empty( $settings->additional_content ) ) {
	echo wpautop( do_shortcode( $settings->additional_content ) );
}