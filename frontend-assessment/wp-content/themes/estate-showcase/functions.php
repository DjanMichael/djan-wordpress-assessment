<?php
/**
 * Estate Showcase theme setup and assets.
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

define( 'ESTATE_SHOWCASE_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/class-estate-showcase-navwalker.php';
require_once get_template_directory() . '/inc/content.php';
require_once get_template_directory() . '/inc/seo.php';

/**
 * Theme supports and menu locations.
 */
function estate_showcase_setup() {
	load_theme_textdomain( 'estate-showcase', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 88,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	// Page excerpts double as the meta description (see inc/seo.php).
	add_post_type_support( 'page', 'excerpt' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'estate-showcase' ),
			'footer'  => __( 'Footer links', 'estate-showcase' ),
		)
	);
}
add_action( 'after_setup_theme', 'estate_showcase_setup' );

/**
 * Third-party assets, pinned and loaded from CDNs with Subresource Integrity.
 *
 * @return array<string, array{url: string, integrity: string}>
 */
function estate_showcase_cdn_assets() {
	return array(
		'bootstrap'    => array(
			'url'       => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
			'integrity' => 'sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH',
		),
		'font-awesome' => array(
			'url'       => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
			'integrity' => 'sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==',
		),
		'bootstrap-js' => array(
			'url'       => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
			'integrity' => 'sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz',
		),
	);
}

/**
 * Styles and scripts.
 */
function estate_showcase_enqueue_assets() {
	$cdn = estate_showcase_cdn_assets();

	wp_enqueue_style( 'estate-showcase-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts URL is versioned by its query.
	wp_enqueue_style( 'bootstrap', $cdn['bootstrap']['url'], array(), '5.3.3' );
	wp_enqueue_style( 'font-awesome', $cdn['font-awesome']['url'], array(), '6.5.2' );
	wp_enqueue_style( 'estate-showcase', get_stylesheet_uri(), array( 'bootstrap', 'font-awesome' ), ESTATE_SHOWCASE_VERSION );

	wp_enqueue_script(
		'bootstrap',
		$cdn['bootstrap-js']['url'],
		array(),
		'5.3.3',
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_enqueue_script(
		'estate-showcase',
		get_template_directory_uri() . '/assets/js/theme.js',
		array( 'bootstrap' ),
		ESTATE_SHOWCASE_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'estate_showcase_enqueue_assets' );

/**
 * Adds integrity + crossorigin to the pinned CDN files.
 *
 * Versioned URLs get "?ver=" appended by WordPress, so match on the handle.
 *
 * @param string $tag    Generated <link> or <script> tag.
 * @param string $handle Asset handle.
 * @return string
 */
function estate_showcase_add_sri( $tag, $handle ) {
	$cdn = estate_showcase_cdn_assets();
	$key = 'bootstrap' === $handle && false !== strpos( $tag, '<script' ) ? 'bootstrap-js' : $handle;

	if ( ! isset( $cdn[ $key ] ) ) {
		return $tag;
	}

	$attributes = sprintf( ' integrity="%s" crossorigin="anonymous"', esc_attr( $cdn[ $key ]['integrity'] ) );

	return preg_replace( '/^<(link|script)\b/', '<$1' . $attributes, ltrim( $tag ), 1 );
}
add_filter( 'style_loader_tag', 'estate_showcase_add_sri', 10, 2 );
add_filter( 'script_loader_tag', 'estate_showcase_add_sri', 10, 2 );

/**
 * Early connections to the font and CDN hosts.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function estate_showcase_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
		$urls[] = array(
			'href'        => 'https://cdn.jsdelivr.net',
			'crossorigin' => 'anonymous',
		);
		$urls[] = array(
			'href'        => 'https://cdnjs.cloudflare.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'estate_showcase_resource_hints', 10, 2 );

/**
 * Links shown when no menu is assigned to a location yet.
 *
 * @return array<string, string> Label => URL.
 */
function estate_showcase_default_links() {
	return array(
		__( 'Home', 'estate-showcase' )              => home_url( '/' ),
		__( 'Featured listings', 'estate-showcase' ) => home_url( '/#featured' ),
		__( 'Reviews', 'estate-showcase' )           => home_url( '/#reviews' ),
		__( 'Contact', 'estate-showcase' )           => home_url( '/#contact' ),
	);
}

/**
 * Fallback for wp_nav_menu() — same markup as the real menu.
 *
 * @param array $args wp_nav_menu() arguments.
 */
function estate_showcase_menu_fallback( $args ) {
	$is_primary = isset( $args['theme_location'] ) && 'primary' === $args['theme_location'];
	$items      = '';

	foreach ( estate_showcase_default_links() as $label => $url ) {
		$items .= sprintf(
			'<li class="%1$s"><a class="%2$s" href="%3$s">%4$s</a></li>',
			$is_primary ? 'nav-item' : '',
			$is_primary ? 'nav-link' : '',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	printf( '<ul class="%s">%s</ul>', esc_attr( $args['menu_class'] ), $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts above.
}
