<?php
/**
 * Profile socials template part
 *
 * @package g1-socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$data = get_the_author_meta( 'g1_socials', bp_displayed_user_id() );
if ( ! is_array( $data ) ) {
	$data = array();
}

$g1_networks = array_filter( $data );
?>
<?php if ( ! empty( $g1_networks ) ): ?>
<div class="bp-widget base">
	<h2><?php esc_html_e( 'Social media', 'g1_socials' ); ?></h2>
	<table class="profile-fields">
		<tbody>
		<?php
		foreach ( $data as $network => $value ) {
			$network_url = isset( $data[ $network ] ) ? $data[ $network ] : '';
			if ( empty( $network_url ) ) {
				continue;
			} ?>
			<tr class="field_1 field_name required-field visibility-public field_type_textbox">
				<td class="label">
					<span class="g1-socials-item-icon g1-socials-item-icon-<?php echo sanitize_html_class( $network ); ?>" title="<?php esc_attr( $network ); ?>"></span>
					<span class="g1-social-admin-network-name"><?php echo esc_html( $network ); ?></span>
				</td>
				<td class="data"><p>
					<a target="_blank" href="<?php echo esc_url( $network_url );?>" title="<?php echo esc_html( $network ); ?>"><?php echo esc_url( $network_url );?></a>
				</p></td>
			</tr>
			<?php }?>
		</tbody>
	</table>
</div>
<?php endif; ?>

