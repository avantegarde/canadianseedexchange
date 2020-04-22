<?php
/**
 * Snapcode things
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Socials Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// Snapchat for now added here.
require_once( plugin_dir_path( __FILE__ ) . 'snapcode-helpers.php' );
require_once( plugin_dir_path( __FILE__ ) . 'snapcode-shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-g1_socials-snapcode-widget.php' );
