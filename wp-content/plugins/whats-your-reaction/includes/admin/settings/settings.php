<?php
/**
 * Settings Functions
 *
 * @package whats-your-reaction
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class Wyr_Settings_Page {
	/**
	 * Admin capability
	 *
	 * @var string
	 */
	public $capability;

	/**
	 * Admin settings page
	 *
	 * @var string
	 */
	public $page;

	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_hooks();
	}

	/**
	 * Variables
	 */
	private function setup_globals() {
		// Main capability.
		$this->capability = wyr_get_capability();

		// Main settings page.
		$this->page = 'options-general.php';
	}

	/**
	 * Resources
	 */
	private function includes() {
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'navigation.php' );
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'sections.php' );

		// Pages.
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'pages/general.php' );
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'pages/fakes.php' );
	}

	/**
	 * Define all hooks
	 */
	private function setup_hooks() {
		add_action( 'admin_menu',           array( $this, 'add_page' ) );
		add_action( 'admin_head',           array( $this, 'hide_subpages' ) );
		add_action( 'admin_init',           array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_page() {
		$hooks = array();

		$settings_pages = wyr_get_settings_pages();

		foreach( $settings_pages as $page_id => $page_config ) {
			$hooks[] = add_options_page(
				__( 'What\'s Your Reaction', 'wyr' ),
				__( 'What\'s Your Reaction', 'wyr' ),
				$this->capability,
				$page_id,
				$page_config['page_callback']
			);
		}

		// Highlight Settings > What's Your Reaction menu item regardless of current tab.
		foreach ( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'wyr_admin_settings_menu_highlight' );
		}
	}

	/**
	 * Hide submenu items under the Settings section
	 */
	public function hide_subpages() {
		$pages = wyr_get_settings_pages();
		$index = 0;

		foreach( $pages as $page_id => $page ) {
			if ( 0 === $index++ ) {
				continue;
			}

			remove_submenu_page( $this->page, $page_id );
		}
	}

	/**
	 * Register settings
	 *
	 * @return void
	 */
	public function page_init() {
		// Bail if no sections available.
		$sections = wyr_admin_get_settings_sections();

		if ( empty( $sections ) ) {
			return;
		}

		// Loop through sections.
		foreach ( (array) $sections as $section_id => $section ) {

			// Only add section and fields if section has fields.
			$fields = wyr_admin_get_settings_fields_for_section( $section_id );

			if ( empty( $fields ) ) {
				continue;
			}

			$page = $section['page'];

			// Add the section.
			add_settings_section(
				$section_id,
				$section['title'],
				$section['callback'],
				$page
			);

			// Loop through fields for this section.
			foreach ( (array) $fields as $field_id => $field ) {

				// Add the field.
				if ( ! empty( $field['callback'] ) && ! empty( $field['title'] ) ) {
					add_settings_field(
						$field_id,
						$field['title'],
						$field['callback'],
						$page,
						$section_id,
						$field['args']
					);
				}

				// Register the setting.
				register_setting( $page, $field_id, $field['sanitize_callback'] );
			}
		}
	}
}

// Init.
new Wyr_Settings_Page();
