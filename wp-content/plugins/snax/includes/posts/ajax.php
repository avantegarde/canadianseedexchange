<?php
/**
 * Snax Post Ajax Functions
 *
 * @package snax
 * @subpackage Ajax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Delete post ajax handler
 */
function snax_ajax_delete_post() {
	// Sanitize item id.
	$post_id = (int) filter_input( INPUT_POST, 'snax_post_id', FILTER_SANITIZE_NUMBER_INT );

	if ( 0 === $post_id ) {
		snax_ajax_response_error( 'Post id not set!' );
		exit;
	}

	check_ajax_referer( 'snax-delete-post-' . $post_id, 'security' );

	if ( ! current_user_can( 'snax_delete_posts', $post_id ) ) {
		wp_die( esc_html__( 'Cheatin&#8217; uh?', 'snax' ) );
	}

	$deleted = snax_delete_post( $post_id );

	if ( ! $deleted ) {
		snax_ajax_response_error( sprintf( 'Failed to delete post with id %d', $post_id ) );
		exit;
	}

	snax_ajax_response_success( esc_html__( 'Post deleted successfully.', 'snax' ) );
	exit;
}
