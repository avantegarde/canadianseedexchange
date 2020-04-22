<?php
/**
 * Featured Images module loader
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'functions.php' );
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'hooks.php' );

if ( is_admin() ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/settings.php' );
}
