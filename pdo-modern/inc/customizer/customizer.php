<?php
/**
 * Customizer registration.
 *
 * Panels:
 *   PDO Site Identity · PDO Colors · PDO Typography · PDO Layout
 *   PDO Header · PDO Navigation · PDO Footer · PDO Sections
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

require_once PDO_MODERN_DIR . '/inc/customizer/class-pdo-sortable-control.php';
require_once PDO_MODERN_DIR . '/inc/customizer/class-pdo-range-control.php';
require_once PDO_MODERN_DIR . '/inc/customizer/class-pdo-heading-control.php';

/**
 * Colour tokens exposed in the Customizer.
 *
 * key => array( label, default, section, description )
 *
 * @return array<string,array>
 */
function pdo_modern_color_tokens() {
	return array(
		/* Brand ---------------------------------------------------------- */
		'emerald'            => array( __( 'Brand emerald', 'pdo-modern' ), '#0d7a53', 'brand' ),
		'emerald_dark'       => array( __( 'Brand emerald (dark)', 'pdo-modern' ), '#084c34', 'brand' ),
		'emerald_light'      => array( __( 'Brand emerald (light wash)', 'pdo-modern' ), '#e8f5ee', 'brand' ),
		'accent'             => array( __( 'Accent', 'pdo-modern' ), '#d97706', 'brand' ),
		'accent_light'       => array( __( 'Accent (light wash)', 'pdo-modern' ), '#fef3c7', 'brand' ),
		'gold'               => array( __( 'Gold / highlight', 'pdo-modern' ), '#f59e0b', 'brand' ),
		'teal'               => array( __( 'Secondary teal', 'pdo-modern' ), '#14b8a6', 'brand' ),
		'navy'               => array( __( 'Navy', 'pdo-modern' ), '#0f172a', 'brand' ),
		'slate'              => array( __( 'Slate', 'pdo-modern' ), '#334155', 'brand' ),

		/* Text ----------------------------------------------------------- */
		'text'               => array( __( 'Body text', 'pdo-modern' ), '#334155', 'text' ),
		'heading'            => array( __( 'Headings', 'pdo-modern' ), '#0f172a', 'text' ),
		'muted'              => array( __( 'Muted text', 'pdo-modern' ), '#64748b', 'text' ),
		'link'               => array( __( 'Links', 'pdo-modern' ), '#0d7a53', 'text' ),
		'link_hover'         => array( __( 'Links (hover)', 'pdo-modern' ), '#084c34', 'text' ),
		'text_on_dark'       => array( __( 'Text on dark backgrounds', 'pdo-modern' ), '#f8fafc', 'text' ),

		/* Surfaces ------------------------------------------------------- */
		'body_bg'            => array( __( 'Page background', 'pdo-modern' ), '#ffffff', 'surface' ),
		'surface'            => array( __( 'Card surface', 'pdo-modern' ), '#ffffff', 'surface' ),
		'surface_alt'        => array( __( 'Alternate section background', 'pdo-modern' ), '#f8fafc', 'surface' ),
		'surface_tint'       => array( __( 'Tinted section background', 'pdo-modern' ), '#e8f5ee', 'surface' ),
		'surface_dark'       => array( __( 'Dark section background', 'pdo-modern' ), '#084c34', 'surface' ),
		'border'             => array( __( 'Borders', 'pdo-modern' ), '#e2e8f0', 'surface' ),
		'border_strong'      => array( __( 'Strong borders', 'pdo-modern' ), '#cbd5e1', 'surface' ),

		/* Header / footer ------------------------------------------------ */
		'topbar_bg'          => array( __( 'Top bar background', 'pdo-modern' ), '#084c34', 'chrome' ),
		'topbar_text'        => array( __( 'Top bar text', 'pdo-modern' ), '#d1fae5', 'chrome' ),
		'header_bg'          => array( __( 'Header background', 'pdo-modern' ), '#ffffff', 'chrome' ),
		'header_text'        => array( __( 'Header text', 'pdo-modern' ), '#0f172a', 'chrome' ),
		'nav_hover'          => array( __( 'Navigation hover', 'pdo-modern' ), '#0d7a53', 'chrome' ),
		'dropdown_bg'        => array( __( 'Dropdown background', 'pdo-modern' ), '#ffffff', 'chrome' ),
		'footer_bg'          => array( __( 'Footer background', 'pdo-modern' ), '#0f172a', 'chrome' ),
		'footer_text'        => array( __( 'Footer text', 'pdo-modern' ), '#cbd5e1', 'chrome' ),
		'footer_heading'     => array( __( 'Footer headings', 'pdo-modern' ), '#ffffff', 'chrome' ),
		'footer_link_hover'  => array( __( 'Footer link hover', 'pdo-modern' ), '#f59e0b', 'chrome' ),

		/* Feedback ------------------------------------------------------- */
		'success'            => array( __( 'Success', 'pdo-modern' ), '#15803d', 'feedback' ),
		'error'              => array( __( 'Error', 'pdo-modern' ), '#b91c1c', 'feedback' ),
		'warning'            => array( __( 'Warning', 'pdo-modern' ), '#b45309', 'feedback' ),
		'info'               => array( __( 'Information', 'pdo-modern' ), '#0369a1', 'feedback' ),
		'focus'              => array( __( 'Keyboard focus ring', 'pdo-modern' ), '#d97706', 'feedback' ),
	);
}

/**
 * Homepage sections that can be reordered / toggled.
 *
 * @return array<string,string>
 */
function pdo_modern_home_sections() {
	return array(
		'hero'     => __( 'Hero', 'pdo-modern' ),
		'stats'    => __( 'Impact statistics', 'pdo-modern' ),
		'pillars'  => __( 'Core pillars', 'pdo-modern' ),
		'toc'      => __( 'Theory of Change', 'pdo-modern' ),
		'president'=> __( 'President teaser', 'pdo-modern' ),
		'gallery'  => __( 'Photo gallery', 'pdo-modern' ),
		'partners' => __( 'Partners strip', 'pdo-modern' ),
		'cta'      => __( 'Call to action', 'pdo-modern' ),
	);
}

/**
 * Default homepage section order.
 *
 * @return string
 */
function pdo_modern_default_home_order() {
	return implode( ',', array_keys( pdo_modern_home_sections() ) );
}

/**
 * Register everything.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @return void
 */
function pdo_modern_customize_register( $wp_customize ) {

	/* --------------------------------------------------------------------
	 * Core refinements
	 * ----------------------------------------------------------------- */
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->selective_refresh->add_partial(
		'blogname',
		array(
			'selector'        => '.pdo-brand__name',
			'render_callback' => static function () {
				return get_bloginfo( 'name', 'display' );
			},
		)
	);

	/* ====================================================================
	 * PANEL: Site Identity extras
	 * ================================================================= */
	$wp_customize->add_panel(
		'pdo_identity',
		array(
			'title'       => __( 'PDO · Site Identity', 'pdo-modern' ),
			'description' => __( 'Logos for each context, their dimensions, and the compact brand lockup.', 'pdo-modern' ),
			'priority'    => 20,
		)
	);

	$wp_customize->add_section(
		'pdo_logos',
		array(
			'title' => __( 'Logos', 'pdo-modern' ),
			'panel' => 'pdo_identity',
		)
	);

	$logos = array(
		'pdo_logo_dark'    => array( __( 'Logo for dark backgrounds', 'pdo-modern' ), __( 'Used in the footer and on dark hero variants.', 'pdo-modern' ) ),
		'pdo_logo_sticky'  => array( __( 'Compact logo (sticky header)', 'pdo-modern' ), __( 'Falls back to the main logo.', 'pdo-modern' ) ),
		'pdo_logo_mobile'  => array( __( 'Mobile logo', 'pdo-modern' ), __( 'Falls back to the main logo.', 'pdo-modern' ) ),
		'pdo_favicon_hint' => array( __( 'Email / share image', 'pdo-modern' ), __( 'Used as the Open Graph fallback image.', 'pdo-modern' ) ),
	);

	foreach ( $logos as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => '',
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$key,
				array(
					'label'       => $meta[0],
					'description' => $meta[1],
					'section'     => 'pdo_logos',
					'mime_type'   => 'image',
				)
			)
		);
	}

	$dimensions = array(
		'pdo_logo_width'        => array( __( 'Logo width (px)', 'pdo-modern' ), 190, 60, 480 ),
		'pdo_logo_height'       => array( __( 'Logo max height (px)', 'pdo-modern' ), 52, 20, 160 ),
		'pdo_logo_width_mobile' => array( __( 'Logo width on mobile (px)', 'pdo-modern' ), 150, 60, 320 ),
		'pdo_logo_width_footer' => array( __( 'Logo width in the footer (px)', 'pdo-modern' ), 200, 60, 420 ),
	);

	foreach ( $dimensions as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'absint',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new PDO_Range_Control(
				$wp_customize,
				$key,
				array(
					'label'   => $meta[0],
					'section' => 'pdo_logos',
					'min'     => $meta[2],
					'max'     => $meta[3],
					'step'    => 1,
					'unit'    => 'px',
				)
			)
		);
	}

	$wp_customize->add_setting(
		'pdo_show_tagline',
		array(
			'default'           => true,
			'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_show_tagline',
		array(
			'label'   => __( 'Show the tagline next to the logo', 'pdo-modern' ),
			'section' => 'pdo_logos',
			'type'    => 'checkbox',
		)
	);

	/* ====================================================================
	 * PANEL: Colors
	 * ================================================================= */
	$wp_customize->add_panel(
		'pdo_colors',
		array(
			'title'       => __( 'PDO · Colors', 'pdo-modern' ),
			'description' => __( 'Every colour is emitted as a CSS custom property, so templates never hardcode a hex value.', 'pdo-modern' ),
			'priority'    => 21,
		)
	);

	$color_sections = array(
		'brand'    => __( 'Brand palette', 'pdo-modern' ),
		'text'     => __( 'Text & links', 'pdo-modern' ),
		'surface'  => __( 'Surfaces & borders', 'pdo-modern' ),
		'chrome'   => __( 'Header & footer', 'pdo-modern' ),
		'feedback' => __( 'Feedback states', 'pdo-modern' ),
	);

	foreach ( $color_sections as $key => $label ) {
		$wp_customize->add_section(
			'pdo_colors_' . $key,
			array(
				'title' => $label,
				'panel' => 'pdo_colors',
			)
		);
	}

	foreach ( pdo_modern_color_tokens() as $token => $meta ) {
		$setting = 'pdo_color_' . $token;
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting,
				array(
					'label'       => $meta[0],
					'section'     => 'pdo_colors_' . $meta[2],
					'description' => isset( $meta[3] ) ? $meta[3] : '',
				)
			)
		);
	}

	$wp_customize->add_setting(
		'pdo_gradient_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'pdo_gradient_enabled',
		array(
			'label'       => __( 'Use the brand gradient on dark sections', 'pdo-modern' ),
			'section'     => 'pdo_colors_brand',
			'type'        => 'checkbox',
			'description' => __( 'Turn off for a flat, single-colour look.', 'pdo-modern' ),
		)
	);

	/* ====================================================================
	 * PANEL: Typography
	 * ================================================================= */
	$wp_customize->add_panel(
		'pdo_typography',
		array(
			'title'    => __( 'PDO · Typography', 'pdo-modern' ),
			'priority' => 22,
		)
	);

	$wp_customize->add_section(
		'pdo_fonts',
		array(
			'title' => __( 'Font families', 'pdo-modern' ),
			'panel' => 'pdo_typography',
		)
	);

	$font_stacks = pdo_modern_font_stacks();
	$font_labels = wp_list_pluck( $font_stacks, 'label' );

	$wp_customize->add_setting(
		'pdo_font_body',
		array(
			'default'           => 'inter',
			'sanitize_callback' => 'pdo_modern_sanitize_font',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'pdo_font_body',
		array(
			'label'   => __( 'Body font', 'pdo-modern' ),
			'section' => 'pdo_fonts',
			'type'    => 'select',
			'choices' => $font_labels,
		)
	);

	$wp_customize->add_setting(
		'pdo_font_heading',
		array(
			'default'           => 'inter',
			'sanitize_callback' => 'pdo_modern_sanitize_font',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'pdo_font_heading',
		array(
			'label'   => __( 'Heading font', 'pdo-modern' ),
			'section' => 'pdo_fonts',
			'type'    => 'select',
			'choices' => $font_labels,
		)
	);

	$wp_customize->add_setting(
		'pdo_font_bengali',
		array(
			'default'           => 'hind-siliguri',
			'sanitize_callback' => 'pdo_modern_sanitize_font',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'pdo_font_bengali',
		array(
			'label'       => __( 'Bengali font', 'pdo-modern' ),
			'description' => __( 'Applied automatically whenever Bengali content is rendered.', 'pdo-modern' ),
			'section'     => 'pdo_fonts',
			'type'        => 'select',
			'choices'     => wp_list_pluck( pdo_modern_font_stacks( true ), 'label' ),
		)
	);

	$wp_customize->add_setting(
		'pdo_use_bundled_fonts',
		array(
			'default'           => true,
			'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_use_bundled_fonts',
		array(
			'label'       => __( 'Load the bundled web fonts', 'pdo-modern' ),
			'description' => __( 'Fonts are served from your own server – no Google Fonts request, no CDN. Turn this off to use system fonts only.', 'pdo-modern' ),
			'section'     => 'pdo_fonts',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_section(
		'pdo_font_sizes',
		array(
			'title' => __( 'Sizes & rhythm', 'pdo-modern' ),
			'panel' => 'pdo_typography',
		)
	);

	$sizes = array(
		'pdo_font_size_base'    => array( __( 'Base font size (px)', 'pdo-modern' ), 16, 13, 22, 1 ),
		'pdo_font_scale'        => array( __( 'Heading scale ratio', 'pdo-modern' ), 1.25, 1.1, 1.5, 0.01 ),
		'pdo_line_height'       => array( __( 'Body line height', 'pdo-modern' ), 1.7, 1.3, 2.2, 0.05 ),
		'pdo_heading_weight'    => array( __( 'Heading weight', 'pdo-modern' ), 800, 500, 900, 100 ),
		'pdo_heading_tracking'  => array( __( 'Heading letter spacing (em ×100)', 'pdo-modern' ), -2, -5, 5, 1 ),
		'pdo_bengali_scale'     => array( __( 'Bengali size adjustment (%)', 'pdo-modern' ), 104, 90, 125, 1 ),
	);

	foreach ( $sizes as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'pdo_modern_sanitize_float',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new PDO_Range_Control(
				$wp_customize,
				$key,
				array(
					'label'   => $meta[0],
					'section' => 'pdo_font_sizes',
					'min'     => $meta[2],
					'max'     => $meta[3],
					'step'    => $meta[4],
				)
			)
		);
	}

	$wp_customize->add_setting(
		'pdo_uppercase_eyebrows',
		array(
			'default'           => true,
			'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'pdo_uppercase_eyebrows',
		array(
			'label'   => __( 'Uppercase the small section labels', 'pdo-modern' ),
			'section' => 'pdo_font_sizes',
			'type'    => 'checkbox',
		)
	);

	/* ====================================================================
	 * PANEL: Layout
	 * ================================================================= */
	$wp_customize->add_panel(
		'pdo_layout',
		array(
			'title'    => __( 'PDO · Layout', 'pdo-modern' ),
			'priority' => 23,
		)
	);

	$wp_customize->add_section(
		'pdo_layout_global',
		array(
			'title' => __( 'Containers & spacing', 'pdo-modern' ),
			'panel' => 'pdo_layout',
		)
	);

	$wp_customize->add_setting(
		'pdo_layout_width',
		array(
			'default'           => 'wide',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_layout_width',
		array(
			'label'   => __( 'Content width', 'pdo-modern' ),
			'section' => 'pdo_layout_global',
			'type'    => 'select',
			'choices' => array(
				'narrow' => __( 'Narrow (1100px)', 'pdo-modern' ),
				'wide'   => __( 'Wide (1280px)', 'pdo-modern' ),
				'xwide'  => __( 'Extra wide (1440px)', 'pdo-modern' ),
				'full'   => __( 'Full width', 'pdo-modern' ),
			),
		)
	);

	$layout_ranges = array(
		'pdo_container_padding' => array( __( 'Container side padding (px)', 'pdo-modern' ), 24, 12, 80, 1 ),
		'pdo_section_spacing'   => array( __( 'Vertical section spacing (px)', 'pdo-modern' ), 88, 32, 200, 4 ),
		'pdo_card_gap'          => array( __( 'Grid gap (px)', 'pdo-modern' ), 28, 8, 64, 1 ),
		'pdo_radius'            => array( __( 'Corner radius (px)', 'pdo-modern' ), 16, 0, 36, 1 ),
		'pdo_shadow_strength'   => array( __( 'Shadow strength (%)', 'pdo-modern' ), 100, 0, 200, 5 ),
	);

	foreach ( $layout_ranges as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'pdo_modern_sanitize_float',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new PDO_Range_Control(
				$wp_customize,
				$key,
				array(
					'label'   => $meta[0],
					'section' => 'pdo_layout_global',
					'min'     => $meta[2],
					'max'     => $meta[3],
					'step'    => $meta[4],
					'unit'    => 'px',
				)
			)
		);
	}

	$toggles = array(
		'pdo_rounded_corners' => array( __( 'Rounded corners', 'pdo-modern' ), true ),
		'pdo_animations'      => array( __( 'Scroll & hover animations', 'pdo-modern' ), true ),
		'pdo_back_to_top'     => array( __( 'Show the “back to top” button', 'pdo-modern' ), true ),
		'pdo_breadcrumbs'     => array( __( 'Show breadcrumbs on inner pages', 'pdo-modern' ), true ),
		'pdo_disable_emojis'  => array( __( 'Disable the WordPress emoji script', 'pdo-modern' ), true ),
	);

	foreach ( $toggles as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $meta[0],
				'section' => 'pdo_layout_global',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_section(
		'pdo_layout_blog',
		array(
			'title' => __( 'Blog & archives', 'pdo-modern' ),
			'panel' => 'pdo_layout',
		)
	);

	$wp_customize->add_setting(
		'pdo_blog_layout',
		array(
			'default'           => 'grid',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_blog_layout',
		array(
			'label'   => __( 'Archive layout', 'pdo-modern' ),
			'section' => 'pdo_layout_blog',
			'type'    => 'select',
			'choices' => array(
				'grid' => __( 'Card grid', 'pdo-modern' ),
				'list' => __( 'List', 'pdo-modern' ),
			),
		)
	);

	$wp_customize->add_setting(
		'pdo_blog_sidebar',
		array(
			'default'           => 'right',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_blog_sidebar',
		array(
			'label'   => __( 'Sidebar position', 'pdo-modern' ),
			'section' => 'pdo_layout_blog',
			'type'    => 'select',
			'choices' => array(
				'right' => __( 'Right', 'pdo-modern' ),
				'left'  => __( 'Left', 'pdo-modern' ),
				'none'  => __( 'No sidebar', 'pdo-modern' ),
			),
		)
	);

	$wp_customize->add_setting(
		'pdo_blog_columns',
		array(
			'default'           => 3,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new PDO_Range_Control(
			$wp_customize,
			'pdo_blog_columns',
			array(
				'label'   => __( 'Cards per row', 'pdo-modern' ),
				'section' => 'pdo_layout_blog',
				'min'     => 1,
				'max'     => 4,
				'step'    => 1,
			)
		)
	);

	$wp_customize->add_setting(
		'pdo_excerpt_length',
		array(
			'default'           => 28,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new PDO_Range_Control(
			$wp_customize,
			'pdo_excerpt_length',
			array(
				'label'   => __( 'Excerpt length (words)', 'pdo-modern' ),
				'section' => 'pdo_layout_blog',
				'min'     => 10,
				'max'     => 80,
				'step'    => 1,
			)
		)
	);

	/* ====================================================================
	 * PANEL: Header
	 * ================================================================= */
	$wp_customize->add_panel(
		'pdo_header',
		array(
			'title'    => __( 'PDO · Header', 'pdo-modern' ),
			'priority' => 24,
		)
	);

	$wp_customize->add_section(
		'pdo_header_topbar',
		array(
			'title' => __( 'Top bar', 'pdo-modern' ),
			'panel' => 'pdo_header',
		)
	);

	$wp_customize->add_setting(
		'pdo_topbar_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_topbar_enabled',
		array(
			'label'   => __( 'Show the top bar', 'pdo-modern' ),
			'section' => 'pdo_header_topbar',
			'type'    => 'checkbox',
		)
	);

	$topbar_items = array(
		'pdo_topbar_show_reg'      => array( __( 'Show the registration number', 'pdo-modern' ), true ),
		'pdo_topbar_show_phone'    => array( __( 'Show the phone number', 'pdo-modern' ), true ),
		'pdo_topbar_show_email'    => array( __( 'Show the email address', 'pdo-modern' ), true ),
		'pdo_topbar_show_location' => array( __( 'Show the location', 'pdo-modern' ), true ),
		'pdo_topbar_show_socials'  => array( __( 'Show the social links', 'pdo-modern' ), true ),
		'pdo_topbar_show_lang'     => array( __( 'Show the language switcher', 'pdo-modern' ), true ),
	);

	foreach ( $topbar_items as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $meta[0],
				'section' => 'pdo_header_topbar',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_section(
		'pdo_header_main',
		array(
			'title' => __( 'Main header', 'pdo-modern' ),
			'panel' => 'pdo_header',
		)
	);

	$wp_customize->add_setting(
		'pdo_header_layout',
		array(
			'default'           => 'standard',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_header_layout',
		array(
			'label'   => __( 'Header layout', 'pdo-modern' ),
			'section' => 'pdo_header_main',
			'type'    => 'select',
			'choices' => array(
				'standard' => __( 'Logo left · menu right', 'pdo-modern' ),
				'centered' => __( 'Centered logo · menu below', 'pdo-modern' ),
				'split'    => __( 'Split menu around the logo', 'pdo-modern' ),
				'minimal'  => __( 'Minimal (logo + menu button)', 'pdo-modern' ),
			),
		)
	);

	$header_toggles = array(
		'pdo_header_sticky'      => array( __( 'Sticky header on scroll', 'pdo-modern' ), true ),
		'pdo_header_shrink'      => array( __( 'Shrink the header when sticky', 'pdo-modern' ), true ),
		'pdo_header_search'      => array( __( 'Show the search button', 'pdo-modern' ), true ),
		'pdo_header_cta_enabled' => array( __( 'Show the header call-to-action button', 'pdo-modern' ), true ),
		'pdo_header_border'      => array( __( 'Show a bottom border', 'pdo-modern' ), true ),
	);

	foreach ( $header_toggles as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $meta[0],
				'section' => 'pdo_header_main',
				'type'    => 'checkbox',
			)
		);
	}

	$cta_fields = array(
		'pdo_header_cta_label'    => array( __( 'Button label', 'pdo-modern' ), 'Join as Guardian', 'sanitize_text_field' ),
		'pdo_header_cta_label_bn' => array( __( 'Button label (Bengali)', 'pdo-modern' ), '', 'sanitize_text_field' ),
		'pdo_header_cta_url'      => array( __( 'Button URL', 'pdo-modern' ), '', 'esc_url_raw' ),
		'pdo_header_cta_icon'     => array( __( 'Button icon name', 'pdo-modern' ), 'hand-heart', 'sanitize_key' ),
	);

	foreach ( $cta_fields as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => $meta[2],
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'           => $meta[0],
				'section'         => 'pdo_header_main',
				'type'            => 'text',
				'active_callback' => static function () {
					return (bool) get_theme_mod( 'pdo_header_cta_enabled', true );
				},
			)
		);
	}

	$wp_customize->selective_refresh->add_partial(
		'pdo_header_cta_label',
		array(
			'selector'            => '.pdo-header__cta',
			'container_inclusive' => true,
			'render_callback'     => 'pdo_modern_render_header_cta',
		)
	);

	/* ====================================================================
	 * PANEL: Navigation
	 * ================================================================= */
	$wp_customize->add_panel(
		'pdo_navigation',
		array(
			'title'    => __( 'PDO · Navigation', 'pdo-modern' ),
			'priority' => 25,
		)
	);

	$wp_customize->add_section(
		'pdo_nav_desktop',
		array(
			'title' => __( 'Desktop menu', 'pdo-modern' ),
			'panel' => 'pdo_navigation',
		)
	);

	$wp_customize->add_setting(
		'pdo_nav_style',
		array(
			'default'           => 'underline',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_nav_style',
		array(
			'label'   => __( 'Hover effect', 'pdo-modern' ),
			'section' => 'pdo_nav_desktop',
			'type'    => 'select',
			'choices' => array(
				'underline' => __( 'Underline', 'pdo-modern' ),
				'pill'      => __( 'Pill', 'pdo-modern' ),
				'color'     => __( 'Colour change only', 'pdo-modern' ),
			),
		)
	);

	$wp_customize->add_setting(
		'pdo_nav_font_size',
		array(
			'default'           => 15,
			'sanitize_callback' => 'pdo_modern_sanitize_float',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new PDO_Range_Control(
			$wp_customize,
			'pdo_nav_font_size',
			array(
				'label'   => __( 'Menu font size (px)', 'pdo-modern' ),
				'section' => 'pdo_nav_desktop',
				'min'     => 12,
				'max'     => 20,
				'step'    => 0.5,
				'unit'    => 'px',
			)
		)
	);

	$wp_customize->add_setting(
		'pdo_nav_gap',
		array(
			'default'           => 6,
			'sanitize_callback' => 'pdo_modern_sanitize_float',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new PDO_Range_Control(
			$wp_customize,
			'pdo_nav_gap',
			array(
				'label'   => __( 'Space between items (px)', 'pdo-modern' ),
				'section' => 'pdo_nav_desktop',
				'min'     => 0,
				'max'     => 32,
				'step'    => 1,
				'unit'    => 'px',
			)
		)
	);

	$nav_toggles = array(
		'pdo_nav_uppercase'  => array( __( 'Uppercase menu labels', 'pdo-modern' ), false ),
		'pdo_nav_icons'      => array( __( 'Show the per-item icons', 'pdo-modern' ), true ),
		'pdo_nav_caret'      => array( __( 'Show the dropdown caret', 'pdo-modern' ), true ),
		'pdo_nav_mega'       => array( __( 'Render top-level dropdowns as mega menus', 'pdo-modern' ), true ),
	);

	foreach ( $nav_toggles as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $meta[0],
				'section' => 'pdo_nav_desktop',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_section(
		'pdo_nav_mobile',
		array(
			'title' => __( 'Mobile menu', 'pdo-modern' ),
			'panel' => 'pdo_navigation',
		)
	);

	$wp_customize->add_setting(
		'pdo_mobile_breakpoint',
		array(
			'default'           => 1024,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new PDO_Range_Control(
			$wp_customize,
			'pdo_mobile_breakpoint',
			array(
				'label'       => __( 'Switch to the mobile menu below (px)', 'pdo-modern' ),
				'section'     => 'pdo_nav_mobile',
				'min'         => 640,
				'max'         => 1280,
				'step'        => 2,
				'unit'        => 'px',
				'description' => __( 'Reload the preview after changing this value.', 'pdo-modern' ),
			)
		)
	);

	$wp_customize->add_setting(
		'pdo_mobile_style',
		array(
			'default'           => 'drawer',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_mobile_style',
		array(
			'label'   => __( 'Mobile menu style', 'pdo-modern' ),
			'section' => 'pdo_nav_mobile',
			'type'    => 'select',
			'choices' => array(
				'drawer'   => __( 'Slide-in drawer', 'pdo-modern' ),
				'fullpage' => __( 'Full-screen overlay', 'pdo-modern' ),
				'dropdown' => __( 'Push-down panel', 'pdo-modern' ),
			),
		)
	);

	$wp_customize->add_setting(
		'pdo_mobile_cta',
		array(
			'default'           => true,
			'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pdo_mobile_cta',
		array(
			'label'   => __( 'Repeat the call to action inside the mobile menu', 'pdo-modern' ),
			'section' => 'pdo_nav_mobile',
			'type'    => 'checkbox',
		)
	);

	/* ====================================================================
	 * PANEL: Footer
	 * ================================================================= */
	$wp_customize->add_panel(
		'pdo_footer',
		array(
			'title'    => __( 'PDO · Footer', 'pdo-modern' ),
			'priority' => 26,
		)
	);

	$wp_customize->add_section(
		'pdo_footer_layout',
		array(
			'title' => __( 'Footer layout', 'pdo-modern' ),
			'panel' => 'pdo_footer',
		)
	);

	$wp_customize->add_setting(
		'pdo_footer_columns',
		array(
			'default'           => 4,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new PDO_Range_Control(
			$wp_customize,
			'pdo_footer_columns',
			array(
				'label'   => __( 'Footer columns', 'pdo-modern' ),
				'section' => 'pdo_footer_layout',
				'min'     => 1,
				'max'     => 5,
				'step'    => 1,
			)
		)
	);

	$footer_toggles = array(
		'pdo_footer_about'     => array( __( 'Show the organisation column', 'pdo-modern' ), true ),
		'pdo_footer_nav'       => array( __( 'Show the quick navigation column', 'pdo-modern' ), true ),
		'pdo_footer_programs'  => array( __( 'Show the flagship programs column', 'pdo-modern' ), true ),
		'pdo_footer_contact'   => array( __( 'Show the contact column', 'pdo-modern' ), true ),
		'pdo_footer_socials'   => array( __( 'Show the social icons', 'pdo-modern' ), true ),
		'pdo_footer_newsletter'=> array( __( 'Show the “stay in touch” note', 'pdo-modern' ), true ),
		'pdo_footer_badge'     => array( __( 'Show the registration badge', 'pdo-modern' ), true ),
	);

	foreach ( $footer_toggles as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'pdo_modern_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $meta[0],
				'section' => 'pdo_footer_layout',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'pdo_footer_credit',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'pdo_footer_credit',
		array(
			'label'       => __( 'Extra credit line', 'pdo-modern' ),
			'description' => __( 'Optional. Appended after the copyright notice.', 'pdo-modern' ),
			'section'     => 'pdo_footer_layout',
			'type'        => 'textarea',
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'pdo_footer_credit',
		array(
			'selector'        => '.pdo-footer__credit',
			'render_callback' => static function () {
				return wp_kses_post( (string) get_theme_mod( 'pdo_footer_credit', '' ) );
			},
		)
	);

	/* ====================================================================
	 * PANEL: Sections
	 * ================================================================= */
	$wp_customize->add_panel(
		'pdo_sections',
		array(
			'title'       => __( 'PDO · Sections', 'pdo-modern' ),
			'description' => __( 'Reorder the homepage, hide sections, and control which breakpoints they appear on. The content of each section is edited under “PDO Manager”.', 'pdo-modern' ),
			'priority'    => 27,
		)
	);

	$wp_customize->add_section(
		'pdo_sections_home',
		array(
			'title' => __( 'Homepage order', 'pdo-modern' ),
			'panel' => 'pdo_sections',
		)
	);

	$wp_customize->add_setting(
		'pdo_home_order',
		array(
			'default'           => pdo_modern_default_home_order(),
			'sanitize_callback' => 'pdo_modern_sanitize_order',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new PDO_Sortable_Control(
			$wp_customize,
			'pdo_home_order',
			array(
				'label'       => __( 'Drag to reorder the homepage sections', 'pdo-modern' ),
				'description' => __( 'Untick a section to hide it everywhere.', 'pdo-modern' ),
				'section'     => 'pdo_sections_home',
				'choices'     => pdo_modern_home_sections(),
			)
		)
	);

	$wp_customize->add_section(
		'pdo_sections_visibility',
		array(
			'title'       => __( 'Responsive visibility', 'pdo-modern' ),
			'description' => __( 'Hide individual sections per breakpoint without deleting their content.', 'pdo-modern' ),
			'panel'       => 'pdo_sections',
		)
	);

	foreach ( pdo_modern_home_sections() as $key => $label ) {
		$wp_customize->add_setting(
			'pdo_section_hide_' . $key,
			array(
				'default'           => '',
				'sanitize_callback' => 'pdo_modern_sanitize_visibility',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'pdo_section_hide_' . $key,
			array(
				'label'   => $label,
				'section' => 'pdo_sections_visibility',
				'type'    => 'select',
				'choices' => array(
					''        => __( 'Visible everywhere', 'pdo-modern' ),
					'mobile'  => __( 'Hide on mobile', 'pdo-modern' ),
					'tablet'  => __( 'Hide on tablet', 'pdo-modern' ),
					'desktop' => __( 'Hide on desktop', 'pdo-modern' ),
					'all'     => __( 'Hide everywhere', 'pdo-modern' ),
				),
			)
		);
	}

	/* Selective refresh partials for the homepage sections. */
	foreach ( array_keys( pdo_modern_home_sections() ) as $key ) {
		$wp_customize->selective_refresh->add_partial(
			'pdo_section_partial_' . $key,
			array(
				'selector'            => '#pdo-section-' . $key,
				'settings'            => array( 'pdo_section_hide_' . $key ),
				'container_inclusive' => true,
				'render_callback'     => static function () use ( $key ) {
					pdo_modern_render_home_section( $key );
				},
			)
		);
	}
}
add_action( 'customize_register', 'pdo_modern_customize_register' );

/* -------------------------------------------------------------------------
 * Sanitisers
 * ---------------------------------------------------------------------- */

/**
 * Checkbox.
 *
 * @param mixed $value Value.
 * @return bool
 */
function pdo_modern_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Float / number.
 *
 * @param mixed $value Value.
 * @return float
 */
function pdo_modern_sanitize_float( $value ) {
	return is_numeric( $value ) ? (float) $value : 0.0;
}

/**
 * Font key.
 *
 * @param mixed $value Value.
 * @return string
 */
function pdo_modern_sanitize_font( $value ) {
	$stacks = pdo_modern_font_stacks( null );
	$value  = sanitize_key( $value );
	return isset( $stacks[ $value ] ) ? $value : 'inter';
}

/**
 * Comma separated section order.
 *
 * @param mixed $value Value.
 * @return string
 */
function pdo_modern_sanitize_order( $value ) {
	$allowed = array_keys( pdo_modern_home_sections() );
	$parts   = array_filter( array_map( 'sanitize_key', explode( ',', (string) $value ) ) );
	$clean   = array();

	foreach ( $parts as $part ) {
		$bare = ltrim( $part, '_' );
		if ( in_array( $bare, $allowed, true ) && ! in_array( $part, $clean, true ) ) {
			$clean[] = $part;
		}
	}

	// Append anything the stored value is missing so new sections appear.
	foreach ( $allowed as $slug ) {
		if ( ! in_array( $slug, $clean, true ) && ! in_array( '_' . $slug, $clean, true ) ) {
			$clean[] = $slug;
		}
	}

	return implode( ',', $clean );
}

/**
 * Responsive visibility choice.
 *
 * @param mixed $value Value.
 * @return string
 */
function pdo_modern_sanitize_visibility( $value ) {
	$allowed = array( '', 'mobile', 'tablet', 'desktop', 'all' );
	$value   = sanitize_key( $value );
	return in_array( $value, $allowed, true ) ? $value : '';
}

/* -------------------------------------------------------------------------
 * Customizer preview JS
 * ---------------------------------------------------------------------- */

/**
 * Live preview script.
 *
 * @return void
 */
function pdo_modern_customize_preview_js() {
	wp_enqueue_script(
		'pdo-modern-customizer-preview',
		PDO_MODERN_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		pdo_modern_asset_version( 'assets/js/customizer-preview.js' ),
		true
	);

	$stacks = array();
	foreach ( pdo_modern_font_stacks( null ) as $key => $meta ) {
		$stacks[ $key ] = $meta['stack'];
	}

	$widths = array();
	foreach ( array( 'narrow', 'wide', 'xwide', 'full' ) as $option ) {
		$widths[ $option ] = pdo_modern_container_width( $option );
	}

	wp_localize_script(
		'pdo-modern-customizer-preview',
		'pdoCustomizePreview',
		array(
			'fonts'  => $stacks,
			'widths' => $widths,
		)
	);
}
add_action( 'customize_preview_init', 'pdo_modern_customize_preview_js' );

/**
 * Controls script + styles (sortable list).
 *
 * @return void
 */
function pdo_modern_customize_controls_assets() {
	wp_enqueue_script(
		'pdo-modern-customizer-controls',
		PDO_MODERN_URI . '/assets/js/customizer-controls.js',
		array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
		pdo_modern_asset_version( 'assets/js/customizer-controls.js' ),
		true
	);
	wp_enqueue_style(
		'pdo-modern-customizer-controls',
		PDO_MODERN_URI . '/assets/css/customizer.css',
		array(),
		pdo_modern_asset_version( 'assets/css/customizer.css' )
	);
}
add_action( 'customize_controls_enqueue_scripts', 'pdo_modern_customize_controls_assets' );
