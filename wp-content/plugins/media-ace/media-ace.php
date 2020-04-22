<?php
/**
Plugin Name:    MediaAce
Description:    Your media assistant
Author:         bringthepixel
Version:        1.4.4
Author URI:     http://www.bringthepixel.com
Text Domain:    mace
Domain Path:    /languages/
License: 		Located in the 'Licensing' folder
License URI: 	Located in the 'Licensing' folder

@package media-ace
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Return the plugin directory path
 *
 * @return string
 */
function mace_get_plugin_dir() {
	return trailingslashit( plugin_dir_path( __FILE__ ) );
}

/**
 * Return the plugin directory url
 *
 * @return string
 */
function mace_get_plugin_url() {
	return trailingslashit( plugin_dir_url( __FILE__ ) );
}

/**
 * Return the plugin basename
 *
 * @return string
 */
function mace_get_plugin_basename() {
	return plugin_basename( __FILE__ );
}

/**
 * Return the plugin version
 *
 * @return string
 */
function mace_get_plugin_version() {
	$version = false;
	$data = get_plugin_data( __FILE__ );

	if ( ! empty( $data['Version'] ) ) {
		$version = $data['Version'];
	}

	return $version;
}

// Common.
require_once( mace_get_plugin_dir() . 'includes/functions.php' );
require_once( mace_get_plugin_dir() . 'includes/hooks.php' );

// Admin.
if ( is_admin() ) {
	require_once( mace_get_plugin_dir() . 'includes/admin/functions.php' );
	require_once( mace_get_plugin_dir() . 'includes/admin/settings/settings.php' );
	require_once( mace_get_plugin_dir() . 'includes/admin/hooks.php' );
}

// Modules.
require_once( mace_get_plugin_dir() . 'includes/image-sizes/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/image-bulk/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/regenerate-thumbs/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/lazy-load/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/watermarks/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/hotlink-protection/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/gif-to-mp4/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/auto-featured-image/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/expiry-header/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/video/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/video-playlist/loader.php' );
require_once( mace_get_plugin_dir() . 'includes/gallery/loader.php' );
$dev = defined( 'BTP_DEV' ) && BTP_DEV;
$staging = defined( 'BTP_ENV' ) && 'staging' === BTP_ENV;
$live = defined( 'BTP_ENV' ) && 'live' === BTP_ENV;
if ( $dev || $staging || $live ) {
	require_once( mace_get_plugin_dir() . 'includes/bulk-replace/loader.php' );
}
require_once( mace_get_plugin_dir() . 'includes/multisite/multisite.php' );

require_once( mace_get_plugin_dir() . 'includes/options.php' );
require_once( mace_get_plugin_dir() . 'includes/plugins/functions.php' );

// Init.
register_activation_hook( 	mace_get_plugin_basename(), 'mace_activate' );
register_deactivation_hook( mace_get_plugin_basename(), 'mace_deactivate' );
register_uninstall_hook( 	mace_get_plugin_basename(), 'mace_uninstall' );
