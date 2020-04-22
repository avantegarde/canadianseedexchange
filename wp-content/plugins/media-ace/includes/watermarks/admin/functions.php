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

if ( mace_watermarks_is_enabled() ) {
	add_filter( 'wp_generate_attachment_metadata',          'mace_generate_attachment_metadata', 10, 2 );
	add_filter( 'wp_image_editors',                         'mace_register_image_editors' );
	add_filter( 'media_row_actions',                        'mace_watermarks_media_row_actions', 10, 2);
	add_action( 'admin_head-upload.php',                    'mace_watermarks_bulk_actions' );
	add_action( 'admin_action_mace_bulk_add_watermarks',    'mace_watermarks_bulk_actions_handler' );
	add_action( 'admin_action_mace_bulk_remove_watermarks', 'mace_watermarks_bulk_actions_handler' );
	add_action( 'admin_action_-1',                          'mace_watermarks_bulk_actions_handler' );
	add_action( 'admin_enqueue_scripts',                    'mace_watermarks_enqueue_scripts' );
	add_action( 'check_ajax_referer',                       'mace_dont_watermark_new_watermark', 10, 2 );
	add_action( 'delete_attachment',                        'mace_delete_attachment', 10, 1 );
}

/**
 * Add watermarks to an image during generating its metadata
 *
 * @param array $metadata           Attachment generated metadata.
 * @param int   $attachment_id      Attachment id.
 *
 * @return array
 */
function mace_generate_attachment_metadata( $metadata, $attachment_id ) {
	// Skip, attachment is not an image.
	if ( ! mace_is_attachment_image( $attachment_id ) ) {
		return $metadata;
	}

	$orig_file_full_path    = get_attached_file( $attachment_id );  // "FULL/SYSTEM/PATH/2017/06/filename.ext"
	$orig_file_rel_path     = $metadata['file'];                    // "2017/06/filename.ext"
	$orig_file_time         = substr( $orig_file_rel_path, 0, 7);   // Extract the date in form "2017/06"
	$orig_file_upload_dir   = wp_upload_dir( $orig_file_time );

	$watermark_image_id = mace_watermarks_get_image();

	// Skip, watermark image not set.
	if ( $watermark_image_id <= 0 ) {
		return $metadata;
	}

	// Skip, don't watermark the watermark.
	if ( $watermark_image_id === $attachment_id ) {
		return $metadata;
	}

	// Skip, attachment excluded from watermarking.
	if ( mace_watermarks_attachment_excluded( $attachment_id ) ) {
		return $metadata;
	}

	// All checked, we can load all necessary resources.
	require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/class-mace-image-editor-gd.php';
	require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'lib/class-mace-image-editor-imagick.php';

	// Remove current watermarks.
	mace_remove_watermarks( $attachment_id );

	$watermark_path = get_attached_file( $watermark_image_id );
	$watermark_meta = wp_get_attachment_metadata( $watermark_image_id );

	$sizes      = mace_watermarks_get_sizes();
	if ( strpos( $metadata['file'], '.gif' ) > -1 ) {
		foreach ( $sizes as $key => $value ) {
			if ( 'full' === $value ) {
				unset( $sizes[ $key ] );
			}
		}
	}
	$base_path  = trailingslashit( $orig_file_upload_dir['path'] );

	$sizes_to_watermarked = array();

	if ( in_array( 'full', $sizes ) ) {
		$sizes_to_watermarked['original'] = array(
			'path'   => $orig_file_full_path,
			'width'  => $metadata['width'],
			'height' => $metadata['height'],
		);
	}

	foreach( $sizes as $size ) {
		if ( ! isset( $metadata['sizes'][ $size ] ) ) {
			continue;
		}

		$sizes_to_watermarked[ $size ] = array(
			'path'   => $base_path . $metadata['sizes'][ $size ]['file'],
			'width'  => $metadata['sizes'][ $size ]['width'],
			'height' => $metadata['sizes'][ $size ]['height'],
		);
	}

	// Backup original file.
	if ( mace_watermarks_use_backup() ) {
		// Skip, backup required but we couldn't save the original file.
		if ( ! mace_watermarks_backup_file( $orig_file_full_path ) ) {
			return $metadata;
		}
	}

	$metadata['mace_watermarks'] = array();

	$scale = mace_watermarks_get_scale();

	foreach ( $sizes_to_watermarked as $size_id => $size_data ) {
		$path   = $size_data['path'];
		$width  = $size_data['width'];
		$height = $size_data['height'];

		$editor = wp_get_image_editor( $path );

		if ( is_wp_error( $editor ) || ! is_callable( array( $editor, 'add_watermark' ) ) ) {
			continue;
		}

		$watermark_editor = wp_get_image_editor( $watermark_path );

		// Proceed only if our editor was loaded.
		if ( is_wp_error( $watermark_editor ) || ! is_callable( array( $watermark_editor, 'add_watermark' ) ) ) {
			continue;
		}

		list( $w_width, $w_height ) = mace_watermarks_calc_watermark_size( $watermark_meta['width'], $watermark_meta['height'], $width, $height, $scale );

		$watermark_editor->resize( $w_width, $w_height );

		$success = $editor->add_watermark( $watermark_editor->get_image(), array(
			'position' => mace_watermarks_get_position(),
			'offset_x' => mace_watermarks_get_offset('x'),
			'opacity'  => mace_watermarks_get_opacity(),
		) );

		if ( ! is_wp_error( $success ) ) {
			$editor->save( $path );

			$metadata['mace_watermarks'][] = $path;
		}
	}

	return $metadata;
}

/**
 * Add watermarks
 *
 * @param int $id Media id.
 *
 * @return array
 */
function mace_add_watermarks( $id ) {
	if ( ! mace_is_function_allowed( 'set_time_limit' ) ) {
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

	$image_fullpath = get_attached_file( $image->ID );
	$orig_path      = $image_fullpath;

	$backup_path = '';

	if ( mace_watermarks_use_backup() ) {
		$backup_path = mace_watermarks_get_backup_path( $image_fullpath );
	}

	// Add Watermark action is hooked on the "wp_generate_attachment_metadata" action.
	$metadata = wp_generate_attachment_metadata( $image->ID, $orig_path );

	$time = timer_stop();

	return array(
		'data'      => array(
			'image_path'    => $orig_path,
			'backup_path'   => $backup_path,
			'time'          => $time,
			'watermarked'   => $metadata['mace_watermarks'],
		)
	);
}

/**
 * Remove watermarks
 *
 * @param int $id Media id.
 *
 * @return array
 */
function mace_remove_watermarks( $id ) {
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

	$image_fullpath = get_attached_file( $image->ID );
	$backup_path    = mace_watermarks_get_backup_path( $image_fullpath );

	if ( ! mace_watermarks_restore_file( $image_fullpath ) ) {
		return new WP_Error( 'mace_restore_failed', esc_html__( 'Backup file missing.', 'mace' ) );
	}

	// Add Watermark action is hooked on the "wp_generate_attachment_metadata" action.
	remove_filter( 'wp_generate_attachment_metadata', 'mace_generate_attachment_metadata', 10 );

	wp_generate_attachment_metadata( $image->ID, $image_fullpath );

	$time = timer_stop();

	return array(
		'data'      => array(
			'image_path'    => $image_fullpath,
			'backup_path'   => $backup_path,
			'time'          => $time,
		)
	);
}

/**
 * Backup file.
 *
 * @param string $path      Image path.
 *
 * @return bool
 */
function mace_watermarks_backup_file( $path ) {
	$backup_path = mace_watermarks_get_backup_path( $path );

	/**
	 * @global WP_Filesystem_Base $wp_filesystem Subclass
	 */
	WP_Filesystem();
	global $wp_filesystem;

	if ( $wp_filesystem->exists( $backup_path ) ) {
		return true;
	}

	return $wp_filesystem->copy( $path, $backup_path );
}

/**
 * Restore backuped file
 *
 * @param string $path      Image path.
 *
 * @return bool
 */
function mace_watermarks_restore_file( $path ) {
	$backup_path = mace_watermarks_get_backup_path( $path );

	if ( ! file_exists( $backup_path ) ) {
		return false;
	}

	/**
	 * @global WP_Filesystem_Base $wp_filesystem Subclass
	 */
	WP_Filesystem();
	global $wp_filesystem;

	return $wp_filesystem->copy( $backup_path, $path, true );
}

/**
 * Calculate watermark size to keep $scale value
 *
 * @param int $w_width          Watermark original width.
 * @param int $w_height         Watermark original height.
 * @param int $i_width          Image original width.
 * @param int $i_height         Image original height.
 * @param int $scale            Scale (0-100%), how much space of image watermark will occupy.
 *
 * @return array
 */
function mace_watermarks_calc_watermark_size( $w_width, $w_height, $i_width, $i_height, $scale ) {
	// Scale by X.
	if ( $w_width > $w_height ) {
		$new_w_width  = $i_width * $scale / 100;
		$prop = $new_w_width / $w_width;
		$new_w_height = $w_height * $prop;
	// Scale by Y.
	} else {
		$new_w_height  = $i_height * $scale / 100;
		$prop = $new_w_height / $w_height;
		$new_w_width = $w_width * $prop;
	}

	return array(
		(int) $new_w_width,
		(int) $new_w_height,
	);
}

/**
 * Register our own editors' implementations
 *
 * @param array $editors        WP editors.
 *
 * @return array
 */
function mace_register_image_editors( $editors ) {
	if ( ! is_array( $editors ) ) {
		return $editors;
	}

	if ( class_exists( 'Mace_Image_Editor_GD' ) ) {
		array_unshift( $editors, 'Mace_Image_Editor_GD' );
	}

	if ( class_exists( 'Mace_Image_Editor_Imagick' ) ) {
		array_unshift( $editors, 'Mace_Image_Editor_Imagick' );
	}

	return $editors;
}

/**
 * Check whether the Watermarks are enabled
 *
 * @return bool
 */
function mace_watermarks_is_enabled() {
	return 'standard' === get_option( 'mace_watermarks_enabled', 'standard' );
}

/**
 * Return watermark image
 *
 * @return int
 */
function mace_watermarks_get_image() {
	return (int) get_option( 'mace_watermarks_image', '' );
}

/**
 * Return watermark image position
 *
 * @return string
 */
function mace_watermarks_get_position() {
	return get_option( 'mace_watermarks_position', 'right_bottom' );
}

/**
 * Return watermark all possible positions
 *
 * @return array
 */
function mace_watermarks_get_allowed_positions() {
	$positions = array(
		'left_top',
		'right_top',
		'center_center',
		'right_bottom',
		'left_bottom',
	);

	return apply_filters( 'mace_watermarks_allowed_positions', $positions );
}

/**
 * Return watermark opacity (0-100)
 *
 * @return int
 */
function mace_watermarks_get_opacity() {
	return (int) get_option( 'mace_watermarks_opacity', 80 );
}
/**
 * Return sizes to be watermarked
 *
 * @return array
 */
function mace_watermarks_get_sizes() {
	$sizes = get_option( 'mace_watermarks_sizes', array() );

	// Default set up.
	if ( empty( $sizes ) ) {
		global $_wp_additional_image_sizes;

		$min_width = 700;

		$sizes[] = 'full';

		foreach ( get_intermediate_image_sizes() as $size ) {
			if ( mace_is_wp_image_size( $size ) ) {
				$width = get_option( "{$size}_size_w" );
			} elseif( isset( $_wp_additional_image_sizes[ $size ] ) ) {
				$width = $_wp_additional_image_sizes[ $size ]['width'];
			} else {
				$width = 0;
			}

			if ( $width >= $min_width ) {
				$sizes[] = $size;
			}
		}
	}

	return $sizes;
}


/**
 * Return watermark opacity (0-100)
 *
 * @return int
 */
function mace_watermarks_get_scale() {
	return (int) get_option( 'mace_watermarks_scale', 20 );
}

/**
 * Return watermark offset
 *
 * @return int
 */
function mace_watermarks_get_offset( $dim ) {
	$offset = get_option( 'mace_watermarks_offset', array( 'x' => 10 ) );

	return $offset[ $dim ];
}

/**
 * Check whether the backup is enabled
 *
 * @return bool
 */
function mace_watermarks_use_backup() {
	return 'standard' === apply_filters( 'mace_watermarks_backup', get_option( 'mace_watermarks_backup', 'standard' ) );
}

/**
 * Return image backup path
 *
 * @param string $orig_path         Original image path.
 *
 * @return string
 */
function mace_watermarks_get_backup_path( $orig_path ) {
	$path_parts     = pathinfo( $orig_path );
	$backup_path    = str_replace( $path_parts['filename'], $path_parts['filename'] . '-mace-bak', $orig_path );

	return $backup_path;
}

function mace_watermarks_action_url( $action, $ids ) {
	if ( ! is_array( $ids ) ) {
		$ids = array( $ids );
	}

	$url = admin_url( 'admin.php?page=' . mace_get_image_bulk_settings_page_id() );

	$url = add_query_arg( array(
		'mace-action'   => $action,
		'mace-ids'      => implode( ',', $ids ),
	), $url );

	return $url;
}

/**
 * Add a "Add/Remove Watermarks" link to the media row actions
 *
 * @param array  $actions       List of registered actions.
 * @param string $post          Attachment.
 *
 * @return array
 */
function mace_watermarks_media_row_actions( $actions, $post ) {
	if ( ! mace_is_attachment_image( $post ) || ! current_user_can( mace_get_capability() ) ) {
		return $actions;
	}

	$add_url    = mace_watermarks_action_url( 'add-watermarks', $post->ID );
	$remove_url = mace_watermarks_action_url( 'remove-watermarks', $post->ID );

	$status = mace_watermarks_attachment_excluded( $post->ID ) ? 'excluded' : '';

	$actions['mace_add_watermarks']     = sprintf( '<a href="%s" class="mace-add-watermarks" title="%s">%s</a>', esc_url( $add_url ), esc_attr__( 'Add watermarks for this image', 'mace' ), esc_html__( 'Add Watermarks', 'mace' ) );
	$actions['mace_remove_watermarks']  = sprintf( '<a href="%s" class="mace-remove-watermarks" title="%s">%s</a>', esc_url( $remove_url ), esc_attr__( 'Remove watermarks from this image', 'mace' ), esc_html__( 'Remove Watermarks', 'mace' ) );
	$actions['mace_include_watermarks'] = sprintf( '<a href="#" class="mace-include-watermarks" data-mace-id="%d" title="%s">%s</a>', absint( $post->ID ), esc_attr__( 'Allow watermarking for this image', 'mace' ), esc_html__( 'Allow Watermarking', 'mace' ) );
	$actions['mace_exclude_watermarks'] = sprintf( '<a href="#" class="mace-exclude-watermarks" data-mace-id="%d" data-mace-status="%s" title="%s">%s</a>', absint( $post->ID ), esc_attr( $status ), esc_attr__( 'Exclude this image from Watermarking', 'mace' ), esc_html__( 'Exclude from Watermarking', 'mace' ) );

	return $actions;
}

/**
 * Add new items to the Bulk Actions using Javascript
 */
function mace_watermarks_bulk_actions() {

	if ( ! current_user_can( mace_get_capability() ) ) {
		return;
	}
	?>
	<script type="text/javascript">
		(function($) {
			$(document).ready(function(){
				var add_option      = '<option value="mace_bulk_add_watermarks"><?php echo esc_attr(__('Add Watermarks', 'mace')); ?></option>';
				var remove_option   = '<option value="mace_bulk_remove_watermarks"><?php echo esc_attr(__('Remove Watermarks', 'mace')); ?></option>';

				$('select[name^="action"] option:last-child').before(add_option + remove_option);
			});
		})(jQuery);
	</script>
	<?php
}

/**
 * Handles the bulk actions
 */
function mace_watermarks_bulk_actions_handler() {
	$action  = $_REQUEST['action'];
	$action2 = $_REQUEST['action2'];
	$media   = isset( $_REQUEST['media'] ) ? $_REQUEST['media'] : array();

	$add_bulk_action    = 'mace_bulk_add_watermarks';
	$remove_bulk_action = 'mace_bulk_remove_watermarks';

	$bulk_actions = array( $add_bulk_action, $remove_bulk_action );

	if ( empty( $action ) && empty( $action2 ) ) {
		return;
	}

	if ( ! in_array( $action, $bulk_actions, true ) && ! in_array( $action2, $bulk_actions, true ) ) {
		return;
	}

	if ( empty( $media ) || ! is_array( $media ) ) {
		return;
	}

	check_admin_referer('bulk-media');
	$ids = implode( ',', array_map( 'absint', $_REQUEST['media'] ) );

	$watermarks_action = '-1' !== $action ? $action : $action2;

	$watermarks_action = str_replace( 'mace_bulk_', '', $watermarks_action );
	$watermarks_action = str_replace( '_', '-', $watermarks_action );

	wp_redirect( mace_watermarks_action_url( $watermarks_action, $ids ) );
	exit();
}

/**
 * Check whether attachment can be watermarked
 *
 * @param int $attachment_id        Attachment id.
 *
 * @return bool
 */
function mace_watermarks_attachment_excluded( $attachment_id ) {
	$excluded_flag_set = (bool) get_post_meta( $attachment_id, 'mace_do_not_watermark', true );

	$exclude_patterns = apply_filters( 'mace_watermarks_exclude_patterns', array( '/watermark/' ) );

	$filename = basename( get_attached_file( $attachment_id ) );
	$filename_excluded = false;

	foreach ( $exclude_patterns as $pattern ) {
		if ( preg_match( $pattern, $filename ) ) {
			$filename_excluded = true;
			break;
		}
	}

	return $excluded_flag_set || $filename_excluded;
}

/**
 * Exclude attachment from watermarking
 *
 * @param int $attachment_id        Attachment id.
 */
function mace_watermarks_exclude_attachment( $attachment_id ) {
	update_post_meta( $attachment_id, 'mace_do_not_watermark', true );
}

/**
 * Allow watermarking for attachment
 *
 * @param int $attachment_id        Attachment id.
 */
function mace_watermarks_include_attachment( $attachment_id ) {
	update_post_meta( $attachment_id, 'mace_do_not_watermark', false );
}

function mace_watermarks_enqueue_scripts( $hook ) {
	if ( 'upload.php' === $hook ) {
		$ver = mace_get_plugin_version();
		$url = trailingslashit( mace_get_plugin_url() );

		wp_enqueue_script( 'ma-admin-settings', $url . 'includes/watermarks/admin/js/watermarks.js', array( 'jquery' ), $ver, true );
	}
}

function mace_dont_watermark_new_watermark( $action ) {
	if ( 'media-form' === $action ) {
		$http_referer = $_SERVER['HTTP_REFERER'];

		// Prevent watermarking when upload from the "Watermarks S
		if ( false !== strpos( $http_referer, 'page=' . mace_get_watermarks_settings_page_id() ) ) {
			remove_filter( 'wp_generate_attachment_metadata', 'mace_generate_attachment_metadata', 10 );
		}
	}
}

function mace_delete_attachment( $post_id ) {
	$backup_path = mace_watermarks_get_backup_path( get_attached_file( $post_id ) );

	@ unlink( $backup_path );
}
