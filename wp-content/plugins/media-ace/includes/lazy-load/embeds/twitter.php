<?php
/**
 * Twitter embed
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'embed_oembed_html', 'mace_lazy_load_twitter', 10, 3 );

/**
 * Lazy load Twitter.
 *
 * @param string $html      oEmbed HTML.
 * @param string $url       URL.
 * @param array  $attr      Params.
 *
 * @return string
 */
function mace_lazy_load_twitter( $html, $url, $attr ) {
	if ( false === strpos( $url, 'twitter.com' ) ) {
		return $html;
	}

	// Look for the <script> tag.
	if ( preg_match( '/<script.*src="([^"]+)"[^>]*><\/script>/', $html, $matches ) ) {
		$script_tag = $matches[0];
		$script_url = $matches[1];

		// Strip <script> tag to prevent immediate loading.
		$html = str_replace( $script_tag, '', $html );

		// Set up lazysizes.
		$html = str_replace( 'blockquote class="twitter-tweet"', 'blockquote class="twitter-tweet lazyload" data-script="'. $script_url .'" ', $html );

		// Start loading 600px below the screen.
		$html = str_replace( 'data-script', 'data-expand="600" data-script', $html );
	}

	return $html;
}
