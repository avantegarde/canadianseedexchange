<?php
/**
 * GIF Settings page
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'mace_settings_pages', 'mace_register_gif_settings_page', 10 );

function mace_get_gif_settings_page_id() {
	return apply_filters( 'mace_gif_settings_page_id', 'mace-gif-settings' );
}

function mace_get_gif_settings_page_config() {
	return apply_filters( 'mace_gif_settings_config', array(
		'tab_title'                 => __( 'GIF to MP4', 'mace' ),
		'page_title'                => __( 'GIF to MP4 Conversion', 'mace' ),
		'page_description_callback' => 'mace_gif_settings_page_description',
		'page_callback'             => 'mace_gif_settings_page',
		'fields' => array(
			'mace_gif_cc_key' => array(
				'title'             => __( 'Cloud Convert API Key', 'mace' ),
				'callback'          => 'mace_gif_setting_cc_key',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_gif_storage' => array(
				'title'             => __( 'Store MP4 files', 'mace' ),
				'callback'          => 'mace_gif_setting_storage',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_gif_s3_keyid' => array(
				'title'             => __( 'Amazon S3 Access Key ID', 'mace' ),
				'callback'          => 'mace_gif_setting_s3_keyid',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_gif_s3_key' => array(
				'title'             => __( 'Amazon S3 Access Key', 'mace' ),
				'callback'          => 'mace_gif_setting_s3_key',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_gif_s3_bucket' => array(
				'title'             => __( 'Amazon S3 Bucket', 'mace' ),
				'callback'          => 'mace_gif_setting_s3_bucket',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_gif_settings_page( $pages ) {
	$pages[ mace_get_gif_settings_page_id() ] = mace_get_gif_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_gif_settings_page_description() {
	?>
	<p><?php printf( esc_html__( 'The plugin uses the %s service for GIF to MP4 on the fly conversion. CloudConvert can be used absolutely free! Nevertheless, if your usage exceeds free plan, you will need to choose one of prepaid packages and subscriptions.', 'mace' ), '<a href="https://cloudconvert.com/" target="_blank">CloudConvert</a>' ); ?></p>
	<?php
}

/**
 * Settings page
 */
function mace_gif_settings_page() {
	$page_id        = mace_get_gif_settings_page_id();
	$page_config    = mace_get_gif_settings_page_config();
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'MediaAce Settings', 'mace' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php mace_admin_settings_tabs( $page_config['tab_title'] ); ?></h2>
		<form action="options.php" method="post">

			<?php settings_fields( $page_id ); ?>
			<?php do_settings_sections( $page_id ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'mace' ); ?>" />
			</p>

		</form>
	</div>

	<?php
}

/**
 * CloudConvert API key
 */
function mace_gif_setting_cc_key() {
	?>
	<input name="mace_gif_cc_key" id="mace_gif_cc_key" class="widefat code" type="text" value="<?php echo esc_attr( mace_get_gif_cc_key() ); ?>" />

	<p class="description">
		<?php esc_html_e( 'Your API key can be found in the Dashboard section of your CloudConvert account.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Storage
 */
function mace_gif_setting_storage() {
	$storage = mace_get_gif_storage();
	?>
	<select name="mace_gif_storage" id="mace_gif_storage">
		<option value="local" <?php selected( 'local', $storage ) ?>><?php esc_html_e( 'on disk', 'mace' ) ?></option>
		<option value="s3" <?php selected( 's3' , $storage ) ?>><?php esc_html_e( 'on Amazon S3', 'mace' ) ?></option>
	</select>
	<?php
}

/**
 * Amazon S3 API key
 */
function mace_gif_setting_s3_key() {
	?>
	<input name="mace_gif_s3_key" id="mace_gif_s3_key" class="regular-text code mace-s3-storage-config" type="text" value="<?php echo esc_attr( mace_get_gif_s3_key() ); ?>" />
	<?php
}

/**
 * Amazon S3 Key ID
 */
function mace_gif_setting_s3_keyid() {
	?>
	<input name="mace_gif_s3_keyid" id="mace_gif_s3_keyid" class="regular-text code mace-s3-storage-config" type="text" value="<?php echo esc_attr( mace_get_gif_s3_keyid() ); ?>" />
	<?php
}

/**
 * Amazon S3 Bucket
 */
function mace_gif_setting_s3_bucket() {
	?>
	<input name="mace_gif_s3_bucket" id="mace_gif_s3_bucket" class="regular-text code mace-s3-storage-config" type="text" value="<?php echo esc_attr( mace_get_gif_s3_bucket() ); ?>" />
	<?php
}


