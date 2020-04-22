<?php
/**
 * MediaAce Self Hosted Video
 *
 * @package media-ace
 * @subpackage Classes
 */

/**
 * MediaAce Class for Self Hosted Video
 */
class Mace_Video_SelfHosted extends Mace_Video {

	/**
	 * Return video type
	 *
	 * @return string
	 */
	public function get_type() {
		return 'SelfHosted';
	}

	/**
	 * Render video HTML
	 */
	public function render() {
		$img = $this->get_poster();
		?>
		<video class="mace-video-player"
		       width="100%"
		       height="100%"
		       controls="controls"
		       preload="none"
		       <?php if ( strlen( $img ) ) : ?>poster="<?php echo esc_url( $img ); ?>"<?php endif; ?>
		>
			<source type="video/mp4" src="<?php echo esc_url( $this->get_url() ); ?>" />
		</video>
		<?php
	}

	/**
	 * Try to extract video id from url
	 *
	 * @return string|WP_Error
	 */
	protected function extract_video_id() {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", trim( $this->get_url() ) ) );

		if ( empty( $attachment ) ) {
			/* translators: %s is replaced with the video url */
			return new WP_Error( 'mace_vp_self_hosted_invalid_url', sprintf( esc_html__( 'Self-hosted video url %s is not a valid attachment url!', 'mace' ), $this->get_url() ) );
		}

		return $attachment[0];
	}

	protected function skip_cache() {
		// There is no need to cache data for self-hosted video.
		// User can change featured image, title etc. and should see results immediately.
		return true;
	}

	/**
	 * Fetch and format video details
	 *
	 * @param string $video_id      Video unique id.
	 *
	 * @return bool|WP_Error        True if ok, WP_Error otherwise.
	 */
	public function fetch_details( $video_id ) {
		$video      = get_post( $video_id );
		$video_meta = get_post_meta( $video_id, '_wp_attachment_metadata', true );

		$thumbnail = '';
		$poster    = '';

		$post_thumbnail_id = get_post_thumbnail_id( $video_id );

		if ( $post_thumbnail_id ) {
			$thumbnail = wp_get_attachment_image_url( $post_thumbnail_id, 'post-thumbnail' );
			$poster    = wp_get_attachment_image_url( $post_thumbnail_id, 'large' );
		}

		$this->details = array(
			'id'        => $video_id,
			'url'       => $this->get_url(),
			'title'     => $video->post_title,
			'thumbnail' => $thumbnail,
			'poster'    => $poster,
			'duration'  => $video_meta['length'],
		);

		return true;
	}
}
