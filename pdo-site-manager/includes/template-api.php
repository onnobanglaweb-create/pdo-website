<?php
/**
 * Public template API.
 *
 * Every function here is the contract between the plugin (content) and any
 * theme (presentation). Themes should guard each call with function_exists()
 * so they degrade gracefully when the plugin is deactivated.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'pdo_settings' ) ) {
	/**
	 * Fetch a whole settings group.
	 *
	 * @param string $group Group key.
	 * @return array
	 */
	function pdo_settings( $group ) {
		return PDO_SM_Settings::get_group( $group );
	}
}

if ( ! function_exists( 'pdo_setting' ) ) {
	/**
	 * Fetch a raw setting.
	 *
	 * @param string $group   Group key.
	 * @param string $field   Field key.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	function pdo_setting( $group, $field, $default = '' ) {
		return PDO_SM_Settings::get( $group, $field, $default );
	}
}

if ( ! function_exists( 'pdo_text' ) ) {
	/**
	 * Fetch a bilingual setting in the active language.
	 *
	 * @param string $group   Group key.
	 * @param string $field   Field key.
	 * @param mixed  $default Fallback.
	 * @return string
	 */
	function pdo_text( $group, $field, $default = '' ) {
		return PDO_SM_Settings::text( $group, $field, $default );
	}
}

if ( ! function_exists( 'pdo_rows' ) ) {
	/**
	 * Fetch the visible rows of a repeater, sorted.
	 *
	 * @param string $group Group key.
	 * @param string $field Field key.
	 * @param int    $limit Optional limit (0 = all).
	 * @return array<array>
	 */
	function pdo_rows( $group, $field, $limit = 0 ) {
		$values = PDO_SM_Settings::get_group( $group );
		$rows   = isset( $values[ $field ] ) && is_array( $values[ $field ] ) ? $values[ $field ] : array();
		$rows   = PDO_SM_Helpers::visible_rows( $rows );
		if ( $limit > 0 ) {
			$rows = array_slice( $rows, 0, $limit );
		}
		return $rows;
	}
}

if ( ! function_exists( 'pdo_row_text' ) ) {
	/**
	 * Read a bilingual value from a repeater row.
	 *
	 * @param array  $row     Row.
	 * @param string $field   Key.
	 * @param string $default Fallback.
	 * @return string
	 */
	function pdo_row_text( $row, $field, $default = '' ) {
		return PDO_SM_I18n::field( $row, $field, $default );
	}
}

if ( ! function_exists( 'pdo_section_enabled' ) ) {
	/**
	 * Is a page section switched on?
	 *
	 * @param string $group  Group key.
	 * @param string $prefix Section prefix.
	 * @return bool
	 */
	function pdo_section_enabled( $group, $prefix ) {
		$values = PDO_SM_Settings::get_group( $group );
		$key    = $prefix . '_enabled';
		if ( ! array_key_exists( $key, $values ) ) {
			return true;
		}
		return PDO_SM_Helpers::to_bool( $values[ $key ] );
	}
}

if ( ! function_exists( 'pdo_org' ) ) {
	/**
	 * Organisation settings.
	 *
	 * @param string $field   Optional field.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	function pdo_org( $field = '', $default = '' ) {
		if ( '' === $field ) {
			return PDO_SM_Settings::get_group( 'org' );
		}
		return PDO_SM_Settings::text( 'org', $field, $default );
	}
}

if ( ! function_exists( 'pdo_meta' ) ) {
	/**
	 * Read a PDO post meta value in the active language.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key without the `_pdo_` prefix.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	function pdo_meta( $post_id, $key, $default = '' ) {
		return PDO_SM_I18n::meta( $post_id, $key, $default );
	}
}

if ( ! function_exists( 'pdo_raw_meta' ) ) {
	/**
	 * Read a PDO post meta value without language resolution.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key without the `_pdo_` prefix.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	function pdo_raw_meta( $post_id, $key, $default = '' ) {
		$value = get_post_meta( $post_id, '_pdo_' . $key, true );
		if ( '' === $value || null === $value || array() === $value ) {
			return $default;
		}
		return $value;
	}
}

if ( ! function_exists( 'pdo_meta_rows' ) ) {
	/**
	 * Read a repeater stored in post meta.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key without the `_pdo_` prefix.
	 * @return array<array>
	 */
	function pdo_meta_rows( $post_id, $key ) {
		$rows = get_post_meta( $post_id, '_pdo_' . $key, true );
		if ( ! is_array( $rows ) ) {
			return array();
		}
		return PDO_SM_Helpers::visible_rows( $rows );
	}
}

if ( ! function_exists( 'pdo_get_posts' ) ) {
	/**
	 * Fetch PDO records with the house ordering defaults.
	 *
	 * @param string $post_type Post type.
	 * @param array  $args      Extra WP_Query args.
	 * @return WP_Post[]
	 */
	function pdo_get_posts( $post_type, $args = array() ) {
		$defaults = array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => 12,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'ignore_sticky_posts'    => true,
		);

		if ( 'pdo_news' === $post_type ) {
			$defaults['orderby']                = array( 'date' => 'DESC' );
			$defaults['update_post_term_cache'] = true;
		}

		$query = new WP_Query( wp_parse_args( $args, $defaults ) );

		return $query->posts;
	}
}

if ( ! function_exists( 'pdo_sections' ) ) {
	/**
	 * Fetch the dynamic sections for a location.
	 *
	 * @param string $location  Location key.
	 * @param string $placement before|after.
	 * @return array<array>
	 */
	function pdo_sections( $location, $placement = 'after' ) {
		if ( ! class_exists( 'PDO_SM_Sections' ) ) {
			return array();
		}
		return PDO_SM_Sections::get( $location, $placement );
	}
}

if ( ! function_exists( 'pdo_document_file' ) ) {
	/**
	 * Resolve the real downloadable file of a document record.
	 *
	 * Returns an empty array when no file has been uploaded, so templates can
	 * hide the download button instead of offering a broken link.
	 *
	 * @param int $post_id Document ID.
	 * @return array{url:string,size:string,mime:string,ext:string,id:int}|array{}
	 */
	function pdo_document_file( $post_id ) {
		$attachment_id = (int) get_post_meta( $post_id, '_pdo_file', true );
		$url           = '';

		if ( $attachment_id > 0 ) {
			$url = (string) wp_get_attachment_url( $attachment_id );
		}

		if ( ! $url ) {
			$external = (string) get_post_meta( $post_id, '_pdo_external_url', true );
			if ( ! $external ) {
				$external = (string) get_post_meta( $post_id, '_pdo_file_url', true );
			}
			if ( $external ) {
				return array(
					'id'   => 0,
					'url'  => esc_url_raw( $external ),
					'size' => '',
					'mime' => '',
					'ext'  => strtoupper( (string) pathinfo( wp_parse_url( $external, PHP_URL_PATH ), PATHINFO_EXTENSION ) ),
				);
			}
			return array();
		}

		$path = get_attached_file( $attachment_id );
		$size = '';
		if ( $path && file_exists( $path ) ) {
			$size = size_format( (int) filesize( $path ), 1 );
		}

		return array(
			'id'   => $attachment_id,
			'url'  => $url,
			'size' => $size,
			'mime' => (string) get_post_mime_type( $attachment_id ),
			'ext'  => strtoupper( (string) pathinfo( $url, PATHINFO_EXTENSION ) ),
		);
	}
}

if ( ! function_exists( 'pdo_form_config' ) ) {
	/**
	 * Fetch a front-end form definition.
	 *
	 * @param string $type contact|volunteer|partner.
	 * @return array|null
	 */
	function pdo_form_config( $type ) {
		return PDO_SM_Forms::filtered_config( $type );
	}
}

if ( ! function_exists( 'pdo_form_result' ) ) {
	/**
	 * Result of the previous submission of a form, if any.
	 *
	 * @param string $type Form type.
	 * @return array|null
	 */
	function pdo_form_result( $type ) {
		return PDO_SM_Forms::result( $type );
	}
}

if ( ! function_exists( 'pdo_form_field_name' ) ) {
	/**
	 * Input name of a configured form field.
	 *
	 * @param array $field Field row.
	 * @param int   $index Index.
	 * @return string
	 */
	function pdo_form_field_name( $field, $index = 0 ) {
		return PDO_SM_Forms::field_name( $field, $index );
	}
}

if ( ! function_exists( 'pdo_form_field_choices' ) ) {
	/**
	 * Choices of a select / radio form field.
	 *
	 * @param array $field Field row.
	 * @return array<string>
	 */
	function pdo_form_field_choices( $field ) {
		return PDO_SM_Forms::choices( $field );
	}
}

if ( ! function_exists( 'pdo_page_url' ) ) {
	/**
	 * Permalink of one of the managed pages.
	 *
	 * @param string $key home|about|impact|transparency|media|involved|contact.
	 * @return string
	 */
	function pdo_page_url( $key ) {
		$pages = get_option( 'pdo_sm_pages', array() );
		if ( is_array( $pages ) && ! empty( $pages[ $key ] ) ) {
			$url = get_permalink( (int) $pages[ $key ] );
			if ( $url ) {
				return $url;
			}
		}

		$blueprint = PDO_SM_Importer::pages();
		if ( isset( $blueprint[ $key ] ) ) {
			$page = get_page_by_path( $blueprint[ $key ]['slug'] );
			if ( $page ) {
				return (string) get_permalink( $page );
			}
		}

		return home_url( '/' );
	}
}

if ( ! function_exists( 'pdo_image' ) ) {
	/**
	 * Render an image from an attachment ID with a URL fallback.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $fallback_url  Fallback URL.
	 * @param array  $args          Args.
	 * @return string
	 */
	function pdo_image( $attachment_id, $fallback_url = '', $args = array() ) {
		return PDO_SM_Media::image_tag( $attachment_id, $fallback_url, $args );
	}
}

if ( ! function_exists( 'pdo_row_image' ) ) {
	/**
	 * Render the image of a repeater row (`image` id + `image_url` fallback).
	 *
	 * @param array  $row    Row.
	 * @param array  $args   Args passed to pdo_image().
	 * @param string $prefix Field prefix, default `image`.
	 * @return string
	 */
	function pdo_row_image( $row, $args = array(), $prefix = 'image' ) {
		$id  = isset( $row[ $prefix ] ) ? (int) $row[ $prefix ] : 0;
		$url = isset( $row[ $prefix . '_url' ] ) ? (string) $row[ $prefix . '_url' ] : '';
		if ( empty( $args['alt'] ) ) {
			$args['alt'] = PDO_SM_I18n::field( $row, $prefix . '_alt', isset( $row['title'] ) ? wp_strip_all_tags( (string) $row['title'] ) : '' );
		}
		return PDO_SM_Media::image_tag( $id, $url, $args );
	}
}

if ( ! function_exists( 'pdo_post_image' ) ) {
	/**
	 * Render the image of a PDO record: featured image, then `_pdo_{prefix}_url`.
	 *
	 * @param int    $post_id Post ID.
	 * @param array  $args    Args.
	 * @param string $prefix  Meta prefix (image|photo).
	 * @return string
	 */
	function pdo_post_image( $post_id, $args = array(), $prefix = 'image' ) {
		$attachment_id = (int) get_post_thumbnail_id( $post_id );
		if ( ! $attachment_id ) {
			$attachment_id = (int) get_post_meta( $post_id, '_pdo_' . $prefix, true );
		}
		$url = (string) get_post_meta( $post_id, '_pdo_' . $prefix . '_url', true );
		if ( empty( $args['alt'] ) ) {
			$args['alt'] = (string) get_post_meta( $post_id, '_pdo_' . $prefix . '_alt', true );
			if ( ! $args['alt'] ) {
				$args['alt'] = get_the_title( $post_id );
			}
		}
		return PDO_SM_Media::image_tag( $attachment_id, $url, $args );
	}
}

if ( ! function_exists( 'pdo_scholarship_chart' ) ) {
	/**
	 * Scholarship tier data used by the donut chart.
	 *
	 * @return array{rows:array,total:int}
	 */
	function pdo_scholarship_chart() {
		$palette = array( '#0d7a53', '#14b8a6', '#f59e0b', '#d97706', '#084c34', '#334155' );
		$rows    = pdo_rows( 'impact', 'tiers' );
		$total   = 0;
		$output  = array();

		foreach ( $rows as $index => $row ) {
			$value = PDO_SM_Helpers::to_int( isset( $row['students'] ) ? $row['students'] : 0 );
			if ( $value <= 0 ) {
				continue;
			}
			$color = PDO_SM_Helpers::sanitize_color( isset( $row['color'] ) ? $row['color'] : '' );
			if ( ! $color ) {
				$color = $palette[ $index % count( $palette ) ];
			}
			$total   += $value;
			$output[] = array(
				'label'       => PDO_SM_I18n::field( $row, 'title', '' ),
				'percentage'  => isset( $row['percentage'] ) ? (string) $row['percentage'] : '',
				'count_label' => PDO_SM_I18n::field( $row, 'students_label', '' ),
				'description' => PDO_SM_I18n::field( $row, 'description', '' ),
				'value'       => $value,
				'color'       => $color,
			);
		}

		foreach ( $output as $index => $row ) {
			$output[ $index ]['share'] = $total > 0 ? round( ( $row['value'] / $total ) * 100, 1 ) : 0;
		}

		return array(
			'rows'  => $output,
			'total' => $total,
		);
	}
}

if ( ! function_exists( 'pdo_news_categories' ) ) {
	/**
	 * Categories that actually have published articles.
	 *
	 * @return WP_Term[]
	 */
	function pdo_news_categories() {
		$terms = get_terms(
			array(
				'taxonomy'   => 'pdo_news_cat',
				'hide_empty' => true,
			)
		);
		return is_wp_error( $terms ) ? array() : $terms;
	}
}

if ( ! function_exists( 'pdo_related_news' ) ) {
	/**
	 * Related articles for a single news item.
	 *
	 * @param int $post_id Post ID.
	 * @param int $limit   How many.
	 * @return WP_Post[]
	 */
	function pdo_related_news( $post_id, $limit = 3 ) {
		$terms = wp_get_post_terms( $post_id, 'pdo_news_cat', array( 'fields' => 'ids' ) );
		$args  = array(
			'posts_per_page' => $limit,
			'post__not_in'   => array( $post_id ),
		);

		if ( $terms && ! is_wp_error( $terms ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'pdo_news_cat',
					'field'    => 'term_id',
					'terms'    => $terms,
				),
			);
		}

		$related = pdo_get_posts( 'pdo_news', $args );

		if ( count( $related ) < $limit ) {
			$fill = pdo_get_posts(
				'pdo_news',
				array(
					'posts_per_page' => $limit - count( $related ),
					'post__not_in'   => array_merge( array( $post_id ), wp_list_pluck( $related, 'ID' ) ),
				)
			);
			$related = array_merge( $related, $fill );
		}

		return $related;
	}
}

if ( ! function_exists( 'pdo_share_links' ) ) {
	/**
	 * Build share URLs for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string,array{label:string,url:string,icon:string}>
	 */
	function pdo_share_links( $post_id ) {
		$url   = rawurlencode( (string) get_permalink( $post_id ) );
		$title = rawurlencode( wp_strip_all_tags( get_the_title( $post_id ) ) );

		return array(
			'facebook' => array(
				'label' => __( 'Share on Facebook', 'pdo-site-manager' ),
				'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
				'icon'  => 'facebook',
			),
			'x'        => array(
				'label' => __( 'Share on X', 'pdo-site-manager' ),
				'url'   => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
				'icon'  => 'twitter',
			),
			'linkedin' => array(
				'label' => __( 'Share on LinkedIn', 'pdo-site-manager' ),
				'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
				'icon'  => 'linkedin',
			),
			'whatsapp' => array(
				'label' => __( 'Share on WhatsApp', 'pdo-site-manager' ),
				'url'   => 'https://wa.me/?text=' . $title . '%20' . $url,
				'icon'  => 'whatsapp',
			),
			'email'    => array(
				'label' => __( 'Share by email', 'pdo-site-manager' ),
				'url'   => 'mailto:?subject=' . $title . '&body=' . $url,
				'icon'  => 'mail',
			),
		);
	}
}

if ( ! function_exists( 'pdo_team_groups' ) ) {
	/**
	 * Team members grouped by their team group term.
	 *
	 * @return array<array{term:WP_Term,members:WP_Post[]}>
	 */
	function pdo_team_groups() {
		$terms = get_terms(
			array(
				'taxonomy'   => 'pdo_team_group',
				'hide_empty' => true,
				'orderby'    => 'meta_value_num',
				'meta_key'   => '_pdo_order', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || ! $terms ) {
			return array();
		}

		$groups = array();
		foreach ( $terms as $term ) {
			$members = pdo_get_posts(
				'pdo_team',
				array(
					'posts_per_page' => 50,
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						array(
							'taxonomy' => 'pdo_team_group',
							'field'    => 'term_id',
							'terms'    => $term->term_id,
						),
					),
				)
			);
			if ( $members ) {
				$groups[] = array(
					'term'    => $term,
					'members' => $members,
				);
			}
		}

		return $groups;
	}
}

if ( ! function_exists( 'pdo_post_title' ) ) {
	/**
	 * Language-aware post title.
	 *
	 * @param int|WP_Post $post Post.
	 * @return string
	 */
	function pdo_post_title( $post ) {
		return PDO_SM_I18n::post_title( $post );
	}
}

if ( ! function_exists( 'pdo_post_content' ) ) {
	/**
	 * Language-aware post content (already filtered through the_content).
	 *
	 * @param int|WP_Post $post Post.
	 * @return string
	 */
	function pdo_post_content( $post ) {
		return PDO_SM_I18n::post_content( $post );
	}
}

if ( ! function_exists( 'pdo_post_excerpt' ) ) {
	/**
	 * Language-aware post excerpt.
	 *
	 * @param int|WP_Post $post Post.
	 * @return string
	 */
	function pdo_post_excerpt( $post ) {
		return PDO_SM_I18n::post_excerpt( $post );
	}
}

if ( ! function_exists( 'pdo_visible_rows' ) ) {
	/**
	 * Filter + sort repeater rows the way the admin ordered them.
	 *
	 * @param mixed $rows Rows.
	 * @return array
	 */
	function pdo_visible_rows( $rows ) {
		return PDO_SM_Helpers::visible_rows( is_array( $rows ) ? $rows : array() );
	}
}
