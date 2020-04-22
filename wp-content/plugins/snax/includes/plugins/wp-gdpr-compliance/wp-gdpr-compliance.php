<?php
/**
 * WP GDPR Compliance plugin functions
 *
 * @package snax
 * @subpackage Plugins
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'snax_slog_after_links',    'snax_gdpr_render_consent_form' );

function snax_gdpr_render_consent_form() {
	if ( ! snax_slog_gdpr_enabled() ) {
		return;
	}

	snax_get_template_part( 'wp-gdpr-compliance/consent' );
}

/**
 * Render GDPR consent text
 */
function snax_gdpr_render_consent_text() {
	$consent = snax_slog_gdpr_consent_text();

	$link = snax_gdpr_get_privacy_policy_link();

	if ( $link ) {
		$consent = str_replace( '%privacy_policy%', $link, $consent );
	}
	?>
	<?php echo wp_kses_post( $consent );?>
	<?php
}

/**
 * Return the GDPR Privacy Policy page link
 *
 * @return string | bool        A HTML link or false if not set
 */
function snax_gdpr_get_privacy_policy_link() {
	$link = false;
	$page = get_option( 'wpgdprc_settings_privacy_policy_page' ) ;

	if ( $page ) {
		$text = get_option( 'wpgdprc_settings_privacy_policy_text' );

		if ( empty( $text ) ) {
			$text = get_the_title( $page );
		}

		$link = '<a href="' . get_page_link( $page ) . '" target="_blank">' . esc_html( $text ) . '</a>';
	}

	return $link;
}
