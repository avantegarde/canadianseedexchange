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
add_filter( 'snax_admin_get_settings_sections', 'snax_admin_settings_sections_audios' );
add_filter( 'snax_admin_get_settings_fields',   'snax_admin_settings_fields_audios' );

/**
 * Register section
 *
 * @param array $sections       Sections.
 *
 * @return array
 */
function snax_admin_settings_sections_audios( $sections ) {
	$sections['snax_settings_audios'] = array(
		'title'    => __( 'Audios', 'snax' ),
		'callback' => 'snax_admin_settings_audios_section_description',
		'page'      => 'snax-audios-settings',
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
function snax_admin_settings_fields_audios( $fields ) {
	$fields['snax_settings_audios'] = array(

		/* Frontend Form */

		'snax_audio_frontend_form_header' => array(
			'title'             => '<h2>' . __( 'Frontend Form', 'snax' ) . '</h2>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'intval',
			'args'              => array(),
		),
		'snax_audio_featured_media_field' => array(
			'title'             => __( 'Featured Image', 'snax' ),
			'callback'          => 'snax_admin_setting_audio_featured_media_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_audio_category_field' => array(
			'title'             => __( 'Category', 'snax' ),
			'callback'          => 'snax_admin_setting_audio_category_field',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_audio_category_multi' => array(
			'title'             => __( 'Multiple categories selection?', 'snax' ),
			'callback'          => 'snax_admin_setting_audio_category_multi',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_audio_category_whitelist' => array(
			'title'             => __( 'Category whitelist', 'snax' ),
			'callback'          => 'snax_admin_setting_audio_category_whitelist',
			'sanitize_callback' => 'snax_sanitize_category_whitelist',
			'args'              => array(),
		),
		'snax_audio_category_auto_assign' => array(
			'title'             => __( 'Auto assign to categories', 'snax' ),
			'callback'          => 'snax_admin_setting_audio_category_auto_assign',
			'sanitize_callback' => 'snax_sanitize_category_whitelist',
			'args'              => array(),
		),
		'snax_audio_allow_snax_authors_to_add_referrals' => array(
			'title'             => __( 'Referral link', 'snax' ),
			'callback'          => 'snax_admin_setting_audio_allow_snax_authors_to_add_referrals',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),

		/* Single Post */

		'snax_audio_single_post_header' => array(
			'title'             => '<h2>' . __( 'Single Post', 'snax' ) . '</h2>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'intval',
			'args'              => array(),
		),

		'snax_audio_show_featured_media' => array(
			'title'             => __( 'Show Featured Media', 'snax' ),
			'callback'          => 'snax_admin_setting_audio_show_featured_media',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),

		/* Limits */

		'snax_limits_audio_header' => array(
			'title'             => '<h2>' . __( 'Limits', 'snax' ) . '</h2>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_audio_upload_allowed' => array(
			'title'             => __( 'Allowed?', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_upload_allowed',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array( 'type' => 'audio' ),
		),
		'snax_audio_max_upload_size' => array(
			'title'             => __( 'Maximum file size', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_upload_max_size',
			'sanitize_callback' => 'intval',
			'args'              => array( 'type' => 'audio' ),
		),
		'snax_audio_allowed_types' => array(
			'title'             => __( 'Allowed types', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_upload_allowed_types',
			'sanitize_callback' => 'snax_sanitize_audio_array',
			'args'              => array( 'type' => 'audio' ),
		),

		/* Texts */

		'snax_audio_texts_header' => array(
			'title'             => '<h2>' . _x( 'Texts', 'Setting label', 'snax' ) . '</h2>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_audio_singular_name' => array(
			'title'             => _x( 'Singular name', 'Setting label', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_audio_singular_name',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_audio_add_new' => array(
			'title'             => _x( 'Add new', 'Setting label', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_audio_add_new',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_audio_description' => array(
			'title'             => _x( 'Description', 'Setting label', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_audio_description',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_audio_overview' => array(
			'title'             => _x( 'Overview', 'Setting label', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_audio_overview',
			'sanitize_callback' => 'wp_kses_post',
			'args'              => array(),
		),
	);

	return $fields;
}

function snax_admin_audios_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ) ?></h1>
		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( __( 'Formats', 'snax' ) ); ?></h2>
		<?php snax_admin_settings_subtabs( 'formats', __( 'Audios', 'snax' ) ); ?>

		<form action="options.php" method="post">

			<?php settings_fields( 'snax-audios-settings' ); ?>
			<?php do_settings_sections( 'snax-audios-settings' ); ?>

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
function snax_admin_settings_audios_section_description() {}

/**
 * Featured media field
 */
function snax_admin_setting_audio_featured_media_field() {
	$field = snax_audio_featured_media_field();
	?>

	<select name="snax_audio_featured_media_field" id="snax_audio_featured_media_field">
		<option value="disabled" <?php selected( $field, 'disabled' ) ?>><?php esc_html_e( 'disabled', 'snax' ) ?></option>
		<option value="required" <?php selected( $field, 'required' ) ?>><?php esc_html_e( 'required', 'snax' ) ?></option>
		<option value="optional" <?php selected( $field, 'optional' ) ?>><?php esc_html_e( 'optional', 'snax' ) ?></option>
	</select>
	<?php
}

/**
 * Featured media on single post
 */
function snax_admin_setting_audio_show_featured_media() {
	$checked = snax_audio_show_featured_media();
	?>
	<input name="snax_audio_show_featured_media" id="snax_audio_show_featured_media" value="standard" type="checkbox" <?php checked( $checked ); ?> />
	<?php
}

/**
 * Category field
 */
function snax_admin_setting_audio_category_field() {
	$field = snax_audio_category_field();
	?>

	<select name="snax_audio_category_field" id="snax_audio_category_field">
		<option value="disabled" <?php selected( $field, 'disabled' ) ?>><?php esc_html_e( 'disabled', 'snax' ) ?></option>
		<option value="required" <?php selected( $field, 'required' ) ?>><?php esc_html_e( 'required', 'snax' ) ?></option>
		<option value="optional" <?php selected( $field, 'optional' ) ?>><?php esc_html_e( 'optional', 'snax' ) ?></option>
	</select>
	<?php
}

/**
 * Multiple categories selection.
 */
function snax_admin_setting_audio_category_multi() {
	$checked = snax_audio_multiple_categories_selection();
	?>
	<input name="snax_audio_category_multi" id="snax_audio_category_multi" value="standard" type="checkbox" <?php checked( $checked ); ?> />
	<?php
}

/**
 * Category white-list
 */
function snax_admin_setting_audio_category_whitelist() {
	$whitelist      = snax_audio_get_category_whitelist();
	$all_categories = get_categories( 'hide_empty=0' );
	?>
	<select size="10" name="snax_audio_category_whitelist[]" id="snax_audio_category_whitelist" multiple="multiple">
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
function snax_admin_setting_audio_category_auto_assign() {
	$auto_assign_list = snax_audio_get_category_auto_assign();
	$all_categories = get_categories( 'hide_empty=0' );
	?>
	<select size="10" name="snax_audio_category_auto_assign[]" id="snax_audio_category_auto_assign" multiple="multiple">
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

/**
 * Whether to allow the Snax Author to add referral links to posts and items
 */
function snax_admin_setting_audio_allow_snax_authors_to_add_referrals() {
	$allow = snax_audio_allow_snax_authors_to_add_referrals();
	?>

	<select name="snax_audio_allow_snax_authors_to_add_referrals" id="snax_audio_allow_snax_authors_to_add_referrals">
		<option value="standard" <?php selected( $allow, true ) ?>><?php esc_html_e( 'show', 'snax' ) ?></option>
		<option value="none" <?php selected( $allow, false ) ?>><?php esc_html_e( 'hide', 'snax' ) ?></option>
	</select>
	<p class="description"><?php esc_html_e( 'Applies only to Snax Authors', 'snax' ); ?></p>
	<?php
}

/*
 * Texts > Singular Name
 */
function snax_admin_setting_callback_audio_singular_name() {
	?>
	<input name="snax_audio_singular_name" id="snax_audio_singular_name" class="regular-text" type="text" value="<?php echo esc_attr( snax_audio_get_singular_name() ); ?>" placeholder="<?php echo esc_attr_x( 'e.g. Audio', 'Setting placeholder', 'snax' ); ?>" />
	<p class="description">
		<?php echo esc_html_x( 'Leave empty to use default.', 'Setting description', 'snax' ); ?>
	</p>
	<?php
}

/*
 * Texts > Add new
 */
function snax_admin_setting_callback_audio_add_new() {
	?>
	<input name="snax_audio_add_new" id="snax_audio_add_new" class="regular-text" type="text" value="<?php echo esc_attr( snax_audio_get_add_new() ); ?>" placeholder="<?php echo esc_attr_x( 'e.g. Upload Your Audio', 'Setting placeholder', 'snax' ); ?>" />
	<p class="description">
		<?php echo esc_html_x( 'Leave empty to use default.', 'Setting description', 'snax' ); ?>
	</p>
	<?php
}

/*
 * Texts > Description
 */
function snax_admin_setting_callback_audio_description() {
	?>
	<input name="snax_audio_description" id="snax_audio_description" class="regular-text" type="text" value="<?php echo esc_attr( snax_audio_get_description() ); ?>" placeholder="<?php echo esc_attr_x( 'e.g. MP3 or SoundCloud embed, MixCloud, etc.', 'Setting placeholder', 'snax' ); ?>" />
	<p class="description">
		<?php echo esc_html_x( 'Leave empty to use default.', 'Setting description', 'snax' ); ?>
	</p>
	<?php
}

/*
 * Texts > Overview
 */
function snax_admin_setting_callback_audio_overview() {
	?>
	<textarea name="snax_audio_overview" id="snax_audio_overview" rows="5" class="large-text"><?php echo esc_attr( snax_audio_get_overview() ); ?></textarea>
	<?php
}
