<div class="pp-testimonial layout-4<?php echo $classes; ?><?php echo ! isset( $testimonial['photo'] ) || empty( $testimonial['photo']['src'] ) ? ' no-image-inner' : ''; ?>">
	<?php if ( isset( $testimonial['photo'] ) && ! empty( $testimonial['photo']['src'] ) ) { ?>
		<div class="pp-testimonials-image">
			<img class="pp-testimonial-img" src="<?php echo $testimonial['photo']['src']; ?>" alt="<?php echo $testimonial['photo']['alt']; ?>" />
		</div>
	<?php } ?>
	<div class="layout-4-content pp-content-wrapper">
		<?php if ( $testimonial['testimonial'] ) { ?>
			<?php if ( ! empty( $testimonial['testimonial_title'] ) ) { ?>
			<<?php echo $testimonial_title_tag; ?> class="pp-testimonials-title"><?php echo $testimonial['testimonial_title']; ?></<?php echo $testimonial_title_tag; ?>>
			<?php } ?>
			<div class="pp-testimonials-content"><?php echo $testimonial['testimonial']; ?></div>
		<?php } ?>
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
</div>