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

add_action( 'wp_ajax_mace_bulk_replace',  'mace_ajax_mace_bulk_replace' );

/**
 * Bulk repplace images._caseless_remove
 */
function mace_ajax_mace_bulk_replace() {
	check_ajax_referer( 'mace-image-bulk-replace', 'security' );
	$replaced_id = filter_input( INPUT_POST, 'replace', FILTER_SANITIZE_NUMBER_INT );
	$replacement_id = filter_input( INPUT_POST, 'replaceWith', FILTER_SANITIZE_NUMBER_INT );
	$replaced_srces = mace_get_all_image_sizes_srcs( $replaced_id );
	$replacement_srces = mace_get_all_image_sizes_srcs( $replacement_id );
	$replaced_basename = pathinfo( basename( $replaced_srces[0] ), PATHINFO_FILENAME );
	$replacement_basename = pathinfo( basename( $replacement_srces[0] ), PATHINFO_FILENAME );
	$replaced_path = pathinfo( wp_parse_url( $replaced_srces[0] , PHP_URL_PATH ), PATHINFO_DIRNAME );
	$replacement_path = pathinfo( wp_parse_url( $replacement_srces[0] , PHP_URL_PATH ), PATHINFO_DIRNAME );
	foreach ( $replaced_srces as $src ) {
		$path = $_SERVER['DOCUMENT_ROOT'] . wp_parse_url( $src , PHP_URL_PATH );
		$path = mace_fix_image_path_for_multisite( $path );
		$result = unlink( $path );
	}

	foreach ( $replacement_srces as $src ) {
		$path = $_SERVER['DOCUMENT_ROOT'] . wp_parse_url( $src , PHP_URL_PATH );
		$new_path = str_replace( $replacement_basename, $replaced_basename , $path );
		$new_path = str_replace( $replacement_path, $replaced_path , $new_path );
		$new_path = mace_fix_image_path_for_multisite( $new_path );
		$path = mace_fix_image_path_for_multisite( $path );
		$success = rename( $path, $new_path );
	}

	wp_delete_attachment( $replacement_id, true );

	mace_ajax_response_success( '', array() );
	exit;
}

add_action( 'wp_ajax_mace_bulk_replace_get_replacements_array',  'mace_ajax_mace_bulk_replace_get_replacements_array' );

/**
 * Bulk repplace images._caseless_remove
 */
function mace_ajax_mace_bulk_replace_get_replacements_array() {
	check_ajax_referer( 'mace-image-bulk-replace', 'security' );
	$replaced_ids = $_REQUEST['replace_ids'];
	$replacement_ids = $_REQUEST['replaceWith_ids'];
	$left_entries = [];
	$right_entries = [];
	foreach ( $replaced_ids as $id ) {
		$src = wp_get_attachment_image_src( $id, 'full' );
		if ( $src ) {
			$src = pathinfo( basename( $src[0] ), PATHINFO_FILENAME );
		}
		$left_entries[ $id ] = $src;
	}

	foreach ( $replacement_ids as $id ) {
		$src = wp_get_attachment_image_src( $id, 'full' );
		if ( $src ) {
			$src = pathinfo( basename( $src[0] ), PATHINFO_FILENAME );
		}
		$right_entries[ $id ] = $src;
	}

	$replacements = [];
	if ( ! empty( $left_entries ) && ! empty( $right_entries ) ) {
		foreach ( $left_entries as $left_id => $left_name ) {
			foreach ( $right_entries as $right_id => $right_name ) {
				if ( $left_name === $right_name ) {
					$replacements[ $left_id ] = array(
						'leftId' => $left_id,
						'rightId' => $right_id,
						'leftName' => $left_name,
						'rightName' => $right_name,
					);
				} else {
					$str = $left_name . '-(\d)+';
					preg_match( '/' . $str . '/', $right_name, $matches );
					if ( ! empty( $matches ) ) {
						$replacements[ $left_id ] = array(
							'leftId' => $left_id,
							'rightId' => $right_id,
							'leftName' => $left_name,
							'rightName' => $right_name,
						);
					}
				}
			}
		}
	}
	if ( empty( $replacements ) ) {
		$replacements = false;
	}
	mace_ajax_response_success( '', array( 'replacements' => $replacements ) );
	exit;
}
