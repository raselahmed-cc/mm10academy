<div class="pp-notification-wrapper">
	<div class="pp-notification-inner">
		<?php if ( $settings->notification_icon != '' ) { ?>
			<div class="pp-notification-icon">
				<span class="pp-icon <?php echo esc_attr( $settings->notification_icon ); ?>"></span>
			</div>
		<?php } ?>
		<div class="pp-notification-content">
			<p><?php echo $settings->notification_content; ?></p>
		</div>
	</div>
</div>
