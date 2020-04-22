<?php
/**
 * BuddyPress plugin functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'bp_core_admin_get_components',		'wyr_bp_register_custom_components', 10, 2 );

/**
 * Return votew component unique id
 *
 * @return string
 */
function wyr_reactions_bp_component_id() {
	return 'reactions';
}

/**
 * Init our custom components states
 *
 * @param bool $force           Skip checking components setup flag.
 */
function wyr_bp_activate_components( $force = false ) {
	$wyr_bp_components = get_option( 'wyr_bp_components' );

	if ( $force || 'loaded' !== $wyr_bp_components ) {
		$bp_active_components = bp_get_option( 'bp-active-components', array() );

		$bp_active_components[ wyr_reactions_bp_component_id() ] = 1;

		bp_update_option( 'bp-active-components', $bp_active_components );
		add_option( 'wyr_bp_components', 'loaded' );
	}
}

/**
 * Register wyr custom components
 *
 * @param array  $components        Registered components.
 * @param string $type              Component type.
 *
 * @return array
 */
function wyr_bp_register_custom_components( $components, $type ) {
	if ( in_array( $type, array( 'all', 'optional' ), true ) ) {

		$votes_id = wyr_reactions_bp_component_id();

		// Votes.
		$components[ $votes_id ] = array(
			'title'       => _x( 'Reactions', 'BuddyPress component name', 'wyr' ),
			'description' => _x( 'Allow your users to manage their reactions directly from within their profiles.', 'BuddyPress component description', 'wyr' ),
		);
	}

	return $components;
}

if ( ! class_exists( 'Wyr_Reactions_BP_Component' ) ) :
	/**
	 * Loads Component for BuddyPress
	 */
	class Wyr_Reactions_BP_Component extends BP_Component {

		/**
		 * Start the component creation process
		 */
		public function __construct() {
			parent::start(
				wyr_reactions_bp_component_id(),
				__( 'Reactions', 'wyr' )
			);
			$this->fully_loaded();
		}

		/**
		 * Setup hooks
		 */
		public function setup_actions() {

			parent::setup_actions();
		}

		/**
		 * Allow the variables, actions, and filters to be modified by third party
		 * plugins and themes.
		 */
		private function fully_loaded() {
			do_action_ref_array( 'wyr_votes_bp_component_loaded', array( $this ) );
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

			// Votes.
			$main_nav = array(
				'name'                => __( 'Reactions', 'wyr' ),
				'slug'                => $this->slug,
				'position'            => 9,
				'screen_function'     => 'wyr_member_screen_reactions',
				'default_subnav_slug' => 'reactions',
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

			$main_nav = apply_filters( 'snax_bp_component_main_nav', $main_nav, $this->id );
			parent::setup_nav( $main_nav, $sub_nav );
		}

		/**
		 * Sets up the title for pages and <title>
		 */
		public function setup_title() {
			$bp = buddypress();

			// Adjust title based on view.
			$is_wyr_component = (bool) bp_is_current_component( $this->id );

			if ( $is_wyr_component ) {
				if ( bp_is_my_profile() ) {
					$bp->bp_options_title = __( 'Reactions', 'wyr' );
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

add_action( 'bp_include', 'wyr_setup_buddypress', 10 );

function wyr_setup_buddypress() {
	if ( ! function_exists( 'buddypress' ) ) {
		/**
		 * Create helper for BuddyPress 1.6 and earlier.
		 *
		 * @return bool
		 */
		function buddypress() {
			return isset( $GLOBALS['bp'] ) ? $GLOBALS['bp'] : false;
		}
	}

	// Bail if in maintenance mode.
	if ( ! buddypress() || buddypress()->maintenance_mode ) {
		return;
	}

	/* Activate our custom components */
	global $pagenow;
	$forced =  ( 'options-permalink.php' === $pagenow ) ? true : false;
	wyr_bp_activate_components( $forced );

	// Votes.
	$component_id = wyr_reactions_bp_component_id();

	if ( bp_is_active( $component_id ) ) {
		$component = new Wyr_Reactions_BP_Component();
		buddypress()->$component_id = $component;
	}
}

/**
 * Hook "Reactions" template into plugins template
 */
function wyr_member_screen_reactions() {
	add_action( 'bp_template_content', 'wyr_member_reactions_content' );
	bp_core_load_template( apply_filters( 'wyr_member_screen_reactions', 'members/single/plugins' ) );
}


/**
 * Reactions template part
 */
function wyr_member_reactions_content() { ?>

	<div id="wyr-member-reactions">

		<?php wyr_get_template_part( 'buddypress' );; ?>

	</div>

	<?php
}