<?php
/**
 * MediaAce YouTube Video
 *
 * @package media-ace
 * @subpackage Classes
 */

/**
 * MediaAce Video Class for YouTube
 */
abstract class Mace_Video implements Mace_Video_Interface {

	/**
	 * Video url
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Video width
	 *
	 * @var int
	 */
	protected $width;

	/**
	 * Video data
	 *
	 * @var array
	 */
	protected $details;

	/**
	 * Last error
	 *
	 * @var WP_Error
	 */
	protected $last_error;

	/**
	 * Constructor
	 *
	 * @param string $url       Video url.
	 */
	public function __construct( $url ) {
		$this->url = $url;

		$video_id = $this->extract_video_id();

		if ( is_wp_error( $video_id ) ) {
			$this->last_error = $video_id;
			return;
		}

		$this->load_details( $video_id );
	}

	/**
	 * Return video id
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->details['id'];
	}

	/**
	 * Get video width
	 */
	public function get_width() {
		// Read theme content.
		if ( empty( $this->width ) ) {
			global $content_width;

			if ( ! empty( $content_width ) ) {
				return $content_width;
			}

			// Fallback.
			return apply_filters( 'mace_vp_default_video_width', 788 );
		}

		return $this->width;
	}

	/**
	 * Get video height
	 */
	public function get_height() {
		$width = $this->get_width();

		// Default ratio, 16:9.
		$ratio = apply_filters( 'mace_vp_video_ratio', 0.5625 );

		return absint( $width * $ratio );
	}

	/**
	 * Set video width
	 *
	 * @param int $width
	 */
	public function set_width( $width ) {
		$this->width = $width;
	}

	/**
	 * Return video original url
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Return video title
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->details['title'];
	}

	/**
	 * Return video thumbnail
	 *
	 * @return string
	 */
	public function get_thumbnail() {
		return $this->details['thumbnail'];
	}

	/**
	 * Return video poster
	 *
	 * @return string
	 */
	public function get_poster() {
		return $this->details['poster'];
	}


	/**
	 * Return video duration (in seconds)
	 *
	 * @return string
	 */
	public function get_duration() {
		return $this->details['duration'];
	}

	/**
	 * Return video duration (formatted)
	 *
	 * @return string
	 */
	public function get_formatted_duration() {
		$duration = $this->get_duration();

		$hour_in_seconds = 60 * 60;

		if ( $duration > $hour_in_seconds ) {
			$format = 'H:i:s';
		} else {
			$format = 'i:s';
		}

		$format = apply_filters( 'mace_video_duration_format', $format, $duration );

		return date( $format, $duration );
	}

	/**
	 * Return last error message
	 *
	 * @return string       Last error or empty string if no error
	 */
	public function get_last_error() {
		if ( is_wp_error( $this->last_error ) ) {
			return $this->last_error->get_error_message();
		}

		return '';
	}

	/**
	 * Return JSON encoded video config
	 *
	 * @return string
	 */
	public function get_json_config() {
		return wp_json_encode( $this->details );
	}

	protected function skip_cache() {
		return (bool) filter_input( INPUT_GET, 'mace_video_skip_cache', FILTER_SANITIZE_STRING );
	}

	/**
	 * Load video details
	 *
	 * @param string $video_id      Video unique id.
	 */
	protected function load_details( $video_id ) {
		// Read cached value.
		$details = get_transient( 'mace_video_' . $video_id );

		$skip_cache = $this->skip_cache();

		if ( apply_filters( 'mace_video_skip_cache', $skip_cache ) ) {
			$details = false;
		}

		// Cache not exists.
		if ( false === $details ) {
			$res = $this->fetch_details( $video_id );

			if ( is_wp_error( $res ) ) {
				$this->last_error = $res;
			} else {
				// Cache data.
				$cache_time = apply_filters( 'mace_video_cache_time', 60 * 60 * 2 ); // 2 hours.
				set_transient( 'mace_video_' . $video_id, $this->details, $cache_time );
			}

		// Load from cache.
		} else {
			$this->details = $details;
		}
	}

	abstract protected function extract_video_id();

	abstract protected function fetch_details( $video_id );
}
