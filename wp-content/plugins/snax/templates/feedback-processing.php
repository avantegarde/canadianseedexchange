<?php
/**
 * Feedback - uploading
 *
 * @package snax 1.11
 * @subpackage FrontendSubmission
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<div class="snax">
	<div class="snax-feedback snax-feedback-off snax-feedback-processing-files">
		<div class="snax-feedback-inner">

			<div class="snax-details">
				<a href="#" class="snax-close-button"><?php echo esc_html_x( 'Close', 'button', 'snax' ); ?></a>

				<div class="snax-xofy">
					<?php
					printf(
						esc_html_x( '%s of %s', 'e.g., 3 of 10', 'snax' ),
						'<span class="snax-xofy-x"></span>',
						'<span class="snax-xofy-y"></span>'
					);
					?>
				</div>

				<ul class="snax-states">
				</ul>

				<p class="snax-text-processing"><?php esc_html_e( 'Processing files&hellip;', 'snax' ); ?></p>

			</div>

		</div>
	</div>
</div>
