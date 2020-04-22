<?php
/**
 * GIF Conversion Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

use \CloudConvert\Api;

/**
 * Convert GIF to MP4 on image added to the library
 *
 * @param int $post_id      Post id.
 */
function mace_convert_gif_to_mp4( $post_id ) {
	$api_key   = mace_get_gif_cc_key();
	$input_url = get_post( $post_id )->guid;

	$path_info = pathinfo( $input_url );

	if ( ! isset( $path_info['extension'] ) || 'gif' !== $path_info['extension'] || empty( $api_key ) ) {
		return;
	}

	$wp_upload_dir = wp_upload_dir();
	$output_url    = str_ireplace( '.gif', '.mp4', $input_url );
	$output_path   = str_ireplace( $wp_upload_dir['url'], $wp_upload_dir['path'], $output_url );

	$storage      = mace_get_gif_storage();
	$s3           = array();
	$s3['key']    = mace_get_gif_s3_keyid();
	$s3['keyid']  = mace_get_gif_s3_key();
	$s3['bucket'] = mace_get_gif_s3_bucket();

	foreach ( $s3 as $key => $value ) {
		if ( empty( $value ) ) {
			$storage = 'local';
		}
	}

	if ( 's3' === $storage ) {
		$result     = mace_cloud_convert_s3( $input_url, $output_path, $api_key, $s3 );
		$output_url = 'https://' . $s3['bucket'] . '.s3.amazonaws.com/' . basename( $output_url );
	} else {
		$result = mace_cloud_convert_local( $input_url, $output_path, $api_key );
	}

	if ( 'success' === $result ) {
		update_post_meta( $post_id, '_mace_mp4_version', $output_url );
		update_post_meta( $post_id, '_mace_mp4_version_storage', $storage );
	} else {
		echo $result;
	}
}

/**
 * Delete the MP4 version from local on attachment delete
 *
 * @param int $post_id      Post id.
 */
function mace_delete_mp4_version( $post_id ) {
	if ( 'local' !== get_post_meta( $post_id, '_mace_mp4_version_storage', true ) ) {
		return;
	}

	$mp4_file = str_replace( rtrim( get_site_url(), '/' ) . '/', ABSPATH, get_post_meta( $post_id, '_mace_mp4_version', true ) );
	unlink( $mp4_file );
}

/**
 * GIF to MP4 conversion for local storage
 *
 * @param string $input_url         GIF url.
 * @param string $output_path       Save path.
 * @param string $api_key           API key.
 *
 * @return string
 */
function mace_cloud_convert_local( $input_url, $output_path, $api_key ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/autoload.php' );

	$options = array( 'http' => array( 'header' => 'referer: ' . get_home_url() ) );
	$context = stream_context_create( $options );

	try {
		$api = new Api( $api_key );
		$args = array(
			'inputformat'  => 'gif',
			'outputformat' => 'mp4',
			'input'        => 'upload',
			'timeout'      => 0,
			'file'         => fopen( $input_url, 'r', false, $context ),
		);
		$api->convert( $args )
			->wait()
			->download( $output_path );

		$result  = 'success';
	} catch ( Exception $e ) {
		$result = 'CloudConvert: ' . $e->getMessage();
	}

	return $result;
}

/**
 * GIF to MP4 conversion for S3 storage
 *
 * @param string $input_url         GIF url.
 * @param string $output_path       Save path.
 * @param string $api_key           API key.
 * @param array  $s3                Config.
 *
 * @return string
 */
function mace_cloud_convert_s3( $input_url, $output_path, $api_key, $s3 ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/autoload.php' );

	/**
	 * Filters S3 endpoint
	 * apparently CC api works only with some endpoints, so better make this easy to debug
	 *
	 * @param string $s3_region     Amazon S3 region.
	 */
	$s3_region = apply_filters( 'mace_gif_convert_s3_region', 'eu-central-1' );

	try {
		$api     = new Api( $api_key );
		$api->convert( [
			'inputformat'  => 'gif',
			'outputformat' => 'mp4',
			'input'        => 'upload',
			'timeout'      => 0,
			'file'         => fopen( $input_url, 'r' ),
			'output'       => array(
				's3' => array(
					'acl'             => 'public-read',
					'accesskeyid'     => $s3['key'],
					'secretaccesskey' => $s3['keyid'],
					'bucket'          => $s3['bucket'],
					'region'          => $s3_region,
				),
			),
		] )
			->wait();

		$result  = 'success';
	} catch ( Exception $e ) {
		$result = 'CloudConvert: ' . $e->getMessage();
	}

	return $result;
}

/**
 * Replaces GIF images with mp4 version
 *
 * @param string $html                  HTML.
 * @param int    $attachment_id         Attachment id.
 *
 * @return string
 */
function mace_replace_gif_with_shortcode( $html, $attachment_id ) {
	$shortcode = mace_get_video_shortcode( $attachment_id );

	if ( ! $shortcode ) {
		return $html;
	}

	$html = preg_replace( '/<img[^>]*>/', $shortcode, $html );

	return $html;
}

/**
 * Return MP4 video shortocode
 *
 * @param int $attachment_id        Media attachemnt id.
 *
 * @return bool|string              MP4 video or false if not exists.
 */
function mace_get_video_shortcode( $attachment_id ) {
	$mp4_version = mace_get_gif_mp4_version( $attachment_id );

	if ( ! $mp4_version ) {
		return false;
	}

	$video_attr = array(
		'src="' . $mp4_version . '"',
		'class="wp-video-shortcode mace-video"',
	);

	$mp4_meta = wp_get_attachment_metadata( $attachment_id );

	if ( $mp4_meta ) {
		$video_attr[] = 'width="' . $mp4_meta['width'] . '"';
		$video_attr[] = 'height="' . $mp4_meta['height'] . '"';
	}

	return '[video ' . implode( ' ', $video_attr ) . ']';
}

/**
 * Return MP4 version of a GIF
 *
 * @param int $attachment_id        Gif attachment id.
 *
 * @return string|bool              MP4 url or false if not available
 */
function mace_get_gif_mp4_version( $attachment_id ) {
	$attachment = get_post( $attachment_id );

	if ( ! $attachment ) {
		return false;
	}

	$input_url = $attachment->guid;
	$pathinfo = pathinfo( $input_url );

	if ( ! isset( $pathinfo['extension'] ) || 'gif' !== $pathinfo['extension'] ) {
		return false;
	}

	$mp4_version = get_post_meta( $attachment_id, '_mace_mp4_version', true );

	if ( empty( $mp4_version ) ) {
		return false;
	}

	return $mp4_version;
}

/**
 * Find image ID by an url
 *
 * @param string $image_url         Image url.
 *
 * @return string
 */
function mace_get_image_by_url( $image_url ) {
	global $wpdb;

	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );

	return $attachment[0];
}

/**
 * Get gif url equivalent of and mp4 file
 *
 * @param string $mp4_url           MP4 url.
 *
 * @return string
 */
function mace_get_image_by_mp4_version( $mp4_url ) {
	$mp4_url = str_ireplace( 'src=', '', $mp4_url );
	$mp4_url = str_ireplace( '"', '', $mp4_url );
	$gif_url = '';

	$post    = get_posts( array(
		'meta_key'   => '_mace_mp4_version',
		'meta_value' => $mp4_url,
		'post_type'  => 'attachment',
	) );

	if ( ! empty( $post ) ) {
		$gif_url = $post[0]->guid;
	}

	return $gif_url;
}

/**
 * Return GIF CloudConvert API key
 *
 * @return string
 */
function mace_get_gif_cc_key() {
	return get_option( 'mace_gif_cc_key', '' );
}

/**
 * Return GIF storage name
 *
 * @return string
 */
function mace_get_gif_storage() {
	return get_option( 'mace_gif_storage', 'local' );
}

/**
 * Return Amazon S3 Access Key ID
 *
 * @return string
 */
function mace_get_gif_s3_keyid() {
	return get_option( 'mace_gif_s3_keyid', '' );
}

/**
 * Return Amazon S3 Access Key
 *
 * @return string
 */
function mace_get_gif_s3_key() {
	return get_option( 'mace_gif_s3_key', '' );
}

/**
 * Return Amazon S3 Bucket
 *
 * @return string
 */
function mace_get_gif_s3_bucket() {
	return get_option( 'mace_gif_s3_bucket', '' );
}
