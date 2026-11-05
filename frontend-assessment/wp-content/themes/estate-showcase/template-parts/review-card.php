<?php
/**
 * One Google review: dark speech bubble with the reviewer's name underneath.
 *
 * @package Estate_Showcase
 *
 * @var array $args {
 *     @type array $review Review from estate_showcase_reviews().
 * }
 */

defined( 'ABSPATH' ) || exit;

$estate_showcase_review = $args['review'];
$estate_showcase_rating = max( 0, min( 5, (int) $estate_showcase_review['rating'] ) );
?>
<figure class="es-review">
	<blockquote class="es-review__bubble">
		<p class="es-review__stars" role="img" aria-label="<?php /* translators: %d: star rating. */ echo esc_attr( sprintf( __( 'Rated %d out of 5', 'estate-showcase' ), $estate_showcase_rating ) ); ?>">
			<?php echo str_repeat( '<i class="fa-solid fa-star" aria-hidden="true"></i>', $estate_showcase_rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?>
		</p>
		<p class="es-review__text"><?php echo esc_html( $estate_showcase_review['text'] ); ?></p>
	</blockquote>
	<figcaption class="es-review__author">
		<i class="fa-brands fa-google" aria-hidden="true"></i>
		<?php echo esc_html( $estate_showcase_review['author'] ); ?>
	</figcaption>
</figure>
