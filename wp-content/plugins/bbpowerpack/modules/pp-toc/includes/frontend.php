<div class="pp-toc-container">
	<?php if ( ! empty( $settings->heading_title ) ) { ?>
	<div class="pp-toc-header">
		<div class="pp-toc-header-title"><?php echo $settings->heading_title; ?></div>
		<?php
		if ( 'yes' === $settings->collapsable_toc ) {	
		?>
		<span class="header-icon-collapse">
			<span class="<?php echo esc_attr( $settings->collapse_icon_field ); ?>"></span>
		</span>
		<span class="header-icon-expand active">
			<span class="<?php echo esc_attr( $settings->expand_icon_field ); ?>"></span>
		</span>
		<?php } ?>
	</div>
	<div class="pp-toc-separator"></div>
	<?php } ?>

	<div class="pp-toc-body">
		<?php
		if ( 'bullets' === $settings->list_style ) {
			?>
			<ul class="pp-toc-list-wrapper pp-toc-list-bullet"></ul>
		<?php } elseif ( 'numbers' === $settings->list_style ) { ?>
			<ol class="pp-toc-list-wrapper pp-toc-list-number"></ol>
		<?php } else { ?>
			<ul class="pp-toc-list-wrapper pp-toc-list-icon"></ul>
			<?php
		}
		?>
	</div>
</div>

<?php
if ( 'none' !== $settings->scroll_top ) {
	?>
	<div class="pp-toc-scroll-top-container" style="display: none;">
		<span class="<?php echo esc_html( $settings->scroll_icon ); ?> pp-toc-scroll-top-icon"></span>
	</div>
<?php } ?>
