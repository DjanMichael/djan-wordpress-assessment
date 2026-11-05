<?php
/**
 * SEO basics: meta description, Open Graph / Twitter cards and JSON-LD.
 *
 * WordPress core already prints the <title> (title-tag support), the canonical
 * link and the robots meta. When an SEO plugin is active the meta/social tags
 * here step aside so nothing is printed twice.
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a dedicated SEO plugin is handling meta tags.
 *
 * @return bool
 */
function estate_showcase_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * Description for the current view: the page excerpt, else its opening words, else the tagline.
 *
 * @return string
 */
function estate_showcase_meta_description() {
	if ( is_singular() ) {
		$post = get_queried_object();

		if ( has_excerpt( $post ) ) {
			return wp_strip_all_tags( get_the_excerpt( $post ) );
		}

		$text = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
		if ( '' !== $text ) {
			return $text;
		}
	}

	return get_bloginfo( 'description' );
}

/**
 * Prints the description and social sharing tags.
 */
function estate_showcase_meta_tags() {
	if ( estate_showcase_has_seo_plugin() ) {
		return;
	}

	$description = estate_showcase_meta_description();
	$url         = is_singular() ? get_permalink() : home_url( '/' );
	$properties  = estate_showcase_properties();
	$image       = is_singular() && has_post_thumbnail()
		? get_the_post_thumbnail_url( null, 'large' )
		: ( $properties ? estate_showcase_image_url( $properties[0]['image'] ) : '' );

	$tags = array(
		'description'         => $description,
		'og:type'             => is_front_page() ? 'website' : 'article',
		'og:site_name'        => get_bloginfo( 'name' ),
		'og:title'            => wp_get_document_title(),
		'og:description'      => $description,
		'og:url'              => $url,
		'og:image'            => $image,
		'og:locale'           => str_replace( '-', '_', get_bloginfo( 'language' ) ),
		'twitter:card'        => 'summary_large_image',
		'twitter:title'       => wp_get_document_title(),
		'twitter:description' => $description,
		'twitter:image'       => $image,
	);

	foreach ( $tags as $key => $content ) {
		if ( '' === (string) $content ) {
			continue;
		}

		$attribute = 0 === strpos( $key, 'og:' ) ? 'property' : 'name';
		$value     = in_array( $key, array( 'og:url', 'og:image', 'twitter:image' ), true ) ? esc_url( $content ) : esc_attr( $content );

		printf( '<meta %1$s="%2$s" content="%3$s" />' . "\n", esc_attr( $attribute ), esc_attr( $key ), $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
}
add_action( 'wp_head', 'estate_showcase_meta_tags', 1 );

/**
 * JSON-LD for the landing page: the agency and its featured listings.
 */
function estate_showcase_structured_data() {
	if ( ! is_page_template( 'page-templates/featured-listings.php' ) ) {
		return;
	}

	$contact  = estate_showcase_contact();
	$page_url = get_permalink();
	$items    = array();

	foreach ( estate_showcase_properties() as $index => $property ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'item'     => array(
				'@type'  => 'RealEstateListing',
				'name'   => $property['address'],
				'url'    => $page_url . '#property-' . ( $index + 1 ),
				'image'  => estate_showcase_image_url( $property['image'] ),
				'offers' => array(
					'@type'         => 'Offer',
					'price'         => $property['amount'],
					'priceCurrency' => 'GBP',
					'availability'  => 'let-agreed' === $property['status'] ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
				),
			),
		);
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type'     => 'RealEstateAgent',
				'@id'       => home_url( '/#organization' ),
				'name'      => get_bloginfo( 'name' ),
				'url'       => home_url( '/' ),
				'telephone' => $contact['phone'],
				'email'     => $contact['email'],
				'address'   => $contact['address'],
			),
			array(
				'@type'           => 'ItemList',
				'name'            => __( 'Our featured listing', 'estate-showcase' ),
				'itemListElement' => $items,
			),
		),
	);

	printf( '<script type="application/ld+json">%s</script>' . "\n", wp_json_encode( $data, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
}
add_action( 'wp_head', 'estate_showcase_structured_data', 20 );
