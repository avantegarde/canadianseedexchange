<?php
/**
 * Video module loader
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/interface-mace-video.php' );
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/class-mace-video.php' );
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/class-mace-video-youtube.php' );
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/class-mace-video-vimeo.php' );
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/class-mace-video-self-hosted.php' );
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'functions.php' );

if ( mace_is_auto_video_length_enabled() ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'auto-video-length.php' );
}

if ( is_admin() ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/settings.php' );
}
