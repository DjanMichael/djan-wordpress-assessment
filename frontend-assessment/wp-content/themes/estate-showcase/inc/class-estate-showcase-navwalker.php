<?php
/**
 * Prints wp_nav_menu() items as a standard Bootstrap 5 navbar (with dropdowns).
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bootstrap 5 nav walker: nav-item / nav-link, dropdown-menu / dropdown-item.
 */
class Estate_Showcase_Navwalker extends Walker_Nav_Menu {

	/**
	 * Opens a submenu.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="dropdown-menu">';
	}

	/**
	 * Closes a submenu.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	/**
	 * Opens a menu item.
	 *
	 * @param string   $output            Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object       Menu item data object.
	 * @param int      $depth             Depth of menu item.
	 * @param stdClass $args              An object of wp_nav_menu() arguments.
	 * @param int      $current_object_id Optional. ID of the current menu item.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item         = $data_object;
		$classes      = empty( $item->classes ) ? array() : array_filter( (array) $item->classes );
		$has_children = in_array( 'menu-item-has-children', $classes, true ) && 0 === $depth;
		// WordPress marks "/#section" links current on the home page; only the real page link should be.
		$is_anchor  = false !== strpos( (string) $item->url, '#' );
		$is_current = ! $is_anchor && in_array( 'current-menu-item', $classes, true );
		$is_active  = $is_current || ( ! $is_anchor && array_intersect( array( 'current-menu-ancestor', 'current-menu-parent' ), $classes ) );

		$li_classes = array_merge( 0 === $depth ? array( 'nav-item' ) : array(), $has_children ? array( 'dropdown' ) : array(), $classes );
		$output    .= '<li class="' . esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', $li_classes, $item, $args, $depth ) ) ) . '">';

		$atts = array(
			'href'   => ! empty( $item->url ) ? $item->url : '#',
			'title'  => $item->attr_title,
			'target' => $item->target,
			'rel'    => '_blank' === $item->target && empty( $item->xfn ) ? 'noopener' : $item->xfn,
			'class'  => ( 0 === $depth ? 'nav-link' : 'dropdown-item' ) . ( $is_active ? ' active' : '' ),
		);

		if ( $is_current ) {
			$atts['aria-current'] = 'page';
		}

		if ( $has_children ) {
			$atts['class']         .= ' dropdown-toggle';
			$atts['role']           = 'button';
			$atts['data-bs-toggle'] = 'dropdown';
			$atts['aria-expanded']  = 'false';
		}

		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = 'href' === $attr ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$output .= '<a' . $attributes . '>' . $title . '</a>';
	}

	/**
	 * Closes a menu item.
	 *
	 * @param string   $output      Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object Menu item data object. Not used.
	 * @param int      $depth       Depth of page. Not Used.
	 * @param stdClass $args        An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}
