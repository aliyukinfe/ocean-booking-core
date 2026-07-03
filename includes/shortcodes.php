<?php
/**
 * Frontend shortcodes.
 *
 * @package OceanBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function obc_booking_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'id' => get_the_ID() ), $atts, 'ocean_booking_widget' );
	return obc_render_booking_widget( absint( $atts['id'] ) );
}
add_shortcode( 'ocean_booking_widget', 'obc_booking_shortcode' );

function obc_contact_shortcode() {
	$settings = obc_get_booking_settings();
	$message  = '';

	if ( isset( $_POST['obc_contact_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obc_contact_nonce'] ) ), 'obc_contact' ) ) {
		$name    = sanitize_text_field( wp_unslash( $_POST['obc_name'] ?? '' ) );
		$email   = sanitize_email( wp_unslash( $_POST['obc_email'] ?? '' ) );
		$content = sanitize_textarea_field( wp_unslash( $_POST['obc_message'] ?? '' ) );
		if ( $name && is_email( $email ) && $content ) {
			wp_mail( $settings['contact_email'], sprintf( __( 'New enquiry from %s', 'ocean-booking' ), $name ), $content, array( 'Reply-To: ' . $name . ' <' . $email . '>' ) );
			$message = '<p class="form-success">' . esc_html__( 'Thank you. We will reply shortly.', 'ocean-booking' ) . '</p>';
		} else {
			$message = '<p class="form-error">' . esc_html__( 'Please complete all fields with a valid email address.', 'ocean-booking' ) . '</p>';
		}
	}

	ob_start();
	echo wp_kses_post( $message );
	?>
	<form class="contact-form" method="post">
		<?php wp_nonce_field( 'obc_contact', 'obc_contact_nonce' ); ?>
		<label><?php esc_html_e( 'Name', 'ocean-booking' ); ?><input name="obc_name" required /></label>
		<label><?php esc_html_e( 'Email', 'ocean-booking' ); ?><input name="obc_email" type="email" required /></label>
		<label><?php esc_html_e( 'Message', 'ocean-booking' ); ?><textarea name="obc_message" rows="6" required></textarea></label>
		<button class="button button-primary" type="submit"><?php esc_html_e( 'Send message', 'ocean-booking' ); ?></button>
		<?php if ( $settings['whatsapp_url'] ) : ?>
			<a class="button button-secondary" href="<?php echo esc_url( $settings['whatsapp_url'] ); ?>"><?php esc_html_e( 'WhatsApp', 'ocean-booking' ); ?></a>
		<?php endif; ?>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ocean_contact_form', 'obc_contact_shortcode' );

