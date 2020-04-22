<?php
/**
 * Twitter module
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

require_once plugin_dir_path( __FILE__ ) . 'functions.php';

if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'options-page.php';
}
