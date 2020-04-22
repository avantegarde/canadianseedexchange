<?php
/**
 * MediaAce 3rd party plugins integration
 *
 * @package media-ace
 * @subpackage Plugins
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( mace_can_use_plugin( 'amp/amp.php' ) ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'amp.php' );
}

if ( mace_can_use_plugin( 'ad-ace/ad-ace.php' ) ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'ad-ace.php' );
}

if ( mace_can_use_plugin( 'revslider/revslider.php' ) ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'revslider.php' );
}
