<?php
/**
 * Meta boxes for the PDO content types.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta box controller.
 */
class PDO_SM_Meta_Boxes {

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Meta field definitions per post type.
	 *
	 * @param string $post_type Post type.
	 * @return array
	 */
	public static function schema( $post_type ) {
		$shared_visibility = array(
			array(
				'key'     => 'hidden',
				'type'    => 'checkbox',
				'label'   => __( 'Hide from the website', 'pdo-site-manager' ),
				'checkbox_label' => __( 'Hidden', 'pdo-site-manager' ),
				'default' => 0,
			),
		);

		$translation = array(
			array(
				'key'   => 'title_bn',
				'type'  => 'text',
				'label' => __( 'Title (বাংলা)', 'pdo-site-manager' ),
			),
			array(
				'key'   => 'excerpt_bn',
				'type'  => 'textarea',
				'label' => __( 'Excerpt (বাংলা)', 'pdo-site-manager' ),
				'rows'  => 3,
			),
			array(
				'key'   => 'content_bn',
				'type'  => 'richtext',
				'label' => __( 'Content (বাংলা)', 'pdo-site-manager' ),
				'rows'  => 10,
			),
		);

		switch ( $post_type ) {
			case 'pdo_program':
				return array(
					'details' => array(
						'title'  => __( 'Program details', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'       => 'short_name',
								'type'      => 'text',
								'label'     => __( 'Short name', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'   => 'icon',
								'type'  => 'icon',
								'label' => __( 'Icon', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'image_alt',
								'type'  => 'text',
								'label' => __( 'Featured image alt text', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'image_url',
								'type'  => 'url',
								'label' => __( 'Fallback image URL', 'pdo-site-manager' ),
								'desc'  => __( 'Used only when no featured image is set.', 'pdo-site-manager' ),
							),
							array(
								'key'       => 'sdgs',
								'type'      => 'text',
								'label'     => __( 'SDG references', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'       => 'results',
								'type'      => 'textarea',
								'label'     => __( 'Results summary', 'pdo-site-manager' ),
								'bilingual' => true,
								'rows'      => 3,
							),
							array(
								'key'       => 'cta_label',
								'type'      => 'text',
								'label'     => __( 'Call-to-action label', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'   => 'cta_url',
								'type'  => 'url',
								'label' => __( 'Call-to-action URL', 'pdo-site-manager' ),
							),
							array(
								'key'     => 'accent',
								'type'    => 'select',
								'label'   => __( 'Accent colour', 'pdo-site-manager' ),
								'options' => PDO_SM_Schema::accent_choices(),
								'default' => 'emerald',
							),
							array(
								'key'     => 'featured',
								'type'    => 'checkbox',
								'label'   => __( 'Feature this program', 'pdo-site-manager' ),
								'checkbox_label' => __( 'Featured', 'pdo-site-manager' ),
								'default' => 0,
							),
						),
					),
					'stats'   => array(
						'title'  => __( 'Statistics & achievements', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'        => 'stats',
								'type'       => 'repeater',
								'label'      => __( 'Program statistics', 'pdo-site-manager' ),
								'item_label' => 'label',
								'add_label'  => __( 'Add statistic', 'pdo-site-manager' ),
								'fields'     => array(
									array(
										'key'   => 'value',
										'type'  => 'text',
										'label' => __( 'Value', 'pdo-site-manager' ),
									),
									array(
										'key'       => 'label',
										'type'      => 'text',
										'label'     => __( 'Label', 'pdo-site-manager' ),
										'bilingual' => true,
									),
									array(
										'key'   => 'icon',
										'type'  => 'icon',
										'label' => __( 'Icon', 'pdo-site-manager' ),
									),
								),
							),
							array(
								'key'        => 'achievements',
								'type'       => 'repeater',
								'label'      => __( 'Achievements', 'pdo-site-manager' ),
								'item_label' => 'text',
								'add_label'  => __( 'Add achievement', 'pdo-site-manager' ),
								'fields'     => array(
									array(
										'key'       => 'text',
										'type'      => 'text',
										'label'     => __( 'Achievement', 'pdo-site-manager' ),
										'bilingual' => true,
									),
									array(
										'key'     => 'icon',
										'type'    => 'icon',
										'label'   => __( 'Icon', 'pdo-site-manager' ),
										'default' => 'check-circle',
									),
								),
							),
							array(
								'key'        => 'gallery',
								'type'       => 'repeater',
								'label'      => __( 'Related gallery images', 'pdo-site-manager' ),
								'item_label' => 'caption',
								'add_label'  => __( 'Add image', 'pdo-site-manager' ),
								'fields'     => array(
									array(
										'key'   => 'image',
										'type'  => 'image',
										'label' => __( 'Image', 'pdo-site-manager' ),
									),
									array(
										'key'   => 'alt',
										'type'  => 'text',
										'label' => __( 'Alt text', 'pdo-site-manager' ),
									),
									array(
										'key'       => 'caption',
										'type'      => 'text',
										'label'     => __( 'Caption', 'pdo-site-manager' ),
										'bilingual' => true,
									),
								),
							),
							array(
								'key'        => 'documents',
								'type'       => 'repeater',
								'label'      => __( 'Related documents', 'pdo-site-manager' ),
								'item_label' => 'label',
								'add_label'  => __( 'Add document link', 'pdo-site-manager' ),
								'fields'     => array(
									array(
										'key'       => 'label',
										'type'      => 'text',
										'label'     => __( 'Label', 'pdo-site-manager' ),
										'bilingual' => true,
									),
									array(
										'key'   => 'file',
										'type'  => 'file',
										'label' => __( 'File', 'pdo-site-manager' ),
									),
									array(
										'key'   => 'url',
										'type'  => 'url',
										'label' => __( 'Or external URL', 'pdo-site-manager' ),
									),
								),
							),
						),
					),
					'i18n'    => array(
						'title'  => __( 'Bengali translation', 'pdo-site-manager' ),
						'fields' => $translation,
					),
					'display' => array(
						'title'  => __( 'Display', 'pdo-site-manager' ),
						'fields' => $shared_visibility,
						'context' => 'side',
					),
				);

			case 'pdo_team':
				return array(
					'details' => array(
						'title'  => __( 'Member details', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'       => 'designation',
								'type'      => 'text',
								'label'     => __( 'Designation', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'   => 'photo_url',
								'type'  => 'url',
								'label' => __( 'Fallback photo URL', 'pdo-site-manager' ),
								'desc'  => __( 'Used only when no featured image is set.', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'email',
								'type'  => 'email',
								'label' => __( 'Email (optional)', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'phone',
								'type'  => 'text',
								'label' => __( 'Phone (optional)', 'pdo-site-manager' ),
							),
							array(
								'key'        => 'socials',
								'type'       => 'repeater',
								'label'      => __( 'Social links', 'pdo-site-manager' ),
								'item_label' => 'label',
								'add_label'  => __( 'Add social link', 'pdo-site-manager' ),
								'fields'     => array(
									array(
										'key'   => 'label',
										'type'  => 'text',
										'label' => __( 'Label', 'pdo-site-manager' ),
									),
									array(
										'key'     => 'icon',
										'type'    => 'icon',
										'label'   => __( 'Icon', 'pdo-site-manager' ),
										'default' => 'link',
									),
									array(
										'key'   => 'url',
										'type'  => 'url',
										'label' => __( 'URL', 'pdo-site-manager' ),
									),
								),
							),
						),
					),
					'i18n'    => array(
						'title'  => __( 'Bengali translation', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'   => 'title_bn',
								'type'  => 'text',
								'label' => __( 'Name (বাংলা)', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'content_bn',
								'type'  => 'richtext',
								'label' => __( 'Biography (বাংলা)', 'pdo-site-manager' ),
								'rows'  => 6,
							),
						),
					),
					'display' => array(
						'title'   => __( 'Display', 'pdo-site-manager' ),
						'fields'  => $shared_visibility,
						'context' => 'side',
					),
				);

			case 'pdo_partner':
				return array(
					'details' => array(
						'title'  => __( 'Partner details', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'       => 'description',
								'type'      => 'text',
								'label'     => __( 'Short description', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'   => 'website',
								'type'  => 'url',
								'label' => __( 'Partner website', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'icon',
								'type'  => 'icon',
								'label' => __( 'Icon (used when no logo is set)', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'logo_url',
								'type'  => 'url',
								'label' => __( 'Fallback logo URL', 'pdo-site-manager' ),
							),
							array(
								'key'     => 'accent',
								'type'    => 'select',
								'label'   => __( 'Accent colour', 'pdo-site-manager' ),
								'options' => PDO_SM_Schema::accent_choices(),
								'default' => 'emerald',
							),
						),
					),
					'i18n'    => array(
						'title'  => __( 'Bengali translation', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'   => 'title_bn',
								'type'  => 'text',
								'label' => __( 'Partner name (বাংলা)', 'pdo-site-manager' ),
							),
						),
					),
					'display' => array(
						'title'   => __( 'Display', 'pdo-site-manager' ),
						'fields'  => $shared_visibility,
						'context' => 'side',
					),
				);

			case 'pdo_story':
				return array(
					'details' => array(
						'title'  => __( 'Story details', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'       => 'beneficiary',
								'type'      => 'text',
								'label'     => __( 'Beneficiary name or approved label', 'pdo-site-manager' ),
								'bilingual' => true,
								'desc'      => __( 'Use an anonymous label when the beneficiary has not consented to being named.', 'pdo-site-manager' ),
							),
							array(
								'key'       => 'quote',
								'type'      => 'textarea',
								'label'     => __( 'Quote', 'pdo-site-manager' ),
								'bilingual' => true,
								'rows'      => 3,
							),
							array(
								'key'   => 'photo_url',
								'type'  => 'url',
								'label' => __( 'Fallback photo URL', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'image_alt',
								'type'  => 'text',
								'label' => __( 'Photo alt text', 'pdo-site-manager' ),
							),
							array(
								'key'       => 'location',
								'type'      => 'text',
								'label'     => __( 'Location', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'   => 'story_date',
								'type'  => 'text',
								'label' => __( 'Date', 'pdo-site-manager' ),
							),
							array(
								'key'       => 'tag',
								'type'      => 'text',
								'label'     => __( 'Badge / tag', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'   => 'program',
								'type'  => 'post_select',
								'label' => __( 'Associated program', 'pdo-site-manager' ),
								'post_type' => 'pdo_program',
							),
						),
					),
					'i18n'    => array(
						'title'  => __( 'Bengali translation', 'pdo-site-manager' ),
						'fields' => $translation,
					),
					'display' => array(
						'title'   => __( 'Display', 'pdo-site-manager' ),
						'fields'  => $shared_visibility,
						'context' => 'side',
					),
				);

			case 'pdo_document':
				return array(
					'details' => array(
						'title'  => __( 'Document details', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'       => 'description',
								'type'      => 'textarea',
								'label'     => __( 'Short description', 'pdo-site-manager' ),
								'bilingual' => true,
								'rows'      => 2,
							),
							array(
								'key'   => 'file',
								'type'  => 'file',
								'label' => __( 'Document file (PDF or other)', 'pdo-site-manager' ),
								'desc'  => __( 'Upload the real document. If no file and no external URL is provided, the download button is hidden automatically.', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'external_url',
								'type'  => 'url',
								'label' => __( 'External document URL', 'pdo-site-manager' ),
							),
							array(
								'key'     => 'icon',
								'type'    => 'icon',
								'label'   => __( 'Icon', 'pdo-site-manager' ),
								'default' => 'file-pdf',
							),
							array(
								'key'   => 'year',
								'type'  => 'text',
								'label' => __( 'Publication year', 'pdo-site-manager' ),
							),
							array(
								'key'       => 'download_label',
								'type'      => 'text',
								'label'     => __( 'Download button text', 'pdo-site-manager' ),
								'bilingual' => true,
							),
							array(
								'key'     => 'preview_enabled',
								'type'    => 'checkbox',
								'label'   => __( 'Allow in-page preview', 'pdo-site-manager' ),
								'checkbox_label' => __( 'Preview enabled', 'pdo-site-manager' ),
								'default' => 1,
							),
						),
					),
					'i18n'    => array(
						'title'  => __( 'Bengali translation', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'   => 'title_bn',
								'type'  => 'text',
								'label' => __( 'Document title (বাংলা)', 'pdo-site-manager' ),
							),
							array(
								'key'   => 'content_bn',
								'type'  => 'richtext',
								'label' => __( 'Summary (বাংলা)', 'pdo-site-manager' ),
								'rows'  => 6,
							),
						),
					),
					'display' => array(
						'title'   => __( 'Display', 'pdo-site-manager' ),
						'fields'  => $shared_visibility,
						'context' => 'side',
					),
				);

			case 'pdo_news':
				return array(
					'i18n'    => array(
						'title'  => __( 'Bengali translation', 'pdo-site-manager' ),
						'fields' => $translation,
					),
					'details' => array(
						'title'  => __( 'Article options', 'pdo-site-manager' ),
						'fields' => array(
							array(
								'key'   => 'image_url',
								'type'  => 'url',
								'label' => __( 'Fallback image URL', 'pdo-site-manager' ),
							),
							array(
								'key'       => 'kicker',
								'type'      => 'text',
								'label'     => __( 'Kicker / location line', 'pdo-site-manager' ),
								'bilingual' => true,
							),
						),
					),
					'display' => array(
						'title'   => __( 'Display', 'pdo-site-manager' ),
						'fields'  => $shared_visibility,
						'context' => 'side',
					),
				);

			case 'pdo_section':
				return PDO_SM_Sections::meta_schema();
		}

		return array();
	}

	/**
	 * Register the meta boxes.
	 *
	 * @return void
	 */
	public function register() {
		foreach ( PDO_SM_Content_Types::post_types() as $post_type ) {
			$schema = self::schema( $post_type );
			foreach ( $schema as $box_id => $box ) {
				add_meta_box(
					'pdo_box_' . $box_id,
					$box['title'],
					array( $this, 'render' ),
					$post_type,
					isset( $box['context'] ) ? $box['context'] : 'normal',
					isset( $box['priority'] ) ? $box['priority'] : 'default',
					array(
						'box_id'    => $box_id,
						'post_type' => $post_type,
					)
				);
			}
		}
	}

	/**
	 * Render a meta box.
	 *
	 * @param WP_Post $post Post.
	 * @param array   $args Box args.
	 * @return void
	 */
	public function render( $post, $args ) {
		$box_id = $args['args']['box_id'];
		$schema = self::schema( $post->post_type );
		if ( empty( $schema[ $box_id ]['fields'] ) ) {
			return;
		}

		wp_nonce_field( 'pdo_sm_save_meta_' . $post->ID, 'pdo_sm_meta_nonce' );

		$values = self::get_values( $post->ID, $schema[ $box_id ]['fields'] );

		echo '<div class="pdo-metabox">';
		foreach ( $schema[ $box_id ]['fields'] as $field ) {
			if ( 'post_select' === $field['type'] ) {
				self::render_post_select( $field, $values );
				continue;
			}
			PDO_SM_Fields::render_field( $field, $values, 'pdo_meta' );
		}
		PDO_SM_Fields::render_field_registry( $schema[ $box_id ]['fields'] );
		echo '</div>';
	}

	/**
	 * Render a post relationship dropdown.
	 *
	 * @param array $field  Field definition.
	 * @param array $values Values.
	 * @return void
	 */
	private static function render_post_select( $field, $values ) {
		$value = isset( $values[ $field['key'] ] ) ? (int) $values[ $field['key'] ] : 0;
		$posts = get_posts(
			array(
				'post_type'      => $field['post_type'],
				'posts_per_page' => 200,
				'post_status'    => array( 'publish', 'draft' ),
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		echo '<div class="pdo-field pdo-field--select">';
		echo '<label class="pdo-field__label">' . esc_html( $field['label'] ) . '</label>';
		echo '<select name="pdo_meta[' . esc_attr( $field['key'] ) . ']" class="pdo-input pdo-select">';
		echo '<option value="0">' . esc_html__( '— none —', 'pdo-site-manager' ) . '</option>';
		foreach ( $posts as $p ) {
			printf( '<option value="%1$d" %2$s>%3$s</option>', (int) $p->ID, selected( $value, $p->ID, false ), esc_html( $p->post_title ) );
		}
		echo '</select></div>';
	}

	/**
	 * Read stored values for a set of fields.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $fields  Fields.
	 * @return array
	 */
	public static function get_values( $post_id, $fields ) {
		$values = array();
		foreach ( $fields as $field ) {
			if ( empty( $field['key'] ) ) {
				continue;
			}
			$keys = array( $field['key'] );
			if ( ! empty( $field['bilingual'] ) ) {
				$keys[] = $field['key'] . '_bn';
			}
			foreach ( $keys as $key ) {
				$stored = get_post_meta( $post_id, '_pdo_' . $key, true );
				if ( '' === $stored && isset( $field['default'] ) && ! metadata_exists( 'post', $post_id, '_pdo_' . $key ) ) {
					$stored = $field['default'];
				}
				$values[ $key ] = $stored;
			}
		}
		return $values;
	}

	/**
	 * Save meta box values.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post.
	 * @return void
	 */
	public function save( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! in_array( $post->post_type, PDO_SM_Content_Types::post_types(), true ) ) {
			return;
		}
		if ( ! isset( $_POST['pdo_sm_meta_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pdo_sm_meta_nonce'] ) ), 'pdo_sm_save_meta_' . $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$schema = self::schema( $post->post_type );
		$fields = array();
		foreach ( $schema as $box ) {
			if ( ! empty( $box['fields'] ) ) {
				$fields = array_merge( $fields, $box['fields'] );
			}
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised per field below.
		$raw = isset( $_POST['pdo_meta'] ) ? wp_unslash( $_POST['pdo_meta'] ) : array();
		$raw = is_array( $raw ) ? $raw : array();

		foreach ( $fields as $field ) {
			if ( empty( $field['key'] ) ) {
				continue;
			}
			$keys = array( $field['key'] );
			if ( ! empty( $field['bilingual'] ) ) {
				$keys[] = $field['key'] . '_bn';
			}
			foreach ( $keys as $key ) {
				$type = isset( $field['type'] ) ? $field['type'] : 'text';
				if ( 'post_select' === $type ) {
					$value = isset( $raw[ $key ] ) ? absint( $raw[ $key ] ) : 0;
				} else {
					$value = PDO_SM_Settings::sanitize_field( $field, isset( $raw[ $key ] ) ? $raw[ $key ] : null );
				}
				if ( '' === $value || array() === $value ) {
					delete_post_meta( $post_id, '_pdo_' . $key );
				} else {
					update_post_meta( $post_id, '_pdo_' . $key, $value );
				}
			}
		}
	}
}
