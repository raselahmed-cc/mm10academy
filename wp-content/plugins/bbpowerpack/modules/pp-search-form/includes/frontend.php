<div class="pp-search-form-wrap pp-search-form--style-<?php echo esc_attr( $settings->style ); ?> pp-search-form--button-type-<?php echo esc_attr( $settings->button_type ); ?>">
	<form class="pp-search-form" role="search" action="<?php echo home_url(); ?>" method="get" aria-label="<?php esc_attr_e( 'Search form', 'bb-powerpack' ); ?>">
		<?php if ( 'full_screen' === $settings->style ) : ?>
			<div class="pp-search-form__toggle">
				<?php echo apply_filters( 'pp_search_form_toggle_icon_html', sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $settings->toggle_icon ) ), $settings ); ?>
				<span class="pp-screen-reader-text"><?php esc_html_e( 'Search', 'bb-powerpack' ); ?></span>
			</div>
		<?php endif; ?>
		<div class="pp-search-form__container">
			<?php if ( 'minimal' === $settings->style ) :
				$input_icon = ! isset( $settings->input_icon ) ? 'fa fa-search' : esc_attr( $settings->input_icon );
				$input_icon = apply_filters( 'pp_search_form_input_icon_html', sprintf( '<i class="%s" aria-hidden="true"></i>', $input_icon ), $settings );
				if ( ! empty( $input_icon ) ) :
				?>
				<div class="pp-search-form__icon">
					<?php echo $input_icon; ?>
					<span class="pp-screen-reader-text"><?php esc_html_e( 'Search', 'bb-powerpack' ); ?></span>
				</div>
				<?php endif; ?>
			<?php endif; ?>
			<label class="pp-screen-reader-text" for="pp-search-form__input-<?php echo $id; ?>">
				<?php echo ! empty( $settings->placeholder ) ? $settings->placeholder : __( 'Type something here to search', 'bb-powerpack' ); ?>
			</label>
			<input id="pp-search-form__input-<?php echo $id; ?>" <?php $module->render_input_attrs(); ?>>
			<?php $module->render_content_inputs(); ?>
			<?php if ( 'classic' === $settings->style ) : ?>
			<button class="pp-search-form__submit" type="submit">
				<?php if ( 'icon' === $settings->button_type ) : ?>
					<?php if ( ! empty( $settings->icon ) ) : ?>
					<i class="<?php echo esc_attr( $settings->icon ); ?>" aria-hidden="true"></i>
					<span class="pp-screen-reader-text"><?php esc_html_e( 'Search', 'bb-powerpack' ); ?></span>
					<?php endif; ?>
				<?php elseif ( ! empty( $settings->button_text ) ) : ?>
					<?php echo $settings->button_text; ?>
				<?php endif; ?>
			</button>
			<?php endif; ?>
			<?php if ( 'full_screen' === $settings->style ) : ?>
			<div class="pp-search-form--lightbox-close">
				<span class="pp-icon-close" aria-hidden="true">
					<svg viewbox="0 0 40 40">
						<path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" />
					</svg>
				</span>
				<span class="pp-screen-reader-text"><?php esc_html_e( 'Close', 'bb-powerpack' ); ?></span>
			</div>
			<?php endif ?>
		</div>
		<?php do_action( 'pp_search_form_before_close', $settings ); ?>
	</form>
</div>