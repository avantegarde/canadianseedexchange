<?php
/**
 * AMP plugin functions
 *
 * @package mace
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'wp', 'mace_amp_hooks' );


/**
 * AMP specific hooks.
 */
function mace_amp_hooks() {
	if ( function_exists( 'is_amp_endpoint' ) ) {
		if ( is_amp_endpoint() ) {
			add_filter( 'mace_lazy_load_embed',     '__return_false', 99 );
			add_filter( 'mace_lazy_load_image',     '__return_false', 99 );
		}
	}
}
