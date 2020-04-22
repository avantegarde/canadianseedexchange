<?php
/**
 * Template for displaying single item share
 *
 * @package snax 1.11
 * @subpackage Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<?php

$item_url 		= snax_get_share_url();
$item_title 	= get_the_title();
$item_thumb_url = snax_is_item( null, 'image' ) ? get_the_post_thumbnail_url() : '';
?>
<div class="snax-item-share">
	<a class="snax-item-share-toggle" href="#"><?php esc_html_e( 'Share', 'snax' ); ?></a>

	<div class="snax-item-share-content">
		<?php
		$share_networks = snax_get_share_position_active_networks( 'list_item' );

		foreach( $share_networks as $share_network ) {
			switch ( $share_network ) {
				case 'facebook':
					snax_render_facebook_share_button( array(
						'share_url'     => $item_url,
						'share_text'    => $item_title,
						'classes'       => array( 'snax-share' ),
					) );
					break;

				case 'twitter':
					snax_render_twitter_share_button( array(
						'share_url'     => $item_url,
						'share_text'    => $item_title,
						'classes'       => array( 'snax-share' ),
					) );
					break;

				case 'pinterest':
					snax_render_pinterest_share_button( array(
						'share_url'     => $item_url,
						'share_text'    => $item_title,
						'share_media'   => $item_thumb_url,
						'classes'       => array( 'snax-share' ),
					) );
					break;
			}
		}
		?>
	</div>
</div>
