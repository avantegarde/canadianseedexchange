<?php
/**
 * Snax Settings Section
 *
 * @package snax
 * @subpackage Settings
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// Register section and fields.
add_filter( 'snax_admin_get_settings_sections', 'snax_admin_settings_sections_extproduct' );
add_filter( 'snax_admin_get_settings_fields',   'snax_admin_settings_fields_extproduct' );
add_filter( 'snax_settings_pages',              'snax_register_extproduct_settings_page', 10, 2 );
add_action( 'admin_head',                       'snax_remove_extproduct_settings_page_from_menu' );
add_filter( 'snax_settings_menu_highlight',     'snax_extproduct_settings_menu_highlight' );

/**
 * Register the format settings page
 *
 * @param array $pages      Registered pages.
 *
 * @return array
 */
function snax_register_extproduct_settings_page( $pages, $capability ) {
	$pages[] = add_options_page(
		__( 'Snax External Product', 'snax' ),
		__( 'Snax External Product', 'snax' ),
		$capability,
		'snax-extproduct-settings',
		'snax_admin_extproduct_settings'
	);

	return $pages;
}

/**
 * Remove the format page extproduct from Setting menu
 */
function snax_remove_extproduct_settings_page_from_menu() {
	remove_submenu_page( snax_admin()->settings_page, 'snax-extproduct-settings' );
}

/**
 * Highlight the Snax main menu when the format settings is selected
 *
 * @param array $slugs  Page slugs.
 *
 * @return array
 */
function snax_extproduct_settings_menu_highlight( $slugs ) {
	$slugs[] = 'snax-extproduct-settings';

	return $slugs;
}

/**
 * Register section
 *
 * @param array $sections       Sections.
 *
 * @return array
 */
function snax_admin_settings_sections_extproduct( $sections ) {
	$sections['snax_settings_extproduct'] = array(
		'title'    => __( 'External Product', 'snax' ),
		'callback' => 'snax_admin_settings_extproduct_section_description',
		'page'      => 'snax-extproduct-settings',
	);

	return $sections;
}

/**
 * Register section fields
 *
 * @param array $fields     Fields.
 *
 * @return array
 */
function snax_admin_settings_fields_extproduct( $fields ) {
	$fields['snax_settings_extproduct'] = array(

		/* Frontend Form */

		'snax_extproduct_frontend_form_header' => array(
			'title'             => '<h2>' . __( 'Frontend Form', 'snax' ) . '</h2>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'intval',
			'args'              => array(),
		),
		'snax_extproduct_featured_media_field' => array(
			'title'             => __( 'Featured Image', 'snax' ),
			'callback'          => 'snax_admin_setting_extproduct_featured_media_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_extproduct_category_field' => array(
			'title'             => __( 'Category', 'snax' ),
			'callback'          => 'snax_admin_setting_extproduct_category_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_extproduct_category_multi' => array(
			'title'             => __( 'Multiple categories selection?', 'snax' ),
			'callback'          => 'snax_admin_setting_extproduct_category_multi',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_extproduct_category_whitelist' => array(
			'title'             => __( 'Category whitelist', 'snax' ),
			'callback'          => 'snax_admin_setting_extproduct_category_whitelist',
			'sanitize_callback' => 'snax_sanitize_category_whitelist',
			'args'              => array(),
		),
		'snax_extproduct_category_auto_assign' => array(
			'title'             => __( 'Auto assign to categories', 'snax' ),
			'callback'          => 'snax_admin_setting_extproduct_category_auto_assign',
			'sanitize_callback' => 'snax_sanitize_category_whitelist',
			'args'              => array(),
		),

		/* Single Post */

		'snax_extproduct_single_post_header' => array(
			'title'             => '<h2>' . __( 'Single Post', 'snax' ) . '</h2>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'intval',
			'args'              => array(),
		),

		'snax_extproduct_show_featured_media' => array(
			'title'             => __( 'Show Featured Media', 'snax' ),
			'callback'          => 'snax_admin_setting_extproduct_show_featured_media',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),

		/* Texts */

		'snax_extproduct_texts_header' => array(
			'title'             => '<h2>' . _x( 'Texts', 'Setting label', 'snax' ) . '</h2>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_extproduct_singular_name' => array(
			'title'             => _x( 'Singular name', 'Setting label', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_extproduct_singular_name',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_extproduct_add_new' => array(
			'title'             => _x( 'Add new', 'Setting label', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_extproduct_add_new',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_extproduct_description' => array(
			'title'             => _x( 'Description', 'Setting label', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_extproduct_description',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_extproduct_overview' => array(
			'title'             => _x( 'Overview', 'Setting label', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_extproduct_overview',
			'sanitize_callback' => 'wp_kses_post',
			'args'              => array(),
		),
	);

	if ( defined( 'BTP_DEV' ) && BTP_DEV ) {

		/* Demos */

		$fields['snax_settings_extproduct']['snax_demo_header'] = array(
			'title'             => '<h2>' . __( 'Demo', 'snax' ) . '</h2>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'intval',
			'args'              => array(),
		);

		$fields['snax_settings_extproduct']['snax_demo_extproduct_post_ids'] = array(
			'title'             => __( 'Example Products', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_demo_posts',
			'sanitize_callback' => 'snax_sanitize_text_array',
			'args'              => array(
				'post_type' => 'product',
				'format'    => 'extproduct',

			),
		);
	}

	return $fields;
}

function snax_admin_extproduct_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( __( 'Formats', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'formats', __( 'External Product', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-extproduct-settings' ); ?>
			<?php do_settings_sections( 'snax-extproduct-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * Render section description
 */
function snax_admin_settings_extproduct_section_description() {}

/**
 * Featured media field
 */
function snax_admin_setting_extproduct_featured_media_field() {
	$field = snax_extproduct_featured_media_field();
	?>

	<select name="snax_extproduct_featured_media_field" id="snax_extproduct_featured_media_field">
		<option value="disabled" <?php selected( $field, 'disabled' ) ?>><?php esc_html_e( 'disabled', 'snax' ) ?></option>
		<option value="required" <?php selected( $field, 'required' ) ?>><?php esc_html_e( 'required', 'snax' ) ?></option>
		<option value="optional" <?php selected( $field, 'optional' ) ?>><?php esc_html_e( 'optional', 'snax' ) ?></option>
	</select>
	<?php
}

/**
 * Featured media on single post
 */
function snax_admin_setting_extproduct_show_featured_media() {
	$checked = snax_extproduct_show_featured_media();
	?>
	<input name="snax_extproduct_show_featured_media" id="snax_extproduct_show_featured_media" value="standard" type="checkbox" <?php checked( $checked ); ?> />
	<?php
}

/**
 * Category field
 */
function snax_admin_setting_extproduct_category_field() {
	$field = snax_extproduct_category_field();
	?>

	<select name="snax_extproduct_category_field" id="snax_extproduct_category_field">
		<option value="disabled" <?php selected( $field, 'disabled' ) ?>><?php esc_html_e( 'disabled', 'snax' ) ?></option>
		<option value="required" <?php selected( $field, 'required' ) ?>><?php esc_html_e( 'required', 'snax' ) ?></option>
		<option value="optional" <?php selected( $field, 'optional' ) ?>><?php esc_html_e( 'optional', 'snax' ) ?></option>
	</select>
	<?php
}

/**
 * Multiple categories selection.
 */
function snax_admin_setting_extproduct_category_multi() {
	$checked = snax_extproduct_multiple_categories_selection();
	?>
	<input name="snax_extproduct_category_multi" id="snax_extproduct_category_multi" value="standard" type="checkbox" <?php checked( $checked ); ?> />
	<?php
}

/**
 * Category white-list
 */
function snax_admin_setting_extproduct_category_whitelist() {
	$whitelist      = snax_extproduct_get_category_whitelist();
	$all_categories = get_categories( array(
		'hide_empty' => false,
		'taxonomy' => 'product_cat',
	) );
	?>
	<select size="10" name="snax_extproduct_category_whitelist[]" id="snax_extproduct_category_whitelist" multiple="multiple">
		<option value="" <?php selected( in_array( '', $whitelist, true ) ); ?>><?php esc_html_e( '- Allow all -', 'snax' ) ?></option>
		<?php foreach ( $all_categories as $category_obj ) : ?>
			<?php
			// Exclude the Uncategorized option.
			if ( 'uncategorized' === $category_obj->slug ) {
				continue;
			}
			?>

			<option value="<?php echo esc_attr( $category_obj->slug ); ?>" <?php selected( in_array( $category_obj->slug, $whitelist, true ) ); ?>><?php echo esc_html( $category_obj->name ) ?></option>
		<?php endforeach; ?>
	</select>
	<p class="description"><?php esc_html_e( 'Categories allowed for user while creating a new post.', 'snax' ); ?></p>
	<?php
}

/**
 * Auto assign to category.
 */
function snax_admin_setting_extproduct_category_auto_assign() {
	$auto_assign_list = snax_extproduct_get_category_auto_assign();
	$all_categories = get_categories( array(
		'hide_empty' => false,
		'taxonomy' => 'product_cat',
	) );
	?>
	<select size="10" name="snax_extproduct_category_auto_assign[]" id="snax_extproduct_category_auto_assign" multiple="multiple">
		<option value="" <?php selected( in_array( '', $auto_assign_list, true ) ); ?>><?php esc_html_e( '- Not assign -', 'snax' ) ?></option>
		<?php foreach ( $all_categories as $category_obj ) : ?>
			<?php
			// Exclude the Uncategorized option.
			if ( 'uncategorized' === $category_obj->slug ) {
				continue;
			}
			?>

			<option value="<?php echo esc_attr( $category_obj->slug ); ?>" <?php selected( in_array( $category_obj->slug, $auto_assign_list, true ) ); ?>><?php echo esc_html( $category_obj->name ) ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}

/*
 * Texts > Singular Name
 */
function snax_admin_setting_callback_extproduct_singular_name() {
	?>
	<input name="snax_extproduct_singular_name" id="snax_extproduct_singular_name" class="regular-text" type="text" value="<?php echo esc_attr( snax_extproduct_get_singular_name() ); ?>" placeholder="<?php echo esc_attr_x( 'e.g. External Product', 'Setting placeholder', 'snax' ); ?>" />
	<p class="description">
		<?php echo esc_html_x( 'Leave empty to use default.', 'Setting description', 'snax' ); ?>
	</p>
	<?php
}

/*
 * Texts > Add new
 */
function snax_admin_setting_callback_extproduct_add_new() {
	?>
	<input name="snax_extproduct_add_new" id="snax_extproduct_add_new" class="regular-text" type="text" value="<?php echo esc_attr( snax_extproduct_get_add_new() ); ?>" placeholder="<?php echo esc_attr_x( 'e.g. Submit Your External Product', 'Setting placeholder', 'snax' ); ?>" />
	<p class="description">
		<?php echo esc_html_x( 'Leave empty to use default.', 'Setting description', 'snax' ); ?>
	</p>
	<?php
}

/*
 * Texts > Description
 */
function snax_admin_setting_callback_extproduct_description() {
	?>
	<input name="snax_extproduct_description" id="snax_extproduct_description" class="regular-text" type="text" value="<?php echo esc_attr( snax_extproduct_get_description() ); ?>" placeholder="<?php echo esc_attr_x( 'e.g. Embed 3rd party product', 'Setting placeholder', 'snax' ); ?>" />
	<p class="description">
		<?php echo esc_html_x( 'Leave empty to use default.', 'Setting description', 'snax' ); ?>
	</p>
	<?php
}

/*
 * Texts > Overview
 */
function snax_admin_setting_callback_extproduct_overview() {
	?>
	<textarea name="snax_extproduct_overview" id="snax_extproduct_overview" rows="5" class="large-text" placeholder="<?php echo esc_attr_x( 'e.g. Just paste external product\'s URL, we will do the rest.', 'Setting placeholder', 'snax' ); ?>"><?php echo esc_attr( snax_extproduct_get_overview() ); ?></textarea>
	<?php
}
