<?php
/**
 * Site footer.
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

$estate_showcase_contact = estate_showcase_contact();
$estate_showcase_social  = array(
	'facebook'  => __( 'Facebook', 'estate-showcase' ),
	'instagram' => __( 'Instagram', 'estate-showcase' ),
	'x-twitter' => __( 'X (Twitter)', 'estate-showcase' ),
	'linkedin'  => __( 'LinkedIn', 'estate-showcase' ),
);
?>
<footer id="contact" class="es-footer">
	<div class="container py-5">
		<div class="row g-4">
			<div class="col-12 col-lg-5">
				<p class="es-footer__brand mb-2">
					<i class="fa-solid fa-house-chimney me-1" aria-hidden="true"></i>
					<?php bloginfo( 'name' ); ?>
				</p>
				<p class="mb-0"><?php bloginfo( 'description' ); ?></p>
			</div>

			<div class="col-12 col-sm-5 col-lg-3">
				<h2 class="es-footer__heading"><?php esc_html_e( 'Explore', 'estate-showcase' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'footer',
						'container'            => 'nav',
						'container_aria_label' => __( 'Footer', 'estate-showcase' ),
						'menu_class'           => 'es-footer__links',
						'depth'                => 1,
						'fallback_cb'          => 'estate_showcase_menu_fallback',
					)
				);
				?>
			</div>

			<div class="col-12 col-sm-7 col-lg-4">
				<h2 class="es-footer__heading"><?php esc_html_e( 'Get in touch', 'estate-showcase' ); ?></h2>
				<address class="mb-3">
					<ul class="es-footer__contact">
						<li><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?php echo esc_html( $estate_showcase_contact['address'] ); ?></li>
						<li>
							<i class="fa-solid fa-phone" aria-hidden="true"></i>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $estate_showcase_contact['phone'] ) ); ?>"><?php echo esc_html( $estate_showcase_contact['phone'] ); ?></a>
						</li>
						<li>
							<i class="fa-solid fa-envelope" aria-hidden="true"></i>
							<a href="mailto:<?php echo esc_attr( antispambot( $estate_showcase_contact['email'] ) ); ?>"><?php echo esc_html( antispambot( $estate_showcase_contact['email'] ) ); ?></a>
						</li>
					</ul>
				</address>
				<ul class="es-social list-inline mb-0">
					<?php foreach ( $estate_showcase_contact['social'] as $estate_showcase_network => $estate_showcase_url ) : ?>
						<li class="list-inline-item">
							<a href="<?php echo esc_url( $estate_showcase_url ); ?>" rel="noopener" target="_blank">
								<i class="fa-brands fa-<?php echo esc_attr( $estate_showcase_network ); ?>" aria-hidden="true"></i>
								<span class="visually-hidden">
									<?php
									/* translators: %s: social network name. */
									printf( esc_html__( '%s (opens in a new tab)', 'estate-showcase' ), esc_html( isset( $estate_showcase_social[ $estate_showcase_network ] ) ? $estate_showcase_social[ $estate_showcase_network ] : ucfirst( $estate_showcase_network ) ) );
									?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-footer__bottom">
		<div class="container py-3 d-flex flex-column flex-md-row justify-content-between gap-2">
			<p class="mb-0">
				&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
				<?php esc_html_e( 'All rights reserved.', 'estate-showcase' ); ?>
			</p>
			<?php if ( get_privacy_policy_url() ) : ?>
				<p class="mb-0"><?php the_privacy_policy_link(); ?></p>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
