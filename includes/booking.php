<?php
/**
 * Booking integrations.
 *
 * @package OceanBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function obc_get_event_checkout_url( $post_id ) {
	$settings = obc_get_booking_settings();
	$url      = obc_get_event_meta( $post_id, 'checkout_url' );
	return $url ? $url : $settings['default_checkout_url'];
}

function obc_render_booking_widget( $post_id = 0 ) {
	$post_id   = $post_id ? absint( $post_id ) : get_the_ID();
	$settings  = obc_get_booking_settings();
	$event_id  = obc_get_event_meta( $post_id, 'booking_embed_id' );
	$checkout  = obc_get_event_checkout_url( $post_id );
	$mode      = $settings['integration_mode'];
	$button    = $checkout ? sprintf( '<a class="button button-primary" href="%1$s" rel="nofollow">%2$s</a>', esc_url( $checkout ), esc_html__( 'Book now', 'ocean-booking' ) ) : '';

	if ( 'embed' === $mode && $settings['booking_embed_url'] ) {
		$src = add_query_arg( array_filter( array( 'event_id' => $event_id ) ), $settings['booking_embed_url'] );
		return '<div class="booking-frame-wrap"><iframe src="' . esc_url( $src ) . '" title="' . esc_attr__( 'Booking widget', 'ocean-booking' ) . '" loading="lazy" allow="payment"></iframe></div><p class="booking-fallback">' . $button . '</p>';
	}

	if ( 'api' === $mode ) {
		$availability = obc_fetch_availability( $post_id );
		if ( is_wp_error( $availability ) ) {
			return '<div class="booking-notice">' . esc_html__( 'Live availability is temporarily unavailable. Please continue to checkout.', 'ocean-booking' ) . '</div><p>' . $button . '</p>';
		}
		$list = '';
		foreach ( (array) ( $availability['ticket_types'] ?? array() ) as $ticket ) {
			$list .= '<li>' . esc_html( $ticket['name'] ?? '' ) . '</li>';
		}
		return '<div class="booking-api"><h3>' . esc_html__( 'Available tickets', 'ocean-booking' ) . '</h3><ul>' . $list . '</ul>' . $button . '</div>';
	}

	return $button ? '<p class="booking-external">' . $button . '</p>' : '<div class="booking-notice">' . esc_html__( 'Booking link is not configured yet.', 'ocean-booking' ) . '</div>';
}

function obc_fetch_availability( $post_id ) {
	$settings = obc_get_booking_settings();
	$api_url  = $settings['booking_api_url'];
	$api_key  = $settings['booking_api_key'];
	$event_id = obc_get_event_meta( $post_id, 'booking_embed_id' );

	if ( ! $api_url || ! $api_key || ! $event_id ) {
		return new WP_Error( 'obc_missing_api', __( 'Booking API settings are incomplete.', 'ocean-booking' ) );
	}

	$endpoint = apply_filters( 'obc_api_availability_endpoint', trailingslashit( $api_url ) . 'availability', $post_id, $event_id );
	$response = wp_remote_get(
		add_query_arg( 'event_id', rawurlencode( $event_id ), $endpoint ),
		array(
			'timeout' => 8,
			'headers' => array( 'Authorization' => 'Bearer ' . $api_key ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $code ) {
		return new WP_Error( 'obc_api_error', __( 'Booking API returned an error.', 'ocean-booking' ) );
	}

	return json_decode( wp_remote_retrieve_body( $response ), true );
}

function obc_create_booking( $post_id, $payload ) {
	$settings = obc_get_booking_settings();
	$api_url  = $settings['booking_api_url'];
	$api_key  = $settings['booking_api_key'];
	$event_id = obc_get_event_meta( $post_id, 'booking_embed_id' );

	if ( ! $api_url || ! $api_key || ! $event_id ) {
		return new WP_Error( 'obc_missing_api', __( 'Booking API settings are incomplete.', 'ocean-booking' ) );
	}

	$endpoint = apply_filters( 'obc_api_booking_endpoint', trailingslashit( $api_url ) . 'bookings', $post_id, $event_id );
	$body     = wp_parse_args( $payload, array( 'event_id' => $event_id ) );
	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array(
				'Authorization' => 'Bearer ' . $api_key,
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( $body ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	return apply_filters( 'obc_api_booking_response', $data, $response, $post_id );
}

function obc_print_event_schema() {
	if ( ! is_singular( 'event' ) ) {
		return;
	}
	$post_id = get_the_ID();
	$image   = get_the_post_thumbnail_url( $post_id, 'large' );
	$schema  = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Event',
		'name'        => get_the_title( $post_id ),
		'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
		'image'       => $image ? array( $image ) : array(),
		'location'    => array(
			'@type' => 'Place',
			'name'  => obc_get_event_meta( $post_id, 'location' ),
		),
		'offers'      => array(
			'@type'         => 'Offer',
			'price'         => obc_get_event_meta( $post_id, 'price_from' ),
			'priceCurrency' => 'EUR',
			'url'           => obc_get_event_checkout_url( $post_id ),
			'availability'  => 'https://schema.org/InStock',
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'obc_print_event_schema' );
