<?php
/**
 * Taxonomy registration.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomies.
 */
class PDO_SM_Taxonomies {

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ), 6 );
	}

	/**
	 * Register all taxonomies.
	 *
	 * @return void
	 */
	public function register() {
		$this->register_tax(
			'pdo_news_cat',
			'pdo_news',
			__( 'News Category', 'pdo-site-manager' ),
			__( 'News Categories', 'pdo-site-manager' ),
			'media-category',
			true
		);

		$this->register_tax(
			'pdo_news_tag',
			'pdo_news',
			__( 'News Tag', 'pdo-site-manager' ),
			__( 'News Tags', 'pdo-site-manager' ),
			'media-tag',
			false
		);

		$this->register_tax(
			'pdo_program_cat',
			'pdo_program',
			__( 'Program Category', 'pdo-site-manager' ),
			__( 'Program Categories', 'pdo-site-manager' ),
			'program-category',
			true
		);

		$this->register_tax(
			'pdo_team_group',
			'pdo_team',
			__( 'Team Group', 'pdo-site-manager' ),
			__( 'Team Groups', 'pdo-site-manager' ),
			'team-group',
			true
		);

		$this->register_tax(
			'pdo_partner_cat',
			'pdo_partner',
			__( 'Partnership Category', 'pdo-site-manager' ),
			__( 'Partnership Categories', 'pdo-site-manager' ),
			'partner-category',
			true
		);

		$this->register_tax(
			'pdo_story_cat',
			'pdo_story',
			__( 'Story Category', 'pdo-site-manager' ),
			__( 'Story Categories', 'pdo-site-manager' ),
			'story-category',
			true
		);

		$this->register_tax(
			'pdo_doc_cat',
			'pdo_document',
			__( 'Document Category', 'pdo-site-manager' ),
			__( 'Document Categories', 'pdo-site-manager' ),
			'document-category',
			true
		);
	}

	/**
	 * Helper for register_taxonomy().
	 *
	 * @param string $slug         Taxonomy slug.
	 * @param string $object_type  Post type.
	 * @param string $singular     Singular label.
	 * @param string $plural       Plural label.
	 * @param string $rewrite      Rewrite slug.
	 * @param bool   $hierarchical Hierarchical?
	 * @return void
	 */
	private function register_tax( $slug, $object_type, $singular, $plural, $rewrite, $hierarchical ) {
		register_taxonomy(
			$slug,
			$object_type,
			array(
				'labels'            => array(
					'name'          => $plural,
					'singular_name' => $singular,
					/* translators: %s: plural taxonomy name. */
					'search_items'  => sprintf( __( 'Search %s', 'pdo-site-manager' ), $plural ),
					/* translators: %s: plural taxonomy name. */
					'all_items'     => $plural,
					/* translators: %s: singular taxonomy name. */
					'edit_item'     => sprintf( __( 'Edit %s', 'pdo-site-manager' ), $singular ),
					/* translators: %s: singular taxonomy name. */
					'add_new_item'  => sprintf( __( 'Add New %s', 'pdo-site-manager' ), $singular ),
					'menu_name'     => $plural,
				),
				'hierarchical'      => $hierarchical,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'show_in_menu'      => true,
				'rewrite'           => array(
					'slug'       => $rewrite,
					'with_front' => false,
				),
			)
		);
	}
}
