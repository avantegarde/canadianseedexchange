<?php
/**
 * Notifications Template.
 *
 * @package snax 1.19
 * @subpackage Notifications
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<div class="snax snax-notifications snax-notifications-off">
	<div class="snax-notification">
		<button class="snax-notification-close"><?php echo esc_html_x( 'Close', 'button', 'snax' ); ?></button>
		<p class="snax-notification-text"></p>
	</div>
</div>