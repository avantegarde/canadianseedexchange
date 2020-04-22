<?php
/**
 * Snax Share Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'wp_enqueue_scripts', 'snax_shares_enqueue_assets' );

/**
 * Load JS/CSS resources
 */
function snax_shares_enqueue_assets() {
	// Register script here, it will be loaded on demand only when needed.
	wp_register_script( 'snax-shares', snax()->assets_url . 'js/shares'. snax()->scripts_version .'.js', array( 'jquery' ), snax()->version, true );

	$config = array(
		'debug_mode'    => snax_shares_debug_mode_enabled(),
		'facebook_sdk'  => array(
			'url'               => sprintf( 'https://connect.facebook.net/%s/sdk.js', get_locale() ),
			'app_id'	        => snax_get_facebook_app_id(),
			'version'	        => 'v5.0',
		),
		'i18n' => array(
			'fb_app_id_not_set' => _x( 'Facebook App Id not set in Snax > Shares', 'Shares', 'snax' ),
		),
	);

	$config  = apply_filters( 'snax_shares_config', $config );

	wp_localize_script( 'snax-shares', 'snax_shares_config', wp_json_encode( $config ) );
}

/**
 * Return Facebook share button
 *
 * @param array $args       Buttons config.
 *
 * @return string           Button HTML.
 */
function snax_facebook_share_button( $args ) {
	// Load dependencies.
	wp_enqueue_script( 'snax-shares' );

	$args = wp_parse_args( $args, array(
		'share_url'     => '',
		'share_text'    => '',
		'classes'       => array(),
		'label'         => esc_html__( 'Share on Facebook', 'snax' ),
		'on_share'      => '',
	));

	// Add class for JS logic.
	$args['classes'][] = 'snax-share-facebook';

	// When JS fails, share still be possible.
	$share_fallback_url = sprintf( 'https://www.facebook.com/dialog/share?app_id=%s&display=popup&href=%s&quote=%s',snax_get_facebook_app_id(), $args['share_url'], $args['share_text'] );

	// Format HTML.
	$button = sprintf( '<a class="%s" href="%s" title="%s" data-share-url="%s" data-share-text="%s" data-on-share-callback="%s" target="_blank" rel="nofollow">%s</a>',
		implode( ' ', array_map( 'sanitize_html_class', $args['classes'] ) ),
		esc_url( $share_fallback_url ),
		esc_html__( 'Share on Facebook', 'snax' ),
		esc_url( $args['share_url'] ),
		esc_attr( $args['share_text'] ),
		esc_js( $args['on_share'] ),
		esc_html( $args['label'] )
	);

	return apply_filters( 'snax_facebook_share_button_html', $button, $args );
}

/**
 * Render Facebook share button
 *
 * @param array $args       Buttons config.
 */
function snax_render_facebook_share_button( $args ) {
	echo snax_facebook_share_button( $args );
}

/**
 * Return Twitter share button
 *
 * @param array $args       Buttons config.
 *
 * @return string           Button HTML.
 */
function snax_twitter_share_button( $args ) {
	// Load dependencies.
	wp_enqueue_script( 'snax-shares' );

	$args = wp_parse_args( $args, array(
		'share_url'     => '',
		'share_text'    => '',
		'classes'       => array(),
		'label'         => esc_html__( 'Share on Twitter', 'snax' ),
	));

	// Add class for JS logic.
	$args['classes'][] = 'snax-share-twitter';

	$twitter_url = add_query_arg( array(
		'url'  => $args['share_url'],
		'text' => urlencode( $args['share_text'] ),
	), '//twitter.com/intent/tweet' );

	// Format HTML.
	$button = sprintf( '<a class="%s" href="%s" title="%s" target="_blank" rel="nofollow">%s</a>',
		implode( ' ', array_map( 'sanitize_html_class', $args['classes'] ) ),
		esc_url( $twitter_url ),
		esc_html__( 'Share on Twitter', 'snax' ),
		esc_attr( $args['label'] )
	);

	return apply_filters( 'snax_facebook_share_button_html', $button, $args );
}

/**
 * Render Twitter share button
 *
 * @param array $args       Buttons config.
 */
function snax_render_twitter_share_button( $args ) {
	echo snax_twitter_share_button( $args );
}

/**
 * Return Pinterest share button
 *
 * @param array $args       Buttons config.
 *
 * @return string           Button HTML.
 */
function snax_pinterest_share_button( $args ) {
	// Load dependencies.
	wp_enqueue_script( 'snax-shares' );

	$args = wp_parse_args( $args, array(
		'share_url'     => '',
		'share_text'    => '',
		'share_media'   => '',
		'classes'       => array(),
		'label'         => esc_html__( 'Share on Pinterest', 'snax' ),
	));

	// Add class for JS logic.
	$args['classes'][] = 'snax-share-pinterest';

	$pinterest_url = add_query_arg( array(
		'url'           => $args['share_url'],
		'description'   => $args['share_text'],
		'media'         => $args['share_media'],
	), 'https://pinterest.com/pin/create/button' );

	// Format HTML.
	$button = sprintf( '<a class="%s" href="%s" title="%s" target="_blank" rel="nofollow">%s</a>',
		implode( ' ', array_map( 'sanitize_html_class', $args['classes'] ) ),
		esc_url( $pinterest_url ),
		esc_html__( 'Share on Pinterest', 'snax' ),
		esc_attr( $args['label'] )
	);

	return apply_filters( 'snax_facebook_share_button_html', $button, $args );
}

/**
 * Render Pinterest share button
 *
 * @param array $args       Buttons config.
 */
function snax_render_pinterest_share_button( $args ) {
	echo snax_pinterest_share_button( $args );
}
