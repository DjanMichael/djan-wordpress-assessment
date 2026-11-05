<?php
/**
 * Sets up the demo landing page on a fresh install. Safe to run more than once.
 *
 * Run from the WordPress root after activating the Estate Showcase theme:
 *   wp eval-file setup/demo-content.php     (WP-CLI)
 *   php setup/demo-content.php              (plain PHP CLI)
 *
 * It sets the site title/tagline, creates a "Home" page using the
 * "Featured Listings" template, makes it the front page, switches on pretty
 * permalinks and builds the Primary/Footer menus. Nothing is deleted.
 */

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 404 );
	exit;
}

if ( ! defined( 'ABSPATH' ) ) {
	require dirname( __DIR__ ) . '/wp-load.php';
}

if ( 'estate-showcase' !== get_template() ) {
	fwrite( STDERR, "Activate the Estate Showcase theme first (Appearance > Themes), then run this again.\n" );
	exit( 1 );
}

update_option( 'blogname', 'Brightmoor Estates' );
// Front-page <title> is "Site name – Tagline": kept under 60 characters so search results show it in full.
update_option( 'blogdescription', 'Lettings & sales in Southern England' );

// Home page with the landing template.
$home = get_page_by_path( 'home' );
if ( ! $home ) {
	$home_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Homes to rent and buy across the South of England',
			'post_name'    => 'home',
			'post_excerpt' => 'Browse featured homes to rent and buy in Brockenhurst, Exeter and Sherborne, and read 5-star Google reviews from our clients.',
			'post_content' => "<!-- wp:paragraph -->\n<p>Hand-picked homes from our local team. Browse this week's featured listings and see what our clients say about us.</p>\n<!-- /wp:paragraph -->",
		),
		true
	);
	if ( is_wp_error( $home_id ) ) {
		fwrite( STDERR, $home_id->get_error_message() . "\n" );
		exit( 1 );
	}
} else {
	$home_id = $home->ID;
}
update_post_meta( $home_id, '_wp_page_template', 'page-templates/featured-listings.php' );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );

$GLOBALS['wp_rewrite']->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules();

// One menu for both locations: Home plus the on-page sections.
$menu = wp_get_nav_menu_object( 'Primary' );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( 'Primary' );
	$links   = array(
		'Home'              => home_url( '/' ),
		'Featured listings' => home_url( '/#featured' ),
		'Reviews'           => home_url( '/#reviews' ),
		'Contact'           => home_url( '/#contact' ),
	);
	foreach ( $links as $label => $url ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $label,
				'menu-item-url'    => $url,
				'menu-item-type'   => 'custom',
				'menu-item-status' => 'publish',
			)
		);
	}
} else {
	$menu_id = $menu->term_id;
}

$locations            = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $menu_id;
$locations['footer']  = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

echo 'Demo content ready: ' . home_url( '/' ) . "\n";
