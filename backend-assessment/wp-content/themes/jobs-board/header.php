<?php
/**
 * Site header.
 *
 * @package Jobs_Board
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'jobs-board' ); ?></a>

<header class="site-header">
	<div class="wrap site-header__inner">
		<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
		<?php
		wp_nav_menu(
			array(
				'theme_location'       => 'primary',
				'container'            => 'nav',
				'container_aria_label' => __( 'Main navigation', 'jobs-board' ),
				'menu_class'           => 'site-nav',
				'depth'                => 1,
				'fallback_cb'          => 'jobs_board_menu_fallback',
			)
		);
		?>
	</div>
</header>
