<?php
/**
 * Snax Custom Collection class
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

if ( ! class_exists( 'Snax_Custom_Collection' ) ) {

	/**
	 * Class Snax_Custom_Collection
	 */
	class Snax_Custom_Collection extends Snax_Collection {

		public function __construct( $id ) {
			$create_new = is_array( $id );

			// Adjust new collection config.
			if ( $create_new ) {
				if ( ! isset( $id['args']['content'] ) ) {
					$id['args']['content'] = snax_get_collection_intro_shortcode();
				}
			}

			parent::__construct( $id );
		}
	}
}
