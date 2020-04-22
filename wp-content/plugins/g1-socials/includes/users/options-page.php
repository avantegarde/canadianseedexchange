<?php
/**
 * Options Page for Users
 *
 * @package G1 Socials
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'g1_socials_options_tabs', 'g1_socials_users_add_options_tab' );
add_action( 'admin_menu', 'g1_socials_add_users_options_sections_and_fields' );

/**
 * Add Options Tab
 */
function g1_socials_users_add_options_tab( $tabs = array() ) {
	$tabs['g1_socials_users'] = array(
		'path'     => add_query_arg( array(
			'page' => g1_socials_options_page_slug(),
			'tab'  => 'g1_socials_users',
		), '' ),
		'label'    => esc_html__( 'Users', 'g1_socials' ),
		'settings' => 'g1_socials_users',
	);
	return $tabs;
}

/**
 * Add options page sections, fields and options.
 */
function g1_socials_add_users_options_sections_and_fields() {
	// Add setting section.
	add_settings_section(
		'g1_socials_users', // Section id.
		'', // Section title.
		'g1_socials_options_users_description_renderer_callback', // Section renderer callback with args pass.
		'g1_socials_users' // Page.
	);

	// Consumer key.
	add_settings_field(
		'g1_socials_enable_user_profiles', // Field ID.
		__( 'Enable User Profiles', 'g1_socials' ), // Field title.
		'g1_socials_options_users_fields_renderer_callback', // Callback.
		'g1_socials_users', // Page.
		'g1_socials_users', // Section.
		array(
			'field_for' => 'g1_socials_enable_user_profiles',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_users', // Option group.
		'g1_socials_enable_user_profiles' // Option name.
	);


}

function g1_socials_options_users_description_renderer_callback() {}

/**
 * Options fields renderer.
 *
 * @param array $args Field arguments.
 */
function g1_socials_options_users_fields_renderer_callback( $args ) {
	switch ( $args['field_for'] ) {
		case 'g1_socials_enable_user_profiles':
			$option = get_option( $args['field_for'], 'standard' );
			?>
			<input type="checkbox" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="standard"<?php checked( 'standard', $option ); ?> />
			<?php
			break;
	}
}

