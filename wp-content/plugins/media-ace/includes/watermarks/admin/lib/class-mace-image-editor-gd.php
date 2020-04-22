<?php
/**
 * MediaAce GD Image Editor
 *
 * @package media-ace
 * @subpackage Classes
 */

require_once( ABSPATH . WPINC . '/class-wp-image-editor.php' );
require_once( ABSPATH . WPINC . '/class-wp-image-editor-gd.php' );

/**
 * MediaAce Image Editor Class for Image Manipulation through GD
 */
class Mace_Image_Editor_GD extends WP_Image_Editor_GD {
	/**
	 * @param resource $watermark           An image resource.
	 * @param array    $args                Arguments.
	 *
	 * @return bool|WP_Error                True if loaded successfully, WP_Error on failure.
	 */
	public function add_watermark( $watermark, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'position'  => 'right_bottom',
			'offset_x'  => 10,
			'opacity'   => 80,  // 0 (fully transparent) - 100 (fully opaque).
		) );

		/**
		 * The resource of the image to edit is stored in
		 * $this->image, after $this->load() was called
		 */
		$loaded = $this->load();

		if ( is_wp_error( $loaded ) ) {
			return $loaded;
		}

		list( $dst_x, $dst_y ) = $this->get_watermark_position( $watermark, $args );

		// Copy the watermark image onto the image.
		$copied =  imagecopymerge(
			$this->image,
			$watermark,
			$dst_x,
			$dst_y,
			0,
			0,
			imagesx( $watermark ),
			imagesy( $watermark ),
			$args['opacity']
		);

		if ( ! $copied ) {
			return new WP_Error( 'mace_failed', esc_html__( 'Watermark not applied.', 'mace' ) );
		}

		return $copied;
	}

	public function get_image() {
		return $this->image;
	}

	protected function get_watermark_position( $watermark, $args ) {
		$image_width      = imagesx( $this->image );
		$image_height     = imagesy( $this->image );
		$watermark_width  = imagesx( $watermark );
		$watermark_height = imagesy( $watermark );

		$offset_x_px = round( $image_width * $args['offset_x'] / 100 );
		$offset_y_px = $offset_x_px;

		switch( $args['position'] ) {
			case 'left_top':
				$dst_x = $offset_x_px;
				$dst_y = $offset_y_px;
				break;

			case 'right_top':
				$dst_x = $image_width - $watermark_width - $offset_x_px;
				$dst_y = $offset_y_px;
				break;

			case 'left_bottom':
				$dst_x = $offset_x_px;
				$dst_y = $image_height - $watermark_height - $offset_y_px;
				break;

			case 'center_center':
				$dst_x = round( ( $image_width - $watermark_width ) / 2 );
				$dst_y = round( ( $image_height - $watermark_height ) / 2 );;
				break;

			case 'right_bottom':
			default:
				$dst_x = $image_width - $watermark_width - $offset_x_px;
				$dst_y = $image_height - $watermark_height - $offset_y_px;
		}

		return array( $dst_x, $dst_y );
	}

	/**
	 * Override parent to keep gif|png transparency while resizing
	 *
	 * @param int $max_w
	 * @param int $max_h
	 * @param bool|array $crop
	 *
	 * @return resource|WP_Error
	 */
	protected function _resize( $max_w, $max_h, $crop = false ) {
		$dims = image_resize_dimensions( $this->size['width'], $this->size['height'], $max_w, $max_h, $crop );

		if ( ! $dims ) {
			return new WP_Error( 'error_getting_dimensions', __('Could not calculate resized image dimensions'), $this->file );
		}

		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

		$resized = wp_imagecreatetruecolor( $dst_w, $dst_h );

		// Preserves transparency.
		if( in_array( $this->mime_type, array( 'image/png', 'image/gif' ), true ) ) {
			imagecolortransparent( $resized, imagecolorallocatealpha( $resized, 0, 0, 0, 127 ) );
			imagealphablending( $resized, false );
			imagesavealpha( $resized, true );
		}

		imagecopyresampled( $resized, $this->image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

		if ( is_resource( $resized ) ) {
			$this->update_size( $dst_w, $dst_h );
			return $resized;
		}

		return new WP_Error( 'image_resize_error', __('Image resize failed.'), $this->file );
	}
}
