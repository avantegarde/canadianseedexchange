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

if ( mace_expiry_is_headers_enabled() ) {
	add_filter( 'mod_rewrite_rules', 'mace_expiry_headers_rewrite_rules' );
}

add_action( 'admin_init', 'mace_expiry_headers_save_rewrite_rules' );

/**
 * Add expiry to htaccess
 *
 * @param string  $rewrite
 *
 * @return string
 */
function mace_expiry_headers_rewrite_rules( $rewrite ) {
	$rules = "<IfModule mod_expires.c>" . PHP_EOL . mace_expiry_content() . PHP_EOL . "</IfModule>" . PHP_EOL . PHP_EOL;
	return $rules . $rewrite;
}

function mace_expiry_headers_save_rewrite_rules() {
	$page    = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
	$updated = filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN );

	if ( $page === mace_get_expiry_settings_page_id() && $updated ) {
		save_mod_rewrite_rules();
	}
}

/**
 * Check whether the Expiry Headers is active
 *
 * @return bool
 */
function mace_expiry_is_headers_enabled() {
	return 'standard' === get_option( 'mace_expiry_headers', 'standard' );
}

/**
 * Return content
 *
 * @return string
 */
function mace_expiry_content() {
	$default_content = '
# Enable expirations
ExpiresActive On
# Default directive
ExpiresDefault "access plus 1 month"
# My favicon
ExpiresByType image/x-icon "access plus 1 year"
# Images
ExpiresByType image/gif "access plus 1 month"
ExpiresByType image/png "access plus 1 month"
ExpiresByType image/jpg "access plus 1 month"
ExpiresByType image/jpeg "access plus 1 month"
# CSS
ExpiresByType text/css "access plus 1 month"
# Javascript
ExpiresByType application/javascript "access plus 1 year"';

	return get_option( 'mace_expiry_content', $default_content );
}
