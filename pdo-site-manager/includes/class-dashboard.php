<?php
/**
 * The "PDO Website Manager" admin area.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin dashboard controller.
 */
class PDO_SM_Dashboard {

	/**
	 * Menu slug of the dashboard.
	 */
	const SLUG = 'pdo-manager';

	/**
	 * Hook suffixes registered by this class.
	 *
	 * @var array<string>
	 */
	private $screens = array();

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Settings pages driven by the schema.
	 *
	 * @return array<string,array{group:string,title:string,menu:string}>
	 */
	public static function settings_pages() {
		return array(
			'pdo-organization' => array(
				'group' => 'org',
				'title' => __( 'Organization Information', 'pdo-site-manager' ),
				'menu'  => __( 'Organization', 'pdo-site-manager' ),
			),
			'pdo-homepage'     => array(
				'group' => 'home',
				'title' => __( 'Homepage Manager', 'pdo-site-manager' ),
				'menu'  => __( 'Homepage', 'pdo-site-manager' ),
			),
			'pdo-about'        => array(
				'group' => 'about',
				'title' => __( 'About Page Manager', 'pdo-site-manager' ),
				'menu'  => __( 'About Page', 'pdo-site-manager' ),
			),
			'pdo-impact'       => array(
				'group' => 'impact',
				'title' => __( 'Impact Page Manager', 'pdo-site-manager' ),
				'menu'  => __( 'Impact Page', 'pdo-site-manager' ),
			),
			'pdo-transparency' => array(
				'group' => 'transparency',
				'title' => __( 'Transparency Manager', 'pdo-site-manager' ),
				'menu'  => __( 'Transparency', 'pdo-site-manager' ),
			),
			'pdo-media'        => array(
				'group' => 'media',
				'title' => __( 'Media & News Settings', 'pdo-site-manager' ),
				'menu'  => __( 'Media Settings', 'pdo-site-manager' ),
			),
			'pdo-involved'     => array(
				'group' => 'involved',
				'title' => __( 'Get Involved Manager', 'pdo-site-manager' ),
				'menu'  => __( 'Get Involved', 'pdo-site-manager' ),
			),
			'pdo-contact'      => array(
				'group' => 'contact',
				'title' => __( 'Contact Page Manager', 'pdo-site-manager' ),
				'menu'  => __( 'Contact Page', 'pdo-site-manager' ),
			),
			'pdo-translations' => array(
				'group' => 'i18n',
				'title' => __( 'Translations & Language', 'pdo-site-manager' ),
				'menu'  => __( 'Translations', 'pdo-site-manager' ),
			),
		);
	}

	/**
	 * Register the menu tree.
	 *
	 * @return void
	 */
	public function register_menu() {
		$cap = PDO_SM_Capabilities::MANAGE;

		$this->screens[] = add_menu_page(
			__( 'PDO Website Manager', 'pdo-site-manager' ),
			__( 'PDO Manager', 'pdo-site-manager' ),
			$cap,
			self::SLUG,
			array( $this, 'render_overview' ),
			'dashicons-heart',
			3
		);

		$this->screens[] = add_submenu_page(
			self::SLUG,
			__( 'Dashboard', 'pdo-site-manager' ),
			__( 'Dashboard', 'pdo-site-manager' ),
			$cap,
			self::SLUG,
			array( $this, 'render_overview' )
		);

		foreach ( self::settings_pages() as $slug => $page ) {
			$this->screens[] = add_submenu_page(
				self::SLUG,
				$page['title'],
				$page['menu'],
				$cap,
				$slug,
				array( $this, 'render_settings' )
			);
		}

		$this->screens[] = add_submenu_page(
			self::SLUG,
			__( 'Form Submissions', 'pdo-site-manager' ),
			__( 'Form Submissions', 'pdo-site-manager' ),
			PDO_SM_Capabilities::SUBMISSIONS,
			'pdo-submissions',
			array( $this, 'render_submissions' )
		);

		$this->screens[] = add_submenu_page(
			self::SLUG,
			__( 'Reorder Content', 'pdo-site-manager' ),
			__( 'Reorder Content', 'pdo-site-manager' ),
			$cap,
			'pdo-reorder',
			array( $this, 'render_reorder' )
		);

		$this->screens[] = add_submenu_page(
			self::SLUG,
			__( 'Setup Wizard', 'pdo-site-manager' ),
			__( 'Setup Wizard', 'pdo-site-manager' ),
			$cap,
			'pdo-setup',
			array( $this, 'render_setup' )
		);

		$this->screens[] = add_submenu_page(
			self::SLUG,
			__( 'Tools', 'pdo-site-manager' ),
			__( 'Tools', 'pdo-site-manager' ),
			$cap,
			'pdo-tools',
			array( $this, 'render_tools' )
		);
	}

	/**
	 * Is the current screen one of ours?
	 *
	 * @return bool
	 */
	private function is_pdo_screen() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return false;
		}
		if ( in_array( $screen->id, $this->screens, true ) ) {
			return true;
		}
		return in_array( $screen->post_type, PDO_SM_Content_Types::post_types(), true );
	}

	/**
	 * Add a body class on our screens.
	 *
	 * @param string $classes Body classes.
	 * @return string
	 */
	public function body_class( $classes ) {
		if ( $this->is_pdo_screen() ) {
			$classes .= ' pdo-sm-admin ';
		}
		return $classes;
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current hook.
	 * @return void
	 */
	public function enqueue( $hook ) {
		unset( $hook );

		if ( ! $this->is_pdo_screen() ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_editor();

		wp_enqueue_style(
			'pdo-sm-admin',
			PDO_SM_URL . 'admin/assets/css/admin.css',
			array( 'wp-color-picker' ),
			PDO_SM_VERSION
		);

		wp_enqueue_script(
			'pdo-sm-admin',
			PDO_SM_URL . 'admin/assets/js/admin.js',
			array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ),
			PDO_SM_VERSION,
			true
		);

		wp_localize_script(
			'pdo-sm-admin',
			'pdoSMAdmin',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'reorderNonce' => wp_create_nonce( 'pdo_sm_reorder' ),
				'icons'        => PDO_SM_Icons::all(),
				'i18n'         => array(
					'selectImage'   => __( 'Select image', 'pdo-site-manager' ),
					'useImage'      => __( 'Use this image', 'pdo-site-manager' ),
					'selectFile'    => __( 'Select file', 'pdo-site-manager' ),
					'useFile'       => __( 'Use this file', 'pdo-site-manager' ),
					'confirmRemove' => __( 'Remove this item?', 'pdo-site-manager' ),
					'searchIcons'   => __( 'Search icons…', 'pdo-site-manager' ),
					'saved'         => __( 'Order saved.', 'pdo-site-manager' ),
					'saveFailed'    => __( 'The new order could not be saved.', 'pdo-site-manager' ),
				),
			)
		);
	}

	/* --------------------------------------------------------------------- */
	/* Views                                                                 */
	/* --------------------------------------------------------------------- */

	/**
	 * Dashboard overview.
	 *
	 * @return void
	 */
	public function render_overview() {
		PDO_SM_Capabilities::assert_manage();
		require PDO_SM_DIR . 'admin/views/overview.php';
	}

	/**
	 * Schema-driven settings screen.
	 *
	 * @return void
	 */
	public function render_settings() {
		PDO_SM_Capabilities::assert_manage();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page  = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		$pages = self::settings_pages();

		if ( ! isset( $pages[ $page ] ) ) {
			wp_die( esc_html__( 'Unknown settings page.', 'pdo-site-manager' ) );
		}

		$group_key  = $pages[ $page ]['group'];
		$group      = PDO_SM_Schema::group( $group_key );
		$values     = PDO_SM_Settings::get_group( $group_key );
		$page_title = $pages[ $page ]['title'];

		$tabs = isset( $group['tabs'] ) ? $group['tabs'] : array();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
		if ( ! $current_tab || ! isset( $tabs[ $current_tab ] ) ) {
			$current_tab = (string) key( $tabs );
		}

		require PDO_SM_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Submissions screen.
	 *
	 * @return void
	 */
	public function render_submissions() {
		PDO_SM_Capabilities::assert_submissions();
		require PDO_SM_DIR . 'admin/views/submissions.php';
	}

	/**
	 * Drag-and-drop ordering screen.
	 *
	 * @return void
	 */
	public function render_reorder() {
		PDO_SM_Capabilities::assert_manage();
		require PDO_SM_DIR . 'admin/views/reorder.php';
	}

	/**
	 * Setup wizard screen.
	 *
	 * @return void
	 */
	public function render_setup() {
		PDO_SM_Capabilities::assert_manage();
		require PDO_SM_DIR . 'admin/views/setup-wizard.php';
	}

	/**
	 * Tools screen.
	 *
	 * @return void
	 */
	public function render_tools() {
		PDO_SM_Capabilities::assert_manage();
		require PDO_SM_DIR . 'admin/views/tools.php';
	}

	/* --------------------------------------------------------------------- */
	/* Helpers used by the views                                             */
	/* --------------------------------------------------------------------- */

	/**
	 * Content counts for the overview cards.
	 *
	 * @return array<string,array>
	 */
	public static function counts() {
		$icons = array(
			'pdo_news'     => 'newspaper',
			'pdo_program'  => 'layers',
			'pdo_team'     => 'users',
			'pdo_partner'  => 'handshake',
			'pdo_story'    => 'heart',
			'pdo_document' => 'file-lines',
			'pdo_section'  => 'grid',
		);

		$out = array();
		foreach ( PDO_SM_Content_Types::post_types() as $slug ) {
			$object = get_post_type_object( $slug );
			if ( ! $object ) {
				continue;
			}
			$counts       = wp_count_posts( $slug );
			$out[ $slug ] = array(
				'label'     => $object->labels->name,
				'published' => isset( $counts->publish ) ? (int) $counts->publish : 0,
				'draft'     => isset( $counts->draft ) ? (int) $counts->draft : 0,
				'icon'      => isset( $icons[ $slug ] ) ? $icons[ $slug ] : 'layers',
				'add_url'   => admin_url( 'post-new.php?post_type=' . $slug ),
				'list_url'  => admin_url( 'edit.php?post_type=' . $slug ),
			);
		}
		return $out;
	}

	/**
	 * Build the admin notice for a settings screen.
	 *
	 * @return void
	 */
	public static function settings_notice() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['pdo_saved'] ) ) {
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html__( 'Changes saved and verified in the database.', 'pdo-site-manager' )
			);
		}
		if ( ! empty( $_GET['pdo_error'] ) ) {
			$code     = sanitize_key( wp_unslash( $_GET['pdo_error'] ) );
			$messages = array(
				'save'    => __( 'The changes could not be written to the database. Nothing has been saved.', 'pdo-site-manager' ),
				'nofile'  => __( 'Please choose a file to import.', 'pdo-site-manager' ),
				'upload'  => __( 'The upload failed. Please try again.', 'pdo-site-manager' ),
				'invalid' => __( 'That file is not a valid PDO settings export.', 'pdo-site-manager' ),
			);
			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				esc_html( isset( $messages[ $code ] ) ? $messages[ $code ] : $messages['save'] )
			);
		}
		// phpcs:enable
	}
}
