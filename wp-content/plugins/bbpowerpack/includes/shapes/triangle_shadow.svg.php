<svg class="pp-big-triangle-shadow" xmlns="http://www.w3.org/2000/svg" version="1.1" fill="currentColor" width="100%" height="<?php echo $height; ?>" viewBox="0 0 100 100" preserveAspectRatio="none" role="presentation">
	<path class="pp-main-color" d="M0 0 L50 100 L100 0 Z" />
	<path class="pp-shadow-color" <?php echo isset( $shadow ) && '' != $shadow ? 'fill="#' . $shadow . '"' : ''; ?> d="M50 100 L100 40 L100 0 Z" />
</svg>