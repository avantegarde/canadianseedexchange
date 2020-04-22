<?php
/**
 * Instagram things
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'options-page.php';
}

if ( get_option( 'g1_socials_instagram_enabled', false ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'helpers.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-g1-socials-widget-instagram.php' );

	require_once plugin_dir_path( __FILE__ ) . 'functions.php';
}