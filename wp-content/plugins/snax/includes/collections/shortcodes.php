<?php
/**
 * Snax Shortcodes
 *
 * @package snax
 * @subpackage Shortcodes
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_shortcode( 'snax_collection_intro', 'snax_collection_intro_shortcode' );

function snax_get_collection_intro_shortcode() {
	return '[snax_collection_intro]';
}

/**
 * Renders collection intro content
 */
function snax_collection_intro_shortcode() {
	//if ( is_user_logged_in() ) {
	//	return '';
	//}

	if ( ! snax_is_abstract_collection() && ! snax_is_custom_collection() ) {
		return '';
	}

	$obj  = get_queried_object();
	$slug = $obj->post_name;

	ob_start();
	snax_get_template_part( 'collections/intro', $slug );
	$out = ob_get_clean();

	return $out;
}



add_shortcode( 'snax_collections', 'snax_collections_shortcode' );

/**
 * Collections shortcode
 *
 * @param array $atts			Shortcode attributes.
 *
 * @return string				Shortcode output.
 */
function snax_collections_shortcode( $atts ) {
	$default_atts = array(
		'title' 				=> '',
		'title_size' 			=> 'h4',
		'title_align' 			=> '',
		'template' 				=> 'tiles',
//		'columns' 				=> 3,
		'type' 					=> 'recent',
		'ids'                   => '',
//		'time_range'			=> 'all',
		'max' 					=> 6,
//		'offset' 				=> '',
//		'category' 				=> '',
//		'post_tag' 				=> '',
//		'post_format'			=> '',
//		'snax_format'			=> '',
//		'author'                => '',

		// Elements visibility.
//		'show_featured_media'	=> 'standard',
//		'show_subtitle'	        => 'standard',
//		'show_shares' 			=> 'standard',
//		'show_votes'			=> 'none',
//		'show_views' 			=> 'standard',
//		'show_downloads'		=> 'standard',
//		'show_comments_link'	=> 'standard',
//		'show_categories' 		=> 'standard',
//		'show_summary' 			=> 'standard',
		'show_author' 			=> 'standard',
//		'show_avatar' 			=> 'standard',
//		'show_date' 			=> 'standard',
//		'show_voting_box'		=> 'none',
//		'show_call_to_action'   => 'standard',
//		'show_action_links'     => 'standard',

	);

	$atts = shortcode_atts( $default_atts, $atts, 'snax_collections' );


	// Query args.
	// ----------

	// Common.
//	$query_args = array(
//		'post_type'           	=> 'post',
//		'post_status'         	=> 'publish',
//		'ignore_sticky_posts' 	=> true,
//		'posts_per_page'		=> $atts['max'],
//		'snax_format'			=> $atts['snax_format'],
//	);


	// Loop posts.
	// -----------

	$query_args = array(
		'post_type'         => snax_get_collection_post_type(),
		'post_status'       => 'publish',
		'posts_per_page'	=> $atts['max'],
		'meta_query' => array(
			'relation'      => 'AND',
			array(
				'key'       => '_snax_visibility',
				'value'     => 'public',
				'compare'   => '=',
			),
			array(
				'key'       => '_snax_user_custom',
				'compare'   => 'EXISTS',
			)
		),
	);


	$ids = explode( ',', $atts['ids'] );
	$ids = array_map( 'trim', $ids );
	$ids = array_filter( $ids );

	$final_ids = array();

	// Normalize. Get ids by slugs, if any.
	foreach( $ids as $value ) {
		if ( ! is_int( $value ) ) {
			$collection = get_page_by_path( $value, OBJECT, snax_get_collection_post_type() );
			if ( $collection ) {
				$final_ids[] = $collection->ID;
			}
		} else {
			$final_ids[] = $value;
		}
	}

	if ( count( $final_ids ) ) {
		$query_args['post__in'] = $final_ids;
		$query_args['orderby'] = 'post__in';
	}

	$query = new WP_Query( $query_args );
	set_query_var( 'snax_collections_query', $query );

	// Automatic title based on the "type" argument.
	if ( ! strlen( $atts['title'] ) ) {
		switch ( $atts['type'] ) {
			default:
				$atts['title'] = __( 'Recent Collections', 'snax' );
				break;
		}
	}

	set_query_var( 'snax_collections_elements', array(
		'author' => 'none' === $atts['show_author'] ? false : $atts['show_author'],
	) );

	set_query_var( 'snax_collections_title', $atts['title'] );
	set_query_var( 'snax_collections_title_size', $atts['title_size'] );
	set_query_var( 'snax_collections_title_align', $atts['title_align'] );

	ob_start();
	snax_get_template_part( 'collections/templates/' . $atts['template'] );
	$out = ob_get_clean();

	wp_reset_postdata();

	return $out;
}
