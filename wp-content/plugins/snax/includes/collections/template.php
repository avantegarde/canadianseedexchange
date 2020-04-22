<?php
/**
 * Snax Entry Template Tags
 *
 * @package snax
 * @subpackage TemplateTags
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Render collection title.
 *
 * @param string $before Before title.
 * @param string $after Before title.

 */
function snax_render_collection_title( $before = '<h3 class="snax-collection-title"><a href="%1s">', $after = '</a></h3>' ) {
	$before = sprintf( $before, esc_url( apply_filters( 'the_permalink', get_permalink() ) ) );

	the_title( $before, $after );
}

/**
 * Render author information for collection
 *
 * @param array $args       Extra arguments.
 * @param boolean $force  Always display.
 */
function snax_render_collection_author( $args = array() ) {
	echo wp_kses_post( snax_capture_collection_author( $args ) );
}

/**
 * Capture author information for collection
 *
 * @param array $args           Extra arguments.
 *
 * @return string
 */
function snax_capture_collection_author( $args = array() ) {
	$out = '';

	$args = wp_parse_args( $args, array(
		'avatar'        => true,
		'avatar_size'   => 24,
		'class'         => '',
	) );

	$final_class = array(
		'snax-collection-author',
	);

	$final_class = array_merge( $final_class, explode( ' ', $args['class'] ) );

	$out .= '<span class="' . implode( ' ', array_map( 'sanitize_html_class', $final_class ) ) . '" itemscope="" itemprop="author" itemtype="http://schema.org/Person">';
	$out .= '<span class="snax-collection-author-label">' . __( 'by', 'snax' ) . ' </span>';
	$out .= sprintf(
		'<a href="%s" title="%s" rel="author">',
		snax_get_item_author_url(),
		sprintf( __( 'Posts by %s', 'snax' ), get_the_author() )
	);

	if ( $args['avatar'] ) {
		$out .= get_avatar( get_the_author_meta( 'email' ), $args['avatar_size'] );
		$out .= ' ';
	}

	$out .= '<strong itemprop="name">' . get_the_author() . '</strong>';
	$out .= '</a>';
	$out .= '</span>';

	return $out;
}



function snax_render_collection_item_count() {
	$out = '';
	$out .= '<span class="snax-collection-item-count">';
	$out .= sprintf(
				str_replace( '%d', '<strong>%d</strong>', esc_html(_n( '%d entry', '%d entries', snax_get_collection_item_count( get_the_ID() ), 'snax' ) ) ),
				snax_get_collection_item_count( get_the_ID() )
			);
	$out .= '</span>';

	echo $out;
}

function snax_render_collection_update() {
	$out = '';
	$out .= '<span class="snax-entry-update">';
	$out .= sprintf(
				__( 'last updated on %s', 'snax' ),
				get_the_date()
			);
	$out .= '</span>';

	echo $out;
}

function snax_render_collection_visibility() {
	$out = '';

	$mapping = array(
		'public'    => _x( 'Public', 'collection visibility', 'snax' ),
		'private'   => _x( 'Private', 'collection visibility', 'snax' ),
	);

	$visibility = get_post_meta( get_the_ID(), '_snax_visibility', true );
	$visibility = isset( $mapping[ $visibility ] ) ? $mapping[ $visibility ] : 'private';

	$out .= '<span class="snax-collection-visibility">';
	$out .= esc_html( $visibility );
	$out .= '</span>';

	echo $out;
}






/**
 * Capture author information for entry
 * 
 * @param array $args       Extra arguments.
 * @param boolean $force  Always display.
 */
function snax_render_entry_author( $args = array() ) {
	echo wp_kses_post( snax_capture_entry_author( $args ) );
}

/**
 * Capture author information for entry
 *
 * @param array $args           Extra arguments.
 *
 * @return string
 */
function snax_capture_entry_author( $args = array() ) {
	$out = '';

	$args = wp_parse_args( $args, array(
		'avatar'        => true,
		'avatar_size'   => 24,
		'class'         => '',
	) );

	$final_class = array(
		'snax-entry-author',
	);

	$final_class = array_merge( $final_class, explode( ' ', $args['class'] ) );


	$out .= '<span class="' . implode( ' ', array_map( 'sanitize_html_class', $final_class ) ) . '" itemscope="" itemprop="author" itemtype="http://schema.org/Person">';
	$out .= '<span class="snax-entry-author-label">' . __( 'by', 'snax' ) . ' </span>';
	$out .= sprintf(
		'<a href="%s" title="%s" rel="author">',
		snax_get_item_author_url(),
		sprintf( __( 'Posts by %s', 'snax' ), get_the_author() )
	);

	if ( $args['avatar'] ) {
		$out .= get_avatar( get_the_author_meta( 'email' ), $args['avatar_size'] );
		$out .= ' ';
	}

	$out .= '<strong itemprop="name">' . get_the_author() . '</strong>';
	$out .= '</a>';
	$out .= '</span>';

	return $out;
}


/**
 * Render date information for the current post.
 *
 * @param array $args Arguments.
 */
function snax_render_entry_date( $args = array() ) {
	$defaults = array(
		'use_timeago'   => false,
		'class'         => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$date = get_the_time( get_option( 'date_format' ) );
	$time = get_the_time( get_option( 'time_format' ) );
	$sep  = $time ? apply_filters( 'snax_entry_date_time_separator', ', ' ) : '';

	if ( $args['use_timeago'] ) {
		$html = sprintf(
			_x( '%s ago', '%s = human-readable time difference', 'snax' ),
			human_time_diff( get_the_date( 'U' ) ),
			current_time( 'timestamp' )
		);
	} else {
		$html = $date . $sep . $time;
	}

	$final_class = array(
		'snax-entry-date',
	);
	$final_class = array_merge( $final_class, explode( ' ', $args['class'] ) );

	$html = sprintf(
		'<time class="' . implode( ' ', array_map( 'sanitize_html_class', $final_class ) ) . '" datetime="%1$s">%2$s</time>',
		esc_attr( get_the_time( 'Y-m-d' ) . 'T' . get_the_time( 'H:i:s' ) ),
		esc_html( $html )
	);

	echo apply_filters( 'snax_entry_date_html', $html, $args );
}

/**
 * Output action links for collection items
 *
 * @param int   $collection_id      Collection id.
 * @param array $args               Arguments.
 */
function snax_render_collection_item_action_links( $collecton_id = 0, $args = array() ) {
	$links = snax_capture_collection_item_action_links( $collecton_id, $args );

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
function snax_capture_collection_item_action_links( $collection_id = 0, $args = array() ) {
	$collection = get_post( $collection_id );

	$args = wp_parse_args( $args, array(
		'collection_id' => 0,
		'before'        => '<div class="snax-actions"><button class="snax-button-none snax-actions-toggle">' . esc_html__( 'More', 'snax' ) . '</button><ul class="snax-action-links"><li>',
		'after'         => '</li></ul></div>',
		'sep'           => '</li><li>',
		'links'         => array(),
	) );

	$args = apply_filters( 'snax_collection_item_action_links_args', $args );

	if ( empty( $args['links'] ) ) {
		if ( snax_user_is_collection_owner( null, $collection->ID ) ) {
			$args['links'] = apply_filters( 'snax_get_collection_item_action_links', array(
				'delete'      => sprintf( '<button type="button" class="snax-button-none snax-collection-action-remove-post" data-snax-post="%d" data-snax-nonce="%s">%s</button>', get_the_ID(), wp_create_nonce( 'snax-collection-delete' ), esc_html__( 'Remove from Collection', 'snax' )),
			) );
		}
	}

	// Prepare output.
	$out   = '';
	$links = implode( $args['sep'], array_filter( $args['links'] ) );

	if ( strlen( $links ) ) {
		$out = $args['before'] . $links . $args['after'];
	}

	return apply_filters( 'snax_capture_collection_item_action_links', $out, $args );
}

/**
 * Return link to an action
 *
 * @param string $slug              Action slug.
 * @param string $label             Action label.
 * @param string $url               Collection url.
 * @param array  $extra_classes     Extra CSS classes.
 *
 * @return string
 */
function snax_render_add_to_collection_button( $slug, $label, $url, $extra_classes = array() ) {
	$classes = array(
		'snax-action',
		'snax-action-add-to-collection',
		'snax-action-add-to-collection-' . $slug,
	);

	$classes = array_merge( $classes, $extra_classes );
	$class   = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
	$post_id = get_the_ID();
	$nonce   = wp_create_nonce( 'snax-collection-add' );

	return sprintf(
		'<button class="%s" type="button" data-snax-collection="%s" data-snax-post="%d" data-snax-nonce="%s" data-snax-redirect="%s">%s</button>',
		$class,
		esc_attr( $slug ),
		absint( $post_id ),
		esc_attr( $nonce ),
		esc_url( $url ),
		esc_html( $label )
	);
}
