<?php
/**
 * Positions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Return all available share positions
 *
 * @return array
 */
function snax_get_share_positions() {
	$positions = array(
		'quiz_result' => array(
			'name'           => _x( 'Quiz Result', 'Shares', 'snax' ),
			'networks'       => array( 'facebook', 'twitter' ),
			'networks_order' => 'facebook,twitter',
		),
		'poll_question' => array(
			'name'           => _x( 'Poll Question', 'Shares', 'snax' ),
			'networks'       => array( 'facebook', 'twitter' ),
			'networks_order' => 'facebook,twitter',
		),
		'list_item' => array(
			'name'           => _x( 'List item', 'Shares', 'snax' ),
			'networks'       => array( 'pinterest', 'facebook', 'twitter' ),
			'networks_order' => 'pinterest,facebook,twitter',
		),
	);

	return apply_filters( 'snax_share_positions', $positions );
}

/**
 * Return active share positions
 *
 * @return array
 */
function snax_get_active_share_positions() {
	if ( ! snax_shares_enabled() ) {
		return array();
	}

	$positions = get_option( 'snax_share_positions' );

	// Option not set.
	if ( false === $positions ) {
		// Load default.
		$positions = snax_get_share_positions();

		return array_keys( $positions );
	}

	return $positions['active'];
}

/**
 * Check whether the position is active
 *
 * @param string $position      Position id.
 *
 * @return bool
 */
function snax_is_active_share_position( $position ) {
	$active_positions = snax_get_active_share_positions();

	return in_array( $position, $active_positions, true );
}

/**
 * Return share position active networks (ordered)
 *
 * @param string $position      Position id.
 *
 * @return array|WP_Error
 */
function snax_get_share_position_active_networks( $position ) {
    if ( ! snax_shares_enabled() ) {
        return array();
    }

	$positions = get_option( 'snax_share_positions' );

	// Option not set.
	if ( false === $positions ) {
		// Load default.
		$positions = snax_get_share_positions();
	}

	if ( isset( $positions[ $position ] ) ) {
		// Sort.
		$order = snax_get_share_position_networks_order( $position );
		$active_networks = $positions[ $position ]['networks'];

		foreach ( $order as $index => $network ) {
			if ( ! in_array( $network, $active_networks ) ) {
				unset( $order[ $index ] );
			}
		}

		return $order;
	}

	return array();
}

/**
 * Return share position networks order
 *
 * @param string $position      Position id.
 *
 * @return array|WP_Error
 */
function snax_get_share_position_networks_order( $position ) {
    if ( ! snax_shares_enabled() ) {
        return array();
    }

	$positions = get_option( 'snax_share_positions' );

	// Option not set.
	if ( false === $positions ) {
		// Load default.
		$positions = snax_get_share_positions();
	}

	if ( isset( $positions[ $position ] ) ) {
		$order_str =  $positions[ $position ]['networks_order'];
		return explode( ',', $order_str );
	}

	return array();
}
