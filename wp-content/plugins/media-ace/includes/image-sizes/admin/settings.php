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

add_filter( 'mace_settings_pages',          'mace_register_image_sizes_settings_page', 10 );

function mace_get_image_sizes_settings_page_id() {
	return apply_filters( 'mace_image_sizes_settings_page_id', 'mace-image-sizes-settings' );
}

function mace_get_image_sizes_settings_page_config() {
	return apply_filters( 'mace_image_sizes_settings_config', array(
		'tab_title'                 => __( 'Image Sizes', 'mace' ),
		'page_title'                => __( 'Manage Image Sizes', 'mace' ),
		'page_description_callback' => 'mace_image_sizes_settings_page_description',
		'page_callback'             => 'mace_image_sizes_settings_page',
		'fields'                    => array(),
	) );
}

function mace_register_image_sizes_settings_page( $pages ) {
	$pages[ mace_get_image_sizes_settings_page_id() ] = mace_get_image_sizes_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_image_sizes_settings_page_description() {
	?>
	<p>
		<?php printf( esc_html__( 'After editing or adding an image size, you have to %s to apply changes to all existing images.', 'mace' ), '<a href="' . esc_url( admin_url( 'admin.php?page=' . mace_get_image_bulk_settings_page_id() ) ) . '">' . esc_html__( 'Regenerate Thumbnails', 'mace' ) . '</a>' ); ?>
	</p>
	<?php
}

/**
 * Settings page
 */
function mace_image_sizes_settings_page() {
	$page_config    = mace_get_image_sizes_settings_page_config();
	$ver            = mace_get_plugin_version();
	$base_url       = mace_get_plugin_url() . 'includes/image-sizes/admin/';

	// Load assets.
	wp_enqueue_style( 'mace-image-sizes', $base_url . 'css/image-sizes.css', array(), $ver );
	wp_enqueue_script( 'mace-image-sizes', $base_url . 'js/image-sizes.js', array( 'jquery' ), $ver, true );
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'MediaAce Settings', 'mace' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php mace_admin_settings_tabs( $page_config['tab_title'] ); ?></h2>
		<form action="options.php" method="post">

			<input type="hidden" id="mace-image-size-nonce" value="<?php echo esc_attr( wp_create_nonce( 'mace-image-size' ) ); ?>"/>
			<input type="hidden" id="mace-image-size-prefix" value="<?php echo esc_attr( mace_get_image_size_prefix() ); ?>"/>

			<h2>
				<?php echo esc_html( $page_config['page_title'] ); ?>
				<a href="#" class="page-title-action mace-image-size-add-new">Add New</a>
			</h2>
			<?php mace_image_sizes_add_new_form(); ?>

			<?php mace_image_sizes_settings_page_description(); ?>

			<?php
			require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/class-mace-image-size-list-table.php' );

			$list = new Mace_Image_Size_List_Table();

			$list->prepare_items();
			$list->views();
			$list->display();
			?>

		</form>
	</div>

	<?php
}

function mace_image_sizes_add_new_form() {
	?>
	<div class="mace-image-size-new-form mace-hidden">
		<h3><?php esc_html_e( 'New image size', 'mace' ); ?></h3>

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Name', 'mace' ); ?></th>
				<td>
					<input class="name-field" type="text" size="32" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Size', 'mace' ); ?>
				</th>
				<td>
					<label>
						<?php esc_html_e( 'Width', 'mace' ); ?>
						<input class="width-field small-text" type="number" size="5" />
					</label>
					<label>
						<?php esc_html_e( 'Height', 'mace' ); ?>
						<input class="height-field small-text" type="number" size="5" />
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Crop', 'mace' ); ?>
				</th>
				<td>
					<input class="crop-field" type="checkbox" />

					<label>
						<?php esc_html_e( 'Crop X', 'mace' ); ?>
						<?php echo mace_image_sizes_crop_x_select('center'); ?>
					</label>
					<label>
						<?php esc_html_e( 'Crop Y', 'mace' ); ?>
						<?php echo mace_image_sizes_crop_y_select('center'); ?>
					</label>
				</td>
			</tr>
			</tbody>
		</table>

		<p class="mace-image-size-actions">
			<a href="#" class="button button-primary mace-image-size-action mace-image-size-add"><?php esc_html_e( 'Add', 'mace' ); ?></a>
		</p>
	</div>
	<?php
}

function mace_image_sizes_crop_x_select( $current ) {
	$out = '';

	$out .= '<select class="crop-x-field">';
	$out .= '<option value="left"' . selected( 'left', $current, false ) . '>' . esc_html__( 'Left', 'mace' ) . '</option>';
	$out .= '<option value="center"' . selected( 'center', $current, false ) . '>' . esc_html__( 'Center', 'mace' ) . '</option>';
	$out .= '<option value="right"' . selected( 'right', $current, false ) . '>' . esc_html__( 'Right', 'mace' ) . '</option>';
	$out .= '</select>';

	return $out;
}

function mace_image_sizes_crop_y_select( $current ) {
	$out = '';

	$out .= '<select class="crop-y-field">';
	$out .= '<option value="top"' . selected( 'top', $current, false ) . '>' . esc_html__( 'Top', 'mace' ) . '</option>';
	$out .= '<option value="center"' . selected( 'center', $current, false ) . '>' . esc_html__( 'Center', 'mace' ) . '</option>';
	$out .= '<option value="bottom"' . selected( 'bottom', $current, false ) . '>' . esc_html__( 'Bottom', 'mace' ) . '</option>';
	$out .= '</select>';

	return $out;
}