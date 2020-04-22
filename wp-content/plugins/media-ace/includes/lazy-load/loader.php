<?php
/**
 * Lazy load module loader
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$lazy_load_dir_path = trailingslashit( dirname( __FILE__ ) );

// Common functions.
require_once $lazy_load_dir_path . 'common.php';

// Frontend.
if ( mace_lazy_load_enabled() && ! is_admin() ) {
	require_once $lazy_load_dir_path . 'front.php';
}

// Backend.
if ( is_admin() ) {
	require_once $lazy_load_dir_path . 'admin/settings.php';
}

// Backward compatibility.
if ( ! function_exists( 'mace_lazy_load_content_image' ) ) {
	function mace_lazy_load_content_image( $content ) {
		return $content;
	}

}