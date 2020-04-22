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

add_filter( 'mace_settings_pages', 'mace_register_hotlink_settings_page', 10 );

function mace_get_hotlink_settings_page_id() {
	return apply_filters( 'mace_hotlink_settings_page_id', 'mace-hotlink-settings' );
}

function mace_get_hotlink_settings_page_config() {
	return apply_filters( 'mace_hotlink_settings_config', array(
		'tab_title'                 => __( 'Hotlink Protection', 'mace' ),
		'page_title'                => __( 'Configure Hotlink Protection', 'mace' ),
		'page_description_callback' => 'mace_hotlink_settings_page_description',
		'page_callback'             => 'mace_hotlink_settings_page',
		'fields'                    => array(
			'mace_hotlink_protection' => array(
				'title'             => __( 'Enabled?', 'mace' ),
				'callback'          => 'mace_hotlink_setting_protection_enabled',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_hotlink_whitelist_domains' => array(
				'title'             => __( 'Access allowed for URLs', 'mace' ),
				'callback'          => 'mace_hotlink_setting_whitelist_domains',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_hotlink_block_access_to' => array(
				'title'             => __( 'Block direct access to these files', 'mace' ),
				'callback'          => 'mace_hotlink_setting_block_access_to',
				'sanitize_callback' => 'mace_sanitize_file_extensions_field',
				'args'              => array(),
			),
			'mace_hotlink_allow_direct_requests' => array(
				'title'             => __( 'Direct requests', 'mace' ),
				'callback'          => 'mace_hotlink_setting_direct_requests',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_hotlink_redirect_url' => array(
				'title'             => __( 'Redirect blocked request to this URL', 'mace' ),
				'callback'          => 'mace_hotlink_setting_redirect_url',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_hotlink_test' => array(
				'title'             => __( 'Test protection', 'mace' ),
				'callback'          => 'mace_hotlink_setting_test',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_hotlink_settings_page( $pages ) {
	$pages[ mace_get_hotlink_settings_page_id() ] = mace_get_hotlink_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_hotlink_settings_page_description() {
	?>
	<p>
		<?php esc_html_e( 'Protect your bandwidth by preventing other sites from using images hosted on your site.', 'mace' ); ?>
		<?php
		if ( is_multisite() ) {?>
		<br><style>.wrap .form-table{display:none;}</style>
			<b><?php esc_html_e( 'Please use the global multisite settings to set up the hotlink protection for the whole multisite.', 'mace' ); ?></b>

		<?php } ?>
	</p>
	<?php
}

/**
 * Settings page
 */
function mace_hotlink_settings_page() {
	$page_id        = mace_get_hotlink_settings_page_id();
	$page_config    = mace_get_hotlink_settings_page_config();
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
 * Enabled?
 */
function mace_hotlink_setting_protection_enabled() {
	?>
	<input name="mace_hotlink_protection" id="mace_hotlink_protection" class="mace-toggle-module" type="checkbox" <?php echo checked( mace_hotlink_is_protection_enabled() ); ?> value="standard" />
	<?php
}

/**
 * Valid URLs
 */
function mace_hotlink_setting_whitelist_domains() {
	$site_domain = mace_get_site_domain();

	?>
	<textarea name="mace_hotlink_whitelist_domains" id="mace_hotlink_whitelist_domains" cols="40" rows="5" class="large-text"><?php echo esc_textarea( mace_hotlink_get_whilelist_domains() ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Regular expressions allowed.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Block direct access to these files
 */
function mace_hotlink_setting_block_access_to() {
	?>
	<input name="mace_hotlink_block_access_to" id="mace_hotlink_block_access_to" type="text" value="<?php echo esc_attr( mace_hotlink_get_block_access_to() ); ?>" />
	<p class="description">
		<?php esc_html_e( 'Space-separated list of file extensions', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Allow direct requests
 */
function mace_hotlink_setting_direct_requests() {
	?>
	<input name="mace_hotlink_allow_direct_requests" id="mace_hotlink_allow_direct_requests" type="checkbox" <?php echo checked( mace_hotlink_allow_direct_requests() ); ?> value="standard" />
	<p class="description">
		<?php esc_html_e( 'Some visitors uses a personal firewall or antivirus program, that deletes the page referer information sent by the web browser. Hotlink protection is based on this information. If you choose not to allow direct requests, you will block these users. You will also prevent people from directly accessing an image by typing in the URL in their browser.', 'mace' ); ?>
	</p>
	<p class="description">
		<?php esc_html_e( 'We recommend to check this option.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Redirect blocked requests
 */
function mace_hotlink_setting_redirect_url() {
	?>
	<input name="mace_hotlink_redirect_url" id="mace_hotlink_redirect_url" class="widefat" type="text" value="<?php echo esc_url( mace_hotlink_get_redirect_url() ); ?>" />
	<p class="description">
		<?php esc_html_e( 'Leave blank to use default image.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Test hotlink protection
 */
function mace_hotlink_setting_test() {
	esc_html_e( 'To make sure that hotlink protection works correct, do as follows:', 'mace' );
	?>
	<ol>
		<li><?php esc_html_e( 'Uncheck the "Direct requests" option above and save changes.', 'mace' ); ?></li>
		<li><?php esc_html_e( 'Clear your browser cache (it is enough to purge the Cached Images and Files).', 'mace' ); ?></li>
		<li><?php esc_html_e( 'Copy url of any of your images.', 'mace' ); ?></li>
		<li><?php esc_html_e( 'Open that url in new browser tab (or window).', 'mace' ); ?></li>
		<li><?php esc_html_e( 'You should be redirected to the "Redirect blocked request to this URL" (if set) or see forbidden page (if url not set).', 'mace' ); ?></li>
		<li><?php esc_html_e( 'After finishing, restore the "Direct requests" option.', 'mace' ); ?></li>
	</ol>
	<?php

}

/**
 * Remove all unwanted chars from file extensions string
 *
 * @param string $value        Value to sanitize.
 *
 * @return string
 */
function mace_sanitize_file_extensions_field( $value ) {
	$value = str_replace( '.', '', $value );

	return trim( $value );
}