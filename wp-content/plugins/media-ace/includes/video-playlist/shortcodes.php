<?php
/**
 * Shortcodes
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_shortcode( 'mace_video_playlist',   'mace_vp_list_shortcode' );
add_shortcode( 'mace_video_item',       'mace_vp_item_shortcode' );

/**
 * Video playlist shortcode
 *
 * @param array  $atts          Shortcode attributes.
 * @param string $content       Optional. Shortcode content.
 *
 * @return string
 */
function mace_vp_list_shortcode( $atts, $content = '' ) {
	$atts   = wp_parse_args( $atts, array(
		'title'     => '',
		'width'     => '',
	) );
	$urls   = mace_vp_extract_urls( $content );
	$out    = '';
	$videos = array();

	foreach ( $urls as $url ) {
		/**
		 * Video object
		 *
		 * @var Mace_Video $video_obj
		 */
		$video_obj = mace_get_video( $url );

		if ( is_wp_error( $video_obj ) ) {
			continue;
		}

		if ( ! empty( $atts['width'] ) ) {
			$video_obj->set_width( absint( $atts['width'] ) );
		}

		$video_id = $video_obj->get_id();

		if ( empty( $video_id ) ) {
			$out .= $video_obj->get_last_error();
			continue;
		}

		$videos[] = $video_obj;
	}

	if ( count($urls) && empty( $videos ) && current_user_can('administrator') ) {
		echo esc_html__( 'The playlist could not be displayed, please make sure that YouTube API key is set in the settings.', 'mace' );
	}

	if ( ! empty( $videos ) ) {
		mace_vp_enqueue_scripts();

		global $mace_vp_data;
		$mace_vp_data = array(
			'title'     => $atts['title'],
			'videos'    => $videos,
		);

		ob_start();
		mace_get_template_part( 'video-playlist' );
		$out .= ob_get_clean();
	}

	return $out;
}

/**
 * Video item shortcode
 *
 * @param array  $atts          Shortcode attributes.
 * @param string $content       Optional. Shortcode content.
 *
 * @return string
 */
function mace_vp_item_shortcode( $atts, $content = '' ) {
	$content = strip_tags( $content );
	$content = trim( $content );

	return $content;
}