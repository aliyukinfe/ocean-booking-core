<?php
/**
 * Shared helpers.
 *
 * @package OceanBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function obc_get_event_fields() {
	return array(
		'short_description'    => array( 'label' => __( 'Short description', 'ocean-booking' ), 'type' => 'textarea' ),
		'location'             => array( 'label' => __( 'Location', 'ocean-booking' ), 'type' => 'text' ),
		'meeting_point'        => array( 'label' => __( 'Meeting point', 'ocean-booking' ), 'type' => 'text' ),
		'event_date'           => array( 'label' => __( 'Event date', 'ocean-booking' ), 'type' => 'date' ),
		'duration'             => array( 'label' => __( 'Duration', 'ocean-booking' ), 'type' => 'text' ),
		'check_in_time'        => array( 'label' => __( 'Check-in time', 'ocean-booking' ), 'type' => 'text' ),
		'start_time'           => array( 'label' => __( 'Start time', 'ocean-booking' ), 'type' => 'text' ),
		'return_time'          => array( 'label' => __( 'Return time', 'ocean-booking' ), 'type' => 'text' ),
		'price_from'           => array( 'label' => __( 'Price from', 'ocean-booking' ), 'type' => 'number' ),
		'event_rating'         => array( 'label' => __( 'Rating', 'ocean-booking' ), 'type' => 'text' ),
		'availability'         => array( 'label' => __( 'Availability', 'ocean-booking' ), 'type' => 'text' ),
		'ticket_types'         => array( 'label' => __( 'Ticket types', 'ocean-booking' ), 'type' => 'textarea' ),
		'included_features'    => array( 'label' => __( 'Included features', 'ocean-booking' ), 'type' => 'textarea' ),
		'itinerary_timeline'   => array( 'label' => __( 'Itinerary timeline', 'ocean-booking' ), 'type' => 'textarea' ),
		'faq'                  => array( 'label' => __( 'FAQ', 'ocean-booking' ), 'type' => 'textarea' ),
		'cancellation_policy'  => array( 'label' => __( 'Cancellation policy', 'ocean-booking' ), 'type' => 'textarea' ),
		'good_to_know'         => array( 'label' => __( 'Good to know', 'ocean-booking' ), 'type' => 'textarea' ),
		'booking_embed_id'     => array( 'label' => __( 'Booking embed/API ID', 'ocean-booking' ), 'type' => 'text' ),
		'checkout_url'         => array( 'label' => __( 'Event checkout URL', 'ocean-booking' ), 'type' => 'url' ),
		'og_image_url'         => array( 'label' => __( 'OpenGraph image URL', 'ocean-booking' ), 'type' => 'url' ),
		'meta_title'           => array( 'label' => __( 'Meta title', 'ocean-booking' ), 'type' => 'text' ),
		'meta_description'     => array( 'label' => __( 'Meta description', 'ocean-booking' ), 'type' => 'textarea' ),
	);
}

function obc_get_event_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, '_obc_' . $key, true );
	return '' === $value ? $default : $value;
}

function obc_lines_to_list( $value ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $value );
	$lines = array_filter( array_map( 'trim', $lines ) );
	return $lines;
}

function obc_get_booking_settings() {
	$defaults = array(
		'provider_name'        => '',
		'booking_embed_url'    => '',
		'booking_api_url'      => '',
		'booking_api_key'      => '',
		'default_checkout_url' => '',
		'integration_mode'     => 'external',
		'contact_email'        => get_option( 'admin_email' ),
		'whatsapp_url'         => '',
	);

	return wp_parse_args( get_option( 'obc_booking_settings', array() ), $defaults );
}

function obc_allowed_iframe_html() {
	return array(
		'iframe' => array(
			'src'             => true,
			'title'           => true,
			'loading'         => true,
			'allow'           => true,
			'allowfullscreen' => true,
		),
	);
}
