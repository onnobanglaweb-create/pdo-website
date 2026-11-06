<?php
/**
 * Dynamic section – program listing.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

$pdo_limit = isset( $pdo_section['limit'] ) ? (int) $pdo_section['limit'] : 0;
$pdo_posts = pdo_get_posts( 'pdo_program', array( 'posts_per_page' => $pdo_limit > 0 ? $pdo_limit : 6 ) );

if ( ! $pdo_posts ) {
	return;
}

pdo_modern_dynamic_open( $pdo_section );
pdo_modern_dynamic_header( $pdo_section );
?>
<div class="pdo-grid <?php echo esc_attr( pdo_modern_columns_class( isset( $pdo_section['columns'] ) ? $pdo_section['columns'] : 3 ) ); ?>">
	<?php foreach ( $pdo_posts as $pdo_post ) : ?>
		<?php get_template_part( 'template-parts/components/card', 'program', array( 'post' => $pdo_post ) ); ?>
	<?php endforeach; ?>
</div>
<?php
pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
