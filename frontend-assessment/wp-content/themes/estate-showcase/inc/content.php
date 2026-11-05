<?php
/**
 * Landing page content: featured properties, Google reviews and contact details.
 *
 * Edit the arrays here, or change them from a plugin/child theme with the
 * estate_showcase_properties, estate_showcase_reviews and
 * estate_showcase_contact filters.
 *
 * @package Estate_Showcase
 */

defined( 'ABSPATH' ) || exit;

/**
 * Featured listings (Design 1).
 *
 * The status key picks the badge colour: let-agreed (red), to-let / for-sale (gold).
 *
 * @return array<int, array<string, mixed>>
 */
function estate_showcase_properties() {
	$properties = array(
		array(
			'address'   => '17 Carter Villas, Brockenhurst',
			'price'     => '£1,600 PCM',
			'amount'    => 1600,
			'status'    => 'let-agreed',
			'bedrooms'  => 2,
			'bathrooms' => 0,
			'type'      => __( 'Other', 'estate-showcase' ),
			'available' => __( 'Now', 'estate-showcase' ),
			'image'     => 'carter-villas-living-room.jpg',
			'image_alt' => __( 'Bright open-plan living room with large windows at 17 Carter Villas, Brockenhurst', 'estate-showcase' ),
		),
		array(
			'address'   => '56 Dumpton Cross, Exeter',
			'price'     => '£1,400 PCM',
			'amount'    => 1400,
			'status'    => 'to-let',
			'bedrooms'  => 4,
			'bathrooms' => 4,
			'type'      => __( 'House', 'estate-showcase' ),
			'available' => __( 'November 6, 2022', 'estate-showcase' ),
			'image'     => 'dumpton-cross-house.jpg',
			'image_alt' => __( 'Detached family house with a large front lawn at 56 Dumpton Cross, Exeter', 'estate-showcase' ),
		),
		array(
			'address'   => '118 Kingsdale Ford, Sherborne',
			'price'     => '£290,000',
			'amount'    => 290000,
			'status'    => 'for-sale',
			'bedrooms'  => 5,
			'bathrooms' => 1,
			'type'      => __( 'House', 'estate-showcase' ),
			'available' => __( 'For Sale', 'estate-showcase' ),
			'image'     => 'kingsdale-ford-bedroom.jpg',
			'image_alt' => __( 'Double bedroom with a padded headboard at 118 Kingsdale Ford, Sherborne', 'estate-showcase' ),
		),
	);

	return apply_filters( 'estate_showcase_properties', $properties );
}

/**
 * Badge text for a listing status.
 *
 * @param string $status let-agreed | to-let | for-sale.
 * @return string
 */
function estate_showcase_status_label( $status ) {
	$labels = array(
		'let-agreed' => __( 'Let Agreed', 'estate-showcase' ),
		'to-let'     => __( 'To Let', 'estate-showcase' ),
		'for-sale'   => __( 'For Sale', 'estate-showcase' ),
	);

	return isset( $labels[ $status ] ) ? $labels[ $status ] : ucwords( str_replace( '-', ' ', $status ) );
}

/**
 * URL of an image bundled with the theme, or a full URL as given.
 *
 * @param string $image File name in assets/images, or an absolute URL.
 * @return string
 */
function estate_showcase_image_url( $image ) {
	if ( preg_match( '#^https?://#', $image ) ) {
		return $image;
	}

	return get_template_directory_uri() . '/assets/images/' . rawurlencode( $image );
}

/**
 * Google reviews for the slider (Design 2). Placeholder copy until the client supplies real reviews.
 *
 * @return array<int, array{author: string, rating: int, text: string}>
 */
function estate_showcase_reviews() {
	$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque aliquam cursus nulla. Morbi aliquet accumsan est et mollis. Nunc in quam orci. Quisque risus metus, aliquet nec metus sed, bibendum eleifend turpis. Quisque imperdiet ligula ante, vel tincidunt nibh bibendum non.';

	$reviews = array(
		array(
			'author' => 'Phil Mitchel',
			'rating' => 5,
			'text'   => $lorem,
		),
		array(
			'author' => 'Karl Pilkington',
			'rating' => 5,
			'text'   => $lorem,
		),
		array(
			'author' => 'Simon Pegg',
			'rating' => 5,
			'text'   => $lorem,
		),
		array(
			'author' => 'Sarah Jennings',
			'rating' => 5,
			'text'   => $lorem,
		),
		array(
			'author' => 'Daniel Okafor',
			'rating' => 5,
			'text'   => $lorem,
		),
		array(
			'author' => 'Emma Whitfield',
			'rating' => 5,
			'text'   => $lorem,
		),
	);

	return apply_filters( 'estate_showcase_reviews', $reviews );
}

/**
 * Contact details for the footer and structured data.
 *
 * @return array{address: string, phone: string, email: string, social: array<string, string>}
 */
function estate_showcase_contact() {
	return apply_filters(
		'estate_showcase_contact',
		array(
			'address' => '12 High Street, Exeter, EX1 1AA',
			'phone'   => '01392 000 000',
			'email'   => 'hello@example.com',
			'social'  => array(
				'facebook'  => 'https://www.facebook.com/',
				'instagram' => 'https://www.instagram.com/',
				'x-twitter' => 'https://x.com/',
				'linkedin'  => 'https://www.linkedin.com/',
			),
		)
	);
}
