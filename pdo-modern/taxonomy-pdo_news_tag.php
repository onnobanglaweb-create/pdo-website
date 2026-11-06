<?php
/**
 * News archive (also used for the news taxonomies).
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

$pdo_is_tax = is_tax();
?>
<section class="pdo-page-hero">
	<div class="pdo-container">
		<?php
		pdo_modern_section_header(
			array(
				'eyebrow'     => $pdo_is_tax ? __( 'Category', 'pdo-modern' ) : pdo_text( 'media', 'intro_eyebrow', '' ),
				'title'       => $pdo_is_tax ? wp_strip_all_tags( get_the_archive_title() ) : pdo_text( 'media', 'intro_title', __( 'Media & Insights', 'pdo-modern' ) ),
				'description' => $pdo_is_tax ? wp_strip_all_tags( get_the_archive_description() ) : pdo_text( 'media', 'intro_description', '' ),
				'level'       => 'h1',
			)
		);
		?>
	</div>
</section>
<?php
pdo_modern_section_open( array( 'class' => 'pdo-archive pdo-archive--news' ) );

if ( ! $pdo_is_tax && pdo_setting( 'media', 'show_filter', true ) ) {
	get_template_part( 'template-parts/components/news-filter' );
}

if ( have_posts() ) :
	?>
	<div class="pdo-grid pdo-grid--auto" id="pdo-news-list">
		<?php
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/components/card', 'news', array( 'post' => get_post(), 'heading' => 2 ) );
		}
		?>
	</div>

	<p class="pdo-align-center" data-pdo-filter-empty hidden>
		<?php esc_html_e( 'No articles match that category yet.', 'pdo-modern' ); ?>
	</p>

	<?php
	pdo_modern_pagination();
else :
	get_template_part( 'template-parts/components/no-results' );
endif;

pdo_modern_section_close();

get_footer();
