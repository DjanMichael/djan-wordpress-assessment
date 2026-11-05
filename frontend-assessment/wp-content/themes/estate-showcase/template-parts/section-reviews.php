<?php
/**
 * Design 2 — six Google reviews on a three-column Bootstrap carousel.
 *
 * The server renders three reviews per slide; assets/js/theme.js regroups them
 * to two per slide on tablets and one on phones.
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

$estate_showcase_reviews = estate_showcase_reviews();

if ( empty( $estate_showcase_reviews ) ) {
	return;
}

$estate_showcase_slides = array_chunk( $estate_showcase_reviews, 3 );
$estate_showcase_total  = count( $estate_showcase_reviews );
?>
<section id="reviews" class="es-reviews" aria-labelledby="reviews-title">
	<div class="container">
		<h2 id="reviews-title" class="es-reviews__brand">
			<span class="es-reviews__google">Google</span>
			<span class="es-reviews__label">
				<?php esc_html_e( 'Reviews', 'estate-showcase' ); ?>
				<span aria-hidden="true"><?php echo str_repeat( '<i class="fa-solid fa-star"></i>', 5 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?></span>
			</span>
		</h2>

		<div id="reviews-carousel" class="carousel slide" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Customer reviews', 'estate-showcase' ); ?>" data-es-reviews>
			<div class="carousel-inner">
				<?php foreach ( $estate_showcase_slides as $estate_showcase_slide_index => $estate_showcase_slide ) : ?>
					<div class="carousel-item<?php echo 0 === $estate_showcase_slide_index ? ' active' : ''; ?>" role="group" aria-roledescription="slide">
						<div class="row g-4">
							<?php foreach ( $estate_showcase_slide as $estate_showcase_review ) : ?>
								<div class="col-12 col-md-6 col-lg-4" data-es-review>
									<?php get_template_part( 'template-parts/review-card', null, array( 'review' => $estate_showcase_review ) ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="carousel-indicators">
				<?php foreach ( $estate_showcase_slides as $estate_showcase_slide_index => $estate_showcase_slide ) : ?>
					<?php
					$estate_showcase_from = $estate_showcase_slide_index * 3 + 1;
					$estate_showcase_to   = $estate_showcase_from + count( $estate_showcase_slide ) - 1;
					/* translators: 1: first review number, 2: last review number, 3: total reviews. */
					$estate_showcase_label = sprintf( __( 'Show reviews %1$d to %2$d of %3$d', 'estate-showcase' ), $estate_showcase_from, $estate_showcase_to, $estate_showcase_total );
					?>
					<button
						type="button"
						data-bs-target="#reviews-carousel"
						data-bs-slide-to="<?php echo esc_attr( $estate_showcase_slide_index ); ?>"
						aria-label="<?php echo esc_attr( $estate_showcase_label ); ?>"
						<?php echo 0 === $estate_showcase_slide_index ? 'class="active" aria-current="true"' : ''; ?>
					></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
