<?php
/**
 * YouTube embed
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'embed_oembed_html',    'mace_replace_yt_video_with_preview', 10, 3 );
add_action( 'wp_enqueue_scripts',   'mace_load_yt_lazy_load_assets' );

/**
 * Wrap YT embed in preview wrapper
 *
 * @param string $html oembed       HTML markup.
 * @param string $url               Embed URL.
 * @param array  $attr              Attributes.
 *
 * @return string
 */
function mace_replace_yt_video_with_preview( $html, $url, $attr ) {
	if ( ! apply_filters( 'mace_lazy_load_embed', true, $html, $url, $attr ) ) {
		return $html;
	}

	if ( ! mace_is_yt_embed( $url ) ) {
		return $html;
	}

	$video_id = mace_get_yt_video_id( $url );

	if ( ! $video_id ) {
		return $html;
	}

	$thumb_url  = mace_get_yt_thumb_url( $video_id );
	$iframe_url = mace_get_yt_iframe_url( $video_id );

	$replaced = '<div class="mace-youtube" data-mace-video="' . esc_url( $iframe_url ) . '" data-mace-video-thumb="' . esc_url( $thumb_url ) . '">' .
				'<div class="mace-play-button"></div>' .
				'</div>';

	return $replaced;
}

/**
 * Get video id from embed url
 *
 * @param string $url       Embed url.
 *
 * @return bool
 */
function mace_get_yt_video_id( $url ) {
	// https://www.youtube.com/watch?v=H0VdY6gSG0E
	if ( strpos( $url, 'youtube' ) ) {
		// Parse url query to get video id.
		$query_string = parse_url( $url, PHP_URL_QUERY );
		parse_str( $query_string, $query_args );

		if ( empty( $query_args ) || empty( $query_args['v'] ) ) {
			return false;
		}

		return $query_args['v'];
	}

	// https://www.youtu.be/H0VdY6gSG0E
	if ( strpos( $url, 'youtu.be' ) ) {
		if ( preg_match( '/youtu\.be\/(.+)$/', $url, $matches ) ) {
			return $matches[1];
		}
	}

	return false;
}

/**
 * Return video preview thumbnail
 *
 * @param string $video_id      Video id.
 *
 * @return string
 */
function mace_get_yt_thumb_url( $video_id ) {
	$url = 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';

	return apply_filters( 'mace_yt_thumb_url', $url, $video_id );
}

/**
 * Return video url to use in iframe
 *
 * @param string $video_id      Video id.
 * @param array $args           Optional. Query arguments (to set up player).
 *
 * @return string
 */
function mace_get_yt_iframe_url( $video_id, $args = array() ) {
	$url = 'https://www.youtube.com/embed/' . $video_id;

	$players_args = mace_get_lazy_load_yt_player_args();

	$args = wp_parse_args( $args, array(
		'showinfo'          => 0,
		'rel'               => $players_args['rel'],    // Show related movies after finishing.
		'ytp-pause-overlay' => $players_args['rel'],    // Show related movies when playback is paused.
		'autoplay'          => 1,
		'enablejsapi'       => 1,
	) );

	$args = apply_filters( 'mace_yt_player_args', $args );

	$url = add_query_arg( $args, $url );

	return apply_filters( 'mace_yt_iframe_url', $url, $video_id );
}

/**
 * Load YT lazy load js,css on demand
 */
function mace_load_yt_lazy_load_assets() {
	$ver = mace_get_plugin_version();
	$plugin_url = mace_get_plugin_url();

	wp_enqueue_style( 'mace-lazy-load-youtube', $plugin_url . 'includes/lazy-load/assets/css/youtube.min.css', array(), $ver );
	wp_enqueue_script( 'mace-lazy-load-youtube', $plugin_url . 'includes/lazy-load/assets/js/youtube.js', array( 'jquery' ), $ver, true );
}

/**
 * Check whether provided url is YouTube embed.
 *
 * @param string $url       Embed url.
 *
 * @return bool
 */
function mace_is_yt_embed( $url ) {
	return strpos( $url, 'youtube' ) || strpos( $url, 'youtu.be' );
}
