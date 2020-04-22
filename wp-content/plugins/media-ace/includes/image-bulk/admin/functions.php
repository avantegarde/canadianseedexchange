<?php
/**
 * Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get list of images that can be regenerated
 *
 * @return array|WP_Error
 */
function mace_fetch_images_to_regenerate() {
	global $wpdb;

	$images = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID DESC" );

	if ( ! $images ) {
		return new WP_Error( 'mace_no_images', esc_html__( 'Couldn\'t find any images.', 'mace' ) );
	}

	$ids = array();

	foreach ( $images as $image ) {
		$ids[] = $image->ID;
	}

	return $ids;
}

/**
 * Get list of images that can be watermarked
 *
 * @return array|WP_Error
 */
function mace_fetch_images_to_watermark() {
	global $wpdb;

	$images = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID DESC" );

	if ( ! $images ) {
		return new WP_Error( 'mace_no_images', esc_html__( 'Couldn\'t find any images.', 'mace' ) );
	}

	$ids = array();

	foreach ( $images as $image ) {
		if ( ! mace_watermarks_attachment_excluded( $image->ID ) ) {
			$ids[] = $image->ID;
		}
	}

	return $ids;
}
