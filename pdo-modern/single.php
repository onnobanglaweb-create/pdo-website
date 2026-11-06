<?php
/**
 * Single post.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

$pdo_sidebar = pdo_modern_has_sidebar();

pdo_modern_section_open( array( 'class' => 'pdo-single' ) );
?>
<div class="<?php echo $pdo_sidebar ? 'pdo-with-sidebar' : ''; ?>">
	<div class="pdo-content">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'pdo-entry' ); ?>>
				<header class="pdo-entry__header">
					<h1 class="pdo-section__title"><?php echo esc_html( pdo_post_title( get_the_ID() ) ); ?></h1>
					<?php pdo_modern_post_meta( get_the_ID() ); ?>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="pdo-entry__media"><?php the_post_thumbnail( 'full' ); ?></figure>
				<?php endif; ?>

				<div class="pdo-prose">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
					echo pdo_post_content( get_the_ID() );
					?>
				</div>

				<footer class="pdo-entry__footer">
					<?php
					$pdo_tags = get_the_tags();
					if ( $pdo_tags ) :
						?>
						<ul class="pdo-taglist">
							<?php foreach ( $pdo_tags as $pdo_tag ) : ?>
								<li><a href="<?php echo esc_url( (string) get_tag_link( $pdo_tag ) ); ?>">#<?php echo esc_html( $pdo_tag->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php get_template_part( 'template-parts/components/share' ); ?>
				</footer>
			</article>

			<?php get_template_part( 'template-parts/components/post-nav' ); ?>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</div>

	<?php if ( $pdo_sidebar ) { get_sidebar(); } ?>
</div>
<?php
pdo_modern_section_close();

get_footer();
