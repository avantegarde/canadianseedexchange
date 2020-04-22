<?php
/**
 * Snapcode things
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Socials Theme
 */

// @todo Make it work with https://github.com/jusleg/snaptag --
// @todo shortcode. --
// @todo Widget.
// @todo Move the code to the appropriate file. Maybe a separate plugin? y, i think so.
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get Snapcode
 *
 * @param string $username Username to be displayed.
 * @param string $user_id User id on Snapchat.
 * @param string $useravatar Link to avatar.
 * @todo move html to parts once we know that it will be in plugin/theme
 */
function g1_socials_get_youtube( $channel_id = '' ) {
	$youtube_api_key = get_option( 'g1_socials_youtube_api_key', '' );
	$youtube_api_key = apply_filters( 'g1_socials_youtube_api_key', $youtube_api_key );

	$youtube_remote_url = add_query_arg(
		array(
			'channelId'  => $channel_id,
			'key'        => $youtube_api_key,
			'part'       => 'snippet',
			'order'      => 'date',
			'maxResults' => '1',
		),
		'https://www.googleapis.com/youtube/v3/search/'
	);
	$youtube_json = get_transient( esc_attr( $channel_id ) );
	if ( false === $youtube_json ) {
		// Get remote.
		$youtube_remote_responce = wp_remote_get( $youtube_remote_url );
		// Check for error.
		if ( is_wp_error( $youtube_remote_responce ) ) {
			return;
		}
		// Parse remote file.
		$youtube_remote_responce_body = wp_remote_retrieve_body( $youtube_remote_responce );
		$youtube_json = json_decode( $youtube_remote_responce_body );
		set_transient( esc_attr( $channel_id ), $youtube_json, 30 );
	}
	if ( null === $youtube_json || false === $youtube_json ) {
		delete_transient( esc_attr( $channel_id ) );
		return;
	}
	if ( isset( $youtube_json->items ) ) {
		$video = $youtube_json->items[0];
		if ( isset(  $video->id->videoId ) ) {
			$video_iframe_url = add_query_arg(
				array(
					'controls' => 0,
					'showinfo' => 0,
				),
				esc_url( 'https://www.youtube.com/embed/' . $video->id->videoId )
			);
			?>
			<iframe width="560" height="190" src="<?php echo( esc_url( $video_iframe_url ) ); ?>" frameborder="0" allowfullscreen=""></iframe>
			<p class="g1-youtube-video-title entry-title g1-gamma g1-gamma-1st">
				<a target="_blank" rel="noopener" href="<?php echo( esc_url( 'https://www.youtube.com/watch?v=' . $video->id->videoId ) ); ?>"><?php echo( esc_html( $video->snippet->title ) ); ?></a>
			</p>
			<?php
		}
	} else {
		?>
		<p class="g1-youtube-video-error">
		<?php
		if ( isset( $youtube_json->error ) && current_user_can( 'administrator' ) ) {
			echo esc_html( $youtube_json->error->errors[0]->message );
		} else {
			esc_html_e( 'Something went wrong. Check YouTube API key and channel ID.' );
		}
		?>
		</p>
		<?php
	}
}
