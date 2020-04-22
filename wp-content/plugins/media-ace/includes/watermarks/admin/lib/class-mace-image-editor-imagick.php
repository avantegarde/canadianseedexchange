<?php
/**
 * MediaAce Imagick Image Editor
 *
 * @package media-ace
 * @subpackage Classes
 */

require_once( ABSPATH . WPINC . '/class-wp-image-editor.php' );
require_once( ABSPATH . WPINC . '/class-wp-image-editor-imagick.php' );

/**
 * MediaAce Image Editor Class for Image Manipulation through Imagick PHP Module
 */
class Mace_Image_Editor_Imagick extends WP_Image_Editor_Imagick {
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
		$copied = $this->image ;
		if ( $args['opacity'] > 0 ) {
			$watermark->evaluateImage( Imagick::EVALUATE_DIVIDE, 100 / $args['opacity'], Imagick::CHANNEL_ALPHA );
			$copied->compositeImage( $watermark, imagick::COMPOSITE_DEFAULT, $dst_x, $dst_y );
		}
		if ( ! $copied ) {
			return new WP_Error( 'mace_failed', esc_html__( 'Watermark not applied.', 'mace' ) );
		}
		return $copied;
	}

	public function get_image() {
		return $this->image;
	}

	protected function get_watermark_position( $watermark, $args ) {
		$image = $this->image;
		$image_width      = $image->getImageWidth();
		$image_height     = $image->getImageHeight();
		$watermark_width  = $watermark->getImageWidth();
		$watermark_height = $watermark->getImageHeight();

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
}
