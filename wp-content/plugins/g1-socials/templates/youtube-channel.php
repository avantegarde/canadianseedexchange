<?php
/**
 * A template part for displaying YouTube Channel.
 *
 * @package g1-socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<div class="g1-youtube-channel">
	<?php g1_socials_get_youtube( $g1_socials_yt_channel_id ); ?>

	<p class="g1-youtube-channel-name">
		<a href="<?php echo ( esc_url( 'https://www.youtube.com/channel/' . $g1_socials_yt_channel_id ) ); ?>" rel="me noopener" target="_blank">&#64;<?php echo wp_kses_post( $g1_socials_yt_channel_name ); ?></a>
	</p>

	<p class="g1-youtube-follow">
		<a href="<?php echo ( esc_url( 'https://www.youtube.com/channel/' . $g1_socials_yt_channel_id ) ); ?>" rel="me noopener" target="_blank"><?php esc_html_e( 'Subscribe', 'g1_socials' ); ?></a>
	</p>
</div>
