<?php
/**
 * Common
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Check whether the lazy load module is enabled
 *
 * @return bool
 */
function mace_lazy_load_enabled() {
	return 'standard' === get_option( 'mace_lazy_load_enabled', 'standard' );
}

/**
 * Check whether to lazy load images
 *
 * @return string
 */
function mace_lazy_load_images_enabled() {
	return 'standard' === get_option( 'mace_lazy_load_images', 'standard' );
}

function  mace_get_lazy_load_images() {
	return mace_lazy_load_images_enabled();
}

/**
 * Check whether to lazy load images with unveilling effect
 *
 * @return string
 */
function mace_lazy_load_images_unveilling_effect_enabled() {
	return 'standard' === get_option( 'mace_lazy_load_images_unveilling_effect', 'standard' );
}

/**
 * Check whether to lazy load embeds
 *
 * @return string
 */
function mace_lazy_load_embeds_enabled() {
	return 'standard' === get_option( 'mace_lazy_load_embeds', 'standard' );
}

/**
 * YouTube player arguments
 *
 * @return string
 */
function mace_get_lazy_load_yt_player_args() {
	$defaults = array(
		'rel'       => 1,
	);

	$args = get_option( 'mace_lazy_load_yt_player_args', false );

	// If not set.
	if ( false === $args ) {
		 $args = $defaults;
	}

	// Normalize.
	if ( false !== $args && ! isset( $args['rel'] ) ) {
		$args['rel'] = 0;
	}

	return $args;
}

/**
 * Return CSS class for lazy load
 *
 * @return string
 */
function mace_get_lazy_load_class() {
	return apply_filters( 'mace_lazy_load_class', 'lazyload' );
}

/**
 * Disable lazy load on feeds
 *
 * @param  bool $lazy_load  Wheter to lazy load.
 * @return bool
 */
function mace_disable_lazy_load_on_feed( $lazy_load ) {
	if ( is_feed() ) {
		return false;
	}
	return $lazy_load;
}


class Mace_Lazy_Load {
	private static $instance;

	static function get_instance() {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Based on: https://codepen.io/shshaw/post/responsive-placeholder-image
	 *
	 * @param $width int
	 * @param $height int
	 *
	 * @return string
	 */
	function get_placeholder_src( $width, $height ) {
		return 'data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\' viewBox%3D\'0 0 ' . absint( $width ) . ' ' . absint( $height ) .'\'%2F%3E';
	}

}