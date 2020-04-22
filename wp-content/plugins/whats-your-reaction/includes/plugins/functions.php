<?php
/**
 * Plugin resources loader
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package whats-your-reaction
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( wyr_can_use_plugin( 'mycred/mycred.php' ) ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/mycred/mycred.php' );
}

if ( wyr_can_use_plugin( 'buddypress/bp-loader.php' ) ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'buddypress.php' );
}
