<?php
/**
 * Social Login Core Functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Snax
**/

/**
 * Check whether the social login module is enabled
 *
 * @return bool
 */
function snax_slog_enabled() {
	$enabled = 'standard' === get_option( 'snax_slog_enabled', 'none' );

	return apply_filters( 'snax_slog_enabled', $enabled );
}

/**
 * Check whether the debug mode  is enabled
 *
 * @return bool
 */
function snax_slog_debug_mode_enabled() {
	$enabled = 'standard' === get_option( 'snax_slog_debug_mode', 'none' );

	return apply_filters( 'snax_slog_debug_mode_enabled', $enabled );
}

/**
 * Return a list of all available social login providers
 *
 * @return array
 */
function snax_slog_get_providers() {
	$folder_uri = trailingslashit( snax()->css_url ) . 'snaxicon/svg/';

	$providers = array(
		'Facebook' => array(
			'name' => 'Facebook',
			'icon' => $folder_uri . 'ue00a-facebook.svg',
		),
		'Google' => array(
			'name' => 'Google',
			'icon' => $folder_uri . 'ue081-google.svg',
		),
		'Twitter' => array(
			'name' => 'Twitter',
			'icon' => $folder_uri . 'ue00b-twitter.svg',
		),
		'Instagram' => array(
			'name' => 'Instagram',
			'icon' => $folder_uri . 'ue029-instagram.svg',
		),
		'LinkedIn' => array(
			'name' => 'LinkedIn',
			'icon' => $folder_uri . 'ue080-linkedin.svg',
		),
		'Vkontakte' => array(
			'name' => 'VKontakte',
			'icon' => $folder_uri . 'ue02e-vk.svg',
		),
//		'AOLOpenID',
//		'Blizzard',
//		'Disqus',
//		'GitHub',
//		'OpenID',
//		'Spotify',
//		'SteemConnect',
//		'WordPress',
//		'Amazon',
//		'BlizzardAPAC',
//		'Dribbble',
//		'GitLab',
//		'Mailru',
//		'Paypal',
//		'StackExchange',
//		'Tumblr',
//		'WeChat',
//		'Yahoo',
//		'Authentiq',
//		'BlizzardEU',
//		'MicrosoftGraph',
//		'PaypalOpenID',
//		'StackExchangeOpenID',
//		'TwitchTV',
//		'WeChatChina',
//		'YahooOpenID',
//		'BitBucket',
//		'Discord',
//		'Foursquare',
//		'Odnoklassniki',
//		'Reddit',
//		'Steam',
//		'WindowsLive',
//		'Yandex',
	);

	return apply_filters( 'snax_slog_providers', $providers );
}

/**
 * Return a list of active social login providers
 *
 * @return array
 */
function snax_slog_get_active_providers() {
	return (array) get_option( 'snax_slog_active_providers', array() );
}

/**
 * Return providers configuration data
 *
 * @return array
 */
function snax_slog_get_providers_creds() {
	return (array) get_option( 'snax_slog_providers_creds', array() );
}

/**
 * Check whether the social login GDPR module is enabled
 *
 * @return bool
 */
function snax_slog_gdpr_enabled() {
	$enabled = 'standard' === get_option( 'snax_slog_gdpr_enabled', 'none' );
	$can_use = snax_can_use_plugin( 'wp-gdpr-compliance/wp-gdpr-compliance.php' );

	return apply_filters( 'snax_slog_gdpr_enabled', $enabled && $can_use );
}

/**
 * Return template of the Snax Popup social login location
 *
 * @return string
 */
function snax_slog_location_popup_tpl() {
	return apply_filters( 'snax_slog_location_popup_tpl', get_option( 'snax_slog_location_popup_tpl', 'buttons' ) );
}

/**
 * Return template of the register page social login location
 *
 * @return string
 */
function snax_slog_location_register_page_tpl() {
	return apply_filters( 'snax_slog_location_register_page_tpl', get_option( 'snax_slog_location_register_page_tpl', 'buttons' ) );
}

/**
 * Return template of the login widget social login location
 *
 * @return string
 */
function snax_slog_location_login_form_tpl() {
	return apply_filters( 'snax_slog_location_login_form_tpl', get_option( 'snax_slog_location_login_form_tpl', 'icons' ) );
}

/**
 * Return GDPR Consent text
 *
 * @return string
 */
function snax_slog_gdpr_consent_text() {
	$text = get_option( 'snax_slog_gdpr_consent_text', '' );

	return apply_filters( 'snax_slog_gdpr_consent_text', $text );
}

/**
 * @param null $user_id
 */
function snax_slog_get_user_photo_url( $user_id = null ) {
	$user_photo_url = false;

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( $user_id ) {
		$user_photo_url = get_user_meta( $user_id, '_snax_slog_photo_url', true );
	}

	return apply_filters( 'snax_slog_user_photo_url', $user_photo_url, $user_id );
}