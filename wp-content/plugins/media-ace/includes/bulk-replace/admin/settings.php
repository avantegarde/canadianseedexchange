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

add_filter( 'mace_settings_pages', 'mace_register_bulk_replace_settings_page', 10 );

function mace_get_bulk_replace_settings_page_id() {
	return apply_filters( 'mace_bulk_replace_settings_page_id', 'mace-bulk-replace-settings' );
}

function mace_get_bulk_replace_settings_page_config() {
	return apply_filters( 'mace_bulk_replace_settings_config', array(
		'tab_title'                 => __( 'Image Bulk Replace', 'mace' ),
		'page_title'                => __( 'Replace multiple images', 'mace' ),
		'page_description_callback' => 'mace_bulk_replace_settings_page_description',
		'page_callback'             => 'mace_bulk_replace_settings_page',
		'fields'                    => array(
			'mace_bulk_replace_replace_images' => array(
				'title'             => __( 'Choose images to replace', 'mace' ),
				'callback'          => 'mace_bulk_replace_setting_replace_images',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_bulk_replace_replacement_images' => array(
				'title'             => __( 'Choose to insert (filenames must match)', 'mace' ),
				'callback'          => 'mace_bulk_replace_setting_replacement_images',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_bulk_replace_replace_images_start' => array(
				'title'             => __( 'Replace images', 'mace' ),
				'callback'          => 'mace_bulk_replace_setting_replace_images_start',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_bulk_replace_settings_page( $pages ) {
	$pages[ mace_get_bulk_replace_settings_page_id() ] = mace_get_bulk_replace_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_bulk_replace_settings_page_description() {}

/**
 * Settings page
 */
function mace_bulk_replace_settings_page() {
	$page_id        = mace_get_bulk_replace_settings_page_id();
	$page_config    = mace_get_bulk_replace_settings_page_config();
	$ver            = mace_get_plugin_version();
	$base_url       = mace_get_plugin_url() . 'includes/bulk-replace/admin/';
	$request_action = filter_input( INPUT_GET, 'mace-action', FILTER_SANITIZE_STRING );
	$request_ids     = filter_input( INPUT_GET, 'mace-ids', FILTER_SANITIZE_STRING );

	wp_enqueue_script('plupload-handlers');
	wp_enqueue_style( 'mace-replace-images', $base_url . 'css/replace-images.css', array(), $ver );
	wp_enqueue_script( 'mace-replace-images', $base_url . 'js/replace-images.js', array( 'jquery' ), $ver, true );
	wp_enqueue_media();
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'MediaAce Settings', 'mace' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php mace_admin_settings_tabs( $page_config['tab_title'] ); ?></h2>
		<form action="options.php" method="post">
			<input type="hidden" id="mace-image-bulk-replace-nonce" value="<?php echo esc_attr( wp_create_nonce( 'mace-image-bulk-replace' ) ); ?>"/>

			<?php if ( ! empty( $request_action ) && ! empty( $request_ids ) ): ?>
				<input type="hidden" id="mace-request-action" value="<?php echo esc_attr( $request_action ); ?>"/>
				<input type="hidden" id="mace-request-ids" value="<?php echo implode( ',', array_map( 'absint', explode( ',', $request_ids ) ) ); ?>"/>

				<div class="wrap">
					<h1>Drop files below and then click start replacement</h1>
					<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'media-new.php' ); ?>" class="" id="file-form">

					<?php media_upload_form(); ?>

					<script type="text/javascript">
					var post_id = 0, shortform = 3;
					</script>
					<input type="hidden" name="post_id" id="post_id" value="0" />
					<?php wp_nonce_field( 'media-form' ); ?>
					<div id="media-items" class="hide-if-no-js"></div>
					</form>
				</div>
				<button href="#" disabled class="mace-replace-start-button button">Start replacement</button>
				<p class="image-replace-status"></p>
			<?php else : ?>
			<p>Please pick the images in the Media section and use the bulk action.</p>
			<?php endif; ?>


		</form>
	</div>

	<?php
}

function mace_bulk_replace_setting_replace_images() {
	?>
	<div class="mace-replaced-images">
		<button class="button button-secondary mace-start bulk-replace-images-button"><?php esc_html_e( 'Pick', 'mace' ); ?></button>

	</div>
	<?php
}

function mace_bulk_replace_setting_replacement_images() {
	?>
	<div class="mace-replacement-images">
		<button class="button button-secondary mace-start bulk-replace-insert-button"><?php esc_html_e( 'Pick', 'mace' ); ?></button>

	</div>
	<?php
}

function mace_bulk_replace_setting_replace_images_start() {
	?>
	<div class="mace-bulk-replace">
		<button disabled class="button button-secondary mace-start bulk-replace-start-button"><?php esc_html_e( 'Start', 'mace' ); ?></button>
		<p class="image-replace-status">
		</p>
	</div>
	<?php
}
