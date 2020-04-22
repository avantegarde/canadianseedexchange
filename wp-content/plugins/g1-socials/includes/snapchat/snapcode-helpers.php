<?php
/**
 * Snapcode things
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package G1_Socials Theme
 */


// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get Snapcode
 *
 * @param string $username Username to be displayed.
 * @param string $user_id User id on Snapchat.
 * @param string $useravatar Link to avatar.
 * @param string $useravatar_hdpi Link to avatar HDPI.
 *
 * @return string HTML.
 *
 * @todo Don't use g1-gamma, g1-gamma-1st classes, override template part via theme instead.
 */
function g1_socials_get_snapcode( $username = '', $user_id = '', $useravatar = '', $useravatar_hdpi = '' ) {
	// Check if username is empty.
	if ( empty( $username ) || empty( $user_id ) || empty( $useravatar ) ) {
		return false;
	}
	// Get user snapcode.
	$user_snapcode = g1_socials_get_user_snapcode_svg( $user_id );
	$user_snapcode_url = 'https://snapchat.com/add/' . $user_id;
	// If failed to get return false.
	if ( ! $user_snapcode ) {
		return false;
	}
	$snapcode_size = g1_socials_get_snapcode_size();
	ob_start();
	?>
	<div class="g1-snapchat">
		<a class="g1-snapchat-code" href="<?php echo( esc_url( $user_snapcode_url ) ); ?>" target="_blank" rel="nofollow">
			<?php
			$img = sprintf(
				'<img class="g1-snapchat-code-dots" width="%d" height="%d" src="%s" alt="%s">',
				$snapcode_size,
				$snapcode_size,
				esc_url( $user_snapcode ),
				esc_attr__( 'User snapcode.', 'g1_socials' )
			);

			echo apply_filters( 'g1_socials_snapchat_code_dots', $img );

			$img = sprintf(
				'<img class="g1-snapchat-code-avatar" src="%s" srcset="%s" alt="" />',
				esc_url( $useravatar ),
				empty( $useravatar_hdpi ) ? esc_url( $useravatar ) .' 1x' : esc_url( $useravatar_hdpi ) . ' 2x, ' . esc_url( $useravatar ) . ' 1x'
			);

			echo apply_filters( 'g1_socials_snapchat_code_avatar', $img );
			?>
		</a>
		<h3 class="entry-title g1-gamma g1-gamma-1st g1-snapchat-username"><a href="<?php echo( esc_url( $user_snapcode_url ) ); ?>" target="_blank" rel="nofollow"><?php echo ( esc_html( $username ) ); ?></a></h3>
		<p class="g1-snapchat-userid g1-typography-s"><a href="<?php echo( esc_url( $user_snapcode_url ) ); ?>" target="_blank" rel="nofollow" class="snapcode-userid-link">&#64;<?php echo ( esc_html( $user_id ) ); ?></a></p>
	</div>
	<?php
	$snapcode_out = ob_get_clean();
	return apply_filters( 'g1_socials_get_snapcode', $snapcode_out );
}

/**
 * Get Snapcode SVG Size
 *
 * @return int Snapcode size.
 */
function g1_socials_get_snapcode_size() {
	return apply_filters( 'g1_socials_snapcode_size', 512 );
}

/**
 * Get Snapcode Remote URL
 *
 * @param string $user_id Snapcode user id.
 * @return url|false Snapcode remote url or false.
 */
function g1_socials_get_snapcode_remote_url( $user_id = '' ) {
	// Check if user id is provided.
	if ( empty( $user_id ) ) {
		return false;
	}
	$snapcode_remote_args = array(
		'username' => $user_id,
		'type'     => 'SVG',
		'size'     => g1_socials_get_snapcode_size(),
	);
	$snapcode_remote_url = add_query_arg( $snapcode_remote_args, 'https://snapcodes.herokuapp.com/snapcode.php' );
	return apply_filters( 'g1_socials_snapcode_remote_url', $snapcode_remote_url );
}

/**
 * Get Snapcode Remote SVG, and parse it
 *
 * @param string $user_id Snapcode user id.
 * @return string|false Returns svg string or false.
 */
function g1_socials_get_snapcode_remote_svg_content( $user_id = '' ) {
	// Check if user id is provided.
	if ( empty( $user_id ) ) {
		return false;
	}
	// If we dont have cached file build url.
	$snapcode_remote_url = g1_socials_get_snapcode_remote_url( $user_id );
	// Get remote.
	$snapcode_remote_responce = wp_remote_get( $snapcode_remote_url );
	// Check for error.
	if ( is_wp_error( $snapcode_remote_responce ) ) {
		return false;
	}
	// Parse remote file.
	$svg_file = wp_remote_retrieve_body( $snapcode_remote_responce );
	// Check for error.
	if ( is_wp_error( $svg_file ) ) {
		return false;
	}
	// Parsing svg, based on https://github.com/jusleg/snaptag.
	// First add id to svg element.
	$svg_file = str_replace( '#FFFC00', '#fffd88', $svg_file );
	// Change id.
	$svg_file = str_replace( 'id="tag"', 'id="snapcode-' . sanitize_title( $user_id ) . '" ', $svg_file );
	// Fix sizes.
	$svg_file = str_replace( '=' . g1_socials_get_snapcode_size() . 'px' , '="' . g1_socials_get_snapcode_size() . 'px"', $svg_file );
	// Remove last path, ghost.
	$path_start = strpos( $svg_file, '<path id="ghost"' );
	$svg_end = strpos( $svg_file, '</svg>' );
	$svg_file = substr( $svg_file, 0, $path_start ) . substr( $svg_file, $svg_end );
	$svg_file = trim( $svg_file );
	// Applying filters to file before save.
	return apply_filters( 'g1_socials_snapcode_svg_file_content', $svg_file );
}

/**
 * Get Snapcode dir path
 *
 * @return string|false path to snapcodes cache dir or false
 */
function g1_socials_get_snapcode_cache_dir() {
	$upload_dir = wp_upload_dir();
	$upload_dir = trailingslashit( $upload_dir['basedir'] );
	$snapcode_cache_dir = trailingslashit( $upload_dir . 'g1_socials-snapcodes' );
	if ( wp_mkdir_p( $snapcode_cache_dir ) ) {
		return apply_filters( 'g1_socials_snapcode_cache_dir', $snapcode_cache_dir );
	} else {
		return false;
	}
}

/**
 * Get Snapcode dir path
 *
 * @return string|false Path to snapcodes cache dir or false.
 */
function g1_socials_get_snapcode_cache_dir_url() {
	$upload_dir = wp_upload_dir();
	$upload_dir = trailingslashit( $upload_dir['baseurl'] );
	$snapcode_cache_url = trailingslashit( $upload_dir . 'g1_socials-snapcodes' );
	return apply_filters( 'g1_socials_snapcode_cache_url', $snapcode_cache_url );
}

/**
 * Whether or not caching directory of snapcodes is writable.
 *
 * @return bool Is cache dir writable.
 */
function g1_socials_is_snapcode_cache_dir_writable() {
	return wp_is_writable( g1_socials_get_snapcode_cache_dir() );
}

/**
 * Save Snapcode SVG
 *
 * @param string $user_id Snapcode user id.
 * @param string $svg_file_content Svg file content.
 * @return bool If save was successfull.
 */
function g1_socials_save_snapcode_remote_svg( $user_id = '', $svg_file_content = '' ) {
	// Check if user id is provided.
	if ( empty( $user_id ) ) {
		return false;
	}
	// Check if svg is provided.
	if ( empty( $svg_file_content ) ) {
		return false;
	}
	// Check if cache dir is writable.
	if ( ! g1_socials_is_snapcode_cache_dir_writable() ) {
		return false;
	}
	// Load WP_Filesystem().
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	WP_Filesystem();
	// Load $wp_filesystem.
	global $wp_filesystem;
	if ( ! $wp_filesystem ) {
		return false;
	}
	// Build file name.
	$snapcode_filename = trailingslashit( g1_socials_get_snapcode_cache_dir() ) . sanitize_title( 'snapcode-' . $user_id . '-' . g1_socials_get_snapcode_size() ) . '.svg';
	// Check if its already here.
	if ( $wp_filesystem->exists( $snapcode_filename ) ) {
		$wp_filesystem->delete( $snapcode_filename );
	}
	// Lets try saving.
	if ( $wp_filesystem->put_contents( $snapcode_filename, $svg_file_content, FS_CHMOD_FILE ) ) {
		return true;
	}
	return false;
}

/**
 * Look for user SVG
 *
 * @param string $user_id Snapcode user id.
 * @return string|bool url to svg or false if dont exist
 */
function g1_socials_find_user_snapcode_svg( $user_id ) {
	// Check if user id is provided.
	if ( empty( $user_id ) ) {
		return false;
	}
	$snapcode_file = sanitize_title( 'snapcode-' . $user_id . '-' . g1_socials_get_snapcode_size() ) . '.svg';
	$snapcode_filename = trailingslashit( g1_socials_get_snapcode_cache_dir() ) . $snapcode_file;
	if ( file_exists( $snapcode_filename ) ) {
		return g1_socials_get_snapcode_cache_dir_url() . $snapcode_file;
	} else {
		return false;
	}
}

/**
 * Get Snapcode SVG
 *
 * @param string $user_id Snapcode id of some sort.
 */
function g1_socials_get_user_snapcode_svg( $user_id = '' ) {
	// Check if user id is provided.
	if ( empty( $user_id ) ) {
		return false;
	}
	// Make sure that @ was removed - this is for url and file.
	$user_id = str_replace( '@', '', $user_id );
	// Look for svg in cache folder.
	$user_snapcode = g1_socials_find_user_snapcode_svg( $user_id );
	if ( $user_snapcode ) {
		return $user_snapcode;
	} else {
		$svg_file_content = g1_socials_get_snapcode_remote_svg_content( $user_id );
		if ( $svg_file_content ) {
			if ( g1_socials_save_snapcode_remote_svg( $user_id, $svg_file_content ) ) {
				return g1_socials_find_user_snapcode_svg( $user_id );
			}
		}
	}
	return false;
}
