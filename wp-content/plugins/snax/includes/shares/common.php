<?php
/**
 * Shares Core Functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Snax
**/

/**
 * Check whether the module is enabled
 *
 * @return bool
 */
function snax_shares_enabled() {
	$enabled = 'standard' === get_option( 'snax_shares_enabled', 'standard' );

	return apply_filters( 'snax_shares_enabled', $enabled );
}

/**
 * Check whether the debug mode  is enabled
 *
 * @return bool
 */
function snax_shares_debug_mode_enabled() {
	$enabled = 'standard' === get_option( 'snax_shares_debug_mode', 'none' );

	return apply_filters( 'snax_shares_debug_mode_enabled', $enabled );
}

/**
 * Check whether to share URLs in shortlink form
 *
 * @return bool
 */
function snax_shares_use_shortlinks() {
	return apply_filters( 'snax_shares_use_shortlinks', false );
}

/**
 * Return post share URL
 *
 * @param int $post         Optional. Post object or id.
 *
 * @return string
 */
function snax_get_share_url( $post = 0 ) {
	$post = get_post( $post );

	if ( snax_shares_use_shortlinks() ) {
		$url = wp_get_shortlink( $post );
	} else {
		$url = get_permalink( $post );
	}

	return $url;
}
