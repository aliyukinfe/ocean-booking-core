<?php
/**
 * Plugin Name: Ocean Booking Core
 * Description: Event, guide, booking integration, SEO schema, and contact features for the Ocean Booking theme.
 * Version: 1.0.0
 * Author: Ocean Booking
 * Text Domain: ocean-booking
 * Domain Path: /languages
 *
 * @package OceanBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OBC_VERSION', '1.0.0' );
define( 'OBC_PATH', plugin_dir_path( __FILE__ ) );
define( 'OBC_URL', plugin_dir_url( __FILE__ ) );

require_once OBC_PATH . 'includes/helpers.php';
require_once OBC_PATH . 'includes/cpt.php';
require_once OBC_PATH . 'includes/meta-boxes.php';
require_once OBC_PATH . 'includes/settings.php';
require_once OBC_PATH . 'includes/booking.php';
require_once OBC_PATH . 'includes/shortcodes.php';

function obc_load_textdomain() {
	load_plugin_textdomain( 'ocean-booking', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'obc_load_textdomain' );

function obc_activate() {
	obc_register_post_types();
	obc_create_default_pages();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'obc_activate' );

function obc_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'obc_deactivate' );

function obc_register_polylang_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}
	foreach ( array( 'Book now', 'Available tickets', 'Send message', 'WhatsApp' ) as $string ) {
		pll_register_string( 'Ocean Booking', $string, 'Ocean Booking' );
	}
}
add_action( 'init', 'obc_register_polylang_strings', 20 );

function obc_create_default_pages() {
	$pages = array(
		'booking'              => array( 'title' => __( 'Booking', 'ocean-booking' ), 'content' => '', 'template' => 'templates/booking.php' ),
		'booking-confirmation' => array( 'title' => __( 'Booking Confirmation', 'ocean-booking' ), 'content' => '', 'template' => 'templates/booking-confirmation.php' ),
		'faq'                  => array( 'title' => __( 'FAQ', 'ocean-booking' ), 'content' => '' ),
		'contact'              => array( 'title' => __( 'Contact', 'ocean-booking' ), 'content' => '[ocean_contact_form]', 'template' => 'templates/contact.php' ),
		'about'                => array( 'title' => __( 'About', 'ocean-booking' ), 'content' => '' ),
		'privacy-policy'       => array( 'title' => __( 'Privacy Policy', 'ocean-booking' ), 'content' => '' ),
		'terms'                => array( 'title' => __( 'Terms', 'ocean-booking' ), 'content' => '' ),
		'imprint'              => array( 'title' => __( 'Imprint', 'ocean-booking' ), 'content' => '' ),
		'cookie-policy'        => array( 'title' => __( 'Cookie Policy', 'ocean-booking' ), 'content' => '' ),
	);

	foreach ( $pages as $slug => $page ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		$page_id = wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => $page['content'],
				'post_status'  => 'draft',
				'post_type'    => 'page',
			)
		);
		if ( ! is_wp_error( $page_id ) && ! empty( $page['template'] ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page['template'] );
		}
	}
}
