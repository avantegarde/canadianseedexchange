<?php
/**
 * MediaAce Vimeo Video
 *
 * @package media-ace
 * @subpackage Classes
 */

/**
 * MediaAce Video Class for Vimeo
 */
class Mace_Video_Vimeo extends Mace_Video {

	/**
	 * Return video type
	 *
	 * @return string
	 */
	public function get_type() {
		return 'Vimeo';
	}

	/**
	 * Return video original url
	 *
	 * @return string
	 */
	public function get_url() {
		$url = parent::get_url();

		$query_args = apply_filters( 'mace_vp_vimeo_autoplay_params', array(
			'background' => 1,
			'muted'      => 1,
			'loop'       => 0,
		) );

		$url = add_query_arg( $query_args, $url );

		return $url;
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
			<source type="video/vimeo" src="<?php echo esc_url( $this->get_url() ); ?>" />
		</video>
		<?php
	}

	/**
	 * Try to extract video id from url
	 *
	 * @return string|WP_Error
	 */
	protected function extract_video_id() {
		if ( ! preg_match( mace_get_video_type_regex( 'Vimeo' ), $this->get_url(), $matches ) ) {
			/* translators: %s is replaced with the video url */
			return new WP_Error( 'mace_vp_vimeo_invalid_url', sprintf( esc_html__( 'Vimeo video url %s is not valid!', 'mace' ), $this->get_url() ) );
		}

		return trim( $matches[5] );
	}

	/**
	 * Fetch and format video details
	 *
	 * @param string $video_id      Video unique id.
	 *
	 * @return bool|WP_Error        True if ok, WP_Error otherwise.
	 */
	protected function fetch_details( $video_id ) {
		$details = $this->call_api( $video_id );

		if ( is_wp_error( $details ) ) {
			return $details;
		}

		if ( empty( $details[0] ) ) {
			/* translators: %s is replaced with the video url */
			return new WP_Error( 'mace_vp_vimeo_not_found', sprintf( esc_html__( 'Vimeo video %s not found!', 'mace' ), $this->get_url() ) );
		}

		$item = $details[0];

		$this->details = array(
			'id'        => $video_id,
			'url'       => $this->get_url(),
			'title'     => $item['title'],
			'thumbnail' => $this->get_item_thumbnail( $item ),
			'poster'    => '',
			'duration'  => $this->get_item_duration( $item ),
		);

		return true;
	}

	/**
	 * Call Vimeo API to get video details
	 *
	 * @param string $video_id      Video unique id.
	 *
	 * @return array|WP_Error       Array with details or WP_Error
	 */
	protected function call_api( $video_id ) {
		$api_url = apply_filters( 'mace_vp_vimeo_video_api', 'https://vimeo.com/api/v2/video/' );

		$api_url = sprintf( '%s%s.json', $api_url, $video_id );

		$request = wp_remote_get( $api_url );

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		return json_decode( wp_remote_retrieve_body( $request ), true );
	}

	/**
	 * Extract video thumbnail from details
	 *
	 * @param array $item       Video item data.
	 *
	 * @return string
	 */
	protected function get_item_thumbnail( $item ) {
		if ( ! empty( $item['thumbnail_small'] ) ) {
			return $item['thumbnail_small'];
		}

		return '';
	}

	/**
	 * Extract video duration from details
	 *
	 * @param array $item       Video item data.
	 *
	 * @return int
	 */
	protected function get_item_duration( $item ) {
		if ( ! empty( $item['duration'] ) ) {
			return $item['duration'];
		}

		return 0;
	}
}
