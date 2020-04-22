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
add_filter( 'snax_settings_pages',              'snax_settings_pages_slog', 10, 2 );
add_action( 'snax_hide_submenu_page',           'snax_hide_submenu_page_slog' );

// Register navigation.
add_filter( 'snax_get_admin_settings_tabs',     'snax_get_admin_settings_tabs_slog' );
add_filter( 'snax_settings_menu_highlight',     'snax_settings_menu_highlight_slog' );

// Register sections and fields.
add_action( 'admin_init',                       'snax_register_slog_admin_settings' );

/**
 * Register a page
 *
 * @param array  $hooks             Registered pages.
 * @param string $capability        Capability name.
 *
 * @return array
 */
function snax_settings_pages_slog( $hooks, $capability ) {
	// General.
	$hooks[] = add_options_page(
		__( 'Snax Social Login', 'snax' ),
		__( 'Snax Social Login', 'snax' ),
		$capability,
		'snax-slog-settings',
		'snax_admin_slog_settings'
	);

	// Networks.
	$hooks[] = add_options_page(
		__( 'Snax Social Login', 'snax' ),
		__( 'Snax Social Login', 'snax' ),
		$capability,
		'snax-slog-networks-settings',
		'snax_admin_slog_networks_settings'
	);

	// Locations.
	$hooks[] = add_options_page(
		__( 'Snax Social Login', 'snax' ),
		__( 'Snax Social Login', 'snax' ),
		$capability,
		'snax-slog-locations-settings',
		'snax_admin_slog_locations_settings'
	);

	// Log.
	$hooks[] = add_options_page(
		__( 'Snax Social Login', 'snax' ),
		__( 'Snax Social Login', 'snax' ),
		$capability,
		'snax-slog-log-settings',
		'snax_admin_slog_log_settings'
	);

	// GDPR.
	$hooks[] = add_options_page(
		__( 'Snax Social Login', 'snax' ),
		__( 'Snax Social Login', 'snax' ),
		$capability,
		'snax-slog-gdpr-settings',
		'snax_admin_slog_gdpr_settings'
	);

	return $hooks;
}

/**
 * Render General settings form
 */
function snax_admin_slog_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( __( 'Social Login', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'social-login', _x( 'General', 'Social Login', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-slog-settings' ); ?>
			<?php do_settings_sections( 'snax-slog-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * Render Networks settings form
 */
function snax_admin_slog_networks_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( __( 'Social Login', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'social-login', _x( 'Networks', 'Social Login', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-slog-networks-settings' ); ?>
			<?php do_settings_sections( 'snax-slog-networks-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * Render Locations settings form
 */
function snax_admin_slog_locations_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( __( 'Social Login', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'social-login', _x( 'Locations', 'Social Login', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-slog-locations-settings' ); ?>
			<?php do_settings_sections( 'snax-slog-locations-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * Render Log settings form
 */
function snax_admin_slog_log_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( __( 'Social Login', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'social-login', _x( 'Log', 'Social Login', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-slog-log-settings' ); ?>
			<?php do_settings_sections( 'snax-slog-log-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * Render GDPR settings form
 */
function snax_admin_slog_gdpr_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( __( 'Social Login', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'social-login', _x( 'GDPR', 'Social Login', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-slog-gdpr-settings' ); ?>
			<?php do_settings_sections( 'snax-slog-gdpr-settings' ); ?>

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
function snax_hide_submenu_page_slog( $settings_page ) {
	remove_submenu_page( $settings_page, 'snax-slog-settings' );
	remove_submenu_page( $settings_page, 'snax-slog-networks-settings' );
	remove_submenu_page( $settings_page, 'snax-slog-locations-settings' );
	remove_submenu_page( $settings_page, 'snax-slog-log-settings' );
	remove_submenu_page( $settings_page, 'snax-slog-gdpr-settings' );
}

/**
 * Register a navigation tab
 *
 * @param array $tabs       Registered tabs.
 *
 * @return array
 */
function snax_get_admin_settings_tabs_slog( $tabs ) {
	$tabs['social-login'] = array(
		'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-slog-settings' ), 'admin.php' ) ),
		'name'  => __( 'Social Login', 'snax' ),
		'order' => 145,
		'subtabs'   => array(
			'general' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-slog-settings' ), 'admin.php' ) ),
				'name'  => _x( 'General', 'Social Login', 'snax' ),
				'order' => 10,
			),
			'networks' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-slog-networks-settings' ), 'admin.php' ) ),
				'name'  => _x( 'Networks', 'Social Login', 'snax' ),
				'order' => 20,
			),
			'locations' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-slog-locations-settings' ), 'admin.php' ) ),
				'name'  => _x( 'Locations', 'Social Login', 'snax' ),
				'order' => 30,
			),
			'gdpr' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-slog-gdpr-settings' ), 'admin.php' ) ),
				'name'  => _x( 'GDPR', 'Social Login', 'snax' ),
				'order' => 40,
			),
			'log' => array(
				'href'  => snax_admin_url( add_query_arg( array( 'page' => 'snax-slog-log-settings' ), 'admin.php' ) ),
				'name'  => _x( 'Log', 'Social Login', 'snax' ),
				'order' => 50,
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
function snax_settings_menu_highlight_slog( $pages ) {
	$pages[] = 'snax-slog-settings';
	$pages[] = 'snax-slog-networks-settings';
	$pages[] = 'snax-slog-locations-settings';
	$pages[] = 'snax-slog-log-settings';
	$pages[] = 'snax-slog-gdpr-settings';

	return $pages;
}

function snax_register_slog_admin_settings() {

	// Section: General.

	add_settings_section(
		'snax_settings_slog_general',                                           // Section id.
		esc_html_x( 'General', 'Social Login Settings', 'snax' ),               // Section title.
		'',                                                                     // Section renderer callback with args pass.
		'snax-slog-settings'                                                    // Settings page.
	);

	// Field: General > Enabled.

	add_settings_field(
		'snax_slog_enabled',                                                    // Field ID.
		esc_html_x( 'Enabled', 'Social Login Settings', 'snax' ),               // Field title.
		'snax_admin_setting_slog_enabled',                                      // Callback.
		'snax-slog-settings',                                                   // Settings page.
		'snax_settings_slog_general'                                            // Section.
	);

	register_setting(
		'snax-slog-settings',                                                   // Settings page.
		'snax_slog_enabled'                                                     // Option name.
	);

	// Section: Networks.

	add_settings_section(
		'snax_settings_slog_providers',                                         // Section id.
		esc_html_x( 'Networks', 'Social Login Settings', 'snax' ),              // Section title.
		'snax_admin_setting_slog_module_disabled_info',                        // Section renderer callback with args pass.
		'snax-slog-networks-settings'                                          // Settings page.
	);

	if ( snax_slog_enabled() ) {
		$providers        = snax_slog_get_providers();
		$active_providers = snax_slog_get_active_providers();
		$providers_config = snax_slog_get_providers_creds();

		foreach ( $providers as $provider => $provider_config ) {

			// Field: Network > Name.


			add_settings_field(
				'snax_slog_providers_name[' . $provider . '][name]',                // Field ID.
				'<h3><img class="snax-admin-social-login-provider-icon" width="24" height="24" src="' . esc_url( $provider_config['icon'] ) . '" alt="" />' . $provider_config['name'] . '</h3>', // Field title.
				'__return_empty_string',                                            // Callback.
				'snax-slog-networks-settings',                                      // Settings page.
				'snax_settings_slog_providers'                                      // Section.
			);

			// Field: Network > Enabled.

			add_settings_field(
				'snax_slog_active_providers[' . $provider . ']',         // Field ID.
				esc_html_x( 'Enabled', 'Social Login Settings', 'snax' ),           // Field title.
				'snax_admin_setting_slog_provider_enabled',                         // Callback.
				'snax-slog-networks-settings',                                      // Settings page.
				'snax_settings_slog_providers',                                     // Section.
				array(
					'provider' => $provider,                                         // Data for callback.
					'enabled'  => in_array( $provider, $active_providers ),
				)
			);

			// Field: Network > Application ID.

			add_settings_field(
				'snax_slog_providers_creds[' . $provider . '][app_id]',             // Field ID.
				esc_html_x( 'Application ID', 'Social Login Settings', 'snax' ),    // Field title.
				'snax_admin_setting_slog_provider_config',                          // Callback.
				'snax-slog-networks-settings',                                      // Settings page.
				'snax_settings_slog_providers',                                     // Section.
				array(
					'provider' => $provider,                                        // Data for callback.
					'field'    => 'app_id',
					'value'    => isset( $providers_config[ $provider ] ) ? $providers_config[ $provider ]['app_id'] : '',
				)
			);

			// Field: Network > Application Secret.

			add_settings_field(
				'snax_slog_providers_creds[' . $provider . '][app_secret]',         // Field ID.
				esc_html_x( 'Application Secret', 'Social Login Settings', 'snax' ),// Field title.
				'snax_admin_setting_slog_provider_config',                          // Callback.
				'snax-slog-networks-settings',                                      // Settings page.
				'snax_settings_slog_providers',                                     // Section.
				array(
					'provider' => $provider,                                        // Data for callback.
					'field'    => 'app_secret',
					'value'    => isset( $providers_config[ $provider ] ) ? $providers_config[ $provider ]['app_secret'] : '',
				)
			);
		}

		register_setting(
			'snax-slog-networks-settings',                                          // Settings page.
			'snax_slog_providers_creds'                                             // Option name.
		);

		register_setting(
			'snax-slog-networks-settings',                                          // Settings page.
			'snax_slog_active_providers'                                            // Option name.
		);
	}

	// Section: Locations.

	add_settings_section(
		'snax_settings_slog_locations',                                         // Section id.
		esc_html_x( 'Locations', 'Social Login Settings', 'snax' ),             // Section title.
		'snax_admin_setting_slog_module_disabled_info',                         // Section renderer callback with args pass.
		'snax-slog-locations-settings'                                          // Settings page.
	);

	if ( snax_slog_enabled() ) {
		// Field: Locations > Login popup.

		add_settings_field(
			'snax_slog_location_popup_tpl',                                         // Field ID.
			esc_html_x( 'Snax Login Popup', 'Social Login Settings', 'snax' ),           // Field title.
			'snax_admin_setting_slog_location_popup_tpl',                           // Callback.
			'snax-slog-locations-settings',                                         // Settings page.
			'snax_settings_slog_locations'                                          // Section.
		);

		register_setting(
			'snax-slog-locations-settings',                                         // Settings page.
			'snax_slog_location_popup_tpl'                                          // Option name.
		);

		// Field: Locations > Register page.

		add_settings_field(
			'snax_slog_location_register_page_tpl',                                 // Field ID.
			esc_html_x( 'BuddyPress Register Page', 'Social Login Settings', 'snax' ),           // Field title.
			'snax_admin_setting_slog_location_register_page_tpl',                   // Callback.
			'snax-slog-locations-settings',                                         // Settings page.
			'snax_settings_slog_locations'                                          // Section.
		);

		register_setting(
			'snax-slog-locations-settings',                                         // Settings page.
			'snax_slog_location_register_page_tpl'                                  // Option name.
		);

		// Field: Locations > Login form.

		add_settings_field(
			'snax_slog_location_login_form_tpl',                                    // Field ID.
			esc_html_x( 'bbPress Login Widget', 'Social Login Settings', 'snax' ),            // Field title.
			'snax_admin_setting_slog_location_login_form_tpl',                      // Callback.
			'snax-slog-locations-settings',                                         // Settings page.
			'snax_settings_slog_locations'                                          // Section.
		);

		register_setting(
			'snax-slog-locations-settings',                                         // Settings page.
			'snax_slog_location_login_form_tpl'                                   // Option name.
		);
	}

	// Section: Log

	add_settings_section(
		'snax_settings_slog_log',                                           // Section id.
		esc_html_x( 'Log', 'Social Login Settings', 'snax' ),               // Section title.
		'snax_admin_setting_slog_module_disabled_info',                     // Section renderer callback with args pass.
		'snax-slog-log-settings'                                            // Settings page.
	);

	if ( snax_slog_enabled() ) {
		// Field: Log > Debug mode.

		add_settings_field(
			'snax_slog_debug_mode',                                                 // Field ID.
			esc_html_x( 'Debug mode', 'Social Login Settings', 'snax' ),            // Field title.
			'snax_admin_setting_slog_debug_mode',                                   // Callback.
			'snax-slog-log-settings',                                               // Settings page.
			'snax_settings_slog_log'                                                // Section.
		);

		register_setting(
			'snax-slog-log-settings',                                               // Settings page.
			'snax_slog_debug_mode'                                                  // Option name.
		);

		if ( snax_slog_debug_mode_enabled() ) {

			// Field: Log > Entries.

			add_settings_field(
				'snax_slog_log_entries',                                            // Field ID.
				esc_html_x( 'Entries', 'Social Login Settings', 'snax' ),           // Field title.
				'snax_admin_setting_slog_log_entries',                              // Callback.
				'snax-slog-log-settings',                                           // Settings page.
				'snax_settings_slog_log'                                            // Section.
			);
		}
	}

	// Section: GDPR.

	add_settings_section(
		'snax_settings_slog_gdpr',                                          // Section id.
		esc_html_x( 'GDPR', 'Social Login Settings', 'snax' ),              // Section title.
		'snax_admin_setting_slog_module_disabled_info',                     // Section renderer callback with args pass.
		'snax-slog-gdpr-settings'                                           // Settings page.
	);

	if ( snax_slog_enabled() ) {
		// Field: GDPR > Enabled.

		add_settings_field(
			'snax_slog_gdpr_enabled',                                               // Field ID.
			esc_html_x( 'Enabled', 'Social Login Settings', 'snax' ),               // Field title.
			'snax_admin_setting_slog_gdpr_enabled',                                 // Callback.
			'snax-slog-gdpr-settings',                                              // Settings page.
			'snax_settings_slog_gdpr'                                               // Section.
		);

		register_setting(
			'snax-slog-gdpr-settings',                                              // Settings page.
			'snax_slog_gdpr_enabled'                                                // Option name.
		);

		if ( snax_slog_gdpr_enabled() ) {

			// Field: GDPR > Consent text.

			add_settings_field(
				'snax_slog_gdpr_consent_text',                                      // Field ID.
				esc_html_x( 'Consent text', 'Social Login Settings', 'snax' ),      // Field title.
				'snax_admin_setting_slog_gdpr_consent_text',                        // Callback.
				'snax-slog-gdpr-settings',                                          // Settings page.
				'snax_settings_slog_gdpr'                                           // Section.
			);

			register_setting(
				'snax-slog-gdpr-settings',                                          // Settings page.
				'snax_slog_gdpr_consent_text'                                       // Option name.
			);
		}
	}
}

/**
 * Render the Enabled field
 */
function snax_admin_setting_slog_enabled() {
	$enabled = snax_slog_enabled();

	?>
	<input type="checkbox" id="snax_slog_enabled" name="snax_slog_enabled" value="standard" <?php checked( true, $enabled ); ?> />
	<?php
	do_action( 'snax_after_snax_admin_setting_slog_enabled' );
}

/**
 * Render the Popup location field
 */
function snax_admin_setting_slog_location_popup_tpl() {
	$value = snax_slog_location_popup_tpl();

	?>
	<select id="snax_slog_location_popup_tpl" name="snax_slog_location_popup_tpl">
		<option value="icons"<?php selected( $value, 'icons' ) ?>><?php echo esc_html_x( 'icons', 'Social Login Settings', 'snax' ); ?></option>
		<option value="buttons"<?php selected( $value, 'buttons' ) ?>><?php echo esc_html_x( 'buttons', 'Social Login Settings', 'snax' ); ?></option>
	</select>
	<?php
}

/**
 * Render the Register Page location field
 */
function snax_admin_setting_slog_location_register_page_tpl() {
	$value = snax_slog_location_register_page_tpl();
	$is_bp_active = snax_can_use_plugin( 'buddypress/bp-loader.php' );
	$title = $is_bp_active ? '' : esc_html_x( 'Activate BuddyPress plugin to use this option', 'Social Login Settings', 'snax' );
	?>
	<select id="snax_slog_location_register_page_tpl" name="snax_slog_location_register_page_tpl" title="<?php echo esc_attr( $title ); ?>" <?php disabled( $is_bp_active, false ); ?>>
		<option value="icons"<?php selected( $value, 'icons' ) ?>><?php echo esc_html_x( 'icons', 'Social Login Settings', 'snax' ); ?></option>
		<option value="buttons"<?php selected( $value, 'buttons' ) ?>><?php echo esc_html_x( 'buttons', 'Social Login Settings', 'snax' ); ?></option>
	</select>
	<?php
}

/**
 * Render the Login Widget location field
 */
function snax_admin_setting_slog_location_login_form_tpl() {
	$value = snax_slog_location_login_form_tpl();
	?>
	<select id="snax_slog_location_login_form_tpl" name="snax_slog_location_login_form_tpl">
		<option value="icons"<?php selected( $value, 'icons' ) ?>><?php echo esc_html_x( 'icons', 'Social Login Settings', 'snax' ); ?></option>
		<option value="buttons"<?php selected( $value, 'buttons' ) ?>><?php echo esc_html_x( 'buttons', 'Social Login Settings', 'snax' ); ?></option>
	</select>
	<?php
}

/**
 * Render the Debug Mode field
 */
function snax_admin_setting_slog_debug_mode() {
	$enabled = snax_slog_debug_mode_enabled();

	?>
	<input type="checkbox" id="snax_slog_debug_mode" name="snax_slog_debug_mode" value="standard" <?php checked( true, $enabled ); ?> />
	<?php
}

function snax_admin_setting_slog_module_disabled_info() {
	if ( snax_slog_enabled() ) {
		return;
	}

	$url = snax_admin_url( add_query_arg( array( 'page' => 'snax-slog-settings' ), 'admin.php' ) );

	echo wp_kses_post( sprintf( _x( 'Social Login is disabled. Please enable it in the <a href="%s">General</a> tab to use this section', 'Social Login Settings', 'snax' ), $url ) );
}

/**
 * Render the Enabled field for a provider
 *
 * @param array $args   Field arguments.
 */
function snax_admin_setting_slog_provider_enabled( $args ) {
	$provider = $args['provider'];
	$enabled  = $args['enabled'];

	$field_id   = sprintf( 'snax_slog_active_providers_%s', $provider );
	$field_name = 'snax_slog_active_providers[]';

	?>
	<input type="checkbox" id="<?php echo( esc_attr( $field_id ) ); ?>" name="<?php echo( esc_attr( $field_name ) ); ?>" value="<?php echo esc_attr( $provider ) ?>" <?php checked( true, $enabled ); ?> />
	<?php
}

/**
 * Render the Provider config fields
 *
 * @param array $args   Field arguments.
 */
function snax_admin_setting_slog_provider_config( $args ) {
	$provider = $args['provider'];
	$field    = $args['field'];
	$value    = $args['value'];

	$field_id         = sprintf( 'snax_slog_providers_creds_%s_%s', $provider, $field );
	$field_name       = sprintf( 'snax_slog_providers_creds[%s][%s]', $provider, $field );
	$provider_id      = strtolower( $provider );
	$provider_doc_url = sprintf( 'http://docs.bimber.bringthepixel.com/articles/community/login-with-social-networks/index.html#%s', $provider_id );

	switch ( $field ) {
		case 'app_id':
			?>
			<input class="widefat code" size="100" type="text" id="<?php echo( esc_attr( $field_id ) ); ?>" name="<?php echo( esc_attr( $field_name ) ); ?>" value="<?php echo( esc_attr( $value ) ); ?>" />
			<p class="description">
				<a href="#" onclick="jQuery('#snax-slog-provider-setup-<?php echo sanitize_html_class( $provider_id ); ?>').toggle(); return false;">
					<?php echo esc_attr_x( 'Where do I get this info?', 'Social Login Settings', 'snax' ); ?>
				</a>
			</p>
			<?php
			break;

		case 'app_secret':
			?>
			<input class="widefat code" size="100" type="text" id="<?php echo( esc_attr( $field_id ) ); ?>" name="<?php echo( esc_attr( $field_name ) ); ?>" value="<?php echo( esc_attr( $value ) ); ?>" />
			<p class="description">
				<a href="#" onclick="jQuery('#snax-slog-provider-setup-<?php echo sanitize_html_class( $provider_id ); ?>').toggle(); return false;">
					<?php esc_attr_e( 'Where do I get this info?', 'Social Login Settings', 'snax' ); ?>
				</a>
			</p>
			<div id="snax-slog-provider-setup-<?php echo sanitize_html_class( $provider_id ); ?>" style="display: none; margin-top: 10px;">
				<p class="snax-slog-provider-setup-doc">
					<?php printf( wp_kses_post( _x( 'To get the required credentials, you will need to register a new %s API Application. This step-by-step <a href="%s" target="_blank">guide</a> will walk you through the whole process.', 'Social Login Settings', 'snax' ) ), esc_html( $provider ), esc_url( $provider_doc_url ) ); ?>
				</p>
				<p class="snax-slog-provider-setup-details">
					<?php snax_get_template_part( 'social-login/'. $provider_id .'-setup' ); ?>
				</p>
			</div>
			<?php
			break;
	}
}

/**
 * Render debug log entries
 */
function snax_admin_setting_slog_log_entries() {
	$log = Snax_Social_Login_Logger::get_log();

	// Reverse order.
	$k  = array_keys( $log );
	$rk = array_reverse( $k );
	$v  = array_values( $log );
	$rv = array_reverse($v);

	$log = array_combine( $rk, $rv );

	if ( empty( $log ) ) {
		echo esc_html_x( 'Log is empty', 'Social Login Log', 'snax' );
		return;
	}

	?>
	<style type="text/css">
		.snax-slog-col-nr { width: 20px !important; }
		.snax-slog-col-time { width: 150px !important; }
		.snax-slog-col-type { width: 50px !important; }
		.snax-slog-col-message { width: 500px; !important; }

		.snax-slog-log-entries.snax-slog-hide {  display: none; }
		.snax-slog-log-entries.snax-slog-show {  display: table-row; }

		.snax-slog-log-entry-info {
			color: #31708f;
			background-color: #d9edf7;
		}

		.snax-slog-log-entry-error {
			color: #D8000C;
			background-color: #FFBABA;
		}

		.snax-slog-session-success {
			color: #4F8A10;
			background-color: #DFF2BF;
		}

		.snax-slog-session-failed {
			color: #D8000C;
			background-color: #FFBABA;
		}
	</style>
	<table>
		<thead>
			<tr>
				<th class="snax-slog-col-session"><?php echo esc_html_x( 'Session', 'Social Login Log', 'snax' ); ?></th>
				<th class="snax-slog-col-started-at"><?php echo esc_html_x( 'Started at', 'Social Login Log', 'snax' ); ?></th>
				<th class="snax-slog-col-status"><?php echo esc_html_x( 'Status', 'Social Login Log', 'snax' ); ?></th>
				<th class="snax-slog-col-actions"><?php echo esc_html_x( 'Actions', 'Social Login Log', 'snax' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $log as $session => $log_entries ): ?>
			<?php
			if ( empty( $log_entries ) ) {
				continue;
			}

			$first_log_entry = $log_entries[0];
			$last_log_entry_index = count( $log_entries ) - 1;
			$last_log_entry  = $log_entries[ $last_log_entry_index ];
			$status = $last_log_entry['type'] !== Snax_Social_Login_Logger::TYPE_ERROR ? 'SUCCESS' : 'FAILED';
			?>

			<tr class="snax-slog-session snax-slog-session-<?php echo sanitize_html_class( strtolower( $status ) ); ?>">
				<td class="snax-slog-col-session"><a href="#" class="snax-slog-session-toggle"><?php echo esc_html( $session ); ?></a></td>
				<td class="snax-slog-col-started-at"><?php echo esc_html( $first_log_entry['date'] ); ?></td>
				<td class="snax-slog-col-status"><?php echo esc_html( $status ); ?></td>
				<td class="snax-slog-col-actions"><a href="#" class="snax-slog-session-toggle"><?php echo esc_attr_x( 'Details', 'Social Login Log', 'snax' ); ?></a></td>
			</tr>
			<tr class="snax-slog-log-entries snax-slog-hide">
				<td colspan="4">
					<table>
						<thead>
							<tr>
								<th class="snax-slog-col-nr"><?php echo esc_html_x( '#', 'Social Login Log', 'snax' ); ?></th>
								<th class="snax-slog-col-time"><?php echo esc_html_x( 'Time', 'Social Login Log', 'snax' ); ?></th>
								<th class="snax-slog-col-type"><?php echo esc_html_x( 'Type', 'Social Login Log', 'snax' ); ?></th>
								<th class="snax-slog-col-message"><?php echo esc_html_x( 'Message', 'Social Login Log', 'snax' ); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach( $log_entries as $log_entry_index => $log_entry ): ?>
							<tr class="snax-slog-log-entry snax-slog-log-entry-<?php echo sanitize_html_class( strtolower( $log_entry['type'] ) ); ?>">
								<td class="snax-slog-col-nr"><?php echo intval( $log_entry_index + 1 ); ?></td>
								<td class="snax-slog-col-date"><?php echo esc_html( $log_entry['date'] ); ?></td>
								<td class="snax-slog-col-type"><?php echo esc_html( $log_entry['type'] ); ?></td>
								<td class="snax-slog-col-message"><?php echo esc_html( $log_entry['message'] ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<script type="text/javascript">
		(function($) {
			$('.snax-slog-session-toggle').on('click', function(e) {
				e.preventDefault();

				$(this).parents('tr').next('tr').toggleClass('snax-slog-hide snax-slog-show');
			});
		})(jQuery);
	</script>
	<?php
}

/**
 * Render the GDPR > Enabled field
 */
function snax_admin_setting_slog_gdpr_enabled() {
	$can_use = snax_can_use_plugin( 'wp-gdpr-compliance/wp-gdpr-compliance.php' );

	if ( ! $can_use ) {
		printf( _x( 'To enable the GDPR module, the <a href="%s" target="_blank">WP GDPR Compliance</a> has to be activated', 'Social Login Settings', 'snax' ), 'https://wordpress.org/plugins/wp-gdpr-compliance/' );
		return;
	}

	$enabled = snax_slog_gdpr_enabled();

	?>
	<input type="checkbox" id="snax_slog_gdpr_enabled" name="snax_slog_gdpr_enabled" value="standard" <?php checked( true, $enabled ); ?> />
	<?php
}

/**
 * Render GDPR > Consent text field
 */
function snax_admin_setting_slog_gdpr_consent_text() {
	$text = snax_slog_gdpr_consent_text();

	?>
	<input size="120" type="text" id="snax_slog_gdpr_consent_text" name="snax_slog_gdpr_consent_text" value="<?php echo wp_kses_post( $text ); ?>" /><br />
	<small>
		<?php printf( esc_html_x( 'Use the %s tag to add a link to the Privacy Policy page', 'Social Login Settings', 'snax' ), '<code>%privacy_policy%</code>' ); ?>
	</small>
	<br />
	<br />
	<p>
		<?php
		$privacy_policy_link = snax_gdpr_get_privacy_policy_link();
		$gdpr_settings_url   = admin_url( 'tools.php?page=wp_gdpr_compliance&type=settings' );

		if ( $privacy_policy_link ) {
			printf( esc_html_x( 'The Privacy Policy page is set and the %s tag will be replaced with: %s', 'Social Login Settings', 'snax' ), '<code>%privacy_policy%</code>', $privacy_policy_link );
			echo '<br />';
			echo wp_kses_post( sprintf( _x( 'You can change the page and the link text in the <a href="%s" target="_blank">GDPR plugin settings page</a>', 'Social Login Settings', 'snax' ), esc_url( $gdpr_settings_url ) ) );
		} else {
			echo wp_kses_post( sprintf( _x( 'To use the %s tag you have to select the Privacy Policy page in the <a href="%s" target="_blank">GDPR plugin settings page</a>', 'Social Login Settings', 'snax' ), '<code>%privacy_policy%</code>', esc_url( $gdpr_settings_url ) ) );
		}
		?>
	</p>
	<?php
}