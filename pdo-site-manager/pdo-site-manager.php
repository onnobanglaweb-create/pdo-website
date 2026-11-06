<?php
/**
 * Plugin Name:       PDO Website Manager
 * Plugin URI:        https://github.com/onnobanglaweb-create/pdo-website
 * Description:       Content management system for the Peoples Development Organisation (PDO) website. Provides the PDO dashboard, organisation settings, programs, team, partners, impact stories, documents, news, dynamic sections, bilingual content and form submissions. Designed to work with the PDO Modern theme, but all content survives a theme change.
 * Version:           1.0.2
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Peoples Development Organisation
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pdo-site-manager
 * Domain Path:       /languages
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

define( 'PDO_SM_VERSION', '1.0.2' );
define( 'PDO_SM_FILE', __FILE__ );
define( 'PDO_SM_DIR', plugin_dir_path( __FILE__ ) );
define( 'PDO_SM_URL', plugin_dir_url( __FILE__ ) );
define( 'PDO_SM_BASENAME', plugin_basename( __FILE__ ) );
define( 'PDO_SM_DB_VERSION', '1.0.0' );

require_once PDO_SM_DIR . 'includes/class-helpers.php';
require_once PDO_SM_DIR . 'includes/class-capabilities.php';
require_once PDO_SM_DIR . 'includes/class-icons.php';
require_once PDO_SM_DIR . 'includes/class-i18n.php';
require_once PDO_SM_DIR . 'includes/class-settings.php';
require_once PDO_SM_DIR . 'includes/class-schema.php';
require_once PDO_SM_DIR . 'includes/class-fields.php';
require_once PDO_SM_DIR . 'includes/class-content-types.php';
require_once PDO_SM_DIR . 'includes/class-taxonomies.php';
require_once PDO_SM_DIR . 'includes/class-meta-boxes.php';
require_once PDO_SM_DIR . 'includes/class-media.php';
require_once PDO_SM_DIR . 'includes/class-sections.php';
require_once PDO_SM_DIR . 'includes/class-submissions.php';
require_once PDO_SM_DIR . 'includes/class-forms.php';
require_once PDO_SM_DIR . 'includes/class-importer.php';
require_once PDO_SM_DIR . 'includes/class-exporter.php';
require_once PDO_SM_DIR . 'includes/class-setup-wizard.php';
require_once PDO_SM_DIR . 'includes/class-dashboard.php';
require_once PDO_SM_DIR . 'includes/class-rest.php';
require_once PDO_SM_DIR . 'includes/class-activator.php';
require_once PDO_SM_DIR . 'includes/class-deactivator.php';
require_once PDO_SM_DIR . 'includes/template-api.php';

/**
 * Main plugin container.
 */
final class PDO_SM_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var PDO_SM_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Loaded components.
	 *
	 * @var array<string,object>
	 */
	private $components = array();

	/**
	 * Retrieve the singleton.
	 *
	 * @return PDO_SM_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->components['settings']      = new PDO_SM_Settings();
		$this->components['content_types'] = new PDO_SM_Content_Types();
		$this->components['taxonomies']    = new PDO_SM_Taxonomies();
		$this->components['meta_boxes']    = new PDO_SM_Meta_Boxes();
		$this->components['media']         = new PDO_SM_Media();
		$this->components['sections']      = new PDO_SM_Sections();
		$this->components['submissions']   = new PDO_SM_Submissions();
		$this->components['forms']         = new PDO_SM_Forms();
		$this->components['importer']      = new PDO_SM_Importer();
		$this->components['exporter']      = new PDO_SM_Exporter();
		$this->components['wizard']        = new PDO_SM_Setup_Wizard();
		$this->components['dashboard']     = new PDO_SM_Dashboard();
		$this->components['i18n']          = new PDO_SM_I18n();
		$this->components['rest']          = new PDO_SM_Rest();

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'maybe_upgrade' ) );
		add_filter( 'plugin_action_links_' . PDO_SM_BASENAME, array( $this, 'action_links' ) );
	}

	/**
	 * Access a loaded component.
	 *
	 * @param string $key Component key.
	 * @return object|null
	 */
	public function get( $key ) {
		return isset( $this->components[ $key ] ) ? $this->components[ $key ] : null;
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'pdo-site-manager', false, dirname( PDO_SM_BASENAME ) . '/languages' );
	}

	/**
	 * Run database/structure upgrades when the stored version is behind.
	 *
	 * @return void
	 */
	public function maybe_upgrade() {
		if ( get_option( 'pdo_sm_db_version' ) !== PDO_SM_DB_VERSION ) {
			PDO_SM_Activator::create_tables();
			PDO_SM_Capabilities::add_caps();
			update_option( 'pdo_sm_db_version', PDO_SM_DB_VERSION, false );
		}
	}

	/**
	 * Add a settings shortcut on the plugins screen.
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public function action_links( $links ) {
		$url = admin_url( 'admin.php?page=pdo-manager' );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'PDO Dashboard', 'pdo-site-manager' ) . '</a>' );
		return $links;
	}
}

register_activation_hook( __FILE__, array( 'PDO_SM_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'PDO_SM_Deactivator', 'deactivate' ) );

/**
 * Convenience accessor.
 *
 * @return PDO_SM_Plugin
 */
function pdo_sm() {
	return PDO_SM_Plugin::instance();
}

pdo_sm();
