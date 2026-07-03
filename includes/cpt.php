<?php
/**
 * Custom post types and taxonomies.
 *
 * @package OceanBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function obc_register_post_types() {
	register_post_type(
		'event',
		array(
			'labels'       => array(
				'name'          => __( 'Events', 'ocean-booking' ),
				'singular_name' => __( 'Event', 'ocean-booking' ),
				'add_new_item'  => __( 'Add New Event', 'ocean-booking' ),
				'edit_item'     => __( 'Edit Event', 'ocean-booking' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'events' ),
			'menu_icon'    => 'dashicons-tickets-alt',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
		)
	);

	register_post_type(
		'guide',
		array(
			'labels'       => array(
				'name'          => __( 'Guides', 'ocean-booking' ),
				'singular_name' => __( 'Guide', 'ocean-booking' ),
				'add_new_item'  => __( 'Add New Guide', 'ocean-booking' ),
				'edit_item'     => __( 'Edit Guide', 'ocean-booking' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'guides' ),
			'menu_icon'    => 'dashicons-location-alt',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		)
	);

	register_taxonomy(
		'event_location',
		'event',
		array(
			'labels'       => array(
				'name'          => __( 'Event Locations', 'ocean-booking' ),
				'singular_name' => __( 'Event Location', 'ocean-booking' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'event-location' ),
		)
	);

	register_taxonomy(
		'event_category',
		'event',
		array(
			'labels'       => array(
				'name'          => __( 'Event Categories', 'ocean-booking' ),
				'singular_name' => __( 'Event Category', 'ocean-booking' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'event-category' ),
		)
	);
}
add_action( 'init', 'obc_register_post_types' );

