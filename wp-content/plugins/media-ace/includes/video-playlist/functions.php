<?php
/**
 * Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Enqueue video playlist assets
 */
function mace_vp_enqueue_scripts() {
	$ver = mace_get_plugin_version();
	$plugin_url = mace_get_plugin_url();

	wp_enqueue_style( 'wp-mediaelement' );

	wp_enqueue_style( 'mace-vp-style', $plugin_url . 'includes/video-playlist/css/video-playlist.min.css' );

	wp_enqueue_script( 'mace-vp-renderer-vimeo', $plugin_url . 'includes/video-playlist/js/mejs-renderers/vimeo.min.js', array( 'wp-mediaelement' ), $ver, true );
	wp_enqueue_script( 'mace-vp', $plugin_url . 'includes/video-playlist/js/playlist.js', array( 'jquery', 'wp-mediaelement' ), $ver, true );
}

/**
 * Extract video url from content, divided by new line.
 *
 * @param string $content       Content.
 *
 * @return array
 */
function mace_vp_extract_urls( $content ) {
	$content = strip_tags( $content );
	$content = trim( $content );
	str_replace( '[mace_video_item]', '', $content );
	$list = explode( '[/mace_video_item]', $content );
	$urls = array();

	foreach ( $list as $item_shortcode ) {
		$urls[] = do_shortcode( $item_shortcode );
	}

	return $urls;
}
