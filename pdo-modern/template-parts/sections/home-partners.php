<?php
/**
 * Homepage – strategic partners strip.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_limit    = (int) pdo_setting( 'home', 'partners_limit', 6 );
$pdo_partners = pdo_get_posts( 'pdo_partner', array( 'posts_per_page' => $pdo_limit > 0 ? $pdo_limit : 6 ) );

if ( ! $pdo_partners ) {
	return;
}

pdo_modern_section_open(
	array(
		'id'         => 'pdo-section-partners',
		'class'      => 'pdo-section--tight pdo-section-partners',
		'background' => 'light',
		'visibility' => pdo_modern_visibility_class( 'partners' ),
		'label'      => 'pdo-partners-title',
	)
);

pdo_modern_section_header(
	array(
		'title' => pdo_text( 'home', 'partners_title', '' ),
		'id'    => 'pdo-partners-title',
		'level' => 'h2',
	)
);
?>
<div class="pdo-grid pdo-grid--auto">
	<?php foreach ( $pdo_partners as $pdo_partner ) : ?>
		<?php get_template_part( 'template-parts/components/card', 'partner', array( 'post' => $pdo_partner ) ); ?>
	<?php endforeach; ?>
</div>
<?php
pdo_modern_section_close();
