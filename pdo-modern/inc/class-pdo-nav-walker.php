<?php
/**
 * Accessible navigation walker with bilingual labels and bundled icons.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Primary menu walker.
 */
class PDO_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Render the mobile variant (details/summary disclosure) instead of hover dropdowns.
	 *
	 * @var bool
	 */
	public $mobile = false;

	/**
	 * Start a submenu level.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$classes = array( 'pdo-submenu', 'pdo-submenu--depth-' . $depth );

		if ( 0 === $depth && ! $this->mobile && get_theme_mod( 'pdo_nav_mega', true ) ) {
			$classes[] = 'pdo-submenu--mega';
		}

		$output .= "\n" . $indent . '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">' . "\n";
	}

	/**
	 * Close a submenu level.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= str_repeat( "\t", $depth ) . "</ul>\n";
	}

	/**
	 * Start an element.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		$classes[] = 'pdo-menu-item';
		$classes[] = 'pdo-menu-item--depth-' . $depth;

		$has_children = in_array( 'menu-item-has-children', $classes, true );
		if ( $has_children ) {
			$classes[] = 'pdo-has-children';
		}

		$class_names = implode( ' ', array_filter( array_unique( apply_filters( 'nav_menu_css_class', $classes, $item, $args, $depth ) ) ) );
		$menu_id     = 'pdo-menu-item-' . $item->ID;
		$submenu_id  = 'pdo-submenu-' . $item->ID;

		$output .= '<li id="' . esc_attr( $menu_id ) . '" class="' . esc_attr( $class_names ) . '">';

		$atts = array(
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
			'href'   => ! empty( $item->url ) ? $item->url : '',
			'class'  => 'pdo-menu-link',
		);

		if ( '_blank' === $atts['target'] && empty( $atts['rel'] ) ) {
			$atts['rel'] = 'noopener';
		}
		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true ) ) {
			$atts['aria-current'] = 'page';
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' === $value || false === $value ) {
				continue;
			}
			$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}

		$icon = '';
		if ( 0 === $depth || $this->mobile ) {
			$icon_name = get_post_meta( $item->ID, '_pdo_menu_icon', true );
			if ( $icon_name && get_theme_mod( 'pdo_nav_icons', true ) ) {
				$icon = '<span class="pdo-menu-link__icon" aria-hidden="true">' . pdo_get_icon( $icon_name, array( 'size' => 16 ) ) . '</span>';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$description = '';
		if ( $depth > 0 && ! empty( $item->description ) ) {
			$description = '<span class="pdo-menu-link__desc">' . esc_html( $item->description ) . '</span>';
		}

		$caret = '';
		if ( $has_children && get_theme_mod( 'pdo_nav_caret', true ) ) {
			$caret = '<span class="pdo-menu-link__caret" aria-hidden="true"></span>';
		}

		$item_output  = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $icon;
		$item_output .= '<span class="pdo-menu-link__label">' . esc_html( $title ) . '</span>';
		$item_output .= $description;
		$item_output .= $caret;
		$item_output .= '</a>';

		if ( $has_children ) {
			$item_output .= sprintf(
				'<button type="button" class="pdo-submenu-toggle" aria-expanded="false" aria-controls="%1$s"><span class="screen-reader-text">%2$s</span><span class="pdo-submenu-toggle__icon" aria-hidden="true"></span></button>',
				esc_attr( $submenu_id ),
				esc_html(
					sprintf(
						/* translators: %s: menu item label. */
						__( 'Toggle the %s submenu', 'pdo-modern' ),
						wp_strip_all_tags( $title )
					)
				)
			);
		}

		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
