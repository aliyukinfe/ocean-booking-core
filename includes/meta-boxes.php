<?php
/**
 * Admin meta boxes.
 *
 * @package OceanBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function obc_add_meta_boxes() {
	add_meta_box( 'obc_event_details', __( 'Event Details', 'ocean-booking' ), 'obc_render_event_meta_box', 'event', 'normal', 'high' );
	add_meta_box( 'obc_guide_related', __( 'Related Events', 'ocean-booking' ), 'obc_render_guide_meta_box', 'guide', 'side' );
}
add_action( 'add_meta_boxes', 'obc_add_meta_boxes' );

function obc_render_event_meta_box( $post ) {
	wp_nonce_field( 'obc_save_event_meta', 'obc_event_nonce' );
	echo '<div class="obc-grid">';
	foreach ( obc_get_event_fields() as $key => $field ) {
		$value = obc_get_event_meta( $post->ID, $key );
		printf( '<p><label for="obc_%1$s"><strong>%2$s</strong></label>', esc_attr( $key ), esc_html( $field['label'] ) );
		if ( 'textarea' === $field['type'] ) {
			printf( '<textarea id="obc_%1$s" name="obc_%1$s" rows="4" style="width:100%%">%2$s</textarea>', esc_attr( $key ), esc_textarea( $value ) );
		} else {
			printf( '<input id="obc_%1$s" name="obc_%1$s" type="%2$s" value="%3$s" style="width:100%%" />', esc_attr( $key ), esc_attr( $field['type'] ), esc_attr( $value ) );
		}
		echo '</p>';
	}
	echo '</div>';
}

function obc_render_guide_meta_box( $post ) {
	wp_nonce_field( 'obc_save_guide_meta', 'obc_guide_nonce' );
	$selected = (array) get_post_meta( $post->ID, '_obc_related_events', true );
	$events   = get_posts(
		array(
			'post_type'      => 'event',
			'posts_per_page' => 50,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	foreach ( $events as $event ) {
		printf(
			'<label style="display:block;margin:.35rem 0"><input type="checkbox" name="obc_related_events[]" value="%1$d" %2$s /> %3$s</label>',
			absint( $event->ID ),
			checked( in_array( $event->ID, $selected, true ), true, false ),
			esc_html( get_the_title( $event ) )
		);
	}
}

function obc_save_event_meta( $post_id ) {
	if ( ! isset( $_POST['obc_event_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obc_event_nonce'] ) ), 'obc_save_event_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( obc_get_event_fields() as $key => $field ) {
		$posted = isset( $_POST[ 'obc_' . $key ] ) ? wp_unslash( $_POST[ 'obc_' . $key ] ) : '';
		$value  = 'url' === $field['type'] ? esc_url_raw( $posted ) : sanitize_textarea_field( $posted );
		update_post_meta( $post_id, '_obc_' . $key, $value );
	}
}
add_action( 'save_post_event', 'obc_save_event_meta' );

function obc_save_guide_meta( $post_id ) {
	if ( ! isset( $_POST['obc_guide_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obc_guide_nonce'] ) ), 'obc_save_guide_meta' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$ids = isset( $_POST['obc_related_events'] ) ? array_map( 'absint', (array) $_POST['obc_related_events'] ) : array();
	update_post_meta( $post_id, '_obc_related_events', array_filter( $ids ) );
}
add_action( 'save_post_guide', 'obc_save_guide_meta' );

