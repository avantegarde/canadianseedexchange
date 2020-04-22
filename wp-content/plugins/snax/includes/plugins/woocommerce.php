<?php
/**
 * WooCommerce plugin functions
 *
 * @package snax
 * @subpackage Plugins
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'woocommerce_prevent_admin_access', 'snax_woocommerce_prevent_admin_access', 99 );
add_filter( 'snax_format_taxonomy_post_types',  'snax_woocommerce_format_taxonomy_post_types' );
add_filter( 'template_include',                 'snax_woocommerce_template_include', 11 );

function snax_woocommerce_prevent_admin_access( $prevent ) {
	$snax_action = filter_input( INPUT_POST, 'snax_media_upload_action', FILTER_SANITIZE_STRING );

	if ( ! empty( $snax_action ) ) {
		$prevent = false;
	}

	return $prevent;
}

function snax_woocommerce_format_taxonomy_post_types( $post_types ) {
	$post_types[] = 'product';

	return $post_types;
}

function snax_woocommerce_template_include( $template ) {
	if ( is_tax( snax_get_snax_format_taxonomy_slug() ) ) {
		$object = get_queried_object();

		// Load default template for our taxonomy but leave External Product with WC template.
		if ( $object->slug !== 'extproduct' ) {
			$search_files = array(
				"taxonomy-{$object->taxonomy}-{$object->slug}.php",
				"taxonomy-{$object->taxonomy}.php",
				'taxonomy.php'
			);

			$template = locate_template( $search_files );
		}
	}

	return $template;
}
