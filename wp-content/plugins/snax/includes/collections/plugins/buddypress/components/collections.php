<?php
/**
 * Snax BuddyPress Collections Component Class
 *
 * @package snax
 * @subpackage BuddyPress
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Snax_Collections_BP_Component' ) ) :
	/**
	 * Loads Component for BuddyPress
	 */
	class Snax_Collections_BP_Component extends BP_Component {

		/**
		 * Start the Snax component creation process
		 */
		public function __construct() {
			parent::start(
				'snax_collections',
				__( 'Collections', 'snax' )
			);

			$this->fully_loaded();
		}

		/**
		 * Setup globals
		 *
		 * @param array $args           Component global variables.
		 */
		public function setup_globals( $args = array() ) {
			// All arguments for forums component.
			$args = array(
				'slug'          => snax_collections_bp_component_id(),
				'path'          => BP_PLUGIN_DIR,
				'search_string' => __( 'Search Collections...', 'snax' ),
				'notification_callback' => 'snax_bp_format_notifications',
			);

			parent::setup_globals( $args );
		}

		/**
		 * Setup hooks
		 */
		public function setup_actions() {

			add_filter( 'snax_user_private_collections_page',   array( $this, 'user_private_collections_page' ), 10, 2 );
			add_filter( 'snax_user_public_collections_page',    array( $this, 'user_public_collections_page' ), 10, 2 );
			add_filter( 'snax_collections_pagination_base',     array( $this, 'user_collections_pagination_base' ), 10, 2 );

			parent::setup_actions();
		}

		/**
		 * Return BP private collections page url
		 *
		 * @param string $url               Current url.
		 * @param int    $user_id           User id.
		 *
		 * @return string
		 */
		public function user_private_collections_page( $url, $user_id ) {
			$base_url       = bp_core_get_user_domain( $user_id );

			$component_slug = $this->slug;
			$status_slug    = snax_get_user_private_collections_slug();

			$url = $base_url . $component_slug . '/' . $status_slug;

			return $url;
		}

		/**
		 * Return BP public collections page url
		 *
		 * @param string $url               Current url.
		 * @param int    $user_id           User id.
		 *
		 * @return string
		 */
		public function user_public_collections_page( $url, $user_id ) {
			$base_url       = bp_core_get_user_domain( $user_id );
			$component_slug = $this->slug;
			$status_slug    = snax_get_user_public_collections_slug();

			$url = $base_url . $component_slug . '/' . $status_slug;

			return $url;
		}

		/**
		 * Change pagination base url
		 *
		 * @param string $base          Current base url.
		 * @param array  $args          WP Query args.
		 *
		 * @return string
		 */
		public function user_collections_pagination_base( $base, $args ) {
			global $wp_rewrite;

			if ( $wp_rewrite->using_permalinks() && isset( $args['author'] ) ) {
				$user_id = $args['author'];
				$component_slug = $this->slug;

				$sub_component_slug = '';

				if ( isset( $args['type'] ) ) {
					switch ( $args['type'] ) {
						case snax_get_collection_visibility_public():
							$sub_component_slug = snax_get_user_public_collections_slug() . '/';
							break;

						case snax_get_collection_visibility_private():
							$sub_component_slug = snax_get_user_private_collections_slug() . '/';
							break;
					}
				}

				$base = bp_core_get_user_domain( $user_id ) . $component_slug . '/'. $sub_component_slug;

				// Use pagination base.
				$base = trailingslashit( $base ) . user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );
			}

			return $base;
		}

		/**
		 * Allow the variables, actions, and filters to be modified by third party
		 * plugins and themes.
		 */
		private function fully_loaded() {
			do_action_ref_array( 'snax_collections_bp_component_loaded', array( $this ) );
		}

		/**
		 * Setup BuddyBar navigation
		 *
		 * @param array $main_nav               Component main navigation.
		 * @param array $sub_nav                Component sub navigation.
		 */
		public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

			// Stop if there is no user displayed or logged in.
			if ( ! is_user_logged_in() && ! bp_displayed_user_id() ) {
				return;
			}

			// Collections.
			$main_nav = array(
				'name'                => _x( 'Collections', 'BuddyPress tab title', 'snax' ),
				'slug'                => $this->slug,
				'position'            => 6,
				'screen_function'     => 'snax_member_screen_public_collections',
				'default_subnav_slug' => snax_get_user_public_collections_slug(),
				'item_css_id'         => $this->id,
			);

			// Determine user to use.
			if ( bp_displayed_user_id() ) {
				$user_domain = bp_displayed_user_domain();
			} elseif ( bp_loggedin_user_domain() ) {
				$user_domain = bp_loggedin_user_domain();
			} else {
				return;
			}

			$component_link = trailingslashit( $user_domain . $this->slug );

			// Collections > Public.
			$sub_nav[] = array(
				'name'            => _x( 'Public', 'Collection type', 'snax' ),
				'slug'            => snax_get_user_public_collections_slug(),
				'parent_url'      => $component_link,
				'parent_slug'     => $this->slug,
				'screen_function' => 'snax_member_screen_public_collections',
				'position'        => 10,
				'item_css_id'     => 'public-collections',
			);

			if ( get_current_user_id() === bp_displayed_user_id() ) {
				// Collections > Private (only for logged in user).
				$sub_nav[] = array(
					'name'            => _x( 'Private', 'Collection type', 'snax' ),
					'slug'            => snax_get_user_private_collections_slug(),
					'parent_url'      => $component_link,
					'parent_slug'     => $this->slug,
					'screen_function' => 'snax_member_screen_private_collections',
					'position'        => 20,
					'item_css_id'     => 'private-collections',
				);
			}

			$main_nav = apply_filters( 'snax_bp_component_main_nav', $main_nav, $this->id );
			$sub_nav  = apply_filters( 'snax_bp_component_sub_nav', $sub_nav, $this->id );

			parent::setup_nav( $main_nav, $sub_nav );
		}

		/**
		 * Set up the admin bar
		 *
		 * @param array $wp_admin_nav       Component entries in the WordPress Admin Bar.
		 */
		public function setup_admin_bar( $wp_admin_nav = array() ) {

			// Menus for logged in user.
			if ( is_user_logged_in() ) {

				// Setup the logged in user variables.
				$user_domain = bp_loggedin_user_domain();
				$component_link = trailingslashit( $user_domain . $this->slug );

				// Collections.
				$wp_admin_nav[] = array(
					'parent' => buddypress()->my_account_menu_id,
					'id'     => 'my-account-' . $this->id,
					'title'  => _x( 'Collections', 'BuddyPress tab title', 'snax' ),
					'href'   => trailingslashit( $component_link ),
				);

				// Collections > Public.
				$wp_admin_nav[] = array(
					'parent' => 'my-account-' . $this->id,
					'id'     => 'my-account-' . $this->id . '-public-collections',
					'title'  => _x( 'Public', 'Collection type', 'snax' ),
					'href'   => trailingslashit( $component_link . snax_get_user_public_collections_slug() ),
				);

				// Collections > Private.
				$wp_admin_nav[] = array(
					'parent' => 'my-account-' . $this->id,
					'id'     => 'my-account-' . $this->id . '-private-collections',
					'title'  => _x( 'Private', 'Collection type', 'snax' ),
					'href'   => trailingslashit( $component_link . snax_get_user_private_collections_slug() ),
				);
			}

			parent::setup_admin_bar( $wp_admin_nav );
		}

		/**
		 * Sets up the title for pages and <title>
		 */
		public function setup_title() {
			$bp = buddypress();

			// Adjust title based on view.
			$is_snax_component = (bool) bp_is_current_component( $this->id );

			if ( $is_snax_component ) {
				if ( bp_is_my_profile() ) {
					$bp->bp_options_title = _x( 'Collections', 'BuddyPress tab title', 'snax' );
				} elseif ( bp_is_user() ) {
					$bp->bp_options_avatar = bp_core_fetch_avatar( array(
						'item_id' => bp_displayed_user_id(),
						'type'    => 'thumb',
					) );

					$bp->bp_options_title = bp_get_displayed_user_fullname();
				}
			}

			parent::setup_title();
		}
	}
endif;
