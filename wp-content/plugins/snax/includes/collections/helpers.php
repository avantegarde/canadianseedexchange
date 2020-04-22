<?php
/**
 * Snax Collection Helpers
 *
 * @package Snax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Return collection by id
 *
 * @param int $id                   Collection id.
 *
 * @return Snax_Collection|bool     Collection object or false.
 */
function snax_get_collection_by_id( $id ) {
	$class_name = 'Snax_Collection';

	if ( $abstract_collection_slug = snax_collection_inherits_from_abstract( $id ) ) {
		$abstract_collection = snax_get_abstract_collection( $abstract_collection_slug );

		if ( $abstract_collection ) {
			$class_name = $abstract_collection['class'];
		}
	} else if ( snax_is_user_custom_collection( $id ) ) {
		$class_name = 'Snax_User_Collection';
	}

	$collection = call_user_func( array( $class_name, 'get_by_id' ), $id );

	if ( ! is_wp_error( $collection ) ) {
		return $collection;
	}

	return false;
}

/**
 * Return ids of posts belong to the collection
 *
 * @param int $collection_id    Collection id.
 * @param int $page             Page number.
 *
 * @return array                Post ids.
 */
function snax_get_collection_posts_on_page( $collection_id, $page = 1 ) {
	$collection = snax_get_collection_by_id( $collection_id );

	if ( ! is_wp_error( $collection ) ) {
		$max    = snax_get_collections_posts_per_page();
		$offset = ( $page - 1 ) * $max;

		return $collection->get_posts( $max, $offset );
	}

	return array();
}

/**
 * Return collection object, create if not exists
 *
 * @param int    $user_id           User id.
 * @param string $slug              Collection base slug (e.g. history)
 *
 * @return bool|Snax_Collection     Collection object or false if not exists.
 */
function snax_get_collection_by_user( $user_id, $slug ) {
	$config = snax_get_abstract_collection( $slug );

	if ( $config ) {
		$collection_class = isset( $config['class'] ) ? $config['class'] : 'Snax_Abstract_Collection';

		if ( is_callable( array( $collection_class, 'get_by_user' ) ) ) {
			return call_user_func( array( $collection_class, 'get_by_user' ), $user_id, $slug );
		}
	}

	return false;
}

/**
 * Check whether the user's collection exists
 *
 * @param int    $user_id           User id.
 * @param string $slug              Collection base slug (e.g. history).
 *
 * @return bool|Snax_Collection     Collection object or false if not exists.
 */
function snax_user_has_collection( $user_id, $slug ) {
	$config = snax_get_abstract_collection( $slug );

	if ( $config ) {
		$collection_class = isset( $config['class'] ) ? $config['class'] : 'Snax_Abstract_Collection';

		if ( is_callable( array( $collection_class, 'exists' ) ) ) {
			return call_user_func( array( $collection_class, 'exists' ), $user_id, $slug );
		}
	}

	return false;
}

/**
 * Return collection's posts count
 *
 * @param int $collection_id        Collection id.
 *
 * @return int
 */
function snax_get_collection_item_count( $collection_id ) {
	$collection = snax_get_collection_by_id( $collection_id );

	return $collection->count_posts();
}

/**
 * Create collection
 *
 * @param string $class_name        Class name.
 * @param array $args               Config.
 *
 * @return object
 */
function snax_create_collection_by_class_name( $class_name, $args ) {
	$reflectionClass = new ReflectionClass( $class_name );

	return $reflectionClass->newInstanceArgs(array( $args ));
}

/**
 * Check whether the collection has items
 *
 * @param int $collection_id        Optional. Collection id.
 *
 * @return bool
 */
function snax_collection_has_items( $collection_id = null ) {
	$collection = get_post( $collection_id );

	$items = snax_get_collection_item_count( $collection->ID );

	return $items > 0;
}

/**
 * Check if the user is collection owner
 *
 * @param int $user_id                  Optional. User id.
 * @param int $collection_id            Optional. Collection id.
 *
 * @return bool
 */
function snax_user_is_collection_owner( $user_id = null, $collection_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$author_id = (int) get_post_field( 'post_author', $collection_id );

	return $user_id === $author_id;
}

/**
 * Check whether user can edit collection
 *
 * @param int $user_id                  Optional. User id.
 * @param int $collection_id            Optional. Collection id.
 *
 * @return bool
 */
function snax_user_can_edit_collection( $user_id = null, $collection_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$collection = get_post( $collection_id );

	$can = snax_is_user_custom_collection( $collection ) && snax_user_is_collection_owner( $user_id, $collection );

	return apply_filters( 'snax_user_can_edit_collection', $can, $user_id, $collection_id );
}

/**
 * Check whether user can delete collection
 *
 * @param int $user_id                  Optional. User id.
 * @param int $collection_id            Optional. Collection id.
 *
 * @return bool
 */
function snax_user_can_delete_collection( $user_id = null, $collection_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$collection = get_post( $collection_id );

	$can = snax_is_user_custom_collection( $collection ) && snax_user_is_collection_owner( $user_id, $collection );

	return apply_filters( 'snax_user_can_delete_collection', $can, $user_id, $collection_id );
}

/**
 * Check whether collection edit view is requested
 *
 * @return bool
 */
function snax_is_collection_edit_view() {
	return ( null !== filter_input( INPUT_GET, 'edit', FILTER_SANITIZE_STRING ) );
}

function snax_get_collection_edit_url( $post = null ) {
	$post = get_post( $post );

	return add_query_arg( 'edit', '', get_permalink( $post ) );
}

function snax_collection_has_description( $post = null ) {
	$post = get_post( $post );

	return ! empty( $post->post_excerpt );
}

function snax_collection_description( $post = null ) {
	$collection_post = get_post( $post );

	if ( $abstract_slug = snax_collection_inherits_from_abstract( $collection_post->ID ) ) {
		$post_id = snax_get_abstract_collection_post_id( $abstract_slug );

		$collection_post = get_post( $post_id );
	}

	echo wp_kses_post( wpautop( $collection_post->post_excerpt ) );
}

function snax_get_collection_visibility( $post = null ) {
	$post = get_post( $post );

	if ( ! snax_is_collection( $post ) ) {
		return false;
	}

	$visibility = get_post_meta( $post->ID, '_snax_visibility', true );

	return $visibility;
}

function snax_is_history_collection_activated() {
	return snax_is_abstract_collection_activated( 'history' );
}

/**
 * Return collection featrued image id
 *
 * @param WP_Post $post     Post or id.
 *
 * @return string|int       Post thumbnail ID or empty string.
 */
function snax_get_collection_thumbnail_id( $post = null ) {
	$collection_post = get_post( $post );

	if ( $abstract_slug = snax_collection_inherits_from_abstract( $collection_post->ID ) ) {
		$abstract_collection_id = snax_get_abstract_collection_post_id( $abstract_slug );

		if ( $abstract_collection_id > 0 ) {
			 $collection_post = get_post( $abstract_collection_id );
		}
	}

	return get_post_thumbnail_id( $collection_post->ID );
}
