<?php
/**
 * Admin Hooks
 *
 * @package whats-your-reaction
 * @subpackage Hooks
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// Edit.
add_action( 'reaction_add_form_fields', 'wyr_taxonomy_add_form_fields' );
add_action( 'reaction_edit_form_fields', 'wyr_taxonomy_edit_form_fields' );

// Save.
add_action( 'create_reaction', 'wyr_taxonomy_save_custom_form_fields', 10, 2 );
add_action( 'edited_reaction', 'wyr_taxonomy_save_custom_form_fields', 10, 2 );

// List view.
add_filter( 'manage_edit-reaction_columns', 'wyr_taxonomy_add_columns' );
add_filter( 'manage_reaction_custom_column', 'wyr_taxonomy_display_custom_columns_content', 10, 3 );
add_action( 'parse_term_query', 'wyr_taxonomy_change_term_list_order', 10, 1 );

// Assets.
add_action( 'admin_enqueue_scripts', 'wyr_admin_enqueue_styles' );
add_action( 'admin_enqueue_scripts', 'wyr_admin_enqueue_scripts' );

// Metaboxes.
add_action( 'add_meta_boxes',       'wyr_add_fake_reactions_metabox', 10 ,2 );
add_action( 'save_post',            'wyr_save_fake_reactions_metabox' );

// Ajax.
add_action( 'wp_ajax_wyr_save_custom_icon',        'wyr_ajax_save_custom_icon' );
