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

add_action( 'wp_ajax_mace_fetch_images_to_regenerate',  'mace_ajax_fetch_images_to_regenerate' );
add_action( 'wp_ajax_mace_regenerate_thumbs',           'mace_ajax_regenerate_thumbs' );
add_action( 'wp_ajax_mace_fetch_images_to_watermark',   'mace_ajax_fetch_images_to_watermark' );
add_action( 'wp_ajax_mace_add_watermarks',              'mace_ajax_add_watermarks' );
add_action( 'wp_ajax_mace_remove_watermarks',           'mace_ajax_remove_watermarks' );

/**
 * Fetch image to regenerate
 */
function mace_ajax_fetch_images_to_regenerate() {
	check_ajax_referer( 'mace-image-bulk', 'security' );

	$ids = mace_fetch_images_to_regenerate();

	if ( is_wp_error( $ids ) ) {
		mace_ajax_response_error( $ids->get_error_message() );
		exit;
	}

	mace_ajax_response_success( esc_html__( 'Image list ready.', 'mace' ), array(
		'ids' => $ids,
	) );
	exit;
}

/**
 * Fetch image to watermark
 */
function mace_ajax_fetch_images_to_watermark() {
	check_ajax_referer( 'mace-image-bulk', 'security' );

	$ids = mace_fetch_images_to_watermark();

	if ( is_wp_error( $ids ) ) {
		mace_ajax_response_error( $ids->get_error_message() );
		exit;
	}

	mace_ajax_response_success( esc_html__( 'Image list ready.', 'mace' ), array(
		'ids' => $ids,
	) );
	exit;
}

/**
 * Regenerate thumbs
 */
function mace_ajax_regenerate_thumbs() {
	check_ajax_referer( 'mace-image-bulk', 'security' );

	$id = (int) filter_input( INPUT_POST, 'mace_media_id', FILTER_SANITIZE_NUMBER_INT );

	if ( ! $id ) {
		mace_ajax_response_error( 'Image id not set!' );
		exit;
	}

	$image = mace_regenerate_thumbs( $id );

	if ( is_wp_error( $image ) ) {
		mace_ajax_response_error( $image->get_error_message(), array(
			'id'        => $id,
			'filename'  => $image->get_error_message(),
		) );
		exit;
	}

	mace_ajax_response_success( esc_html__( 'Image thumbnails regenerated.', 'mace' ), array(
		'id'        => $id,
		'filename'  => $image['filename'],
		'data'      => $image['data'],
	) );
	exit;
}

/**
 * Add watermarks
 */
function mace_ajax_add_watermarks() {
	check_ajax_referer( 'mace-image-bulk', 'security' );

	$id = (int) filter_input( INPUT_POST, 'mace_media_id', FILTER_SANITIZE_NUMBER_INT );

	if ( ! $id ) {
		mace_ajax_response_error( 'Image id not set!' );
		exit;
	}

	$filename = get_the_title( $id );

	$image = mace_add_watermarks( $id );

	if ( is_wp_error( $image ) ) {
		mace_ajax_response_error( $image->get_error_message(), array(
			'id'        => $id,
			'filename'  => $filename,
		) );
		exit;
	}

	mace_ajax_response_success( esc_html__( 'Watermarks added.', 'mace' ), array(
		'id'        => $id,
		'filename'  => $filename,
		'data'      => $image['data'],
	) );
	exit;
}

/**
 * Remove watermarks
 */
function mace_ajax_remove_watermarks() {
	check_ajax_referer( 'mace-image-bulk', 'security' );

	$id = (int) filter_input( INPUT_POST, 'mace_media_id', FILTER_SANITIZE_NUMBER_INT );

	if ( ! $id ) {
		mace_ajax_response_error( 'Image id not set!' );
		exit;
	}

	$filename = get_the_title( $id );

	$image = mace_remove_watermarks( $id );

	if ( is_wp_error( $image ) ) {
		mace_ajax_response_error( $image->get_error_message(), array(
			'id'        => $id,
			'filename'  => $filename,
		) );
		exit;
	}

	mace_ajax_response_success( esc_html__( 'Watermarks removed.', 'mace' ), array(
		'id'        => $id,
		'filename'  => $filename,
		'data'      => $image['data'],
	) );
	exit;
}
