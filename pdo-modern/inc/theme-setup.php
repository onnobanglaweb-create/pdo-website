<?php
/**
 * Theme supports, menus, image sizes and widget areas.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme support.
 *
 * @return void
 */
function pdo_modern_setup() {
	load_theme_textdomain( 'pdo-modern', PDO_MODERN_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 64,
			'width'                => 240,
			'flex-height'          => true,
			'flex-width'           => true,
			'header-text'          => array( 'site-title', 'site-description' ),
			'unlink-homepage-logo' => false,
		)
	);
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'ffffff',
		)
	);

	add_editor_style( 'assets/css/editor.css' );

	// Content width used by oEmbed and wide images.
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 760;
	}

	add_image_size( 'pdo-hero', 1600, 900, true );
	add_image_size( 'pdo-card', 760, 460, true );
	add_image_size( 'pdo-thumb', 420, 300, true );
	add_image_size( 'pdo-portrait', 480, 560, true );
	add_image_size( 'pdo-square', 420, 420, true );

	register_nav_menus(
		array(
			'primary'         => __( 'Primary Navigation', 'pdo-modern' ),
			'topbar'          => __( 'Top Bar (utility links)', 'pdo-modern' ),
			'footer'          => __( 'Footer – Quick Navigation', 'pdo-modern' ),
			'footer_programs' => __( 'Footer – Flagship Programs', 'pdo-modern' ),
			'legal'           => __( 'Footer – Legal', 'pdo-modern' ),
		)
	);

	/**
	 * Fires after the theme has registered its supports.
	 */
	do_action( 'pdo_modern_after_setup_theme' );
}
add_action( 'after_setup_theme', 'pdo_modern_setup' );

/**
 * Expose friendly size names in the editor.
 *
 * @param array $sizes Sizes.
 * @return array
 */
function pdo_modern_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'pdo-card'     => __( 'PDO Card (760×460)', 'pdo-modern' ),
			'pdo-thumb'    => __( 'PDO Thumbnail (420×300)', 'pdo-modern' ),
			'pdo-portrait' => __( 'PDO Portrait (480×560)', 'pdo-modern' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'pdo_modern_image_size_names' );

/**
 * Register the widget areas.
 *
 * @return void
 */
function pdo_modern_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'pdo-modern' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Shown beside the blog, archives and search results.', 'pdo-modern' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Extra', 'pdo-modern' ),
			'id'            => 'footer-extra',
			'description'   => __( 'Optional widget column rendered inside the footer.', 'pdo-modern' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="pdo-footer__heading">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'pdo_modern_widgets_init' );

/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function pdo_modern_body_classes( $classes ) {
	$classes[] = 'pdo-theme';
	$classes[] = 'pdo-layout-' . sanitize_html_class( (string) get_theme_mod( 'pdo_layout_width', 'wide' ) );
	$classes[] = 'pdo-header-' . sanitize_html_class( (string) get_theme_mod( 'pdo_header_layout', 'standard' ) );

	if ( get_theme_mod( 'pdo_header_sticky', true ) ) {
		$classes[] = 'pdo-has-sticky-header';
	}
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'pdo-no-sidebar';
	}
	if ( 'bn' === pdo_lang() ) {
		$classes[] = 'pdo-lang-bn';
	}
	if ( get_theme_mod( 'pdo_rounded_corners', true ) ) {
		$classes[] = 'pdo-rounded';
	}
	if ( ! get_theme_mod( 'pdo_animations', true ) ) {
		$classes[] = 'pdo-no-animation';
	}

	return $classes;
}
add_filter( 'body_class', 'pdo_modern_body_classes' );

/**
 * Pingback header on singular views.
 *
 * @return void
 */
function pdo_modern_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'pdo_modern_pingback_header' );
