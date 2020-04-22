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

add_action( 'set_object_terms', 'mace_video_post_format_set', 10, 6 );
add_action( 'wp_insert_post',   'mace_video_post_inserted', 10 );
add_action( 'post_updated',     'mace_video_post_updated', 10 );

/**
 * Return list of supported post types the video length should be stored for
 *
 * @return array
 */
function mace_get_auto_video_length_post_types() {
	return apply_filters( 'mace_auto_video_length_post_types', array( 'post' ) );
}

/**
 * Check whether post format is set to video
 *
 * @param int    $object_id  Object ID.
 * @param array  $terms      An array of object terms.
 * @param array  $tt_ids     An array of term taxonomy IDs.
 * @param string $taxonomy   Taxonomy slug.
 * @param bool   $append     Whether to append new terms to the old terms.
 * @param array  $old_tt_ids Old array of term taxonomy IDs.
 */
function mace_video_post_format_set( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
	// Video post format is set for a post.
	if ( 'post_format' === $taxonomy && in_array( 'post-format-video', $terms ) && in_array( get_post_type( $object_id ), mace_get_auto_video_length_post_types() ) ) {
		mace_update_video_post_format_meta( $object_id );
	}
}

/**
 * Video post added
 *
 * @param int $post_id      Post id.
 */
function mace_video_post_inserted( $post_id ) {
	if ( 'video' === get_post_format( $post_id ) && in_array( get_post_type( $post_id ), mace_get_auto_video_length_post_types() ) ) {
		mace_update_video_post_format_meta( $post_id );
	}
}

/**
 * Video post updated
 *
 * @param int $post_id      Post id.
 */
function mace_video_post_updated( $post_id ) {
	if ( 'video' === get_post_format( $post_id ) && in_array( get_post_type( $post_id ), mace_get_auto_video_length_post_types() ) ) {
		mace_update_video_post_format_meta( $post_id );
	}
}

/**
 * Update video post format meta
 *
 * @param $post_id
 */
function mace_update_video_post_format_meta( $post_id ) {
	$video_data = mace_get_post_video_data( $post_id );

	$new_video_data = false;

	// Video data not set so far.
	if ( empty( $video_data ) ) {
		$new_video_data = mace_fetch_video_data( $post_id );
	} else {
		// Current video url.
		$post_video_url = mace_get_first_url_in_content( $post_id );

		// Video url has changed.
		if ( ! empty( $post_video_url ) && $post_video_url !== $video_data['url'] ) {
			$new_video_data = mace_fetch_video_data( $post_id, $post_video_url );
		}
	}

	if ( is_wp_error( $new_video_data ) ) {
		update_post_meta( $post_id, '_mace_video_error', $new_video_data->get_error_message() );

		delete_post_meta( $post_id, '_mace_video_data' );
		delete_post_meta( $post_id, '_mace_video_length' );
	} else {
		delete_post_meta( $post_id, '_mace_video_error' );
	}

	if ( is_array( $new_video_data ) && ! empty( $new_video_data ) ) {
		update_post_meta( $post_id, '_mace_video_data', $new_video_data );
		update_post_meta( $post_id, '_mace_video_length', $new_video_data['length'] );
	}
}

/**
 * Return video meta data (length, url)
 *
 * @param int $post_id      Post id.
 *
 * @return string|array     Array with data of empty string if not available.
 */
function mace_get_post_video_data( $post_id ) {
	return get_post_meta( $post_id, '_mace_video_data', true );
}

/**
 * Return video fetching error message if exists
 *
 * @param int $post_id      Post id.
 *
 * @return string|array     Array with data of empty string if not available.
 */
function mace_get_video_fetching_error_message( $post_id ) {
	return get_post_meta( $post_id, '_mace_video_error', true );
}
