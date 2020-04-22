<?php
/**
 * Settings page
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'mace_settings_pages', 'mace_register_video_settings_page', 10 );

function mace_get_video_settings_page_id() {
	return apply_filters( 'mace_video_settings_page_id', 'mace-video-settings' );
}

function mace_get_video_settings_page_config() {
	return apply_filters( 'mace_video_settings_config', array(
		'tab_title'                 => _x( 'Video', 'Settings tab title', 'mace' ),
		'page_title'                => _x( 'Video', 'Setting page title', 'mace' ),
		'page_description_callback' => 'mace_video_settings_page_description',
		'page_callback'             => 'mace_video_settings_page',
		'fields'                    => array(
			'mace_auto_video_length' => array(
				'title'             => _x( 'Auto video length', 'Setting title', 'mace' ),
				'callback'          => 'mace_auto_video_length_setting',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_video_settings_page( $pages ) {
	$pages[ mace_get_video_settings_page_id() ] = mace_get_video_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_video_settings_page_description() {
	?>
	<p>
		<?php echo esc_html_x( 'Common video tasks.', 'Settings page description', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Settings page
 */
function mace_video_settings_page() {
	$page_id        = mace_get_video_settings_page_id();
	$page_config    = mace_get_video_settings_page_config();
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
 * Enable auto featured
 */
function mace_auto_video_length_setting() {
	?>
	<input name="mace_auto_video_length" id="mace_auto_video_length" type="checkbox" <?php echo checked( mace_is_auto_video_length_enabled() ); ?> value="standard" />
	<p class="description">
		<?php echo esc_html_x( 'Fetch and store video length when video post format is creating and updating.', 'Setting description', 'mace' ); ?><br />
		<?php echo esc_html_x( 'For YouTube videos, you have to provide YouTube App Key in the Settings tab.', 'YouTube invalid key message', 'mace' ); ?>
	</p>
	<?php
}
