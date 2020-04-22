<?php
/**
 * Ajax Functions
 *
 * @package whats-your-reaction
 * @subpackage Ajax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Vote ajax handler
 */
function wyr_ajax_save_custom_icon() {
	// Sanitize post id.
	$icon_id = (int) filter_input( INPUT_POST, 'wyr_icon_id', FILTER_SANITIZE_NUMBER_INT ); // Removes all illegal characters from a number.

	if ( 0 === $icon_id ) {
		wyr_ajax_response_error( _x( 'Icon id not set!', 'ajax internal message', 'wyr' ) );
		exit;
	}

	$attachment = get_post( $icon_id );

	if ( 'attachment' !== get_post_type( $attachment ) ) {
		wyr_ajax_response_error( _x( 'This is not an attachment!', 'ajax internal message', 'wyr' ) );
		exit;
	}

	add_post_meta( $attachment->ID, '_wyr_custom_icon', true );

	wyr_ajax_response_success( _x( 'Icon saved successfully.', 'ajax internal message', 'wyr' ) );
	exit;
}
