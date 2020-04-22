<?php
/**
 * MediaAce Video Common Interface
 *
 * @package media-ace
 * @subpackage Classes
 */

/**
 * MediaAce Video Interface
 */
interface Mace_Video_Interface {
	/**
	 * Return video id
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Return video type
	 *
	 * @return string
	 */
	public function get_type();

	/**
	 * Return video original url
	 *
	 * @return string
	 */
	public function get_url();

	/**
	 * Return video title
	 *
	 * @return string
	 */
	public function get_title();

	/**
	 * Return video thumbnail url
	 *
	 * @return string
	 */
	public function get_thumbnail();

	/**
	 * Return video thumbnail
	 *
	 * @return string
	 */
	public function get_poster();

	/**
	 * Return video duration (in seconds)
	 *
	 * @return string
	 */
	public function get_duration();

	/**
	 * Return video duration (formatted)
	 *
	 * @return string
	 */
	public function get_formatted_duration();

	/**
	 * Return JSON encoded video config
	 *
	 * @return string
	 */
	public function get_json_config();

	/**
	 * Render video HTML
	 *
	 * @return string
	 */
	public function render();
}
