<?php
/**
 * Asset loading.
 *
 * Everything is bundled locally – no CDN, no framework, no jQuery on the
 * front end. Google Fonts are only requested when the administrator opts in
 * from the Customizer; the default is the locally bundled stack.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset version – file mtime in development, theme version otherwise.
 *
 * @param string $relative Relative path inside the theme.
 * @return string
 */
function pdo_modern_asset_version( $relative ) {
	$path = PDO_MODERN_DIR . '/' . ltrim( $relative, '/' );
	if ( file_exists( $path ) ) {
		return PDO_MODERN_VERSION . '.' . filemtime( $path );
	}
	return PDO_MODERN_VERSION;
}

/**
 * Are any self-hosted webfont binaries actually bundled?
 *
 * The repository ships assets/fonts/fonts.css but deliberately not the .woff2
 * files (licensing + repo size). Until someone drops them in, the theme falls
 * back to the system font stack rather than requesting files that 404.
 *
 * @return bool
 */
function pdo_modern_has_bundled_fonts() {
	static $has = null;

	if ( null === $has ) {
		$has = (bool) glob( PDO_MODERN_DIR . '/assets/fonts/*.woff2' );
	}

	return $has;
}

/**
 * Enqueue the front-end assets.
 *
 * @return void
 */
function pdo_modern_enqueue() {
	// Theme header (required by WordPress) – tiny, holds only core classes.
	wp_enqueue_style( 'pdo-modern-base', get_stylesheet_uri(), array(), pdo_modern_asset_version( 'style.css' ) );

	// Locally bundled fonts – skipped entirely when no .woff2 has been dropped
	// into assets/fonts/, so the browser never chases 404s for missing files.
	if ( get_theme_mod( 'pdo_use_bundled_fonts', true ) && pdo_modern_has_bundled_fonts() ) {
		wp_enqueue_style(
			'pdo-modern-fonts',
			PDO_MODERN_URI . '/assets/fonts/fonts.css',
			array(),
			pdo_modern_asset_version( 'assets/fonts/fonts.css' )
		);
	}

	// Design system.
	wp_enqueue_style(
		'pdo-modern-main',
		PDO_MODERN_URI . '/assets/css/main.css',
		array( 'pdo-modern-base' ),
		pdo_modern_asset_version( 'assets/css/main.css' )
	);

	// Customizer tokens.
	wp_add_inline_style( 'pdo-modern-main', pdo_modern_css_variables() );

	wp_enqueue_style(
		'pdo-modern-print',
		PDO_MODERN_URI . '/assets/css/print.css',
		array( 'pdo-modern-main' ),
		pdo_modern_asset_version( 'assets/css/print.css' ),
		'print'
	);

	wp_enqueue_script(
		'pdo-modern-main',
		PDO_MODERN_URI . '/assets/js/main.js',
		array(),
		pdo_modern_asset_version( 'assets/js/main.js' ),
		true
	);

	wp_localize_script(
		'pdo-modern-main',
		'pdoModern',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'restUrl'    => esc_url_raw( rest_url() ),
			'lang'       => pdo_lang(),
			'animations' => (bool) get_theme_mod( 'pdo_animations', true ),
			'i18n'       => array(
				'menu'     => __( 'Menu', 'pdo-modern' ),
				'close'    => __( 'Close', 'pdo-modern' ),
				'expand'   => __( 'Expand submenu', 'pdo-modern' ),
				'collapse' => __( 'Collapse submenu', 'pdo-modern' ),
				'loading'  => __( 'Loading…', 'pdo-modern' ),
				'copied'   => __( 'Link copied to the clipboard.', 'pdo-modern' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pdo_modern_enqueue' );

/**
 * Block editor styles so the backend matches the front end.
 *
 * @return void
 */
function pdo_modern_editor_assets() {
	wp_enqueue_style(
		'pdo-modern-editor',
		PDO_MODERN_URI . '/assets/css/editor.css',
		array(),
		pdo_modern_asset_version( 'assets/css/editor.css' )
	);
	wp_add_inline_style( 'pdo-modern-editor', pdo_modern_css_variables() );
}
add_action( 'enqueue_block_editor_assets', 'pdo_modern_editor_assets' );

/**
 * Preload the two critical fonts so text never flashes.
 *
 * @return void
 */
function pdo_modern_resource_hints() {
	if ( ! get_theme_mod( 'pdo_use_bundled_fonts', true ) || ! pdo_modern_has_bundled_fonts() ) {
		return;
	}
	$fonts = array(
		'/assets/fonts/inter-variable.woff2',
		'/assets/fonts/hind-siliguri-400.woff2',
	);
	foreach ( $fonts as $font ) {
		if ( ! file_exists( PDO_MODERN_DIR . $font ) ) {
			continue;
		}
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( PDO_MODERN_URI . $font )
		);
	}
}
add_action( 'wp_head', 'pdo_modern_resource_hints', 1 );

/**
 * Add `defer` to the theme script.
 *
 * WordPress passes a single argument to `wp_script_attributes`; the handle is
 * available inside the attribute array as the element id.
 *
 * @param array $attr Script attributes.
 * @return array
 */
function pdo_modern_script_attributes( $attr ) {
	if ( isset( $attr['id'] ) && 'pdo-modern-main-js' === $attr['id'] ) {
		$attr['defer'] = true;
	}
	return $attr;
}
add_filter( 'wp_script_attributes', 'pdo_modern_script_attributes' );

/**
 * Drop the emoji detection script – it costs ~12 KB and PDO does not need it.
 *
 * Gutenberg block styles and global styles are deliberately left alone so
 * normal pages and posts keep working with the block editor.
 *
 * @return void
 */
function pdo_modern_trim_head() {
	if ( ! get_theme_mod( 'pdo_disable_emojis', true ) ) {
		return;
	}
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'pdo_modern_trim_head' );
