<?php
/**
 * Site header.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#<?php echo esc_attr( pdo_modern_main_id() ); ?>">
	<?php esc_html_e( 'Skip to the main content', 'pdo-modern' ); ?>
</a>

<div id="pdo-page" class="pdo-page">

	<?php get_template_part( 'template-parts/header/topbar' ); ?>

	<header
		id="pdo-masthead"
		class="pdo-header<?php echo get_theme_mod( 'pdo_header_border', true ) ? ' pdo-header--bordered' : ''; ?><?php echo get_theme_mod( 'pdo_header_shrink', true ) ? ' pdo-header--shrink' : ''; ?>"
		<?php echo get_theme_mod( 'pdo_header_sticky', true ) ? 'data-pdo-sticky' : ''; ?>
	>
		<div class="pdo-container pdo-header__inner">
			<div class="pdo-header__brand">
				<?php pdo_modern_logo( 'header' ); ?>
			</div>

			<?php get_template_part( 'template-parts/header/navigation' ); ?>

			<div class="pdo-header__actions">
				<?php if ( get_theme_mod( 'pdo_header_search', true ) ) : ?>
					<button
						type="button"
						class="pdo-iconbtn pdo-header__search-toggle"
						aria-expanded="false"
						aria-controls="pdo-search-panel"
						data-pdo-search-toggle
					>
						<?php pdo_icon( 'search', array( 'size' => 18 ) ); ?>
						<span class="screen-reader-text"><?php esc_html_e( 'Search the website', 'pdo-modern' ); ?></span>
					</button>
				<?php endif; ?>

				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper.
				echo pdo_modern_render_header_cta();
				?>

				<button
					type="button"
					class="pdo-iconbtn pdo-header__burger"
					aria-expanded="false"
					aria-controls="pdo-drawer"
					data-pdo-drawer-toggle
				>
					<span class="pdo-burger" aria-hidden="true"><span></span><span></span><span></span></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Open the menu', 'pdo-modern' ); ?></span>
				</button>
			</div>
		</div>

		<?php if ( get_theme_mod( 'pdo_header_search', true ) ) : ?>
			<div id="pdo-search-panel" class="pdo-search-panel" hidden>
				<div class="pdo-container">
					<?php get_search_form(); ?>
				</div>
			</div>
		<?php endif; ?>
	</header>

	<?php get_template_part( 'template-parts/header/drawer' ); ?>

	<?php pdo_modern_breadcrumbs(); ?>

	<main id="<?php echo esc_attr( pdo_modern_main_id() ); ?>" class="pdo-main" tabindex="-1">
