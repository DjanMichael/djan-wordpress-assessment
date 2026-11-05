<?php
/**
 * Design 1 — three-column grid of featured properties.
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

$estate_showcase_properties = estate_showcase_properties();

if ( empty( $estate_showcase_properties ) ) {
	return;
}
?>
<section id="featured" class="es-featured" aria-labelledby="featured-title">
	<div class="container">
		<h2 id="featured-title" class="es-section-title"><?php esc_html_e( 'Our featured listing', 'estate-showcase' ); ?></h2>

		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
			<?php foreach ( $estate_showcase_properties as $estate_showcase_index => $estate_showcase_property ) : ?>
				<div class="col">
					<?php
					get_template_part(
						'template-parts/property-card',
						null,
						array(
							'property' => $estate_showcase_property,
							'id'       => 'property-' . ( $estate_showcase_index + 1 ),
						)
					);
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
