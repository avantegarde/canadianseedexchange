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

add_filter( 'mace_settings_pages', 'mace_register_watermarks_settings_page', 10 );

function mace_get_watermarks_settings_page_id() {
	return apply_filters( 'mace_watermarks_settings_page_id', 'mace-watermarks-settings' );
}

function mace_get_watermarks_settings_page_config() {
	return apply_filters( 'mace_watermarks_settings_config', array(
		'tab_title'                 => __( 'Watermarks', 'mace' ),
		'page_title'                => __( 'Image Watermark', 'mace' ),
		'page_description_callback' => 'mace_watermarks_settings_page_description',
		'page_callback'             => 'mace_watermarks_settings_page',
		'fields'                    => array(
			'mace_watermarks_enabled' => array(
				'title'             => __( 'Enabled?', 'mace' ),
				'callback'          => 'mace_watermarks_setting_enabled',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_watermarks_image' => array(
				'title'             => __( 'Watermark image', 'mace' ),
				'callback'          => 'mace_watermarks_setting_image',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_watermarks_position' => array(
				'title'             => __( 'Position on image', 'mace' ),
				'callback'          => 'mace_watermarks_setting_position',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_watermarks_opacity' => array(
				'title'             => __( 'Opacity', 'mace' ),
				'callback'          => 'mace_watermarks_setting_opacity',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_watermarks_advanced_options' => array(
				'title'             => '<a href="#" id="mace-watermarks-advanced-options">' . __( 'Advanced options', 'mace' ) . '</a>',
				'callback'          => '__return_empty_string',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_watermarks_sizes' => array(
				'title'             => __( 'Image sizes', 'mace' ),
				'callback'          => 'mace_watermarks_setting_sizes',
				'sanitize_callback' => 'mace_sanitize_text_array',
				'args'              => array(),
			),
			'mace_watermarks_scale' => array(
				'title'             => __( 'Scale', 'mace' ),
				'callback'          => 'mace_watermarks_setting_scale',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_watermarks_offset' => array(
				'title'             => __( 'Offset', 'mace' ),
				'callback'          => 'mace_watermarks_setting_offset',
				'sanitize_callback' => 'mace_sanitize_text_array',
				'args'              => array(),
			),
//			'mace_watermarks_backup' => array(
//				'title'             => __( 'Backup', 'mace' ),
//				'callback'          => 'mace_watermarks_setting_backup',
//				'sanitize_callback' => 'sanitize_text_field',
//				'args'              => array(),
//			),
		),
	) );
}

function mace_register_watermarks_settings_page( $pages ) {
	$pages[ mace_get_watermarks_settings_page_id() ] = mace_get_watermarks_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_watermarks_settings_page_description() {
	?>
	<p>
		<?php esc_html_e( 'Add your logo, stamp or signature to images to protect them and indicate your ownership.', 'mace' ); ?>
		<?php printf( esc_html__( 'To apply watermarks to all existing images, use the %s bulk tool.', 'mace' ), '<a href="' . esc_url( admin_url( 'admin.php?page=' . mace_get_image_bulk_settings_page_id() ) ) . '">' . esc_html__( 'Apply Watermarks', 'mace' ) . '</a>' ); ?>
	</p>
	<?php
}

/**
 * Settings page
 */
function mace_watermarks_settings_page() {
	$page_id        = mace_get_watermarks_settings_page_id();
	$page_config    = mace_get_watermarks_settings_page_config();
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
function mace_watermarks_setting_enabled() {
	?>
	<input name="mace_watermarks_enabled" id="mace_watermarks_enabled" class="mace-toggle-module" type="checkbox" <?php echo checked( mace_watermarks_is_enabled() ); ?> value="standard" />
	<?php
}

/**
 * Image
 */
function mace_watermarks_setting_image() {
	$attachment_id = mace_watermarks_get_image();

	mace_select_image_control( 'mace_watermarks_image', $attachment_id );
}

/**
 * Position
 */
function mace_watermarks_setting_position() {
	$position       = mace_watermarks_get_position();
	$all_positions  = mace_watermarks_get_allowed_positions();

	?>
	<select name="mace_watermarks_position" id="mace_watermarks_position">
	<?php foreach ( $all_positions as $position_id ) : ?>
		<option value="<?php echo esc_attr( $position_id ); ?>"<?php selected( $position, $position_id ); ?>>
			<?php echo esc_html( str_replace( '_', ' ', $position_id ) ); ?>
		</option>
	<?php endforeach; ?>
	</select>
	<?php
}

/**
 * Opacity
 */
function mace_watermarks_setting_opacity() {
	?>
	<input class="small-text" name="mace_watermarks_opacity" id="mace_watermarks_opacity" type="number" min="0" max="100" step="1" value="<?php echo absint( mace_watermarks_get_opacity() ); ?>" /> &#37;
	<?php
}

/**
 * Image sizes
 */
function mace_watermarks_setting_sizes() {
	$sizes = mace_watermarks_get_sizes();

	?>
	<p>
		<?php esc_html_e( 'Choose sizes to be watermarked', 'mace' ); ?>:
	</p>
	<p>
		<input name="mace_watermarks_sizes[]" type="checkbox" value="full"<?php checked( in_array( 'full', $sizes, true ) ); ?> /> <?php echo esc_html( 'full' ); ?>
	</p>
	<?php
	foreach ( get_intermediate_image_sizes() as $size ) {
		?>
		<p>
			<input name="mace_watermarks_sizes[]" type="checkbox" value="<?php echo esc_attr( $size ); ?>"<?php checked( in_array( $size, $sizes, true ) ); ?> /> <?php echo esc_html( $size ); ?>
		</p>
		<?php
	}
}

/**
 * Scale
 */
function mace_watermarks_setting_scale() {
	?>
	<input class="small-text" name="mace_watermarks_scale" id="mace_watermarks_scale" type="number" min="0" max="100" step="1" value="<?php echo absint( mace_watermarks_get_scale() ); ?>" /> &#37;
	<p class="description">
		<?php esc_html_e( 'Scale of watermark in relation to image.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Offset
 */
function mace_watermarks_setting_offset() {
	?>
	<p>
		<input class="small-text" name="mace_watermarks_offset[x]" id="mace_watermarks_offset_x" size="5" type="number" min="0" value="<?php echo absint( mace_watermarks_get_offset('x') ); ?>" /> &#37;
	</p>
	<p class="description">
		<?php esc_html_e( 'Move watermark from image edges. Does not apply for the "center center" position.', 'mace' ); ?>
	</p>
	<?php
}

/**
 * Backup
 */
function mace_watermarks_setting_backup() {
	?>
	<input name="mace_watermarks_backup" id="mace_watermarks_backup" type="checkbox" <?php echo checked( mace_watermarks_use_backup() ); ?> value="standard" />
	<p class="description">
		<?php esc_html_e( 'Keep copy of original images to be able to remove watermarks.', 'mace' ); ?>
		<?php printf( esc_html__( 'To remove watermarks from all existing images, use the %s bulk tool.', 'mace' ), '<a href="' . esc_url( admin_url( 'admin.php?page=' . mace_get_image_bulk_settings_page_id() ) ) . '">' . esc_html__( 'Remove Watermarks', 'mace' ) . '</a>' ); ?>
	</p>
	<?php
}
