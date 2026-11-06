<?php
/**
 * Component – news category filter.
 *
 * Works as plain links without JavaScript; main.js upgrades it to an
 * in-place filter when scripting is available.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_cats = pdo_news_categories();

if ( ! $pdo_cats ) {
	return;
}
?>
<ul class="pdo-filters" data-pdo-filter-group data-pdo-filter-target="#pdo-news-list .pdo-card--news">
	<li>
		<button type="button" class="pdo-filters__btn is-active" data-pdo-filter="all" aria-pressed="true">
			<?php echo esc_html( pdo_text( 'media', 'filter_all_label', __( 'All updates', 'pdo-modern' ) ) ); ?>
		</button>
	</li>
	<?php foreach ( $pdo_cats as $pdo_cat ) : ?>
		<li>
			<button
				type="button"
				class="pdo-filters__btn"
				data-pdo-filter="<?php echo esc_attr( $pdo_cat->slug ); ?>"
				aria-pressed="false"
			>
				<?php echo esc_html( $pdo_cat->name ); ?>
				<span class="pdo-filters__count"><?php echo esc_html( (string) $pdo_cat->count ); ?></span>
			</button>
		</li>
	<?php endforeach; ?>
</ul>

<noscript>
	<ul class="pdo-filters">
		<?php foreach ( $pdo_cats as $pdo_cat ) : ?>
			<li><a class="pdo-filters__btn" href="<?php echo esc_url( (string) get_term_link( $pdo_cat ) ); ?>"><?php echo esc_html( $pdo_cat->name ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</noscript>
