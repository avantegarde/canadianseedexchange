<?php
/**
 * User fields
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1 Socials
 */

add_action( 'init', 'g1_socials_user_init' );

function g1_socials_user_init() {
	if ( g1_socials_user_profiles_enabled() ) {
		add_action( 'show_user_profile', 'g1_socials_user_fields' );
		add_action( 'edit_user_profile', 'g1_socials_user_fields' );
		add_action( 'personal_options_update', 'g1_socials_user_fields_save' );
		add_action( 'edit_user_profile_update', 'g1_socials_user_fields_save' );
	}
}

function g1_socials_user_profiles_enabled() {
	$plugin = G1_Socials();

	return 'standard' === get_option( $plugin->get_option_name() . '_enable_user_profiles', 'standard' );
}

/**
 * Render fields on backend
 *
 * @param WP_User $user  User.
 */
function g1_socials_user_fields( $user ) {
	$data = get_the_author_meta( 'g1_socials', $user->ID );
	if ( ! is_array( $data ) ) {
		$data = array();
	}
	$networks = g1_socials_user_get_supported_networks();
	?>
	<h3><?php esc_html_e( 'G1 Socials', 'g1_socials' ); ?></h3>
	<table class="form-table">
	<?php
	foreach ( $networks as $network => $value ) {
		$network_url = isset( $data[ $network ] ) ? $data[ $network ] : '' ?>
			<tr>
				<th><label for="g1_socials[<?php echo esc_attr( $network ); ?>]">
					<span class="g1-socials-item-icon g1-socials-item-icon-<?php echo sanitize_html_class( $network ); ?>" title="<?php esc_attr( $network ); ?>"></span>
					<span class="g1-social-admin-network-name"><?php echo esc_html( $network ); ?></span>
				</label></th>
				<td>
					<input type="url" pattern="https?://.+" name="g1_socials[<?php echo esc_attr( $network ); ?>]" id="g1_<?php echo esc_attr( $network ); ?>" value="<?php echo esc_attr( $network_url ); ?>" class="regular-text" />
				</td>
			</tr>
	<?php }?>
	</table><?php
}

/**
 * Save user fields
 *
 * @param int $user_id  User id.
 */
function g1_socials_user_fields_save( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	$data = filter_input( INPUT_POST, 'g1_socials', FILTER_VALIDATE_URL, FILTER_REQUIRE_ARRAY );
	update_user_meta( $user_id, 'g1_socials', $data );
}

/**
 * Get all supported networks
 *
 * @return array
 */
function g1_socials_user_get_supported_networks() {
	$networks = G1_Socials()->get_items();
	return apply_filters( 'g1_socials_user_get_supported_networks', $networks );
}

add_filter( 'g1_socials_user_get_supported_networks', 'g1_socials_user_set_supported_networks' );

/**
 * Set the basic networks as a default
 *
 * @return array
 */
function g1_socials_user_set_supported_networks() {
	return array(
		'facebook'      => 'facebook',
		'instagram'     => 'instagram',
		'linkedin'      => 'linkedin',
		'pinterest'     => 'pinterest',
		'reddit'        => 'reddit',
		'snapchat'      => 'snapchat',
		'tumblr'        => 'tumblr',
		'twitter'       => 'twitter',
		'vimeo'         => 'vimeo',
		'youtube'       => 'youtube',
	);
}
