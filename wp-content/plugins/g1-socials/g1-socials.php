<?php
/*
Plugin Name:    G1 Socials
Plugin URI:     http://www.bringthepixel.com
Description:    Create icon links to your social channels.
Author:         bringthepixel
Version:        1.2.20
Author URI:     http://www.bringthepixel.com
Text Domain:    g1_socials
Domain Path:    /languages/
License: 		Located in the 'Licensing' folder
License URI: 	Located in the 'Licensing' folder
*/

// Prevent direct script access
if ( !defined('ABSPATH') )
	die ( 'No direct script access allowed' );
?>
<?php
if ( ! class_exists( 'G1_Socials' ) ):

	class G1_Socials {
		private $version = '1.2.20';
		private static $option_name = 'g1_socials';
		private static $items;

		/**
		 * The object instance
		 *
		 * @var G1_Socials
		 */
		private static $instance;

		/**
		 * Return the only existing instance of the object
		 *
		 * @return G1_Socials
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new G1_Socials();
			}

			return self::$instance;
		}

		/**
		 * Check whether the plugin is in dev mode
		 *
		 * @return bool
		 */
		public function in_dev_mode() {
			return defined( 'BTP_DEV' ) && BTP_DEV;
		}

		private function __construct() {
			// Standard hooks for plugins
			register_activation_hook( plugin_basename( __FILE__ ), array( $this, 'activate' ) );
			register_deactivation_hook( plugin_basename( __FILE__ ), array( $this, 'deactivate' ) );
			register_uninstall_hook( plugin_basename( __FILE__ ), array( 'G1_Socials', 'uninstall' ) );

			// Enable localization
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

			// Migrations.
			add_action( 'plugins_loaded', array( $this, 'run_migrations' ) );

			add_action( 'widgets_init', array( $this, 'register_widget' ) );

			require_once( plugin_dir_path( __FILE__ ) . '/includes/shortcodes/shortcodes.php' );
			require_once( plugin_dir_path( __FILE__ ) . '/includes/shortcodes/user.php' );

			if ( is_admin() ) {
				require_once( plugin_dir_path( __FILE__ ) . 'g1-socials-admin.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'options-page.php' );
			} else {
				require_once( plugin_dir_path( __FILE__ ) . 'g1-socials-front.php' );
			}

			require_once( plugin_dir_path( __FILE__ ) . '/includes/customizer.php' );
			require_once( plugin_dir_path( __FILE__ ) . '/includes/plugins/functions.php' );

			if ( apply_filters( 'g1_socials_support_snapchat', true ) ) {
				require_once( plugin_dir_path( __FILE__ ) . '/includes/snapchat/snapcode-init.php' );
			}

			if ( apply_filters( 'g1_socials_support_facebook', true ) ) {
				require_once( plugin_dir_path( __FILE__ ) . '/includes/facebook/facebook-init.php' );
			}

			add_action( 'init', array( $this, 'load_youtube' ), 0 );
			add_action( 'init', array( $this, 'load_instagram' ), 0 );
			add_action( 'init', array( $this, 'load_twitter' ), 0 );

			require_once( plugin_dir_path( __FILE__ ) . '/includes/users/init.php' );
		}

		public function run_migrations() {
			// @todo - replace with "upgrader_process_complete" action.
			// The hook didn't work so this is just a temporary workaround.

			// 1.2.13.
			if ( ! get_option( 'g1_socials_migration_v1_2_13' ) ) {
				delete_transient( 'g1-instagram-cache' );

				update_option( 'g1_socials_migration_v1_2_13', 'done' );
			}
		}

		public function load_youtube() {
			if ( $this->in_dev_mode() || apply_filters( 'g1_socials_support_youtube', false ) ) {
				require_once( plugin_dir_path( __FILE__ ) . '/includes/youtube/init.php' );
			}
		}

		public function load_instagram() {
			if ( $this->in_dev_mode() || apply_filters( 'g1_socials_support_instagram', false ) ) {
				require_once( plugin_dir_path( __FILE__ ) . '/includes/instagram/init.php' );
			}
		}

		public function load_twitter() {
			if ( apply_filters( 'g1_socials_support_twitter', false ) ) {
				require_once( plugin_dir_path( __FILE__ ) . '/includes/twitter/init.php' );
			}
		}

		public function load_textdomain() {
			load_plugin_textdomain( 'g1_socials', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		public function register_widget () {
			require_once( plugin_dir_path( __FILE__ ) . '/includes/widgets/widgets.php' );

			register_widget( 'G1_Socials_Widget' );
			register_widget( 'G1_Facebook_Page_Widget' );
		}

		public function get_plugin_dir_path() {
			return plugin_dir_path( __FILE__ );
		}

		public function get_plugin_dir_url() {
			return plugin_dir_url( __FILE__ );
		}

		public function get_version() {
			return $this->version;
		}

		public function get_option_name() {
			return self::$option_name;
		}

		public function activate() {
			$items = get_option( $this->get_option_name(), false );

			// not set already
			if ( $items === false ) {
				$defaults = array(
					'facebook' => array(
						'label'     => 'Facebook',
						'caption'   => 'Facebook',
						'link'      => 'https://www.facebook.com/YOUR_USERNAME/',
					),
					'twitter' => array(
						'label'     => 'Twitter',
						'caption'   => 'Twitter',
						'link'      => 'https://twitter.com/YOUR_USERNAME',
					)
				);

				update_option( $this->get_option_name(), $defaults );
			}
		}

		public function deactivate() {

		}

		public static function uninstall() {
			delete_option( self::$option_name );
		}

		public function get_defined_social_items () {
			$items = get_option( $this->get_option_name(), array() );
			$defined_items = array();

			foreach ( $items as $id => $data ) {
				if ( isset( $data['link'] ) && ! empty( $data['link'] ) ) {
					$defined_items[ $id ] = (object) array(
						'id'            => $id,
						'name'          => $data['label'] ? $data['label'] : $id,
						'title'         => $data['caption'] ? $data['caption'] : '',
						'description'   => '',
						'url'           => $data['link'],
					);
				}
			}

			return $defined_items;
		}

		public function get_items() {
			if (empty(self::$items)) {
				$items = apply_filters( 'g1_socials_items', $this->get_default_items() );
				$stored_icons = get_option($this->get_option_name());

				if ($stored_icons) {
					self::$items = array();

					foreach ($stored_icons as $icon_name => $icon_data) {
						if (!empty($items[$icon_name])) {
							self::$items[$icon_name] = $items[$icon_name];
						}

						unset($items[$icon_name]);
					}

					// some icons are still in items array (new added via hook)
					if (!empty($items)) {
						self::$items = array_merge(self::$items, $items);
					}
				} else {
					self::$items = $items;
				}
			}

			return self::$items;
		}

		private function get_default_items () {
			return array(
				'behance'       => 'behance',
				'codepen'       => 'codepen',
				'delicious'     => 'delicious',
				'deviantart'    => 'deviantart',
				'discord'       => 'discord',
				'digg'          => 'digg',
				'dribbble'      => 'dribbble',
				'facebook'      => 'facebook',
				'flickr'        => 'flickr',
				'foursquare'    => 'foursquare',
				'github'        => 'github',
				'instagram'     => 'instagram',
				'jsfiddle'      => 'jsfiddle',
				'lastfm'        => 'lastfm',
				'linkedin'      => 'linkedin',
				'pinterest'     => 'pinterest',
				'reddit'        => 'reddit',
				'snapchat'      => 'snapchat',
				'spotify'       => 'spotify',
				'stackoverflow' => 'stackoverflow',
				'tumblr'        => 'tumblr',
				'twitter'       => 'twitter',
				'vimeo'         => 'vimeo',
				'xing'          => 'xing',
				'yelp'          => 'yelp',
				'youtube'       => 'youtube',
			);
		}
	}
endif;

function G1_Socials() {
	return G1_Socials::get_instance();
}
// Fire in the hole :)
G1_Socials();

/**
 * Load a template part into a template
 *
 * This a plugin specific version of the get_template_part function:
 * http://codex.wordpress.org/Function_Reference/get_template_part
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function g1_socials_get_template_part( $slug, $name = '' ) {
	// Trim off any slashes from the slug
	$slug  = ltrim( $slug, '/' );

	if ( empty( $slug ) ) {
		return;
	}

	$parent_dir_path  = trailingslashit( get_template_directory() );
	$child_dir_path   = trailingslashit( get_stylesheet_directory() );

	$files = array(
		$child_dir_path . 'g1-socials/' . $slug . '.php',
		$parent_dir_path . 'g1-socials/' . $slug  . '.php',
		trailingslashit( G1_Socials()->get_plugin_dir_path() ) . 'templates/' . $slug  . '.php',
	);

	if ( ! empty( $name ) ) {
		array_unshift(
			$files,
			$child_dir_path . 'g1-socials/' . $slug . '-' . $name . '.php',
			$parent_dir_path . 'g1-socials/' . $slug . '-' .  $name . '.php',
			trailingslashit( G1_Socials()->get_plugin_dir_path() ) . 'templates/' . $slug . '-' . $name . '.php'
		);
	}

	$located = '';

	foreach ( $files as $file ) {
		if ( empty( $file ) ) {
			continue;
		}

		if ( file_exists( $file ) ) {
			$located = $file;
			break;
		}
	}

	if ( strlen( $located ) ) {
		load_template( $located, false );
	}
}

//must be in plugin due to wp boot sequence.
if ( defined( 'BTP_DEV' ) && BTP_DEV ) {
	add_filter( 'customize_loaded_components', 			'bimber_remove_widgets_panel_for_dev', 10, 2 );
}

/**
 * Remove the widgets panel in the dev mode (for performace)
 *
 * @param arrray               $components			Components.
 * @param WP_Customize_Manager $wp_customize        WP Customizer instance.
 * @return arrray
 */
function bimber_remove_widgets_panel_for_dev( $components, $wp_customize ) {
	$i = array_search( 'widgets', $components, true );
	if ( false !== $i ) {
		unset( $components[ $i ] );
	}
	return $components;
}

/**
 * Get AdAce Options slug. Its used in few places, this makes it easy to get/change.
 *
 * @return string Options page slug.
 */
function g1_socials_options_page_slug() {
	return 'g1_socials_options';
}
