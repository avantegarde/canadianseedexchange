<?php
/**
 * Admin Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

function mace_get_settings_pages() {
	return apply_filters( 'mace_settings_pages', array() );
}

/**
 * Load stylesheets.
 *
 * @param string $hook      Page slug.
 */
function ma_admin_enqueue_styles( $hook ) {}

/**
 * Load javascripts.
 *
 * @param string $hook			Page slug.
 */
function ma_admin_enqueue_scripts( $hook ) {
	$ver = mace_get_plugin_version();
	$url = trailingslashit( mace_get_plugin_url() ) . 'includes/admin/assets/js/';

	$settings_pages = mace_get_settings_pages();

	$slugs = array();

	foreach( $settings_pages as $page_id => $page_config ) {
		$slugs[] = 'settings_page_' . $page_id;
	}

	if ( in_array( $hook, $slugs, true ) ) {
		wp_enqueue_script( 'ma-admin-settings', $url . 'settings.js', array( 'jquery' ), $ver, true );
	}
}

/**
 * Prints ajax response, json encoded
 *
 * @param string $status    Status of the response (success|error).
 * @param string $message   Text message describing response status code.
 * @param array  $args      Response extra arguments.
 *
 * @return void
 */
function mace_ajax_response( $status, $message, $args ) {
	$res = array(
		'status'  => $status,
		'message' => $message,
		'args'    => $args,
	);

	echo wp_json_encode( $res );
}

/**
 * Prints ajax success response, json encoded
 *
 * @param string $message       Text message describing response status code.
 * @param array  $args          Response extra arguments.
 *
 * @return void
 */
function mace_ajax_response_success( $message, $args = array() ) {
	mace_ajax_response( 'success', $message, $args );
}

/**
 * Prints ajax error response, json encoded
 *
 * @param string $message       Text message describing response status code.
 * @param array  $args          Response extra arguments.
 *
 * @return void
 */
function mace_ajax_response_error( $message, $args = array() ) {
	mace_ajax_response( 'error', $message, $args );
}

/**
 * Sanitize array text values (1 level deep only)
 *
 * @param array $input_array        Input.
 *
 * @return array                    Output.
 */
function mace_sanitize_text_array( $input_array ) {
	if ( ! is_array( $input_array ) ) {
		return array();
	}

	foreach ( $input_array as $key => $value ) {
		if ( is_array( $value ) ) {
			$input_array[ $key ] = array_map( 'sanitize_text_field', $input_array );
		} else {
			$input_array[ $key ] = sanitize_text_field( $value );
		}
	}

	return $input_array;
}