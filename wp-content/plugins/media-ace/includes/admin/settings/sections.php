<?php
/**
 * Settings Sections
 *
 * @package media-ace
 * @subpackage Settings
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


/**
 * Get the main settings sections.
 *
 * @return array
 */
function mace_admin_get_settings_sections() {
	$sections = array();

	$settings_pages = mace_get_settings_pages();

	foreach( $settings_pages as $page_id => $page_config ) {
		$sections[ $page_id ] = array(
			'title'    => $page_config['page_title'],
			'callback' => $page_config['page_description_callback'],
			'page'     => $page_id,
		);
	}

	return (array) apply_filters( 'mace_admin_get_settings_sections', $sections );
}

/**
 * Get all of the settings fields.
 *
 * @return array
 */
function mace_admin_get_settings_fields() {
	$fields = array();
	$settings_pages = mace_get_settings_pages();

	foreach( $settings_pages as $page_id => $page_config ) {
		$fields[ $page_id ] = $page_config['fields'];
	}

	return (array) apply_filters( 'mace_admin_get_settings_fields', $fields );
}


/**
 * Get settings fields by section.
 *
 * @param string $section_id    Section id.
 *
 * @return mixed                False if section is invalid, array of fields otherwise.
 */
function mace_admin_get_settings_fields_for_section( $section_id = '' ) {

	// Bail if section is empty.
	if ( empty( $section_id ) ) {
		return false;
	}

	$fields = mace_admin_get_settings_fields();
	$retval = isset( $fields[ $section_id ] ) ? $fields[ $section_id ] : false;

	return (array) apply_filters( 'mace_admin_get_settings_fields_for_section', $retval, $section_id );
}
