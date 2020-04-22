<?php
/**
 * BuddyPress plugin functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package g1-socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'init', 'g1_socials_bp_init' );

function g1_socials_bp_init() {
	if ( g1_socials_user_profiles_enabled() ) {
		add_action( 'bp_after_profile_field_content', 'g1_socials_buddypress_display_fields' );
		add_action( 'bp_after_profile_field_content', 'g1_socials_buddypress_display_edit_fields' );
		add_action( 'xprofile_updated_profile', 'g1_socials_buddypress_save_fields', 10, 5 );

		add_action( 'bp_directory_members_item', 'g1_socials_buddypress_collection_display_fields', 6 );

		add_action( 'bp_after_member_header_meta', 'g1_socials_buddypress_header_display_fields' );
		add_action( 'bp_after_member_header_meta', 'g1_socials_buddypress_header_display_fields' );
	}
}


/**
 * Add BP edit fields to profile
 */
function g1_socials_buddypress_display_fields() {
	global $group;

	if ( ! $group  ) {
		return;
	}

	// Don't add our elements to groups other the the Primary (which can't be deleted).
	if ( $group->can_delete ) {
		return;
	}

	if ( 'profile' !== bp_current_component() || ( 'public' !== bp_current_action() && 'classic' !== bp_current_action() ) ) {
		return;
	}
	g1_socials_get_template_part( 'bp-profile' );
}

/**
 * Add BP edit fields to profile header
 */
function g1_socials_buddypress_header_display_fields() {
	g1_socials_get_template_part( 'bp-profile-header' );
}

/**
 * Add BP edit fields to profile in collection
 */
function g1_socials_buddypress_collection_display_fields() {
	g1_socials_get_template_part( 'bp-profile-collection' );
}

/**
 * Add BP edit fields to profile
 */
function g1_socials_buddypress_display_edit_fields() {
	if ( 'profile' !== bp_current_component() || 'edit' !== bp_current_action() ) {
		return;
	}
	$data = get_the_author_meta( 'g1_socials', bp_displayed_user_id() );
	if ( ! is_array( $data ) ) {
		$data = array();
	}
	$networks = g1_socials_user_get_supported_networks();
	?>
	<div class="g1-socials-edit-profiles">
		<h2><?php esc_html_e( 'Social Media Profiles', 'g1_socials' ); ?></h2>
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
		</table>
	</div>
	<?php
}

/**
 * Save networks via BP
 *
 * @param int   $bp_displayed_user_id   Displayed user ID.
 * @param array $posted_field_ids 		Array of field IDs that were edited.
 * @param bool  $errors           		Whether or not any errors occurred.
 * @param array $old_values       		Array of original values before updated.
 * @param array $new_values       		Array of newly saved values after update.
 */
function g1_socials_buddypress_save_fields( $bp_displayed_user_id, $posted_field_ids, $errors, $old_values, $new_values ) {
	$data = filter_input( INPUT_POST, 'g1_socials', FILTER_VALIDATE_URL, FILTER_REQUIRE_ARRAY );
	update_user_meta( $bp_displayed_user_id, 'g1_socials', $data );
}
