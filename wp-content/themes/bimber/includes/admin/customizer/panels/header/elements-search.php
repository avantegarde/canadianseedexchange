<?php
/**
 * WP Customizer panel section to handle header design options
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Bimber_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$bimber_option_name = bimber_get_theme_id();

$wp_customize->add_section( 'bimber_header_builder_section_elements_search', array(
	'title'    => sprintf(  __( 'Element: %s', 'bimber' ), __( 'Search', 'bimber' ) ),
	'panel'    => 'bimber_header_panel',
) );

$wp_customize->add_setting( $bimber_option_name . '[header_builder_element_size_search_dropdown]', array(
	'default'               => $bimber_customizer_defaults['header_builder_element_size_search_dropdown'],
	'type'                  => 'option',
	'capability'            => 'edit_theme_options',
	'sanitize_callback'     => 'sanitize_text_field',
	'transport'  			=> 'postMessage',
) );
$wp_customize->add_control( new Bimber_Customize_Custom_Radio_Control( $wp_customize, 'bimber_header_builder_element_size_search_dropdown', array(
	'label'    => __( 'Dropdown Size', 'bimber' ),
	'section'  => 'bimber_header_builder_section_elements_search',
	'settings' => $bimber_option_name . '[header_builder_element_size_search_dropdown]',
	'type'     => 'radio',
	'input_attrs' => array(
		'row-class' => 'radio-single-line',
	),
	'choices'  => array(
		'g1-drop-s'      	=> '16px',
		'g1-drop-m'      	=> '24px',
		'g1-drop-l'      	=> '32px',
	),
) ) );

$wp_customize->add_setting( $bimber_option_name . '[header_builder_element_type_search_dropdown]', array(
	'default'               => $bimber_customizer_defaults['header_builder_element_type_search_dropdown'],
	'type'                  => 'option',
	'capability'            => 'edit_theme_options',
	'sanitize_callback'     => 'sanitize_text_field',
	'transport'  			=> 'postMessage',
) );
$wp_customize->add_control( new Bimber_Customize_Custom_Radio_Control( $wp_customize, 'bimber_header_builder_element_type_search_dropdown', array(
	'label'    => __( 'Dropdown Type', 'bimber' ),
	'section'  => 'bimber_header_builder_section_elements_search',
	'settings' => $bimber_option_name . '[header_builder_element_type_search_dropdown]',
	'type'     => 'radio',
	'input_attrs' => array(
		'row-class' => 'radio-single-line',
	),
	'choices'  => array(
		'g1-drop-icon'      	=> __( 'Icon', 'bimber' ),
		'g1-drop-text'      	=> __( 'Text', 'bimber' ),
	),
) ) );

$wp_customize->add_setting( $bimber_option_name . '[header_builder_element_size_search]', array(
	'default'               => $bimber_customizer_defaults['header_builder_element_size_search'],
	'type'                  => 'option',
	'capability'            => 'edit_theme_options',
	'sanitize_callback'     => 'sanitize_text_field',
	'transport'  			=> 'postMessage',
) );
$wp_customize->add_control( new Bimber_Customize_Custom_Radio_Control( $wp_customize, 'bimber_header_builder_element_size_search', array(
	'label'    => __( 'Form Size', 'bimber' ),
	'section'  => 'bimber_header_builder_section_elements_search',
	'settings' => $bimber_option_name . '[header_builder_element_size_search]',
	'type'     => 'radio',
	'input_attrs' => array(
		'row-class' => 'radio-single-line',
	),
	'choices'  => array(
		'g1-form-s'      	=> __( 'small', 'bimber' ),
		'standard'      => __( 'standard', 'bimber' ),
	),
) ) );

