<?php
/**
 * BuddyPress Snax Plugin
 *
 * @package snax
 * @subpackage BuddyPress
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'bp_include', 'snax_setup_buddypress', 10 );

/**
 * Setup BuddyPress
 */
function snax_setup_buddypress() {
	if ( ! function_exists( 'buddypress' ) ) {
		/**
		 * Create helper for BuddyPress 1.6 and earlier.
		 *
		 * @return bool
		 */
		function buddypress() {
			return isset( $GLOBALS['bp'] ) ? $GLOBALS['bp'] : false;
		}
	}

	// Bail if in maintenance mode.
	if ( ! buddypress() || buddypress()->maintenance_mode ) {
		return;
	}
	$bp_path = trailingslashit( dirname( __FILE__ ) );
	require( $bp_path . 'functions.php' );

	// Load template functions.
	require( $bp_path . 'template-functions.php' );

	// Load notifications.
	if ( bp_is_active( 'notifications' ) ) {
		require( $bp_path . 'notifications.php' );
	}

	// Load activities.
	if ( bp_is_active( 'activity' ) ) {
		require( $bp_path . 'activity.php' );
	}

	/* Activate our custom components */
	global $pagenow;
	$forced =  ( 'options-permalink.php' === $pagenow ) ? true : false;
	snax_bp_activate_components( $forced );

	/** COMPONENTS ********************************** */

	// Instantiate BuddyPress components.
	snax()->plugins->buddypress = new stdClass();

	// Posts.
	if ( bp_is_active( 'snax_posts' ) ) {
		require( $bp_path . 'components/posts.php' );

		$posts_component = new Snax_Posts_BP_Component();

		snax()->plugins->buddypress->snax_posts = $posts_component;

		// Register our custom componentns references into BP to enable BP notifications built-in system.
		// BP checkes active notifications components and only in this way we can inject our components into it.
		buddypress()->snax_posts = $posts_component;
	}

	// Items.
	if ( bp_is_active( 'snax_items' ) ) {
		require( $bp_path . 'components/items.php' );

		$items_component = new Snax_Items_BP_Component();

		snax()->plugins->buddypress->snax_items = $items_component;

		buddypress()->snax_items = $items_component;
	}

	// Votes.
	if ( bp_is_active( 'snax_votes' ) ) {
		require( $bp_path . 'components/votes.php' );

		$votes_component = new Snax_Votes_BP_Component();

		snax()->plugins->buddypress->snax_votes = $votes_component;

		buddypress()->snax_votes = $votes_component;
	}
}
