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
?>
<div class="snax-note snax-note-success">
	<div class="snax-note-icon">
	</div>

	<h2 class="snax-note-title"><?php esc_html_e( 'Your post has been deleted', 'snax' ); ?></h2>

	<p>
		<?php
		$user_posts_page_url = snax_get_user_approved_posts_page();

		if ( ! empty( $user_posts_page_url ) ) {
			printf( wp_kses_post( __( 'Back to all your posts on <a href="%s">your profile page</a>.', 'snax' ) ), esc_url( $user_posts_page_url ) );
		}
		?>
	</p>
</div>