<?php
/**
 * Hooks
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'add_attachment', 			'mace_convert_gif_to_mp4', 10, 1 );
add_action( 'delete_attachment', 		'mace_delete_mp4_version', 10, 1 );
add_filter( 'image_send_to_editor', 	'mace_replace_gif_with_shortcode' , 10, 2 );
