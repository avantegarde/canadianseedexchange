<?php
/**
 * Instagram functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'wp_head', 'g1_socials_instagram_save_token' );

function g1_socials_instagram_save_token() {
	$url = add_query_arg( array(
		'page' => g1_socials_options_page_slug(),
		'tab'  => 'g1_socials_instagram',
		'code' => filter_input( INPUT_GET, 'code', FILTER_SANITIZE_STRING ),
	), admin_url( 'options-general.php' ) );

	?>
	<script>if ('#_' === window.location.hash) window.location.href='<?php echo $url; ?>';</script>
	<?php
}
