<?php
/**
 * The Template for displaying info about missing plugin to render an ad box.
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Bimber_Theme 4.10
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<?php if ( current_user_can( 'edit_plugins' ) ) : ?>
<div class="g1-message g1-message-warning">
	<div class="g1-message-inner">
		<p><?php printf( wp_kses_post( __( 'This newsletter cannot be displayed. The <strong>%s</strong> plugin is not activated.', 'bimber' ) ), esc_html( 'MailChimp for WordPress' ) ); ?></p>
		<p><?php printf( wp_kses_post( __( 'If you want to use other newsletter plugin, please refer to the <a href="%s" target="_blank">documentation</a>.', 'bimber' ) ), esc_url( 'http://docs.bimber.bringthepixel.com/#g1docs-use-other-newsletter-plugin' ) ); ?></p>
	</div>
</div>
<?php endif; ?>
