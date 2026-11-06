<?php
/**
 * Template Name: PDO – About Us
 * Template Post Type: page
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/pages/about' );
}

get_footer();
