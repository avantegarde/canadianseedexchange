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

add_filter( 'mace_settings_pages', 'mace_register_image_bulk_settings_page', 10 );

function mace_get_image_bulk_settings_page_id() {
	return apply_filters( 'mace_image_bulk_settings_page_id', 'mace-image-bulk-settings' );
}

function mace_get_image_bulk_settings_page_config() {
	return apply_filters( 'mace_image_bulk_settings_config', array(
		'tab_title'                 => __( 'Image Bulk Actions', 'mace' ),
		'page_title'                => __( 'Process all images at once', 'mace' ),
		'page_description_callback' => 'mace_image_bulk_settings_page_description',
		'page_callback'             => 'mace_image_bulk_settings_page',
		'fields'                    => array(
			'mace_image_bulk_regenerate_thumbs' => array(
				'title'             => __( 'Regenerate Thumbnails', 'mace' ),
				'callback'          => 'mace_image_bulk_setting_regenerate_thumbs',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_image_bulk_add_watermarks' => array(
				'title'             => __( 'Add Watermarks', 'mace' ),
				'callback'          => 'mace_image_bulk_setting_add_watermarks',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_image_bulk_remove_watermarks' => array(
				'title'             => __( 'Remove Watermarks', 'mace' ),
				'callback'          => 'mace_image_bulk_setting_remove_watermarks',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function mace_register_image_bulk_settings_page( $pages ) {
	$pages[ mace_get_image_bulk_settings_page_id() ] = mace_get_image_bulk_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_image_bulk_settings_page_description() {}

/**
 * Settings page
 */
function mace_image_bulk_settings_page() {
	$page_id        = mace_get_image_bulk_settings_page_id();
	$page_config    = mace_get_image_bulk_settings_page_config();
	$ver            = mace_get_plugin_version();
	$base_url       = mace_get_plugin_url() . 'includes/image-bulk/admin/';
	$request_action = filter_input( INPUT_GET, 'mace-action', FILTER_SANITIZE_STRING );
	$request_ids     = filter_input( INPUT_GET, 'mace-ids', FILTER_SANITIZE_STRING );

	// Load assets.
	wp_enqueue_style( 'mace-regenerate-thumbs', $base_url . 'css/regenerate-thumbs.css', array(), $ver );
	wp_enqueue_script( 'mace-regenerate-thumbs', $base_url . 'js/regenerate-thumbs.js', array( 'jquery' ), $ver, true );

	wp_enqueue_style( 'mace-watermarks', $base_url . 'css/watermarks.css', array(), $ver );
	wp_enqueue_script( 'mace-watermarks', $base_url . 'js/watermarks.js', array( 'jquery' ), $ver, true );
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'MediaAce Settings', 'mace' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php mace_admin_settings_tabs( $page_config['tab_title'] ); ?></h2>
		<form action="options.php" method="post">
			<input type="hidden" id="mace-image-bulk-nonce" value="<?php echo esc_attr( wp_create_nonce( 'mace-image-bulk' ) ); ?>"/>

			<?php if ( ! empty( $request_action ) && ! empty( $request_ids ) ): ?>
			<input type="hidden" id="mace-request-action" value="<?php echo esc_attr( $request_action ); ?>"/>
			<input type="hidden" id="mace-request-ids" value="<?php echo implode( ',', array_map( 'absint', explode( ',', $request_ids ) ) ); ?>"/>
			<?php endif; ?>

			<?php settings_fields( $page_id ); ?>
			<?php do_settings_sections( $page_id ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'mace' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

function mace_image_bulk_setting_regenerate_thumbs() {
	?>
	<p>
		<?php printf( esc_html__( 'Create thumbnails based on defined %s and clean up disk from old unused files. This process can take a lot of time (and server CPU/memory) and have to be run with caution.', 'mace' ), '<a href="' . esc_url( admin_url( 'admin.php?page=' . mace_get_image_sizes_settings_page_id() ) ) . '">' . esc_html__( 'Image Sizes', 'mace' ) . '</a>' ); ?>
	</p>
	<br />
	<div class="mace-regenerate-thumbs">
		<button class="button button-secondary mace-start"><?php esc_html_e( 'Start', 'mace' ); ?></button>

		<h3 class="mace-processing-header"><?php esc_html_e( 'Processing files...', 'mace' ); ?></h3>
		<h3 class="mace-processed-header"><?php esc_html_e( 'Completed', 'mace' ); ?></h3>
		<div class="mace-stats">
			<p>
				<?php esc_html_e( 'Please be patient while the process can take a while (depends on your server resources).', 'mace' ); ?>
			</p>
			<p class="mace-to-process"><?php esc_html_e( 'To process', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-processed"><?php esc_html_e( 'Processed', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-success"><?php esc_html_e( 'Success', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-failed"><?php esc_html_e( 'Failed', 'mace' ); ?>: <span>0</span></p>
		</div>
		<ol class="mace-processed-files"></ol>
	</div>
	<?php
}

function mace_image_bulk_setting_add_watermarks() {
	?>
	<div class="mace-watermarks mace-add-watermarks">
		<button class="button button-secondary mace-start"><?php esc_html_e( 'Start', 'mace' ); ?></button>

		<h3 class="mace-processing-header"><?php esc_html_e( 'Processing files...', 'mace' ); ?></h3>
		<h3 class="mace-processed-header"><?php esc_html_e( 'Completed', 'mace' ); ?></h3>
		<div class="mace-stats">
			<p>
				<?php esc_html_e( 'Please be patient while the process can take a while (depends on your server resources).', 'mace' ); ?>
			</p>
			<p class="mace-to-process"><?php esc_html_e( 'To process', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-processed"><?php esc_html_e( 'Processed', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-success"><?php esc_html_e( 'Success', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-failed"><?php esc_html_e( 'Failed', 'mace' ); ?>: <span>0</span></p>
		</div>
		<ol class="mace-processed-files"></ol>
	</div>
	<?php
}

function mace_image_bulk_setting_remove_watermarks() {
	?>
	<div class="mace-watermarks mace-remove-watermarks">
		<button class="button button-secondary mace-start"><?php esc_html_e( 'Start', 'mace' ); ?></button>

		<h3 class="mace-processing-header"><?php esc_html_e( 'Processing files...', 'mace' ); ?></h3>
		<h3 class="mace-processed-header"><?php esc_html_e( 'Completed', 'mace' ); ?></h3>
		<div class="mace-stats">
			<p>
				<?php esc_html_e( 'Please be patient while the process can take a while (depends on your server resources).', 'mace' ); ?>
			</p>
			<p class="mace-to-process"><?php esc_html_e( 'To process', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-processed"><?php esc_html_e( 'Processed', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-success"><?php esc_html_e( 'Success', 'mace' ); ?>: <span>0</span></p>
			<p class="mace-failed"><?php esc_html_e( 'Failed', 'mace' ); ?>: <span>0</span></p>
		</div>
		<ol class="mace-processed-files"></ol>
	</div>
	<?php
}