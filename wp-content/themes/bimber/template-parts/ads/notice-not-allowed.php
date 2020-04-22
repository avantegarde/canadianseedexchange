<?php
/**
 * The Template for displaying info about missing plugin to render an ad box.
 *
 * @package Bimber_Theme 4.10
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$slot_name = get_query_var( 'plugin_required_notice_slot_id' );
?>

<?php if ( current_user_can( 'edit_plugins' ) ) : ?>
	<div class="g1-message g1-message-warning">
		<div class="g1-message-inner">
			<p><?php printf( __( 'The %s ad cannot be displayed here due to one of following reasons', 'bimber' ), $slot_name ); ?>:</p>
			<ul>
				<li><?php esc_html_e( 'ad is not assigned to this location', 'bimber' ); ?></li>
				<li><?php esc_html_e( 'maximum number of ads on a single page has been reached', 'bimber' ); ?></li>
			</ul>
		</div>
	</div>
<?php endif;
