<?php
/**
 * Fallback template for pages, posts, archives, search and 404.
 *
 * Jobs use archive-job.php and single-job.php.
 *
 * @package Jobs_Board
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main wrap">
	<?php if ( is_404() ) : ?>
		<h1><?php esc_html_e( 'Page not found', 'jobs-board' ); ?></h1>
		<p><a href="<?php echo esc_url( get_post_type_archive_link( 'job' ) ); ?>"><?php esc_html_e( 'Browse all jobs', 'jobs-board' ); ?></a></p>

	<?php elseif ( is_singular() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>

	<?php else : ?>
		<h1>
			<?php
			if ( is_home() && ! is_front_page() ) {
				single_post_title();
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				esc_html_e( 'Latest posts', 'jobs-board' );
			}
			?>
		</h1>
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'job-card' ); ?>>
					<h2 class="job-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'jobs-board' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</main>

<?php
get_footer();
