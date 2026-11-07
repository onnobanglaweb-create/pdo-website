<?php
/**
 * PDO Modern theme bootstrap.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

define( 'PDO_MODERN_VERSION', '1.0.3' );
define( 'PDO_MODERN_DIR', get_template_directory() );
define( 'PDO_MODERN_URI', get_template_directory_uri() );

require_once PDO_MODERN_DIR . '/inc/compatibility.php';
require_once PDO_MODERN_DIR . '/inc/helpers.php';
require_once PDO_MODERN_DIR . '/inc/theme-setup.php';
require_once PDO_MODERN_DIR . '/inc/enqueue.php';
require_once PDO_MODERN_DIR . '/inc/class-pdo-nav-walker.php';
require_once PDO_MODERN_DIR . '/inc/template-functions.php';
require_once PDO_MODERN_DIR . '/inc/sections.php';
require_once PDO_MODERN_DIR . '/inc/seo.php';
require_once PDO_MODERN_DIR . '/inc/customizer/customizer.php';
require_once PDO_MODERN_DIR . '/inc/customizer/customizer-css.php';
