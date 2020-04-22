<?php
/**
 * Settings Navigation
 *
 * @package media-ace
 * @subpackage Settings
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Highlight the Settings > MediaAce main menu item regardless of which actual tab we are on.
 */
function mace_admin_settings_menu_highlight() {
	global $plugin_page, $submenu_file;

	$settings_pages = mace_get_settings_pages();

	$page_ids = array_keys( $settings_pages );

	if ( in_array( $plugin_page, $page_ids, true ) ) {
		// We want to map all subpages to one settings page (in main menu).
		$submenu_file = $page_ids[0];
	}
}

/**
 * Get tabs in the admin settings area.
 *
 * @param string $active_tab        Name of the tab that is active. Optional.
 *
 * @return string
 */
function mace_get_admin_settings_tabs( $active_tab = '' ) {
	$tabs           = array();
	$settings_pages = mace_get_settings_pages();

	foreach( $settings_pages as $page_id => $page_config ) {
		$tabs[] = array(
			'href' => mace_admin_url( add_query_arg( array( 'page' => $page_id ), 'admin.php' ) ),
			'name' => $page_config['tab_title'],
		);
	}

	return apply_filters( 'mace_get_admin_settings_tabs', $tabs, $active_tab );
}

/**
 * Output the tabs in the admin area.
 *
 * @param string $active_tab        Name of the tab that is active. Optional.
 */
function mace_admin_settings_tabs( $active_tab = '' ) {
	$tabs_html    = '';
	$idle_class   = 'nav-tab';
	$active_class = 'nav-tab nav-tab-active';

	/**
	 * Filters the admin tabs to be displayed.
	 *
	 * @param array $value      Array of tabs to output to the admin area.
	 */
	$tabs = apply_filters( 'mace_admin_settings_tabs', mace_get_admin_settings_tabs( $active_tab ) );

	// Loop through tabs and build navigation.
	foreach ( array_values( $tabs ) as $tab_data ) {
		$is_current = (bool) ( $tab_data['name'] === $active_tab );
		$tab_class  = $is_current ? $active_class : $idle_class;
		$tabs_html .= '<a href="' . esc_url( $tab_data['href'] ) . '" class="' . esc_attr( $tab_class ) . '">' . esc_html( $tab_data['name'] ) . '</a>';
	}

	echo filter_var( $tabs_html );

	do_action( 'mace_admin_tabs' );
}
