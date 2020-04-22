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

add_action( 'wp_ajax_mace_save_image_size',     'mace_ajax_save_image_size' );
add_action( 'wp_ajax_mace_delete_image_size',   'mace_ajax_delete_image_size' );
add_action( 'wp_ajax_mace_activate_image_size', 'mace_ajax_activate_image_size' );

/**
 * Save Image Size
 */
function mace_ajax_save_image_size() {
	check_ajax_referer( 'mace-image-size', 'security' );

	$name = filter_input( INPUT_POST, 'mace_name', FILTER_SANITIZE_STRING );

	if ( empty( $name ) ) {
		mace_ajax_response_error( 'Image size name not set!' );
		exit;
	}

	$width = (int) filter_input( INPUT_POST, 'mace_width', FILTER_SANITIZE_NUMBER_INT );

	if ( ! $width ) {
		mace_ajax_response_error( 'Image size width is not a valid number!' );
		exit;
	}

	$height = (int) filter_input( INPUT_POST, 'mace_height', FILTER_SANITIZE_NUMBER_INT );

	if ( ! $height ) {
		mace_ajax_response_error( 'Image size height is not a valid number!' );
		exit;
	}


	$crop = (bool) filter_input( INPUT_POST, 'mace_crop', FILTER_VALIDATE_BOOLEAN );

	$crop_x = filter_input( INPUT_POST, 'mace_crop_x', FILTER_SANITIZE_STRING );

	$crop_y = filter_input( INPUT_POST, 'mace_crop_y', FILTER_SANITIZE_STRING );

	$saved = mace_save_image_size( $name, array(
		'width'     => $width,
		'height'    => $height,
		'crop'      => $crop,
		'crop_x'    => $crop_x,
		'crop_y'    => $crop_y,
	) );

	if ( is_wp_error( $saved ) ) {
		mace_ajax_response_error( $saved->get_error_message() );
		exit;
	}

	mace_ajax_response_success( esc_html__( 'Image size saved.', 'mace' ) );
	exit;
}

/**
 * Delete Image Size
 */
function mace_ajax_delete_image_size() {
	check_ajax_referer( 'mace-image-size', 'security' );

	$name = filter_input( INPUT_POST, 'mace_name', FILTER_SANITIZE_STRING );

	if ( empty( $name ) ) {
		mace_ajax_response_error( 'Image size name not set!' );
		exit;
	}

	$deleted = mace_delete_image_size( $name );

	if ( is_wp_error( $deleted ) ) {
		mace_ajax_response_error( $deleted->get_error_message() );
		exit;
	}

	mace_ajax_response_success( esc_html__( 'Image size deleted.', 'mace' ) );
	exit;
}

/**
 * Activate Image Size
 */
function mace_ajax_activate_image_size() {
	check_ajax_referer( 'mace-image-size', 'security' );

	$name = filter_input( INPUT_POST, 'mace_name', FILTER_SANITIZE_STRING );

	if ( empty( $name ) ) {
		mace_ajax_response_error( 'Image size name not set!' );
		exit;
	}

	$activated = mace_activate_image_size( $name );

	if ( is_wp_error( $activated ) ) {
		mace_ajax_response_error( $activated->get_error_message() );
		exit;
	}

	mace_ajax_response_success( esc_html__( 'Image size activated.', 'mace' ) );
	exit;
}
