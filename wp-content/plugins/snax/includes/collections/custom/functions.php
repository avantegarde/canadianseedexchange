<?php
/**
 * Snax Custom Collections Common Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Return custom collection config
 *
 * @return array
 */
function snax_get_custom_collection_config() {
	$config = array(
		'slug'          => 'custom',
		'title'         => esc_html_x( 'Custom Collection', 'Custom collection title', 'snax' ),
		'visibility'    => 'private',
		'sort'          => true,
		'reverse_order' => false,
		'add_criteria'  => 'manual',
		'add_to_label'  => esc_html_x( 'Add to Collection', 'Add to custom collection label', 'snax' ),
		'class'         => 'Snax_Custom_Collection'
	);

	return apply_filters( 'snax_custom_collection_config', $config );
}

/**
 * Check whether the custom collection is enabled
 *
 * @return bool
 */
function snax_is_custom_collection_activated() {
	$collection_id = snax_get_activated_custom_collection();

	if ( $collection_id === 0 ) {
		$activated = false;
	} else {
		$activated = snax_is_collection( $collection_id ) && 'publish' === get_post_status( $collection_id );
	}

	return apply_filters( 'snax_custom_collection_activated', $activated );
}

/**
 * Check whether the post is the custom collection
 *
 * @param WP_Post|int $post     Optional. Post object or id.
 *
 * @return bool
 */
function snax_is_custom_collection( $post = null ) {
	$post = get_post( $post );

	if ( ! snax_is_collection( $post ) ) {
		return false;
	}

	$custom_collection_id = (int) snax_get_activated_custom_collection();

	return $post->ID === $custom_collection_id;
}

/**
 * Check whether the post is the user custom collection
 *
 * @param WP_Post|int $post     Optional. Post object or id.
 *
 * @return bool
 */
function snax_is_user_custom_collection( $post = null ) {
	$post = get_post( $post );

	if ( ! snax_is_collection( $post ) ) {
		return false;
	}

	return (bool) get_post_meta( $post->ID, '_snax_user_custom', true );
}

/**
 * Return custom collection page url
 *
 * @return string
 */
function snax_get_custom_collection_url() {
	return get_permalink( snax_get_activated_custom_collection() );
}

/**
 * Return id of the custom collection
 *
 * @param int $default      Default return if option not set.
 *
 * @return int              0 if not activated.
 */
function snax_get_activated_custom_collection( $default = 0 ) {
	return (int) apply_filters( 'snax_activated_custom_collection', get_option( 'snax_activated_custom_collection', $default ) );
}

/**
 * Return user custom collections
 *
 * @param string $search_phrase     Phrase to filter collections.
 * @param int    $user_id           User id.
 * @param int    $max               Max. posts to fetch, -1 unlimited.
 *
 * @return array
 */
function snax_get_user_custom_collections( $search_phrase = '', $user_id = 0, $max = -1 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	return get_posts( array(
		'posts_per_page'    => $max,
		'post_type'         => snax_get_collection_post_type(),
		's'                 => $search_phrase,
		'post_status'       => array( 'publish', 'private' ),
		'orderby'           => 'title',
		'order'             => 'ASC',
		'author'	        => $user_id,
		'meta_query' => array(
			array(
				'key' => '_snax_user_custom',
				'compare' => 'EXISTS',
			)
		),
	) );
}


/**
 * Return user custom collection count.
 *
 * @param int    $user_id           User id.
 *
 * @return array
 */
function snax_get_user_custom_collection_count( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$count = get_posts( array(
		'post_type'         => snax_get_collection_post_type(),
		'post_status'       => 'publish',
		'author'	        => $user_id,
		'posts_per_page'    => '-1',
		'meta_query' => array(
			array(
				'key'       => '_snax_user_custom',
				'compare'   => 'EXISTS',
			)
		),
		'fields'            => 'ids', // Don't return objects, just ids
	) );

	return count( $count );
}
