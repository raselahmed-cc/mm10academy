<div class="pp-testimonial layout-5<?php echo $classes; ?>">
	<div class="pp-vertical-align">
		<?php if ( $testimonial['title'] || $testimonial['subtitle'] ) { ?>
			<div class="pp-title-wrapper">
				<?php if ( $testimonial['title'] ) { ?>
					<<?php echo $title_tag; ?> class="pp-testimonials-name"><?php echo $testimonial['title']; ?></<?php echo $title_tag; ?>>
				<?php } ?>
				<?php if ( $testimonial['subtitle'] ) { ?>
					<<?php echo $subtitle_tag; ?> class="pp-testimonials-designation"><?php echo $testimonial['subtitle']; ?></<?php echo $subtitle_tag; ?>>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php if ( $testimonial['testimonial'] ) { ?>
		<div class="pp-content-wrapper">
			<?php if ( $settings->show_arrow == 'yes' ) { ?><div class="pp-arrow-top"></div><?php } ?>
			<?php if ( ! empty( $testimonial['testimonial_title'] ) ) { ?>
			<<?php echo $testimonial_title_tag; ?> class="pp-testimonials-title"><?php echo $testimonial['testimonial_title']; ?></<?php echo $testimonial_title_tag; ?>>
			<?php } ?>
			<div class="pp-testimonials-content"><?php echo $testimonial['testimonial']; ?></div>
		</div>
	<?php } ?>
</div>