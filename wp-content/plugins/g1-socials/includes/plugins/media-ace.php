<?php
/**
 * MediaAce plugin functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package media-ace
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'g1_socials_snapchat_code_dots',    'g1_socials_mace_lazy_load' );
add_filter( 'g1_socials_snapchat_code_avatar',  'g1_socials_mace_lazy_load' );
add_filter( 'g1_socials_instagram_item_image',  'g1_socials_mace_lazy_load' );

function g1_socials_mace_lazy_load( $img ) {
	if ( mace_get_lazy_load_images() ) {
		$img = mace_lazy_load_content_image( $img );
	}

	return $img;
}