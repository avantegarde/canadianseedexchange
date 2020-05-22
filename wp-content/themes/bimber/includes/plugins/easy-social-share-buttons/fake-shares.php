<?php
/**
 * East Social Share Buttons fake shares
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Bimber_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Bimber version of the essb_apply_dummy_counter_values() function
 *
 * @param array $cached_counters        Counters.
 *
 * @return array
 */
function bimber_essb_apply_dummy_counter_values($cached_counters = array()) {
	global $post;

	if ( ! isset( $post ) ) {
		return $cached_counters;
	}

	$minimal_fake = get_option( 'essb-fake' );

	if ( ! is_array( $minimal_fake ) ) {
		$minimal_fake = array();
	}

	// @since 5.1 support for selected fake social counters
	$fake_networks = essb_option_value( 'fake_networks' );

	if ( ! is_array( $fake_networks ) ) {
		$fake_networks = array();
	}

	$post_id = $post->ID;

	$cumulative_total = 0;

	foreach ( $cached_counters as $network => $shares ) {
		if ( $network == 'total' ) {
			continue;
		}

		if ( count( $fake_networks ) > 0 && ! in_array( $network, $fake_networks ) ) {
			$shares = $cached_counters[ $network ];
			$cumulative_total += intval( $shares );
			continue;
		}

		$shares = (int) get_post_meta( $post_id, 'essb_pc_'.$network, true );
		$minimal_fake_shares = isset( $minimal_fake['fake_'.$network] ) ? (int) $minimal_fake[ 'fake_'.$network ] : 0;

		$fake_factor = bimber_essb_get_fake_factor();
		$fake_shares = round( $minimal_fake_shares * $fake_factor, 0);

		// Agragate post shares with fake shares.
		$shares += $fake_shares;

		$cached_counters[ $network ] = $shares;
		$cumulative_total += intval( $shares );
	}

	$cached_counters['total'] = $cumulative_total;

	return $cached_counters;
}

/**
 * Return fake shares multiplier
 *
 * @return float
 */
function bimber_essb_get_fake_factor() {
	$words_in_title = count( explode( ' ', the_title_attribute('echo=0') ) );
	$factor = $words_in_title / 10;

	return apply_filters( 'bimber_essb_fake_factor', $factor );
}
