<?php
/**
 * Snax History Collection
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'init', 'snax_register_history_collection' );

/**
 * Register History collection
 */
function snax_register_history_collection() {
	snax_register_abstract_collection(
		'history',
		esc_html_x( 'History', 'Built-in collection title', 'snax' ),
		array(
			'visibility'    => 'private',
			'sort'          => false,
			'reverse_order' => true,
			'add_criteria'  => 'auto',
			'class'         => 'Snax_History_Collection'
	) );
}

/**
 * Return user's History collection object
 *
 * @param int $user_id                  User id.
 *
 * @return Snax_Collection|bool         Collection object or false.
 */
function snax_get_user_history( $user_id ) {
	$collection = snax_get_collection_by_user( $user_id, 'history' );

	if ( ! is_wp_error( $collection ) ) {
		return $collection;
	}

	return false;
}
