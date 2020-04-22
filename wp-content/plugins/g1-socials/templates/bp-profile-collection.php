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

$data = get_the_author_meta( 'g1_socials', bp_get_member_user_id() );
// Normalize.
if ( ! is_array( $data ) ) {
	$data = array();
}

// Remove not configured networks.
$data = array_filter( $data );
// Remove not supported networks.
$data = array_intersect_key( $data, g1_socials_user_get_supported_networks() );
?>

<?php if ( count( $data ) ) : ?>
	<div class="g1-socials-bp-profile-collection">
		<?php foreach ( $data as $network => $value ) : ?>
			<a
			class="g1-socials-item-icon g1-socials-item-icon-<?php echo sanitize_html_class( $network ); ?> g1-socials-item-icon-text"
			href="<?php echo esc_url( $value );?>"
			title="<?php echo esc_html( $network ); ?>"
			target="_blank"
			rel="noopener">
				<?php echo esc_html( $network ); ?>
			</a>
		<?php endforeach; ?>
	</div>
<?php endif;
