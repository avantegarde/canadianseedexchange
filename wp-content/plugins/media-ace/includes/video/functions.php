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
 * Check whether to lazy load images
 *
 * @return string
 */
function mace_is_auto_video_length_enabled() {
	return 'standard' === get_option( 'mace_auto_video_length', 'standard' );
}

/**
 * Return video object by url
 *
 * @param string $url       Video url.
 *
 * @return Mace_Video|WP_Error
 */
function mace_get_video( $url ) {
	$url_type = mace_get_video_type( $url );

	if ( false === $url_type ) {
		$supported_types = mace_get_video_supported_types();

		return new WP_Error( 'mace_unknown_video_type', sprintf( 'URL %s does not match any of known video types (%s).', $url, implode( ', ', $supported_types ) ) );
	}

	$class_name = sprintf( 'Mace_Video_%s', $url_type );

	if ( ! class_exists( $class_name ) ) {
		return new WP_Error( 'mace_undefined_class', sprintf( 'Class %s does not exist!' ), $class_name );
	}

	/**
	 * Video object
	 *
	 * @var Mace_Video $video_obj
	 */
	return new $class_name( $url );
}

/**
 * Return video type (youtube, vimeo, self_hosted) by url
 *
 * @param string $url       Video url.
 *
 * @return string|bool      Type or false if doesn't match to any.
 */
function mace_get_video_type( $url ) {
	$type_regex = mace_get_video_type_regex();

	foreach ( $type_regex as $type => $regex ) {
		if ( preg_match( $regex, $url ) ) {
			return $type;
		}
	}

	// Self-hosted?
	if ( false !== strpos( $url, get_home_url() ) ) {
		return 'SelfHosted';
	}

	return false;
}

/**
 * Return regular expression to parse video url
 *
 * @param string $type          Optional. Video type.
 *
 * @return string|array
 */
function mace_get_video_type_regex( $type = '' ) {
	$regex = apply_filters( 'mace_video_type_regex', array(
		'YouTube'   =>'#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#',
		'Vimeo'     => '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/',
	) );

	if ( ! empty( $type ) && isset( $regex[ $type ] ) ) {
		return $regex[ $type ];
	}

	return $regex;
}

function mace_get_video_supported_types() {
	$types = array(
		'YouTube',
		'Vimeo',
		'self-hosted'
	);

	return apply_filters( 'mace_video_supported_types', $types );
}

/**
 * Fetch meta data (length etc) by video url
 *
 * @param int    $post_id       Post id.
 * @param string $video_url     Optional. Video url for get data for.
 *
 * @return array|WP_Error       WP_Error if failed, array on success.
 */
function mace_fetch_video_data( $post_id, $video_url = '' ) {
	if ( ! $video_url ) {
		$video_url = mace_get_first_url_in_content( $post_id );
	}

	if ( ! $video_url ) {
		return new WP_Error( 'mace_url_not_found', 'Video url not found in content!' );
	}

	/**
	 * Video object
	 *
	 * @var Mace_Video $video
	 */
	$video = mace_get_video( $video_url );

	if ( is_wp_error( $video ) ) {
		return $video;
	}

	$last_error = $video->get_last_error();

	if ( ! empty( $last_error ) ) {
		return new WP_Error( 'mace_unknown_error', $last_error );
	}

	$data = array(
		'url'    => $video_url,
		'length' => $video->get_duration(),
	);

	return $data;
}
