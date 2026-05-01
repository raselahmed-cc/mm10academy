<?php
$html_tag = isset( $settings->name_html_tag ) ? esc_attr( $settings->name_html_tag ) : 'h4';
?>
<div class="pp-pullquote">
	<div class="pp-pullquote-wrapper clearfix">
		<?php if ( 'yes' === $settings->show_icon ) { ?>
			<div class="pp-pullquote-icon">
				<span class="pp-icon <?php echo esc_attr( $settings->quote_icon ); ?>"></span>
			</div>
		<?php } ?>
		<div class="pp-pullquote-inner">
			<div class="pp-pullquote-content">
				<p><?php echo $settings->quote_text; ?></p>
			</div>
			<?php if ( ! empty( $settings->quote_name ) ) { ?>
			<div class="pp-pullquote-title">
				<<?php echo $html_tag; ?> class="pp-pullquote-name"><?php echo $settings->quote_name; ?></<?php echo $html_tag; ?>>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
