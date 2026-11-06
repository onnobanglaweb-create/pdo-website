<?php
/**
 * Generic archive – post type archives, taxonomy terms, date and author views.
 *
 * Dedicated templates exist for the news archive and its taxonomies; everything
 * else lands here and picks the card component that matches the post type.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

$pdo_sidebar     = pdo_modern_has_sidebar();
$pdo_title       = wp_strip_all_tags( get_the_archive_title() );
$pdo_description = wp_strip_all_tags( get_the_archive_description() );
$pdo_eyebrow     = '';

if ( is_post_type_archive() ) {
	$pdo_object = get_queried_object();
	if ( $pdo_object instanceof WP_Post_Type ) {
		$pdo_title   = $pdo_object->labels->name;
		$pdo_eyebrow = pdo_org( 'name', '' );
		if ( ! $pdo_description && ! empty( $pdo_object->description ) ) {
			$pdo_description = $pdo_object->description;
		}
	}
} elseif ( is_tax() || is_category() || is_tag() ) {
	$pdo_object = get_queried_object();
	if ( $pdo_object instanceof WP_Term ) {
		$pdo_taxonomy = get_taxonomy( $pdo_object->taxonomy );
		$pdo_title    = $pdo_object->name;
		$pdo_eyebrow  = $pdo_taxonomy ? $pdo_taxonomy->labels->singular_name : '';
	}
}
?>
<section class="pdo-page-hero">
	<div class="pdo-container">
		<?php
		pdo_modern_section_header(
			array(
				'eyebrow'     => $pdo_eyebrow,
				'title'       => $pdo_title,
				'description' => $pdo_description,
				'level'       => 'h1',
			)
		);
		?>
	</div>
</section>
<?php
pdo_modern_section_open( array( 'class' => 'pdo-archive' ) );
?>
<div class="<?php echo $pdo_sidebar ? 'pdo-with-sidebar' : ''; ?>">
	<div class="pdo-content">
		<?php if ( have_posts() ) : ?>
			<div class="pdo-grid <?php echo esc_attr( pdo_modern_columns_class( get_theme_mod( 'pdo_blog_columns', 3 ), 3 ) ); ?>">
				<?php
				while ( have_posts() ) {
					the_post();
					pdo_modern_render_card( get_post(), array( 'heading' => 2 ) );
				}
				?>
			</div>
			<?php pdo_modern_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/components/no-results' ); ?>
		<?php endif; ?>
	</div>

	<?php if ( $pdo_sidebar ) { get_sidebar(); } ?>
</div>
<?php
pdo_modern_section_close();

get_footer();
