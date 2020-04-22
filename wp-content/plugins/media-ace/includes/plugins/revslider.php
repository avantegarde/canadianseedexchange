<?php
/**
 * Slider Revolution functions
 *
 * @package mace
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'revslider_remove_version', 'mace_revslider_disable_lazyload' );
add_filter( 'revslider_add_js_delay', 'mace_revslider_enable_lazyload' );

/**
 * Disable lazyload for revslider
 *
 * @param mixed $input  Whatever is sent our way we return.
 */
function mace_revslider_disable_lazyload( $input ) {
	add_filter( 'mace_lazy_load_embed',     '__return_false', 99 );
	add_filter( 'mace_lazy_load_image',     '__return_false', 99 );
	return $input;
}

/**
 * Reenable lazyload for revslider
 *
 * @param mixed $input  Whatever is sent our way we return.
 */
function mace_revslider_enable_lazyload( $input ) {
	remove_filter( 'mace_lazy_load_embed',  '__return_false', 99 );
	remove_filter( 'mace_lazy_load_image',  '__return_false', 99 );
	return $input;
}
