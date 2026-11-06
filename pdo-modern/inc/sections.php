<?php
/**
 * Homepage section ordering plus the dynamic section renderer.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ordered, enabled homepage sections.
 *
 * @return array<string>
 */
function pdo_modern_home_section_order() {
	$stored = (string) get_theme_mod( 'pdo_home_order', pdo_modern_default_home_order() );
	$known  = array_keys( pdo_modern_home_sections() );
	$order  = array();

	foreach ( array_filter( array_map( 'trim', explode( ',', $stored ) ) ) as $slug ) {
		if ( 0 === strpos( $slug, '_' ) ) {
			continue; // Disabled in the Customizer.
		}
		if ( in_array( $slug, $known, true ) && ! in_array( $slug, $order, true ) ) {
			$order[] = $slug;
		}
	}

	foreach ( $known as $slug ) {
		if ( ! in_array( $slug, $order, true ) && false === strpos( $stored, '_' . $slug ) ) {
			$order[] = $slug;
		}
	}

	/**
	 * Filter the homepage section order.
	 *
	 * @param array $order Section keys.
	 */
	return apply_filters( 'pdo_modern_home_section_order', $order );
}

/**
 * Render one homepage section.
 *
 * @param string $key Section key.
 * @return void
 */
function pdo_modern_render_home_section( $key ) {
	$key = sanitize_key( $key );

	if ( 'all' === get_theme_mod( 'pdo_section_hide_' . $key, '' ) ) {
		return;
	}
	if ( ! pdo_section_enabled( 'home', $key ) ) {
		return;
	}

	get_template_part( 'template-parts/sections/home', $key );
}

/**
 * Render every dynamic section registered for a location + placement.
 *
 * @param string $location  Location key.
 * @param string $placement before|after.
 * @return void
 */
function pdo_modern_dynamic_sections( $location, $placement = 'after' ) {
	$sections = pdo_sections( $location, $placement );
	if ( ! $sections ) {
		return;
	}

	foreach ( $sections as $section ) {
		pdo_modern_render_dynamic_section( $section );
	}
}

/**
 * Render one dynamic section.
 *
 * @param array $section Prepared section data.
 * @return void
 */
function pdo_modern_render_dynamic_section( $section ) {
	if ( empty( $section['type'] ) ) {
		return;
	}

	$type = sanitize_key( $section['type'] );

	$hidden = array();
	if ( ! empty( $section['hide_desktop'] ) ) {
		$hidden[] = 'pdo-hide-desktop';
	}
	if ( ! empty( $section['hide_tablet'] ) ) {
		$hidden[] = 'pdo-hide-tablet';
	}
	if ( ! empty( $section['hide_mobile'] ) ) {
		$hidden[] = 'pdo-hide-mobile';
	}

	$section['_visibility'] = implode( ' ', $hidden );

	/**
	 * Allow plugins/child themes to take over the rendering of a section type.
	 *
	 * @param bool   $handled Whether the section was already rendered.
	 * @param array  $section Section data.
	 * @param string $type    Section type.
	 */
	if ( apply_filters( 'pdo_modern_pre_render_section', false, $section, $type ) ) {
		return;
	}

	set_query_var( 'pdo_section', $section );
	$located = locate_template( 'template-parts/sections/dynamic-' . $type . '.php' );

	if ( $located ) {
		get_template_part( 'template-parts/sections/dynamic', $type, array( 'section' => $section ) );
	} else {
		get_template_part( 'template-parts/sections/dynamic', 'fallback', array( 'section' => $section ) );
	}
}

/**
 * Read a bilingual value from a prepared dynamic section.
 *
 * @param array  $section Section.
 * @param string $key     Key.
 * @param string $default Fallback.
 * @return string
 */
function pdo_modern_section_text( $section, $key, $default = '' ) {
	if ( 'bn' === pdo_lang() && ! empty( $section[ $key . '_bn' ] ) ) {
		return (string) $section[ $key . '_bn' ];
	}
	return isset( $section[ $key ] ) && '' !== $section[ $key ] ? (string) $section[ $key ] : $default;
}

/**
 * Grid column class for a configured column count.
 *
 * @param mixed $columns Column count.
 * @param int   $default Default.
 * @return string
 */
function pdo_modern_columns_class( $columns, $default = 3 ) {
	$columns = (int) $columns;
	if ( $columns < 1 || $columns > 6 ) {
		$columns = $default;
	}
	return 'pdo-grid--' . $columns;
}

/**
 * Read the current dynamic section inside a `template-parts/sections/dynamic-*.php` file.
 *
 * @param array $args Template part args.
 * @return array
 */
function pdo_modern_section_args( $args = array() ) {
	if ( isset( $args['section'] ) && is_array( $args['section'] ) ) {
		return $args['section'];
	}
	$section = get_query_var( 'pdo_section' );
	return is_array( $section ) ? $section : array();
}

/**
 * Open the wrapper for a dynamic section.
 *
 * @param array  $section Section data.
 * @param string $class   Extra classes.
 * @return void
 */
function pdo_modern_dynamic_open( $section, $class = '' ) {
	$padding = isset( $section['padding'] ) ? (string) $section['padding'] : '';
	$classes = array( 'pdo-dynamic', 'pdo-dynamic--' . sanitize_html_class( isset( $section['type'] ) ? $section['type'] : 'section' ) );

	if ( $class ) {
		$classes[] = $class;
	}
	if ( 'tight' === $padding || 'small' === $padding ) {
		$classes[] = 'pdo-section--tight';
	}
	if ( 'none' === $padding ) {
		$classes[] = 'pdo-section--flush-top pdo-section--flush-bottom';
	}

	$style = '';
	if ( ! empty( $section['text_color'] ) ) {
		$style = '--pdo-text:' . $section['text_color'] . ';--pdo-heading:' . $section['text_color'];
	}

	pdo_modern_section_open(
		array(
			'id'         => ! empty( $section['id'] ) ? 'pdo-section-' . (int) $section['id'] : '',
			'class'      => implode( ' ', $classes ),
			'background' => isset( $section['background'] ) ? $section['background'] : '',
			'visibility' => isset( $section['_visibility'] ) ? $section['_visibility'] : '',
			'style'      => $style,
		)
	);
}

/**
 * Standard heading block for a dynamic section.
 *
 * @param array $section Section data.
 * @return void
 */
function pdo_modern_dynamic_header( $section ) {
	pdo_modern_section_header(
		array(
			'eyebrow'     => pdo_modern_section_text( $section, 'eyebrow', '' ),
			'title'       => pdo_modern_section_text( $section, 'heading', '' ),
			'description' => pdo_modern_section_text( $section, 'content', '' ),
			'align'       => ! empty( $section['align'] ) ? sanitize_html_class( $section['align'] ) : 'center',
			'icon'        => isset( $section['icon'] ) ? $section['icon'] : '',
		)
	);
}

/**
 * Render the dynamic section button, if one is configured.
 *
 * @param array  $section Section.
 * @param string $style   Button style.
 * @return void
 */
function pdo_modern_dynamic_button( $section, $style = 'primary' ) {
	$label = pdo_modern_section_text( $section, 'btn_label', '' );
	$url   = isset( $section['btn_url'] ) ? (string) $section['btn_url'] : '';

	if ( ! $label || ! $url ) {
		return;
	}

	echo '<p class="pdo-section__cta">';
	pdo_modern_button( $label, $url, array( 'style' => $style, 'icon' => 'arrow-right' ) );
	echo '</p>';
}

/**
 * Rows of a dynamic section, respecting the configured limit.
 *
 * @param array $section Section.
 * @return array
 */
function pdo_modern_dynamic_items( $section ) {
	$items = isset( $section['items'] ) && is_array( $section['items'] ) ? $section['items'] : array();
	$limit = isset( $section['limit'] ) ? (int) $section['limit'] : 0;

	if ( $limit > 0 ) {
		$items = array_slice( $items, 0, $limit );
	}

	return $items;
}
