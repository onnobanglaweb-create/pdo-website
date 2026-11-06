<?php
/**
 * Dynamic section – chart / infographic.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

$pdo_items = pdo_modern_dynamic_items( $pdo_section );
$pdo_rows  = array();
$pdo_total = 0;

foreach ( $pdo_items as $pdo_index => $pdo_item ) {
	$pdo_value = isset( $pdo_item['value'] ) ? (float) preg_replace( '/[^0-9.]/', '', (string) $pdo_item['value'] ) : 0;
	if ( $pdo_value <= 0 ) {
		continue;
	}
	$pdo_total  += $pdo_value;
	$pdo_rows[] = array(
		'label'       => pdo_row_text( $pdo_item, 'title', '' ),
		'description' => pdo_row_text( $pdo_item, 'description', '' ),
		'count_label' => pdo_row_text( $pdo_item, 'subtitle', '' ),
		'value'       => $pdo_value,
		'color'       => ! empty( $pdo_item['color'] ) ? $pdo_item['color'] : '',
	);
}

if ( ! $pdo_rows ) {
	return;
}

$pdo_palette = array( 'var(--pdo-emerald)', 'var(--pdo-teal)', 'var(--pdo-gold)', 'var(--pdo-accent)', 'var(--pdo-emerald-dark)', 'var(--pdo-slate)' );
foreach ( $pdo_rows as $pdo_index => $pdo_row ) {
	if ( ! $pdo_row['color'] ) {
		$pdo_rows[ $pdo_index ]['color'] = $pdo_palette[ $pdo_index % count( $pdo_palette ) ];
	}
	$pdo_rows[ $pdo_index ]['share'] = $pdo_total > 0 ? round( ( $pdo_row['value'] / $pdo_total ) * 100, 1 ) : 0;
}

/*
 * The donut's centre caption has no dedicated control, so this section type
 * repurposes the generic "Button label" field (default: "Total").
 * This is documented in docs/EDITOR-GUIDE.md.
 */
pdo_modern_dynamic_open( $pdo_section );
pdo_modern_dynamic_header( $pdo_section );
?>
<div class="pdo-split pdo-chart">
	<div class="pdo-chart__visual">
		<?php
		pdo_modern_donut_chart(
			array(
				'rows'  => $pdo_rows,
				'total' => $pdo_total,
			),
			array(
				'center_value' => number_format_i18n( $pdo_total ),
				'center_label' => pdo_modern_section_text( $pdo_section, 'btn_label', __( 'Total', 'pdo-modern' ) ),
				'title'        => pdo_modern_section_text( $pdo_section, 'heading', __( 'Distribution', 'pdo-modern' ) ),
			)
		);
		?>
	</div>
	<ul class="pdo-legend">
		<?php foreach ( $pdo_rows as $pdo_row ) : ?>
			<li class="pdo-legend__item">
				<span class="pdo-legend__swatch" style="background:<?php echo esc_attr( $pdo_row['color'] ); ?>"></span>
				<span>
					<span class="pdo-legend__title"><?php echo esc_html( $pdo_row['label'] ); ?></span>
					<span class="pdo-legend__meta">
						<?php echo esc_html( number_format_i18n( $pdo_row['value'] ) ); ?>
						<?php echo $pdo_row['count_label'] ? esc_html( ' ' . $pdo_row['count_label'] ) : ''; ?>
						· <?php echo esc_html( $pdo_row['share'] ); ?>%
					</span>
					<?php if ( $pdo_row['description'] ) : ?>
						<span class="pdo-legend__meta"><?php echo esc_html( $pdo_row['description'] ); ?></span>
					<?php endif; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
pdo_modern_section_close();
