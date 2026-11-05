<?php
/**
 * Single job (/jobs/{slug}/): the posting, its custom fields and description.
 *
 * @package Jobs_Board
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main wrap">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'job-single' ); ?>>
			<p class="job-single__back">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'job' ) ); ?>"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'All jobs', 'jobs-board' ); ?></a>
			</p>

			<header class="page-header">
				<h1><?php the_title(); ?></h1>
				<p class="job-single__date">
					<?php
					/* translators: %s: date the job was posted. */
					printf( esc_html__( 'Posted %s', 'jobs-board' ), '<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>' );
					?>
				</p>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large', array( 'class' => 'job-single__image' ) ); ?>
			<?php endif; ?>

			<section class="job-single__details" aria-labelledby="job-details-title">
				<h2 id="job-details-title" class="screen-reader-text"><?php esc_html_e( 'Job details', 'jobs-board' ); ?></h2>
				<?php get_template_part( 'template-parts/job-fields' ); ?>
			</section>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
