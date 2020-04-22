<?php
/**
 * Snax User Collection class
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

if ( ! class_exists( 'Snax_User_Collection' ) ) {

	/**
	 * Class Snax_Custom_Collection
	 */
	class Snax_User_Collection extends Snax_Custom_Collection {

		public function __construct( $id ) {
			$create_new = is_array( $id );

			// Adjust new collection config.
			if ( $create_new ) {
				if ( ! isset( $id['args']['content'] ) ) {
					// Prevent parent collection from loading intro.
					$id['args']['content'] = '';
				}

				$id['args']['post_status'] = 'private';
			}

			$ret = parent::__construct( $id );

			if ( is_wp_error( $ret ) ) {
				return $ret;
			}

			// Set custom meta.
			if ( $create_new ) {
				add_post_meta( $this->id, '_snax_user_custom', true );
			}
		}

		/**
		 * Fires when a collection post is created.
		 *
		 * @param int $post_id           Post id.
		 */
		protected function created( $post_id ) {
			// Update slug.
			wp_update_post( array(
				'ID'        => $post_id,
				'post_name' => $post_id,
			) );

			parent::created( $post_id );
		}
	}
}
