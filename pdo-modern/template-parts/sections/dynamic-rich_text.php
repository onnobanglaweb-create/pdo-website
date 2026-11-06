<?php
/**
 * Dynamic section – rich text.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

pdo_modern_dynamic_open( $pdo_section );
pdo_modern_dynamic_header( $pdo_section );
?>
<div class="pdo-prose pdo-container--narrow">
	<?php echo wp_kses_post( wpautop( pdo_modern_section_text( $pdo_section, 'content', '' ) ) ); ?>
</div>
<?php
pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
