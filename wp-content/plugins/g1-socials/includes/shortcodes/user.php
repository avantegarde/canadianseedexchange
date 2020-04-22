<?php

/**
 * Get user social links
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class G1_Socials_User_Shortcode {
	private $id;
	private $elements;
	private $items;
	private static $counter;

	/**
	 * The object instance
	 *
	 * @var G1_Socials_User_Shortcode
	 */
	private static $instance;

	/**
	 * Return the only existing instance of the object
	 *
	 * @return G1_Socials_User_Shortcode
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new G1_Socials_User_Shortcode();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->id = 'g1_socials_user';
		$this->elements = array(
			'icon'    => 'icon',
			'label'   => 'label',
			'caption' => 'caption',
		);

		$this->setup_hooks();
	}

	protected function setup_hooks() {
		add_shortcode( $this->id, array( $this, 'do_shortcode' ) );
	}

	public function get_id() {
		return $this->id;
	}

	/**
	 * Shortcode callback function.
	 *
	 * @return string
	 */
	public function do_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'user'       => '',
			'template'   => 'grid',
			'icon_size'  => '32',
			'icon_color' => 'original',
		), $atts, 'g1_socials' ) );

		if ( empty( $user ) ) {
			return;
		}

		$icon_size = absint( $icon_size );

		// Backward compatibility.
		if ( 'light' === $icon_color || 'dark' === $icon_color ) {
			$icon_color = 'text';
		}

		$data = get_the_author_meta( 'g1_socials', $user );
		if ( ! is_array( $data ) ) {
			$data = array();
		}

		// Remove invalid or depreciated networks.
		$data = array_intersect_key( $data, g1_socials_user_get_supported_networks() );

		global $g1_socials_items;
		// Rewrite it to match format used in shortcode.
		// I dont know who wrote this originaly, but he sux a lot.
		foreach ( $data as $social_key => $social_link ) {
			if ( ! empty( $social_link ) ) {
				$g1_socials_items[ $social_key ] = (object) array(
					'id'          => $social_key,
					'name'        => $social_key,
					'title'       => '',
					'description' => '',
					'url'         => $social_link,
				);
			}
		}

		global $g1_socials_shortcode;
		$g1_socials_shortcode = array();
		$g1_socials_shortcode['final_id'] = ! empty( $id ) ? $id : 'g1-social-icons-' . $this->get_counter();
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

	protected  function get_counter() {
		if ( empty( self::$counter ) ) {
			self::$counter = 0;
		}
		self::$counter++;
		return self::$counter;
	}

	protected function get_collection_templates() {
		$templates = array(
			'list-vertical'   => esc_html__( 'list-vertical', 'g1_socials' ),
			'list-horizontal' => esc_html__( 'list-horizontal', 'g1_socials' ),
		);

		return apply_filters( 'g1_socials_collection_templates', $templates );
	}

	protected function get_collection_sizes() {
		$templates = array(
			'16' => '16',
			'24' => '24',
			'32' => '32',
			'48' => '48',
			'64' => '64',
		);

		return apply_filters( 'g1_socials_collection_sizes', $templates );
	}

	function string_to_bools( $string ) {
		$string = preg_replace( '/[^0-9a-zA-Z,_-]*/', '', $string );

		$results = array();
		$bools = explode( ',', $string );

		foreach ( $bools as $key => $value ) {
			$results[ $value ] = true;
		}

		return $results;
	}
}

function G1_Socials_User_Shortcode() {
	return G1_Socials_User_Shortcode::get_instance();
}

G1_Socials_User_Shortcode();
