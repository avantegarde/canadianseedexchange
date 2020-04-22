<?php
/**
 * Snax Collections Common Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'request',                  'snax_abstract_collection_request', 10, 1 );
add_action( 'delete_post',              'snax_before_collection_deleted', 10, 1 );

// Inherit from abstract collections.
add_filter( 'the_title',                'snax_abstract_collection_title', 10, 2 );
add_filter( 'document_title_parts',     'snax_abstract_collection_document_title', 99 );
add_filter( 'post_thumbnail_html',      'snax_abstract_collection_featured_image', 10, 5 );

/**
 * Override request for abstract collections
 *
 * @param array $request        Request params.
 *
 * @return array
 */
function snax_abstract_collection_request( $request ) {
	// Skip if admin request.
	if ( is_admin() ) {
		return $request;
	}

	// Skip if user is not logged in.
	if ( ! is_user_logged_in() ) {
		return $request;
	}

	$collection_post_type = snax_get_collection_post_type();

	// Skip if not a collection post type request.
	if ( ! isset( $request[ $collection_post_type ] ) ) {
		return $request;
	}

	$slug = isset( $request['name'] ) ? $request['name'] : '';

	// Skip if collection name not set.
	if ( ! $slug ) {
		return $request;
	}

	$config = snax_get_abstract_collection( $slug );

	// Collection registered?
	if ( ! $config ) {
		return $request;
	}

	$collection = snax_user_has_collection( get_current_user_id(), $slug );

	// Proceed only if user's collection exists.
	if ( ! $collection ) {
		return $request;
	}

	global $bimber_requested_collection_slug;

	$bimber_requested_collection_slug = $slug;

	// Reset current request.
	unset( $request['name'] );
	unset( $request[ $collection_post_type ] );

	// Set up collection.
	$request['p'] = $collection->get_id();

	remove_action( 'template_redirect', 'redirect_canonical' );

	return $request;
};

/**
 * Check whether the post is an abstract collection
 *
 * @param WP_Post|int $post     Optional. Post object or id.
 *
 * @return bool
 */
function snax_is_abstract_collection( $post = null ) {
	$post = get_post( $post );

	if ( ! snax_is_collection( $post ) ) {
		return false;
	}

	$activated = snax_get_activated_abstract_collections();

	return in_array( $post->ID, array_values( $activated ) );
}

/**
 * Check whether the collection inherits from abstract collection
 *
 * @param int $post           Post id.
 *
 * @return mixed        False if doesn't inherit, abstract collection slug otherwise.
 */
function snax_collection_inherits_from_abstract( $post ) {
	$post = get_post( $post );

	return get_post_meta( $post->ID, '_snax_abstract', true );
}

/**
 * Return list of abstract collections that are active
 *
 * @param array $default            Default return if option not set.
 *
 * @return array
 */
function snax_get_activated_abstract_collections( $default = array() ) {
	return apply_filters( 'snax_activated_abstract_collections', get_option( 'snax_activated_abstract_collections', $default ) );
}

/**
 * Activate abstract collection
 *
 * @param string $slug          Collection slug.
 * @param int    $id            Post id.
 */
function snax_activate_abstract_collection( $slug, $id ) {
	$collections = snax_get_activated_abstract_collections();

	$collections[ $slug ] = $id;

	update_option( 'snax_activated_abstract_collections', $collections );
	flush_rewrite_rules();
}

/**
 * Deactivate abstract collection
 *
 * @param string $slug          Collection slug.
 */
function snax_deactivate_abstract_collection( $slug ) {
	$collections = snax_get_activated_abstract_collections();

	if ( isset( $collections[ $slug ] ) ) {
		unset( $collections[ $slug ] );
	}

	update_option( 'snax_activated_abstract_collections', $collections );
}

/**
 * Return id of a post assigned to the abstract collection
 *
 * @param string $slug          Collection slug.
 *
 * @return int                  Collection id, 0 if collection is not set.
 */
function snax_get_abstract_collection_post_id( $slug ) {
	if ( ! snax_is_abstract_collection_activated( $slug ) ) {
		return 0;
	}

	$activated = snax_get_activated_abstract_collections();

	if ( isset( $activated[ $slug ] ) ) {
		return $activated[ $slug ];
	}

	return 0;
}

/**
 * Return abstract collection page url
 *
 * @param string $slug          Collection slug.
 *
 * @return string               Empty if collection not active
 */
function snax_get_abstract_collection_url( $slug ) {
	$url = '';

	$post_id = snax_get_abstract_collection_post_id( $slug );

	if ( $post_id > 0 ) {
		$url = get_permalink( snax_get_abstract_collection_post_id( $slug ) );
	}

	return $url;
}

/**
 * Check whether the abstract collection is activated.
 * Activated abstract collection means:
 *   - collection is defined
 *   - collection has assigned a post
 *   - assigned post is a collection post type
 *
 * @param string $slug      Collection slug
 *
 * @return bool
 */
function snax_is_abstract_collection_activated( $slug ) {
	$activated = snax_get_activated_abstract_collections();

	$collection_id = isset( $activated[ $slug ] ) ? (int) $activated[ $slug ] : 0;

	if ( $collection_id === 0 ) {
		$activated = false;
	} else {
		$activated = snax_is_collection( $collection_id ) && 'publish' === get_post_status( $collection_id );
	}

	return apply_filters( 'snax_abstract_collection_enabled', $activated, $slug );
}

/**
 * Register a new abstract collection
 *
 * @param string $slug          Unique collection slug (e.g. history)
 * @param string $title         Collection title.
 * @param array  $args          (Optional) Config.
 *
 * @return bool
 */
function snax_register_abstract_collection( $slug, $title, $args = array() ) {
	global $snax_abstract_collections;

	// Init.
	if ( ! isset( $snax_abstract_collections ) ) {
		$snax_abstract_collections = array();
	}

	// Already exists?
	if ( isset( $snax_abstract_collections[ $slug ] ) ) {
		return false;
	}

	$defaults = array(
		'visibility'    => 'public',
		'sort'          => true,
		'reverse_order' => false,
		'add_criteria'  => 'manual',
		'add_to_label'  => sprintf( esc_html_x( 'Add to %s', 'Add to abstract collection label', 'snax' ), $title ),
		'class'         => 'Snax_Abstract_Collection'
	);

	$args = wp_parse_args( $args, $defaults );

	// Slug.
	$args['slug'] = $slug;

	// Title.
	$args['title'] = $title;

	$snax_abstract_collections[ $slug ] = $args;

	return true;
}

/**
 * Return abstract collections
 *
 * @return array
 */
function snax_get_abstract_collections() {
	global $snax_abstract_collections;

	return $snax_abstract_collections;
}

/**
 * Return abstract collection
 *
 * @param string $slug          Collection base slug (e.g. history).
 *
 * @return mixed                Return collection config or false if collection not registered.
 */
function snax_get_abstract_collection( $slug ) {
	$collections = snax_get_abstract_collections();

	if ( isset($collections[ $slug ] ) ) {
		return $collections[ $slug ];
	}

	return false;
}

/**
 * Actions performed just before the collection is removed
 *
 * @param int $post_id      Post id.
 */
function snax_before_collection_deleted( $post_id ) {
	if ( snax_is_abstract_collection( $post_id ) ) {
		$post = get_post( $post_id );

		$slug = str_replace( '__trashed', '', $post->post_name );

		snax_deactivate_abstract_collection( $slug );
	}
}

/**
 * Set up title for collections that inherit from abstract collection
 *
 * @param string $title         Post title.
 * @param int    $id            Post id.
 *
 * @return string
 */
function snax_abstract_collection_title( $title, $id = 0 ) {
	if ( ! snax_is_collection( $id ) ) {
		return $title;
	}

	$new_title = snax_get_inheriting_collection_title( $id );

	if ( $new_title ) {
		$title = $new_title;
	}

	return $title;
}

/**
 * Set up document title (in head) for collections that inherit from abstract collection
 *
 * @param array $title          Title parts (title and site).
 *
 * @return array
 */
function snax_abstract_collection_document_title( $title ) {
	$id = get_the_ID();

	if ( ! snax_is_collection( $id ) ) {
		return $title;
	}

	$new_title = snax_get_inheriting_collection_title( $id );

	if ( $new_title ) {
		$title['title'] = $new_title;
	}

	return $title;
}

/**
 * Return title for collection that inherits from abstract collection
 *
 * @param int $collection_id        Post collection id.
 *
 * @return mixed                    Title or false if failed.
 */
function snax_get_inheriting_collection_title( $collection_id ) {
	$abstract_slug = snax_collection_inherits_from_abstract( $collection_id );

	if ( $abstract_slug ) {
		$post_id = snax_get_abstract_collection_post_id( $abstract_slug );

		if ( $post_id > 0 ) {
			$post = get_post( $post_id );

			return $post->post_title;
		}
	}

	return false;
}

/**
 * Return featured image HTML for collection that inherits from abstract collection
 *
 * @param string       $html                The post thumbnail HTML.
 * @param int          $post_id             The post ID.
 * @param string       $post_thumbnail_id   The post thumbnail ID.
 * @param string|array $size                The post thumbnail size. Image size or array of width and height
 *                                          values (in that order). Default 'post-thumbnail'.
 * @param string       $attr                Query string of attributes.
 *
 * @return string                           Featured image.
 */
function snax_abstract_collection_featured_image( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	// Skip if the image is set.
	if ( $post_thumbnail_id > 0 ) {
		return $html;
	}

	// Skip if not a collection.
	if ( ! snax_is_collection( $post_id ) ) {
		return $html;
	}

	$abstract_slug = snax_collection_inherits_from_abstract( $post_id );

	// Process only if the collection inherits from abstract.
	if ( $abstract_slug ) {
		$abstract_collection_id = snax_get_abstract_collection_post_id( $abstract_slug );

		if ( $abstract_collection_id > 0 ) {
			// Prevent infinite loop.
			remove_filter( 'post_thumbnail_html', 'snax_abstract_collection_featured_image', 10 );

			$html = get_the_post_thumbnail( $abstract_collection_id, $size, $attr );
		}
	}

	return $html;
}
