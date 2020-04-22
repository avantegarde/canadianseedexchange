<?php
/**
 * Snax Watch Later Collection
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'init', 'snax_register_favourites_collection' );

/**
 * Register collection
 */
function snax_register_favourites_collection() {
	snax_register_abstract_collection(
		'favourites',
		esc_html_x( 'Favourites', 'Built-in collection title', 'snax' ),
		array(
			'visibility'    => 'private',
			'sort'          => true,
			'add_criteria'  => 'manual',
			'add_to_label'  => esc_html_x( 'Add to Favourites', 'Add to collection label', 'snax' ),
		) );
}