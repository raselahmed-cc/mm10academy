<?php

	$columns = count($settings->pricing_columns);

	if ( $settings->pricing_table_style == 'matrix' ) {
		$columns = $columns + 1;
	}

	$eq_heights = isset( $settings->equal_heights ) && 'yes' === $settings->equal_heights && 'cards' === $settings->pricing_table_style ? ' pp-pricing-table-eq-heights' : '';

?>

<div class="pp-pricing-table pp-pricing-table-spacing-<?php echo esc_attr( $settings->box_spacing ); ?><?php echo $eq_heights; ?>">

	<?php if ( 'yes' == $settings->dual_pricing ) { ?>
	<div class="pp-pricing-table-switch">
		<div class="pp-pricing-table-buttons">
			<a href="javascript:void(0)" class="pp-pricing-table-button pp-pricing-button-1 pp-pricing-button-active" data-activate-price="primary"><?php echo $settings->dp_button_1_text; ?></a>
			<a href="javascript:void(0)" class="pp-pricing-table-button pp-pricing-button-2" data-activate-price="secondary"><?php echo $settings->dp_button_2_text; ?></a>
		</div>
	</div>
	<?php } ?>

	<div class="pp-pricing-table-colset">
	<?php if ( $settings->pricing_table_style == 'matrix' ) { ?>
		<div class="pp-pricing-table-col pp-pricing-table-col-<?php echo $columns; ?> pp-pricing-table-matrix">
			<div class="pp-pricing-table-column">
				<div class="pp-pricing-table-header">
					<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-pricing-table-title" aria-hidden="true">&nbsp;</<?php echo esc_attr( $settings->title_tag ); ?>>
					<div class="pp-pricing-table-price" aria-hidden="true">
						&nbsp;
					</div>
				</div>
				<ul class="pp-pricing-table-features">
					<?php if ( ! empty( $settings->matrix_items ) ) : $item_count = 0; ?>
						<?php foreach ( $settings->matrix_items as $item ) : $item_count++; ?>
						<li class="pp-pricing-table-item-<?php echo $item_count; ?>"><?php echo trim( $item ); ?></li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	<?php } ?>

	<?php

	for ( $i=0; $i < count( $settings->pricing_columns ); $i++ ) :

		if ( ! is_object( $settings->pricing_columns[ $i ] ) ) continue;

		$pricing_column = $settings->pricing_columns[$i];

		$highlight = '';
		$f_title = '';

		if ( $settings->highlight !== 'none' && $i == $settings->hl_packages ) {
			$highlight = ' pp-pricing-table-highlight';
			if ( $settings->highlight == 'title' ) {
				$highlight = ' pp-pricing-table-highlight-title';
			}
			if ( $settings->highlight == 'price' ) {
				$highlight = ' pp-pricing-table-highlight-price';
			}
		}

		if ( $pricing_column->hl_featured_title ) {
			$f_title = ' pp-has-featured-title';
		}
	?>
	<div class="pp-pricing-table-col pp-pricing-table-card pp-pricing-table-col-<?php echo $columns; ?><?php echo $highlight; ?><?php echo $f_title; ?>">
		<div class="pp-pricing-table-column pp-pricing-table-column-<?php echo $i; ?>">
			<?php if ( $pricing_column->hl_featured_title ) {
				$ribbon_class = isset( $settings->featured_title_ribbon ) && 'yes' === $settings->featured_title_ribbon ? ' pp-pricing-ribbon pp-pricing-ribbon-' . esc_attr( $settings->featured_title_ribbon_pos ) : '';
				?>
				<?php if ( ! empty( $ribbon_class ) ) { ?>
				<div class="<?php echo $ribbon_class; ?>">
				<?php } ?>
				<div class="pp-pricing-featured-title">
					<span><?php echo $pricing_column->hl_featured_title; ?></span>
				</div>
				<?php if ( ! empty( $ribbon_class ) ) { ?>
				</div>
				<?php } ?>
			<?php } ?>
			<div class="pp-pricing-table-inner-wrap">
				<div class="pp-pricing-table-header">
					<?php if ( $settings->title_position == 'above' ) { ?>
						<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-pricing-table-title"><?php echo isset( $pricing_column->title ) ? $pricing_column->title : ''; ?></<?php echo esc_attr( $settings->title_tag ); ?>>
					<?php } ?>
					<div class="pp-pricing-table-price pp-price-primary">
						<?php echo isset( $pricing_column->price ) ? $pricing_column->price : ''; ?> <span class="pp-pricing-table-duration"><?php echo isset( $pricing_column->duration ) ? $pricing_column->duration : ''; ?></span>
					</div>
					<?php if ( 'yes' == $settings->dual_pricing ) { ?>
						<div class="pp-pricing-table-price pp-price-secondary">
						<?php echo $pricing_column->price_2; ?> <span class="pp-pricing-table-duration"><?php echo $pricing_column->duration_2; ?></span>
					</div>
					<?php } ?>
					<?php if ( $settings->title_position == 'below' ) { ?>
						<<?php echo esc_attr( $settings->title_tag ); ?> class="pp-pricing-table-title"><?php echo $pricing_column->title; ?></<?php echo esc_attr( $settings->title_tag ); ?>>
					<?php } ?>
				</div>

				<?php $module->render_feature( $i ); ?>
				<?php $module->render_button( $i ); ?>
			</div>
		</div>
	</div>
	<?php

	endfor;

	?>
	</div>
</div>
