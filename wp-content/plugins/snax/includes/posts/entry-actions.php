<?php
/**
 * Entry Actions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


add_filter( 'the_content', 'snax_todo_actions', 99 );

function snax_todo_actions( $content ) {
	if ( snax_in_custom_loop() || ! is_single() ) {
		return $content;
	}

	if ( apply_filters( 'snax_enable_entry_action_links', true ) ) {
		$content .= snax_capture_entry_action_links();
	}

	return $content;
}


/**
 * Output action links for entry
 *
 * @param array $args
 */
function snax_render_entry_action_links( $args = array() ) {
	$links = snax_capture_entry_action_links( $args );

	echo filter_var( $links );
}

/**
 * Return action links for entry
 *
 * @param array $args This function supports these arguments (
 *  - before: Before the links
 *  - after: After the links
 *  - sep: Links separator
 *  - links: item admin links array
 * ).
 *
 * @return string
 */
function snax_capture_entry_action_links( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'before' => '<div class="snax-actions"><a href="#" class="snax-actions-toggle">' . esc_html__( 'More', 'snax' ) . '</a><ul class="snax-action-links"><li>',
		'after'  => '</li></ul></div>',
		'sep'    => '</li><li>',
		'links'  => array(),
	) );

	$args = apply_filters( 'snax_entry_action_links_args', $args );

	// Prepare output.
	$out   = '';
	$links = implode( $args['sep'], array_filter( $args['links'] ) );

	if ( strlen( $links ) ) {
		$out = $args['before'] . $links . $args['after'];
	}

	return apply_filters( 'snax_capture_entry_action_links', $out, $args );
}
