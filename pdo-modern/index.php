<?php
/**
 * Fallback template – the blog index and any view without a closer match.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

$pdo_sidebar = pdo_modern_has_sidebar();

pdo_modern_section_open( array( 'class' => 'pdo-archive' ) );
?>
<div class="<?php echo $pdo_sidebar ? 'pdo-with-sidebar' : ''; ?>">
	<div class="pdo-content">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<header class="pdo-section__header pdo-align-left">
				<h1 class="pdo-section__title"><?php single_post_title(); ?></h1>
			</header>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="pdo-grid <?php echo esc_attr( pdo_modern_columns_class( get_theme_mod( 'pdo_blog_columns', 2 ), 2 ) ); ?>">
				<?php
				while ( have_posts() ) {
					the_post();
					pdo_modern_render_card( get_post() );
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
