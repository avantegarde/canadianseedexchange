<?php
/**
 * Twitter share link
 *
 * @package snax 1.11
 * @subpackage Share
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

global $snax_share_args;

snax_render_twitter_share_button( array(
	'share_url'  => $snax_share_args['url'],
	'share_text' => $snax_share_args['title'] . '. ' . $snax_share_args['description'],
	'label'      => __( 'Twitter', 'snax' ),
	'classes' => array(
		'quizzard-share',
		'quizzard-share-twitter'
	),
) );
