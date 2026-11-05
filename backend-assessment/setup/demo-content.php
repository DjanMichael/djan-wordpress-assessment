<?php
/**
 * Adds six sample jobs and a menu on a fresh install. Safe to run more than once.
 *
 * Run from the WordPress root after activating the Jobs Board theme:
 *   wp eval-file setup/demo-content.php     (WP-CLI)
 *   php setup/demo-content.php              (plain PHP CLI)
 *
 * It sets the site title, switches on pretty permalinks (so /jobs/ works),
 * creates the sample jobs if no jobs exist yet, adds a Home page that links to
 * the jobs list, and builds a Home / Jobs menu. Nothing is deleted.
 */

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 404 );
	exit;
}

if ( ! defined( 'ABSPATH' ) ) {
	require dirname( __DIR__ ) . '/wp-load.php';
}

if ( 'jobs-board' !== get_template() || ! post_type_exists( 'job' ) ) {
	fwrite( STDERR, "Activate the Jobs Board theme first (Appearance > Themes), then run this again.\n" );
	exit( 1 );
}

update_option( 'blogname', 'Essex Digital Careers' );
update_option( 'blogdescription', 'Web, SEO and marketing jobs across Essex.' );
// Pretty permalinks, applied to this request too so the links built below use them.
$GLOBALS['wp_rewrite']->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules();

$jobs = array(
	array( 'Senior WordPress Developer – Chelmsford', 'Senior WordPress Developer', '£45,000 – £55,000 per year', 'Chelmsford, Essex (Hybrid)', 'Lead custom theme and plugin builds for agency clients, review code and mentor two junior developers.' ),
	array( 'Front-end Developer – Colchester', 'Front-end Developer', '£32,000 – £38,000 per year', 'Colchester, Essex', 'Turn Figma designs into fast, accessible Bootstrap and WordPress pages that score well on Core Web Vitals.' ),
	array( 'SEO Executive – Southend', 'SEO Executive', '£26,000 – £30,000 per year', 'Southend-on-Sea, Essex', 'Run technical audits, keyword research and monthly reporting for a portfolio of local businesses.' ),
	array( 'Part-time Content Writer', 'Content Writer', '£14 per hour (20 hours a week)', 'Remote (UK)', 'Write well-researched blog posts, landing pages and product copy that rank and convert.' ),
	array( 'PPC Account Manager – Basildon', 'PPC Account Manager', '£35,000 per year + bonus', 'Basildon, Essex', 'Plan, launch and optimise Google Ads and Microsoft Ads campaigns with monthly budgets up to £50k.' ),
	array( 'Junior Web Designer – Brentwood', 'Junior Web Designer', '£24,000 per year', 'Brentwood, Essex (Hybrid)', 'Design page layouts, social graphics and email templates alongside our senior designer.' ),
);

$existing = new WP_Query(
	array(
		'post_type'      => 'job',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	)
);

if ( ! $existing->have_posts() ) {
	foreach ( $jobs as $index => $job ) {
		list( $post_title, $job_title, $salary, $location, $summary ) = $job;

		$job_id = wp_insert_post(
			array(
				'post_type'    => 'job',
				'post_status'  => 'publish',
				'post_title'   => $post_title,
				'post_excerpt' => $summary,
				'post_content' => "<!-- wp:paragraph -->\n<p>{$summary}</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">What you'll do</h2>\n<!-- /wp:heading -->\n\n<!-- wp:list -->\n<ul class=\"wp-block-list\"><!-- wp:list-item -->\n<li>Work closely with clients and the delivery team.</li>\n<!-- /wp:list-item -->\n\n<!-- wp:list-item -->\n<li>Own your projects from brief to launch.</li>\n<!-- /wp:list-item --></ul>\n<!-- /wp:list -->\n\n<!-- wp:paragraph -->\n<p>To apply, send your CV and a short cover note to the hiring team.</p>\n<!-- /wp:paragraph -->",
				// Newest first in the archive, in the order listed above.
				'post_date'    => wp_date( 'Y-m-d H:i:s', time() - ( $index * DAY_IN_SECONDS ) ),
			),
			true
		);

		if ( is_wp_error( $job_id ) ) {
			fwrite( STDERR, $job_id->get_error_message() . "\n" );
			exit( 1 );
		}

		update_post_meta( $job_id, '_job_title', $job_title );
		update_post_meta( $job_id, '_job_salary', $salary );
		update_post_meta( $job_id, '_job_location', $location );
	}
}

// A simple Home page pointing to the jobs archive.
$home = get_page_by_path( 'home' );
if ( ! $home ) {
	$archive = get_post_type_archive_link( 'job' );
	$home_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Find your next role in digital',
			'post_name'    => 'home',
			'post_content' => "<!-- wp:paragraph -->\n<p>We list web development, SEO and marketing roles with agencies and businesses across Essex.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"{$archive}\">Browse all jobs</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->",
		)
	);
} else {
	$home_id = $home->ID;
}
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );

$menu = wp_get_nav_menu_object( 'Primary' );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( 'Primary' );
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'  => 'Home',
			'menu-item-url'    => home_url( '/' ),
			'menu-item-type'   => 'custom',
			'menu-item-status' => 'publish',
		)
	);
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'  => 'Jobs',
			'menu-item-type'   => 'post_type_archive',
			'menu-item-object' => 'job',
			'menu-item-status' => 'publish',
		)
	);
} else {
	$menu_id = $menu->term_id;
}

$locations            = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

echo 'Demo content ready: ' . get_post_type_archive_link( 'job' ) . "\n";
