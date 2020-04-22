<?php
/**
 * Lazy load YouTube module loader
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( is_admin() ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/functions.php' );
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/ajax.php' );
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/settings.php' );
}


