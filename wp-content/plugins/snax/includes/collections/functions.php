<?php
/**
 * Snax Collections Common Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'init',                                     'snax_register_collection_post_type' );
add_action( 'plugins_loaded',                           'snax_install_collections_schema' );
add_action( 'wp_enqueue_scripts',                       'snax_collection_enqueue_scripts' );
add_action( 'deleted_post',                             'snax_delete_collection_items', 10, 1 );
add_action( 'the_content',                              'snax_render_collection_intro', 1, 1 ); // Hook as high as possible, before other plugin add their content.
add_action( 'the_content',                              'snax_render_collection_content', 10, 1 );
add_filter( 'the_posts',                                'snax_collection_pagination', 10, 2 );
add_filter( 'post_type_link',                           'snax_collection_pagination_link', 10, 2 );
add_filter( 'display_post_states',                      'snax_display_collection_type_on_list', 10, 2 );

// Post actions.
add_filter( 'snax_entry_action_links_args',             'snax_add_collection_action_links' );

// User's collection featured image (automatic).
add_action( 'snax_post_added_to_collection',            'snax_set_collection_featured_media', 10, 2 );
add_action( 'snax_before_post_removed_from_collection', 'snax_update_collection_featured_media', 10, 2 );
add_action( 'added_post_meta',                          'snax_is_collection_featured_media_changed', 10, 4 );
add_action( 'updated_post_meta',                        'snax_is_collection_featured_media_changed', 10, 4 );
add_action( 'deleted_post_meta',                        'snax_is_collection_featured_media_deleted', 10, 4 );

// Private collection.
add_filter( 'private_title_format',                     'snax_collection_private_title_format', 10, 2 );

// Menu endpoints.
add_filter( 'snax_menu_item_obj',                       'snax_collection_menu_item_obj', 10, 2 );

// Collection archive.
add_filter( 'pre_get_posts',                            'snax_collection_archive_query' );

/**
 * Return collection post type name
 *
 * @return string
 */
function snax_get_collection_post_type() {
	return 'snax_collection';
}

/**
 * Register post type for a single "Collection"
 */
function snax_register_collection_post_type() {
	$args = array(
		'labels' => array(
			'name'                  => _x( 'Collections', 'post type general name', 'snax' ),
			'singular_name'         => _x( 'Collection', 'post type singular name', 'snax' ),
			'menu_name'             => _x( 'Collections', 'admin menu', 'snax' ),
			'name_admin_bar'        => _x( 'Collection', 'add new on admin bar', 'snax' ),
			'add_new'               => _x( 'Add New', 'poll item', 'snax' ),
			'add_new_item'          => __( 'Add New Collection', 'snax' ),
			'new_item'              => __( 'New Collection', 'snax' ),
			'edit_item'             => __( 'Edit Collection', 'snax' ),
			'view_item'             => __( 'View Collection', 'snax' ),
			'all_items'             => __( 'All Collections', 'snax' ),
			'search_items'          => __( 'Search Collections', 'snax' ),
			'parent_item_colon'     => __( 'Parent Collections:', 'snax' ),
			'not_found'             => __( 'No collections found.', 'snax' ),
			'not_found_in_trash'    => __( 'No collections found in Trash.', 'snax' ),
		),
		'public'                    => true,
		// Below values are inherited from the 'public' if not set.
		'exclude_from_search'       => true,       // for readers
		'publicly_queryable'        => true,        // for readers
		'show_ui'                   => true,        // for authors
		// WP Admin > Appearance > Menus > Collections metabox.
		'show_in_nav_menus'         => false,
		// WP Admin > Collections.
		'show_in_menu'              => true,
		'rewrite'            		=> array(
			'slug' => snax_get_url_var( 'collection' ),
			'feeds' 				=> true,
		),
		'has_archive'        => true,
		'supports'                  => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
		),
	);

	register_post_type( snax_get_collection_post_type(), apply_filters( 'snax_collection_post_type_args', $args ) );
}

/**
 * Install collections table
 */
function snax_install_collections_schema() {
	global $wpdb;

	$current_ver    = '1.0';
	$installed_ver  = get_option( 'snax_collections_table_version' );
	$installed_ver  = false;

	// Create table only if needed.
	if ( $installed_ver !== $current_ver ) {
		$table_name      = $wpdb->prefix . Snax_Collection::get_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		collection_id bigint(20) unsigned NOT NULL,
		post_id bigint(20) unsigned NOT NULL,
		post_order int(11) NOT NULL DEFAULT '0',
		date datetime NOT NULL default '0000-00-00 00:00:00',
  		date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY (ID),
		KEY collection_id (collection_id)
	) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$res = dbDelta( $sql );

		update_option( 'snax_collections_table_version', $current_ver );
	}
}

/**
 * Load scripts.
 */
function snax_collection_enqueue_scripts() {
	$url = trailingslashit( snax_get_assets_url() );
	$ver = snax_get_version();
	$min = defined( 'BTP_DEV' ) && BTP_DEV ? '' : '.min';

	wp_enqueue_script( 'snax-collections', $url . 'js/collections'. $min .'.js', array( 'jquery' ), $ver, true );

	$config = array(
		'ajax_url'  => admin_url( 'admin-ajax.php' ),
		'home_url'  => home_url(),
		'user_id'   => get_current_user_id(),
		'post_id'   => apply_filters( 'snax_can_be_stored_in_history', is_single() ) ? get_the_ID() : 0,
		'nonce'     => wp_create_nonce( 'snax-collection-add' ),
		'history'   => snax_is_history_collection_activated() ? 'on' : 'off',
		'i18n'      => array(
			'are_you_sure_remove'    => _x( 'Entire collection with all items will be removed. Proceed?', 'Collection action message', 'snax' ),
			'are_you_sure_clear_all' => _x( 'All collection items will be removed. Proceed?', 'Collection action message', 'snax' ),
			'removed'                => _x( 'Collection has been successfully removed', 'Collection action message', 'snax' ),
			'removing_items'         => _x( 'Removing collection items...', 'Collection action message', 'snax' ),
		),
	);

	wp_localize_script( 'snax-collections', 'snax_collections_js_config', wp_json_encode( $config ) );
}

/**
 * Check whether the post is a collection
 *
 * @param WP_Post|int $post     Post object or id.
 *
 * @return bool
 */
function snax_is_collection( $post ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	return snax_get_collection_post_type() === get_post_type( $post );
}

/**
 * Return public collection type name
 *
 * @return string
 */
function snax_get_collection_visibility_public() {
	return apply_filters( 'snax_collection_visibility_public', 'public' );
}

/**
 * Return private collection type name
 *
 * @return string
 */
function snax_get_collection_visibility_private() {
	return apply_filters( 'snax_collection_visibility_private', 'private' );
}

/**
 * Delete all collection items
 *
 * @param int $post_id      Collection id.
 *
 * @return bool
 */
function snax_delete_collection_items( $post_id ) {
	if ( snax_is_collection( $post_id ) ) {
		$collection = snax_get_collection_by_id( $post_id );

		if ( $collection ) {
			return $collection->remove_all_posts();
		}
	}

	return false;
}

/**
 * Render collection intro text, if empty.
 *
 * @param string $content       Content.
 *
 * @return string
 */
function snax_render_collection_intro( $content ) {
    if ( is_user_logged_in() ) {
        return  $content;
    }

	if ( empty( $content ) && snax_is_abstract_collection()  ) {
		$content .= do_shortcode( snax_get_collection_intro_shortcode() );
	}

	return $content;
}

/**
 * Append collection items to collection post content
 *
 * @param string $content       Post content.
 *
 * @return string
 */
function snax_render_collection_content( $content ) {
	if ( is_singular( snax_get_collection_post_type() ) ) {
		ob_start();
		snax_get_template_part( 'collections/content-collection' );
		$content .= ob_get_clean();
	}

	return $content;
}

/**
 * Return maximum number of posts that can be displayed on a single page
 *
 * @return int
 */
function snax_get_collections_posts_per_page() {
	return (int) apply_filters( 'snax_collections_posts_per_page', get_option( 'snax_collections_posts_per_page', 10 ) );
}


/**
 * Generate collection pagination using built-in WP page links
 *
 * @param array    $posts           Array of posts.
 * @param WP_Query $wp_query        WP Query.
 *
 * @return array
 */
function snax_collection_pagination( $posts, $wp_query ) {
	/**
	 * Check if query is an instance of WP_Query.
	 * Some plugins, like BuddyPress may change it.
	 */
	if ( ! ( $wp_query instanceof WP_Query ) ) {
		return $posts;
	}

	// Apply only for the_content on a single post.
	if ( ! ( $wp_query->is_main_query() && $wp_query->is_singular() ) ) {
		return $posts;
	}

	foreach ( $posts as $post ) {
		if ( ! snax_is_collection( $post ) ) {
			continue;
		}

		$collection = Snax_Collection::get_by_id( $post->ID );

		$posts_per_page = snax_get_collections_posts_per_page();
		$all_posts = $collection->count_posts();

		$pages = ceil( $all_posts / $posts_per_page );

		if ( $pages < 2 ) {
			continue;
		}

		// WP skips <!--nextpage--> quick tag if it's placed at the beginning of a post.
		// So if post content is empty we need to add one extra quick tag as a workaround.
		if ( empty( $post->post_content ) ) {
			$post->post_content .= '<!--nextpage-->';
		}

		// The <!--nextpage--> tag is a divider between two pages. Number of dividers = pages - 1.
		$post->post_content .= str_repeat( '<!--nextpage-->', $pages - 1 );
	}

	return $posts;
}

/**
 * @param $link
 * @param $post
 *
 * @return string|void
 */
function snax_collection_pagination_link( $link, $post ) {
	global $bimber_requested_collection_slug;

	// Are we on an abstract collection?
	if ( empty( $bimber_requested_collection_slug ) ) {
		return $link;
	}

	// Is a valid collection?
	if ( ! snax_is_collection( $post ) ) {
		return $link;
	}

	$page = get_page_by_path( $bimber_requested_collection_slug, OBJECT, snax_get_collection_post_type() );

	if ( $page ) {
		// Prevents loop.
		remove_filter( 'post_type_link',       'snax_collection_pagination_link', 10 );

		$link = get_permalink( $page->ID );

		// Restore filter.
		add_filter( 'post_type_link',       'snax_collection_pagination_link', 10, 2 );
	}

	return $link;
}

/**
 * Display collection type on the CPT list view
 *
 * @param array   $post_states      States.
 * @param WP_Post $post             Post object.
 *
 * @return array
 */
function snax_display_collection_type_on_list( $post_states, $post ) {
	$screen = get_current_screen();

	if ( $screen && 'edit-snax_collection' === $screen->id ) {
		if ( snax_is_custom_collection( $post ) ) {
			$post_states[] = esc_html_x( 'Custom', 'Collection type', 'snax' );
		}

		if ( snax_is_abstract_collection( $post ) ) {
			$post_states[] = esc_html_x( 'Predefined', 'Collection type', 'snax' );
		}
	}

	return $post_states;
}

/**
 * Return url to user's collections page
 *
 * @param string $default       Default value.
 *
 * @return string
 */
function snax_get_my_collections_url( $default = '' ) {
	return apply_filters( 'snax_my_collections_url', $default );
}

/**
 * Add collections related links
 *
 * @param array $args       Links configuration.
 *
 * @return array
 */
function snax_add_collection_action_links( $args ) {
	$allowed_post_types = apply_filters( 'snax_collection_action_links_allowed_post_types', array( 'post' ) );

	$allowed = in_array( get_post_type(), $allowed_post_types );

	if ( ! $allowed ) {
		return $args;
	}

	$configs = snax_get_abstract_collections();

	foreach( $configs as $collection_slug => $collection_config ) {
		if ( ! snax_is_abstract_collection_activated( $collection_slug ) ) {
			continue;
		}

		if ( 'auto' === $collection_config['add_criteria'] ) {
			continue;
		}

		$link_id = sprintf( 'snax_collection_%s', $collection_slug );

		$args['links'][ $link_id ] = snax_render_add_to_collection_button(
			$collection_slug,
			$collection_config['add_to_label'],
			snax_get_abstract_collection_url( $collection_slug )
		);
	}

	// Custom collection.
	if ( snax_is_custom_collection_activated() ) {
		$custom_config  = snax_get_custom_collection_config();
		$custom_slug    = $custom_config['slug'];

		$args['links'][ 'snax_collection_' . $custom_slug ] = snax_render_add_to_collection_button(
			$custom_slug,
			$custom_config['add_to_label'],
			snax_get_custom_collection_url(),
			array(
				'snax-action'
			)
		);
	}

	return $args;
}

/**
 * Set collection's featured media
 *
 * @param int $post_id              Added post id.
 * @param int $collection_id        Collection id.
 */
function snax_set_collection_featured_media( $post_id, $collection_id ) {
	if ( snax_is_user_custom_collection( $collection_id ) && ! has_post_thumbnail( $collection_id ) ) {
		$post_thumbnail_id = (int) get_post_thumbnail_id( $post_id );

		if ( $post_thumbnail_id > 0 ) {
			set_post_thumbnail( $collection_id, $post_thumbnail_id );
			update_post_meta( $collection_id, '_snax_featured_image_from_post', $post_id );
		}
	}
}

/**
 * Update collection's featured media
 *
 * @param int $post_id              Id of the post that will be removed.
 * @param int $collection_id        Collection id.
 */
function snax_update_collection_featured_media( $post_id, $collection_id ) {
	// Removing post that the collection's featured image is based on?
	$image_origin_post_id = (int) get_post_meta( $collection_id, '_snax_featured_image_from_post', true );

	if ( $image_origin_post_id > 0 && $post_id === $image_origin_post_id ) {
		delete_post_thumbnail( $collection_id );
		delete_post_meta( $collection_id, '_snax_featured_image_from_post' );
	}
}

/**
 * Check if collection's featured media requires change
 *
 * @param int    $meta_id       Meta id.
 * @param int    $post_id       Post id.
 * @param string $meta_key      Meta key.
 * @param string $meta_value    New meta value.
 */
function snax_is_collection_featured_media_changed( $meta_id, $post_id, $meta_key, $meta_value ) {
	if( $meta_key === '_thumbnail_id' ) {
		// If the featured image for a post has changed, we have to update all collections' featured images that use it.
		$posts = get_posts( array(
			'post_type' => snax_get_collection_post_type(),
			'meta_key' => '_snax_featured_image_from_post',
			'meta_value' => $post_id,
			'posts_per_page' => 1,
		) );

		if ( ! empty( $posts ) ) {
			$collection_id = $posts[0]->ID;

			if ( snax_is_user_custom_collection( $collection_id ) ) {
				set_post_thumbnail( $collection_id, $meta_value );
			}
		}
	}
}

/**
 * Check if collection's featured media requires change
 *
 * @param array  $deleted_meta_ids                  Meta ids.
 * @param int    $post_id                           Post id.
 * @param string $meta_key                          Meta key.
 * @param array  $only_delete_these_meta_values     Meta values.
 */
function snax_is_collection_featured_media_deleted ( $deleted_meta_ids, $post_id, $meta_key, $only_delete_these_meta_values ) {
	if( $meta_key === '_thumbnail_id') {
		// Find collection that uses featured image from that post.
		$posts = get_posts( array(
			'post_type' => snax_get_collection_post_type(),
			'meta_key' => '_snax_featured_image_from_post',
			'meta_value' => $post_id,
			'posts_per_page' => 1,
		) );

		if ( ! empty( $posts ) ) {
			$collection_id = $posts[0]->ID;

			if ( snax_is_user_custom_collection( $collection_id ) ) {
				delete_post_thumbnail( $collection_id );
			}
		}
	}
}

/**
 * Post title format
 *
 * @param string  $format   Current format.
 * @param WP_Post $post     Post object.
 *
 * @return string
 */
function snax_collection_private_title_format( $format, $post ) {
	if ( snax_is_collection( $post ) ) {
		$format = '%s';
	}

	return $format;
}

function snax_collection_menu_item_obj( $menu_item, $menu_item_id ) {
	// My Collections.
	if ( 'my-collections' === $menu_item_id ) {
		if ( is_user_logged_in() ) {
			$menu_item->url = snax_get_my_collections_url( $menu_item->url );
		} else {
			$menu_item->url = snax_get_custom_collection_url();
		}
	}

	// Abstract collections.
	if ( 0 === strpos( $menu_item_id, 'abstract_' ) ) {
		$collection_slug = str_replace( 'abstract_', '', $menu_item_id );

		remove_filter( 'post_type_link', 'snax_collection_pagination_link', 10 );

		$collection_url = snax_get_abstract_collection_url( $collection_slug );

		add_filter( 'post_type_link', 'snax_collection_pagination_link', 10, 2 );

		if ( $collection_url ) {
			$menu_item->url = $collection_url;
		}
	}

	return $menu_item;
}

/**
 * Modify query for the collection archive
 *
 * @param WP_Query $query           Query object.
 */
function snax_collection_archive_query( $query ) {
	if ( is_admin() ) {
		return;
	}

	// Is CPT archive?
	if ( is_post_type_archive( snax_get_collection_post_type() ) && $query->is_main_query() ) {
		// Show only users' public collections.
		$query->set( 'post_status', 'publish');
		$query->set( 'meta_query',
			array(
				array(
					'key' => '_snax_user_custom',
					'compare' => 'EXISTS',
				)
			)
		);
	}
}


/**
 *
 */
function snax_collection_get_slug( $post ) {
	$slug = snax_collection_inherits_from_abstract( $post );

	if ( $slug ) {
		return $slug;
	}

	return 'custom';
}
