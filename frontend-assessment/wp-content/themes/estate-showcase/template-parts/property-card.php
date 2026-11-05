<?php
/**
 * One featured property card.
 *
 * @package Estate_Showcase
 *
 * @var array $args {
 *     @type array  $property Listing from estate_showcase_properties().
 *     @type string $id       Element id (used as the listing's URL fragment).
 * }
 */

defined( 'ABSPATH' ) || exit;

$estate_showcase_property = $args['property'];
$estate_showcase_title_id = $args['id'] . '-title';
?>
<article id="<?php echo esc_attr( $args['id'] ); ?>" class="card h-100 es-property" aria-labelledby="<?php echo esc_attr( $estate_showcase_title_id ); ?>">
	<figure class="es-property__media">
		<img
			src="<?php echo esc_url( estate_showcase_image_url( $estate_showcase_property['image'] ) ); ?>"
			alt="<?php echo esc_attr( $estate_showcase_property['image_alt'] ); ?>"
			width="900"
			height="600"
			loading="lazy"
			decoding="async"
		>
		<figcaption class="es-property__overlay">
			<p class="es-property__price"><?php echo esc_html( $estate_showcase_property['price'] ); ?></p>
			<span class="es-badge es-badge--<?php echo esc_attr( $estate_showcase_property['status'] ); ?>">
				<?php echo esc_html( estate_showcase_status_label( $estate_showcase_property['status'] ) ); ?>
			</span>
		</figcaption>
	</figure>

	<div class="card-body d-flex flex-column">
		<h3 id="<?php echo esc_attr( $estate_showcase_title_id ); ?>" class="es-property__title"><?php echo esc_html( $estate_showcase_property['address'] ); ?></h3>

		<ul class="es-property__specs">
			<li>
				<?php
				/* translators: %s: number of bedrooms. */
				echo esc_html( sprintf( _n( '%s Bedroom', '%s Bedrooms', $estate_showcase_property['bedrooms'], 'estate-showcase' ), number_format_i18n( $estate_showcase_property['bedrooms'] ) ) );
				?>
			</li>
			<li>
				<?php
				/* translators: %s: number of bathrooms. */
				echo esc_html( sprintf( _n( '%s Bathroom', '%s Bathrooms', $estate_showcase_property['bathrooms'], 'estate-showcase' ), number_format_i18n( $estate_showcase_property['bathrooms'] ) ) );
				?>
			</li>
			<li><?php echo esc_html( $estate_showcase_property['type'] ); ?></li>
		</ul>

		<div class="es-property__footer mt-auto">
			<button type="button" class="es-fav" aria-pressed="false" data-es-favourite>
				<i class="fa-regular fa-heart" aria-hidden="true"></i>
				<span class="visually-hidden">
					<?php
					/* translators: %s: property address. */
					printf( esc_html__( 'Save %s to favourites', 'estate-showcase' ), esc_html( $estate_showcase_property['address'] ) );
					?>
				</span>
			</button>
			<p class="es-property__available">
				<i class="fa-regular fa-calendar" aria-hidden="true"></i>
				<?php
				/* translators: %s: availability, e.g. "Now" or a date. */
				printf( esc_html__( 'Available: %s', 'estate-showcase' ), esc_html( $estate_showcase_property['available'] ) );
				?>
			</p>
		</div>
	</div>
</article>
