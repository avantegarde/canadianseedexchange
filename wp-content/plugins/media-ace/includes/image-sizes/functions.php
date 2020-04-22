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

add_action( 'after_setup_theme', 'mace_update_image_sizes', 99 );

function mace_update_image_sizes() {
	$sizes = get_option( 'mace_image_sizes' );

	if ( ! is_array( $sizes ) ) {
		return;
	}

	foreach ( $sizes as $name => $config ) {
		// Add new or update existing image size.
		if ( $config['active'] ) {
			$crop = false;

			if ( $config['crop'] ) {
				$crop = array(
					$config['crop_x'],
					$config['crop_y'],
				);
			}

			add_image_size( $name, $config['width'], $config['height'], $crop );
		} else {
			remove_image_size( $name );
		}
	}
}

function mace_get_image_size_prefix() {
	return 'mace_';
}

function  mace_is_custom_image_size( $name ) {
	return 0 === strpos( $name, mace_get_image_size_prefix() );
}

function mace_get_built_in_default( $size ) {
	$defaults = array(
		'thumbnail' => array(
			'active' => true,
			'width'  => '150',
			'height' => '150',
			'crop'   => true,
		),
		'medium' => array(
			'active' => true,
			'width'  => '300',
			'height' => '300',
			'crop'   => false,
		),
		'medium_large' => array(
			'active' => true,
			'width'  => '768',
			'height' => '0',
			'crop'   => false,
		),
		'large' => array(
			'active' => true,
			'width'  => '1024',
			'height' => '1024',
			'crop'   => false,
		),

	);
	return $defaults[ $size ];
}
