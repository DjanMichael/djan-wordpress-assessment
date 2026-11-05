<?php
/**
 * Jobs Board theme setup.
 *
 * @package Jobs_Board
 */

defined( 'ABSPATH' ) || exit;

define( 'JOBS_BOARD_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/jobs.php';

/**
 * Theme supports and menu location.
 */
function jobs_board_setup() {
	load_theme_textdomain( 'jobs-board', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'jobs-board' ),
		)
	);
}
add_action( 'after_setup_theme', 'jobs_board_setup' );

/**
 * Stylesheet.
 */
function jobs_board_enqueue_assets() {
	wp_enqueue_style( 'jobs-board', get_stylesheet_uri(), array(), JOBS_BOARD_VERSION );
}
add_action( 'wp_enqueue_scripts', 'jobs_board_enqueue_assets' );

/**
 * Menu shown before one is assigned: Home and Jobs.
 *
 * @param array $args wp_nav_menu() arguments.
 */
function jobs_board_menu_fallback( $args ) {
	printf(
		'<ul class="%1$s"><li><a href="%2$s">%3$s</a></li><li><a href="%4$s">%5$s</a></li></ul>',
		esc_attr( $args['menu_class'] ),
		esc_url( home_url( '/' ) ),
		esc_html__( 'Home', 'jobs-board' ),
		esc_url( get_post_type_archive_link( 'job' ) ),
		esc_html__( 'Jobs', 'jobs-board' )
	);
}
