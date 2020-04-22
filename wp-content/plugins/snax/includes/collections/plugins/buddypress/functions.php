<?php
/**
 * Plugin Integration Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'bp_core_admin_get_components',	'snax_bp_register_collections_component', 11, 2 );
add_action( 'bp_include',                   'snax_bp_load_collections_component', 11 );
add_filter( 'snax_bp_activate_components',  'snax_bp_activate_collections_component', 11, 1 );
add_filter( 'snax_my_collections_url',      'snax_bp_my_collections_url', 10, 1 );

/**
 * Return collections component unique id
 *
 * @return string
 */
function snax_collections_bp_component_id() {
	return snax_get_url_var( 'collections' );
}

/**
 * Return the user public collections slug
 *
 * @param string $default Default value.
 *
 * @return string
 */
function snax_get_user_public_collections_slug( $default = 'public' ) {
	return apply_filters( 'snax_get_user_public_collections_slug', $default );
}

/**
 * Return the user private collections slug
 *
 * @param string $default Default value.
 *
 * @return string
 */
function snax_get_user_private_collections_slug( $default = 'private' ) {
	return apply_filters( 'snax_get_user_private_collections_slug', $default );
}

/**
 * Register BP component
 *
 * @param array  $components        Registered components.
 * @param string $type              Component type.
 *
 * @return array
 */
function snax_bp_register_collections_component( $components, $type ) {
	if ( in_array( $type, array( 'all', 'optional' ), true ) ) {

		$components[ 'snax_collections' ] = array(
			'title'       => _x( 'Snax Collections', 'BuddyPress compoment title', 'snax' ),
			'description' => _x( 'Allow your users to manage their collections directly from within their profiles.', 'BuddyPress component description', 'snax' ),
		);
	}

	return $components;
}

/**
 * Load BP component
 */
function snax_bp_load_collections_component() {
	if ( bp_is_active( 'snax_collections' ) ) {
		require_once trailingslashit( dirname( __FILE__ ) ) . 'components/collections.php';

		$component = new Snax_Collections_BP_Component();

		snax()->plugins->buddypress->snax_collections = $component;

		// Register our custom componentns references into BP to enable BP notifications built-in system.
		// BP checkes active notifications components and only in this way we can inject our components into it.
		buddypress()->snax_collections = $component;
	}
}

/**
 * Activate the Collections component inside BP on first load
 *
 * @param array $bp_active_components       BP components.
 *
 * @return array
 */
function snax_bp_activate_collections_component( $bp_active_components ) {
	$bp_active_components[ 'snax_collections' ] = 1;

	return $bp_active_components;
}

/** Custom query ********************************************************/

/**
 * Check whether user has public collections
 *
 * @param int $user_id          User id.
 *
 * @return bool
 */
function snax_has_user_public_collections( $user_id = 0 ) {
	$has = snax_has_user_collections( snax_get_collection_visibility_public(), $user_id );

	return apply_filters( 'snax_has_user_public_collections', $has, $user_id );
}

/**
 * Check whether user has private collections
 *
 * @param int $user_id User id.
 *
 * @return bool
 */
function snax_has_user_private_collections( $user_id = 0 ) {
	$has = snax_has_user_collections( snax_get_collection_visibility_private(), $user_id );

	return apply_filters( 'snax_has_user_private_collections', $has, $user_id );
}

/**
 * Check whether user has collections
 *
 * @param string $visibility    Collection visibility.
 * @param int    $user_id       User id.
 *
 * @return bool
 */
function snax_has_user_collections( $visibility, $user_id = 0 ) {
	$user_id = (int) $user_id;

	// If not set, try to get current.
	if ( 0 === $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( empty( $user_id ) ) {
		return false;
	}

	$query = snax_get_collections_query( array(
		'author'        => $user_id,
		'visibility'    => $visibility,
	) );

	return apply_filters( 'snax_has_user_collections', $query->have_posts(), $user_id );
}

/**
 * Set up collections query
 *
 * @param array $args           WP Query args.
 *
 * @return WP_Query
 */
function snax_get_collections_query( $args = array() ) {
	global $wp_rewrite;

	$visibility = isset( $args['visibility'] ) ? $args['visibility'] : snax_get_collection_visibility_private();

	$r = array(
		'post_type'      => snax_get_collection_post_type(),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'       => '_snax_user_custom',
				'compare'   => 'EXISTS',
			),
			array(
				'key'       => '_snax_visibility',
				'value'     => $visibility,
				'compare'   => '=',
			)
		),
		'posts_per_page' => apply_filters( 'snax_bp_collections_per_page', 10 ),
		'paged'          => snax_get_paged(),
		'max_num_pages'  => false,
	);

	$r = wp_parse_args( $args, $r );

	// Make query.
	$query = new WP_Query( $r );

	// Limited the number of pages shown.
	if ( ! empty( $r['max_num_pages'] ) ) {
		$query->max_num_pages = $r['max_num_pages'];
	}

	// If no limit to posts per page, set it to the current post_count.
	if ( - 1 === $r['posts_per_page'] ) {
		$r['posts_per_page'] = $query->post_count;
	}

	// Add pagination values to query object.
	$query->posts_per_page = $r['posts_per_page'];
	$query->paged          = $r['paged'];

	// Only add pagination if query returned results.
	if ( ( (int) $query->post_count || (int) $query->found_posts ) && (int) $query->posts_per_page ) {

		// Limit the number of topics shown based on maximum allowed pages.
		if ( ( ! empty( $r['max_num_pages'] ) ) && $query->found_posts > $query->max_num_pages * $query->post_count ) {
			$query->found_posts = $query->max_num_pages * $query->post_count;
		}

		$base = add_query_arg( 'paged', '%#%' );

		$base = apply_filters( 'snax_collections_pagination_base', $base, $r );

		// Pagination settings with filter.
		$pagination = apply_filters( 'snax_collections_pagination', array(
			'base'      => $base,
			'format'    => '',
			'total'     => $r['posts_per_page'] === $query->found_posts ? 1 : ceil( (int) $query->found_posts / (int) $r['posts_per_page'] ),
			'current'   => (int) $query->paged,
			'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
			'next_text' => is_rtl() ? '&larr;' : '&rarr;',
			'mid_size'  => 1,
		) );

		// Add pagination to query object.
		$query->pagination_links = paginate_links( $pagination );

		// Remove first page from pagination.
		$query->pagination_links = str_replace( $wp_rewrite->pagination_base . "/1/'", "'", $query->pagination_links );
	}

	snax()->collections_query = $query;

	return $query;
}

/**
 * Whether there are more collections available in the loop
 *
 * @return bool
 */
function snax_collections() {

	$have_posts = snax()->collections_query->have_posts();

	// Reset the post data when finished.
	if ( empty( $have_posts ) ) {
		wp_reset_postdata();
	}

	return $have_posts;
}

/**
 * Loads up the current collection in the loop
 */
function snax_the_collection() {
	snax()->collections_query->the_post();
}

/**
 * Output the pagination count
 */
function snax_collections_pagination_count() {
	echo esc_html( snax_get_collections_pagination_count() );
}

/**
 * Return the pagination count
 *
 * @return string
 */
function snax_get_collections_pagination_count() {
	$query = snax()->collections_query;

	if ( empty( $query ) ) {
		return false;
	}

	// Set pagination values.
	$start_num = intval( ( $query->paged - 1 ) * $query->posts_per_page ) + 1;
	$from_num  = snax_number_format( $start_num );
	$to_num    = snax_number_format( ( $start_num + ( $query->posts_per_page - 1 ) > $query->found_posts ) ? $query->found_posts : $start_num + ( $query->posts_per_page - 1 ) );
	$total_int = (int) ! empty( $query->found_posts ) ? $query->found_posts : $query->post_count;
	$total     = snax_number_format( $total_int );

	// Several topics in a forum with a single page.
	if ( empty( $to_num ) ) {
		$retstr = sprintf( _n( 'Viewing %1$s collection', 'Viewing %1$s collections', $total_int, 'snax' ), $total );

		// Several topics in a forum with several pages.
	} else {
		$retstr = sprintf( _n( 'Viewing collection %2$s (of %4$s total)', 'Viewing %1$s collections - %2$s through %3$s (of %4$s total)', $total_int, 'snax' ), $query->post_count, $from_num, $to_num, $total );
	}

	// Filter and return.
	return apply_filters( 'snax_get_collections_pagination_count', esc_html( $retstr ) );
}

/**
 * Output pagination links
 */
function snax_collections_pagination_links() {
	echo wp_kses_post( snax_get_collections_pagination_links() );
}

/**
 * Return pagination links
 *
 * @return string
 */
function snax_get_collections_pagination_links() {
	$query = snax()->collections_query;

	if ( empty( $query ) ) {
		return false;
	}

	return apply_filters( 'snax_get_collections_pagination_links', $query->pagination_links );
}

/** Screens ********************************************************/


/**
 * Hook "Public Collections" template into plugins template
 */
function snax_member_screen_public_collections() {
	add_action( 'bp_template_content', 'snax_member_public_collections_content' );
	bp_core_load_template( apply_filters( 'snax_member_screen_public_collections', 'members/single/plugins' ) );
}

/**
 * Hook "Private Collections" template into plugins template
 */
function snax_member_screen_private_collections() {
	add_action( 'bp_template_content', 'snax_member_private_collections_content' );
	bp_core_load_template( apply_filters( 'snax_member_screen_private_collections', 'members/single/plugins' ) );
}

/** Templates ******************************************************/


/**
 * Public collections template part
 */
function snax_member_public_collections_content() {
	?>

	<div id="snax-collections snax-public-collections">

		<?php snax_get_template_part( 'buddypress/collections/section-public' ); ?>

	</div>

	<?php
}

/**
 * Private collections template part
 */
function snax_member_private_collections_content() {
	?>

	<div id="snax-collections snax-private-collections">

		<?php snax_get_template_part( 'buddypress/collections/section-private' ); ?>

	</div>

	<?php
}

/**
 * Apply BP Profile > Collection url.
 *
 * @param string $url   Url.
 *
 * @return string       Empty if not set.
 */
function snax_bp_my_collections_url( $url ) {
	if ( bp_is_active( 'snax_collections' ) ) {
		$base_url = bp_core_get_user_domain( get_current_user_id() );
		$url      = $base_url . snax_collections_bp_component_id();
	}

	return $url;
}

function snax_get_user_predefined_collections_query( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$abstract_collection_ids = array_values( snax_get_activated_abstract_collections() );

	$args = array(
		'author'            => $user_id,
		'post_type'         => snax_get_collection_post_type(),
		'post__not_in'      => $abstract_collection_ids,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'       => '_snax_abstract',
				'compare'   => 'EXISTS',
			),
			array(
				'key'       => '_snax_visibility',
				'value'     => 'private',
				'compare'   => '=',
			)
		),
		'posts_per_page' => -1,
	);

	$query = new WP_Query( $args );

	return $query;
}