<?php
/**
 * Ajax Functions
 *
 * @package media-ace
 * @subpackage Ajax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'wp_ajax_mace_watermarks_exclude', 'mace_ajax_watermarks_exclude' );
add_action( 'wp_ajax_mace_watermarks_include', 'mace_ajax_watermarks_include' );

/**
 * Exclude from watermarking
 */
function mace_ajax_watermarks_exclude() {
	$id = (int) filter_input( INPUT_POST, 'mace_media_id', FILTER_SANITIZE_NUMBER_INT );

	if ( ! $id ) {
		mace_ajax_response_error( 'Image id not set!' );
		exit;
	}

	mace_watermarks_exclude_attachment( $id );

	mace_ajax_response_success( esc_html__( 'Image excluded.', 'mace' ) );
	exit;
}

/**
 * Allow watermarking
 */
function mace_ajax_watermarks_include() {
	$id = (int) filter_input( INPUT_POST, 'mace_media_id', FILTER_SANITIZE_NUMBER_INT );

	if ( ! $id ) {
		mace_ajax_response_error( 'Image id not set!' );
		exit;
	}

	mace_watermarks_include_attachment( $id );

	mace_ajax_response_success( esc_html__( 'Image included.', 'mace' ) );
	exit;
}
