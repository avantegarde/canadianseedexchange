<?php

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php
if ( ! class_exists( 'G1_Socials_Shortcode' ) ):

class G1_Socials_Shortcode {
    private $id;
    private $elements;
    private $items;
    private static $counter;

    /**
     * The object instance
     *
     * @var G1_Socials_Shortcode
     */
    private static $instance;

    /**
     * Return the only existing instance of the object
     *
     * @return G1_Socials_Shortcode
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new G1_Socials_Shortcode();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->id = 'g1_socials';
        $this->elements = array(
            'icon'      => 'icon',
            'label'     => 'label',
            'caption'   => 'caption',
        );

        $this->setup_hooks();
    }

    protected function setup_hooks () {
        add_shortcode( $this->id, array($this, 'do_shortcode') );
    }

    public function get_id () {
        return $this->id;
    }

    public function get_item ( $name ) {
        if ( empty( $this->items ) ) {
            $this->items = get_option( $this->get_id() );
        }

        return !empty($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * Shortcode callback function.
     *
     * @return string
     */
    public function do_shortcode ( $atts ) {
        extract( shortcode_atts( array(
            'include' => '',
            'exclude' => '',
            'template' => 'grid',
            'icon_size' => '32',
            'icon_color' => 'original',
        ), $atts, 'g1_socials' ) );

        $icon_size = absint( $icon_size );

        // Backward compatibility.
        if ( 'light' === $icon_color || 'dark' === $icon_color ) {
            $icon_color = 'text';
        }

        $data = get_option( G1_Socials()->get_option_name(), array() );

        $feeds = array();

        // Process the 'include' variable
        $include = explode(',', $include);
        foreach ( $include as $feed ) {
            $feed = preg_replace('/[^a-zA-Z0-9_-]*/', '', $feed);

            if( !empty( $feed ) ) {
                $val = $this->get_item($feed);

                if ( $val !== null && strlen( $val['link'] ) ) {
                    $feeds[$feed] = $val;
                }
            }
        }

        // Populate 'feeds' array only if there are no feeds from the 'include' variable
        if ( !count( $feeds ) ) {
            foreach ( $data as $item_id => $item_args ) {
                if ( count( $item_args ) && isset( $item_args['link'] ) && ! empty( $item_args['link'] ) ) {
                    $feeds[ str_replace( 'feed_', '', $item_id)] = $item_args;
                }
            }

            // Exclude feeds bapadding-left:21px; sed on the 'exclude' variable
            if ( count( $feeds ) ) {
                $exclude = explode(',', $exclude);
                foreach ( $exclude as $feed ) {
                    $feed = preg_replace('/[^a-zA-Z0-9_-]*/', '', $feed);

                    if ( isset($feeds[$feed] ) )
                        unset($feeds[$feed]);
                }
            }
        }

        /**
         * @todo
         *
         * Zdynamizować zmienną $g1_socials_items
         */
        global $g1_socials_items;

        $g1_socials_items = G1_Socials()->get_defined_social_items();

		foreach( $g1_socials_items as $g1_socials_item_id => $g1_socials_item_config ) {
			if ( ! key_exists( $g1_socials_item_id, $feeds ) ) {
				unset( $g1_socials_items[ $g1_socials_item_id ] );
			}
		}

        global $g1_socials_shortcode;
        $g1_socials_shortcode = array();

        $g1_socials_shortcode['final_id'] = !empty( $id ) ? $id : 'g1-social-icons-' . $this->get_counter();

        $g1_socials_shortcode['final_class'] = array(
            'g1-social-icons',
        );

        $g1_socials_shortcode['icon_size'] = $icon_size;
        $g1_socials_shortcode['icon_color'] = $icon_color;

        ob_start();
        g1_socials_get_template_part( 'collection', $template );
        $out = ob_get_clean();

        unset( $GLOBALS['g1_socials_items'] );
        unset( $GLOBALS['g1_socials_shortcode'] );

        return $out;
    }

    protected  function get_counter () {
        if ( empty(self::$counter) ) {
            self::$counter = 0;
        }

        self::$counter++;

        return self::$counter;
    }

    protected function get_collection_templates() {
        $templates = array(
            'list-vertical'		=> esc_html__( 'list-vertical', 'g1_socials' ),
            'list-horizontal'	=> esc_html__( 'list-horizontal', 'g1_socials' ),
        );

        return apply_filters( 'g1_socials_collection_templates', $templates );
    }

    protected function get_collection_sizes() {
        $templates = array(
            '16'    => '16',
            '24'    => '24',
            '32'    => '32',
            '48'    => '48',
            '64'    => '64',
        );

        return apply_filters( 'g1_socials_collection_sizes', $templates );
    }

    function string_to_bools( $string ) {
        $string = preg_replace( '/[^0-9a-zA-Z,_-]*/', '', $string );

        $results = array();
        $bools = explode( ',', $string );

        foreach ( $bools as $key => $value )
            $results[$value] = true;

        return $results;
    }
}
endif;

function G1_Socials_Shortcode() {
    return G1_Socials_Shortcode::get_instance();
}

G1_Socials_Shortcode();
