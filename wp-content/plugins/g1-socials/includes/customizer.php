<?php
/**
 * G1 Socials Customizer Helpers
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1 Socials
 */

// @TODO add comments for these functions. And probably add some changes to these reads. Base is done.
function g1_socials_customizer_options_name() {
	return 'g1_socials_customizer';
}

function g1_socials_customizer_defaults( $option_key ) {
	$defaults = array(
		'patreon_label' => esc_html__( 'Like my blog?', 'g1_socials' ),
		'patreon_title' => esc_html__( 'Donate via Patreon to&nbsp;support me.<br />Thank You!', 'g1_socials' ),
		'patreon_link'  => esc_url( 'https://www.patreon.com/' ),
	);
	if ( isset( $defaults[ $option_key ] ) ) {
		return $defaults[ $option_key ];
	} else {
		return false;
	}
}

function g1_socials_customizer_get_option( $option_key ) {
	$options = get_option( g1_socials_customizer_options_name(), array() );
	if ( ! isset( $options[ $option_key ] ) || null === $options[ $option_key ] ) {
		return g1_socials_customizer_defaults( $option_key );
	} else {
		return $options[ $option_key ];
	}
}
