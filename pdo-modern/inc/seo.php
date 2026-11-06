<?php
/**
 * SEO: canonical, Open Graph, Twitter cards and JSON-LD structured data.
 *
 * Everything is skipped automatically when a dedicated SEO plugin (Yoast,
 * Rank Math, SEOPress, AIOSEO) is active, so tags are never duplicated.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a third-party SEO plugin handling the meta tags?
 *
 * @return bool
 */
function pdo_modern_seo_plugin_active() {
	return (
		defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| class_exists( 'All_in_One_SEO_Pack' )
	);
}

/**
 * The best share image for the current view.
 *
 * @return string
 */
function pdo_modern_share_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		if ( $url ) {
			return $url;
		}
	}

	if ( is_singular() ) {
		$meta = get_post_meta( get_the_ID(), '_pdo_image_url', true );
		if ( $meta ) {
			return (string) $meta;
		}
	}

	$fallback = (int) get_theme_mod( 'pdo_favicon_hint', 0 );
	if ( $fallback ) {
		$url = wp_get_attachment_image_url( $fallback, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	$logo = (int) get_theme_mod( 'custom_logo', 0 );
	if ( $logo ) {
		$url = wp_get_attachment_image_url( $logo, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	return '';
}

/**
 * The description for the current view.
 *
 * @return string
 */
function pdo_modern_meta_description() {
	if ( is_singular() ) {
		$excerpt = get_the_excerpt();
		if ( $excerpt ) {
			return wp_strip_all_tags( $excerpt );
		}
	}

	if ( is_front_page() ) {
		$description = pdo_org( 'description', '' );
		if ( $description ) {
			return wp_strip_all_tags( $description );
		}
	}

	if ( is_category() || is_tax() || is_tag() ) {
		$description = term_description();
		if ( $description ) {
			return wp_strip_all_tags( $description );
		}
	}

	return wp_strip_all_tags( get_bloginfo( 'description' ) );
}

/**
 * Print the meta tags.
 *
 * @return void
 */
function pdo_modern_meta_tags() {
	if ( pdo_modern_seo_plugin_active() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = pdo_modern_meta_description();
	$image       = pdo_modern_share_image();
	$url         = is_singular() ? (string) get_permalink() : home_url( add_query_arg( array() ) );
	$site        = pdo_org( 'name', get_bloginfo( 'name' ) );
	$locale      = 'bn' === pdo_lang() ? 'bn_BD' : get_locale();

	echo "\n<!-- PDO Modern SEO -->\n";

	if ( $description ) {
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );
	}

	if ( is_singular() ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( (string) get_permalink() ) );
	} elseif ( is_front_page() ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( home_url( '/' ) ) );
	}

	printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( $locale ) );
	printf( '<meta property="og:type" content="%s" />' . "\n", is_singular( array( 'post', 'pdo_news' ) ) ? 'article' : 'website' );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( $site ) );

	if ( $image ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
		printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $image ) );
		printf( '<meta name="twitter:card" content="summary_large_image" />' . "\n" );
	} else {
		printf( '<meta name="twitter:card" content="summary" />' . "\n" );
	}

	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $description ) );

	if ( is_singular( array( 'post', 'pdo_news' ) ) ) {
		printf( '<meta property="article:published_time" content="%s" />' . "\n", esc_attr( get_the_date( DATE_W3C ) ) );
		printf( '<meta property="article:modified_time" content="%s" />' . "\n", esc_attr( get_the_modified_date( DATE_W3C ) ) );
	}
}
add_action( 'wp_head', 'pdo_modern_meta_tags', 5 );

/**
 * Core prints its own canonical tag on singular views; this theme emits one in
 * pdo_modern_meta_tags(), so drop the duplicate.
 *
 * @return void
 */
function pdo_modern_remove_core_canonical() {
	remove_action( 'wp_head', 'rel_canonical' );
}
add_action( 'wp_head', 'pdo_modern_remove_core_canonical', 1 );

/**
 * JSON-LD structured data.
 *
 * @return void
 */
function pdo_modern_structured_data() {
	if ( pdo_modern_seo_plugin_active() ) {
		return;
	}

	$graph = array();

	/* Organisation ------------------------------------------------------- */
	$org = array(
		'@type'       => 'NGO',
		'@id'         => home_url( '/#organization' ),
		'name'        => pdo_org( 'name', get_bloginfo( 'name' ) ),
		'alternateName' => pdo_org( 'short_name', '' ),
		'url'         => home_url( '/' ),
		'description' => wp_strip_all_tags( (string) pdo_org( 'description', get_bloginfo( 'description' ) ) ),
		'slogan'      => pdo_org( 'tagline', '' ),
	);

	$founded = pdo_setting( 'org', 'established', '' );
	if ( $founded ) {
		$org['foundingDate'] = $founded;
	}

	$email = pdo_setting( 'org', 'email', '' );
	$phone = pdo_setting( 'org', 'phone', '' );
	if ( $email || $phone ) {
		$org['contactPoint'] = array(
			array(
				'@type'       => 'ContactPoint',
				'contactType' => 'customer support',
				'email'       => $email,
				'telephone'   => $phone,
				'areaServed'  => 'BD',
				'availableLanguage' => array( 'English', 'Bengali' ),
			),
		);
	}

	$address = array_filter(
		array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => wp_strip_all_tags( (string) pdo_setting( 'org', 'address', '' ) ),
			'addressLocality' => pdo_setting( 'org', 'upazila', '' ),
			'addressRegion'   => pdo_setting( 'org', 'division', '' ),
			'addressCountry'  => 'BD',
		)
	);
	if ( count( $address ) > 1 ) {
		$org['address'] = $address;
	}

	$lat = pdo_setting( 'org', 'map_lat', '' );
	$lng = pdo_setting( 'org', 'map_lng', '' );
	if ( $lat && $lng ) {
		$org['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => $lat,
			'longitude' => $lng,
		);
	}

	$socials = pdo_rows( 'org', 'socials' );
	$same_as = array();
	foreach ( $socials as $social ) {
		if ( ! empty( $social['url'] ) && 0 === strpos( (string) $social['url'], 'http' ) ) {
			$same_as[] = $social['url'];
		}
	}
	if ( $same_as ) {
		$org['sameAs'] = array_values( array_unique( $same_as ) );
	}

	$logo = (int) get_theme_mod( 'custom_logo', 0 );
	if ( $logo ) {
		$logo_url = wp_get_attachment_image_url( $logo, 'full' );
		if ( $logo_url ) {
			$org['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $logo_url,
			);
		}
	}

	$graph[] = $org;

	/* Website ------------------------------------------------------------ */
	$graph[] = array(
		'@type'           => 'WebSite',
		'@id'             => home_url( '/#website' ),
		'url'             => home_url( '/' ),
		'name'            => get_bloginfo( 'name' ),
		'publisher'       => array( '@id' => home_url( '/#organization' ) ),
		'inLanguage'      => 'bn' === pdo_lang() ? 'bn-BD' : get_bloginfo( 'language' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	/* Current article ----------------------------------------------------- */
	if ( is_singular( array( 'post', 'pdo_news' ) ) ) {
		$article = array(
			'@type'            => 'NewsArticle',
			'@id'              => get_permalink() . '#article',
			'headline'         => wp_strip_all_tags( get_the_title() ),
			'description'      => wp_strip_all_tags( get_the_excerpt() ),
			'datePublished'    => get_the_date( DATE_W3C ),
			'dateModified'     => get_the_modified_date( DATE_W3C ),
			'mainEntityOfPage' => array( '@id' => get_permalink() ),
			'publisher'        => array( '@id' => home_url( '/#organization' ) ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author(),
			),
		);
		$image   = pdo_modern_share_image();
		if ( $image ) {
			$article['image'] = $image;
		}
		$graph[] = $article;
	}

	/* Breadcrumbs --------------------------------------------------------- */
	if ( ! is_front_page() && is_singular() ) {
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => __( 'Home', 'pdo-modern' ),
					'item'     => home_url( '/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => wp_strip_all_tags( get_the_title() ),
				),
			),
		);
	}

	$payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded.
	);
}
add_action( 'wp_head', 'pdo_modern_structured_data', 6 );

/**
 * Tell search engines the site is bilingual.
 *
 * @param string $output Language attributes.
 * @return string
 */
function pdo_modern_html_lang( $output ) {
	return $output;
}
add_filter( 'language_attributes', 'pdo_modern_html_lang' );
