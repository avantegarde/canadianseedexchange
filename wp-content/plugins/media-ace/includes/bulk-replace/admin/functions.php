<?php
/**
 * Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Return array of image sizes srcs for given id.
 *
 * @param int $image_id Image id.
 * @return array
 */
function mace_get_all_image_sizes_srcs( $image_id ) {
	$result = array();
	$src = wp_get_attachment_image_src( $image_id, 'full' );
	$result[] = $src[0];
	$sizes = get_intermediate_image_sizes();
	foreach ( $sizes as $size ) {
		$src = wp_get_attachment_image_src( $image_id, $size );
		$result[] = $src[0];
	}
	$result = array_unique( $result );
	return $result;
}
/**
 * Fix image path for multisite.
 *
 * @param  string $path Path.
 * @return string
 */
function mace_fix_image_path_for_multisite( $path ) {
	$upload_dir = wp_upload_dir();
	$uploads_path = $upload_dir['path'];
	$uploads_path = preg_replace( '/(.*uploads)(.*)/', '$1', $uploads_path );

	$path = preg_replace( '/(.*)(.*uploads)/U', '$3', $path );
	return $uploads_path . $path;
}

add_action('admin_head-upload.php',     'mace_replace_bulk_actions' );

/**
 * Add new items to the Bulk Actions using Javascript
 */
function mace_replace_bulk_actions() {

	if ( ! current_user_can( mace_get_capability() ) ) {
		return;
	}
	?>
	<script type="text/javascript">
		(function($) {
			$(document).ready(function(){
				var option = '<option value="mace_bulk_replace"><?php echo esc_attr(__('Replace Images', 'mace')); ?></option>';

				$('select[name^="action"] option:last-child').before(option);
			});
		})(jQuery);
	</script>
	<?php
}

add_action('admin_action_mace_bulk_replace',  				'mace_replace_bulk_actions_handler' );
add_action('admin_action_-1',                               'mace_replace_bulk_actions_handler' );

/**
 * Handles the bulk actions
 */
function mace_replace_bulk_actions_handler() {
	$action  = $_REQUEST['action'];
	$action2 = $_REQUEST['action2'];
	$media   = isset( $_REQUEST['media'] ) ? $_REQUEST['media'] : array();

	$bulk_action = 'mace_bulk_replace';

	if ( empty( $action ) && empty( $action2 ) ) {
		return;
	}

	if ( $action !== $bulk_action && $action2 !== $bulk_action ) {
		return;
	}

	if ( empty( $media ) || ! is_array( $media ) ) {
		return;
	}

	check_admin_referer( 'bulk-media' );
	$ids = implode( ',', array_map( 'absint', $_REQUEST['media'] ) );

	wp_redirect( mace_replace_action_url( $ids ) );
	exit();
}

function mace_replace_action_url( $ids ) {
	if ( ! is_array( $ids ) ) {
		$ids = array( $ids );
	}

	$url = admin_url( 'admin.php?page=' . mace_get_bulk_replace_settings_page_id() );

	$url = add_query_arg( array(
		'mace-action'   => 'bulk-replace',
		'mace-ids'      => implode( ',', $ids ),
	), $url );

	return $url;
}
