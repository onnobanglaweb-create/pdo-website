<?php
/**
 * Dynamic section – form.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

/*
 * The section editor has no dedicated "form type" control, so this section type
 * repurposes the generic "Button label" field: type `contact`, `volunteer` or
 * `partner` there to choose which form is embedded. Anything else falls back to
 * the contact form. This is documented in docs/EDITOR-GUIDE.md.
 */
$pdo_type = strtolower( trim( pdo_modern_section_text( $pdo_section, 'btn_label', '' ) ) );
$pdo_type = in_array( $pdo_type, array( 'contact', 'volunteer', 'partner' ), true ) ? $pdo_type : 'contact';

pdo_modern_dynamic_open( $pdo_section );
pdo_modern_dynamic_header( $pdo_section );

get_template_part( 'template-parts/components/form', null, array( 'type' => $pdo_type ) );

pdo_modern_section_close();
