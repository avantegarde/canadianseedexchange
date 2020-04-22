<?php
/**
 * Snax Collections Ajax Functions
 *
 * @package snax
 * @subpackage Ajax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'wp_ajax_snax_add_to_collection',               'snax_ajax_add_to_collection' );
add_action( 'wp_ajax_snax_save_collection',                 'snax_ajax_save_collection' );
add_action( 'wp_ajax_snax_get_user_collections',            'snax_ajax_get_user_collections' );
add_action( 'wp_ajax_snax_remove_collection',               'snax_ajax_remove_collection' );
add_action( 'wp_ajax_snax_remove_all_from_collection',      'snax_ajax_remove_all_from_collection' );
add_action( 'wp_ajax_snax_remove_post_from_collection',     'snax_ajax_remove_post_from_collection' );
add_action( 'wp_ajax_snax_get_collection_featured_media',   'snax_ajax_get_collection_featured_media' );
add_action( 'wp_ajax_snax_get_collection_meta',             'snax_ajax_get_collection_meta' );


/**
 * Add to collection ajax handler
 */
function snax_ajax_add_to_collection() {
	check_ajax_referer( 'snax-collection-add', 'security' );

	// Sanitize input.
	$collection_id  = filter_input( INPUT_POST, 'snax_collection', FILTER_SANITIZE_STRING );
	$post_id        = (int) filter_input( INPUT_POST, 'snax_post_id', FILTER_SANITIZE_NUMBER_INT );

	if ( ! is_user_logged_in() ) {
		// Do not translate the error message, it's only for debugging purposes.
		snax_ajax_response_error( 'User not logged in!' );
		exit;
	}

	if ( empty( $collection_id ) ) {
		snax_ajax_response_error( 'Collection id/slug not set!' );
		exit;
	}

	if ( 0 === $post_id ) {
		snax_ajax_response_error( 'Post id not set!' );
		exit;
	}

	$user_id = get_current_user_id();

	if ( is_numeric( $collection_id ) ) {
		$collection = snax_get_collection_by_id( $collection_id );
	} else {
		$collection = snax_get_collection_by_user( $user_id, $collection_id );
	}

	if ( ! $collection ) {
		snax_ajax_response_error( sprintf( 'Collection with id %s not found!', $collection_id ) );
		exit;
	}

	// Is user the collection's owner?
	if ( $user_id !== $collection->get_owner_id() ) {
		snax_ajax_response_error( sprintf( 'User %s is not the collection owner!', $user_id ) );
		exit;
	}

	$res = $collection->add_post( $post_id );

	if ( is_wp_error( $res ) ) {
		snax_ajax_response_error( $res->get_error_message() );
		exit;
	}

	$html = sprintf( esc_html_x( 'Added to collection %s', 'Ajax response message', 'snax' ), '<a href="' . esc_url( $collection->get_url() ) . '">' . $collection->get_title() . '</a>' );

	snax_ajax_response_success( 'Added to collection.', array(
		'html' => $html,
	) );
	exit;
}

/**
 * Save collection ajax handler
 */
function snax_ajax_save_collection() {
	check_ajax_referer( 'snax-collection-add', 'security' );

	if ( ! is_user_logged_in() ) {
		// Do not translate the error message, it's only for debugging purposes.
		snax_ajax_response_error( 'User not logged in!' );
		exit;
	}

	// Sanitize input.
	$title   = filter_input( INPUT_POST, 'snax_title', FILTER_SANITIZE_STRING );

	if ( empty( $title ) ) {
		// Do not translate the error message, it's only for debugging purposes.
		snax_ajax_response_error( 'Collection title not set!' );
		exit;
	}

	$collection_id  = filter_input( INPUT_POST, 'snax_collection_id', FILTER_SANITIZE_NUMBER_INT );
	$description    = filter_input( INPUT_POST, 'snax_description', FILTER_SANITIZE_STRING );
	$visibility     = filter_input( INPUT_POST, 'snax_visibility', FILTER_SANITIZE_STRING );
	$featured_media = filter_input( INPUT_POST, 'snax_featured_media', FILTER_SANITIZE_STRING );

	// Get collection.
	if ( $collection_id ) {
		if ( ! snax_user_is_collection_owner( get_current_user_id(), $collection_id ) ) {
			snax_ajax_response_error( 'Collection does not belong to user!' );
			exit;
		}

		$collection = snax_get_collection_by_id( $collection_id );

		$updated = $collection->update( array(
			'title'             => $title,
			'description'       => $description,
			'visibility'        => $visibility,
			'featured_media'    => $featured_media,
		) );

		if ( ! $updated  ) {
			snax_ajax_response_error( 'Collection update failed!' );
			exit;
		}

	// Create new.
	} else {
		$collection = new Snax_User_Collection( array(
			'user_id' => get_current_user_id(),
			'title'   => $title,
		) );
	}

	if ( is_wp_error( $collection ) ) {
		snax_ajax_response_error( $collection->get_error_message() );
		exit;
	}

	snax_ajax_response_success( 'Collection saved.', array(
		'collection_id'  => $collection->get_id(),
		'collection_url' => $collection->get_url(),
	) );
	exit;
}

/**
 * Return user's collections
 */
function snax_ajax_get_user_collections() {
	if ( ! is_user_logged_in() ) {
		// Do not translate the error message, it's only for debugging purposes.
		snax_ajax_response_error( 'User not logged in!' );
		exit;
	}

	$list = array();
	$collections = snax_get_user_custom_collections();

	foreach( $collections as $collection ) {
		$list[] = array(
			'ID'            => $collection->ID,
			'post_title'    => $collection->post_title,
			'visibility'    => get_post_meta( $collection->ID, '_snax_visibility', true ),
		);
	}

	snax_ajax_response_success( 'Collections found.', array(
		'list' => $list,
	) );
	exit;
}

/**
 * Remove collection ajax handler
 */
function snax_ajax_remove_collection() {
	check_ajax_referer( 'snax-collection-delete', 'security' );

	// Sanitize input.
	$collection_id  = filter_input( INPUT_POST, 'snax_collection_id', FILTER_SANITIZE_NUMBER_INT );

	if ( empty( $collection_id ) ) {
		snax_ajax_response_error( 'Collection id not set!' );
		exit;
	}

	$collection = snax_get_collection_by_id( $collection_id );

	if ( ! $collection ) {
		snax_ajax_response_error( sprintf( 'Collection with id %s not found!', $collection_id ) );
		exit;
	}

	// Is user the collection's owner?
	if ( ! snax_user_can_delete_collection( get_current_user_id(), $collection_id ) ) {
		snax_ajax_response_error( sprintf( 'User %s is not the collection owner!', get_current_user_id() ) );
		exit;
	}

	$res = $collection->remove();

	if ( is_wp_error( $res ) ) {
		snax_ajax_response_error( $res->get_error_message() );
		exit;
	}

	snax_ajax_response_success( 'Collection removed.' );
	exit;
}

/**
 * Remove all from collection ajax handler
 */
function snax_ajax_remove_all_from_collection() {
	check_ajax_referer( 'snax-collection-delete', 'security' );

	// Sanitize input.
	$collection_id  = filter_input( INPUT_POST, 'snax_collection_id', FILTER_SANITIZE_NUMBER_INT );

	if ( empty( $collection_id ) ) {
		snax_ajax_response_error( 'Collection id not set!' );
		exit;
	}

	$collection = snax_get_collection_by_id( $collection_id );

	if ( ! $collection ) {
		snax_ajax_response_error( sprintf( 'Collection with id %s not found!', $collection_id ) );
		exit;
	}

	// Is user the collection's owner?
	if ( ! snax_user_is_collection_owner( get_current_user_id(), $collection_id ) ) {
		snax_ajax_response_error( sprintf( 'User %s is not the collection owner!', get_current_user_id() ) );
		exit;
	}

	$res = $collection->remove_all_posts();

	if ( is_wp_error( $res ) ) {
		snax_ajax_response_error( $res->get_error_message() );
		exit;
	}

	snax_ajax_response_success( 'Removed all from collection.' );
	exit;
}

/**
 * Remove all from collection ajax handler
 */
function snax_ajax_remove_post_from_collection() {
	check_ajax_referer( 'snax-collection-delete', 'security' );

	// Sanitize input.
	$post_id = (int) filter_input( INPUT_POST, 'snax_post_id', FILTER_SANITIZE_NUMBER_INT );

	if ( 0 === $post_id ) {
		snax_ajax_response_error( 'Post id not set!' );
		exit;
	}

	$collection_id  = filter_input( INPUT_POST, 'snax_collection_id', FILTER_SANITIZE_NUMBER_INT );

	if ( empty( $collection_id ) ) {
		snax_ajax_response_error( 'Collection id not set!' );
		exit;
	}

	$collection = snax_get_collection_by_id( $collection_id );

	if ( ! $collection ) {
		snax_ajax_response_error( sprintf( 'Collection with id %s not found!', $collection_id ) );
		exit;
	}

	// Is user the collection's owner?
	if ( get_current_user_id() !== $collection->get_owner_id() ) {
		snax_ajax_response_error( sprintf( 'User %s is not the collection owner!', get_current_user_id() ) );
		exit;
	}

	$res = $collection->remove_post( $post_id );

	if ( is_wp_error( $res ) ) {
		snax_ajax_response_error( $res->get_error_message() );
		exit;
	}

	snax_ajax_response_success( 'Post removed from collection.' );
	exit;
}

/**
 * Return collection featured media
 */
function snax_ajax_get_collection_featured_media() {
	// Sanitize input.
	$collection_id  = filter_input( INPUT_GET, 'snax_collection_id', FILTER_SANITIZE_STRING );

	if ( empty( $collection_id ) ) {
		snax_ajax_response_error( 'Collection id not set!' );
		exit;
	}

	$user_id = get_current_user_id();

	if ( is_numeric( $collection_id ) ) {
		$collection = snax_get_collection_by_id( $collection_id );
	} else {
		$collection = snax_get_collection_by_user( $user_id, $collection_id );
	}

	if ( ! $collection ) {
		snax_ajax_response_error( sprintf( 'Collection with id %s not found!', $collection_id ) );
		exit;
	}

	// Is user the collection's owner?
	if ( $user_id !== $collection->get_owner_id() ) {
		snax_ajax_response_error( sprintf( 'User %s is not the collection owner!', $user_id ) );
		exit;
	}

	$html = get_the_post_thumbnail( $collection_id, 'thumbnail' );

	snax_ajax_response_success( 'Collection featured media generated.', array(
		'html' => $html,
	) );
	exit;
}

/**
 * Return collection meta
 */
function snax_ajax_get_collection_meta() {
	// Sanitize input.
	$collection_id  = filter_input( INPUT_GET, 'snax_collection_id', FILTER_SANITIZE_STRING );

	if ( empty( $collection_id ) ) {
		snax_ajax_response_error( 'Collection id not set!' );
		exit;
	}

	$user_id = get_current_user_id();

	if ( is_numeric( $collection_id ) ) {
		$collection = snax_get_collection_by_id( $collection_id );
	} else {
		$collection = snax_get_collection_by_user( $user_id, $collection_id );
	}

	if ( ! $collection ) {
		snax_ajax_response_error( sprintf( 'Collection with id %s not found!', $collection_id ) );
		exit;
	}

	// Is user the collection's owner?
	if ( $user_id !== $collection->get_owner_id() ) {
		snax_ajax_response_error( sprintf( 'User %s is not the collection owner!', $user_id ) );
		exit;
	}

	$query = new WP_Query( array(
		'p'                 => $collection_id,
		'post_type'         => snax_get_collection_post_type(),
		'posts_per_page'    => 1,
	) );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) { $query->the_post();
			snax_get_template_part( 'collections/meta');
		}

		wp_reset_postdata();
	}

	$html = ob_get_clean();

	snax_ajax_response_success( 'Collection meta generated.', array(
		'html' => $html,
	) );
	exit;
}
