<?php
/**
 * Fallback template for posts, pages, archives, search and 404.
 *
 * The landing page uses page-templates/featured-listings.php.
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main container py-5 es-content">
	<?php if ( is_404() ) : ?>
		<h1><?php esc_html_e( 'Page not found', 'estate-showcase' ); ?></h1>
		<p><?php esc_html_e( 'The page you were looking for has moved or no longer exists.', 'estate-showcase' ); ?></p>
		<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'estate-showcase' ); ?></a>

	<?php elseif ( is_singular() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'mx-auto es-narrow' ); ?>>
				<h1 class="mb-4"><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>

	<?php else : ?>
		<h1 class="mb-4">
			<?php
			if ( is_home() && ! is_front_page() ) {
				single_post_title();
			} elseif ( is_search() ) {
				/* translators: %s: search query. */
				printf( esc_html__( 'Search results for "%s"', 'estate-showcase' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				esc_html_e( 'Latest news', 'estate-showcase' );
			}
			?>
		</h1>

		<?php if ( have_posts() ) : ?>
			<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<div class="col">
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'card h-100' ); ?>>
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large', array( 'class' => 'card-img-top' ) ); ?>
							<?php endif; ?>
							<div class="card-body">
								<h2 class="h5"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<?php the_excerpt(); ?>
							</div>
						</article>
					</div>
				<?php endwhile; ?>
			</div>
			<div class="mt-4"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'estate-showcase' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</main>

<?php
get_footer();
