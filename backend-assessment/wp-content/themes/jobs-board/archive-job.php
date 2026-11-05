<?php
/**
 * Jobs archive (/jobs/): every job posting with its custom fields.
 *
 * @package Jobs_Board
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main wrap">
	<header class="page-header">
		<h1><?php post_type_archive_title(); ?></h1>
		<?php if ( have_posts() ) : ?>
			<p class="page-header__count">
				<?php
				$jobs_board_total = (int) $GLOBALS['wp_query']->found_posts;
				/* translators: %s: number of open jobs. */
				echo esc_html( sprintf( _n( '%s open position', '%s open positions', $jobs_board_total, 'jobs-board' ), number_format_i18n( $jobs_board_total ) ) );
				?>
			</p>
		<?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="job-list">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'job-card' ); ?>>
					<h2 class="job-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php get_template_part( 'template-parts/job-fields' ); ?>
					<?php if ( has_excerpt() || get_the_content() ) : ?>
						<div class="job-card__summary"><?php the_excerpt(); ?></div>
					<?php endif; ?>
					<a class="job-card__link" href="<?php the_permalink(); ?>">
						<?php
						/* translators: %s: job post title. */
						printf( esc_html__( 'View job%s', 'jobs-board' ), '<span class="screen-reader-text">: ' . esc_html( get_the_title() ) . '</span>' );
						?>
						<span aria-hidden="true">&rarr;</span>
					</a>
				</article>
			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'There are no open positions right now. Please check back soon.', 'jobs-board' ); ?></p>
	<?php endif; ?>
</main>

<?php
get_footer();
