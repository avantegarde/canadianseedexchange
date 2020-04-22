<?php
/**
 * Common Hooks
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'admin_notices', 'mace_time_limit_notice' );

add_action( 'plugins_loaded', 'mace_load_textdomain' );
