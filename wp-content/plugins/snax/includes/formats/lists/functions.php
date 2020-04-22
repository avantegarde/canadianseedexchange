<?php
/**
 * Snax Format Functions
 *
 * @package snax
 * @subpackage Formats
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'snax_handle_list_submission', 'snax_process_open_list_submission', 10, 2 );
add_action( 'snax_post_published',         'snax_publish_list_items', 10, 2 );

/**
 * List submission handler
 *
 * @param array $data             List data.
 * @param WP    $request          Request object.
 */
function snax_process_open_list_submission( $data, $request ) {
	$list_id = snax_create_open_list( $data );

	if ( ! is_wp_error( $list_id ) ) {
		$url_var = snax_get_url_var( 'post_submission' );
		$redirect_url = add_query_arg( $url_var, 'success', get_permalink( $list_id ) );

		$redirect_url = apply_filters( 'snax_new_post_redirect_url', $redirect_url, $list_id );

		$request->set_query_var( 'snax_redirect_to_url', $redirect_url );
	}
}

/**
 * Create a new post of 'list' format
 *
 * @param array $data           Post data.
 *
 * @return int|WP_Error         Post id or WP_Error on failure.
 */
function snax_create_open_list( $data ) {
	$defaults = array(
		'id'				=> 0,
		'title'       		=> '',
		'description' 		=> '',
		'category_id' 		=> '',
		'author'      		=> get_current_user_id(),
		'status'      		=> 'pending',
		'list_voting'       => 'standard',
		'list_submission'	=> 'standard',
	);

	$data = wp_parse_args( $data, $defaults );

	$author_id = (int) $data['author'];
	$status    = $data['status'];

    $is_new_post = 0 === $data['id'];

    $post_status = '';

    // Set comments status explicitly on UPDATE. Otherwise it will be set to "closed".
    if ( ! $is_new_post ) {
        $post_status = comments_open( $data['id'] ) ? 'open' : 'closed';
    }

	$new_post = array(
		'post_title'     => wp_strip_all_tags( $data['title'] ),
		'post_content'   => snax_kses_post( $data['description'] ),
		'post_author'    => $author_id,
		'post_status'    => $status,
        'comment_status' => $post_status,
		'post_type'      => 'post',
		'ID'			 => $data['id'],
	);

	$post_id = wp_insert_post( $new_post );

	if ( 0 === $post_id ) {
		return new WP_Error( 'snax_post_creating_failed', esc_html__( 'Some errors occured while creating post.', 'snax' ) );
	}

	// Assign category.
	$category_id = $data['category_id'];

	if ( ! empty( $category_id ) ) {
		wp_set_post_categories( $post_id, $category_id );
	}

	// Reassign tags.
	snax_remove_post_tags( $post_id );

	$tags = $data['tags'];

	if ( ! empty( $tags ) ) {
		wp_set_post_tags( $post_id, $tags, true );
	}

	// Set meta data.
	snax_set_post_format( $post_id, 'list' );


	snax_open_post_for_contribution( $post_id, array(
		'submission'	=> $data['list_submission'],
		'voting' 	 	=> $data['list_voting'],
	) );

	snax_attach_user_orphan_items_to_post( $post_id, $data['author'] );

	// Set featured image.
	if ( snax_is_featured_media_field_disabled( 'list' ) ) {
		snax_set_first_item_with_image_as_post_featured( $post_id );
	} else {
		$featured_image = snax_get_format_featured_image( 'list', $author_id, $data['id'] );

		if ( $featured_image ) {
			set_post_thumbnail( $post_id, $featured_image->ID );

			snax_reset_format_featured_image( $featured_image );
		} else {
			snax_set_first_item_with_image_as_post_featured( $post_id );
		}
	}

	do_action( 'snax_post_added', $post_id, 'list' );

	return $post_id;
}

/**
 * Publish list items
 */
function snax_publish_list_items( $post_id ) {
	if ( snax_is_post_a_list( $post_id ) ) {
		// Get pending items.
		$items = snax_get_items( $post_id, array(
			'post_status' => snax_get_item_pending_status(),
		) );

		// Publish them.
		foreach( $items as $item ) {
			wp_update_post( array(
				'ID'            => $item->ID,
				'post_status'   => snax_get_item_approved_status(),
			) );
		}
	}
}

/**
 * Check whether the post is one of list types
 *
 * @param int $post_id      Post id.
 *
 * @return bool
 */
function snax_is_post_a_list( $post_id = 0 ) {
	$format = snax_get_post_format( $post_id );

	return in_array( $format, array( 'list', 'ranked_list', 'classic_list' ) );
}

/**
 * Return featured media field visibility type
 *
 * @return string
 */
function snax_list_featured_media_field() {
	// Before 6.0 lists had no the Featured Media field.
	$default = 'disabled';

	return apply_filters( 'snax_list_featured_media_field', get_option( 'snax_list_featured_media_field', $default ) );
}

/**
 * Check whether to link to single item from list.
 *
 * @return bool
 */
function snax_list_link_to_single() {
	$default = 'standard';

	return 'standard' === apply_filters( 'snax_list_link_to_single', get_option( 'snax_list_link_to_single', $default ) );
}

/**
 * Check whether to show the Featured Media on a single post
 *
 * @return bool
 */
function snax_list_show_featured_media() {
	$default = snax_get_legacy_show_featured_media_setting( 'list' );

	return 'standard' === apply_filters( 'snax_list_show_featured_media', get_option( 'snax_list_show_featured_media', $default ) );
}

/**
 * Check whether to show the Featured Media field on form
 *
 * @return bool
 */
function snax_list_show_featured_media_field() {
	return 'disabled' !== snax_list_featured_media_field();
}

/**
 * Return the Category field visibility type
 *
 * @return string
 */
function snax_list_category_field() {
	$default = snax_get_legacy_category_required_setting();

	return apply_filters( 'snax_list_category_field', get_option( 'snax_list_category_field', $default ) );
}

/**
 * Check whether to show the Category field on form
 *
 * @return bool
 */
function snax_list_show_category_field() {
	return 'disabled' !== snax_list_category_field();
}

/**
 * Check whether to allow multiple categories selection
 *
 * @return bool
 */
function snax_list_multiple_categories_selection() {
	$default = snax_get_legacy_category_multi_setting();

	return 'standard' === apply_filters( 'snax_list_category_multi', get_option( 'snax_list_category_multi', $default ) );
}

/**
 * Return list of allowed categories to select during front end post creation
 *
 * @return array
 */
function snax_list_get_category_whitelist() {
	$default = snax_get_legacy_category_whitelist_setting();

	return apply_filters( 'snax_list_category_whitelist', get_option( 'snax_list_category_whitelist', $default ) );
}

/**
 * Return list of categories to be auto-assigned during front end post creation
 *
 * @return array
 */
function snax_list_get_category_auto_assign() {
	$default = snax_get_legacy_category_auto_assign_setting();

	return apply_filters( 'snax_list_category_auto_assign', get_option( 'snax_list_category_auto_assign', $default ) );
}

/**
 * Return the Referral link field visibility type
 *
 * @return string
 */
function snax_list_allow_snax_authors_to_add_referrals() {
	$default = snax_get_legacy_referrals_setting();

	return 'standard' === apply_filters( 'snax_list_allow_snax_authors_to_add_referrals', get_option( 'snax_list_allow_snax_authors_to_add_referrals', $default ) );
}

/**
 * Return singular name (Open List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_list_get_singular_name( $default = '' ) {
	$val = get_option( 'snax_list_singular_name' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_list_singular_name', $val );
}

/**
 * Return "Add new" label (Open List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_list_get_add_new( $default = '' ) {
	$val = get_option( 'snax_list_add_new' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_list_add_new', $val );
}

/**
 * Return description (Open List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_list_get_description( $default = '' ) {
	$val = get_option( 'snax_list_description' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_list_description', $val );
}

/**
 * Return overview (Open List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_list_get_overview( $default = '' ) {
	$val = get_option( 'snax_list_overview' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_list_overview', $val );
}

/**
 * Return singular name (Ranked List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_ranked_list_get_singular_name( $default = '' ) {
	$val = get_option( 'snax_ranked_list_singular_name' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_ranked_list_singular_name', $val );
}

/**
 * Return "Add new" label (Ranked List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_ranked_list_get_add_new( $default = '' ) {
	$val = get_option( 'snax_ranked_list_add_new' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_ranked_list_add_new', $val );
}

/**
 * Return description (Ranked List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_ranked_list_get_description( $default = '' ) {
	$val = get_option( 'snax_ranked_list_description' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_ranked_list_description', $val );
}

/**
 * Return overview (Ranked List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_ranked_list_get_overview( $default = '' ) {
	$val = get_option( 'snax_ranked_list_overview' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_ranked_list_overview', $val );
}

/**
 * Return singular name (Classic List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_classic_list_get_singular_name( $default = '' ) {
	$val = get_option( 'snax_classic_list_singular_name' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_classic_list_singular_name', $val );
}

/**
 * Return "Add new" label (Classic List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_classic_list_get_add_new( $default = '' ) {
	$val = get_option( 'snax_classic_list_add_new' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_classic_list_add_new', $val );
}

/**
 * Return description (Classic List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_classic_list_get_description( $default = '' ) {
	$val = get_option( 'snax_classic_list_description' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_classic_list_description', $val );
}

/**
 * Return overview (Classic List)
 *
 * @param string $default       Default value, if option not set yet.
 *
 * @return string
 */
function snax_classic_list_get_overview( $default = '' ) {
	$val = get_option( 'snax_classic_list_overview' );

	if ( empty( $val ) ) {
		$val = $default;
	}

	return apply_filters( 'snax_classic_list_overview', $val );
}
