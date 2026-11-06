<?php
/**
 * Front page.
 *
 * Sections, their order and their visibility are all controlled from
 * Customizer → Homepage Sections; their content comes from
 * PDO Site Manager → Homepage.
 *
 * Custom sections created in PDO Site Manager → Sections and assigned to the
 * "Homepage" location are rendered before / after the built-in stack, and the
 * "Global" location wraps every template (see header.php / footer.php).
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

pdo_modern_dynamic_sections( 'home', 'before' );

foreach ( pdo_modern_home_section_order() as $pdo_section_key ) {
	pdo_modern_render_home_section( $pdo_section_key );
}

// Editable intro from the Home page itself, when the editor added content.
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$pdo_content = get_the_content();
		if ( '' !== trim( wp_strip_all_tags( $pdo_content ) ) ) {
			pdo_modern_section_open( array( 'class' => 'pdo-section--tight pdo-front-content' ) );
			echo '<div class="pdo-prose pdo-container--narrow">';
			the_content();
			echo '</div>';
			pdo_modern_section_close();
		}
	}
}

pdo_modern_dynamic_sections( 'home', 'after' );

get_footer();
