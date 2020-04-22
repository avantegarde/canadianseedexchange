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
 * Get size information for all currently-registered image sizes.
 *
 * @param string $group     Optional. Image size group.
 *
 * @return array            Data for all currently-registered image sizes.
 */
function mace_get_image_sizes( $group = null ) {
	global $_wp_additional_image_sizes;

	static $sizes;

	$used_sizes = apply_filters( 'mace_get_used_image_sizes', array() );

	if ( ! $sizes ) {
		$sizes = array(
			'wp'            => array(),
			'theme_plugins' => array(),
			'custom'        => array(),
			'inactive'      => array(),
		);

		$mace_sizes = get_option( 'mace_image_sizes', array() );

		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( mace_is_wp_image_size( $_size ) ) {
				$sizes['wp'][ $_size ] = array(
					'active' => true,
					'width'  => get_option( "{$_size}_size_w" ),
					'height' => get_option( "{$_size}_size_h" ),
					'crop'   => (bool) get_option( "{$_size}_crop" ),
					'defaults'	=> mace_get_built_in_default( $_size ),
				);
			} else {
				$_group = mace_is_custom_image_size( $_size ) ? 'custom' : 'theme_plugins';

				$size_config = array();

				if ( isset( $mace_sizes[ $_size ] ) ) {
					$size_config = $mace_sizes[ $_size ];
				}

				$default_config = array(
					'active' => true,
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					'used'   => false,
				);

				$size_config = wp_parse_args( $size_config, $default_config );
				$size_config['defaults'] = $default_config;

				if ( in_array( $_size, $used_sizes ) ) {
					$size_config['in_use'] = true;
				}

				if ( $size_config['active'] ) {
					$sizes[ $_group ][ $_size ] = $size_config;
				}
			}
		}

		// Set up inactive.
		foreach ( $mace_sizes as $name => $config ) {
			if ( ! $config['active'] ) {
				$sizes['inactive'][ $name ] = $config;
			}
		}
	}

	return $group ? $sizes[ $group ] : $sizes;
}

function mace_save_image_size( $name, $args ) {
	$args = wp_parse_args( $args, array(
		'width'     => 0,
		'height'    => 0,
		'crop'      => false,
		'crop_x'    => 'center',
		'crop_y'    => 'center',
	) );

	// WP built-in size?
	if ( mace_is_wp_image_size( $name ) ) {
		update_option( "{$name}_size_w", $args['width'] );
		update_option( "{$name}_size_h", $args['height'] );
		update_option( "{$name}_crop",   $args['crop'] );

	} else {
		$mace_sizes = get_option( 'mace_image_sizes', array() );

		// Init size if not already exists.
		if ( ! isset( $mace_sizes[ $name ] ) ) {
			$mace_sizes[ $name ] = array(
				'active' => true,
			);
		}

		// Update size.
		$mace_sizes[ $name ]['width']   = $args['width'];
		$mace_sizes[ $name ]['height']  = $args['height'];
		$mace_sizes[ $name ]['crop']    = $args['crop'];
		$mace_sizes[ $name ]['crop_x']  = $args['crop_x'];
		$mace_sizes[ $name ]['crop_y']  = $args['crop_y'];

		update_option( 'mace_image_sizes', $mace_sizes );
	}

	return true;
}

function mace_delete_image_size( $name ) {
	$mace_sizes = get_option( 'mace_image_sizes', array() );

	if ( mace_is_wp_image_size( $name ) ) {
		return new WP_Error( 'mace_wp_image_size', esc_html__( 'This image size can\'t be deleted!', 'mace' ) );
	}

	// We can delete our custom sizes.
	if ( mace_is_custom_image_size( $name ) ) {

		if ( isset( $mace_sizes[ $name ] ) ) {
			unset( $mace_sizes[ $name ] );
		}

		// Theme and plugins sizes can be only marked as inactive.
	} else {
		// Init size if not already exists.
		if ( ! isset( $mace_sizes[ $name ] ) ) {
			$mace_sizes[ $name ] = array();
		}

		$mace_sizes[ $name ]['active'] = false;
	}

	update_option( 'mace_image_sizes', $mace_sizes );

	return true;
}

function mace_activate_image_size( $name ) {
	if ( mace_is_wp_image_size( $name ) ) {
		return new WP_Error( 'mace_wp_image_size', esc_html__( 'This image size can\'t be activated!', 'mace' ) );
	}

	if ( mace_is_custom_image_size( $name ) ) {
		return new WP_Error( 'mace_custom_image_size', esc_html__( 'This image size can\'t be activated!', 'mace' ) );
	}

	$mace_sizes = get_option( 'mace_image_sizes', array() );

	if ( ! isset( $mace_sizes[ $name ] ) ) {
		return new WP_Error( 'mace_missing_image_size', esc_html__( 'This image size not exists!', 'mace' ) );
	}

	$mace_sizes[ $name ]['active'] = true;

	// If config consists just of the active flag, we can skip it.
	if ( 1 === count( $mace_sizes[ $name ] ) ) {
		unset( $mace_sizes[ $name ] );
	}

	update_option( 'mace_image_sizes', $mace_sizes );

	return true;
}
