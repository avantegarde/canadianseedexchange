<?php
class Elementor_AdAce_Ad_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'adace_ad';
	}

	public function get_title() {
		return __( 'AdAce Ad', 'adace' );
	}

	public function get_icon() {
		return 'fa fa-ad';
	}

	public function get_categories() {
		return [ 'adace' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'general_section',
			[
				'label' => __( 'General', 'adace' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'id',
			[
				'label'     => __( 'Id', 'adace' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => '',
			]
		);

		$this->add_control(
			'align',
			[
				'label'     => __( 'Align', 'adace' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'none'      => _x( 'none',   'align', 'adace' ),
					'center'    => _x( 'center', 'align', 'adace' ),
					'left'      => _x( 'left',   'align', 'adace' ),
					'right'     => _x( 'right',  'align', 'adace' ),
				),
				'default'   => 'center',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$atts = array(
			'id',
			'align',
		);

		// Build shortcode.
		$shortcode = '';
		$shortcode .= '[adace-ad';
		foreach( $atts as $attr ) {
			if ( isset( $settings[ $attr ] ) && strlen( $settings[ $attr ] ) ) {
				$shortcode .= ' ' . $attr . '="' . $settings[ $attr ] . '"';
			}
		}
		$shortcode .= '][/adace-ad]';

		echo do_shortcode( $shortcode );
	}
}