<?php
/**
 * Template Name: Featured Listings
 * Template Post Type: page
 *
 * Landing page: intro (page title + content), the featured listings grid
 * (Design 1) and the Google reviews slider (Design 2).
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<section class="es-hero" aria-labelledby="page-title">
			<div class="container">
				<h1 id="page-title" class="display-5 mb-3"><?php the_title(); ?></h1>
				<div class="es-hero__intro mb-4"><?php the_content(); ?></div>
				<div class="d-flex flex-wrap gap-2">
					<a class="btn btn-gold btn-lg" href="#featured">
						<i class="fa-solid fa-magnifying-glass me-1" aria-hidden="true"></i>
						<?php esc_html_e( 'View featured listings', 'estate-showcase' ); ?>
					</a>
					<a class="btn btn-outline-light btn-lg" href="#reviews">
						<i class="fa-solid fa-star me-1" aria-hidden="true"></i>
						<?php esc_html_e( 'Read our reviews', 'estate-showcase' ); ?>
					</a>
				</div>
			</div>
		</section>
	<?php endwhile; ?>

	<?php get_template_part( 'template-parts/section', 'featured-listings' ); ?>
	<?php get_template_part( 'template-parts/section', 'reviews' ); ?>
</main>

<?php
get_footer();
