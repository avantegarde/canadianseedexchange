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

// Register pages.
add_filter( 'snax_settings_pages',              'snax_settings_pages_shares', 10, 2 );
add_action( 'snax_hide_submenu_page',           'snax_hide_submenu_page_shares' );

// Register navigation.
add_filter( 'snax_get_admin_settings_tabs',     'snax_get_admin_settings_tabs_shares' );
add_filter( 'snax_settings_menu_highlight',     'snax_settings_menu_highlight_shares' );

// Register sections and fields.
add_action( 'admin_init',                       'snax_register_shares_admin_settings' );

/**
 * Register a page
 *
 * @param array  $hooks             Registered pages.
 * @param string $capability        Capability name.
 *
 * @return array
 */
function snax_settings_pages_shares( $hooks, $capability ) {
	// General.
	$hooks[] = add_options_page(
		_x( 'Snax Shares', 'Shares Settings', 'snax' ),
		_x( 'Snax Shares', 'Shares Settings', 'snax' ),
		$capability,
		'snax-shares-settings',
		'snax_admin_shares_settings'
	);

	// Positions.
	$hooks[] = add_options_page(
		_x( 'Snax Shares', 'Shares Settings', 'snax' ),
		_x( 'Snax Shares', 'Shares Settings', 'snax' ),
		$capability,
		'snax-shares-positions-settings',
		'snax_admin_shares_positions_settings'
	);

	return $hooks;
}

/**
 * Render General settings form
 */
function snax_admin_shares_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( _x( 'Shares', 'Shares Settings', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'shares', _x( 'General', 'Shares Settings', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-shares-settings' ); ?>
			<?php do_settings_sections( 'snax-shares-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * Render Positions settings form
 */
function snax_admin_shares_positions_settings() {
	wp_enqueue_script( 'snax-shares-admin', snax()->includes_url . 'admin/assets/js/shares.js', array( 'jquery' ), snax()->version, true );
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( _x( 'Shares', 'Shares Settings', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'shares', _x( 'Positions', 'Shares Settings', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-shares-positions-settings' ); ?>
			<?php do_settings_sections( 'snax-shares-positions-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * @param string $settings_page     Settings page.
 */
function snax_hide_submenu_page_shares( $settings_page ) {
	remove_submenu_page( $settings_page, 'snax-shares-settings' );
	remove_submenu_page( $settings_page, 'snax-shares-positions-settings' );
}

/**
 * Register a navigation tab
 *
 * @param array $tabs       Registered tabs.
 *
 * @return array
 */
function snax_get_admin_settings_tabs_shares( $tabs ) {
	$tabs['shares'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-shares-settings' ), 'admin.php' ) ),
		'name'  => _x( 'Shares', 'Shares Settings', 'snax' ),
		'order' => 150,
		'subtabs'   => array(
			'general' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-shares-settings' ), 'admin.php' ) ),
				'name'  => _x( 'General', 'Shares Settings', 'snax' ),
				'order' => 10,
			),
			'positions' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-shares-positions-settings' ), 'admin.php' ) ),
				'name'  => _x( 'Positions', 'Shares Settings', 'snax' ),
				'order' => 20,
			),
		),
	);

	return $tabs;
}

/**
 * Register a page to be highlighted
 *
 * @param array $pages  Registered pages.
 *
 * @return array
 */
function snax_settings_menu_highlight_shares( $pages ) {
	$pages[] = 'snax-shares-settings';
	$pages[] = 'snax-shares-positions-settings';

	return $pages;
}

function snax_register_shares_admin_settings() {

	// Section: General.

	add_settings_section(
		'snax_settings_shares_general',                                           // Section id.
		esc_html_x( 'General', 'Shares Settings', 'snax' ),                       // Section title.
		'',                                                                       // Section renderer callback with args pass.
		'snax-shares-settings'                                                    // Settings page.
	);

	// Field: General > Enabled.

	add_settings_field(
		'snax_shares_enabled',                                                    // Field ID.
		esc_html_x( 'Enabled', 'Shares Settings', 'snax' ),                       // Field title.
		'snax_admin_setting_shares_enabled',                                      // Callback.
		'snax-shares-settings',                                                   // Settings page.
		'snax_settings_shares_general'                                            // Section.
	);

	register_setting(
		'snax-shares-settings',                                                   // Settings page.
		'snax_shares_enabled'                                                     // Option name.
	);

	// Field: General > Debug mode.

	add_settings_field(
		'snax_shares_debug_mode',                                                 // Field ID.
		esc_html_x( 'Debug mode', 'Shares Settings', 'snax' ),                    // Field title.
		'snax_admin_setting_shares_debug_mode',                                   // Callback.
		'snax-shares-settings',                                                   // Settings page.
		'snax_settings_shares_general'                                            // Section.
	);

	register_setting(
		'snax-shares-settings',                                                   // Settings page.
		'snax_shares_debug_mode'                                                  // Option name.
	);

	// Field: General > Facebook App ID

	add_settings_field(
		'snax_facebook_app_id',                                                    // Field ID.
		esc_html_x( 'Facebook App ID', 'Shares Settings', 'snax' ),                // Field title.
		'snax_admin_setting_shares_facebook_app_id',                               // Callback.
		'snax-shares-settings',                                                    // Settings page.
		'snax_settings_shares_general'                                             // Section.
	);

	register_setting(
		'snax-shares-settings',                                                    // Settings page.
		'snax_facebook_app_id'                                                     // Option name.
	);

	// Section: Positions.

	add_settings_section(
		'snax_settings_shares_positions',                                          // Section id.
		esc_html_x( 'Positions', 'Shares Settings', 'snax' ),                      // Section title.
		'snax_admin_setting_shares_module_disabled_info',                          // Section renderer callback with args pass.
		'snax-shares-positions-settings'                                           // Settings page.
	);

	if ( snax_shares_enabled() ) {
		$positions = snax_get_share_positions();

		foreach ( $positions as $position => $position_config ) {

			// Field: Position > Name.

			add_settings_field(
				'snax_share_position_name[' . $position . '][name]',                // Field ID.
				'<h3>' . $position_config['name'] . '</h3>',                        // Field title.
				'__return_empty_string',                                            // Callback.
				'snax-shares-positions-settings',                                   // Settings page.
				'snax_settings_shares_positions'                                    // Section.
			);

			// Field: Position > Enabled.

			add_settings_field(
				'snax_share_positions[active][' . $position . ']',                   // Field ID.
				esc_html_x( 'Enabled', 'Shares Settings', 'snax' ),                 // Field title.
				'snax_admin_setting_shares_position_enabled',                       // Callback.
				'snax-shares-positions-settings',                                   // Settings page.
				'snax_settings_shares_positions',                                   // Section.
				array(
					'position' => $position,                                        // Data for callback.
				)
			);

			// Field: Position > Networks.

			add_settings_field(
				'snax_share_positions[' . $position . ']',                          // Field ID.
				esc_html_x( 'Networks', 'Shares Settings', 'snax' ),                // Field title.
				'snax_admin_setting_shares_position_networks',                      // Callback.
				'snax-shares-positions-settings',                                   // Settings page.
				'snax_settings_shares_positions',                                   // Section.
				array(
					'position' => $position,                                        // Data for callback.
				)
			);

		}

		register_setting(
			'snax-shares-positions-settings',                                       // Settings page.
			'snax_share_positions'                                                  // Option name.
		);
	}
}

/**
 * Ask user to enable the module
 */
function snax_admin_setting_shares_module_disabled_info() {
	if ( snax_shares_enabled() ) {
		return;
	}

	$url = snax_admin_url( add_query_arg( array( 'page' => 'snax-shares-settings' ), 'admin.php' ) );

	echo wp_kses_post( sprintf( _x( 'Shares module is disabled. Please enable it in the <a href="%s">General</a> tab to use this section', 'Shares Settings', 'snax' ), $url ) );
}

/**
 * Render the Enabled field
 */
function snax_admin_setting_shares_enabled() {
	$enabled = snax_shares_enabled();

	?>
	<input type="checkbox" id="snax_shares_enabled" name="snax_shares_enabled" value="standard" <?php checked( true, $enabled ); ?> />
	<small>
		<?php echo esc_html_x( 'Uncheck to disable entire module and all share slots at once', 'Shares Settings', 'snax' ); ?>
	</small>
	<?php
}

/**
 * Render the Debug Mode field
 */
function snax_admin_setting_shares_debug_mode() {
	$enabled = snax_shares_debug_mode_enabled();

	?>
	<input type="checkbox" id="snax_shares_debug_mode" name="snax_shares_debug_mode" value="standard" <?php checked( true, $enabled ); ?> />
	<small>
		<?php echo esc_html_x( 'Check to log share details into the JavaScript console (Chrome Dev Tools, Firebug)', 'Shares Settings', 'snax' ); ?>
	</small>
	<?php
}

/**
 * Render the Facebook App Id field
 */
function snax_admin_setting_shares_facebook_app_id() {
	?>
	<input name="snax_facebook_app_id" id="snax_facebook_app_id" class="regular-text" type="text" size="5" value="<?php echo esc_attr( snax_get_facebook_app_id() ); ?>" />
	<small>
		<?php echo wp_kses_post( sprintf( __( 'How do I get my <strong>App ID</strong>? Use the <a href="%s" target="_blank">Register and Configure an App</a> guide for help.', 'snax' ), esc_url( 'https://developers.facebook.com/docs/apps/register' ) ) ); ?>
	</small>
	<?php
}

/**
 * Render the Enabled field for a position
 *
 * @param array $args   Field arguments.
 */
function snax_admin_setting_shares_position_enabled( $args ) {
	$position = $args['position'];
	$enabled  = snax_is_active_share_position( $position );

	$field_id   = sprintf( 'snax_share_positions_active_%s', $position );
	$field_name = 'snax_share_positions[active][]';

	?>
	<input type="checkbox" id="<?php echo( esc_attr( $field_id ) ); ?>" name="<?php echo( esc_attr( $field_name ) ); ?>" value="<?php echo esc_attr( $position ) ?>" <?php checked( true, $enabled ); ?> />
	<?php
}

/**
 * Render position networks
 *
 * @param array $args   Field arguments.
 */
function snax_admin_setting_shares_position_networks( $args ) {
	$position = $args['position'];

	// Field names.
	$networks_field       = sprintf( 'snax_share_positions[%s][networks][]', $position );
	$networks_order_field = sprintf( 'snax_share_positions[%s][networks_order]', $position );

	// Options.
	$position_active_networks = snax_get_share_position_active_networks( $position );
	$position_networks_order = snax_get_share_position_networks_order( $position );

	?>
	<ul class="snax-share-networks sortable">
	<?php

	foreach ( $position_networks_order as $network ) {
		$checked = in_array( $network, $position_active_networks, true );
		?>
		<li><input type="checkbox" class="snax-share-network" name="<?php echo esc_attr( $networks_field ) ?>" value="<?php echo esc_attr( $network ); ?>"<?php checked( $checked ) ?> /> <?php echo esc_html( ucfirst( $network ) ); ?></li>
		<?php
	}
	?>
	</ul>

	<input type="hidden" class="snax-share-networks-order" name="<?php echo esc_attr( $networks_order_field ) ?>" value="<?php echo esc_attr( implode( ',', $position_networks_order ) ); ?>" />
	<?php
}
