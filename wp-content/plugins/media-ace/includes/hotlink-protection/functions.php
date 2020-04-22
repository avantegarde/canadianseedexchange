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

if ( mace_hotlink_is_protection_enabled() ) {
	add_filter( 'mod_rewrite_rules', 'mace_hotlink_protection_rewrite_rules' );
}

add_action( 'admin_init', 'mace_hotlink_protection_save_rewrite_rules' );

/**
 * Protect your bandwidth by preventing other sites from using images hosted on your site.
 *
 * @param string  $rewrite
 *
 * @return string
 */
function mace_hotlink_protection_rewrite_rules( $rewrite ) {
	$block_access_to    = mace_hotlink_get_block_access_to();

	// There are no defined files to protect.
	if ( empty( $block_access_to ) ) {
		return $rewrite;
	}

	$site_domain        = mace_get_site_domain();
	$block_access_to    = explode( ' ', trim( $block_access_to ) ); // Sanitized array.
	$direct_requests    = mace_hotlink_allow_direct_requests();
	$redirect_url       = mace_hotlink_get_redirect_url();
	$redirect_flags     = '[NC,R,L]';   // R - redirect, $redirect url has to be set.

	if ( empty( $redirect_url ) ) {
		// Use default.
		$redirect_url = mace_get_plugin_url() . 'assets/hotlink-placeholder.png';
//		$redirect_url   = '-';
//		$redirect_flags = '[NC,F,L]';   // F - forbidden.
	}

	$rules  = PHP_EOL . "# MediaAce Rules - Hotlink protection" . PHP_EOL;
	$rules .= "<IfModule mod_rewrite.c>" . PHP_EOL;
	$rules .= "RewriteEngine On" . PHP_EOL;

	// Prevent infinite loop when we redirect to image which is on the same domain when we have hotlink protection enabled.
	if ( ! empty( $redirect_url ) ) {
		$redirect_url_cond = $redirect_url;

		// Strip domain, if redirect url is local.
		if ( false !== strpos( $redirect_url, $site_domain ) ) {
			$redirect_url_cond = str_replace(
				array(
					$site_domain,
					'http://',
					'https://',
				),
				array(
					'',
					'',
					'',
				),
				$redirect_url
			);
		}

		$rules .= "RewriteCond %{REQUEST_URI} !$redirect_url_cond$" . PHP_EOL;
	}

	if ( $direct_requests ) {
		$rules .= "RewriteCond %{HTTP_REFERER} !^$" . PHP_EOL;
	}

	$rules .= "RewriteCond %{HTTP_REFERER} !^(http(s)?://)?(www\.)?$site_domain [NC]" . PHP_EOL;

	$whitelist_domains = preg_split( '/[\s,]+/', mace_hotlink_get_whilelist_domains() );

	foreach ( $whitelist_domains as $domain ) {
		$rules .= "RewriteCond %{HTTP_REFERER} !^$domain [NC]" . PHP_EOL;
	}

	$rules .= "RewriteRule \.(" . implode( '|', $block_access_to ) . ")$ $redirect_url $redirect_flags" . PHP_EOL;
	$rules .= "</IfModule>" . PHP_EOL . PHP_EOL;

	return $rules . $rewrite;
}

function mace_hotlink_protection_save_rewrite_rules() {
	$page    = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
	$updated = filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN );

	if ( $page === mace_get_hotlink_settings_page_id() && $updated ) {
		save_mod_rewrite_rules();
	}
}

/**
 * Check whether the Hotlink Protection is active
 *
 * @return bool
 */
function mace_hotlink_is_protection_enabled() {
	return 'standard' === get_option( 'mace_hotlink_protection', 'standard' );
}

/**
 * Return whilelist domains
 *
 * @return string
 */
function mace_hotlink_get_whilelist_domains() {
	$default_domains = array(
		'(http(s)?://)?(www\.)?facebook\.com',
		'(http(s)?://)?(www\.)?google\.*$/.*',
		'(http(s)?://)?(www\.)?pinterest\.*$/.*',
	);

	return get_option( 'mace_hotlink_whitelist_domains', implode( "\n", $default_domains ) );
}

/**
 * Return protected files extensions
 *
 * @return string
 */
function mace_hotlink_get_block_access_to() {
	return get_option( 'mace_hotlink_block_access_to', 'jpg jpeg png gif' );
}

/**
 * Check whether allow direct requests
 *
 * @return bool
 */
function mace_hotlink_allow_direct_requests() {
	return 'standard' === get_option( 'mace_hotlink_allow_direct_requests', 'standard' );
}

/**
 * Return URL to which blocked request will be forwarded
 *
 * @return string
 */
function mace_hotlink_get_redirect_url() {
	return get_option( 'mace_hotlink_redirect_url', '' );
}
