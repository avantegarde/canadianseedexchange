<?php
/**
 * BuddyPress plugin functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Bimber_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once trailingslashit( get_parent_theme_file_path() ) . 'includes/plugins/buddypress/customizer.php';

if ( ! defined( 'BP_DEFAULT_COMPONENT' ) ) {
	define( 'BP_DEFAULT_COMPONENT', 'profile' );
}



// Members.
add_filter( 'bp_directory_members_search_form', 'bimber_bp_directory_search_form' );
add_action( 'bp_setup_nav', 'bimber_bp_add_home_tab', 100 );
add_filter( 'snax_bp_component_main_nav',   'bimber_snax_bp_component_main_nav', 999, 2 );
add_action( 'bp_member_header_actions', 'bimber_bp_open_action_dropdown', 1 );
add_action( 'bp_member_header_actions', 'bimber_bp_member_add_button_class_filters', 1 );
add_action( 'bp_member_header_actions', 'bimber_bp_member_remove_button_class_filters', 9999 );
add_action( 'bp_member_header_actions', 'bimber_bp_close_action_dropdown',  9999 );
add_action( 'bp_directory_members_item', 'bimber_bp_members_counters', 5);
add_action( 'bimber_bp_members_counters', 'bimber_bp_members_counter_posts', 6);
add_filter( 'bp_before_has_members_parse_args', 'bimber_bp_fix_multisite_members', 9999, 2 );
add_filter( 'bp_core_get_active_member_count', 'bimber_bp_fix_multisite_members_count', 9999, 2 );

// XProfiles.
add_action( 'wp_loaded', 'bimber_bp_setup_xprofile_fields' );
add_action( 'bp_after_profile_field_content', 'bimber_bp_profile_elements', 9 );

// Groups.
add_filter( 'bp_directory_groups_search_form', 'bimber_bp_directory_search_form' );
add_action( 'bp_group_header_actions', 'bimber_bp_group_add_button_class_filters', 1 );
add_action( 'bp_group_header_actions', 'bimber_bp_group_remove_button_class_filters', 9999 );



add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'bimber_cover_image_css', 10, 1 );
add_filter( 'bp_before_groups_cover_image_settings_parse_args', 'bimber_cover_image_css', 10, 1 );



add_filter( 'author_link', 'bimber_bp_get_author_link', 10, 3 );

add_filter( 'template_include', 'bimber_bp_load_no_sidebar_page_template', 99 );


add_filter( 'bimber_setup_sidebars', 	'bimber_bp_setup_sidebars' );
add_filter( 'bimber_sidebar',			'bimber_bp_sidebar',100 );


add_action( 'wp', 'bimber_bp_update_last_online',9 );

add_action( 'widgets_init', 'bimber_bp_widgets_init' );
add_filter( 'bimber_author_info_box_bio', 'bimber_buddypress_use_description_for_author_info_box', 10, 2 );


// We will provide our own stylesheets.
add_action( 'wp_enqueue_scripts', 'bimber_bp_dequeue_styles', 20 );
add_action( 'wp_enqueue_scripts', 'bimber_bp_enqueue_head_styles', 20 );
add_action( 'wp_enqueue_scripts', 'bimber_bp_enqueue_scripts', 20 );





if ( function_exists( 'bp_set_theme_compat_feature' ) ) {
	bp_set_theme_compat_feature( 'legacy', array(
		'name'     => 'cover_image',
		'settings' => array(
			'components'   => array( 'xprofile', 'groups' ),
			'width'        => 1920,
			'height'       => 360,
			'callback'     => 'bp_legacy_theme_cover_image',
			'theme_handle' => 'bp-legacy-css',
		),
	) );
}

/**
 * Move Snax tabs to the beginning of profile tabs.
 *
 * @param array  $main_nav      Navigation config.
 * @param string $id            Component id.
 *
 * @return array
 */
function bimber_snax_bp_component_main_nav( $main_nav, $id ) {
	if ( is_network_admin() ) {
		return $main_nav;
	}

	if ( ! bimber_can_use_plugin( 'snax/snax.php' ) && ! is_network_admin() ) {
		return $main_nav;
	}

	$posts_component_id = 'snax_posts';
	$items_component_id = 'snax_items';
	$votes_component_id = 'snax_votes';

	if ( $posts_component_id === $id ) {
		$main_nav['position'] = 4;
	}

	if ( $items_component_id === $id ) {
		$main_nav['position'] = 6;
	}

	if ( $votes_component_id === $id ) {
		$main_nav['position'] = 8;
	}

	return $main_nav;
}

/**
 * Adjust the markup of a directory (groups, members) search form
 *
 * @param string $html HTML markup.
 *
 * @return string
 */
function bimber_bp_directory_search_form( $html ) {
	$html = str_replace(
		array(
			'<form ',
			'<input type="submit"',
		),
		array(
			'<form class="g1-form-s" ',
			'<input class="g1-button g1-button-simple" type="submit"',
		),
		$html
	);
	return $html;
}

function bimber_bp_member_add_button_class_filters() {
	add_filter( 'bp_get_add_friend_button', 			'bimber_bp_get_menu_item_button' );
	add_filter( 'bp_follow_get_add_follow_button',      'bimber_bp_get_menu_item_button' );
	add_filter( 'bp_get_send_public_message_button',    'bimber_bp_get_menu_item_button' );
	add_filter( 'bp_get_send_message_button_args',      'bimber_bp_get_menu_item_button' );
}

function bimber_bp_member_remove_button_class_filters() {
	remove_filter( 'bp_get_add_friend_button', 			'bimber_bp_get_solid_button' );
}

function bimber_bp_group_add_button_class_filters() {
	add_filter( 'bp_get_group_join_button', 			'bimber_bp_get_solid_button' );
}

function bimber_bp_group_remove_button_class_filters() {
	remove_filter( 'bp_get_group_join_button', 			'bimber_bp_get_solid_button' );
}

/**
 * Adjust BuddyPress button classes.
 */
function bimber_bp_get_menu_item_button( $button ) {
	if ( ! is_array( $button ) ) {
		return $button;
	}

	if ( ! isset( $button['wrapper_class'] ) ) {
		$button['wrapper_class'] = 'menu-item';
	} else {
		$button['wrapper_class'] .= ' menu-item';
	}

	return $button;
}

function bimber_bp_get_solid_button( $button ) {
	$button['link_class'] .= ' g1-button g1-button-m g1-button-simple';


//					'component'         => 'friends',
//					'must_be_logged_in' => true,
//					'block_self'        => true,
//					'wrapper_class'     => 'friendship-button pending_friend',
//					'wrapper_id'        => 'friendship-button-' . $potential_friend_id,
//					'link_href'         => wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/requests/cancel/' . $potential_friend_id . '/', 'friends_withdraw_friendship' ),
//					'link_text'         => __( 'Cancel Friendship Request', 'buddypress' ),
//					'link_id'           => 'friend-' . $potential_friend_id,
//					'link_rel'          => 'remove',
//					'link_class'        => 'friendship-button pending_friend requested'


	// Add our special key for tracking purposes
	$button['g1'] = true;

	return $button;
}

/**
 * Render dynamic CSS for the #header-cover-image
 *
 * @param array $params Parameters.
 *
 * @return string
 */
function bimber_cover_image_callback( $params = array() ) {
	if ( empty( $params ) ) {
		return;
	}

	return '
		#buddypress #header-cover-image {
			height: ' . absint( $params['height'] ) . 'px;
			background-image: url(' . esc_url( $params['cover_image'] ) . ');
		}
	';
}

function bimber_cover_image_css( $settings = array() ) {
	/**
	 * If you are using a child theme, use bp-child-css
	 * as the theme handle
	 */
	$settings['theme_handle'] = is_rtl() ? 'bp-parent-css-rtl' : 'bp-parent-css';
	// Adjust size
	$settings['height'] = 360;

	$settings['callback'] = 'bimber_cover_image_callback';

	return $settings;
}

function bimber_render_markup_before_list_items_loop() {
	echo '<div class="g1-indent">';
}

function bimber_render_markup_after_list_items_loop() {
	echo '</div>';
}

/**
 * Return current user profile url
 *
 * @param string $link          Author posts link.
 * @param int 	 $author_id		Author id.
 *
 * @return string
 */
function bimber_bp_get_author_link( $link, $author_id ) {
	$link = bp_core_get_user_domain( $author_id );
	$link = trailingslashit( $link . bp_get_profile_slug() );
	$link = trailingslashit( $link . 'home' );
	return $link;
}

/** PROFILE ************/


/**
 * Whether or not to show the "Change Cover Image" link
 *
 * @return bool
 */
function bimber_bp_show_cover_image_change_link() {
	$show = bp_core_can_edit_settings() && bp_displayed_user_use_cover_image_header();

	return apply_filters( 'bimber_bp_show_cover_image_change_link', $show );
}

/**
 * Render the "Change Cover Image" link
 */
function bimber_bp_render_cover_image_change_link() {
	$link = bp_get_members_component_link( 'profile', 'change-cover-image' );

	?>
	<a class="g1-bp-change-image" href="<?php echo esc_url( $link ); ?>" title="<?php  esc_attr_e( 'Change Cover Image', 'buddypress' ); ?>"><?php esc_html_e( 'Change Cover Image', 'buddypress' ); ?></a>
	<?php
}

/**
 * Whether or not to show the "Change Profile Photo" link
 *
 * @return bool
 */
function bimber_bp_show_profile_photo_change_link() {
	$show = bp_core_can_edit_settings() && buddypress()->avatar->show_avatars;

	return apply_filters( 'bimber_bp_show_profile_photo_change_link', $show );
}

/**
 * Render the "Change Profile Photo" link
 */
function bimber_bp_render_profile_photo_change_link() {
	$link = bp_get_members_component_link( 'profile', 'change-avatar' );

	?>
	<a class="g1-bp-change-avatar" href="<?php echo esc_url( $link ); ?>" title="<?php esc_attr_e( 'Change Profile Photo', 'buddypress' ); ?>"><?php esc_html_e( 'Change Profile Photo', 'buddypress' ); ?></a>
	<?php
}


/** GROUP ************/


/**
 * Whether or not to show the "Change Cover Image" link
 *
 * @return bool
 */
function bimber_bp_show_group_cover_image_change_link() {
	$show = bp_core_can_edit_settings() && bp_group_use_cover_image_header();

	return apply_filters( 'bimber_bp_show_group_cover_image_change_link', $show );
}

/**
 * Render the "Change Cover Image" link
 */
function bimber_bp_render_group_cover_image_change_link() {
	$group_link = bp_get_group_permalink();
	$admin_link = trailingslashit( $group_link . 'admin' );
	$link = trailingslashit( $admin_link . 'group-cover-image' );

	?>
	<a class="g1-bp-change-image" href="<?php echo esc_url( $link ); ?>" title="<?php  esc_attr_e( 'Change Cover Image', 'buddypress' ); ?>"><?php esc_html_e( 'Change Cover Image', 'buddypress' ); ?></a>
	<?php
}

/**
 * Whether or not to show the "Change Profile Photo" link
 *
 * @return bool
 */
function bimber_bp_show_group_photo_change_link() {
	$show = bp_core_can_edit_settings() && ! bp_disable_group_avatar_uploads() && buddypress()->avatar->show_avatars;

	return apply_filters( 'bimber_bp_show_group_photo_change_link', $show );
}

/**
 * Render the "Change Profile Photo" link
 */
function bimber_bp_render_group_photo_change_link() {
	$group_link = bp_get_group_permalink();
	$admin_link = trailingslashit( $group_link . 'admin' );
	$link = trailingslashit( $admin_link . 'group-avatar' );

	?>
	<a class="g1-bp-change-avatar" href="<?php echo esc_url( $link ); ?>" title="<?php esc_attr_e( 'Change Group Photo', 'buddypress' ); ?>"><?php esc_html_e( 'Change Group Photo', 'buddypress' ); ?></a>
	<?php
}

/**
 * Override default page template for BuddyPress pages.
 *
 * @param str $template  Template to load.
 * @return str
 */
function bimber_bp_load_no_sidebar_page_template( $template ) {
	$is_groups = strpos( $template, 'groups' ) > 0;
	if ( 'home' === bp_current_action() && ! $is_groups ) {
		$template = str_replace( 'index.php', 'index-full.php', $template );
	}

	if ( 'standard' === bimber_get_theme_option( 'bp', 'enable_sidebar' ) ) {
		return $template;
	}

	if ( is_buddypress() && strpos( $template, trailingslashit( 'single' ) . 'index.php' )  && ! $is_groups ) {
		$template = str_replace( 'index.php', 'index-full.php', $template );
	}

	if ( is_buddypress() && strpos( $template, 'page.php' )) {
		$template = str_replace( 'page.php', 'g1-template-page-full.php', $template );
	}
	if ( is_buddypress() && strpos( $template, 'index-directory.php' )) {
		$template = str_replace( 'index-directory.php', 'index-directory-full.php', $template );
	}

	return $template;
}

/**
 * Open action dropdown markup
 */
function bimber_bp_open_action_dropdown() {
	?>
	<div class="g1-drop g1-drop-m g1-drop-icon g1-drop-before">
		<button class="g1-button-none g1-drop-toggle">
			<span class="g1-drop-toggle-icon"></span><span class="g1-drop-toggle-text"><?php esc_html_e( 'More', 'bimber' ); ?></span>
			<span class="g1-drop-toggle-arrow"></span>
		</button>

		<div class="g1-drop-content">
			<div class="sub-menu">
	<?php
}

/**
 * Close action dropdown markup
 */
function bimber_bp_close_action_dropdown() {
	?>
			</div>
		</div>
	</div><!-- .g1-drop -->
	<?php
}

/**
 * BP actions placeholder for logged out users
 */
function bimber_bp_actions_placeholder() {
	$items = array();

	if ( function_exists( 'bp_follow_add_follow_button' ) ) {
		$items[] = _x( 'Follow', 'Button', 'bp-follow' );
	}

	if ( bp_is_active( 'friends' ) ) {
		$items[] = __( 'Add Friend', 'buddypress' );
	}

	if ( ! count( $items ) ) {
		return;
	}
	?>

	<?php bimber_bp_open_action_dropdown(); ?>
	<?php foreach ( $items as $index => $text ) : ?>
		<div class="generic-button menu-item"><button class="snax-login-required g1-button g1-button-simple g1-button-m"><?php echo esc_html( $text ); ?></button></div>
	<?php endforeach; ?>
	<?php bimber_bp_close_action_dropdown(); ?>
<?php

//	<a href="#" class="snax-login-required friendship-button not_friends add g1-button g1-button-m g1-button-simple" id="friend-1" rel="add">Public Message</a>
}

/**
 * BP group actions placeholder for logged out users
 */
function bimber_bp_group_actions_placeholder() {
	$items = array();

	global $groups_template;
	$group =& $groups_template->group;

	if ( 'public' === $group->status ) {
		$items[] = __( 'Join Group', 'buddypress' );
	}

	if ( ! count( $items ) ) {
		return;
	}
	?>
	<?php bimber_bp_open_action_dropdown(); ?>
	<?php foreach ( $items as $index => $text ) : ?>
		<div class="generic-button menu-item"><button class="snax-login-required g1-button g1-button-simple g1-button-m"><?php echo esc_html( $text ); ?></button></div>
	<?php endforeach; ?>
	<?php bimber_bp_close_action_dropdown(); ?>
	<?php
}

/**
 * Add member counters section
 */
function bimber_bp_members_counters() {
	?>
	<div class="item-counters">
	<?php do_action( 'bimber_bp_members_counters' ); ?>
	</div>
	<?php
}

/**
 * Add member posts counter
 */
function bimber_bp_members_counter_posts() {
	$post_types = apply_filters( 'bimber_default_post_types', array( 'post') );
	?>
	<div class="item-counters-counter">
		<div class="g1-delta g1-delta-1st item-counters-counter-value">
			<?php echo esc_html( count_user_posts( bp_get_member_user_id(), $post_types ) ); ?>
		</div>
		<div class="g1-meta"><?php esc_html_e( 'Posts', 'bimber' ); ?></div>
	</div>
	<?php
}

/**
 * Add home tab.
 */
function bimber_bp_add_home_tab() {
	global $bp;
	bp_core_new_subnav_item( array(
		'name'              => __( 'Home', 'bimber' ),
		'slug'              => 'home',
		'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'profile' ),
		'parent_slug'       => 'profile',
		'screen_function'   => 'bimber_bp_home_callback',
		'position'          => 1,
	) );

	bp_core_remove_subnav_item( 'profile', 'public' );

	bp_core_new_subnav_item( array(
		'name'            => _x( 'View', 'Member profile view', 'buddypress' ),
		'slug'            => 'classic',
		'parent_url'      => trailingslashit( bp_displayed_user_domain() . 'profile' ),
		'parent_slug'     => 'profile',
		'screen_function' => 'bp_members_screen_display_profile',
		'position'        => 2,
	) );

	bp_core_new_nav_default( array(
		'parent_slug'       => 'profile',
		'subnav_slug'       => 'home',
		'screen_function'   => 'bimber_bp_home_callback',
	) );
	$parent_nav = $bp->members->nav->get_primary( array( 'slug' => 'profile' ), false );
	$parent_nav['profile']['position'] = 2;

}

/**
 * Home tab callback.
 */
function bimber_bp_home_callback() {
	add_action( 'bp_template_content', 'bimber_bp_home_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * Home tab content.
 */
function bimber_bp_home_content() {
	$columns_class = 'g1-collection-columns-3';
	?>
	<div class="g1-collection <?php echo sanitize_html_class( $columns_class )?> buddypress-home">
		<div class="g1-collection-viewport">
			<ul class="g1-collection-items">
				<?php
				add_filter( 'dynamic_sidebar_params', 'bimber_bp_home_content_columns',10 , 1 );
				dynamic_sidebar( 'bimber_bp_home' );
				remove_filter( 'dynamic_sidebar_params', 'bimber_bp_home_content_columns', 10 ,1 );
				?>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * Add column classes to widgets
 *
 * @param array $params  Params.
 * @return array
 */
function bimber_bp_home_content_columns( $params ) {
	$params[0]['before_widget'] = '<li class="g1-collection-item g1-collection-item-1of3"><div class="g1-buddypress-home-item">' . $params[0]['before_widget'];
	$params[0]['after_widget'] = $params[0]['after_widget'] . '</div></li>';
	return $params;
}

/**
 * Fix multisite members view
 *
 * @param array $args Query args.
 * @return array
 */
function bimber_bp_fix_multisite_members( $args ) {
	if ( ! is_multisite() ) {
		return $args;
	}

	global $wpdb;

	$all_users = $wpdb->get_col( "SELECT ID FROM {$wpdb->users}" );
	$current_site = get_current_blog_id();
	$excluded_users = array();

	foreach ( $all_users as $key => $user ) {
		$user_blogs = get_blogs_of_user( $user );

		if ( ! array_key_exists( $current_site, $user_blogs ) ) {
			$excluded_users[] = $user;
		}
	}

	if ( ! empty( $excluded_users ) ) {
		$args['exclude'] = implode( ',', $excluded_users );
	}

	return $args;
}

/**
 * Create Xprofile fields.
 */
function bimber_bp_setup_xprofile_fields() {
	if ( ! bp_is_active( 'xprofile' ) ) {
		return;
	}

	$short_id = get_option( 'bimber_bp_short_field_id', false );
	$long_id = get_option( 'bimber_bp_long_field_id', false );

	// Fallback, so we don't break the old sites.
	$short_id_by_name = xprofile_get_field_id_from_name( bimber_bp_get_short_description_field_name() );
	$long_id_by_name = xprofile_get_field_id_from_name( bimber_bp_get_long_description_field_name() );

	if ( ! $short_id && $short_id_by_name ) {
		update_option( 'bimber_bp_short_field_id', $short_id_by_name );
		$short_id = $short_id_by_name;
	}

	if ( ! $long_id && $long_id_by_name ) {
		update_option( 'bimber_bp_long_field_id', $long_id_by_name );
		$long_id = $long_id_by_name;
	}

	// Create fields if necessary.
	if ( ! $short_id ) {
		$args = array(
			'field_group_id' 	=> 1,
			'type' 				=> 'textbox',
			'name' 				=> bimber_bp_get_short_description_field_name(),
		);
		$id = xprofile_insert_field( $args );
		update_option( 'bimber_bp_short_field_id', $id );
	}

	if ( ! $long_id ) {
		$args = array(
			'field_group_id' 	=> 1,
			'type' 				=> 'textarea',
			'name' 				=> bimber_bp_get_long_description_field_name(),
		);
		$id = xprofile_insert_field( $args );
		update_option( 'bimber_bp_long_field_id', $id );
	}
}

/**
 * Short description field ID.
 */
function bimber_bp_get_short_description_field_id() {
	$short_id = get_option( 'bimber_bp_short_field_id', false );

	// Fallback.
	if ( ! $short_id ) {
		return bimber_bp_get_short_description_field_name();
	}

	return $short_id;
}

/**
 * Long description field ID.
 */
function bimber_bp_get_long_description_field_id() {
	$long_id = get_option( 'bimber_bp_long_field_id', false );

	// Fallback.
	if ( ! $long_id ) {
		return bimber_bp_get_long_description_field_name();
	}

	return $long_id;
}

/**
 * Short description field name.
 */
function bimber_bp_get_short_description_field_name() {
	return __( 'Short Description', 'bimber' );
}

/**
 * Long description field name.
 */
function bimber_bp_get_long_description_field_name() {
	return __( 'Long Description', 'bimber' );
}

/**
 * Register BuddyPress specific sidebars
 *
 * @param array $sidebars		Registered sidebars.
 *
 * @return array
 */
function bimber_bp_setup_sidebars( $sidebars ) {

	// Default BuddyPress sidebar.
	$sidebars['bimber_buddypress'] = array(
		'label'       => 'BuddyPress',
		'description' => esc_html__( 'Leave empty to use the Primary sidebar', 'bimber' ),
	);

	// Members.
	$sidebars['bimber_buddypress_members'] = array(
		'label'       => esc_html__( 'BuddyPress Members', 'bimber' ),
		'description' => esc_html__( 'Leave empty to use the Buddypress sidebar', 'bimber' ),
	);

	// Single member.
	$sidebars['bimber_buddypress_single_member'] = array(
		'label'       => esc_html__( 'BuddyPress Single Member', 'bimber' ),
		'description' => esc_html__( 'Leave empty to use the Buddypress Members sidebar', 'bimber' ),
	);

	// Single member homepage.
	$sidebars['bimber_bp_home'] = array(
		'label'       => esc_html__( 'BuddyPress Single Member Home', 'bimber' ),
		'description' => esc_html__( 'BuddyPress profile Home section', 'bimber' ),
	);

	// Groups.
	$sidebars['bimber_buddypress_groups'] = array(
		'label'       => esc_html__( 'BuddyPress Groups', 'bimber' ),
		'description' => esc_html__( 'Leave empty to use the Buddypress sidebar', 'bimber' ),
	);

	// Single group.
	$sidebars['bimber_buddypress_single_group'] = array(
		'label'       => esc_html__( 'BuddyPress Single Group', 'bimber' ),
		'description' => esc_html__( 'Leave empty to use the Buddypress Groups sidebar', 'bimber' ),
	);


	return $sidebars;
}

/**
 * Load BuddyPress specific sidebar
 *
 * @param string $sidebar		Sidebar set.
 *
 * @return string
 */
function bimber_bp_sidebar( $sidebar ) {
	global $bp;

	// Default BuddyPress sidebar.
	if ( is_buddypress() ) {
		$sidebar = 'bimber_buddypress';
	}

	// Member directory.
	if ( bp_is_current_component( $bp->members->slug ) && is_active_sidebar( 'bimber_buddypress_members' ) ) {
		$sidebar = 'bimber_buddypress_members';
	}

	// Single member.
	if ( bp_is_user() && is_active_sidebar( 'bimber_buddypress_single_member' ) ) {
		$sidebar = 'bimber_buddypress_single_member';
	}

	if ( bp_is_active( 'groups' ) ) {
		// Group directory.
		if ( bp_is_current_component( $bp->groups->slug ) && is_active_sidebar( 'bimber_buddypress_groups' ) ) {
			$sidebar = 'bimber_buddypress_groups';
		}

		// Single group.
		if ( bp_is_group() && is_active_sidebar( 'bimber_buddypress_single_group' ) ) {
			$sidebar = 'bimber_buddypress_single_group';
		}
	}

	return $sidebar;
}

/**
 * Add our fields to BP profile.
 */
function bimber_bp_profile_elements() {
	global $group;

	if ( ! $group  ) {
		return;
	}

	// Don't add our elements to groups other the the Primary (which can't be deleted).
	if ( $group->can_delete ) {
		return;
	}

	$data = get_userdata( bp_displayed_user_id() );
	$registered = $data->user_registered;
	$last_online = get_user_meta( bp_displayed_user_id(), 'bimber_last_online', true );
	if ( ! empty( $last_online ) ) {
		$since_last_check = time() - $last_online;
		if ( $since_last_check > 300 ) {
			$last_online = date( 'h:i d/m/Y', $last_online );
		} else {
			$last_online = __( 'Less than five minutes ago', 'bimber' );
		}
	} else {
		$last_online = __( 'Never', 'bimber' );
	}
	?>
	<div class="bp-widget base">
		<h2><?php esc_html_e( 'Additional info', 'bimber' ); ?></h2>
		<table class="profile-fields">
			<tbody>
				<tr>
					<td class="label">
						<?php echo esc_html__( 'Member since', 'bimber' );?>
					</td>
					<td class="data"><?php echo date( 'd/m/Y', strtotime( $registered ) );?><p>
					</p></td>
				</tr>
				<tr>
					<td class="label">
						<?php echo esc_html__( 'Last online', 'bimber' );?>
					</td>
					<td class="data"><?php echo esc_html( $last_online); ;?><p>
					</p></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Update user last online meta
 */
function bimber_bp_update_last_online() {

	$last_online = get_user_meta( get_current_user_id(), 'bimber_last_online', true );
	if ( $last_online ) {
		$since_last_check = time() - $last_online;
	} else {
		$since_last_check = 301;
	}
	if ( is_user_logged_in() && ! is_admin() && $since_last_check > 300 ) {
		update_user_meta( get_current_user_id(), 'bimber_last_online', time() );
	}
}

/**
 * Init widgets
 */
function bimber_bp_widgets_init() {
	register_widget( 'Bimber_Widget_Featured_Author' );
}

/**
 * Fix multisite member count
 *
 * @param int $count  Member count.
 * @return int
 */
function bimber_bp_fix_multisite_members_count( $count ) {
	if ( ! is_multisite() ) {
		return $count;
	}
	global $wpdb;
	$all_sites = get_sites();
	$all_users = $wpdb->get_col( "SELECT ID FROM {$wpdb->users}" );
	$current_site = get_current_blog_id();
	$excluded_users = array();
	foreach ( $all_users as $key => $user ) {
		$user_blogs = get_blogs_of_user( $user );
		$not_site_user = ! array_key_exists( $current_site, $user_blogs );
		if ( $not_site_user ) {
			$excluded_users[] = $user;
		}
		if ( apply_filters( 'bimber_buddypress_exclude_multisite_user_from_count', false, $user ) ) {
			$excluded_users[] = $user;
		}
	}
	$count = get_transient( 'bp_active_member_count' );
	if ( false === $count ) {
		$bp = buddypress();
		// Avoid a costly join by splitting the lookup.
		if ( is_multisite() ) {
			$sql = "SELECT ID FROM {$wpdb->users} WHERE (user_status != 0 OR deleted != 0 OR user_status != 0) ";
		} else {
			$sql = "SELECT ID FROM {$wpdb->users} WHERE user_status != 0";
		}
		$exclude_users     = $wpdb->get_col( $sql );
		$exclude_users = array_merge( $exclude_users, $excluded_users );
		$exclude_users_sql = !empty( $exclude_users ) ? "AND user_id NOT IN (" . implode( ',', wp_parse_id_list( $exclude_users ) ) . ")" : '';
		$count             = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(user_id) FROM {$bp->members->table_name_last_activity} WHERE component = %s AND type = 'last_activity' {$exclude_users_sql}", $bp->members->id ) );
		set_transient( 'bp_active_member_count', $count );
	}
	return $count;
}

/**
 * Use long description instead of bio in author info box.
 *
 * @param string $bio Bio.
 * @param int    $user_id User id.
 * @return string
 */
function bimber_buddypress_use_description_for_author_info_box( $bio, $user_id ) {
	if ( function_exists( 'xprofile_get_field_data' ) ) {
		// Get ID by name. If user deleted that field, it won't be used.
		$long_id = xprofile_get_field_id_from_name( bimber_bp_get_long_description_field_name() );

		if ( $long_id ) {
			$long_field = xprofile_get_field( $long_id );

			if ( ! $long_field->id || (int) $long_id !== (int) $long_field->id ) {
				return $bio;
			}

			$description = xprofile_get_field_data( $long_id, $user_id );

			if ( ! empty( $description ) ) {
				$bio = $description;
			}
		}
	}

	return $bio;
}











add_action( 'bp_directory_members_item', 'bimber_bp_render_directory_members_item_actions', 100 );
/**
 * Render member actions on BuddyPRess directory pages.
 */
function bimber_bp_render_directory_members_item_actions() {
	?>
	<div class="item-actions">
		<?php do_action( 'bimber_bp_directory_members_item_actions' ); ?>
	</div>
	<?php
}


add_action( 'bimber_bp_directory_members_item_actions', 'bimber_buddypress_render_add_friend_button' );
function bimber_buddypress_render_add_friend_button() {
	if ( bp_is_active( 'friends' ) ) {
		$friend_id = bp_get_member_user_id();
		$friend_status = false;

		// Use proper HTML markup via filter.
		add_filter( 'bp_get_add_friend_button', 'bimber_bp_get_directory_members_item_button' );
		bp_add_friend_button($friend_id, $friend_status);
		remove_filter( 'bp_get_add_friend_button', 'bimber_bp_get_directory_members_item_button' );
	}
}

function bimber_bp_get_directory_members_item_button( $button ) {
//	$button = array(
//		'id'                => 'not_friends',
//		'component'         => 'friends',
//		'must_be_logged_in' => true,
//		'block_self'        => true,
//		'wrapper_class'     => 'friendship-button not_friends',
//		'wrapper_id'        => 'friendship-button-' . $potential_friend_id,
//		'link_href'         => wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/add-friend/' . $potential_friend_id . '/', 'friends_add_friend' ),
//		'link_text'         => __( 'Add Friend', 'buddypress' ),
//		'link_id'           => 'friend-' . $potential_friend_id,
//		'link_rel'          => 'add',
//		'link_class'        => 'friendship-button not_friends add'
//	);

	$button['wrapper'] = '';
	$button['link_class'] .= ' g1-button g1-button-s g1-button-simple';

	return $button;
}



/**
 * Don' load BuddyPress legacy stylesheet.
 *
 * We will provide our own stylesheets.
 */
function bimber_bp_dequeue_styles() {
	wp_dequeue_style( 'bp-legacy-css' );
}

/**
 * Enqueue BuddyPress Plugin integration assets.
 */
function bimber_bp_enqueue_head_styles() {
	// Hook into to disable loading CSS for BuddyPress plugin integration.
	if ( ! apply_filters( 'bimber_bp_load_css', true ) ) {
		return;
	}
	$version = bimber_get_theme_version();
	$stack = bimber_get_current_stack();
	$skin = bimber_get_theme_option( 'global', 'skin' );

	$uri = trailingslashit( get_template_directory_uri() );

	wp_enqueue_style( 'bimber-buddypress', $uri . 'css/' . bimber_get_css_theme_ver_directory() . '/styles/' . $stack . '/buddypress-' . $skin . '.min.css', array(), $version );
	wp_style_add_data( 'bimber-buddypress', 'rtl', 'replace' );

	/**
	 * Conditionally load page-specific CSS
	 *
	 * The whole CSS is divided into multiple files for performance optimization.
	 */
	$bp_load_css_conditionally = apply_filters( 'bimber_bp_load_css_conditionally', true );

	if ( !$bp_load_css_conditionally || bp_is_directory() ) {
		wp_enqueue_style( 'bimber-buddypress-directory', $uri . 'css/' . bimber_get_css_theme_ver_directory() . '/styles/' . $stack . '/buddypress-directory-' . $skin . '.min.css', array(), $version );
		wp_style_add_data( 'bimber-buddypress-directory', 'rtl', 'replace' );
	}

	if ( !$bp_load_css_conditionally || bp_is_register_page() ) {
		wp_enqueue_style( 'bimber-buddypress-sign-in-up', $uri . 'css/' . bimber_get_css_theme_ver_directory() . '/styles/' . $stack . '/buddypress-sign-in-up-' . $skin . '.min.css', array(), $version );
		wp_style_add_data( 'bimber-buddypress-sign-in-up', 'rtl', 'replace' );
	}

	if ( !$bp_load_css_conditionally || bp_is_user() ) {
		wp_enqueue_style( 'bimber-buddypress-single-member', $uri . 'css/' . bimber_get_css_theme_ver_directory() . '/styles/' . $stack . '/buddypress-single-member-' . $skin . '.min.css', array(), $version );
		wp_style_add_data( 'bimber-buddypress-single-member', 'rtl', 'replace' );
	}

	if ( !$bp_load_css_conditionally || bp_is_group() ) {
		wp_enqueue_style( 'bimber-buddypress-single-group', $uri . 'css/' . bimber_get_css_theme_ver_directory() . '/styles/' . $stack . '/buddypress-single-group-' . $skin . '.min.css', array(), $version );
		wp_style_add_data( 'bimber-buddypress-single-group', 'rtl', 'replace' );
	}

	if ( !$bp_load_css_conditionally || bp_is_activity_component() || bp_is_group_activity() ) {
		wp_enqueue_style( 'bimber-buddypress-activity', $uri . 'css/' . bimber_get_css_theme_ver_directory() . '/styles/' . $stack . '/buddypress-activity-' . $skin . '.min.css', array(), $version );
		wp_style_add_data( 'bimber-buddypress-activity', 'rtl', 'replace' );
	}
}

/**
 * Enqueue BuddyPress Plugin integration assets.
 */
function bimber_bp_enqueue_scripts() {
	// Hook into to disable loading JS for BuddyPress plugin integration.
	if ( ! apply_filters( 'bimber_bp_load_js', true ) ) {
		return;
	}
	$version = bimber_get_theme_version();
	$uri = trailingslashit( get_template_directory_uri() );

	/**
	 * Conditionally load page-specific JS
	 *
	 * The whole JS is divided into multiple files for performance optimization.
	 */
	$bp_load_js_conditionally = apply_filters( 'bimber_bp_load_js_conditionally', true );

	if ( !$bp_load_js_conditionally || is_buddypress() ) {
		wp_enqueue_script( 'bimber-bp', $uri . 'js/bp.js', array( 'jquery', 'bimber-global' ), $version, true );
		wp_enqueue_script( 'bimber-bp-tabs', $uri . 'js/bp-tabs.js', array( 'jquery', 'bimber-global' ), $version, true );
		// @todo Load it conditionally
		wp_enqueue_script( 'bimber-bp-follow', $uri . 'js/bp-follow.js', array( 'jquery', 'bimber-global' ), $version, true );
	}

	if ( !$bp_load_js_conditionally || bp_is_user() ) {
		wp_enqueue_script( 'bimber-bp-item-buttons', $uri . 'js/bp-item-buttons.js', array( 'jquery', 'bimber-global' ), $version, true );
	}

	if ( !$bp_load_js_conditionally || bp_is_group() ) {
		wp_enqueue_script( 'bimber-bp-item-buttons', $uri . 'js/bp-item-buttons.js', array( 'jquery', 'bimber-global' ), $version, true );
	}
}





function bimber_bp_user_query_prev( $sql, $query ) {
	$sql['where'][] = 'u.display_name < "' . bp_get_displayed_user_fullname() . '"';
	$sql['order'] = 'DESC';

	return $sql;
}

function bimber_bp_user_query_next( $sql, $query ) {
	$sql['where'][] = 'u.display_name > "' . bp_get_displayed_user_fullname() . '"';
	$sql['order'] = 'ASC';

	return $sql;
}

function bimber_bp_get_next_user_id() {
	$user_id = 0;

	$query_args = array(
		'type'      => 'alphabetical',
		'per_page'  => 1,
	);

	add_filter( 'bp_user_query_uid_clauses', 'bimber_bp_user_query_next', 99, 2 );
	$query = new BP_User_Query( $query_args );
	remove_filter( 'bp_user_query_uid_clauses', 'bimber_bp_user_query_next', 99, 2 );

	if ( count( $query->user_ids ) ) {
		$user_id = $query->user_ids[0];
	}

	return $user_id;
}

function bimber_bp_get_prev_user_id() {
	$user_id = 0;

	$query_args = array(
		'type'      => 'alphabetical',
		'per_page'  => 1,
	);

	add_filter( 'bp_user_query_uid_clauses', 'bimber_bp_user_query_prev', 99, 2 );
	$query = new BP_User_Query( $query_args );
	remove_filter( 'bp_user_query_uid_clauses', 'bimber_bp_user_query_prev', 99, 2 );

	if ( count( $query->user_ids ) ) {
		$user_id = $query->user_ids[0];
	}

	return $user_id;
}


add_action( 'bp_before_group_members_list', 'bimber_bp_before_group_members_list' );
function bimber_bp_before_group_members_list() {

}



add_filter( 'bp_get_group_join_button', 'bimber_bp_get_group_join_button', 10, 2 );
function bimber_bp_get_group_join_button( $button, $group ) {
	$button['link_class'] .= ' g1-button g1-button-simple g1-button-xs';

	return $button;
}