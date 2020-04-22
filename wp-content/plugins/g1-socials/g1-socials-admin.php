<?php
// Prevent direct script access
if ( !defined('ABSPATH') )
	die ( 'No direct script access allowed' );
?>
<?php
if ( ! class_exists( 'G1_Socials_Admin' ) ):

	class G1_Socials_Admin {

		/**
		 * The object instance
		 *
		 * @var G1_Socials_Admin
		 */
		private static $instance;

		/**
		 * Return the only existing instance of the object
		 *
		 * @return G1_Socials_Admin
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new G1_Socials_Admin();
			}

			return self::$instance;
		}

		private function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			// Load css/js resources
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		public function admin_init() {
			register_setting( G1_Socials()->get_option_name(), G1_Socials()->get_option_name() );
		}

		public function register_translation_strings () {
			if ( !function_exists( 'icl_t' ) ) {
				return;
			}

			$icons = get_option( G1_Socials()->get_option_name(), array() );

			foreach ( $icons as $name => $data ) {
				icl_register_string('G1 Socials', $name . ' ' . esc_html__( 'label', 'g1_socials' ), $data['label'] );
				icl_register_string('G1 Socials', $name . ' ' . esc_html__( 'caption', 'g1_socials' ), $data['caption'] );
			}
		}

		public function get_item_value ( $name ) {
			$options = get_option(G1_Socials()->get_option_name());
			$value = !empty($options[$name]) ? $options[$name]: array();

			$default_value = array(
				'label'     => '',
				'caption'   => '',
				'link'      => '',
			);

			return wp_parse_args( $value, $default_value );
		}

		public function enqueue_styles( $hook ) {
			$url = trailingslashit( $this->get_plugin_object()->get_plugin_dir_url() );

			if ( 'post-new.php' === $hook || 'post.php' == $hook ) {
				wp_enqueue_style( 'g1-socials-shortcode', $url . 'css/g1-socials-shortcode.css', array(), $this->get_plugin_object()->get_version() );
			}

			wp_enqueue_style( 'g1-socials-admin',   $url . 'css/admin.css', array(), $this->get_plugin_object()->get_version() );
			wp_style_add_data( 'g1-socials-admin', 'rtl', 'replace' );
		}

		public function enqueue_scripts( $hook ) {
			$url = trailingslashit( $this->get_plugin_object()->get_plugin_dir_url() );
			if ( 'settings_page_g1_socials_options' == $hook ) {
				wp_enqueue_script('jquery-ui-sortable');
			}
			if ( in_array( $hook, array( 'widgets.php' ), true ) ) {
				wp_enqueue_media();
				wp_enqueue_script( 'bimber-category', $url . 'js/g1-widgets-admin.js' );
			}
		}

		private function get_plugin_object () {
			return G1_Socials();
		}
	}
endif;

function G1_Socials_Admin() {
	return G1_Socials_Admin::get_instance();
}
// Fire in the hole :)
G1_Socials_Admin();
