<?php
/**
 * Snax Menu Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Add Snax items to the menu
 *
 * @param WP_Post $menu_item        The menu item.
 *
 * @return WP_Post
 */
function snax_setup_nav_menu_item( $menu_item ) {
	if ( is_admin() ) {
		return $menu_item;
	}

	$menu_classes = $menu_item->classes;

	if ( is_array( $menu_classes ) ) {
		$menu_classes = implode( ' ', $menu_item->classes );
	}

	// The only place we can identify that the $menu_item is ours is CSS class (regex option U for not greedy, stop on first match).
	if ( ! preg_match( '/snax-([^\s]+)-nav$/U', $menu_classes, $matches ) ) {
		return $menu_item;
	}

	$menu_item_id = $matches[1];

	switch ( $menu_item_id ) {
		case 'logout' :
			// Hide for anonymous.
			if ( ! is_user_logged_in() ) {
				$menu_item->_invalid = true;
			} else {
				$menu_item->url = wp_logout_url();
			}

			break;

		case 'login' :
			// Hide item for logged in users.
			if ( is_user_logged_in() ) {
				$menu_item->_invalid = true;
			} else {
				$menu_item->url = wp_login_url();

				if ( ! is_array( $menu_item->classes ) ) {
					$menu_item->classes = array();
				}

				$menu_item->classes[] = 'snax-login-required';
			}

			break;

		case 'register' :
			// Hide for logged in users.
			if ( is_user_logged_in() ) {
				$menu_item->_invalid = true;
			} else {
				$menu_item->url = wp_registration_url();
			}

			break;

		case 'waiting-room' :
			$menu_item->url = snax_get_waiting_room_url();
			break;

		default:
			$menu_item = apply_filters( 'snax_menu_item_obj', $menu_item, $menu_item_id );

			break;
	}

	// Check if current page.
	$http_host      = filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL );
	$request_uri    = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
	$current_url    = ( is_ssl() ? 'https://' : 'http://' ) . $http_host . $request_uri;

	if ( false !== strpos( $current_url, $menu_item->url ) ) {
		if ( ! is_array( $menu_item->classes ) ) {
			$menu_item->classes = array();
		}

		$menu_item->classes[] = 'current_page_item';
		$menu_item->classes[] = 'current-menu-item';
	}

	return $menu_item;
}