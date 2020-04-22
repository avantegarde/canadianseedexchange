<?php
/**
 * Snax Abstract Collection class
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

if ( ! class_exists( 'Snax_Abstract_Collection' ) ) {

	/**
	 * Class Snax_Abstract_Collection
	 */
	class Snax_Abstract_Collection extends Snax_Collection {

		protected $config;

		public function __construct( $id ) {
			$create_new = is_array( $id );

			// Adjust new collection config.
			if ( $create_new ) {
				$slug = $id['args']['slug'];

				if ( isset( $id['args']['abstract'] ) ) {
					$slug = $id['args']['abstract'];
				}

				$config = snax_get_abstract_collection( $slug );

				$id['args']['posts_order'] = $config['reverse_order'] ? 'DESC' : 'ASC';
				$id['args']['visibility']  = $config['visibility'];
			}

			$ret = parent::__construct( $id );

			if ( is_wp_error( $ret ) ) {
				return $ret;
			}

			// Set custom meta.
			if ( $create_new ) {
				add_post_meta( $this->id, '_snax_abstract', $slug );
			}
		}

		/**
		 * Return collection url
		 *
		 * @return string
		 */
		public function get_url() {
			$collection_slug = get_post_meta( $this->post->ID, '_snax_abstract', true );

			return  get_permalink( snax_get_abstract_collection_post_id( $collection_slug ) );
		}

		/**
		 * Return user's collection
		 *
		 * @param int    $user_id               User id.
		 * @param string $base_slug             Collection base slug (e.g. history).
		 *
		 * @return Snax_Collection|WP_Error     Collection object or WP_Error if couldn't create.
		 */
		public static function get_by_user( $user_id, $base_slug ) {
			$slug = self::get_unique_slug( $base_slug, $user_id );

			$collection = self::get_by_slug( $slug );

			// Create if not exists.
			if ( is_wp_error( $collection ) ) {
				$config = snax_get_abstract_collection( $base_slug );

				if ( ! $config ) {
					return new WP_Error( 'collection_not_defined', esc_attr_x( 'Abstract collection not defined!', 'Collection error message', 'snax' ) );
				}

				$collection = new self( array(
					'user_id'   => $user_id,
					'title'     => $config['title'],
					'args'      => array(
						'abstract'      => $base_slug,
						'slug'          => $slug
					),
				) );
			}

			return $collection;
		}

		/**
		 * Check whether the collection exists
		 *
		 * @param int    $user_id               User id.
		 * @param string $base_slug             Collection base slug (e.g. history).
		 *
		 * @return Snax_Collection|bool         Collection object or false is doesn't exist.
		 */
		public static function exists( $user_id, $base_slug ) {
			$slug = self::get_unique_slug( $base_slug, $user_id );

			$collection = self::get_by_slug( $slug );

			return ! is_wp_error( $collection ) ? $collection : false;
		}

		/**
		 * Return unique user's collection slug
		 *
		 * @param string $base_slug     Collection base slug.
		 * @param int    $user_id       User id.
		 *
		 * @return string
		 */
		protected static function get_unique_slug( $base_slug, $user_id ) {
			return sprintf( '%d-%s', $user_id, $base_slug );
		}
	}
}
