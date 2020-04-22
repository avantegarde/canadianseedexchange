<?php
/**
Plugin Name:    Snax
Plugin URI:     http://www.snax.bringthepixel.com
Description:    Snax lets site visitors (via frontend) and editors (via backend) create quizzes, polls, lists, memes and other viral content.
Author:         bringthepixel
Version:        1.71
Author URI:     http://www.bringthepixel.com
Text Domain:    snax
Domain Path:    /languages/
Network:		false
License: 		Located in the 'Licensing' folder
License URI: 	Located in the 'Licensing' folder

@package snax
@subpackage Main
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Snax' ) ) :

	/**
	 * Main Snax class
	 */
	final class Snax {

		/**
		 * The Snax object instance
		 *
		 * @var Snax
		 */
		private static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public $version;

		/**
		 * Debug mode
		 *
		 * @var string
		 */
		public $debug_mode;

		/**
		 * Scripts version
		 *
		 * @var string
		 */
		public $scripts_version;

		/**
		 * Database version
		 *
		 * @var string
		 */
		public $db_version;

		/**
		 * Plugin settings name
		 *
		 * @var string
		 */
		public $option_name;

		/**
		 * Votes table name
		 *
		 * @var string
		 */
		public $votes_table_name;

		/**
		 * Polls table name
		 *
		 * @var string
		 */
		public $polls_table_name;

		/**
		 * Plugin filename
		 *
		 * @var string
		 */
		public $file;

		/**
		 * Plugin basename
		 *
		 * @var string
		 */
		public $basename;

		/**
		 * Plugin dir path
		 *
		 * @var string
		 */
		public $plugin_dir;

		/**
		 * Plugin dir url
		 *
		 * @var string
		 */
		public $plugin_url;

		/**
		 * Plugin assets dir path
		 *
		 * @var string
		 */
		public $assets_dir;

		/**
		 * Plugin assets dir url
		 *
		 * @var string
		 */
		public $assets_url;

		/**
		 * Plugin includes dir path
		 *
		 * @var string
		 */
		public $includes_dir;

		/**
		 * Plugin includes dir url
		 *
		 * @var string
		 */
		public $includes_url;

		/**
		 * Plugin languages dir path
		 *
		 * @var string
		 */
		public $languages_dir;

		/**
		 * Plugin templates dir path
		 *
		 * @var string
		 */
		public $templates_dir;

		/**
		 * Snax Item post type name
		 *
		 * @var string
		 */
		public $item_post_type;

		/**
		 * Translation domain
		 *
		 * @var string
		 */
		public $domain;

		/**
		 * Plugins extensions append to this (BuddyPress, etc...)
		 *
		 * @var stdClass
		 */
		public $plugins;

		/**
		 * Current posts query object
		 *
		 * @var WP_Query
		 */
		public $posts_query;

		/**
		 * Current cards query object
		 *
		 * @var WP_Query
		 */
		public $cards_query;

		/**
		 * Current votes query object
		 *
		 * @var WP_Query
		 */
		public $votes_query;

		/**
		 * Current collections query object
		 *
		 * @var WP_Query
		 */
		public $collections_query;

		/**
		 * Current items query object
		 *
		 * @var WP_Query
		 */
		public $items_query;

		/**
		 * Front-end submission page format
		 *
		 * @var string
		 */
		public $current_format;

		/**
		 * Return the only existing instance of Snax object
		 *
		 * @return Snax
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new Snax();
			}

			return self::$instance;
		}

		/**
		 * Set current format
		 *
		 * @param string $format		Snax format.
		 */
		public function set_current_format( $format ) {
			$this->current_format = $format;
		}

		/**
		 * Return current format
		 *
		 * @return string
		 */
		public function get_current_format() {
			return $this->current_format;
		}

		/**
		 * Snax constructor.
		 */
		private function __construct() {
			if ( function_exists( 'is_network_admin' ) ) {
				if ( is_network_admin() ) {
					return;
				}
			}
			$this->setup_globals();
			$this->includes();
			$this->setup_hooks();
		}

		/**
		 * Prevent object cloning
		 */
		private function __clone() {}

		/**
		 * Define plugin vars
		 */
		private function setup_globals() {

			/** Versions ********************************************************* */

			$this->version      = '1.71';
			$this->db_version   = '1.0';

			/** Database ********************************************************* */

			$this->option_name      = 'snax';
			$this->votes_table_name = 'snax_votes';
			$this->polls_table_name = 'snax_polls';

			/** Paths ************************************************************ */

			// Base.
			$this->file       = __FILE__;
			$this->basename   = apply_filters( 'snax_plugin_basename', plugin_basename( $this->file ) );
			$this->plugin_dir = apply_filters( 'snax_plugin_dir_path', plugin_dir_path( $this->file ) );
			$this->plugin_url = apply_filters( 'snax_plugin_dir_url', plugin_dir_url( $this->file ) );

			// Debug.
			$this->debug_mode      = defined( 'SNAX_DEVELOPER_MODE' ) ? constant( 'SNAX_DEVELOPER_MODE' ) : false;
			$this->scripts_version = $this->debug_mode ? '' : '.min';

			// Assets.
			$this->assets_dir = apply_filters( 'snax_assets_dir', trailingslashit( $this->plugin_dir . 'assets' ) );
			$this->assets_url = apply_filters( 'snax_assets_url', trailingslashit( $this->plugin_url . 'assets' ) );

			// CSS.
			$this->css_dir = apply_filters( 'snax_css_dir', trailingslashit( $this->plugin_dir . 'css' ) );
			$this->css_url = apply_filters( 'snax_css_url', trailingslashit( $this->plugin_url . 'css' ) );

			// Includes.
			$this->includes_dir = apply_filters( 'snax_includes_dir', trailingslashit( $this->plugin_dir . 'includes' ) );
			$this->includes_url = apply_filters( 'snax_includes_url', trailingslashit( $this->plugin_url . 'includes' ) );

			// Languages.
			$this->languages_dir = apply_filters( 'snax_languages_dir', trailingslashit( $this->plugin_dir . 'languages' ) );

			// Templates.
			$this->templates_dir = apply_filters( 'snax_templates_dir', trailingslashit( $this->plugin_dir . 'templates' ) );

			/** Identifiers ****************************************************** */

			// Post types.
			$this->item_post_type = apply_filters( 'snax_item_post_type', 'snax_item' );

			/** Misc ************************************************************* */

			$this->domain   = 'snax';           // Unique identifier for retrieving translated strings.
			$this->plugins  = new stdClass();   // Plugins add data here.
		}

		/**
		 * Include required files
		 */
		private function includes() {

			/** Core ************************************************************* */

			require( $this->includes_dir . 'core/capabilities.php' );
			require( $this->includes_dir . 'core/functions.php' );
			require( $this->includes_dir . 'core/hooks.php' );
			require( $this->includes_dir . 'core/install.php' );
			require( $this->includes_dir . 'core/mirror-functions.php' );
			require( $this->includes_dir . 'core/options.php' );
			require( $this->includes_dir . 'core/template-functions.php' );

			/** Components ******************************************************* */

			// Common.
			require( $this->includes_dir . 'common/ajax.php' );
			require( $this->includes_dir . 'common/functions.php' );
			require( $this->includes_dir . 'common/menu.php' );
			require( $this->includes_dir . 'common/media.php' );
			require( $this->includes_dir . 'common/widgets/widgets.php' );
			require( $this->includes_dir . 'common/shortcodes.php' );
			require( $this->includes_dir . 'common/popup.php' );
			require( $this->includes_dir . 'common/login.php' );
			require( $this->includes_dir . 'common/cron.php' );

			// Collections.
			if ( snax_collections_enabled() ) {
				require( $this->includes_dir . 'collections/loader.php' );
			}

			// Formats.
			require( $this->includes_dir . 'formats/loader.php' );

			// Posts.
			require( $this->includes_dir . 'posts/ajax.php' );
			require( $this->includes_dir . 'posts/capabilities.php' );
			require( $this->includes_dir . 'posts/functions.php' );
			require( $this->includes_dir . 'posts/template.php' );
			require( $this->includes_dir . 'posts/entry-actions.php' );

			// Items.
			require( $this->includes_dir . 'items/ajax.php' );
			require( $this->includes_dir . 'items/capabilities.php' );
			require( $this->includes_dir . 'items/functions.php' );
			require( $this->includes_dir . 'items/embeds.php' );
			require( $this->includes_dir . 'items/template.php' );

			// Votes.
			require( $this->includes_dir . 'votes/ajax.php' );
			require( $this->includes_dir . 'votes/capabilities.php' );
			require( $this->includes_dir . 'votes/functions.php' );
			require( $this->includes_dir . 'votes/template.php' );

			// Social Login.
			require( $this->includes_dir . 'social-login/loader.php' );

			// Shares.
			require( $this->includes_dir . 'shares/loader.php' );

			// Users.
			require( $this->includes_dir . 'users/functions.php' );
			require( $this->includes_dir . 'users/roles.php' );
			require( $this->includes_dir . 'users/capabilities.php' );

			// Frontend Submission.
			require( $this->includes_dir . 'frontend-submission/ajax.php' );
			require( $this->includes_dir . 'frontend-submission/functions.php' );
			require( $this->includes_dir . 'frontend-submission/demo.php' );
			require( $this->includes_dir . 'frontend-submission/edit.php' );
			require( $this->includes_dir . 'frontend-submission/cards.php' );
			require( $this->includes_dir . 'frontend-submission/embeds.php' );
			require( $this->includes_dir . 'frontend-submission/template.php' );
			require( $this->includes_dir . 'frontend-submission/memes.php' );

			// Quizzes.
			require( $this->includes_dir . 'quizzes/loader.php' );

			// Polls.
			require( $this->includes_dir . 'polls/loader.php' );

			// Plugins.
			require( $this->includes_dir . 'plugins/functions.php' );

			/** Admin ************************************************************ */

			if ( is_admin() ) {
				require( $this->includes_dir . 'admin/admin.php' );

				require( $this->includes_dir . 'admin/dashboard-widgets/snax-overview-dashboard-widget.php' );
			}
		}

		/**
		 * Setup the default actions and filters
		 */
		public function setup_hooks() {

			/** Standard plugin hooks ************************** */

			register_activation_hook( $this->basename, array( $this, 'activate' ) );
			register_deactivation_hook( $this->basename, array( $this, 'deactivate' ) );
			register_uninstall_hook( $this->basename, array( 'Snax', 'uninstall' ) );

			/** Init ******************************************* */

			add_action( 'init', array( $this, 'register_post_type' ), 12 );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

			/** Assets ***************************************** */

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Register dependend post types
		 */
		public function register_post_type() {
			$args = array(
				'labels' => array(
					'name'                  => _x( 'Snax Items', 'post type general name', 'snax' ),
					'singular_name'         => _x( 'Snax Item', 'post type singular name', 'snax' ),
					'menu_name'             => _x( 'Snax Items', 'admin menu', 'snax' ),
					'name_admin_bar'        => _x( 'Snax Item', 'add new on admin bar', 'snax' ),
					'add_new'               => _x( 'New Item', 'snax item', 'snax' ),
					'add_new_item'          => __( 'Add New Item', 'snax' ),
					'new_item'              => __( 'New Item', 'snax' ),
					'edit_item'             => __( 'Edit Item', 'snax' ),
					'view_item'             => __( 'View Item', 'snax' ),
					'all_items'             => __( 'All Items', 'snax' ),
					'search_items'          => __( 'Search Items', 'snax' ),
					'parent_item_colon'     => __( 'Parent Items:', 'snax' ),
					'not_found'             => __( 'No items found.', 'snax' ),
					'not_found_in_trash'    => __( 'No items found in Trash.', 'snax' ),
				),
				'public'                    => true,
				// Below values are inherited from the 'public' if not set.
				// ------.
				'exclude_from_search'       => false,        // for readers
				'publicly_queryable'        => true,        // for readers
				'show_in_nav_menus'         => false,       // for authors
				'show_ui'                   => true,        // for authors
				// ------.
				/**
				'capability_type'           => 'snax_item',
				'capabilities'               => array(
					// These capabilites can be assigned to roles.
					'publish_posts'         => 'snax_publish_items',        // This allows a user to publish an item.
					'edit_posts'            => 'snax_edit_items',           // Allows editing of the user’s own items but does not grant publishing permission.
					'edit_others_posts'     => 'snax_edit_others_items',    // Allows the user to edit everyone else’s items but not publish.
					'delete_posts'          => 'snax_delete_items',         // Grants the ability to delete items written by that user but not others’ items.
					'delete_others_posts'   => 'snax_delete_others_items',  // Capability to edit items written by other users.
					'read_private_posts'    => 'snax_read_private_items',   // Allows users to read private items.

					// Meta capabilities. Do not assign them to any role.
					'edit_post'             => 'snax_edit_item',            // Meta capability assigned by WordPress. Do not give to any role.
					'delete_post'           => 'snax_delete_item',          // Meta capability assigned by WordPress. Do not give to any role.
					'read_post'             => 'snax_read_item',            // Meta capability assigned by WordPress. Do not give to any role.
				),
				*/
				'supports'                  => array(
					'title',
					'editor',
					'author',
					'thumbnail',
					'comments',
				),
				'rewrite'                   => array(
					'slug'					=> snax_get_url_var( 'item' ),
					'feeds' 				=> true,
				),
				'has_archive'        => true,
			);

			register_post_type( 'snax_item', apply_filters( 'snax_item_post_type_args', $args ) );

			// Add post formats support to 'snax_item' post_type.
			add_post_type_support( 'snax_item', 'post-formats' );
		}

		/**
		 * Load plugin translations.
		 */
		public function load_textdomain() {
			load_plugin_textdomain( $this->domain, false, 'snax/languages' );
		}

		/**
		 * Load CSS.
		 */
		public function enqueue_styles() {

			/** Core (loaded all across the site) ************************** */

			// Popup.
			wp_enqueue_style( 'jquery-magnific-popup', $this->assets_url . 'js/jquery.magnific-popup/magnific-popup.css' );

			// Front.
			wp_enqueue_style( 'snax', $this->css_url . 'snax.min.css', array(), $this->version );
			wp_style_add_data( 'snax', 'rtl', 'replace' );
			wp_style_add_data( 'snax', 'suffix', '.min' );

			$is_list_open_for_contribution  = snax_is_format( 'list' ) && is_single();
			$is_frontend_submission_page    = snax_is_frontend_submission_page();
			$is_collection_edition_page     = is_singular( snax_get_collection_post_type() );

			$is_snax_page = $is_list_open_for_contribution || $is_frontend_submission_page || $is_collection_edition_page;

			// Frontend submission.
			if ( $is_snax_page ) {
				wp_enqueue_style( 'snax-frontend-submission', $this->css_url . 'snax-frontend-submission.min.css', array(), $this->version );
				wp_style_add_data( 'snax-frontend-submission', 'rtl', 'replace' );
				wp_style_add_data( 'snax-frontend-submission', 'suffix', '.min' );
			}

			// Preview mode.
			if ( is_preview() ) {
				wp_enqueue_style( 'snax-preview', $this->css_url . 'snax-preview.min.css', array('snax'), $this->version );
				wp_style_add_data( 'snax-preview', 'rtl', 'replace' );
				wp_style_add_data( 'snax-preview', 'suffix', '.min' );
			}


			if ( $is_snax_page ) {
				// Media element for MEJS player, for videos.
				wp_enqueue_style( 'wp-mediaelement' );

				wp_enqueue_style( 'jquery-tag-it', $this->assets_url . 'js/jquery.tagit/css/jquery.tagit.css', array(), '2.0' );
				wp_enqueue_style( 'jquery-tag-it-theme', $this->assets_url . 'js/jquery.tagit/css/tagit.ui-zendesk.css', array(), '2.0' );

				// Froala editor (Simple).
				wp_enqueue_style( 'snax-froala-editor', 		$this->assets_url . 'js/froala/css/froala_editor.min.css', array(), '2.3.4' );
				wp_enqueue_style( 'snax-froala-style',			$this->assets_url . 'js/froala/css/froala_style.min.css', array(), '2.3.4' );
				wp_enqueue_style( 'snax-froala-quick-insert',	$this->assets_url . 'js/froala/css/plugins/quick_insert.min.css', array(), '2.3.4' );
				wp_enqueue_style( 'snax-froala-char-counter',	$this->assets_url . 'js/froala/css/plugins/char_counter.min.css', array(), '2.3.4' );
				wp_enqueue_style( 'snax-froala-line-breaker',	$this->assets_url . 'js/froala/css/plugins/line_breaker.min.css', array(), '2.3.4' );

				if ( snax_is_format_submission_page( 'text' ) ) {
					// Froala editor (Rich).
					wp_enqueue_style( 'snax-froala-draggable',		$this->assets_url . 'js/froala/css/plugins/draggable.min.css', array(), '2.3.4' );
					wp_enqueue_style( 'snax-froala-image',			$this->assets_url . 'js/froala/css/plugins/image.min.css', array(), '2.3.4' );
					wp_enqueue_style( 'snax-froala-image-manager',	$this->assets_url . 'js/froala/css/plugins/image_manager.min.css', array(), '2.3.4' );
					wp_enqueue_style( 'snax-froala-video',			$this->assets_url . 'js/froala/css/plugins/video.min.css', array(), '2.3.4' );
				}

				// Froala requires FontAwesome.
				wp_enqueue_style( 'font-awesome', $this->assets_url . 'font-awesome/css/font-awesome.min.css' );
			}

			// Enqueue icon font used in social login buttons.
			if ( snax_can_use_plugin( 'wordpress-social-login/wp-social-login.php' ) ) {
				// We don't need to prefix it, because it's not plugin specific.
				wp_enqueue_style( 'font-awesome', $this->assets_url . 'font-awesome/css/font-awesome.min.css' );
			}
		}

		/**
		 * Load javascripts.
		 */
		public function enqueue_scripts() {
			$developer_mode = defined( 'SNAX_DEVELOPER_MODE' ) ? constant( 'SNAX_DEVELOPER_MODE' ) : false;

			/** Core ************************************** */

			// Popup.
			wp_enqueue_script( 'jquery-magnific-popup', $this->assets_url . 'js/jquery.magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );

			// Convert dates into timestamps.
			wp_enqueue_script( 'jquery-timeago', $this->assets_url . 'js/jquery.timeago/jquery.timeago.js', array( 'jquery' ), '1.5.2', true );
			$this->localize_timeago_script();

			$is_list_open_for_contribution  = snax_is_format( 'list' ) && is_single();
			$is_frontend_submission_page    = snax_is_frontend_submission_page();
			$is_collection_editon_page      = is_singular( snax_get_collection_post_type() );

			$is_snax_page = $is_list_open_for_contribution || $is_frontend_submission_page ||$is_collection_editon_page ;

			$deps = array( 'jquery' );

			if ( $is_snax_page ) {
				wp_enqueue_script( 'snax-plupload-handlers', $this->assets_url . 'js/plupload/handlers.js', array( 'plupload', 'jquery' ), $this->version, true );
				$this->localize_plupload_script( 'snax-plupload-handlers' );

				$deps[] = 'snax-plupload-handlers';
			}

			// Front needs to loaded on all pages (voting, login popup).
			wp_enqueue_script( 'snax-front', $this->assets_url . 'js/front.js', $deps, $this->version, true );

			// Front config.
			$front_config = array(
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'site_url'                  => get_site_url(),
				'autosave_interval'         => (int) constant( 'AUTOSAVE_INTERVAL' ),
				'use_login_recaptcha'       => snax_is_recatpcha_enabled_for_login_form(),
				'recaptcha_api_url'         => snax_get_recaptcha_js_api_url(),
				'recaptcha_version'         => snax_get_recaptcha_version(),
				'recaptcha_site_key'        => snax_get_recaptcha_site_key(),
				'enable_login_popup'	    => snax_enable_login_popup(),
				'login_url'				    => wp_login_url(),
				'login_popup_url_var'	    => snax_get_login_popup_url_variable(),
				'logged_in'				    => is_user_logged_in(),
				'login_success_var'		    => snax_get_url_var( 'login_success' ),
				'delete_status_var'         => snax_get_url_var( 'delete_status' ),
				'i18n' => array(
					'are_you_sure'          => __( 'Are you sure?', 'snax' ),
					'recaptcha_invalid' => snax_get_recaptcha_invalid_message(),
					'passwords_dont_match'	=> __( "Passwords don't match.", 'snax' ),
					'link_invalid'			=> __( 'Your password reset link appears to be invalid or expired.', 'snax' ),
					'password_set'			=> __( 'New password has been set', 'snax' ),
					'duplicate_comment'	    => __( 'Duplicate comment detected; it looks as though you&#8217;ve already said that!', 'snax' ),
					'comment_fail'		    => __( 'Comment Submission Failure', 'snax' ),
					'see_all_replies'	    => __( 'See all replies', 'snax' ),
					'user_is_logging'	    => __( 'Please wait. You are logging in&hellip;', 'snax' ),
					'points_singular_tpl'   => _n( '<strong>%d</strong> point', '<strong>%d</strong> points', (int) 1, 'snax' ),
					'points_plural_tpl'     => _n( '<strong>%d</strong> point', '<strong>%d</strong> points', (int) 0, 'snax' ),
                    'popup_close_label'     => _x( 'Close (Esc)', 'Popup', 'snax' ),
				),
			);

			if ( current_user_can( 'administrator' ) || $this->debug_mode ) {
				$front_config['debug_mode'] = true;
			}

			wp_localize_script( 'snax-front', 'snax_front_config', wp_json_encode( $front_config ) );

			/** Featured Image **************************** */

			if ( $is_snax_page ) {
				wp_enqueue_script( 'snax-featured-image', $this->assets_url . 'js/featured-image.js', array( 'snax-front' ), $this->version, true );
			}

			/** List ************************************** */

			if ( $is_list_open_for_contribution ) {
				// Media element for MEJS player, for videos.
				wp_enqueue_script( 'wp-mediaelement' );

				wp_enqueue_script( 'snax-add-to-list', $this->assets_url . 'js/add-to-list.js', array( 'snax-front' ), $this->version, true );

				$froala_suffix = $developer_mode ? '' : '.min';

				// Froala editor (Simple).
				wp_enqueue_script( 'snax-froala-editor',		$this->assets_url . 'js/froala/js/froala_editor' . $froala_suffix . '.js', array( 'jquery' ), '2.3.4', true );
				$froala_editor_language = $this->load_froala_editor_translation();
				wp_enqueue_script( 'snax-froala-link',	 		$this->assets_url . 'js/froala/js/plugins/link' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
				wp_enqueue_script( 'snax-froala-lists', 		$this->assets_url . 'js/froala/js/plugins/lists' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
				wp_enqueue_script( 'snax-froala-quick-insert', 	$this->assets_url . 'js/froala/js/plugins/quick_insert' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
				wp_enqueue_script( 'snax-froala-char-counter', 	$this->assets_url . 'js/froala/js/plugins/char_counter' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
				wp_enqueue_script( 'snax-froala-line-breaker', 	$this->assets_url . 'js/froala/js/plugins/line_breaker' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );

				$add_to_list_config['froala'] = array(
					'language'					=> $froala_editor_language,
					'async_upload_url'			=> admin_url( 'async-upload.php' ),
					'image_max_size' 			=> snax_get_image_max_upload_size(),
					'image_allowed_types'		=> snax_get_image_allowed_types(),
				);

				$add_to_list_config['i18n'] = array(
					'are_you_sure'          => __( 'Are you sure?', 'snax' ),
					'multi_drop_forbidden'  => __( 'You can drop only one file here.', 'snax' ),
				);

				wp_localize_script( 'snax-add-to-list', 'snax_add_to_list_config', wp_json_encode( $add_to_list_config ) );
			}

			/** Submit form ****************************** */
			if ( $is_frontend_submission_page ) {
				// Media element for MEJS player, for videos.
				wp_enqueue_script( 'wp-mediaelement' );

				// Enqueue input::placeholder polyfill for IE9.
				wp_enqueue_script( 'jquery-placeholders', $this->assets_url . 'js/jquery.placeholder/placeholders.jquery.min.js', array( 'jquery' ), '4.0.1', true );

				// Tag editing UI snippet.
				wp_enqueue_script( 'jquery-tag-it', $this->assets_url . 'js/jquery.tagit/js/tag-it.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-autocomplete' ), '2.0', true );

				// Fabris.js.
				wp_enqueue_script( 'snax-fabric', $this->assets_url . 'js/fabric/fabric.min.js', array(), '1.7.22', true );

				wp_enqueue_script( 'snax-front-submission', 		$this->assets_url . 'js/front-submission.js', array( 'snax-front' ), $this->version, true );

				// Client-Side Image Resize.
				$large_size_h = absint( get_option('large_size_h') );
				if ( ! $large_size_h ) { $large_size_h = 1024; }

				$large_size_w = absint( get_option('large_size_w') );
				if ( ! $large_size_w ) { $large_size_w = 1024; }

				$tags_taxonomy = apply_filters( 'snax_front_submission_tags_taxonomy', 'post_tag' );

				$front_submission_config = array(
					'tags_limit' 				=> snax_get_tags_limit(),
					'tags' 						=> snax_get_tags_array(
						apply_filters( 'snax_tags_autoloaded_limit', 100 ),
						apply_filters( 'snax_tags_autoloaded_args', array() )
					),
					'tags_force_ajax'			=> apply_filters( 'snax_tags_force_ajax', false ),
					'items_limit' 				=> snax_get_new_post_items_limit(),
					'assets_url' 				=> snax_get_assets_url(),
					'featured_media_required'	=> snax_is_featured_media_field_required( snax_get_submission_page_format() ),
					'allow_froala_for_items'	=> snax_froala_for_items(),
					'image_downsized_width'     => $large_size_w,
					'image_downsized_height'    => $large_size_h,
					'meme_font'					=> apply_filters( 'snax_meme_font_family', 'Impact' ),
				);

				$froala_suffix = $developer_mode ? '' : '.min';

				// Froala editor (Simple).
				wp_enqueue_script( 'snax-froala-editor',		$this->assets_url . 'js/froala/js/froala_editor' . $froala_suffix . '.js', array( 'jquery' ), '2.3.4', true );
				$froala_editor_language = $this->load_froala_editor_translation();
				wp_enqueue_script( 'snax-froala-link',	 		$this->assets_url . 'js/froala/js/plugins/link' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
				wp_enqueue_script( 'snax-froala-lists', 		$this->assets_url . 'js/froala/js/plugins/lists' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
				wp_enqueue_script( 'snax-froala-quick-insert', 	$this->assets_url . 'js/froala/js/plugins/quick_insert' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
				wp_enqueue_script( 'snax-froala-char-counter', 	$this->assets_url . 'js/froala/js/plugins/char_counter' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
				wp_enqueue_script( 'snax-froala-line-breaker', 	$this->assets_url . 'js/froala/js/plugins/line_breaker' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );

				if ( snax_is_format_submission_page( 'text' ) ) {
					// Froala editor (Rich).
					wp_enqueue_script( 'snax-froala-draggable',		$this->assets_url . 'js/froala/js/plugins/draggable' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
					wp_enqueue_script( 'snax-froala-image',	 		$this->assets_url . 'js/froala/js/plugins/image' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
					wp_enqueue_script( 'snax-froala-image-manager',	$this->assets_url . 'js/froala/js/plugins/image_manager' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
					wp_enqueue_script( 'snax-froala-custom-video',	$this->assets_url . 'js/froala-custom/js/plugins/video' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
					wp_enqueue_script( 'snax-froala-p-format', 		$this->assets_url . 'js/froala/js/plugins/paragraph_format' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );
					wp_enqueue_script( 'snax-froala-quote', 		$this->assets_url . 'js/froala/js/plugins/quote' . $froala_suffix . '.js', array( 'snax-froala-editor' ), '2.3.4', true );

					// Text format.
					wp_enqueue_script( 'snax-front-submission-text',	$this->assets_url . 'js/front-submission-text.js', array( 'snax-front-submission', 'snax-froala-editor' ), $this->version, true );
				}
				$quality = apply_filters( 'wp_editor_set_quality', 82, 'image/jpeg' );
				$front_submission_config['jpg_quallity'] = $quality;

				$front_submission_config['froala'] = array(
					'language'				=> $froala_editor_language,
					'async_upload_url'		=> admin_url( 'async-upload.php' ),
					'image_max_size' 		=> snax_get_image_max_upload_size(),
					'image_allowed_types'	=> snax_get_image_allowed_types(),
				);

				// i18n.
				$front_submission_config['i18n'] = array(
					'are_you_sure'          => __( 'Are you sure?', 'snax' ),
					'no_featured_uploaded'  => __( 'Please upload the featured image', 'snax' ),
					'no_files_chosen'       => __( 'You need to choose at least one file/embed!', 'snax' ),
					'meme_generation_failed'=> __( 'Meme image generation failed!', 'snax' ),
					'meme_top_text'         => __( 'Top text&hellip;', 'snax' ),
					'meme_bottom_text'      => __( 'Bottom text&hellip;', 'snax' ),
					'multi_drop_forbidden'  => __( 'You can drop only one file here.', 'snax' ),
					'upload_failed'  		=> __( 'Upload failed. Check if the file is a valid image.', 'snax' ),
					'link_processing_text'  => __( 'Processing page meta data...', 'snax' ),
					'extproduct_processing_text'  => __( 'Processing product data...', 'snax' ),
				);

				wp_localize_script( 'snax-front-submission', 'snax_front_submission_config', wp_json_encode( $front_submission_config ) );
			}
		}

		/**
		 * Load Froala translation if exists
		 *
		 * @return mixed		Loaded language or null if not exits.
		 */
		public function load_froala_editor_translation() {
			$locale       = strtolower( get_locale() );
			$locale_parts = explode( '_', $locale );

			// Check by full locale code (eg. pt_br).
			$language_id  = $locale;

			// If translation for that code doesn't exist, try to use only lang code (eg. pt).
			if ( ! file_exists( $this->assets_dir . 'js/froala/js/languages/' . $language_id . '.js' ) ) {
				$language_id = $locale_parts[0];
			}

			if ( ! file_exists( $this->assets_dir . 'js/froala/js/languages/' . $language_id . '.js' ) ) {
				return null;
			}

			// Use this filter in case you need to map resolved language id to some other locale.
			$language_id = apply_filters( 'snax_froala_editor_language_id', $language_id );

			wp_add_inline_script( 'snax-froala-editor', 'var _$ = $; $ = jQuery; // Now $ points to jQuery. Fix for Froala language loading bug.' );
			wp_enqueue_script( 'snax-froala-editor-' . $language_id, $this->assets_url . 'js/froala/js/languages/' . $language_id . '.js', array( 'snax-froala-editor' ), null, true );
			wp_add_inline_script( 'snax-froala-editor-' . $language_id, '$ = _$; // Restore original $.' );

			return $language_id;
		}

		/**
		 * Run during plugin activation
		 *
		 * @param bool $network_wide Whether or not it's a network wide activation.
		 */
		public function activate( $network_wide ) {
			if ( $network_wide ) {
				deactivate_plugins( $this->basename, true, true );
				header( 'Location: ' . network_admin_url( 'plugins.php?deactivate=true' ) );
				exit;
			} else {
				do_action( 'snax_activation' );
			}
		}

		/**
		 * Run during plugin deactivation
		 */
		public function deactivate( $network_wide ) {
			do_action( 'snax_deactivation' );
		}

		/**
		 * Run during plugin uninstallation
		 */
		public static function uninstall() {
			do_action( 'snax_uninstall' );
		}

		/**
		 * Load translation for the timeago script
		 */
		protected function localize_timeago_script() {
			$locale       = get_locale();
			$locale_parts = explode( '_', $locale );
			$lang_code    = $locale_parts[0];

			$exceptions_map = array(
				'pt_BR' => 'pt-br',
				'zh_CN' => 'zh-CN',
				'zh_TW' => 'zh-TW',
			);

			$script_i10n_ext = $lang_code;

			if ( isset( $exceptions_map[ $locale ] ) ) {
				$script_i10n_ext = $exceptions_map[ $locale ];
			}

			// Check if translation file exists in "locales" dir.
			if ( ! file_exists( $this->assets_dir . 'js/jquery.timeago/locales/jquery.timeago.' . $script_i10n_ext . '.js' ) ) {
				return;
			}

			wp_enqueue_script( 'jquery-timeago-' . $script_i10n_ext, $this->assets_url . 'js/jquery.timeago/locales/jquery.timeago.' . $script_i10n_ext . '.js', array( 'jquery-timeago' ), null, true );
		}

		protected function localize_plupload_script( $handle ) {
			$i18n = array(
				'queue_limit_exceeded'      => __('You have attempted to queue too many files.'),
				'file_exceeds_size_limit'   => __('%s exceeds the maximum upload size for this site.'),
				'zero_byte_file'            => __('This file is empty. Please try another.'),
				'invalid_filetype'          => __('This file type is not allowed. Please try another.'),
				'not_an_image'              => __('This file is not an image. Please try another.'),
				'image_memory_exceeded'     => __('Memory exceeded. Please try another smaller file.'),
				'image_dimensions_exceeded' => __('This is larger than the maximum size. Please try another.'),
				'default_error'             => __('An error occurred in the upload. Please try again later.'),
				'missing_upload_url'        => __('There was a configuration error. Please contact the server administrator.'),
				'upload_limit_exceeded'     => __('You may only upload 1 file.'),
				'http_error'                => __('HTTP error.'),
				/* translators: 1: Opening link tag, 2: Closing link tag */
				'big_upload_failed'         => __('Please try uploading this file with the %1$sbrowser uploader%2$s.'),
				'big_upload_queued'         => __('%s exceeds the maximum upload size for the multi-file uploader when used in your browser.'),
				'io_error'                  => __('IO error.'),
				'security_error'            => __('Security error.'),
				'file_cancelled'            => __('File canceled.'),
				'upload_stopped'            => __('Upload stopped.'),
				'dismiss'                   => __('Dismiss'),
				'crunching'                 => __('Crunching&hellip;'),
				'deleted'                   => __('moved to the trash.'),
				'error_uploading'           => __('&#8220;%s&#8221; has failed to upload.'),
				'are_you_sure'          => __( 'Are you sure?', 'snax' ),
				'multi_drop_forbidden'  => __( 'You can drop only one file here.', 'snax' ),
				'upload_failed'  		=> __( 'Upload failed. Check if the file is a valid image.', 'snax' ),
				'invalid_url'  		    => __( 'Upload failed. Provided URL is not valid.', 'snax' ),
			);

			wp_localize_script( $handle, 'snax_plupload_i18n', $i18n );
		}
	}

	/**
	 * The main function responsible for returning the Snax instance.
	 *
	 * @return Snax
	 */
	function snax() {
		return Snax::get_instance();
	}

	snax();

endif;



//
// @todo wesoly Move code to appropriate file.
//
add_filter( 'get_the_archive_title', 'snax_get_the_archive_title' );
/**
 * Change Snax Format archive page title.
 *
 * @param $title string Archive title.
 *
 * @return string
 */
function snax_get_the_archive_title( $title ) {
	// @todo wesoly 'snax_format' shouldn't be string, right?
	if ( is_tax( 'snax_format' ) ) {
		$title = single_term_title( '', false );
	}

	return $title;
}


add_action( 'wp_footer', 'snax_render_notifications_template' );
function snax_render_notifications_template() {
	snax_get_template_part( 'notifications' );
}


add_action( 'wp_footer', 'snax_render_add_to_collection_popup' );
function snax_render_add_to_collection_popup() {
	snax_get_template_part( 'collections/popup-add-to' );
}


function snax_render_svg( $sprite, $target, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'width' => 200,
		'height' => 200,
	) );


	$href = trailingslashit( snax()->plugin_url ) . 'assets/svg/';
	$href = $href . $sprite . '.svg#' . $target;
	?>
	<svg viewbox="0 0 200 200" width="<?php echo (int) $args['width']; ?>" height="<?php echo (int) $args['height']; ?>">
		<use xlink:href="<?php echo esc_url( $href ); ?>" />
	</svg>
	<?php
}
