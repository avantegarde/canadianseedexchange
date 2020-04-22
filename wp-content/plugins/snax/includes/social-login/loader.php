<?php
/**
 * Social Login module loader
 *
 * @package snax
 * @subpackage Social Login
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$slog_dir_path = trailingslashit( dirname( __FILE__ ) );

// Only core functions.
require_once $slog_dir_path . 'core.php';

// Settings panel.
if ( is_admin() ) {
	require_once $slog_dir_path . 'settings.php';
}

// Init. Using here the "after_setup_theme" hook, we are able to disable entire module on theme level.
add_action( 'after_setup_theme', 'snax_slog_bootstrap' );

function snax_slog_bootstrap() {
	if ( ! snax_slog_enabled() ) {
		return;
	}

	// Load functions.
	require_once trailingslashit( dirname( __FILE__ ) ) . 'functions.php';

	// Init.
	snax_slog_init();
}
