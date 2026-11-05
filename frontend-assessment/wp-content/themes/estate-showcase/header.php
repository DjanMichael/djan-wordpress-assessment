<?php
/**
 * Site header with the standard Bootstrap 5 navbar.
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

$estate_showcase_contact = estate_showcase_contact();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#4b3fa7">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link visually-hidden-focusable btn btn-light" href="#main"><?php esc_html_e( 'Skip to content', 'estate-showcase' ); ?></a>

<header class="site-header sticky-top">
	<nav class="navbar navbar-expand-lg bg-white shadow-sm es-navbar" aria-label="<?php esc_attr_e( 'Main navigation', 'estate-showcase' ); ?>">
		<div class="container">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<i class="fa-solid fa-house-chimney" aria-hidden="true"></i>
					<span><?php bloginfo( 'name' ); ?></span>
				</a>
			<?php endif; ?>

			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primary-navigation" aria-controls="primary-navigation" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'estate-showcase' ); ?>">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="primary-navigation">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-2',
						'depth'          => 2,
						'walker'         => new Estate_Showcase_Navwalker(),
						'fallback_cb'    => 'estate_showcase_menu_fallback',
					)
				);
				?>
				<a class="btn btn-primary ms-lg-3 mb-2 mb-lg-0" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $estate_showcase_contact['phone'] ) ); ?>">
					<i class="fa-solid fa-phone me-1" aria-hidden="true"></i>
					<?php esc_html_e( 'Call us', 'estate-showcase' ); ?>
				</a>
			</div>
		</div>
	</nav>
</header>
