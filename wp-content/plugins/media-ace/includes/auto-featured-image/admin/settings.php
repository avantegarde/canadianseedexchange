<?php
/**
 * Auto Featured Image Settings page
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'mace_settings_pages', 'mace_register_auto_featured_image_settings_page', 10 );

function mace_get_auto_featured_image_settings_page_id() {
	return apply_filters( 'mace_auto_featured_image_settings_page_id', 'mace-auto-featured-image-settings' );
}

function mace_get_auto_featured_image_settings_page_config() {
	return apply_filters( 'mace_auto_featured_image_settings_config', array(
		'tab_title'                 => __( 'Auto Featured Images', 'mace' ),
		'page_title'                => __( '', 'mace' ),
		'page_description_callback' => 'mace_auto_featured_image_settings_page_description',
		'page_callback'             => 'mace_auto_featured_image_settings_page',
		'fields'                    => array(
			'mace_auto_featured_image_enable' => array(
				'title'             => __( 'Auto download embed featured images', 'mace' ),
				'callback'          => 'mace_auto_featured_image_setting_enable',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_auto_featured_image_settings_page( $pages ) {
	$pages[ mace_get_auto_featured_image_settings_page_id() ] = mace_get_auto_featured_image_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_auto_featured_image_settings_page_description() {}

/**
 * Settings page
 */
function mace_auto_featured_image_settings_page() {
	$page_id        = mace_get_auto_featured_image_settings_page_id();
	$page_config    = mace_get_auto_featured_image_settings_page_config();
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
 * Enable auto featured
 */
function mace_auto_featured_image_setting_enable() {
	?>
	<input name="mace_auto_featured_image_enable" id="mace_auto_featured_image_enable" type="checkbox" <?php echo checked( mace_get_auto_featured_image_enable() ); ?> value="standard" />
	<?php
}
