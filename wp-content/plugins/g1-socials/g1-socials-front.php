<?php
// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );

if ( ! class_exists( 'G1_Socials_Front' ) ):

    class G1_Socials_Front {

        /**
         * The object instance
         *
         * @var G1_Socials_Front
         */
        private static $instance;

        /**
         * Return the only existing instance of the object
         *
         * @return G1_Socials_Front
         */
        public static function get_instance() {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new G1_Socials_Front();
            }

            return self::$instance;
        }

        private function __construct() {
            $this->setup_hooks();
        }

        public function setup_hooks() {
            add_action( 'wp_footer', array( $this, 'enqueue_styles' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'style_loader_tag', array( $this, 'fix_rtl_styles' ), 10, 4 );
        }

        public function enqueue_styles() {
            $url = trailingslashit( $this->get_plugin_object()->get_plugin_dir_url() );
            $version = $this->get_plugin_object()->get_version();

            wp_enqueue_style( 'g1-socials-basic-screen', $url . 'css/screen-basic.min.css', array(), $version );
            wp_style_add_data( 'g1-socials-basic-screen', 'rtl', 'replace' );

			if ( apply_filters( 'g1_socials_support_snapchat', true ) ) {
				wp_enqueue_style( 'g1-socials-snapcode',    $url . 'css/snapcode.min.css', array(), $version );
			}
        }

        /**
         * Fix RTL styles.
         *
         * @param string $html   The link tag for the enqueued style.
         * @param string $handle The style's registered handle.
         * @param string $href   The stylesheet's source URL.
         * @param string $media  The stylesheet's media attribute.
         * @return string
         */
        public function fix_rtl_styles( $html, $handle, $href, $media ){
            if ( strpos( $handle, 'g1-socials' ) > -1 ) {
                $html = str_replace( '.min-rtl', '-rtl.min', $html );
            }
            return $html;
        }

        public function enqueue_scripts( $hook ) {
        }

        private function get_plugin_object () {
            return G1_Socials();
        }
    }
endif;

if ( ! function_exists( 'G1_Socials_Front' ) ) :

    function G1_Socials_Front() {
        return G1_Socials_Front::get_instance();
    }

endif;

G1_Socials_Front();


/**
 * Load AMP (Accelerated Mobile Pages) CSS
 */
function g1_socials_amp_load_css() {
	$css = '';
	$css .= file_get_contents( esc_url( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'css/screen-basic.min.css' ) );
	echo( $css );
}
