<?php
/**
 * Hooks
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'save_post', 'mace_download_featured_media_for_video', 10, 1 );
