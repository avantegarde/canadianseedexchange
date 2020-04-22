<?php
/**
 * Snax History Collection class
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Snax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Snax_History_Collection' ) ) {

	/**
	 * Class Snax_History_Collection
	 */
	class Snax_History_Collection extends Snax_Abstract_Collection {

		/**
		 * Check whether the post has beed added to the history.
		 *
		 * @param int    $post_id           Post id.
		 *
		 * @return int|bool                 Row ID or false if not found.
		 */
		protected function in_collection( $post_id ) {
			global $wpdb;
			$table_name = $wpdb->prefix . $this->get_table_name();

			// Prepare to use in SQL.
			$collection_id  = (int) $this->id;
			$post_id        = (int) $post_id;

			$id = $wpdb->get_var("
			SELECT
				ID
			FROM
				$table_name
			WHERE
				collection_id = $collection_id AND
				post_id = $post_id AND
				DATE_FORMAT(date, '%Y-%m-%d') = CURDATE()
			");

			if ( null !== $id ) {
				return (int) $id;
			}

			return false;
		}

	}
}
