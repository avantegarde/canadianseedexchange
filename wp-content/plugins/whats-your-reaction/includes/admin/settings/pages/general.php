<?php
/**
 * General Settings page
 *
 * @package whats-your-reaction
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'wyr_settings_pages', 'wyr_register_general_settings_page', 10 );

function wyr_get_general_settings_page_id() {
	return apply_filters( 'wyr_general_settings_page_id', 'wyr-general-settings' );
}

function wyr_get_general_settings_page_config() {
	return apply_filters( 'wyr_general_settings_config', array(
		'tab_title'                 => _x( 'General', 'Settings Page', 'wyr' ),
		'page_title'                => '',
		'page_description_callback' => 'wyr_general_settings_page_description',
		'page_callback'             => 'wyr_general_settings_page',
		'fields'                    => array(
			'wyr_member_profile_link' => array(
				'title'             => _x( 'Show Member Profile Link', 'Settings Page', 'wyr' ),
				'callback'          => 'wyr_setting_member_profile_link',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function wyr_register_general_settings_page( $pages ) {
	$pages[ wyr_get_general_settings_page_id() ] = wyr_get_general_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function wyr_general_settings_page_description() {}

/**
 * Settings page
 */
function wyr_general_settings_page() {
	$page_id        = wyr_get_general_settings_page_id();
	$page_config    = wyr_get_general_settings_page_config();
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'What\'s Your Reaction Settings', 'wyr' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php wyr_admin_settings_tabs( $page_config['tab_title'] ); ?></h2>
		<form action="options.php" method="post">

			<?php settings_fields( $page_id ); ?>
			<?php do_settings_sections( $page_id ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'wyr' ); ?>" />
			</p>

		</form>
	</div>

	<?php
}

function wyr_setting_member_profile_link() {
	$checked = wyr_member_profile_link();
	?>
	<input type="checkbox" name="wyr_member_profile_link" id="wyr_member_profile_link" value="standard"<?php checked( $checked, true ); ?> />
	<?php
}