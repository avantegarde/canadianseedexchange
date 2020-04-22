<?php
/**
 * Common Functions
 *
 * @package whats-your-reaction
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Plugin acitvation
 */
function wyr_activate() {
	wyr_install_votes_schema();
}

/**
 * Plugin deacitvation
 */
function wyr_deactivate() {}

/**
 * Plugin uninstallation
 */
function wyr_uninstall() {}

/**
 * Install table 'wyr_votes'
 */
function wyr_install_votes_schema() {
	global $wpdb;

	$current_ver    = '1.0';
	$installed_ver  = get_option( 'wyr_votes_table_version' );

	// Create table only if needed.
	if ( $installed_ver !== $current_ver ) {
		$table_name      = $wpdb->prefix . wyr_get_votes_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		vote_id bigint(20) unsigned NOT NULL auto_increment,
		post_id bigint(20) NOT NULL ,
		vote varchar(20) NOT NULL,
		author_id bigint(20) NOT NULL default '0',
  		author_ip varchar(100) NOT NULL default '',
		author_host varchar(200) NOT NULL,
		date datetime NOT NULL default '0000-00-00 00:00:00',
  		date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY (vote_id),
		KEY post_id (post_id)
	) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option( 'wyr_votes_table_version', $current_ver );
	}
}

/**
 * Load stylesheets.
 */
function wyr_enqueue_styles() {
	$ver = wyr_get_plugin_version();
	$url = trailingslashit( wyr_get_plugin_url() ) . 'css/';
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_style( 'wyr-main', $url . "main{$min}.css", array(), $ver );
	wp_style_add_data( 'wyr-main', 'rtl', 'replace' );
	wp_style_add_data( 'wyr-main', 'suffix', $min );
	wp_enqueue_style( 'wyr-main' );
}

function wyr_get_capability() {
	$capability = is_multisite() ? 'manage_network_options' : 'manage_options';

	return apply_filters( 'wyr_capability', $capability );
}

/**
 * Return the correct admin URL based on WordPress configuration.
 *
 * @param string $path Optional. The sub-path under /wp-admin to be appended to the admin URL.
 *
 * @param string $scheme The scheme to use. Default is 'admin', which
 *                       obeys {@link force_ssl_admin()} and {@link is_ssl()}. 'http'
 *                       or 'https' can be passed to force those schemes.
 *
 * @return string        Admin url link with optional path appended.
 */
function wyr_admin_url( $path = '', $scheme = 'admin' ) {
	// Links belong in network admin.
	if ( is_network_admin() ) {
		$url = network_admin_url( $path, $scheme );

		// Links belong in site admin.
	} else {
		$url = admin_url( $path, $scheme );
	}

	return $url;
}

/**
 * Load javascripts.
 */
function wyr_enqueue_scripts() {
	$ver = wyr_get_plugin_version();

	wp_enqueue_script( 'wyr-front', wyr_get_plugin_url() . 'js/front.js', array( 'jquery' ), $ver, true );

	global $wp_locale;

	// Defaults.
	$decimals      = 0;
	$dec_point     = '.';
	$thousands_sep = ',';

	if ( isset( $wp_locale ) ) {
		$dec_point = $wp_locale->number_format['decimal_point'];
		$thousands_sep = $wp_locale->number_format['thousands_sep'];
	}

	$front_config = array(
		'ajax_url'          => admin_url( 'admin-ajax.php' ),
		'error_msg'         => __( 'Some error occurred while voting. Please try again.', 'wyr' ),
		'number_format'     => array(
			'decimals'      => $decimals,
			'dec_point'     => $dec_point,
			'thousands_sep' => $thousands_sep,
		),
	);

	wp_localize_script( 'wyr-front', 'wyr_front_config', wp_json_encode( $front_config ) );
}

/**
 * Return unique taxonomy name
 *
 * @return string
 */
function wyr_get_taxonomy_name() {
	return 'reaction';
}

/**
 * Register "Reactions" taxonomy
 */
function wyr_register_taxonomy() {
	$labels = array(
		'name' 				=> _x( 'Reactions', 'taxonomy general name', 'wyr' ),
		'singular_name' 	=> _x( 'Reaction', 'taxonomy singular name', 'wyr' ),
		'search_items' 		=> __( 'Search Reactions', 'wyr' ),
		'all_items' 		=> __( 'All Reactions', 'wyr' ),
		'parent_item' 		=> __( 'Parent Reaction', 'wyr' ),
		'parent_item_colon' => __( 'Parent Reaction:', 'wyr' ),
		'edit_item' 		=> __( 'Edit Reaction', 'wyr' ),
		'update_item' 		=> __( 'Update Reaction', 'wyr' ),
		'add_new_item' 		=> __( 'Add New Reaction', 'wyr' ),
		'new_item_name' 	=> __( 'New Reaction Name', 'wyr' ),
		'menu_name' 		=> __( 'Reactions', 'wyr' ),
		'view_item'         => __( 'View Reaction', 'wyr' ),
		'not_found'         => __( 'No Reactions Found', 'wy' ),
	);

	$args = array(
		'hierarchical' 		=> false,
		'labels' 			=> $labels,
		'show_ui' 			=> true,
		'show_admin_column' => true,
		'query_var' 		=> true,
		'orderby' 			=> 'slug',
		'show_in_rest'      => true,
		'rewrite' 			=> array(
			'slug' => 'reaction',
		),
	);

	$taxonomy_name = wyr_get_taxonomy_name();
	$supported_post_types = array( 'post' );
	if ( wyr_can_use_plugin( 'snax/snax.php' ) && ! is_network_admin() ){
		$supported_post_types[]= snax_get_quiz_post_type();
	}
	register_taxonomy( $taxonomy_name, $supported_post_types, $args );
}

/**
 * Load plugin translations.
 */
function wyr_load_textdomain() {
	// WordPress loading steps:
	// - try to load from the custom Loco languages directory (Loco adds that path, /wp-content/languages/loco/plugins/)
	// - try to load from the languages directory (/wp-content/languages/plugins/)
	// - then from the plugins directory (/wp-content/plugins/whats-your-reaction/languages/)
	load_plugin_textdomain( 'wyr', false, 'whats-your-reaction/languages' );
}

/**
 * Return list of ordered reactions
 *
 * @return array		List of term objects.
 */
function wyr_get_reactions() {
	$terms = get_terms( array(
		'taxonomy'		=> wyr_get_taxonomy_name(),
		'hide_empty'	=> false,
		'meta_key' 	=> 'order',
		'orderby'	=> 'meta_value_num',
		'order'		=> 'ASC',
	) );

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	// @todo - use WP_Meta_Query
	foreach ( $terms as $id => $term ) {
		if ( 'standard' === get_term_meta( $term->term_id, 'disabled', true ) ) {
			unset( $terms[ $id ] );
		}
	}

	return $terms;
}

/**
 * Return reaction term object by name
 *
 * @param string $name		Reaction name.
 *
 * @return bool|WP_Term
 */
function wyr_get_reaction( $name ) {
	$terms = get_terms( array(
		'taxonomy'		=> wyr_get_taxonomy_name(),
		'hide_empty'	=> false,
		'slug'			=> $name,
	) );

	if ( empty( $terms ) ) {
		return false;
	}

	return $terms[0];
}

/**
 * Return list of ordered post reactions
 *
 * @return array		List of term objects.
 */
function wyr_get_post_reactions( $post = null ) {
	$post = get_post( $post );

	$terms = wp_get_post_terms(
		$post->ID,
		wyr_get_taxonomy_name(),
		array(
			'meta_key' 	=> 'order',
			'orderby'	=> 'meta_value_num',
			'order'		=> 'ASC',
		)
	);

	// @todo - use WP_Meta_Query
	foreach ( $terms as $id => $term ) {
		if ( 'standard' === get_term_meta( $term->term_id, 'disabled', true ) ) {
			unset( $terms[ $id ] );
		}
	}

	return $terms;
}

/**
 * Check whether the reaction exists
 *
 * @param string $name		Type of reaction.
 *
 * @return bool
 */
function wyr_is_valid_reaction( $name ) {
	$terms = get_terms( array(
		'taxonomy'		=> wyr_get_taxonomy_name(),
		'hide_empty'	=> false,
		'slug'			=> $name,
	) );

	return ! empty( $terms );
}

/**
 * Hook into post content
 *
 * @param string $content		Post content.
 *
 * @return string
 */
function wyr_load_post_voting_box( $content ) {
	$load = is_single() && 'publish' === get_post_status( get_the_ID() );

	if ( ! apply_filters( 'wyr_load_post_voting_box', $load ) ) {
		return $content;
	}

	$content .= wyr_get_voting_box();

	return $content;
}

/**
 * Return voting box HTML container
 *
 * @return string
 */
function wyr_get_voting_box() {
	return do_shortcode( '[wyr_voting_box]' );
}

/**
 * Render voting box HTML container
 *
 * @return string
 */
function wyr_render_voting_box() {
	echo wyr_get_voting_box();
}

/**
 * Load a template part into a template
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function wyr_get_template_part( $slug, $name = null ) {
	// Trim off any slashes from the slug.
	$slug = ltrim( $slug, '/' );

	if ( empty( $slug ) ) {
		return;
	}

	$parent_dir_path = trailingslashit( get_template_directory() );
	$child_dir_path  = trailingslashit( get_stylesheet_directory() );

	$files = array(
		$child_dir_path . 'whats-your-reaction/' . $slug . '.php',
		$parent_dir_path . 'whats-your-reaction/' . $slug . '.php',
		wyr_get_plugin_dir() . 'templates/' . $slug . '.php',
	);

	if ( ! empty( $name ) ) {
		array_unshift(
			$files,
			$child_dir_path . 'whats-your-reaction/' . $slug . '-' . $name . '.php',
			$parent_dir_path . 'whats-your-reaction/' . $slug . '-' . $name . '.php',
			wyr_get_plugin_dir() . 'templates/' . $slug . '-' . $name . '.php'
		);
	}

	$located = '';

	foreach ( $files as $file ) {
		if ( empty( $file ) ) {
			continue;
		}

		if ( file_exists( $file ) ) {
			$located = $file;
			break;
		}
	}

	if ( strlen( $located ) ) {
		load_template( $located, false );
	}
}

/**
 * Check whether user has already voted for a post
 *
 * @param string $type 			Vote type.
 * @param int    $post_id 		Post id.
 * @param int    $user_id 		User id.
 *
 * @return mixed		Vote type or false if not exists
 */
function wyr_user_voted( $type, $post_id = 0, $user_id = 0 ) {
	$post = get_post( $post_id );

	if ( 0 === $user_id ) {
		$user_id = get_current_user_id();
	}

	// User not logged in, guest voting disabled.
	if ( 0 === $user_id && ! wyr_guest_voting_is_enabled() ) {
		return false;
	}

	// User not logged in, guest voting enabled.
	if ( 0 === $user_id && wyr_guest_voting_is_enabled() ) {
		$vote_cookie = filter_input( INPUT_COOKIE, 'wyr_vote_' . $type . '_' . $post->ID, FILTER_SANITIZE_STRING );

		return (bool) $vote_cookie;
	}

	// User logged in.
	global $wpdb;
	$votes_table_name = $wpdb->prefix . wyr_get_votes_table_name();

	$vote = $wpdb->get_var(
		$wpdb->prepare(
			"
			SELECT vote
			FROM $votes_table_name
			WHERE post_id = %d AND author_id = %d AND vote = %s
			ORDER BY vote_id DESC
			LIMIT 1",
			$post->ID,
			$user_id,
			$type
		)
	);

	return $vote;
}

/**
 * Return user latest votes
 *
 * @param int $author_id        Author id.
 * @param int $max              Max number of returned votes.
 * @param int $offset           Offset.
 *
 * @return array
 */
function wyr_get_user_latest_votes( $author_id, $max = 5, $offset = 0 ) {
	global $wpdb;
	$votes_table_name = $wpdb->prefix . wyr_get_votes_table_name();

	$reactions = wyr_get_reactions();
	$in_clause = array();

	foreach ( $reactions as $reaction ) {
		$in_clause[] = sprintf( "'%s'", $reaction->slug );
	}

	if ( empty( $in_clause ) ) {
		return array();
	}

	if ( $author_id ) {
		$votes = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT *
			FROM $votes_table_name
			WHERE author_id = %d AND vote IN (" . implode( ',', $in_clause ) . ")
			ORDER BY date DESC
			LIMIT %d
			OFFSET  %d
			",
				$author_id,
				$max,
				$offset
			)
		);
	} else {
		$votes = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT *
			FROM $votes_table_name
			WHERE vote IN (" . implode( ',', $in_clause ) . ")
			ORDER BY date DESC
			LIMIT %d
			OFFSET  %d
			",
				$max,
				$offset
			)
		);
	}

	if ( ! empty( $votes ) ) {
		return $votes;
	}

	return array();
}

/**
 * Check whether guest user can vote
 *
 * @return bool
 */
function wyr_guest_voting_is_enabled() {
	return apply_filters( 'wyr_guest_voting_is_enabled', true );
}

/**
 * Get the table name of the votes table
 *
 * @return string
 */
function wyr_get_votes_table_name() {
	return 'wyr_votes';
}

/**
 * Return votes summary
 *
 * @param int|WP_Post $post_id 			Optional. Post ID or WP_Post object. Default is global `$post`.
 *
 * @return array
 */
function wyr_get_post_votes( $post_id = 0 ) {
	$post = get_post( $post_id );

	$votes = get_post_meta( $post->ID, '_wyr_votes', true );

	return apply_filters( 'wyr_post_votes', $votes, $post );
}

/**
 * Register new vote for a post
 *
 * @param array $vote_arr Vote config.
 *
 * @return bool|WP_Error
 */
function wyr_vote_post( $vote_arr ) {
	$defaults = array(
		'post_id'   => get_the_ID(),
		'author_id' => get_current_user_id(),
		'type'      => '',
	);

	$vote_arr = wp_parse_args( $vote_arr, $defaults );

	global $wpdb;
	$table_name = $wpdb->prefix . wyr_get_votes_table_name();

	$post_date  = current_time( 'mysql' );
	$ip_address = wyr_get_ip_address();
	$host = gethostbyaddr( $ip_address );

	$affected_rows = $wpdb->insert(
		$table_name,
		array(
			'post_id'     => $vote_arr['post_id'],
			'vote'        => $vote_arr['type'],
			'author_id'   => $vote_arr['author_id'],
			'author_ip'   => $ip_address ? $ip_address : '',
			'author_host' => $host ? $host : '',
			'date'        => $post_date,
			'date_gmt'    => get_gmt_from_date( $post_date ),
		),
		array(
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		)
	);

	if ( false === $affected_rows ) {
		return new WP_Error( 'wyr_insert_vote_failed', esc_html__( 'Could not insert new reaction into the database!', 'wyr' ) );
	}

	$meta = wyr_update_votes_metadata( $vote_arr['post_id'] );

	// Assign post to reaction term if reached threshold.
	$reaction_threshold = apply_filters( 'wyr_reaction_threshold', 3 );
	$reaction_type 		= $vote_arr['type'];

	if ( $meta[ $reaction_type ]['count'] >= $reaction_threshold ) {
		$reaction_term = wyr_get_reaction( $reaction_type );

		wp_set_post_terms( $vote_arr['post_id'], array( $reaction_term->term_id ), wyr_get_taxonomy_name(), true );
	}

	do_action( 'wyr_vote_added', $vote_arr, $meta );

	return $meta;
}

/**
 * Return vistor IP address
 *
 * @return string
 */
function wyr_get_ip_address() {
	$http_x_forwarder_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_STRING );
	$remote_addr          = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING );

	if ( empty( $http_x_forwarder_for ) ) {
		$ip_address = $remote_addr;
	} else {
		$ip_address = $http_x_forwarder_for;
	}

	if ( false !== strpos( $ip_address, ',' ) ) {
		$ip_address = explode( ',', $ip_address );
		$ip_address = $ip_address[0];
	}

	return $ip_address;
}

/**
 * Update voting stats
 *
 * @param int   $post_id            Post id.
 * @param array $meta               Current meta value.
 *
 * @return bool
 */
function wyr_update_votes_metadata( $post_id = 0, $meta = array() ) {
	$post = get_post( $post_id );

	if ( empty( $meta ) ) {
		$meta = wyr_generate_votes_metadata( $post );
	}

	if ( empty( $meta ) ) {
		return false;
	}

	foreach ( $meta as $type => $data ) {
		update_post_meta( $post->ID, '_wyr_' . $type . '_count', $data['count'] );
		update_post_meta( $post->ID, '_wyr_' . $type . '_percentage', $data['percentage'] );
	}

	update_post_meta( $post->ID, '_wyr_votes', $meta );

	return $meta;
}

/**
 * Generate voting stats
 *
 * @param int $post_id          Post id.
 *
 * @return array
 */
function wyr_generate_votes_metadata( $post_id = 0 ) {
	$post = get_post( $post_id );

	global $wpdb;
	$votes_table_name = $wpdb->prefix . wyr_get_votes_table_name();

	$votes = $wpdb->get_results(
		$wpdb->prepare(
			"
			SELECT vote, count(vote) AS cnt
			FROM $votes_table_name
			WHERE post_id = %d
			GROUP BY vote",
			$post->ID
		)
	);

	$total_votes = 0;
	$meta = array();

	foreach ( $votes as $group_data ) {
		$type = $group_data->vote;
		$cnt  = $group_data->cnt;

		$meta[ $type ] = array(
			'count' => $cnt,
		);

		$total_votes += $cnt;
	}

	if ( $total_votes > 0 ) {
		// Calculate percentages.
		foreach ( $meta as $type => $data ) {
			$percentage = round( ( 100 * $data['count'] ) / $total_votes );

			$meta[ $type ]['percentage'] = $percentage;
		}
	}

	return apply_filters( 'wyr_votes_metadata', $meta, $post->ID );
}

function wyr_render_reaction_icon( $term_id, $args = array() ) {
	echo wyr_capture_reaction_icon( $term_id, $args );
}

function wyr_capture_reaction_icon( $term_id, $args = array() ) {
	$term = get_term( $term_id, wyr_get_taxonomy_name() );

	if ( ! is_wp_error( $term ) ) {
		$icon_set = get_term_meta( $term->term_id, 'icon_set', true );
		$icon = get_term_meta( $term->term_id, 'icon', true );
		$icon_path = ( 'custom' === $icon_set ) ? wp_get_attachment_url( $icon ) : '';

		$term_args = array(
			'type'             => get_term_meta( $term->term_id, 'icon_type', true ),
			'set'              => $icon_set,
			'icon'             => $icon,
			'text'             => $term->name,
			'path'             => $icon_path,
			'color'            => get_term_meta( $term->term_id, 'icon_color', true ),
			'background_color' => get_term_meta( $term->term_id, 'icon_background_color', true ),
		);

		$defaults = array(
			'size'              => 50,
		);

		$args = wp_parse_args( $args, $term_args );
		$args = wp_parse_args( $args, $defaults );
	} else {
		$defaults = array(
			'size'              => 50,
			'type'              => 'visual',
			'color'             => '',
			'text'              => '',
			'background_color'  => '',
		);


		$args = wp_parse_args( $args, $defaults );
	}

	// Normalize.
	$args['set'] = empty ( $args['set'] ) ? 'emoji' : $args['set'];

	// Compose CSS.
	$css_style = '';
	$css_style .= strlen( $args['color'] ) ? 'color: ' . sanitize_hex_color( $args['color'] ) . ';' : '';
	$css_style .= strlen( $args['background_color'] ) ? 'background-color: ' . sanitize_hex_color( $args['background_color'] ) . ';' : '';
	$css_style = strlen( $css_style ) ? 'style="' . $css_style . '"' : '';


	$out = '';

	$class = array(
		'wyr-reaction-icon',
		'wyr-reaction-icon-' . $args['icon'],
		'wyr-reaction-icon-with-' . $args['type'],
	);

	$icon_url = ! empty( $args['path'] ) ? $args['path'] : wyr_build_reaction_icon_url( $args['set'], $args['icon'] );

	$out .= '<span class="'. implode( ' ', array_map( 'sanitize_html_class', $class ) ) . '" '. $css_style . '>';
		$out .= '<img width="' . absint( $args['size'] ) .  '" height="' . absint( $args['size'] ) . '" src="' . esc_url( $icon_url ) . '" alt="' . $args['text'] . '" />';

		$out .= '<span class="wyr-reaction-icon-text">' . esc_html( $args['text'] ) . '</span>';
	$out .= '</span>';

	return apply_filters( 'wyr_capture_reaction_icon', $out, $term_id, $args );
}

function wyr_build_reaction_icon_url( $set, $icon_id ) {
	return wyr_get_plugin_url() . 'images/'. $set . '/' . $icon_id . '.svg';
}

function wyr_get_reaction_icons() {
	$icons = array(
		'emoji' => array(
			'angry'     => array( 'label' => __( 'Angry', 'wyr' ) ),
			'cute'      => array( 'label' => __( 'Cute', 'wyr' ) ),
			'cry'       => array( 'label' => __( 'Cry', 'wyr' ) ),
			'geeky'     => array( 'label' => __( 'Geeky', 'wyr' ) ),
			'lol'       => array( 'label' => __( 'LOL', 'wyr' ) ),
			'love'      => array( 'label' => __( 'LOVE', 'wyr' ) ),
			'omg'       => array( 'label' => __( 'OMG', 'wyr' ) ),
			'win'       => array( 'label' => __( 'WIN', 'wyr' ) ),
			'wtf'       => array( 'label' => __( 'WTF', 'wyr' ) ),
		),
		'vibrant' => array(
			'angry'     => array( 'label' => __( 'Angry', 'wyr' ) ),
			'cute'      => array( 'label' => __( 'Cute', 'wyr' ) ),
			'cry'       => array( 'label' => __( 'Cry', 'wyr' ) ),
			'geeky'     => array( 'label' => __( 'Geeky', 'wyr' ) ),
			'lol'       => array( 'label' => __( 'LOL', 'wyr' ) ),
			'love'      => array( 'label' => __( 'LOVE', 'wyr' ) ),
			'omg'       => array( 'label' => __( 'OMG', 'wyr' ) ),
			'win'       => array( 'label' => __( 'WIN', 'wyr' ) ),
			'wtf'       => array( 'label' => __( 'WTF', 'wyr' ) ),
		),
		'flat' => array(
			'angry'     => array( 'label' => __( 'Angry', 'wyr' ) ),
			'cute'      => array( 'label' => __( 'Cute', 'wyr' ) ),
			'cry'       => array( 'label' => __( 'Cry', 'wyr' ) ),
			'geeky'     => array( 'label' => __( 'Geeky', 'wyr' ) ),
			'lol'       => array( 'label' => __( 'LOL', 'wyr' ) ),
			'love'      => array( 'label' => __( 'LOVE', 'wyr' ) ),
			'omg'       => array( 'label' => __( 'OMG', 'wyr' ) ),
			'win'       => array( 'label' => __( 'WIN', 'wyr' ) ),
			'wtf'       => array( 'label' => __( 'WTF', 'wyr' ) ),
		),
	);

	$custom_icons = wyr_get_custom_reaction_icons();

	if ( ! empty( $custom_icons ) ) {
		$icons['custom'] = $custom_icons;
	}

	return apply_filters( 'wyr_reaction_icons', $icons );
}

/**
 * Register custom icons.
 *
 * @param array $icons			Icons.
 *
 * @return array
 */
function wyr_get_custom_reaction_icons() {
	$attachments = get_posts( array(
		'post_type' 	    => 'attachment',
		'meta_key' 		    => '_wyr_custom_icon',
		'meta_value' 	    => true,
		'order'			    => 'ASC',
		'posts_per_page'    => -1,
	) );

	$icons = array();

	foreach( $attachments as $attachment ) {
		$icons[ $attachment->ID ] = array(
			'label' => $attachment->post_title,
			'path'  => $attachment->guid,
		);
	}

	return $icons;
}

function wyr_fake_reaction_count( $votes, $post ) {
	if ( empty( $post ) ) {
		return $votes;
	}

	// Get value defined for that single post.
	$fake_count = (int) get_post_meta( $post->ID, '_wyr_fake_reaction_count', true );

	// If user not defined the count, calculate it.
	if ( ! $fake_count ) {
		$fake_base = (int) get_option( 'wyr_fake_reaction_count_base' );

		// Only if fake base is set, we can apply fake count.
		if ( $fake_base > 0 ) {
			$fake_factor = wyr_get_fake_factor( $post->post_date );

			$fake_count = round( $fake_base * $fake_factor );
		}
	}

	$fake_count = apply_filters( 'wyr_fake_reaction_count', $fake_count, $post->ID );

	if ( $fake_count <= 0 ) {
		return $votes;
	}

	$fake_votes     = array();
	$reation_terms  = wyr_get_reactions();
	$disabled_reactions = get_post_meta( $post->ID, '_wyr_disable_fakes_for_reactions', true );
	if ( ! is_array( $disabled_reactions )) {
		$disabled_reactions = array();
	}

	// Fill fake votes array with $votes data (if any) to keep order.
	foreach ( $reation_terms as $reation_term ) {
		$id = $reation_term->slug;

		// If condig exists, use it.
		if ( isset( $votes[ $id ] ) ) {
			$fake_votes[ $id ] = $votes[ $id ];
		} else {
			$fake_votes[ $id ] = array(
				'count' 	 => 0,
				'percentage' => 0,
			);
		}
	}

	$total_votes 		= 0;
	$fake_count_diff 	= round( $fake_count * 17 / 100 ); // 17%.
	$fake_count_partial = ( count( $fake_votes ) - 1 ) * $fake_count_diff; // Start with the top value.

	// Add fakes.
	foreach ( $fake_votes as $id => $data ) {
		$disabled_reactions[ $id ] = ( isset( $disabled_reactions[ $id ] ) ) ? $disabled_reactions[ $id ] : false;
		if ( $disabled_reactions[ $id ] ) {
			continue;
		}
		$type_fake_count = $fake_count + $fake_count_partial;

		$fake_votes[ $id ]['count'] += $type_fake_count;

		$total_votes += $fake_votes[ $id ]['count'];

		$fake_count_partial -= $fake_count_diff; // And decrease it.
	}

	// Randomize reactions?
	if ( 'standard' === get_option( 'wyr_fake_reactions_randomize' ) ) {
		$fake_values = array_values( $fake_votes );
		shuffle( $fake_values );

		foreach ( $fake_votes as $id => $value ) {
			$fake_votes[ $id ] = array_pop( $fake_values );
		}
	}

	// Recalculate percentages.
	foreach ( $fake_votes as $id => $data ) {
		if ( $total_votes > 0 ) {
			$percentage = round( ( 100 * $data['count'] ) / $total_votes );
		} else {
			$percentage = 0;
		}

		$fake_votes[ $id ]['percentage'] = $percentage;
	}

	return $fake_votes;
}

/**
 * Return fake factor based on post creation date
 *
 * @param string $date			Post creation date.
 *
 * @return float
 */
function wyr_get_fake_factor( $date ) {
	$current_time = time();
	$date_time 	  = strtotime( $date );

	$day_in_seconds = 24 * 60 * 60;

	$days_diff = round( abs( $current_time - $date_time ) / $day_in_seconds );

	$t = $days_diff;	// Current time.
	$b = 0.1;			// Start value.
	$c = 0.9;			// Change in value.
	$d = 30;			// Duration.

	// Factor function doesn't return value equal to 1 after $d time.
	// Which is normal, as it's sinus, but we want to have 1 value after $d duration.
	if ( $days_diff > $d ) {
		return 1;
	}

	// EaseOutSine.
	$factor = $c * sin( $t / $d * (pi() / 2 ) ) + $b;

	return $factor;
}

/**
 * Check whether the plugin is active and plugin can rely on it
 *
 * @param string $plugin Base plugin path.
 *
 * @return bool
 */
function wyr_can_use_plugin( $plugin ) {
	// Detect plugin. For use on Front End only.
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return is_plugin_active( $plugin );
}

/**
 * Actions performed just before the post is removed
 *
 * @param int $post_id      Post id.
 */
function wyr_delete_post_votes( $post_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . wyr_get_votes_table_name();

	$wpdb->delete(
		$table_name,
		array(
			'post_id' => $post_id,
		),
		array(
			'%d',
		)
	);
}




function wyr_render_reactions_class( $r = array() ) {
	$final = array(
		'wyr-reactions',
	);

	$final = array_merge( $final, $r );
	$final = apply_filters( 'wyr_get_reactions_class', $final );

	echo 'class="' . implode( ' ', array_map( 'sanitize_html_class', $final ) )  . '"';
}

function wyr_render_reactions_body_class( $r = array() ) {
	$final = array(
		'wyr-reactions-body',
	);

	$final = array_merge( $final, $r );
	$final = apply_filters( 'wyr_get_reactions_body_class', $final );

	echo 'class="' . implode( ' ', array_map( 'sanitize_html_class', $final ) )  . '"';
}
