<?php
/**
 * Snapcode shortcode
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

add_shortcode( 'g1_socials_snapcode', 'g1_socials_snapcode_shortcode' );
/**
 * Snapcode shortcode
 *
 * @param array $atts Snapcode shortcode atts.
 * @return string HTML
 */
function g1_socials_snapcode_shortcode( $atts ) {
	// Fill shortcode atts.
	$atts_filled = shortcode_atts(
		array(
			'username'   => '',
			'userid'     => '',
			'useravatar' => '',
		),
		$atts,
		'g1_socials_snapcode'
	);
	// Sanitize atts.
	$username   = filter_var( $atts_filled['username'], FILTER_SANITIZE_STRING );
	$user_id    = filter_var( $atts_filled['userid'], FILTER_SANITIZE_STRING );
	$useravatar = filter_var( $atts_filled['useravatar'], FILTER_SANITIZE_URL );
	// Check if atts are not empty.
	if ( empty( $username ) || empty( $user_id ) || empty( $useravatar ) ) {
		return '';
	} else {
		return g1_socials_get_snapcode( $username, $user_id, $useravatar );
	}
}
