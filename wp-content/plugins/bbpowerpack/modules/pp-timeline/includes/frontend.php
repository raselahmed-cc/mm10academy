<div class="pp-timeline">
	<div class="pp-timeline-content-box clearfix">
		<div class="pp-timeline-content-wrapper">
			<?php
			$timeline_items = $module->get_timeline_items();
			foreach ( $timeline_items as $i => $timeline ) {
			?>
			<div id="pp-timeline-<?php echo $id; ?>-<?php echo $i; ?>" class="pp-timeline-item clearfix pp-timeline-item-<?php echo $i; ?>">
				<div class="pp-timeline-icon-wrapper">
					<span class="pp-separator-arrow"></span>
					<div class="pp-timeline-icon"><?php echo $timeline['icon_markup']; ?></div>
				</div>
				<div class="pp-timeline-content">
					<?php if ( ! empty( $timeline['title'] ) ) { ?>
						<div class="pp-timeline-title-wrapper">
							<<?php echo esc_attr( $settings->title_html_tag ); ?> class="pp-timeline-title"><?php echo $timeline['title']; ?></<?php echo esc_attr( $settings->title_html_tag ); ?>>
						</div>
					<?php } ?>
					<div class="pp-timeline-text-wrapper">
						<div class="pp-timeline-text">
							<?php echo $timeline['content']; ?>
							<?php echo isset( $timeline['button_markup'] ) ? $timeline['button_markup'] : ''; ?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
