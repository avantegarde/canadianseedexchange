<?php
/**
 * Options Page for YouTube
 *
 * @package AdAce
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'g1_socials_options_tabs', 'g1_socials_youtube_add_options_tab' );
add_action( 'admin_menu', 'g1_socials_add_youtube_options_sections_and_fields' );

/**
 * Add Options Tab
 */
function g1_socials_youtube_add_options_tab( $tabs = array() ) {
	$tabs['g1_socials_youtube'] = array(
		'path'     => add_query_arg( array(
			'page' => g1_socials_options_page_slug(),
			'tab'  => 'g1_socials_youtube',
		), '' ),
		'label'    => esc_html__( 'YouTube', 'g1_socials' ),
		'settings' => 'g1_socials_youtube',
	);
	return $tabs;
}

/**
 * Add options page sections, fields and options.
 */
function g1_socials_add_youtube_options_sections_and_fields() {
	// Add setting section.
	add_settings_section(
		'g1_socials_youtube', // Section id.
		'', // Section title.
		'', // Section renderer callback with args pass.
		'g1_socials_youtube' // Page.
	);
	// Add setting field.
	add_settings_field(
		'g1_socials_youtube_api_key', // Field ID.
		esc_html( 'YouTube Data API Key', 'g1_socials' ), // Field title.
		'g1_socials_options_youtube_fields_renderer_callback', // Callback.
		'g1_socials_youtube', // Page.
		'g1_socials_youtube', // Section.
		array(
			'field_for' => 'g1_socials_youtube_api_key',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_youtube', // Option group.
		'g1_socials_youtube_api_key' // Option name.
		//'g1_socials_youtube_options_save_validator' // Options saving validator.
	);
}

/**
 * Options fields renderer.
 *
 * @param array $args Field arguments.
 */
function g1_socials_options_youtube_fields_renderer_callback( $args ) {
	// Action to render outside fields. For example other plugins supported fields.
	do_action( 'g1_socials_options_sponsor_field_renderer_action', $args );
	// Switch field.
	switch ( $args['field_for'] ) {
		case 'g1_socials_youtube_api_key':
			$option = get_option( $args['field_for'] );
		?>
		<input class="widefat" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( esc_html( $option ) ); ?>" />
		<?php
		break;
	}
}

/**
 * Options validator.
 *
 * @param array $input Saved options.
 * @return array Sanitised options for save.
 */
function g1_socials_youtube_options_save_validator( $input ) {
	// Return.
	return apply_filters(
		'g1_socials_youtube_options_save_validator_filter',
		filter_var( $input, FILTER_SANITIZE_STRING )
	);
}
