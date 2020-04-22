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
add_filter( 'snax_admin_get_settings_sections', 'snax_admin_settings_sections_moderation' );
add_filter( 'snax_admin_get_settings_fields',   'snax_admin_settings_fields_moderation' );

/**
 * Register section
 *
 * @param array $sections       Sections.
 *
 * @return array
 */
function snax_admin_settings_sections_moderation( $sections ) {
	$sections['snax_settings_moderation'] = array(
		'title'    => '',
		'callback' => 'snax_admin_settings_moderation_section_description',
		'page'      => 'snax-moderation-settings',
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
function snax_admin_settings_fields_moderation( $fields ) {
	$fields['snax_settings_moderation'] = array(
		'snax_moderation_header' => array(
			'title'             => '<h3>' . __( 'Users\' content', 'snax' ) . '</h3>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'intval',
			'args'              => array(),
		),
		'snax_skip_verification' => array(
			'title'             => __( 'Moderate new posts?', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_skip_verification',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_user_can_edit_posts' => array(
			'title'             => __( 'Can users edit own posts?', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_user_can_edit_posts',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_user_can_delete_posts' => array(
			'title'             => __( 'Can users delete own posts?', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_user_can_delete_posts',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_keep_edited_post_status' => array(
			'title'             => __( 'Keep post status?', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_keep_edited_post_status',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_waiting_room' => array(
			'title'             => __( 'Waiting Room', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_waiting_room',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_reporting_header' => array(
			'title'             => '<h3>' . __( 'Reporting', 'snax' ) . '</h3>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'intval',
			'args'              => array(),
		),
		'snax_report_post_abuse' => array(
			'title'             => __( 'Allow users to report a post abuse?', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_report_post_abuse',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
		'snax_report_mail' => array(
			'title'             => __( 'Report abuse to', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_report_mail',
			'sanitize_callback' => 'sanitize_email',
			'args'              => array(),
		),
		'snax_notifications_header' => array(
			'title'             => '<h3>' . __( 'Notifications', 'snax' ) . '</h3>',
			'callback'          => '__return_empty_string',
			'sanitize_callback' => 'intval',
			'args'              => array(),
		),
		'snax_mail_notifications' => array(
			'title'             => __( 'Send mail to admin when new post/item was added?', 'snax' ),
			'callback'          => 'snax_admin_setting_callback_mail_notifications',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array(),
		),
	);

	return $fields;
}

function snax_admin_moderation_settings() {
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Snax Settings', 'snax' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php snax_admin_settings_tabs( __( 'Moderation', 'snax' ) ); ?></h2>
		<form action="options.php" method="post">

			<?php settings_fields( 'snax-moderation-settings' ); ?>
			<?php do_settings_sections( 'snax-moderation-settings' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'snax' ); ?>" />
			</p>

		</form>
	</div>

	<?php
}


/**
 * Moderation section description
 */
function snax_admin_settings_moderation_section_description() {}

/**
 * Whether to allow user direct publishing
 */
function snax_admin_setting_callback_skip_verification() {
	// The label of the option was changed to Moderate new post? from Skip verification, so "yes" and "no" were inverted in labels here.
	$skip = snax_skip_verification();
	?>

	<select name="snax_skip_verification" id="snax_skip_verification">
		<option value="standard" <?php selected( $skip, true ) ?>><?php esc_html_e( 'no', 'snax' ) ?></option>
		<option value="none" <?php selected( $skip, false ) ?>><?php esc_html_e( 'yes', 'snax' ) ?></option>
	</select>
	<p class="description">
		<?php echo esc_html_x( 'New content has to be approved by a moderator before going live', 'Moderation settings', 'snax' ); ?>
	</p>
	<?php
}

/**
 * Whether to allow user edit own posts
 */
function snax_admin_setting_callback_user_can_edit_posts() {
	$can = snax_user_can_edit_posts();
	?>

	<select name="snax_user_can_edit_posts" id="snax_user_can_edit_posts">
		<option value="standard" <?php selected( $can, true ) ?>><?php esc_html_e( 'yes', 'snax' ) ?></option>
		<option value="none" <?php selected( $can, false ) ?>><?php esc_html_e( 'no', 'snax' ) ?></option>
	</select>
	<?php
}

/**
 * Whether to allow user delete own posts
 */
function snax_admin_setting_callback_user_can_delete_posts() {
	$can = snax_user_can_delete_posts();
	?>

	<select name="snax_user_can_delete_posts" id="snax_user_can_delete_posts">
		<option value="standard" <?php selected( $can, true ) ?>><?php esc_html_e( 'yes', 'snax' ) ?></option>
		<option value="none" <?php selected( $can, false ) ?>><?php esc_html_e( 'no', 'snax' ) ?></option>
	</select>
	<?php
}

/**
 * Whether to keep a post status after edition
 */
function snax_admin_setting_callback_keep_edited_post_status() {
	$keep = snax_keep_edited_post_status();
	?>

	<select name="snax_keep_edited_post_status" id="snax_keep_edited_post_status">
		<option value="standard" <?php selected( $keep, true ) ?>><?php esc_html_e( 'yes', 'snax' ) ?></option>
		<option value="none" <?php selected( $keep, false ) ?>><?php esc_html_e( 'no', 'snax' ) ?></option>
	</select>
	<p class="description">
		<?php echo esc_html_x( 'Leave a post status as it was before edition or require moderation after each change', 'Moderation settings', 'snax' ); ?>
	</p>
	<?php
}

/**
 * Whether the Waiting Room is enabled
 */
function snax_admin_setting_callback_waiting_room() {
	$enabled = snax_waiting_room_enabled();
	?>

	<select name="snax_waiting_room" id="snax_waiting_room">
		<option value="standard" <?php selected( $enabled, true ) ?>><?php esc_html_e( 'enabled', 'snax' ) ?></option>
		<option value="none" <?php selected( $enabled, false ) ?>><?php esc_html_e( 'disabled', 'snax' ) ?></option>
	</select>
	<p class="description">
		<?php echo esc_html_x( 'Allow users to browse pending posts', 'Moderation settings', 'snax' ); ?>
	</p>
	<?php
}

/**
 * Whether to send mail to admin when new post/item was added
 */
function snax_admin_setting_callback_mail_notifications() {
	$mail = snax_mail_notifications();
	?>

	<select name="snax_mail_notifications" id="snax_mail_notifications">
		<option value="standard" <?php selected( $mail, true ) ?>><?php esc_html_e( 'yes', 'snax' ) ?></option>
		<option value="none" <?php selected( $mail, false ) ?>><?php esc_html_e( 'no', 'snax' ) ?></option>
	</select>
	<?php
}

/**
 * Whether to allow users report post/items as abuse
 */
function snax_admin_setting_callback_report_post_abuse() {
	$val = snax_report_post_abuse();
	?>

	<select name="snax_report_post_abuse" id="snax_report_post_abuse">
		<option value="standard" <?php selected( $val, true ) ?>><?php esc_html_e( 'yes', 'snax' ) ?></option>
		<option value="none" <?php selected( $val, false ) ?>><?php esc_html_e( 'no', 'snax' ) ?></option>
	</select>
	<?php
}

/**
 * Report abube target e-mail
 */
function snax_admin_setting_callback_report_mail() {
	?>
	<input name="snax_report_mail" id="snax_report_mail" class="regular-text" type="text" placeholder="<?php echo esc_attr_x( 'user@domain.com', 'Moderation, e-mail example', 'snax' ); ?>" value="<?php echo esc_attr( snax_get_report_mail() ); ?>" />
	<p class="description">
		<?php echo wp_kses_post( sprintf( __( 'Leave empty to use site\'s admin e-mail (%s).', 'snax' ), get_option( 'admin_email' ) ) ); ?>
	</p>
	<?php
}
