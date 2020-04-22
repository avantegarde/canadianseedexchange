<?php
/**
 * Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once $lazy_load_dir_path . 'embeds/iframe.php';
require_once $lazy_load_dir_path . 'embeds/youtube.php';
require_once $lazy_load_dir_path . 'embeds/twitter.php';
require_once $lazy_load_dir_path . 'embeds/instagram.php';
require_once $lazy_load_dir_path . 'embeds/facebook.php';

// Disable embeds lazy load on the feed.
add_filter( 'mace_lazy_load_embed', 'mace_disable_lazy_load_on_feed' );
