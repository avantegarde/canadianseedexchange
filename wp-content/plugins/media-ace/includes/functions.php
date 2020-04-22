<?php
/**
 * Common Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Plugin acitvation
 */
function mace_activate() {}

/**
 * Plugin deacitvation
 */
function mace_deactivate() {}

/**
 * Plugin uninstallation
 */
function mace_uninstall() {}

/**
 * Load plugin translations.
 */
function mace_load_textdomain() {
	load_plugin_textdomain( 'mace', false, 'media-ace/languages' );
}

/**
 * Load a template part into a template
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function mace_get_template_part( $slug, $name = null ) {
	// Trim off any slashes from the slug.
	$slug = ltrim( $slug, '/' );

	if ( empty( $slug ) ) {
		return;
	}

	$parent_dir_path = trailingslashit( get_template_directory() );
	$child_dir_path  = trailingslashit( get_stylesheet_directory() );

	$files = array(
		$child_dir_path . 'media-ace/' . $slug . '.php',
		$parent_dir_path . 'media-ace/' . $slug . '.php',
		mace_get_plugin_dir() . 'templates/' . $slug . '.php',
	);

	if ( ! empty( $name ) ) {
		array_unshift(
			$files,
			$child_dir_path . 'media-ace/' . $slug . '-' . $name . '.php',
			$parent_dir_path . 'media-ace/' . $slug . '-' . $name . '.php',
			mace_get_plugin_dir() . 'templates/' . $slug . '-' . $name . '.php'
		);
	}

	$located = '';

	foreach ( $files as $file ) {
		if ( empty( $file ) ) {
			continue;
		}

		if ( file_exists( $file ) ) {
			$located = $file;
			break;
		}
	}

	if ( strlen( $located ) ) {
		load_template( $located, false );
	}
}

/**
 * Return plugin default options
 *
 * @return array
 */
function mace_get_default_options() {
	return apply_filters( 'mace_default_options', array() );
}

/**
 * Return the correct admin URL based on WordPress configuration.
 *
 * @param string $path Optional. The sub-path under /wp-admin to be appended to the admin URL.
 *
 * @param string $scheme The scheme to use. Default is 'admin', which
 *                       obeys {@link force_ssl_admin()} and {@link is_ssl()}. 'http'
 *                       or 'https' can be passed to force those schemes.
 *
 * @return string        Admin url link with optional path appended.
 */
function mace_admin_url( $path = '', $scheme = 'admin' ) {
	// Links belong in network admin.
	if ( is_network_admin() ) {
		$url = network_admin_url( $path, $scheme );

		// Links belong in site admin.
	} else {
		$url = admin_url( $path, $scheme );
	}

	return $url;
}

/**
 * Return site domain name without protocol
 *
 * @return string
 */
function mace_get_site_domain() {
	$home_url = get_home_url();
	$site_domain = preg_replace( '/http(s)?:\/\/(www\.)?/i', '', $home_url );

	return $site_domain;
}

function mace_is_wp_image_size( $name ) {
	return in_array( $name, array( 'thumbnail', 'medium', 'medium_large', 'large' ) );
}

function mace_get_capability() {
	$capability = is_multisite() ? 'manage_network_options' : 'manage_options';

	return apply_filters( 'mace_capability', $capability );
}

function mace_is_attachment_image( $post ) {
	$post = get_post( $post );

	return ( 'attachment' === $post->post_type && 'image/' === substr( $post->post_mime_type, 0, 6 ) );
}

/**
 * Check if function is disabled in PHP.
 *
 * @param str $function  Function name.
 * @return bool
 */
function mace_is_function_allowed( $function ) {
	$disabled = ini_get( 'disable_functions ');
	if ( $disabled ) {
		$disabled = explode( ',', $disabled );
		$disabled = array_map( 'trim', $disabled );
		return ! in_array( $function, $disabled );
	}
	return true;
}

/**
 * Display a notice about max_execution_time and set_time_limit
 */
function mace_time_limit_notice() {
	$transient = get_transient( '_mace_set_time_limit_blocked' );
	if ( ! $transient ) {
		return;
	}
	delete_transient( '_mace_set_time_limit_blocked' );
	$max_exec_time = ini_get( 'max_execution_time' );
	if ( $max_exec_time > 100 ){
		return;
	}
	?>
	<div class="updated is-dismissible error bimber-translation-not-allowed">
			<p>
				<strong>PHP settings might cause MediaAce to fail</strong><br/>
			</p>
			<p>
				The set_time_limit() function is disabled and the time limit might not be high enough to handle
				watermarking or thumbnail regeneration. Please contact your server administrator.
			</p>
	</div>
<?php

}

/**
 * Check whether the plugin is active and plugin can rely on it
 *
 * @param string $plugin Base plugin path.
 *
 * @return bool
 */
function mace_can_use_plugin( $plugin ) {
	// Detect plugin. For use on Front End only.
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return is_plugin_active( $plugin );
}

/**
 * Insert new value just after specified key in array/hash.
 *
 * @param mixed $key  		Key to insert after.
 * @param array $array 		Array to operate on.
 * @param mixed $new_key	New key.
 * @param mixed $new_value	New val.
 * @return array
 */
function mace_array_insert_after( $key, array &$array, $new_key, $new_value ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = array();
		foreach ( $array as $k => $value ) {
			$new[ $k ] = $value;
			if ( $k === $key ) {
				$new[ $new_key ] = $new_value;
			}
		}
	  	return $new;
	}
	return $array;
}

add_action( 'style_loader_tag', 'mace_fix_rtl_styles', 10, 4 );
/**
 * Fix RTL styles.
 *
 * @param string $html   The link tag for the enqueued style.
 * @param string $handle The style's registered handle.
 * @param string $href   The stylesheet's source URL.
 * @param string $media  The stylesheet's media attribute.
 * @return string
 */
function mace_fix_rtl_styles( $html, $handle, $href, $media ){
	if ( strpos( $handle, 'mace-' ) > -1 ) {
		$html = str_replace( '.min-rtl', '-rtl.min', $html );
	}
	return $html;
}

/**
 * Strip HTML comments from text
 *
 * @param string $input         Input text.
 *
 * @return string
 */
function mace_strip_html_comments( $input = '' ) {
	return preg_replace( '/<!--(.|\s)*?-->/', '', $input );
}

/**
 * Return first url in post content
 *
 * @param int|WP_Post $p       Post id or WP_Post object.
 *
 * @return bool|string          False if not found.
 */
function mace_get_first_url_in_content( $p ) {
	$p = get_post( $p );

	if ( ! $p ) {
		return false;
	}

	$content = $p->post_content;

	$content = mace_strip_html_comments( $content );

	if ( preg_match( '/https?:\/\/[^\n"\'<]+/i', $content, $matches ) ) {
		return trim( esc_url_raw( $matches[0] ) );
	}

	return false;
}