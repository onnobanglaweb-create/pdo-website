<?php
/**
 * Media Library integration helpers.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Media helpers.
 */
class PDO_SM_Media {

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_filter( 'upload_mimes', array( $this, 'allowed_mimes' ) );
		add_action( 'admin_init', array( $this, 'register_image_sizes' ) );
	}

	/**
	 * Allow the document formats PDO publishes.
	 *
	 * @param array $mimes Allowed mimes.
	 * @return array
	 */
	public function allowed_mimes( $mimes ) {
		$mimes['pdf']  = 'application/pdf';
		$mimes['doc']  = 'application/msword';
		$mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		$mimes['xls']  = 'application/vnd.ms-excel';
		$mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		return $mimes;
	}

	/**
	 * Register the image sizes used by the theme-independent renderers.
	 *
	 * @return void
	 */
	public function register_image_sizes() {
		add_image_size( 'pdo-card', 760, 460, true );
		add_image_size( 'pdo-thumb', 420, 300, true );
		add_image_size( 'pdo-portrait', 480, 560, true );
	}

	/**
	 * Build a safe, responsive <img> tag from either an attachment ID or a URL.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $fallback_url  Fallback URL.
	 * @param array  $args          Args: size, class, alt, loading.
	 * @return string
	 */
	public static function image_tag( $attachment_id, $fallback_url = '', $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'size'    => 'pdo-card',
				'class'   => '',
				'alt'     => '',
				'loading' => 'lazy',
				'style'   => '',
			)
		);

		$attachment_id = (int) $attachment_id;

		if ( $attachment_id > 0 && wp_attachment_is_image( $attachment_id ) ) {
			$attrs = array(
				'class'    => $args['class'],
				'loading'  => $args['loading'],
				'decoding' => 'async',
			);
			if ( $args['alt'] ) {
				$attrs['alt'] = $args['alt'];
			}
			if ( $args['style'] ) {
				$attrs['style'] = $args['style'];
			}
			return wp_get_attachment_image( $attachment_id, $args['size'], false, $attrs );
		}

		$fallback_url = trim( (string) $fallback_url );
		if ( '' === $fallback_url ) {
			return '';
		}

		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="%4$s" decoding="async"%5$s />',
			esc_url( $fallback_url ),
			esc_attr( $args['alt'] ),
			esc_attr( $args['class'] ),
			esc_attr( $args['loading'] ),
			$args['style'] ? ' style="' . esc_attr( $args['style'] ) . '"' : ''
		);
	}
}
