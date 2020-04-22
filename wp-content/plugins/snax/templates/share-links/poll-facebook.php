<?php
/**
 * Facebook share link
 *
 * @package snax 1.11
 * @subpackage Share
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

global $snax_share_args;

snax_render_facebook_share_button( array(
	'share_url'  => $snax_share_args['url'],
	'share_text' => $snax_share_args['description'],
	'label'      => __( 'Facebook', 'snax' ),
	'classes' => array(
		'quizzard-share',
		'quizzard-share-facebook'
	),
	'on_share' => 'snax_unlock_on_share',
) );
