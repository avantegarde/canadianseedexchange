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

add_filter( 'mace_settings_pages', 'mace_register_gallery_settings_page', 10 );

function mace_get_gallery_settings_page_id() {
	return apply_filters( 'mace_gallery_settings_page_id', 'mace-gallery-settings' );
}

function mace_get_gallery_settings_page_config() {
	return apply_filters( 'mace_gallery_settings_config', array(
		'tab_title'                 => __( 'Gallery', 'mace' ),
		'page_title'                => __( 'Gallery', 'mace' ),
		'page_description_callback' => 'mace_gallery_settings_page_description',
		'page_callback'             => 'mace_gallery_settings_page',
		'fields'                    => array(
			'mace_gallery_logo' => array(
				'title'             => __( 'Logo', 'mace' ),
				'callback'          => 'mace_gallery_setting_logo',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_gallery_logo_hdpi' => array(
				'title'             => __( 'Logo HDPI', 'mace' ),
				'callback'          => 'mace_gallery_setting_logo_hdpi',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_gallery_skin' => array(
				'title'             => __( 'Lightbox skin', 'mace' ),
				'callback'          => 'mace_gallery_setting_skin',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_gallery_thumbnails' => array(
				'title'             => __( 'Sidebar thumbnails', 'mace' ),
				'callback'          => 'mace_gallery_setting_thumbnails',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_gallery_settings_page( $pages ) {
	$pages[ mace_get_gallery_settings_page_id() ] = mace_get_gallery_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_gallery_settings_page_description() {
	?>
	<?php
}

/**
 * Settings page
 */
function mace_gallery_settings_page() {
	$page_id        = mace_get_gallery_settings_page_id();
	$page_config    = mace_get_gallery_settings_page_config();
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
 * Logo
 */
function mace_gallery_setting_logo() {
	$attachment_id = mace_gallery_get_logo();

	mace_select_image_control( 'mace_gallery_logo', $attachment_id );
}

/**
 * Logo
 */
function mace_gallery_setting_logo_hdpi() {
	$attachment_id = mace_gallery_get_logo_hdpi();

	mace_select_image_control( 'mace_gallery_logo_hdpi', $attachment_id );
}

/**
 * Skin
 */
function mace_gallery_setting_skin() {
	$skin       = mace_gallery_get_skin();
	$all_skins = array( 'dark', 'light' );

	?>
	<select name="mace_gallery_skin" id="mace_gallery_skin">
	<?php foreach ( $all_skins as $skin_id ) : ?>
		<option value="<?php echo esc_attr( $skin_id ); ?>"<?php selected( $skin, $skin_id ); ?>>
			<?php echo esc_html( str_replace( '_', ' ', $skin_id ) ); ?>
		</option>
	<?php endforeach; ?>
	</select>
	<?php
}

/**
 * Sidebar thumbnails
 */
function mace_gallery_setting_thumbnails() {
	$setting       = mace_gallery_get_thumbnails_visibillity();
	$all_settings = array( 'show', 'hide' );

	?>
	<select name="mace_gallery_thumbnails" id="mace_gallery_thumbnails">
	<?php foreach ( $all_settings as $setting_id ) : ?>
		<option value="<?php echo esc_attr( $setting_id ); ?>"<?php selected( $setting, $setting_id ); ?>>
			<?php echo esc_html( str_replace( '_', ' ', $setting_id ) ); ?>
		</option>
	<?php endforeach; ?>
	</select>
	<?php
}

/**
 * FB enabled?
 */
function mace_gallery_setting_shares_header() {
}
