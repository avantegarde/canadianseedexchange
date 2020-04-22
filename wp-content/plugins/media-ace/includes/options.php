<?php
/**
 * Common Options
 *
 * @package media-ace
 * @subpackage Options
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'mace_settings_pages', 'mace_register_general_settings_page', 10 );

function mace_get_general_settings_page_id() {
	return apply_filters( 'mace_general_settings_page_id', 'mace-general-settings' );
}

function mace_get_general_settings_page_config() {
	return apply_filters( 'mace_general_settings_config', array(
		'tab_title'                 => __( 'Settings', 'mace' ),
		'page_title'                => __( 'Settings', 'mace' ),
		'page_description_callback' => 'mace_general_settings_page_description',
		'page_callback'             => 'mace_general_settings_page',
		'fields'                    => array(
			'mace_general_yt_key' => array(
				'title'             => __( 'YouTube App Key', 'mace' ),
				'callback'          => 'mace_general_setting_yt_key',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_general_settings_page( $pages ) {
	$pages[ mace_get_general_settings_page_id() ] = mace_get_general_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_general_settings_page_description() {
	?>
	<?php
}

/**
 * Settings page
 */
function mace_general_settings_page() {
	$page_id        = mace_get_general_settings_page_id();
	$page_config    = mace_get_general_settings_page_config();
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'MediaAce Settings', 'mace' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php mace_admin_settings_tabs( $page_config['tab_title'] ); ?></h2>
		<form action="options.php" method="post">

			<?php settings_fields( $page_id ); ?>
			<?php do_settings_sections( $page_id ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'mace' ); ?>" />
			</p>

		</form>
	</div>

	<?php
}

/**
 * CloudConvert API key
 */
function mace_general_setting_yt_key() {
	?>
	<input name="mace_general_yt_key" id="mace_general_yt_key" class="widefat code" type="text" value="<?php echo esc_attr( mace_get_yt_key() ); ?>" />

	<p class="description">
		<?php esc_html_e( 'Used, for example, for Video Playlist YouTube videos.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Return YT key
 *
 * @return string
 */
function mace_get_yt_key() {
	return get_option( 'mace_general_yt_key', '' );
}

