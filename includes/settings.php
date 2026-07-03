<?php
/**
 * Booking settings.
 *
 * @package OceanBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function obc_register_settings_page() {
	add_options_page( __( 'Ocean Booking', 'ocean-booking' ), __( 'Ocean Booking', 'ocean-booking' ), 'manage_options', 'ocean-booking', 'obc_render_settings_page' );
}
add_action( 'admin_menu', 'obc_register_settings_page' );

function obc_register_settings() {
	register_setting( 'obc_booking_group', 'obc_booking_settings', 'obc_sanitize_booking_settings' );
}
add_action( 'admin_init', 'obc_register_settings' );

function obc_sanitize_booking_settings( $input ) {
	$input = (array) $input;
	return array(
		'provider_name'        => sanitize_text_field( $input['provider_name'] ?? '' ),
		'booking_embed_url'    => esc_url_raw( $input['booking_embed_url'] ?? '' ),
		'booking_api_url'      => esc_url_raw( $input['booking_api_url'] ?? '' ),
		'booking_api_key'      => sanitize_text_field( $input['booking_api_key'] ?? '' ),
		'default_checkout_url' => esc_url_raw( $input['default_checkout_url'] ?? '' ),
		'integration_mode'     => in_array( $input['integration_mode'] ?? 'external', array( 'embed', 'api', 'external' ), true ) ? $input['integration_mode'] : 'external',
		'contact_email'        => sanitize_email( $input['contact_email'] ?? get_option( 'admin_email' ) ),
		'whatsapp_url'         => esc_url_raw( $input['whatsapp_url'] ?? '' ),
	);
}

function obc_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$options = obc_get_booking_settings();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Ocean Booking Settings', 'ocean-booking' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'obc_booking_group' ); ?>
			<table class="form-table" role="presentation">
				<tr><th scope="row"><label for="provider_name"><?php esc_html_e( 'Booking provider name', 'ocean-booking' ); ?></label></th><td><input class="regular-text" id="provider_name" name="obc_booking_settings[provider_name]" value="<?php echo esc_attr( $options['provider_name'] ); ?>" /></td></tr>
				<tr><th scope="row"><label for="integration_mode"><?php esc_html_e( 'Integration mode', 'ocean-booking' ); ?></label></th><td><select id="integration_mode" name="obc_booking_settings[integration_mode]">
					<option value="embed" <?php selected( $options['integration_mode'], 'embed' ); ?>><?php esc_html_e( 'Embed', 'ocean-booking' ); ?></option>
					<option value="api" <?php selected( $options['integration_mode'], 'api' ); ?>><?php esc_html_e( 'API', 'ocean-booking' ); ?></option>
					<option value="external" <?php selected( $options['integration_mode'], 'external' ); ?>><?php esc_html_e( 'External checkout redirect', 'ocean-booking' ); ?></option>
				</select></td></tr>
				<tr><th scope="row"><label for="booking_embed_url"><?php esc_html_e( 'Booking embed URL', 'ocean-booking' ); ?></label></th><td><input class="regular-text code" id="booking_embed_url" name="obc_booking_settings[booking_embed_url]" value="<?php echo esc_attr( $options['booking_embed_url'] ); ?>" /></td></tr>
				<tr><th scope="row"><label for="booking_api_url"><?php esc_html_e( 'Booking API URL', 'ocean-booking' ); ?></label></th><td><input class="regular-text code" id="booking_api_url" name="obc_booking_settings[booking_api_url]" value="<?php echo esc_attr( $options['booking_api_url'] ); ?>" /></td></tr>
				<tr><th scope="row"><label for="booking_api_key"><?php esc_html_e( 'Booking API key', 'ocean-booking' ); ?></label></th><td><input type="password" class="regular-text code" id="booking_api_key" name="obc_booking_settings[booking_api_key]" value="<?php echo esc_attr( $options['booking_api_key'] ); ?>" autocomplete="off" /></td></tr>
				<tr><th scope="row"><label for="default_checkout_url"><?php esc_html_e( 'Default checkout URL', 'ocean-booking' ); ?></label></th><td><input class="regular-text code" id="default_checkout_url" name="obc_booking_settings[default_checkout_url]" value="<?php echo esc_attr( $options['default_checkout_url'] ); ?>" /></td></tr>
				<tr><th scope="row"><label for="contact_email"><?php esc_html_e( 'Contact notification email', 'ocean-booking' ); ?></label></th><td><input class="regular-text" id="contact_email" name="obc_booking_settings[contact_email]" value="<?php echo esc_attr( $options['contact_email'] ); ?>" /></td></tr>
				<tr><th scope="row"><label for="whatsapp_url"><?php esc_html_e( 'WhatsApp URL', 'ocean-booking' ); ?></label></th><td><input class="regular-text code" id="whatsapp_url" name="obc_booking_settings[whatsapp_url]" value="<?php echo esc_attr( $options['whatsapp_url'] ); ?>" /></td></tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

