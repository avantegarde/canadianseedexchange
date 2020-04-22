<?php
/**
 * Front functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// Lazy load for Images.
if ( mace_lazy_load_images_enabled() ) {
	require_once $lazy_load_dir_path . 'images/functions.php';
}

// Lazy load for Embeds.
if ( mace_lazy_load_embeds_enabled() ) {
	require_once $lazy_load_dir_path . 'embeds/functions.php';
}

// Load assets.
add_action( 'wp_enqueue_scripts', 'mace_load_lazy_load_assets' );

/**
 * Load module assets
 */
function mace_load_lazy_load_assets() {
	$plugin_url = mace_get_plugin_url();

	wp_enqueue_script( 'lazysizes', $plugin_url . 'includes/lazy-load/assets/js/lazysizes/lazysizes.min.js', array(), '4.0', true );
	wp_enqueue_script( 'lazysizes-unveilhooks', $plugin_url . 'includes/lazy-load/assets/js/lazysizes/plugins/unveilhooks/ls.unveilhooks.min.js', array( 'lazysizes' ), '5.2.0', true );
}
