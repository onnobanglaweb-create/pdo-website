<?php
/**
 * Template helpers used by header, footer and the page templates.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print the site logo, honouring the context specific overrides.
 *
 * @param string $context header|sticky|mobile|footer.
 * @return void
 */
function pdo_modern_logo( $context = 'header' ) {
	$map = array(
		'sticky' => 'pdo_logo_sticky',
		'mobile' => 'pdo_logo_mobile',
		'footer' => 'pdo_logo_dark',
	);

	$attachment_id = 0;
	if ( isset( $map[ $context ] ) ) {
		$attachment_id = (int) get_theme_mod( $map[ $context ], 0 );
	}
	if ( ! $attachment_id ) {
		$attachment_id = (int) get_theme_mod( 'custom_logo', 0 );
	}

	$name    = pdo_org( 'name', get_bloginfo( 'name' ) );
	$short   = pdo_org( 'short_name', '' );
	$tagline = pdo_org( 'tagline', get_bloginfo( 'description' ) );
	$badge   = pdo_setting( 'org', 'type_label', '' );
	$icon    = pdo_setting( 'org', 'logo_icon', 'people-roof' );

	$class = 'pdo-brand pdo-brand--' . sanitize_html_class( $context );
	?>
	<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<?php if ( $attachment_id ) : ?>
			<span class="pdo-brand__logo">
				<?php
				echo wp_get_attachment_image(
					$attachment_id,
					'full',
					false,
					array(
						'class'   => 'pdo-brand__image',
						'alt'     => esc_attr( $name ),
						'loading' => 'header' === $context ? 'eager' : 'lazy',
					)
				);
				?>
			</span>
		<?php else : ?>
			<span class="pdo-brand__mark" aria-hidden="true">
				<?php pdo_icon( $icon, array( 'size' => 26 ) ); ?>
			</span>
			<span class="pdo-brand__text">
				<span class="pdo-brand__name">
					<?php echo esc_html( $short ? $short : $name ); ?>
					<?php if ( $badge ) : ?>
						<span class="pdo-brand__badge"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>
				</span>
				<?php if ( $tagline && get_theme_mod( 'pdo_show_tagline', true ) ) : ?>
					<span class="pdo-brand__tagline"><?php echo esc_html( $tagline ); ?></span>
				<?php endif; ?>
			</span>
		<?php endif; ?>
	</a>
	<?php
}

/**
 * Header call-to-action button (also used by selective refresh).
 *
 * @return string
 */
function pdo_modern_render_header_cta() {
	if ( ! get_theme_mod( 'pdo_header_cta_enabled', true ) ) {
		return '';
	}

	$label = (string) get_theme_mod( 'pdo_header_cta_label', __( 'Join as Guardian', 'pdo-modern' ) );
	if ( 'bn' === pdo_lang() ) {
		$bn = (string) get_theme_mod( 'pdo_header_cta_label_bn', '' );
		if ( $bn ) {
			$label = $bn;
		}
	}
	if ( ! $label ) {
		return '';
	}

	$url  = (string) get_theme_mod( 'pdo_header_cta_url', '' );
	$url  = $url ? $url : pdo_page_url( 'involved' );
	$icon = (string) get_theme_mod( 'pdo_header_cta_icon', 'hand-heart' );

	return sprintf(
		'<a class="pdo-btn pdo-btn--primary pdo-header__cta" href="%1$s">%2$s<span>%3$s</span></a>',
		esc_url( $url ),
		$icon ? pdo_get_icon( $icon, array( 'size' => 16 ) ) : '',
		esc_html( $label )
	);
}

/**
 * Language switcher.
 *
 * @param string $context topbar|drawer.
 * @return void
 */
function pdo_modern_language_switcher( $context = 'topbar' ) {
	if ( ! class_exists( 'PDO_SM_I18n' ) || ! PDO_SM_I18n::enabled() ) {
		return;
	}

	$current   = pdo_lang();
	$languages = PDO_SM_I18n::languages();
	?>
	<div class="pdo-langswitch pdo-langswitch--<?php echo esc_attr( $context ); ?>" role="group" aria-label="<?php esc_attr_e( 'Choose a language', 'pdo-modern' ); ?>">
		<?php foreach ( $languages as $code => $language ) : ?>
			<a
				class="pdo-langswitch__item<?php echo $code === $current ? ' is-active' : ''; ?>"
				href="<?php echo esc_url( PDO_SM_I18n::switch_url( $code ) ); ?>"
				hreflang="<?php echo esc_attr( $language['locale'] ); ?>"
				lang="<?php echo esc_attr( $language['locale'] ); ?>"
				<?php echo $code === $current ? 'aria-current="true"' : ''; ?>
			>
				<?php echo esc_html( $language['short'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Breadcrumbs.
 *
 * @return void
 */
function pdo_modern_breadcrumbs() {
	if ( is_front_page() || ! get_theme_mod( 'pdo_breadcrumbs', true ) ) {
		return;
	}

	$items = array(
		array(
			'label' => pdo_t( 'nav_home', __( 'Home', 'pdo-modern' ) ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_singular( 'pdo_news' ) ) {
		$items[] = array(
			'label' => pdo_text( 'media', 'intro_title', __( 'Media & Insights', 'pdo-modern' ) ),
			'url'   => pdo_page_url( 'media' ),
		);
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_singular( array( 'pdo_program', 'pdo_story' ) ) ) {
		$items[] = array(
			'label' => pdo_text( 'impact', 'intro_title', __( 'Our Impact', 'pdo-modern' ) ),
			'url'   => pdo_page_url( 'impact' ),
		);
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_singular( array( 'pdo_document' ) ) ) {
		$items[] = array(
			'label' => pdo_text( 'transparency', 'intro_title', __( 'Transparency', 'pdo-modern' ) ),
			'url'   => pdo_page_url( 'transparency' ),
		);
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_page() ) {
		$ancestors = array_reverse( (array) get_post_ancestors( get_queried_object_id() ) );
		foreach ( $ancestors as $ancestor ) {
			$items[] = array(
				'label' => get_the_title( $ancestor ),
				'url'   => (string) get_permalink( $ancestor ),
			);
		}
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_singular() ) {
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_search() ) {
		$items[] = array(
			/* translators: %s: search term. */
			'label' => sprintf( __( 'Search: %s', 'pdo-modern' ), get_search_query() ),
			'url'   => '',
		);
	} elseif ( is_404() ) {
		$items[] = array(
			'label' => __( 'Page not found', 'pdo-modern' ),
			'url'   => '',
		);
	} elseif ( is_archive() ) {
		$items[] = array(
			'label' => wp_strip_all_tags( get_the_archive_title() ),
			'url'   => '',
		);
	}

	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="pdo-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'pdo-modern' ); ?>">
		<div class="pdo-container">
			<ol class="pdo-breadcrumbs__list">
				<?php foreach ( $items as $index => $item ) : ?>
					<li class="pdo-breadcrumbs__item">
						<?php if ( $item['url'] && $index < count( $items ) - 1 ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
						<?php else : ?>
							<span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</nav>
	<?php
}

/**
 * Open a section wrapper.
 *
 * @param array $args id, class, background, padding, visibility.
 * @return void
 */
function pdo_modern_section_open( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'id'         => '',
			'class'      => '',
			'background' => '',
			'visibility' => '',
			'tag'        => 'section',
			'label'      => '',
			'container'  => true,
			'style'      => '',
		)
	);

	$classes = array( 'pdo-section' );
	if ( $args['class'] ) {
		$classes[] = $args['class'];
	}
	$bg = pdo_modern_background_class( $args['background'] );
	if ( $bg ) {
		$classes[] = $bg;
	}
	if ( $args['visibility'] ) {
		$classes[] = $args['visibility'];
	}

	printf(
		'<%1$s%2$s>',
		tag_escape( $args['tag'] ),
		pdo_modern_attrs( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside.
			array(
				'id'              => $args['id'],
				'class'           => implode( ' ', $classes ),
				'aria-labelledby' => $args['label'],
				'style'           => $args['style'],
			)
		)
	);

	if ( $args['container'] ) {
		echo '<div class="pdo-container">';
	}
}

/**
 * Close a section wrapper.
 *
 * @param array $args tag, container.
 * @return void
 */
function pdo_modern_section_close( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'tag'       => 'section',
			'container' => true,
		)
	);
	if ( $args['container'] ) {
		echo '</div>';
	}
	printf( '</%s>', tag_escape( $args['tag'] ) );
}

/**
 * Render a standard section header (eyebrow / title / description).
 *
 * @param array $args eyebrow, title, description, align, id, icon.
 * @return void
 */
function pdo_modern_section_header( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow'     => '',
			'title'       => '',
			'description' => '',
			'align'       => 'center',
			'id'          => '',
			'icon'        => '',
			'level'       => 'h2',
		)
	);

	if ( ! $args['eyebrow'] && ! $args['title'] && ! $args['description'] ) {
		return;
	}

	$level = in_array( $args['level'], array( 'h1', 'h2', 'h3' ), true ) ? $args['level'] : 'h2';
	?>
	<header class="pdo-section__header pdo-align-<?php echo esc_attr( $args['align'] ); ?>">
		<?php if ( $args['eyebrow'] ) : ?>
			<p class="pdo-eyebrow">
				<?php if ( $args['icon'] ) : ?>
					<?php pdo_icon( $args['icon'], array( 'size' => 15 ) ); ?>
				<?php endif; ?>
				<?php echo esc_html( $args['eyebrow'] ); ?>
			</p>
		<?php endif; ?>
		<?php if ( $args['title'] ) : ?>
			<<?php echo tag_escape( $level ); ?> class="pdo-section__title"<?php echo $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : ''; ?>>
				<?php echo wp_kses_post( $args['title'] ); ?>
			</<?php echo tag_escape( $level ); ?>>
		<?php endif; ?>
		<?php if ( $args['description'] ) : ?>
			<p class="pdo-section__lede"><?php echo wp_kses_post( $args['description'] ); ?></p>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Render a button.
 *
 * @param string $label   Label.
 * @param string $url     URL.
 * @param array  $args    style, icon, class, attrs.
 * @return void
 */
function pdo_modern_button( $label, $url, $args = array() ) {
	if ( ! $label || ! $url ) {
		return;
	}
	$args = wp_parse_args(
		$args,
		array(
			'style' => 'primary',
			'icon'  => '',
			'class' => '',
			'attrs' => array(),
		)
	);

	$classes = array( 'pdo-btn', 'pdo-btn--' . sanitize_html_class( $args['style'] ) );
	if ( $args['class'] ) {
		$classes[] = $args['class'];
	}

	printf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s<span>%5$s</span></a>',
		esc_attr( implode( ' ', $classes ) ),
		pdo_modern_url( $url ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by helper.
		pdo_modern_attrs( $args['attrs'] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by helper.
		$args['icon'] ? pdo_get_icon( $args['icon'], array( 'size' => 16 ) ) : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted SVG.
		esc_html( $label )
	);
}

/**
 * Custom excerpt length.
 *
 * @param int $length Default length.
 * @return int
 */
function pdo_modern_excerpt_length( $length ) {
	unset( $length );
	return (int) get_theme_mod( 'pdo_excerpt_length', 28 );
}
add_filter( 'excerpt_length', 'pdo_modern_excerpt_length', 999 );

/**
 * Excerpt ellipsis.
 *
 * @param string $more More string.
 * @return string
 */
function pdo_modern_excerpt_more( $more ) {
	unset( $more );
	return '…';
}
add_filter( 'excerpt_more', 'pdo_modern_excerpt_more' );

/**
 * Pagination.
 *
 * @param WP_Query|null $query Query.
 * @return void
 */
function pdo_modern_pagination( $query = null ) {
	global $wp_query;
	$query = $query ? $query : $wp_query;

	if ( $query->max_num_pages < 2 ) {
		return;
	}

	$links = paginate_links(
		array(
			'total'     => $query->max_num_pages,
			'current'   => max( 1, (int) get_query_var( 'paged' ) ),
			'mid_size'  => 1,
			'prev_text' => '<span aria-hidden="true">&larr;</span><span>' . esc_html__( 'Previous', 'pdo-modern' ) . '</span>',
			'next_text' => '<span>' . esc_html__( 'Next', 'pdo-modern' ) . '</span><span aria-hidden="true">&rarr;</span>',
			'type'      => 'array',
		)
	);

	if ( ! $links ) {
		return;
	}
	?>
	<nav class="pdo-pagination" aria-label="<?php esc_attr_e( 'Pagination', 'pdo-modern' ); ?>">
		<ul class="pdo-pagination__list">
			<?php foreach ( $links as $link ) : ?>
				<li><?php echo wp_kses_post( $link ); ?></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Post meta line for news / blog cards.
 *
 * @param int  $post_id Post ID.
 * @param bool $author  Include the author.
 * @return void
 */
function pdo_modern_post_meta( $post_id = 0, $author = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$type    = get_post_type( $post_id );

	/**
	 * Post types whose byline (publish date) is meaningful to visitors.
	 *
	 * Evergreen PDO records – programs, partners, team members, documents –
	 * deliberately do not advertise a publish date.
	 *
	 * @param string[] $types Post types.
	 */
	$dated = (array) apply_filters( 'pdo_modern_dated_post_types', array( 'post', 'pdo_news' ) );

	if ( ! in_array( $type, $dated, true ) ) {
		return;
	}

	if ( null === $author ) {
		$author = ( 'post' === $type );
	}
	?>
	<p class="pdo-meta">
		<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $post_id ) ); ?>">
			<?php pdo_icon( 'calendar', array( 'size' => 14 ) ); ?>
			<?php echo esc_html( get_the_date( '', $post_id ) ); ?>
		</time>
		<?php if ( $author ) : ?>
			<span class="pdo-meta__author">
				<?php pdo_icon( 'user', array( 'size' => 14 ) ); ?>
				<?php echo esc_html( get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ) ); ?>
			</span>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * Resolve the heading tag a card or list item should use.
 *
 * Cards are reused in two very different contexts:
 *
 *  - inside a section that already owns an `<h2>` (the home page, the
 *    dynamic sections) — the card title must then be an `<h3>`;
 *  - directly under the page `<h1>` on an archive or taxonomy listing —
 *    the card title is the next level down, so it must be an `<h2>`.
 *
 * Callers pass `heading` in the template-part args; everything else keeps
 * the historical default of 3. Keeping the decision here means the document
 * outline stays contiguous without duplicating markup per context.
 *
 * @param array $args    Template part args.
 * @param int   $default Level to use when the caller said nothing.
 * @return string Tag name, e.g. `h2`.
 */
function pdo_modern_heading_tag( $args = array(), $default = 3 ) {
	$level = isset( $args['heading'] ) ? (int) $args['heading'] : (int) $default;

	if ( $level < 1 || $level > 6 ) {
		$level = (int) $default;
	}

	return 'h' . $level;
}

/**
 * Component file to use for a given post.
 *
 * @param int|WP_Post $post Post or post ID.
 * @return string Card slug, e.g. `news` for template-parts/components/card-news.php.
 */
function pdo_modern_card_slug( $post = null ) {
	$map = array(
		'pdo_program'  => 'program',
		'pdo_news'     => 'news',
		'pdo_story'    => 'story',
		'pdo_team'     => 'team',
		'pdo_partner'  => 'partner',
		'pdo_document' => 'document',
	);

	$type = get_post_type( $post );

	/**
	 * Filter the post type → card component map.
	 *
	 * @param array  $map  Post type to card slug.
	 * @param string $type Current post type.
	 */
	$map = (array) apply_filters( 'pdo_modern_card_map', $map, $type );

	return isset( $map[ $type ] ) ? $map[ $type ] : 'post';
}

/**
 * Render the card component that matches the post type.
 *
 * @param int|WP_Post $post Post or post ID. Defaults to the current post.
 * @param array       $args Extra args forwarded to the template part.
 * @return void
 */
function pdo_modern_render_card( $post = null, $args = array() ) {
	$post = $post ? get_post( $post ) : get_post();
	if ( ! $post ) {
		return;
	}

	$args['post'] = $post;

	get_template_part( 'template-parts/components/card', pdo_modern_card_slug( $post ), $args );
}

/**
 * Should the sidebar be rendered on this view?
 *
 * @return bool
 */
function pdo_modern_has_sidebar() {
	if ( 'none' === get_theme_mod( 'pdo_blog_sidebar', 'right' ) ) {
		return false;
	}
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		return false;
	}
	if ( is_front_page() || is_page_template() || pdo_modern_current_location() ) {
		return false;
	}
	return is_home() || is_archive() || is_search() || is_singular( 'post' );
}

/**
 * Skip link target id for the current view.
 *
 * @return string
 */
function pdo_modern_main_id() {
	return 'pdo-main';
}

/**
 * Print an SVG donut chart.
 *
 * @param array $data Output of pdo_scholarship_chart().
 * @param array $args size, thickness, center_value, center_label.
 * @return void
 */
function pdo_modern_donut_chart( $data, $args = array() ) {
	if ( empty( $data['rows'] ) || empty( $data['total'] ) ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'size'         => 260,
			'thickness'    => 34,
			'center_value' => number_format_i18n( $data['total'] ),
			'center_label' => __( 'Students', 'pdo-modern' ),
			'title'        => __( 'Distribution', 'pdo-modern' ),
		)
	);

	$size      = (int) $args['size'];
	$thickness = (int) $args['thickness'];
	$radius    = ( $size - $thickness ) / 2;
	$center    = $size / 2;
	$circum    = 2 * M_PI * $radius;
	$offset    = 0;
	$chart_id  = 'pdo-donut-' . wp_unique_id();
	?>
	<figure class="pdo-donut">
		<svg
			class="pdo-donut__svg"
			viewBox="0 0 <?php echo esc_attr( (string) $size ); ?> <?php echo esc_attr( (string) $size ); ?>"
			role="img"
			aria-labelledby="<?php echo esc_attr( $chart_id ); ?>-title"
		>
			<title id="<?php echo esc_attr( $chart_id ); ?>-title"><?php echo esc_html( $args['title'] ); ?></title>
			<circle
				cx="<?php echo esc_attr( (string) $center ); ?>"
				cy="<?php echo esc_attr( (string) $center ); ?>"
				r="<?php echo esc_attr( (string) $radius ); ?>"
				fill="none"
				stroke="var(--pdo-border)"
				stroke-width="<?php echo esc_attr( (string) $thickness ); ?>"
			/>
			<?php foreach ( $data['rows'] as $index => $row ) : ?>
				<?php
				$length  = ( $row['value'] / $data['total'] ) * $circum;
				$segment = sprintf( '%F %F', $length, $circum - $length );
				?>
				<circle
					class="pdo-donut__segment"
					cx="<?php echo esc_attr( (string) $center ); ?>"
					cy="<?php echo esc_attr( (string) $center ); ?>"
					r="<?php echo esc_attr( (string) $radius ); ?>"
					fill="none"
					stroke="<?php echo esc_attr( $row['color'] ); ?>"
					stroke-width="<?php echo esc_attr( (string) $thickness ); ?>"
					stroke-dasharray="<?php echo esc_attr( $segment ); ?>"
					stroke-dashoffset="<?php echo esc_attr( (string) ( -$offset ) ); ?>"
					stroke-linecap="butt"
					transform="rotate(-90 <?php echo esc_attr( (string) $center ); ?> <?php echo esc_attr( (string) $center ); ?>)"
					style="--pdo-donut-delay: <?php echo esc_attr( (string) ( $index * 0.12 ) ); ?>s"
				>
					<title><?php echo esc_html( $row['label'] . ' — ' . number_format_i18n( $row['value'] ) ); ?></title>
				</circle>
				<?php $offset += $length; ?>
			<?php endforeach; ?>
		</svg>
		<figcaption class="pdo-donut__center">
			<span class="pdo-donut__value"><?php echo esc_html( $args['center_value'] ); ?></span>
			<span class="pdo-donut__label"><?php echo esc_html( $args['center_label'] ); ?></span>
		</figcaption>
	</figure>
	<?php
}

/**
 * Render the editable WordPress content of the current page, when it has any.
 *
 * Managed pages are built from the plugin settings, but editors can still add
 * free-form blocks underneath; this renders them inside a prose container.
 *
 * @param string $class Extra classes for the section.
 * @return void
 */
function pdo_modern_page_content( $class = 'pdo-section--tight' ) {
	if ( ! is_singular() ) {
		return;
	}

	$post = get_post();
	if ( ! $post || '' === trim( wp_strip_all_tags( $post->post_content ) ) ) {
		return;
	}

	pdo_modern_section_open( array( 'class' => $class . ' pdo-page-content' ) );
	echo '<div class="pdo-prose pdo-container--narrow">';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
	echo pdo_post_content( $post );
	echo '</div>';
	pdo_modern_section_close();
}
