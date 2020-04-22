<?php
/**
 * Plugin resources loader
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package g1-socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Check whether the plugin is active and theme can rely on it
 *
 * @param string $plugin        Base plugin path.
 * @return bool
 */
function g1_socials_can_use_plugin( $plugin ) {
	// Detect plugin. For use on Front End only.
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return is_plugin_active( $plugin );
}

if ( g1_socials_can_use_plugin( 'buddypress/bp-loader.php' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/buddypress.php' );
}

if ( g1_socials_can_use_plugin( 'media-ace/media-ace.php' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/media-ace.php' );
}