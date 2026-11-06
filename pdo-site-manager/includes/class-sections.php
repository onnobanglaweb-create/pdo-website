<?php
/**
 * Dynamic section builder.
 *
 * Administrators can add structured, reusable sections to supported pages
 * without writing code. Sections are stored as `pdo_section` posts and
 * rendered by the active theme through reusable template renderers.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sections controller.
 */
class PDO_SM_Sections {

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_filter( 'manage_pdo_section_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_pdo_section_posts_custom_column', array( $this, 'column' ), 10, 2 );
		add_action( 'admin_post_pdo_sm_duplicate_section', array( $this, 'duplicate' ) );
	}

	/**
	 * Supported section types.
	 *
	 * @return array<string,string>
	 */
	public static function types() {
		return array(
			'hero'          => __( 'Hero', 'pdo-site-manager' ),
			'heading'       => __( 'Heading and description', 'pdo-site-manager' ),
			'text_image'    => __( 'Text and image', 'pdo-site-manager' ),
			'image_text'    => __( 'Image and text', 'pdo-site-manager' ),
			'stats'         => __( 'Statistics', 'pdo-site-manager' ),
			'icon_cards'    => __( 'Icon cards', 'pdo-site-manager' ),
			'image_cards'   => __( 'Image cards', 'pdo-site-manager' ),
			'team'          => __( 'Team members', 'pdo-site-manager' ),
			'partners'      => __( 'Partners', 'pdo-site-manager' ),
			'timeline'      => __( 'Timeline', 'pdo-site-manager' ),
			'testimonials'  => __( 'Testimonials', 'pdo-site-manager' ),
			'gallery'       => __( 'Gallery', 'pdo-site-manager' ),
			'programs'      => __( 'Program listing', 'pdo-site-manager' ),
			'stories'       => __( 'Impact stories', 'pdo-site-manager' ),
			'chart'         => __( 'Chart / infographic', 'pdo-site-manager' ),
			'documents'     => __( 'Document library', 'pdo-site-manager' ),
			'cta'           => __( 'Call to action', 'pdo-site-manager' ),
			'contact_info'  => __( 'Contact information', 'pdo-site-manager' ),
			'form'          => __( 'Form', 'pdo-site-manager' ),
			'map'           => __( 'Map', 'pdo-site-manager' ),
			'rich_text'     => __( 'Rich text', 'pdo-site-manager' ),
		);
	}

	/**
	 * Section placement locations.
	 *
	 * @return array<string,string>
	 */
	public static function locations() {
		return array(
			'home'         => __( 'Homepage', 'pdo-site-manager' ),
			'about'        => __( 'About Us page', 'pdo-site-manager' ),
			'impact'       => __( 'Our Impact page', 'pdo-site-manager' ),
			'transparency' => __( 'Transparency page', 'pdo-site-manager' ),
			'media'        => __( 'Media & Insights page', 'pdo-site-manager' ),
			'involved'     => __( 'Get Involved page', 'pdo-site-manager' ),
			'contact'      => __( 'Contact page', 'pdo-site-manager' ),
			'global_top'   => __( 'Every page (above content)', 'pdo-site-manager' ),
			'global_bottom' => __( 'Every page (below content)', 'pdo-site-manager' ),
		);
	}

	/**
	 * Placement relative to the built-in sections.
	 *
	 * @return array<string,string>
	 */
	public static function placements() {
		return array(
			'after'  => __( 'After the built-in sections', 'pdo-site-manager' ),
			'before' => __( 'Before the built-in sections', 'pdo-site-manager' ),
		);
	}

	/**
	 * Meta schema used by the dynamic section editor.
	 *
	 * @return array
	 */
	public static function meta_schema() {
		return array(
			'setup'   => array(
				'title'  => __( 'Section setup', 'pdo-site-manager' ),
				'fields' => array(
					array(
						'key'     => 'type',
						'type'    => 'select',
						'label'   => __( 'Section type', 'pdo-site-manager' ),
						'options' => self::types(),
						'default' => 'heading',
						'desc'    => __( 'Pick the layout renderer used on the front end.', 'pdo-site-manager' ),
					),
					array(
						'key'     => 'location',
						'type'    => 'select',
						'label'   => __( 'Show on', 'pdo-site-manager' ),
						'options' => self::locations(),
						'default' => 'home',
					),
					array(
						'key'     => 'placement',
						'type'    => 'select',
						'label'   => __( 'Placement', 'pdo-site-manager' ),
						'options' => self::placements(),
						'default' => 'after',
					),
					array(
						'key'       => 'eyebrow',
						'type'      => 'text',
						'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
						'bilingual' => true,
					),
					array(
						'key'       => 'heading',
						'type'      => 'text',
						'label'     => __( 'Section title', 'pdo-site-manager' ),
						'bilingual' => true,
					),
					array(
						'key'       => 'content',
						'type'      => 'richtext',
						'label'     => __( 'Content', 'pdo-site-manager' ),
						'bilingual' => true,
						'rows'      => 8,
					),
					array(
						'key'   => 'image',
						'type'  => 'image',
						'label' => __( 'Image', 'pdo-site-manager' ),
					),
					array(
						'key'   => 'icon',
						'type'  => 'icon',
						'label' => __( 'Icon', 'pdo-site-manager' ),
					),
					array(
						'key'       => 'btn_label',
						'type'      => 'text',
						'label'     => __( 'Button label', 'pdo-site-manager' ),
						'bilingual' => true,
					),
					array(
						'key'   => 'btn_url',
						'type'  => 'url',
						'label' => __( 'Button URL', 'pdo-site-manager' ),
					),
				),
			),
			'items'   => array(
				'title'  => __( 'Section items', 'pdo-site-manager' ),
				'fields' => array(
					array(
						'key'        => 'items',
						'type'       => 'repeater',
						'label'      => __( 'Items', 'pdo-site-manager' ),
						'item_label' => 'title',
						'add_label'  => __( 'Add item', 'pdo-site-manager' ),
						'desc'       => __( 'Used by card, statistic, timeline, gallery, testimonial and chart section types.', 'pdo-site-manager' ),
						'fields'     => array(
							array(
								'key'       => 'title',
								'type'      => 'text',
								'label'     => __( 'Title / value', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'       => 'subtitle',
								'type'      => 'text',
								'label'     => __( 'Subtitle / label', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'       => 'description',
								'type'      => 'textarea',
								'label'     => __( 'Description', 'pdo-site-manager' ),
								'bilingual' => true,
								'rows'      => 3,
							),
							array(
								'key'   => 'image',
								'type'  => 'image',
								'label' => __( 'Image', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'image_url',
								'type'  => 'url',
								'label' => __( 'Or image URL', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'alt',
								'type'  => 'text',
								'label' => __( 'Alt text', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'icon',
								'type'  => 'icon',
								'label' => __( 'Icon', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'value',
								'type'  => 'text',
								'label' => __( 'Numeric value (statistics & charts)', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'color',
								'type'  => 'color',
								'label' => __( 'Colour', 'pdo-site-manager' ),
							),
							array(
								'key'       => 'link_label',
								'type'      => 'text',
								'label'     => __( 'Link label', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'   => 'link_url',
								'type'  => 'url',
								'label' => __( 'Link URL', 'pdo-site-manager' ),
							),
						),
					),
				),
			),
			'design'  => array(
				'title'  => __( 'Design & layout', 'pdo-site-manager' ),
				'fields' => array(
					array(
						'key'     => 'background',
						'type'    => 'select',
						'label'   => __( 'Background', 'pdo-site-manager' ),
						'options' => PDO_SM_Schema::background_choices(),
						'default' => '',
					),
					array(
						'key'   => 'text_color',
						'type'  => 'color',
						'label' => __( 'Text colour override', 'pdo-site-manager' ),
					),
					array(
						'key'     => 'columns',
						'type'    => 'select',
						'label'   => __( 'Column arrangement', 'pdo-site-manager' ),
						'options' => array(
							'1' => __( '1 column', 'pdo-site-manager' ),
							'2' => __( '2 columns', 'pdo-site-manager' ),
							'3' => __( '3 columns', 'pdo-site-manager' ),
							'4' => __( '4 columns', 'pdo-site-manager' ),
							'5' => __( '5 columns', 'pdo-site-manager' ),
							'6' => __( '6 columns', 'pdo-site-manager' ),
						),
						'default' => '3',
					),
					array(
						'key'     => 'align',
						'type'    => 'select',
						'label'   => __( 'Alignment', 'pdo-site-manager' ),
						'options' => PDO_SM_Schema::align_choices(),
						'default' => 'left',
					),
					array(
						'key'     => 'padding',
						'type'    => 'select',
						'label'   => __( 'Vertical padding', 'pdo-site-manager' ),
						'options' => array(
							'none'   => __( 'None', 'pdo-site-manager' ),
							'small'  => __( 'Small', 'pdo-site-manager' ),
							'normal' => __( 'Normal', 'pdo-site-manager' ),
							'large'  => __( 'Large', 'pdo-site-manager' ),
						),
						'default' => 'normal',
					),
					array(
						'key'     => 'limit',
						'type'    => 'number',
						'label'   => __( 'Maximum records (dynamic listings)', 'pdo-site-manager' ),
						'default' => 6,
						'min'     => 1,
						'max'     => 60,
					),
				),
			),
			'display' => array(
				'title'   => __( 'Visibility', 'pdo-site-manager' ),
				'context' => 'side',
				'fields'  => array(
					array(
						'key'     => 'hidden',
						'type'    => 'checkbox',
						'label'   => __( 'Hide this section', 'pdo-site-manager' ),
						'checkbox_label' => __( 'Hidden', 'pdo-site-manager' ),
						'default' => 0,
					),
					array(
						'key'     => 'hide_desktop',
						'type'    => 'checkbox',
						'label'   => __( 'Hide on desktop', 'pdo-site-manager' ),
						'checkbox_label' => __( 'Hidden on desktop', 'pdo-site-manager' ),
						'default' => 0,
					),
					array(
						'key'     => 'hide_tablet',
						'type'    => 'checkbox',
						'label'   => __( 'Hide on tablet', 'pdo-site-manager' ),
						'checkbox_label' => __( 'Hidden on tablet', 'pdo-site-manager' ),
						'default' => 0,
					),
					array(
						'key'     => 'hide_mobile',
						'type'    => 'checkbox',
						'label'   => __( 'Hide on mobile', 'pdo-site-manager' ),
						'checkbox_label' => __( 'Hidden on mobile', 'pdo-site-manager' ),
						'default' => 0,
					),
				),
			),
		);
	}

	/**
	 * Fetch sections for a location.
	 *
	 * @param string $location  Location key.
	 * @param string $placement `before` or `after`.
	 * @return array<int,array>
	 */
	public static function get( $location, $placement = 'after' ) {
		$cache_key = 'pdo_sections_' . $location . '_' . $placement;
		$cached    = wp_cache_get( $cache_key, 'pdo_sm' );
		if ( false !== $cached ) {
			return $cached;
		}

		$query = new WP_Query(
			array(
				'post_type'              => 'pdo_section',
				'post_status'            => 'publish',
				'posts_per_page'         => 50,
				'orderby'                => array( 'menu_order' => 'ASC' ),
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small, indexed dataset.
					'relation' => 'AND',
					array(
						'key'   => '_pdo_location',
						'value' => $location,
					),
					array(
						'key'   => '_pdo_placement',
						'value' => $placement,
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => '_pdo_hidden',
							'compare' => 'NOT EXISTS',
						),
						array(
							'key'   => '_pdo_hidden',
							'value' => '0',
						),
					),
				),
			)
		);

		$out = array();
		foreach ( $query->posts as $post ) {
			$out[] = self::prepare( $post );
		}
		wp_reset_postdata();

		wp_cache_set( $cache_key, $out, 'pdo_sm', MINUTE_IN_SECONDS * 5 );

		return $out;
	}

	/**
	 * Normalise a section post into a render-ready array.
	 *
	 * @param WP_Post $post Post.
	 * @return array
	 */
	public static function prepare( $post ) {
		$keys = array(
			'type',
			'location',
			'placement',
			'eyebrow',
			'heading',
			'content',
			'image',
			'icon',
			'btn_label',
			'btn_url',
			'background',
			'text_color',
			'columns',
			'align',
			'padding',
			'limit',
			'hidden',
			'hide_desktop',
			'hide_tablet',
			'hide_mobile',
		);

		$data = array(
			'id'    => $post->ID,
			'title' => $post->post_title,
		);

		foreach ( $keys as $key ) {
			$data[ $key ] = get_post_meta( $post->ID, '_pdo_' . $key, true );
			$bn           = get_post_meta( $post->ID, '_pdo_' . $key . '_bn', true );
			if ( '' !== $bn ) {
				$data[ $key . '_bn' ] = $bn;
			}
		}

		$items         = get_post_meta( $post->ID, '_pdo_items', true );
		$data['items'] = is_array( $items ) ? PDO_SM_Helpers::visible_rows( $items ) : array();

		return $data;
	}

	/**
	 * Section list table columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['pdo_type']     = __( 'Type', 'pdo-site-manager' );
				$new['pdo_location'] = __( 'Location', 'pdo-site-manager' );
			}
		}
		return $new;
	}

	/**
	 * Render the custom columns.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function column( $column, $post_id ) {
		if ( 'pdo_type' === $column ) {
			$types = self::types();
			$type  = get_post_meta( $post_id, '_pdo_type', true );
			echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : '—' );
		} elseif ( 'pdo_location' === $column ) {
			$locations = self::locations();
			$location  = get_post_meta( $post_id, '_pdo_location', true );
			echo esc_html( isset( $locations[ $location ] ) ? $locations[ $location ] : '—' );
			$duplicate = wp_nonce_url(
				admin_url( 'admin-post.php?action=pdo_sm_duplicate_section&post=' . (int) $post_id ),
				'pdo_sm_duplicate_section_' . $post_id
			);
			echo '<br /><a href="' . esc_url( $duplicate ) . '">' . esc_html__( 'Duplicate', 'pdo-site-manager' ) . '</a>';
		}
	}

	/**
	 * Duplicate a section.
	 *
	 * @return void
	 */
	public function duplicate() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		check_admin_referer( 'pdo_sm_duplicate_section_' . $post_id );

		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'You cannot duplicate this section.', 'pdo-site-manager' ), 403 );
		}

		$post = get_post( $post_id );
		if ( ! $post || 'pdo_section' !== $post->post_type ) {
			wp_die( esc_html__( 'Section not found.', 'pdo-site-manager' ), 404 );
		}

		$new_id = wp_insert_post(
			array(
				'post_type'   => 'pdo_section',
				/* translators: %s: original section title. */
				'post_title'  => sprintf( __( '%s (copy)', 'pdo-site-manager' ), $post->post_title ),
				'post_status' => 'draft',
				'menu_order'  => $post->menu_order,
			),
			true
		);

		if ( is_wp_error( $new_id ) ) {
			wp_die( esc_html( $new_id->get_error_message() ) );
		}

		foreach ( get_post_meta( $post_id ) as $key => $values ) {
			if ( 0 !== strpos( $key, '_pdo_' ) ) {
				continue;
			}
			foreach ( $values as $value ) {
				update_post_meta( $new_id, $key, maybe_unserialize( $value ) );
			}
		}

		wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
		exit;
	}
}
