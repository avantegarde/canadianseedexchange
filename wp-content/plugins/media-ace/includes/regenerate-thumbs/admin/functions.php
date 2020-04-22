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

add_filter('media_row_actions',         'mace_regenerate_media_row_actions', 10, 2);
add_action('admin_head-upload.php',     'mace_regenerate_bulk_actions' );
add_action('admin_action_mace_bulk_regenerate_thumbnails',  'mace_regenerate_bulk_actions_handler' );
add_action('admin_action_-1',                               'mace_regenerate_bulk_actions_handler' );

/**
 * Regenerate thumbnails
 *
 * @param int $id Media id.
 *
 * @return array
 */
function mace_regenerate_thumbs( $id ) {
	if ( mace_is_function_allowed( 'set_time_limit' ) ) {
		set_transient( '_mace_set_time_limit_blocked', '1', 60 * 15 );
	}
	@set_time_limit( 0 );
	error_reporting( 0 );

	$image = get_post( $id );

	if ( ! $image ) {
		return new WP_Error( 'mace_missing_file', esc_html__( 'Image not found.', 'mace' ) );
	}

	if ( ! mace_is_attachment_image( $image ) ) {
		return new WP_Error( 'mace_wrong_file_type', esc_html__( 'Invalid image.', 'mace' ) );
	}

	$upload_dir     = wp_upload_dir();
	$image_fullpath = get_attached_file( $image->ID );
	$orig_path      = $image_fullpath;

	if ( empty( $image_fullpath ) ) {
		// Try get image path from url.
		if ( false !== strrpos( $image->guid, $upload_dir['baseurl'] ) ) {
			$image_fullpath = realpath( $upload_dir['basedir'] . DIRECTORY_SEPARATOR . substr( $image->guid, strlen( $upload_dir['baseurl'] ), strlen( $image->guid ) ) );

			if ( false === realpath( $image_fullpath ) ) {
				return new WP_Error( 'mace_original_file_missing', esc_html__( 'The original image file cannot be found.', 'mace' ) );
			}
		} else {
			return new WP_Error( 'mace_original_file_missing', esc_html__( 'The original image file cannot be found.', 'mace' ) );
		}
	}

	// Image path incomplete.
	if ( false === strrpos( $image_fullpath, $upload_dir['basedir'] ) ) {
		$image_fullpath = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $image_fullpath;
	}

	// Image not exists.
	if ( ! file_exists( $image_fullpath ) || false === realpath( $image_fullpath ) ) {
		// Try get image path from url.
		if ( false !== strrpos( $image->guid, $upload_dir['baseurl'] ) ) {
			$image_fullpath = realpath( $upload_dir['basedir'] . DIRECTORY_SEPARATOR . substr( $image->guid, strlen( $upload_dir['baseurl'] ), strlen( $image->guid ) ) );

			if ( false === realpath( $image_fullpath ) ) {
				return new WP_Error( 'mace_original_file_missing', esc_html__( 'The original image file cannot be found.', 'mace' ) );
			}
		} else {
			return new WP_Error( 'mace_original_file_missing', esc_html__( 'The original image file cannot be found.', 'mace' ) );
		}
	}

	update_attached_file( $image->ID, $image_fullpath );

	// Results
	$thumb_deleted    = array();
	$thumb_error      = array();
	$thumb_regenerate = array();

	// Hack to find thumbnail
	$file_info = pathinfo( $image_fullpath );
	$file_info['filename'] .= '-';

	// Try delete all thumbnails.
	$files = array();
	$path  = opendir( $file_info['dirname'] );

	if ( false !== $path ) {
		while ( false !== ( $thumb = readdir( $path ) ) ) {
			if ( ! ( strrpos( $thumb, $file_info['filename'] ) === false ) ) {
				$files[] = $thumb;
			}
		}

		closedir( $path );
		sort( $files );
	}

	foreach ( $files as $thumb ) {
		$thumb_fullpath = $file_info['dirname'] . DIRECTORY_SEPARATOR . $thumb;
		$thumb_info     = pathinfo( $thumb_fullpath );
		$valid_thumb    = explode( $file_info['filename'], $thumb_info['filename'] );

		if ( $valid_thumb[0] == "" ) {
			$dimension_thumb = explode( 'x', $valid_thumb[1] );
			if ( count( $dimension_thumb ) == 2 ) {
				if ( is_numeric( $dimension_thumb[0] ) && is_numeric( $dimension_thumb[1] ) ) {
					unlink( $thumb_fullpath );
					if ( ! file_exists( $thumb_fullpath ) ) {
						$thumb_deleted[] = sprintf( "%sx%s", $dimension_thumb[0], $dimension_thumb[1] );
					} else {
						$thumb_error[] = sprintf( "%sx%s", $dimension_thumb[0], $dimension_thumb[1] );
					}
				}
			}
		}
	}

	// Regenerate all thumbnails.
	$metadata = wp_generate_attachment_metadata( $image->ID, $image_fullpath );

	if ( is_wp_error( $metadata ) ) {
		return new WP_Error( 'mace_generate_metadata_failed', $metadata->get_error_message() );
	}

	if ( empty( $metadata ) ) {
		return new WP_Error( 'mace_unknown_metadata_error', esc_html__( 'Metadata was not generated properly.', 'mace' ) );
	}

	wp_update_attachment_metadata( $image->ID, $metadata );

	// Verify results (deleted, errors, success)
	$files = array();
	$path  = opendir( $file_info['dirname'] );

	if ( false !== $path ) {
		while ( false !== ( $thumb = readdir( $path ) ) ) {
			if ( ! ( strrpos( $thumb, $file_info['filename'] ) === false ) ) {
				$files[] = $thumb;
			}
		}
		closedir( $path );
		sort( $files );
	}

	foreach ( $files as $thumb ) {
		$thumb_fullpath = $file_info['dirname'] . DIRECTORY_SEPARATOR . $thumb;
		$thumb_info     = pathinfo( $thumb_fullpath );
		$valid_thumb    = explode( $file_info['filename'], $thumb_info['filename'] );

		if ( $valid_thumb[0] == "" ) {
			$dimension_thumb = explode( 'x', $valid_thumb[1] );
			if ( count( $dimension_thumb ) == 2 ) {
				if ( is_numeric( $dimension_thumb[0] ) && is_numeric( $dimension_thumb[1] ) ) {
					$thumb_regenerate[] = sprintf( "%sx%s", $dimension_thumb[0], $dimension_thumb[1] );
				}
			}
		}
	}

	// Remove success if has in error list.
	foreach ( $thumb_regenerate as $key => $regenerate ) {
		if ( in_array( $regenerate, $thumb_error ) ) {
			unset( $thumb_regenerate[ $key ] );
		}
	}

	// Remove deleted if has in success list
	foreach ( $thumb_deleted as $key => $deleted ) {
		if ( in_array( $deleted, $thumb_regenerate ) ) {
			unset( $thumb_deleted[ $key ] );
		}
	}

	// Make an array again.
	$thumb_deleted = array_values( $thumb_deleted );

	$generation_time = timer_stop();

	return array(
		'filename'  => get_the_title( $id ),
		'data'      => array(
			'image_path'            => $orig_path,
			'generated_sizes'       => $thumb_regenerate,
			'deleted_sizes'         => $thumb_deleted,
			'failed_sizes'          => $thumb_error,
			'generation_time'       => $generation_time,
		)
	);
}

function mace_regenerate_action_url( $ids ) {
	if ( ! is_array( $ids ) ) {
		$ids = array( $ids );
	}

	$url = admin_url( 'admin.php?page=' . mace_get_image_bulk_settings_page_id() );

	$url = add_query_arg( array(
		'mace-action'   => 'regenerate-thumbs',
		'mace-ids'      => implode( ',', $ids ),
	), $url );

	return $url;
}

/**
 * Add a "Regenerate Thumbnails" link to the media row actions
 *
 * @param array  $actions       List of registered actions.
 * @param string $post          Attachment.
 *
 * @return array
 */
function mace_regenerate_media_row_actions($actions, $post) {
	if ( ! mace_is_attachment_image( $post ) || ! current_user_can( mace_get_capability() ) ) {
		return $actions;
	}

	$action_url = mace_regenerate_action_url( $post->ID );

	$actions['mace_regenerate_thumbnails'] = sprintf( '<a href="%s" title="%s">%s</a>', esc_url( $action_url ), esc_attr__( 'Regenerate thumbnails for this image', 'mace' ), esc_html__( 'Regenerate Thumbnails', 'mace' ) );

	return $actions;
}

/**
 * Add new items to the Bulk Actions using Javascript
 */
function mace_regenerate_bulk_actions() {

	if ( ! current_user_can( mace_get_capability() ) ) {
		return;
	}
	?>
	<script type="text/javascript">
		(function($) {
			$(document).ready(function(){
				var option = '<option value="mace_bulk_regenerate_thumbnails"><?php echo esc_attr(__('Regenerate Thumbnails', 'mace')); ?></option>';

				$('select[name^="action"] option:last-child').before(option);
			});
		})(jQuery);
	</script>
	<?php
}

/**
 * Handles the bulk actions
 */
function mace_regenerate_bulk_actions_handler() {
	$action  = $_REQUEST['action'];
	$action2 = $_REQUEST['action2'];
	$media   = isset( $_REQUEST['media'] ) ? $_REQUEST['media'] : array();

	$bulk_action = 'mace_bulk_regenerate_thumbnails';

	if ( empty( $action ) && empty( $action2 ) ) {
		return;
	}

	if ( $action !== $bulk_action && $action2 !== $bulk_action ) {
		return;
	}

	if ( empty( $media ) || ! is_array( $media ) ) {
		return;
	}

	check_admin_referer('bulk-media');
	$ids = implode( ',', array_map( 'absint', $_REQUEST['media'] ) );

	wp_redirect( mace_regenerate_action_url( $ids ) );
	exit();
}
