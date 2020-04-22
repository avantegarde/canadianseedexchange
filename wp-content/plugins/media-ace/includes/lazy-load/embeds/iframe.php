<?php
/**
 * IFrame
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'embed_oembed_html', 'mace_lazy_load_iframe', 9, 3 );

function mace_lazy_load_iframe( $html, $url, $attr ) {
	if ( ! apply_filters( 'mace_lazy_load_embed', true, $html, $url, $attr ) || is_embed() ) {
		return $html;
	}

	if ( 0 === strpos( $html, '<iframe' ) ) {
		$html       = str_replace('src=', 'data-src=', $html);
		$lazy_class = mace_get_lazy_load_class();

		if ( strpos( $html, 'class=' ) ) {
			$html = str_replace('class="', 'class="' . $lazy_class . ' ', $html);
		} else {
			$html = str_replace('<iframe', '<iframe class="' .$lazy_class . '" ', $html);
		}
	}

	return $html;
}
