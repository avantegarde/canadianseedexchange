<?php
/**
 * WPBakery Page Builder plugin functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Bimber_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'elements/collections.php' );
