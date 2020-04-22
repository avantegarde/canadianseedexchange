<?php
/**
 * Instagram embed
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'embed_oembed_html', 'mace_lazy_load_instagram', 10, 3 );

/**
 * Lazy load Instagram
 *
 * @param string $html      oEmbed HTML.
 * @param string $url       URL.
 * @param array  $attr      Params.
 *
 * @return string
 */
function mace_lazy_load_instagram( $html, $url, $attr ) {
	if ( false === strpos( $url, 'instagram.com' ) ) {
		return $html;
	}

	// Look for the <script> tag.
	if ( preg_match( '/<script.*src="([^"]+)"[^>]*><\/script>/', $html, $matches ) ) {
		$script_tag = $matches[0];
		$script_url = $matches[1];

		// Strip <script> tag to prevent immediate loading.
		$html = str_replace( $script_tag, '', $html );

		// Set up lazysizes.
		$html = str_replace( 'blockquote class="instagram-media"', 'blockquote class="instagram-media lazyload" data-script="'. $script_url .'" ', $html );

		// Start loading 600px below the screen.
		$html = str_replace( 'data-script', 'data-expand="600" data-script', $html );
	}

	return $html;
}
