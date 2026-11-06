<?php
/**
 * Dynamic section – heading and description.
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
pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
