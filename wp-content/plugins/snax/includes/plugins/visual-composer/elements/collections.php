<?php
/**
 * Snax Collections VC Element
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Snax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$snax_vc_collections_params = apply_filters( 'snax_vc_collections_params', array(

	/**
	 * GENERAL
	 */

//	// Template.
//	10 => array(
//		'type' 			=> 'image_radio',
//		'heading' 		=> __( 'Template', 'bimber' ),
//		'param_name' 	=> 'template',
//		'value'         => bimber_get_collection_templates(),
//		'std' 			=> 'grid-standard',
//		'description' 	=> __( 'Select display style for items.', 'bimber' ),
//	),
//	// Columns.
//	20 => array(
//		'type' 			=> 'dropdown',
//		'heading' 		=> __( 'Columns', 'bimber' ),
//		'param_name' 	=> 'columns',
//		'value' 		=> array(
//			'1' => 1,
//			'2' => 2,
//			'3' => 3,
//			'4' => 4,
//		),
//		'std' => 3,
//		'description' 	=> __( 'Number of columns to use for grid template.', 'bimber' ),
//	),

	/**
	 * TITLE
	 */

	// Title.
	30 => array(
		'group' 		=> __( 'Title', 'snax' ),
		'type' 			=> 'textfield',
		'holder' 		=> 'div',
		'class' 		=> '',
		'heading' 		=> __( 'Title', 'snax' ),
		'description' 	=> __( 'Leave empty to use the default value.', 'snax' ),
		'param_name'	=> 'title',
		'value' 		=> '',
	),
//	40 => array(
//		'group' 		=> __( 'Title', 'bimber' ),
//		'type' 			=> 'dropdown',
//		'heading' 		=> __( 'Size', 'bimber' ),
//		'param_name'	=> 'title_size',
//		'value' 		=> array(
//			__( 'H1 Heading', 'bimber' ) => 'h1',
//			__( 'H2 Heading', 'bimber' ) => 'h2',
//			__( 'H3 Heading', 'bimber' ) => 'h3',
//			__( 'H4 Heading', 'bimber' ) => 'h4',
//			__( 'H5 Heading', 'bimber' ) => 'h5',
//			__( 'H6 Heading', 'bimber' ) => 'h6',
//			__( 'Giga', 'bimber' ) 		=> 'giga',
//			__( 'Mega', 'bimber' ) 		=> 'mega',
//		),
//		'std' 		=> 'h4',
//	),
//	50 => array(
//		'group' 		=> __( 'Title', 'bimber' ),
//		'type' 			=> 'dropdown',
//		'heading' 		=> __( 'Align', 'bimber' ),
//		'param_name'	=> 'title_align',
//		'value' 		=> array(
//			__( 'Default', 'bimber' ) => '',
//			__( 'Center', 'bimber' ) => 'center',
//		),
//		'std' 		=> '',
//	),

	/**
	 * DATA
	 */
	// Orderby.
//	100 => array(
//		'group' 		=> __( 'Data', 'bimber' ),
//		'type' 			=> 'dropdown',
//		'holder' 		=> 'div',
//		'class' 		=> '',
//		'heading' 		=> __( 'Order by', 'bimber' ),
//		'param_name'	=> 'orderby',
//		'value' 		=> array(
//			__( 'Name', 'bimber' )  => 'name',
//			__( 'Count', 'bimber' ) => 'count',
//		),
//		'std' 		=> 'name',
//	),

	// Total items.
	110 => array(
		'group' 		=> __( 'Data', 'snax' ),
		'type' 			=> 'textfield',
		'holder' 		=> 'div',
		'class' 		=> '',
		'heading' 		=> __( 'Maximum items', 'snax' ),
		'param_name'	=> 'max',
		'value' 		=> 6,
		'description' 	=> __( 'Set max limit for items or enter -1 to display all.', 'snax' ),
	),

	// IDs.
	120 => array(
		'group' 		=> __( 'Data', 'snax' ),
		'type' 			=> 'textfield',
		'holder' 		=> 'div',
		'class' 		=> '',
		'heading' 		=> __( 'Collection IDs', 'snax' ),
		'param_name'	=> 'ids',
		'value' 		=> '',
		'description' 	=> __( 'Comma-separated list of collection ids.', 'snax' ),
	),


	/**
	 * ITEM DESIGN
	 */
	// Icon.
//	300 => array(
//		'group' 		=> __( 'Item Design', 'bimber' ),
//		'type' 			=> 'dropdown',
//		'heading' 		=> __( 'Icon', 'bimber' ),
//		'param_name' 	=> 'show_icon',
//		'value' 		=> array(
//			__( 'Show', 'bimber' )                  => 'standard',
//			__( 'Hide', 'bimber' )                  => 'none',
//		),
//		'std' 			=> 'standard',
//	),
//	// Date.
//	310 => array(
//		'group' 		=> __( 'Item Design', 'bimber' ),
//		'type' 			=> 'dropdown',
//		'heading' 		=> __( 'Count', 'bimber' ),
//		'param_name' 	=> 'show_count',
//		'value' 		=> array(
//			__( 'Show', 'bimber' )                  => 'standard',
//			__( 'Hide', 'bimber' )                  => 'none',
//		),
//		'std' 			=> 'standard',
//	),
) );

// Sort params by key.
ksort( $snax_vc_collections_params );

if ( function_exists( 'vc_map' ) ) {
	vc_map( array(
		'name' 		=> __( 'Snax Collections', 'snax' ),
		'base'	 	=> 'snax_collections',
		'category'  => __( 'Snax', 'snax' ),
		'params' 	=> $snax_vc_collections_params,
	) );
}
