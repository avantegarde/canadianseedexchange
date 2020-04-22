<?php
/**
 * Featured Images Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Auto download featured image for video format
 *
 * @param int $post_id  Post id.
 */
function mace_download_featured_media_for_video( $post_id ) {
	if ( ! mace_get_auto_featured_image_enable() ) {
		return;
	}
	$format = get_post_format( $post_id );
	if ( ! has_post_thumbnail( $post_id ) && 'video' === $format ) {
		remove_action( 'save_post', 'mace_download_featured_media_for_video', 10, 1 );
		mace_download_embed_featured_media( $post_id );
		add_action( 'save_post', 'mace_download_featured_media_for_video', 10, 1 );
	}
}

/**
 * Download embedded content thumbnail and set as featured media
 *
 * @param int $post_id      Post id.
 */
function mace_download_embed_featured_media( $post_id ) {
	$embed_post = get_post( $post_id );
	$url        = mace_get_first_url_in_content( $embed_post );
	$max_width  = 1000; // Max thumbnail width.
	remove_filter( 'oembed_providers', '__return_empty_array' );
	$wp_oembed    = new WP_oEmbed();
	add_filter( 'oembed_providers', '__return_empty_array' );
	$provider_url = $wp_oembed->get_provider( $url );
	$img_url      = '';
	$max_res_set  = false;

	if ( ! empty( $provider_url ) ) {
		$json    = file_get_contents( $provider_url . '?url=' . $url . '&maxwidth=' . $max_width . '&format=json' );
		$json    = json_decode( $json );
		$img_url = $json->thumbnail_url;
	}

	// Special treatment for YT to try to get maxresdefault.jpg.
	if ( strpos( $url, 'youtube' ) || strpos( $url, 'youtu.be' ) ) {
		$max_res_yt_img_url = str_replace( 'hqdefault', 'maxresdefault', $img_url );
		$max_res_set        = mace_sideload_featured_media( $max_res_yt_img_url, $post_id );
	}

	if ( ! empty( $img_url ) && ! $max_res_set ) {
		mace_sideload_featured_media( $img_url, $post_id );
	}
}

/**
 * Don't download the featured image if not on the supported provider list
 *
 * @param bool $result      False by default.
 * @param int  $url         Embed url.
 *
 * @return bool
 */
function mace_featured_media_supported_providers( $result, $url ) {
	$supported_providers = array(
		'youtube',
		'youtu.be',
		'vimeo',
		'dailymotion',
		'dai.ly',
		'flickr',
		'flic.kr',
		'photobucket',
		'funnyordie',
		'vine',
		'soundcloud',
		'slideshare',
		'instagr.am',
		'instagram',
		'issuu',
		'collegehumor',
		'tedcom',
		'kickstarter',
		'kck.st',
		'cloudup',
	);

	foreach ( $supported_providers as &$provider ) {
		if ( strpos( $url, $provider ) ) {
			$result = true;
		}
	}

	return $result;
}

/**
 * Get image from url and set is as featured
 *
 * @param string $img_url       Image url.
 * @param int    $post_id       Post id.
 *
 * @return bool
 */
function mace_sideload_featured_media( $img_url, $post_id ) {
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	add_action( 'add_attachment', 'mace_set_sideloaded_thumb_as_featured' );

	if ( is_wp_error( media_sideload_image( $img_url, $post_id, 'The embed thumbnail', 'src' ) ) ) {
		remove_action( 'add_attachment', 'mace_set_sideloaded_thumb_as_featured' );

		return false;
	};

	remove_action( 'add_attachment', 'mace_set_sideloaded_thumb_as_featured' );

	return true;
}

/**
 * When attachement is added, set it as featured media for parent
 *
 * @param int $att_id Attachment id.
 */
function mace_set_sideloaded_thumb_as_featured( $att_id ) {
	$att     = get_post( $att_id );
	$post_id = $att->post_parent;

	set_post_thumbnail( $post_id, $att_id );
}

/**
 * Check whether to lazy load images
 *
 * @return string
 */
function mace_get_auto_featured_image_enable() {
	return 'standard' === get_option( 'mace_auto_featured_image_enable', 'standard' );
}
