<?php
/**
 * Snax Settings Section
 *
 * @package snax
 * @subpackage Settings
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// Register section and fields.
add_filter( 'snax_admin_get_settings_sections', 'snax_admin_settings_sections_collections' );
add_filter( 'snax_admin_get_settings_fields',   'snax_admin_settings_fields_collections' );
add_filter( 'snax_settings_pages',              'snax_register_collections_settings_page', 10, 2 );
add_filter( 'snax_get_admin_settings_tabs',     'snax_register_collections_settings_tab', 10, 2 );
add_action( 'admin_head',                       'snax_remove_collections_settings_page_from_menu' );
add_filter( 'snax_settings_menu_highlight',     'snax_collections_settings_menu_highlight' );
add_action( 'admin_init',                       'snax_collections_handle_admin_actions' );

/**
 * Register the settings page
 *
 * @param array $pages      Registered pages.
 *
 * @return array
 */
function snax_register_collections_settings_page( $pages, $capability ) {
	$pages[] = add_options_page(
		'Snax Collections',
		'Snax Collections',
		$capability,
		'snax-collections-settings',
		'snax_admin_collections_settings'
	);

	return $pages;
}

/**
 * Register the settings tab
 *
 * @param array  $tabs          Tabs list.
 * @param string $active_tab    Currently tab.
 *
 * @return array
 */
function snax_register_collections_settings_tab( $tabs, $active_tab ) {
	$tabs['collections'] = array(
		'href'  => snax_get_collections_settings_url(),
		'name'  => esc_html_x( 'Collections', 'Settings tab title', 'snax' ),
		'order' => 25,
	);

	return $tabs;
}

/**
 * Return url of the Collection settings page
 *
 * @return string
 */
function snax_get_collections_settings_url() {
	return snax_admin_url( add_query_arg( array( 'page' => 'snax-collections-settings' ), 'admin.php' ) );
}

/**
 * Return link to the Collection settings page
 *
 * @return string
 */
function snax_get_collections_settings_link() {
	return sprintf( '<a href="%s">%s</a>', snax_get_collections_settings_url(), esc_html_x( 'Collections', 'Settings tab title', 'snax' ) );
}

/**
 * Remove the page from Setting menu
 */
function snax_remove_collections_settings_page_from_menu() {
	remove_submenu_page( snax_admin()->settings_page, 'snax-collections-settings' );
}

/**
 * Highlight the Snax main menu when the settings page is selected
 *
 * @param array $slugs  Page slugs.
 *
 * @return array
 */
function snax_collections_settings_menu_highlight( $slugs ) {
	$slugs[] = 'snax-collections-settings';

	return $slugs;
}

/**
 * Register section
 *
 * @param array $sections       Sections.
 *
 * @return array
 */
function snax_admin_settings_sections_collections( $sections ) {
	$sections['snax_settings_collections'] = array(
		'title'    => esc_html_x( 'Built-in Collections', 'Collections settings page', 'snax' ),
		'callback' => 'snax_admin_settings_collections_section_description',
		'page'      => 'snax-collections-settings',
	);

	return $sections;
}
/**
 * Register section fields
 *
 * @param array $fields     Fields.
 *
 * @return array
 */
function snax_admin_settings_fields_collections( $fields ) {
	$fields['snax_settings_collections'] = array();

	/*
	 * Abstract collections.
	 */

	$abstract_collections = snax_get_abstract_collections();

	foreach( $abstract_collections as $collection_slug => $collection_config ) {
		$fields['snax_settings_collections']['snax_abstract_collection_' . $collection_slug] = array(
			'title'             => $collection_config['title'],
			'callback'          => 'snax_admin_setting_callback_abstract_collection',
			'sanitize_callback' => 'intval',
			'args'              => array(
				'slug' => $collection_slug,
			),
		);
	}

	/*
	 * Custom collection.
	 */
	$fields['snax_settings_collections']['snax_custom_collection'] = array(
		'title'             => _x( 'Custom Collection', 'Settings page', 'snax' ),
		'callback'          => 'snax_admin_setting_callback_custom_collection',
		'sanitize_callback' => 'intval',
		'args'              => array(),
	);

	/*
	 * Common.
	 */

	$fields['snax_settings_collections']['snax_collections_settings_header'] = array(
		'title'             => '<h2>' . esc_html_x( 'Misc', 'Settings page', 'snax' ) . '</h2>',
		'callback'          => '__return_empty_string',
		'sanitize_callback' => 'intval',
		'args'              => array(),
	);

	$fields['snax_settings_collections']['snax_collections_posts_per_page'] = array(
		'title'             => esc_html_x( 'Posts per page', 'Collections settings page', 'snax' ),
		'callback'          => 'snax_admin_setting_callback_collections_posts_per_page',
		'sanitize_callback' => 'intval',
		'args'              => array(),
	);

	return $fields;
}


function snax_admin_collections_settings() {
	?>
	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( esc_html_x( 'Collections', 'Settings tab title', 'snax' ) ); ?></h2>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-collections-settings' ); ?>
			<?php do_settings_sections( 'snax-collections-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * Collection admin actions (create etc.)
 */
function snax_collections_handle_admin_actions() {
	$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

	if ( 'snax-collections-settings' !== $page ) {
		return;
	}

	// Activate collection.
	$slug = filter_input( INPUT_GET, 'activate-collection', FILTER_SANITIZE_STRING );

	if ( empty( $slug ) ) {
		return;
	}

	$custom_collection_config = snax_get_custom_collection_config();

	// Custom.
	if ( $custom_collection_config['slug'] === $slug ) {
		$collection_id = snax_get_activated_custom_collection();

		// Collection is already activated.
		if ( $collection_id > 0 && snax_is_collection( $collection_id ) ) {
			return;
		}

		$class_name = $custom_collection_config['class'];
		$args = array(
			'user_id'   => get_current_user_id(),
			'title'     => $custom_collection_config['title'],
			'args'      => array(
				'slug' => $custom_collection_config['slug'],
			),
		);

		$collection_obj = snax_create_collection_by_class_name( $class_name, $args );

		if ( ! is_wp_error( $collection_obj ) ) {
			update_option( 'snax_activated_custom_collection', $collection_obj->get_id() );
			flush_rewrite_rules();
		}

	// Abstract.
	} else {

		$abstract_collection = snax_get_abstract_collection( $slug );

		// Can't activate. Collection is not registered.
		if ( ! $abstract_collection ) {
			return;
		}

		$activated = snax_get_activated_abstract_collections();
		$collection_id = isset( $activated[ $slug ] ) ? (int) $activated[ $slug ] : 0;

		// Collection is already activated.
		if ( $collection_id > 0 && snax_is_collection( $collection_id ) ) {
			return;
		}

		$class_name = $abstract_collection['class'];
		$args = array(
			'user_id'   => get_current_user_id(),
			'title'     => $abstract_collection['title'],
			'args'      => array(
				'slug'      => $abstract_collection['slug'],
				'content'   => snax_get_collection_intro_shortcode(),
			),
		);

		$collection_obj = snax_create_collection_by_class_name( $class_name, $args );

		if ( ! is_wp_error( $collection_obj ) ) {
			snax_activate_abstract_collection( $abstract_collection['slug'], $collection_obj->get_id() );
		}
	}
}

/**
 * Render section description
 */
function snax_admin_settings_collections_section_description() {}

/**
 * Abstract collections settings
 */
function snax_admin_setting_callback_abstract_collection( $args ) {
	$slug                    = $args['slug'];
	$activated               = snax_get_activated_abstract_collections();
	$activated_collection_id = ! empty( $activated[ $slug ] ) ? $activated[ $slug ] : 0;
	$collection              = get_post( $activated_collection_id );

	// Invalid id.
	if ( ! snax_is_collection( $collection )  ) {
		$activated_collection_id = 0;
	}
	?>
	<div class="snax-collection-status">
		<?php if ( empty( $activated_collection_id ) ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'activate-collection', $slug ) ); ?>" class="button-secondary"><?php esc_html_e( 'Activate', 'snax' ); ?></a>

		<?php else : ?>
			<?php $status = get_post_status( $activated_collection_id ); ?>
			<?php if ( 'publish' === $status ) : ?>
				<strong class="snax-collection-status-on"><?php echo esc_html_x( 'Activated', 'Collection status', 'snax' ); ?></strong>
			<?php else : ?>
				<?php echo esc_html( ucfirst( $status ) ); ?>
			<?php endif; ?>
			<a href="<?php echo esc_url( get_permalink( $activated_collection_id ) ); ?>" class="button-secondary" target="_blank"><?php esc_html_e( 'View', 'snax' ); ?></a>
			<a href="<?php echo esc_url( get_edit_post_link( $activated_collection_id ) ); ?>" class="button-secondary" target="_blank"><?php esc_html_e( 'Edit', 'snax' ); ?></a>

		<?php endif; ?>
	</div>
	<?php
}

/**
 * Custom collection settings
 */
function snax_admin_setting_callback_custom_collection( $args ) {
	$activated_collection_id = snax_get_activated_custom_collection();
	$collection              = get_post( $activated_collection_id );

	// Invalid id.
	if ( ! snax_is_collection( $collection )  ) {
		$activated_collection_id = 0;
	}

	$config = snax_get_custom_collection_config();
	?>
	<div class="snax-collection-status">
		<?php if ( empty( $activated_collection_id ) ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'activate-collection', $config['slug'] ) ); ?>" class="button-secondary"><?php esc_html_e( 'Activate', 'snax' ); ?></a>
		<?php else : ?>
			<?php $status = get_post_status( $activated_collection_id ); ?>
			<?php if ( 'publish' === $status ) : ?>
				<strong class="snax-collection-status-on"><?php echo esc_html_x( 'Activated', 'Collection status', 'snax' ); ?></strong>
			<?php else : ?>
				<?php echo esc_html( ucfirst( $status ) ); ?>
			<?php endif; ?>
			<a href="<?php echo esc_url( get_permalink( $activated_collection_id ) ); ?>" class="button-secondary" target="_blank"><?php esc_html_e( 'View', 'snax' ); ?></a>
			<a href="<?php echo esc_url( get_edit_post_link( $activated_collection_id ) ); ?>" class="button-secondary" target="_blank"><?php esc_html_e( 'Edit', 'snax' ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Posts per page
 */
function snax_admin_setting_callback_collections_posts_per_page() {
	?>
	<input name="snax_collections_posts_per_page" id="snax_collections_posts_per_page" type="number" size="5" value="<?php echo esc_attr( snax_get_collections_posts_per_page() ); ?>" />
	<?php
}
