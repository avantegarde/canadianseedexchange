<?php
/**
 * Template for displaying a success note after submitting new post
 *
 * @package snax 1.11
 * @subpackage Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$error_message = get_query_var( 'snax_error_message' );
?>
<div id="snax-popup-slog-message" class="snax white-popup mfp-hide">
	<h2 class="snax-note-title"><?php esc_html_e( 'Social login error', 'snax' ); ?></h2>

	<p><?php echo esc_html( $error_message ); ?></p>
</div>

<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			if (typeof snax.openPopup === 'function') {
				snax.openPopup($('#snax-popup-slog-message'));

			} else {
				alert( '<?php echo esc_html( $error_message ); ?>' );
			}
		});
	})(jQuery);
</script>
