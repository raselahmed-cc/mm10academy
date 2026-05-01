<?php
$browser = ' pp-user-agent-' . pp_get_user_agent();
$items = $settings->list_items;
?>

<div class="pp-icon-list<?php echo $browser; ?>">
	<ul class="pp-icon-list-items pp-list-type-<?php echo esc_attr( $settings->list_type ); ?>">
	<?php if ( is_array( $items ) && count( $items ) ) {
		
		$starting_number = isset( $settings->start_number ) && ! empty( $settings->start_number ) ? $settings->start_number : 1;

		for ( $i = 0; $i < count( $items ); $i++ ) { ?>

			<li class="pp-icon-list-item pp-icon-list-item-<?php echo $i; ?>">
				<span class="pp-list-item-icon <?php echo 'icon' == $settings->list_type ? esc_attr( $settings->list_icon ) : ''; ?>"><?php echo 'number' == $settings->list_type ? $starting_number + $i : ''; ?></span>
				<span class="pp-list-item-text"><?php echo (string) $items[$i]; ?></span>
			</li>

		<?php } ?>

	<?php } ?>
	</ul>
</div>
