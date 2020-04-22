<?php
/**
 * Hooks
 *
 * @package whats-your-reaction
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// Init.
add_action( 'init', 'wyr_register_taxonomy', 0 );
add_action( 'plugins_loaded', 'wyr_load_textdomain' );

// Post.
add_filter( 'the_content', 'wyr_load_post_voting_box' );

// Ajax.
add_action( 'wp_ajax_wyr_vote_post',        'wyr_ajax_vote_post' );
add_action( 'wp_ajax_nopriv_wyr_vote_post',	'wyr_ajax_vote_post' );

// Assets.
add_action( 'wp_enqueue_scripts', 'wyr_enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'wyr_enqueue_scripts' );

// Fake reactions.
add_filter( 'wyr_post_votes', 'wyr_fake_reaction_count', 11, 2 );

// Clean up.
add_action( 'delete_post', 'wyr_delete_post_votes', 10, 1 );
