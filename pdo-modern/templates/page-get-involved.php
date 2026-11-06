<?php
/**
 * Template Name: PDO – Get Involved
 * Template Post Type: page
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/pages/involved' );
}

get_footer();
