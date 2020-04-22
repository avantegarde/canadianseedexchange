<?php
/**
 * Global settings
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'network_admin_menu', 'mace_mu_admin_menu' );
add_action( 'network_admin_menu', 'mace_mu_admin_menu' );
add_action( 'admin_init', 'mace_network_page_init' );

function bimber_network_write_expiry_to_htaccess(){
	$rules = "<IfModule mod_expires.c>\n" . mace_network_expiry_content() . "\n</IfModule>\n";

	// @TODO we can make this better probably.
	$htaccess = ABSPATH . '.htaccess';
	$current = file_get_contents( $htaccess );
	$current = preg_replace( '/<IfModule mod_expires.c>.*<\/IfModule>/msU', '', $current );
	if ( mace_network_expiry_is_headers_enabled() ) {
		$current = $rules . $current;
	}
	file_put_contents( $htaccess, $current );
}
function bimber_network_write_hotlink_protection_to_htaccess(){
	$block_access_to    = mace_network_hotlink_get_block_access_to();

	// There are no defined files to protect.
	if ( empty( $block_access_to ) ) {
		return $rewrite;
	}

	$site_domain        = mace_get_site_domain();
	$block_access_to    = explode( ' ', trim( $block_access_to ) ); // Sanitized array.
	$direct_requests    = mace_network_hotlink_allow_direct_requests();
	$redirect_url       = mace_network_hotlink_get_redirect_url();
	$redirect_flags     = '[NC,R,L]';   // R - redirect, $redirect url has to be set.

	if ( empty( $redirect_url ) ) {
		// Use default.
		$redirect_url = mace_get_plugin_url() . 'assets/hotlink-placeholder.png';
//		$redirect_url   = '-';
//		$redirect_flags = '[NC,F,L]';   // F - forbidden.
	}

	$rules .= "<IfModule mod_rewrite.c>\n";
	$rules .= "RewriteEngine On\n";

	// Prevent infinite loop when we redirect to image which is on the same domain when we have hotlink protection enabled.
	if ( ! empty( $redirect_url ) ) {
		$redirect_url_cond = $redirect_url;

		// Strip domain, if redirect url is local.
		if ( false !== strpos( $redirect_url, $site_domain ) ) {
			$redirect_url_cond = str_replace(
				array(
					$site_domain,
					'http://',
					'https://',
				),
				array(
					'',
					'',
					'',
				),
				$redirect_url
			);
		}

		$rules .= "RewriteCond %{REQUEST_URI} !$redirect_url_cond$\n";
	}

	if ( $direct_requests ) {
		$rules .= "RewriteCond %{HTTP_REFERER} !^$\n";
	}

	$rules .= "RewriteCond %{HTTP_REFERER} !^(http(s)?://)?(www\.)?$site_domain [NC]\n";

	$whitelist_domains = preg_split( '/[\s,]+/', mace_network_hotlink_get_whilelist_domains() );

	foreach ( $whitelist_domains as $domain ) {
		$rules .= "RewriteCond %{HTTP_REFERER} !^$domain [NC]\n";
	}

	$rules .= "RewriteRule \.(" . implode( '|', $block_access_to ) . ")$ $redirect_url $redirect_flags\n";
	$rules .= "</IfModule>\n\n";

	// @TODO we can make this better probably.
	$htaccess = ABSPATH . '.htaccess';
	$current = file_get_contents( $htaccess );
	$current = preg_replace( '/<IfModule mod_rewrite.c>.*<\/IfModule>/msU', '', $current );
	if ( mace_network_hotlink_is_protection_enabled() ) {
		$current = $rules . $current;
	}
	file_put_contents( $htaccess, $current );
}

function mace_mu_admin_menu() {
	add_submenu_page(
		'settings.php',
		__( 'MediaAce', 'mace' ),
		__( 'MediaAce', 'mace' ),
		mace_get_capability(),
		'mace-network-settings',
		'mace_network_settings_page'
	);
	if ( isset( $_POST['submit'] ) ) {
		if ( ! isset( $_POST['mace_network_nonce'] ) || ! wp_verify_nonce( $_POST['mace_network_nonce'], 'mace_network_nonce' ) ) {
			return;
		}
		$enable = filter_input( INPUT_POST, 'mace_network_expiry_headers', FILTER_SANITIZE_STRING );
		$content = filter_input( INPUT_POST, 'mace_network_expiry_content', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
		update_option( 'mace_network_expiry_headers', $enable );
		update_option( 'mace_network_expiry_content', $content );
		bimber_network_write_expiry_to_htaccess();

		$enable = filter_input( INPUT_POST, 'mace_network_hotlink_protection', FILTER_SANITIZE_STRING );
		$whitelist = filter_input( INPUT_POST, 'mace_network_hotlink_whitelist_domains', FILTER_SANITIZE_STRING );
		$block = filter_input( INPUT_POST, 'mace_network_hotlink_block_access_to', FILTER_SANITIZE_STRING );
		$direct = filter_input( INPUT_POST, 'mace_network_hotlink_allow_direct_requests', FILTER_SANITIZE_STRING );
		$redirect = filter_input( INPUT_POST, 'mace_network_hotlink_redirect_url', FILTER_SANITIZE_STRING );
		update_option( 'mace_network_hotlink_protection', $enable );
		update_option( 'mace_network_hotlink_whitelist_domains', $whitelist );
		update_option( 'mace_network_hotlink_block_access_to', $block );
		update_option( 'mace_network_hotlink_allow_direct_requests', $direct );
		update_option( 'mace_network_hotlink_redirect_url', $redirect );
		bimber_network_write_hotlink_protection_to_htaccess();
	}
}

function mace_network_settings_config() {
	return apply_filters( 'mace_network_settings_config', array(
		'tab_title'                 => __( 'MediaAce', 'mace' ),
		'page_title'                => __( 'MediaAce', 'mace' ),
		'page_description_callback' => 'mace_network_settings_description',
		'page_callback'             => 'mace_network_settings_page',
		'fields'                    => array(),
	) );
}

/**
 * Settings page description
 */
function mace_network_settings_description() {}

/**
 * Settings page
 */
function mace_network_settings_page() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'MediaAce Settings', 'mace' ); ?> </h1>
		<form action="settings.php?page=mace-network-settings" method="post">

			<?php settings_fields( 'mace-network-settings' ); ?>
			<?php do_settings_sections( 'mace-network-settings' ); ?>
			<?php wp_nonce_field('mace_network_nonce', 'mace_network_nonce'); ?>
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'mace' ); ?>" />
			</p>

		</form>
	</div>

	<?php
}

function mace_network_page_init() {
	// Add the section.
	add_settings_section(
		'mace_network',
		__( 'MediaAce Settings', 'mace' ),
		'mace_network_settings_description',
		'mace-network-settings'
	);
	$fields = array(
		'mace_network_expiry_headers' => array(
			'title'             => __( 'Expiry Headers Enabled?', 'mace' ),
			'callback'          => 'mace_network_expiry_setting_headers_enabled',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'mace_network_expiry_content' => array(
			'title'             => __( '.htaccess content', 'mace' ),
			'callback'          => 'mace_network_expiry_setting_content',
			'sanitize_callback' => 'sanitize_textarea_field',
			'args'              => array(),
		),
		'mace_network_hotlink_protection' => array(
			'title'             => __( 'Enabled?', 'mace' ),
			'callback'          => 'mace_network_hotlink_setting_protection_enabled',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'mace_network_hotlink_whitelist_domains' => array(
			'title'             => __( 'Access allowed for URLs', 'mace' ),
			'callback'          => 'mace_network_hotlink_setting_whitelist_domains',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'mace_network_hotlink_block_access_to' => array(
			'title'             => __( 'Block direct access to these files', 'mace' ),
			'callback'          => 'mace_network_hotlink_setting_block_access_to',
			'sanitize_callback' => 'mace_sanitize_file_extensions_field',
			'args'              => array(),
		),
		'mace_network_hotlink_allow_direct_requests' => array(
			'title'             => __( 'Direct requests', 'mace' ),
			'callback'          => 'mace_network_hotlink_setting_direct_requests',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'mace_network_hotlink_redirect_url' => array(
			'title'             => __( 'Redirect blocked request to this URL', 'mace' ),
			'callback'          => 'mace_network_hotlink_setting_redirect_url',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
	);
	// Loop through fields for this section.
	foreach ( (array) $fields as $field_id => $field ) {
		// Add the field.
		if ( ! empty( $field['callback'] ) && ! empty( $field['title'] ) ) {
			add_settings_field(
				$field_id,
				$field['title'],
				$field['callback'],
				'mace-network-settings',
				'mace_network',
				$field['args']
			);
		}
		// Register the setting.
		register_setting( 'mace-network-settings', $field_id, $field['sanitize_callback'] );
	}
}

/**
 * Enabled?
 */
function mace_network_expiry_setting_headers_enabled() {
	?>
	<input name="mace_network_expiry_headers" id="mace_network_expiry_headers" class="mace-toggle-module" type="checkbox" <?php echo checked( mace_network_expiry_is_headers_enabled() ); ?> value="standard" />
	<?php
}

/**
 * Content
 */
function mace_network_expiry_setting_content() {
	$site_domain = mace_get_site_domain();

	?>
	<textarea name="mace_network_expiry_content" id="mace_network_expiry_content" cols="40" rows="20" class="large-text"><?php echo esc_textarea( mace_network_expiry_content() ); ?></textarea>
	<?php
}

/**
 * Check whether the Expiry Headers is active
 *
 * @return bool
 */
function mace_network_expiry_is_headers_enabled() {
	return 'standard' === get_option( 'mace_network_expiry_headers', 'standard' );
}

/**
 * Return content
 *
 * @return string
 */
function mace_network_expiry_content() {
	$default_content = '
# Enable expirations
ExpiresActive On
# Default directive
ExpiresDefault "access plus 1 month"
# My favicon
ExpiresByType image/x-icon "access plus 1 year"
# Images
ExpiresByType image/gif "access plus 1 month"
ExpiresByType image/png "access plus 1 month"
ExpiresByType image/jpg "access plus 1 month"
ExpiresByType image/jpeg "access plus 1 month"
# CSS
ExpiresByType text/css "access plus 1 month"
# Javascript
ExpiresByType application/javascript "access plus 1 year"';

	return get_option( 'mace_network_expiry_content', $default_content );
}

/**
 * Enabled?
 */
function mace_network_hotlink_setting_protection_enabled() {
	?>
	<input name="mace_network_hotlink_protection" id="mace_network_hotlink_protection" class="mace-toggle-module" type="checkbox" <?php echo checked( mace_network_hotlink_is_protection_enabled() ); ?> value="standard" />
	<?php
}

/**
 * Valid URLs
 */
function mace_network_hotlink_setting_whitelist_domains() {
	$site_domain = mace_get_site_domain();

	?>
	<textarea name="mace_network_hotlink_whitelist_domains" id="mace_network_hotlink_whitelist_domains" cols="40" rows="5" class="large-text"><?php echo esc_textarea( mace_network_hotlink_get_whilelist_domains() ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Regular expressions allowed.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Block direct access to these files
 */
function mace_network_hotlink_setting_block_access_to() {
	?>
	<input name="mace_network_hotlink_block_access_to" id="mace_network_hotlink_block_access_to" type="text" value="<?php echo esc_attr( mace_network_hotlink_get_block_access_to() ); ?>" />
	<p class="description">
		<?php esc_html_e( 'Space-separated list of file extensions', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Allow direct requests
 */
function mace_network_hotlink_setting_direct_requests() {
	?>
	<input name="mace_network_hotlink_allow_direct_requests" id="mace_network_hotlink_allow_direct_requests" type="checkbox" <?php echo checked( mace_network_hotlink_allow_direct_requests() ); ?> value="standard" />
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
function mace_network_hotlink_setting_redirect_url() {
	?>
	<input name="mace_network_hotlink_redirect_url" id="mace_network_hotlink_redirect_url" class="widefat" type="text" value="<?php echo esc_url( mace_network_hotlink_get_redirect_url() ); ?>" />
	<p class="description">
		<?php esc_html_e( 'Leave blank to use default image.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Check whether the Hotlink Protection is active
 *
 * @return bool
 */
function mace_network_hotlink_is_protection_enabled() {
	return 'standard' === get_option( 'mace_network_hotlink_protection', 'standard' );
}

/**
 * Return whilelist domains
 *
 * @return string
 */
function mace_network_hotlink_get_whilelist_domains() {
	$default_domains = array(
		'(http(s)?://)?(www\.)?facebook\.com',
		'(http(s)?://)?(www\.)?google\.*$/.*',
		'(http(s)?://)?(www\.)?pinterest\.*$/.*',
	);

	return get_option( 'mace_network_hotlink_whitelist_domains', implode( "\n", $default_domains ) );
}

/**
 * Return protected files extensions
 *
 * @return string
 */
function mace_network_hotlink_get_block_access_to() {
	return get_option( 'mace_network_hotlink_block_access_to', 'jpg jpeg png gif' );
}

/**
 * Check whether allow direct requests
 *
 * @return bool
 */
function mace_network_hotlink_allow_direct_requests() {
	return 'standard' === get_option( 'mace_network_hotlink_allow_direct_requests', 'standard' );
}

/**
 * Return URL to which blocked request will be forwarded
 *
 * @return string
 */
function mace_network_hotlink_get_redirect_url() {
	return get_option( 'mace_network_hotlink_redirect_url', '' );
}
