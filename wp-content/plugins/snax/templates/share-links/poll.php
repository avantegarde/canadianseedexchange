<?php
/**
 * Twitter share link
 *
 * @package snax 1.11
 * @subpackage Share
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

?>

<div class="snax-poll-answers-share">
	<span class="snax-poll-answers-share-title"><?php echo esc_html__( 'Share your vote on', 'snax' ); ?></span>

	<?php
		$links = apply_filters( 'snax_poll_share_links', snax_get_share_position_active_networks( 'poll_question' ) );
		foreach ( $links as $link_id ) {
			snax_get_template_part( 'share-links/poll-' . $link_id );
		}
	?>
</div>
