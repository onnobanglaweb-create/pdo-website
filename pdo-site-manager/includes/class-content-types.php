<?php
/**
 * Custom post type registration.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Content types.
 */
class PDO_SM_Content_Types {

	/**
	 * Parent admin menu slug used by every PDO post type.
	 */
	const MENU = 'pdo-manager';

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ), 5 );
		add_filter( 'enter_title_here', array( $this, 'title_placeholder' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'default_ordering' ) );
		add_filter( 'manage_pdo_program_posts_columns', array( $this, 'order_column' ) );
		add_filter( 'manage_pdo_team_posts_columns', array( $this, 'order_column' ) );
		add_filter( 'manage_pdo_partner_posts_columns', array( $this, 'order_column' ) );
		add_filter( 'manage_pdo_story_posts_columns', array( $this, 'order_column' ) );
		add_filter( 'manage_pdo_document_posts_columns', array( $this, 'order_column' ) );
		add_filter( 'manage_pdo_section_posts_columns', array( $this, 'order_column' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'render_column' ), 10, 2 );
	}

	/**
	 * All PDO post types.
	 *
	 * @return array<string>
	 */
	public static function post_types() {
		return array( 'pdo_news', 'pdo_program', 'pdo_team', 'pdo_partner', 'pdo_story', 'pdo_document', 'pdo_section' );
	}

	/**
	 * Register the post types.
	 *
	 * @return void
	 */
	public function register() {
		$cap = 'edit_posts';

		register_post_type(
			'pdo_news',
			array(
				'labels'             => self::labels( __( 'News Article', 'pdo-site-manager' ), __( 'Media & News', 'pdo-site-manager' ) ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => self::MENU,
				'show_in_rest'       => true,
				'rest_base'          => 'pdo-news',
				'menu_icon'          => 'dashicons-megaphone',
				'has_archive'        => 'media',
				'rewrite'            => array(
					'slug'       => 'media',
					'with_front' => false,
				),
				'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions', 'custom-fields', 'page-attributes' ),
				'taxonomies'         => array( 'pdo_news_cat', 'pdo_news_tag' ),
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
			)
		);

		register_post_type(
			'pdo_program',
			array(
				'labels'          => self::labels( __( 'Program', 'pdo-site-manager' ), __( 'Programs', 'pdo-site-manager' ) ),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => self::MENU,
				'show_in_rest'    => true,
				'menu_icon'       => 'dashicons-chart-area',
				'has_archive'     => 'programs',
				'rewrite'         => array(
					'slug'       => 'program',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
				'taxonomies'      => array( 'pdo_program_cat' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);

		register_post_type(
			'pdo_team',
			array(
				'labels'          => self::labels( __( 'Team Member', 'pdo-site-manager' ), __( 'Team Members', 'pdo-site-manager' ) ),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => self::MENU,
				'show_in_rest'    => true,
				'menu_icon'       => 'dashicons-groups',
				'has_archive'     => false,
				'exclude_from_search' => true,
				'rewrite'         => array(
					'slug'       => 'team',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions' ),
				'taxonomies'      => array( 'pdo_team_group' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);

		register_post_type(
			'pdo_partner',
			array(
				'labels'          => self::labels( __( 'Partner', 'pdo-site-manager' ), __( 'Partners', 'pdo-site-manager' ) ),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => self::MENU,
				'show_in_rest'    => true,
				'menu_icon'       => 'dashicons-networking',
				'has_archive'     => false,
				'exclude_from_search' => true,
				'rewrite'         => array(
					'slug'       => 'partner',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'taxonomies'      => array( 'pdo_partner_cat' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);

		register_post_type(
			'pdo_story',
			array(
				'labels'          => self::labels( __( 'Impact Story', 'pdo-site-manager' ), __( 'Impact Stories', 'pdo-site-manager' ) ),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => self::MENU,
				'show_in_rest'    => true,
				'menu_icon'       => 'dashicons-format-quote',
				'has_archive'     => 'impact-stories',
				'rewrite'         => array(
					'slug'       => 'impact-story',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
				'taxonomies'      => array( 'pdo_story_cat' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);

		register_post_type(
			'pdo_document',
			array(
				'labels'          => self::labels( __( 'Document', 'pdo-site-manager' ), __( 'Policies & Documents', 'pdo-site-manager' ) ),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => self::MENU,
				'show_in_rest'    => true,
				'menu_icon'       => 'dashicons-media-document',
				'has_archive'     => false,
				'exclude_from_search' => false,
				'rewrite'         => array(
					'slug'       => 'document',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'page-attributes', 'thumbnail' ),
				'taxonomies'      => array( 'pdo_doc_cat' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);

		register_post_type(
			'pdo_section',
			array(
				'labels'          => self::labels( __( 'Dynamic Section', 'pdo-site-manager' ), __( 'Dynamic Sections', 'pdo-site-manager' ) ),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => self::MENU,
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-layout',
				'has_archive'     => false,
				'rewrite'         => false,
				'supports'        => array( 'title', 'page-attributes' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);

		unset( $cap );
	}

	/**
	 * Build a label array.
	 *
	 * @param string $singular Singular name.
	 * @param string $plural   Plural name.
	 * @return array
	 */
	private static function labels( $singular, $plural ) {
		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'add_new'            => __( 'Add New', 'pdo-site-manager' ),
			/* translators: %s: singular post type name. */
			'add_new_item'       => sprintf( __( 'Add New %s', 'pdo-site-manager' ), $singular ),
			/* translators: %s: singular post type name. */
			'edit_item'          => sprintf( __( 'Edit %s', 'pdo-site-manager' ), $singular ),
			/* translators: %s: singular post type name. */
			'new_item'           => sprintf( __( 'New %s', 'pdo-site-manager' ), $singular ),
			/* translators: %s: singular post type name. */
			'view_item'          => sprintf( __( 'View %s', 'pdo-site-manager' ), $singular ),
			/* translators: %s: plural post type name. */
			'search_items'       => sprintf( __( 'Search %s', 'pdo-site-manager' ), $plural ),
			/* translators: %s: plural post type name. */
			'not_found'          => sprintf( __( 'No %s found.', 'pdo-site-manager' ), strtolower( $plural ) ),
			/* translators: %s: plural post type name. */
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'pdo-site-manager' ), strtolower( $plural ) ),
			/* translators: %s: plural post type name. */
			'all_items'          => $plural,
		);
	}

	/**
	 * Friendlier title placeholders.
	 *
	 * @param string  $text Placeholder.
	 * @param WP_Post $post Post.
	 * @return string
	 */
	public function title_placeholder( $text, $post ) {
		switch ( $post->post_type ) {
			case 'pdo_team':
				return __( 'Full name', 'pdo-site-manager' );
			case 'pdo_partner':
				return __( 'Partner name', 'pdo-site-manager' );
			case 'pdo_program':
				return __( 'Program name', 'pdo-site-manager' );
			case 'pdo_story':
				return __( 'Story title', 'pdo-site-manager' );
			case 'pdo_document':
				return __( 'Document title', 'pdo-site-manager' );
			case 'pdo_section':
				return __( 'Section title (admin reference)', 'pdo-site-manager' );
			default:
				return $text;
		}
	}

	/**
	 * Default ordering for PDO content (menu_order then title).
	 *
	 * @param WP_Query $query Query.
	 * @return void
	 */
	public function default_ordering( $query ) {
		$types = array( 'pdo_program', 'pdo_team', 'pdo_partner', 'pdo_story', 'pdo_document', 'pdo_section' );
		$pt    = $query->get( 'post_type' );
		if ( ! $pt || ( is_array( $pt ) && ! array_intersect( $pt, $types ) ) || ( is_string( $pt ) && ! in_array( $pt, $types, true ) ) ) {
			return;
		}
		if ( $query->get( 'orderby' ) ) {
			return;
		}
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}

	/**
	 * Add an order column to the list tables.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function order_column( $columns ) {
		$columns['pdo_order']   = __( 'Order', 'pdo-site-manager' );
		$columns['pdo_visible'] = __( 'Visible', 'pdo-site-manager' );
		return $columns;
	}

	/**
	 * Render custom columns.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function render_column( $column, $post_id ) {
		if ( 'pdo_order' === $column ) {
			echo (int) get_post_field( 'menu_order', $post_id );
		} elseif ( 'pdo_visible' === $column ) {
			$hidden = get_post_meta( $post_id, '_pdo_hidden', true );
			echo $hidden ? '<span class="pdo-dot pdo-dot--off">' . esc_html__( 'Hidden', 'pdo-site-manager' ) . '</span>' : '<span class="pdo-dot pdo-dot--on">' . esc_html__( 'Visible', 'pdo-site-manager' ) . '</span>';
		}
	}
}
