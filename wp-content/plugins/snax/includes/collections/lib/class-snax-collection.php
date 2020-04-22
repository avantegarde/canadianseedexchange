<?php
/**
 * Snax Collection class
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

if ( ! class_exists( 'Snax_Collection' ) ) {

	/**
	 * Class Snax_Collection
	 */
	class Snax_Collection {

		protected $id;
		protected $post;

		/**
		 * Return collection object
		 *
		 * @param int|array $id                 If numeric, return existing collection.
		 *                                      If array, create a collection based on it.
		 */
		public function __construct( $id ) {
			// Create a collection.
			if ( is_array( $id ) ) {
				$args = isset( $id['args'] ) ? $id['args'] : array();

				$id = $this->create( $id['user_id'], $id['title'], $args );

				if ( is_wp_error( $id ) ) {
					return $id;
				}
			}

			// Load collection data.
			$this->id   = $id;
			$this->post = get_post( $id );

			if ( ! $this->post ) {
				return new WP_Error( 'snax_invalid_collection_id', esc_html_x( 'Collection ID does not refer to any valid collection!', 'Collection error message', 'snax' ) );
			}
		}

		/**
		 * Create a collection
		 *
		 * @param int    $user_id           User id.
		 * @param string $title             Collection title.
		 * @param array  $args              (Optional) Extra args.
		 *
		 * @return Snax_Collection|WP_Error     Collection object or WP_Error object.
		 */
		private function create( $user_id, $title, $args = array() ) {
			$args = wp_parse_args( $args, array(
				'content'       => '',
				'slug'          => '',
				'posts_order'   => 'ASC',
				'visibility'    => snax_get_collection_visibility_private(),
				'post_status'   => 'publish',
			) );

			$post_id = wp_insert_post( array(
				'post_author'   => $user_id,
				'post_title'    => wp_strip_all_tags( $title ),
				'post_content'  => $args['content'],
				'post_name'     => $args['slug'],
				'post_status'   => $args['post_status'],
				'post_type'     => snax_get_collection_post_type(),
			) );

			// Set up meta.
			add_post_meta( $post_id, '_snax_posts_order', $args['posts_order'] );
			add_post_meta( $post_id, '_snax_visibility', $args['visibility'] );

			$this->created( $post_id );

			return $post_id;
		}

		/**
		 * Fires when a collection post is created.
		 *
		 * @param int $post_id           Post id.
		 */
		protected function created( $post_id ) {
			do_action( 'snax_collection_post_created', $post_id );
		}

		/**
		 * Return id of the collection (it's the post id)
		 *
		 * @return int
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Return id of the collection's owner
		 *
		 * @return int
		 */
		public function get_owner_id() {
			return (int) $this->post->post_author;
		}

		/**
		 * Return title
		 *
		 * @return string
		 */
		public function get_title() {
			return $this->post->post_title;
		}

		/**
		 * Return description
		 *
		 * @return string
		 */
		public function get_description() {
			return $this->post->post_excerpt;
		}

		/**
		 * Return collection url
		 *
		 * @return string
		 */
		public function get_url() {
			return get_permalink( $this->post );
		}

		/**
		 * Return posts order (ASC | DESC)
		 *
		 * @return string
		 */
		public function get_posts_order() {
			return 'DESC';
			// @todo Remove that data, we don't use it anywhere.
			//return get_post_meta( $this->id, '_snax_posts_order', true );
		}

		/**
		 * Return visibility (public | private)
		 *
		 * @return string
		 */
		public function get_visibility() {
			return get_post_meta( $this->id, '_snax_visibility', true );
		}

		/**
		 * Get a collection by id
		 *
		 * @param int $id                       Collection id.
		 *
		 * @return Snax_Collection|WP_Error     Collection object of WP_Error if not found
		 */
		public static function get_by_id( $id ) {
			return new static( $id );
		}

		/**
		 * Get a collection by slug
		 *
		 * @param string $slug                  Collection slug.
		 *
		 * @return Snax_Collection|WP_Error     Collection object or WP_Error if not found.
		 */
		public static function get_by_slug( $slug ) {
			$post = get_page_by_path( $slug, OBJECT, snax_get_collection_post_type() );

			if ( $post && 'publish' === $post->post_status ) {
				return new static( $post->ID );
			}

			return new WP_Error( 'snax_collection_not_found', esc_html_x( 'Could not find a collection!', 'Collection error message', 'snax' ) );
		}

		/**
		 * Add post to collection
		 *
		 * @param int $post_id          Post id.
		 *
		 * @return bool|WP_Error        True on success, WP_Error otherwise.
		 */
		public function add_post( $post_id ) {
			if ( $this->in_collection( $post_id ) ) {
				return true;
			}

			// Post exists?
			$post = get_post( $post_id );

			if ( ! $post ) {
				return new WP_Error( 'snax_invalid_post', esc_html_x( 'Post not exists!', 'Collection error message', 'snax' ) );
			}

			// Is valid post type?
			$excluded = apply_filters( 'snax_collection_excluded_post_types', array(
				'attachment',
				snax_get_collection_post_type(),
			) );

			if ( in_array( get_post_type( $post ), $excluded ) ) {
				return new WP_Error( 'snax_collection_invalid_post_type', esc_html_x( 'Post type not allowed!', 'Collection error message', 'snax' ) );
			}

			global $wpdb;
			$table_name       = $wpdb->prefix . self::get_table_name();
			$post_date        = current_time( 'mysql' );

			$affected_rows = $wpdb->insert(
				$table_name,
				array(
					'collection_id'     => $this->id,
					'post_id'           => $post_id,
					'date'              => $post_date,
					'date_gmt'          => get_gmt_from_date( $post_date ),
				),
				array(
					'%d',
					'%d',
					'%s',
					'%s',
				)
			);

			if ( false === $affected_rows ) {
				return new WP_Error( 'snax_insert_into_collection_failed', esc_html_x( 'Could not insert into the collection!', 'Collection error message', 'snax' ) );
			}

			do_action( 'snax_post_added_to_collection', $post_id, $this->id );

			return true;
		}

		/**
		 * Return list of post ids belong to the collection
		 *
		 * @param int $max              Max number of posts to fetch
		 * @param int $offset           Posts offset.
		 *
		 * @return array                List of post ids.
		 */
		public function get_posts( $max = 10, $offset = 0 ) {
			global $wpdb;
			$table_name       = $wpdb->prefix . self::get_table_name();

			$posts_order = $this->get_posts_order();

			$results = $wpdb->get_results( $wpdb->prepare(
				"SELECT post_id FROM $table_name WHERE collection_id = %d ORDER BY date_gmt $posts_order LIMIT %d OFFSET %d",
				$this->id,
				$max,
				$offset
			), ARRAY_A );

			$ids = array();

			if ( $results ) {
				foreach ( $results as $result ) {
					$ids[]  = $result['post_id'];
				}
			}

			return $ids;
		}

		/**
		 * Count posts that belong to the collection
		 *
		 * @return int
		 */
		public function count_posts() {
			global $wpdb;
			$table_name       = $wpdb->prefix . self::get_table_name();

			$post_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE collection_id = $this->id" );

			return (int) $post_count ;
		}


		/**
		 * Remove post from collection
		 *
		 * @param int $post_id          Post id.
		 *
		 * @return bool|WP_Error        True on success, WP_Error if failed.
		 */
		public function remove_post( $post_id ) {
			global $wpdb;
			$table_name = $wpdb->prefix . self::get_table_name();

			do_action( 'snax_before_post_removed_from_collection', $post_id, $this->id );

			$affected_rows = $wpdb->delete(
				$table_name,
				array(
					'collection_id'     => $this->id,
					'post_id'           => $post_id,
				),
				array(
					'%d',
					'%d',
				)
			);

			if ( false === $affected_rows ) {
				return new WP_Error( 'snax_delete_collection_post_failed', esc_html_x( 'Could not delete collection post!', 'Collection error message', 'snax' ) );
			}

			do_action( 'snax_post_removed_from_collection', $post_id, $this->id );

			return true;
		}

		/**
		 * Remove all posts from collection
		 *
		 * @return bool|WP_Error        True on success, WP_Error if failed.
		 */
		public function remove_all_posts() {
			global $wpdb;
			$table_name = $wpdb->prefix . self::get_table_name();

			$affected_rows = $wpdb->delete(
				$table_name,
				array(
					'collection_id'     => $this->id,
				),
				array(
					'%d',
				)
			);

			if ( false === $affected_rows ) {
				return new WP_Error( 'snax_delete_collection_posts_failed', esc_html_x( 'Could not delete collection posts!', 'Collection error message', 'snax' ) );
			}

			do_action( 'snax_posts_removed_from_collection', $this->id );

			return true;
		}

		/**
		 * Remove the collection post
		 *
		 * @return bool|WP_Error            True if succeed, WP_Error on failure.
		 */
		public function remove() {
			$res = wp_delete_post( $this->id, true );

			// Items will be removed on delete_post hook.

			if ( false === $res ) {
				return new WP_Error( 'snax_delete_collection_failed', esc_html_x( 'Could not delete collection!', 'Collection error message', 'snax' ) );
			}

			return true;
		}

		/**
		 * Update collection
		 *
		 * @param array $data       List of fields to update.
		 *
		 * @return bool
		 */
		public function update( $data ) {
			if ( ! is_array( $data ) ) {
				return false;
			}

			// Map input data into post fields.
			$update_data = array(
				'ID' => $this->get_id(),
			);

			// Title.
			if ( ! empty( $data['title'] ) ) {
				$update_data['post_title'] = $data['title'];
			}

			// Description.
			$update_data['post_excerpt'] = $data['description'];

			if ( ! empty( $data['visibility'] ) ) {
				$valid_visibility = array(
					snax_get_collection_visibility_private(),
					snax_get_collection_visibility_public()
				);

				if ( in_array( $data['visibility'], $valid_visibility ) ) {
					update_post_meta( $this->get_id(), '_snax_visibility', $data['visibility'] );
				}

				// Post is private.
				if ( $data['visibility'] === snax_get_collection_visibility_private() ) {
					$update_data['post_status'] = 'private';
				// Post is public.
				} else {
					$update_data['post_status'] = 'publish';
				}
			}

			// Set collection featured media.
			if ( ! empty( $data['featured_media'] ) ) {
				$current_featured_id = (int) get_post_thumbnail_id( $this->get_id() );
				$featured_id         = (int) $data['featured_media'];

				if ( $current_featured_id !== $featured_id ) {
					// If collection's image is based on item, reset that relation.
					delete_post_meta( $this->get_id(), '_snax_featured_image_from_post' );
				}

				set_post_thumbnail( $this->get_id(), $data['featured_media'] );
			// Remove collection featured media.
			} else {
				delete_post_thumbnail( $this->get_id() );

				// If collection's image is based on item, reset that relation.
				delete_post_meta( $this->get_id(), '_snax_featured_image_from_post' );
			}

			// On each update, check and clean up all uploaded images that are not longer in use.
			$this->clean_up_unused_media();

			$res = wp_update_post( $update_data );

			if ( is_wp_error( $res ) || 0 === $res ) {
				return false;
			}

			// Update post object.
			$this->post = get_post( $this->id );

			return true;
		}

		/**
		 * Get the table name of the collections table
		 *
		 * @return string
		 */
		public static function get_table_name() {
			return 'snax_collections';
		}

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
				post_id = $post_id
			");

			if ( null !== $id ) {
				return (int) $id;
			}

			return false;
		}

		/**
		 * Remove collection's uploaded media that are no longer in use
		 */
		private function clean_up_unused_media() {
			$exclude_ids = array();
			$current_thumbnail_id = get_post_thumbnail_id( $this->get_id() );

			if ( ! empty( $current_thumbnail_id ) ) {
				$exclude_ids[] = $current_thumbnail_id;
			}

			$attachments = get_posts( array(
				'post_type'     => 'attachment',
				'post_status'   => 'inherit',
				'numberposts'   => -1,
				'post_parent'   => $this->get_id(),
				'meta_key'      => '_snax_source_form',
				'meta_value'    => 'collection_edit_form',
				'post__not_in'  => $exclude_ids,
			) );

			if ( ! empty( $attachments ) ) {
				foreach ( $attachments as $attachment ) {
					wp_delete_attachment( $attachment->ID, true );
				}
			}
		}
	}
}
