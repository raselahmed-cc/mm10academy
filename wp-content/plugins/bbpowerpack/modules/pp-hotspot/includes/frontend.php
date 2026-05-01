<?php
if ( 'library' === $settings->photo_source ) {
	$img_url = $settings->photo_src;
	$img_alt = ! empty( $settings->photo ) ? get_post_meta($settings->photo, '_wp_attachment_image_alt', true) : '';
} else {
	$img_url = $settings->photo_url;
	$img_alt = basename( $img_url );
}
if ( empty( $img_url ) ) {
	$img_url = BB_POWERPACK_URL . 'assets/images/default-img.jpg';
	$img_alt = esc_html__( 'Default Image', 'bb-powerpack' );
}
// Hotspot Tour Check
if ( 'yes' === $settings->enable_tour ) {
	$hotspot_tour = 'pp-hotspot-tour pp-tour-active';
	if ( 'yes' === $settings->non_active_marker ) {
		$hotspot_tour .= ' pp-hotspot-marker-nonactive';
	}
} else {
	$hotspot_tour = 'pp-tour-inactive';
}
if ( 'yes' === $settings->enable_tour && 'yes' === $settings->non_active_marker ) {
	$non_active = ' pp-non-active-marker';
} else {
	$non_active = '';
}

/**
 * Array of markers.
 *
 * Mandatory properties of each array item:
 * - marker_title
 * - marker_link
 * - marker_link_target
 * - marker_type = icon | image | text
 * - marker_tooltip_content
 */
$markers_data = apply_filters( 'pp_hotspot_markers_data', $settings->markers_content, $settings );

if ( ! is_array( $markers_data ) || empty( $markers_data ) ) {
	return;
}
?>

<div class="pp-hotspot">
	<div class="pp-hotspot-container">
		<div class="pp-hotspot-image-container">
			<img src="<?php echo esc_url( do_shortcode( $img_url ) ); ?>" alt="<?php esc_attr_e( $img_alt ); ?>" class="pp-hotspot-image">
		</div>
		<div class="pp-hotspot-content">
		<?php
		foreach ( $markers_data as $i => $marker ) {
			/* translators: %d: number count */
			$marker_title = ! empty( $marker->marker_title ) ? $marker->marker_title : sprintf( __( 'Marker %d', 'bb-powerpack' ), ( $i + 1 ) );
			?>
			<?php if ( ! empty( $marker_title ) ) { ?>
				<?php
				if ( 'yes' === $settings->enable_tour ) {
					$enable_tour = ' data-pptour=' . ( $i + 1 );
				} else {
					$enable_tour = '';
				}
				?>
				<span class="pp-hotspot-marker pp-marker-<?php echo ( $i + 1 ) . $non_active; ?>" data-title="<?php echo esc_attr( $marker_title ); ?>" data-tooltip-content="#pp-tooltip-content-<?php echo $id . '-' . ( $i + 1 ); ?>"<?php echo $enable_tour; ?>>
				<?php if ( 'yes' === $settings->add_marker_link && $marker->marker_link ) { ?>
					<a href="<?php echo esc_url( do_shortcode( $marker->marker_link ) ); ?>" target="<?php echo esc_attr( $marker->marker_link_target ); ?>">
				<?php } ?>
					<?php if ( 'icon' === $marker->marker_type ) { ?>
						<i class="<?php echo esc_attr( $marker->marker_icon ); ?> pp-marker-icon"></i>
					<?php } elseif ( 'image' === $marker->marker_type ) { ?>
						<img src="<?php echo esc_url( $marker->marker_image_src ); ?>" alt="hotspot-marker" class="pp-marker-image">
					<?php } elseif ( 'text' === $marker->marker_type ) { ?>
						<p class="pp-marker-text"><?php echo $marker->marker_text; ?></p>
					<?php } ?>
					<span class="pp-marker-title"><?php echo $marker_title; ?></span>
				<?php if ( 'yes' === $settings->add_marker_link && $marker->marker_link ) { ?>
					</a>
				<?php } ?>
				</span>

				<?php if ( 'yes' === $settings->tooltip && ! empty( $marker->tooltip_content ) ) { ?>
					<span class="pp-tooltip-container pp-tooltip-<?php echo ( $i + 1 ); ?>">
						<span class="<?php echo $hotspot_tour; ?> pp-tooltip-content-<?php echo $id; ?>" id="pp-tooltip-content-<?php echo $id . '-' . ( $i + 1 ); ?>">
							<?php if ( 'yes' === $settings->enable_close_icon ) { ?>
								<i class="pp-tooltip-close fas fa-times"></i>
							<?php } ?>
							<?php echo $marker->tooltip_content; ?>
							<?php if ( 'yes' === $settings->enable_tour ) { ?>
								<span class="pp-tour">
									<ul>
										<li>
											<a class="pp-prev" data-tooltipid="<?php echo ( $i + 1 ); ?>" href="javascript:void(0)">
											<?php if ( 'icon' === $settings->navigation_type ) { ?>
												<i class='<?php echo isset( $settings->pre_icon ) ? esc_attr( $settings->pre_icon ) : 'fas fa-angle-double-left'; ?>'></i>
											<?php } elseif ( 'text' === $settings->navigation_type ) { ?>
												<?php echo isset( $settings->pre_text ) ? $settings->pre_text : __( 'Previous', 'bb-powerpack' ); ?>
											<?php } else { ?>
												<i class='<?php echo isset( $settings->pre_icon ) ? esc_attr( $settings->pre_icon ) : 'fas fa-angle-double-left'; ?>'></i>
												<?php echo isset( $settings->pre_text ) ? $settings->pre_text : __( 'Previous', 'bb-powerpack' ); ?>
											<?php } ?>
											</a>
										</li>
										<li>
											<a class="pp-next" data-tooltipid="<?php echo ( $i + 1 ); ?>" href="javascript:void(0)">
											<?php if ( 'icon' === $settings->navigation_type ) { ?>
												<i class='<?php echo isset( $settings->next_icon ) ? esc_attr( $settings->next_icon ) : 'fas fa-angle-double-right'; ?>'></i>
											<?php } elseif ( 'text' === $settings->navigation_type ) { ?>
												<?php echo isset( $settings->next_text ) ? $settings->next_text : __( 'Next', 'bb-powerpack' ); ?>
											<?php } else { ?>
												<?php echo isset( $settings->next_text ) ? $settings->next_text : __( 'Next', 'bb-powerpack' ); ?>
												<i class='<?php echo isset( $settings->next_icon ) ? esc_attr( $settings->next_icon ) : 'fas fa-angle-double-right'; ?>'></i>
											<?php } ?>
											</a>
										</li>
									</ul>
								</span>
								<span class="pp-tour">
									<ul>
										<li>
											<?php if ( 'yes' === $settings->repeat_tour ) { ?>
												<span class="pp-hotspot-end">
													<a class="pp-tour-end" href="javascript:void(0)">
														<?php echo isset( $settings->end_text ) ? $settings->end_text : __( 'End Tour', 'bb-powerpack' ); ?>
													</a>
												</span>
											<?php } ?>
										</li>
										<li>
											<span class="pp-actual-step" style="display: block;">
												<?php echo ( $i + 1 ); ?> 
												<?php echo __( 'of', 'bb-powerpack' ); ?> 
												<?php echo sizeof( $settings->markers_content ); ?>
											</span>
										</li>
									</ul>
								</span>
						<?php } ?>
						</span>
					</span>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		</div>
		<?php if ( 'yes' === $settings->enable_tour && 'button_click' === $settings->launch_tour ) { ?>
			<div class="pp-hotspot-overlay">
				<button class="pp-hotspot-overlay-button"><?php echo $settings->overlay_button; ?></button>
			</div>
		<?php } ?>
	</div>
</div>
