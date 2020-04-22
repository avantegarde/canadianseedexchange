<?php
/**
 * Settings page
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'mace_settings_pages', 'mace_register_expiry_settings_page', 10 );

function mace_get_expiry_settings_page_id() {
	return apply_filters( 'mace_expiry_settings_page_id', 'mace-expiry-settings' );
}

function mace_get_expiry_settings_page_config() {
	return apply_filters( 'mace_expiry_settings_config', array(
		'tab_title'                 => __( 'Expire Headers', 'mace' ),
		'page_title'                => __( 'Configure Expire Headers', 'mace' ),
		'page_description_callback' => 'mace_expiry_settings_page_description',
		'page_callback'             => 'mace_expiry_settings_page',
		'fields'                    => array(
			'mace_expiry_headers' => array(
				'title'             => __( 'Enabled?', 'mace' ),
				'callback'          => 'mace_expiry_setting_headers_enabled',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_expiry_content' => array(
				'title'             => __( '.htaccess content', 'mace' ),
				'callback'          => 'mace_expiry_setting_content',
				'sanitize_callback' => 'sanitize_textarea_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_expiry_settings_page( $pages ) {
	$pages[ mace_get_expiry_settings_page_id() ] = mace_get_expiry_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_expiry_settings_page_description() {
	?>
	<p>
		<?php esc_html_e( 'Leverage browser cache to improve performance.', 'mace' ); ?>
		<?php
		if ( is_multisite() ) {?>
		<br><style>.wrap .form-table{display:none;}</style>
			<b><?php esc_html_e( 'Please use the global multisite settings to set up the expiry headers for the whole multisite.', 'mace' ); ?></b>

		<?php } ?>
	</p>
	<?php
}

/**
 * Settings page
 */
function mace_expiry_settings_page() {
	$page_id        = mace_get_expiry_settings_page_id();
	$page_config    = mace_get_expiry_settings_page_config();
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
 * Enabled?
 */
function mace_expiry_setting_headers_enabled() {
	?>
	<input name="mace_expiry_headers" id="mace_expiry_headers" class="mace-toggle-module" type="checkbox" <?php echo checked( mace_expiry_is_headers_enabled() ); ?> value="standard" />
	<?php
}

/**
 * Content
 */
function mace_expiry_setting_content() {
	$site_domain = mace_get_site_domain();

	?>
	<textarea name="mace_expiry_content" id="mace_expiry_content" cols="40" rows="20" class="large-text"><?php echo esc_textarea( mace_expiry_content() ); ?></textarea>
	<?php
}
