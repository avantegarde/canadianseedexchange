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

add_action( 'print_media_templates',    'mace_gallery_add_custom_settings' );
add_action( 'after_setup_theme', 		'mace_gallery_image_sizes' );
add_action( 'wp_enqueue_scripts',       'mace_gallery_enqueue_scripts' );
add_filter( 'post_gallery',             'mace_gallery_shortcode_custom_output', 10, 3 );

/**
 * Add image size.
 */
function mace_gallery_image_sizes() {
	add_image_size( 'mace-gallery-thumbnail', 180, 120, true );
}

/**
 * Add custom settings to WordPress built-in galley UI
 */
function mace_gallery_add_custom_settings() {
	?>
	<script type="text/html" id="tmpl-mace-gallery-setting">
		<h3 style="z-index: -1;">___________________________________________________________________________________________</h3>
		<h3><?php esc_html_e( 'Media Ace Settings', 'mace' ); ?></h3>

		<label class="setting">
			<span><?php esc_html_e( 'Gallery Type', 'mace' ); ?></span>
			<select data-setting="mace_type">
				<option value="standard"><?php esc_html_e( 'Standard', 'mace' ); ?></option>
				<option value="lightbox"><?php esc_html_e( 'Lightbox', 'mace' ); ?></option>
			</select>
		</label>
		<label class="setting">
			<span><?php esc_html_e( 'Gallery Title', 'mace' ); ?></span><br />
			<textarea data-setting="mace_title" rows="3" style="width: 100%;"></textarea>
		</label>
	</script>

	<script>
		(function($) {
			$(document).ready(function() {
				_.extend(wp.media.gallery.defaults, {
					mace_type: 'standard',
					mace_title: ''
				});

				wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
					template: function(view){
						return wp.media.template('gallery-settings')(view)
							+ wp.media.template('mace-gallery-setting')(view);
					}
				});
			});
		})(jQuery);
	</script>
	<?php

}

/**
 * Enqueue video playlist assets
 */
function mace_gallery_enqueue_scripts() {
	$ver = mace_get_plugin_version();
	$plugin_url = mace_get_plugin_url();

	wp_enqueue_style( 'mace-gallery', $plugin_url . 'includes/gallery/css/gallery.min.css' );
	wp_style_add_data( 'mace-gallery', 'rtl', 'replace' );

	wp_enqueue_script( 'mace-gallery', $plugin_url . 'includes/gallery/js/gallery.js', array( 'jquery' ), $ver, true );

	$data = array(
		'i18n' => array(
			'of' => __( 'of', 'mace' ),
		)
	);
	ob_start();
	mace_get_template_part( 'gallery-template' );
	$data['html'] = ob_get_clean();
	ob_start();
	mace_get_template_part( 'gallery-share' );
	$data['shares'] = ob_get_clean();
	wp_localize_script( 'mace-gallery', 'macegallery', wp_json_encode( $data ) );
}

function mace_gallery_shortcode_custom_output( $output, $attr, $instance ) {
	// Skip if standard gallery.
	if ( empty( $attr['mace_type'] ) || 'standard' === $attr['mace_type'] ) {
		return $output;
	}

	$atts = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'include'    => '',
	), $attr, 'gallery' );

	$query_args = array(
		'include'           => $atts['include'],
		'post_status'       => 'inherit',
		'post_type'         => 'attachment',
		'post_mime_type'    => 'image',
		'order'             => $atts['order'],
		'orderby'           => $atts['orderby']
	);

	$query_args = apply_filters( 'mace_gallery_query_args', $query_args );

	$attachments = get_posts( $query_args );

	global $mace_gallery_data;

	$mace_gallery_data = array(
		'attachments'   => $attachments,
		'attr'          => $attr,
		'instance'      => $instance,
	);

	ob_start();
	mace_get_template_part( 'gallery-teaser' );
	$output .= ob_get_clean();

	return $output;
}

/**
 * Return watermark image
 *
 * @return int
 */
function mace_gallery_get_logo() {
	return (int) get_option( 'mace_gallery_logo', '' );
}

/**
 * Return watermark image
 *
 * @return int
 */
function mace_gallery_get_logo_hdpi() {
	return (int) get_option( 'mace_gallery_logo_hdpi', '' );
}

/**
 * Return watermark image skin
 *
 * @return string
 */
function mace_gallery_get_skin() {
	return get_option( 'mace_gallery_skin', 'dark' );
}

/**
 * Return watermark image skin
 *
 * @return string
 */
function mace_gallery_get_thumbnails_visibillity() {
	return get_option( 'mace_gallery_thumbnails', 'show' );
}

/**
 * Get gallery content.
 *
 * @param array $mace_gallery_attachments  Mace gallery attachements array.
 * @return array
 */
function mace_get_gallery_content( $mace_gallery_attachments ) {
	$ad_inside = false;
	if ( mace_can_use_plugin( 'ad-ace/ad-ace.php' ) && (  adace_is_ad_slot( 'adace-mace-inside-gallery' ) ) ) {
		$ad_inside = mace_adace_gallery_get_ad_inside();
	}
	$images = array();
	foreach ( $mace_gallery_attachments as $index => $image ) {
		// Ad ad.
		if ( $ad_inside ) {
			$inject_at_position       = $ad_inside['position'] - 1;
			$repeat_after_x_positions = $ad_inside['repeat'];

			$start_injection = $index === $inject_at_position;
			$repeat_injection = $repeat_after_x_positions > 0 && $index > $inject_at_position && 0 === ( ( $index - $inject_at_position ) % $repeat_after_x_positions );
			$is_injection = $start_injection || $repeat_injection;

			if ( $is_injection ) {
				$images[] = array(
					'type' => 'ad',
					'html' => $ad_inside['html'],
				);
			}
		}

		// Add image.
		$image->ID;
		$thumbnail 				= wp_get_attachment_image_src( $image->ID, 'thumbnail' );
		$mace_gallery_thumbnail = wp_get_attachment_image_src( $image->ID, 'mace-gallery-thumbnail' );
		$full 					= wp_get_attachment_image_src( $image->ID, 'full' );
		$images[] = array(
			'type'			=> 'image',
			'id'			=> $image->ID,
			'title' 		=> $image->post_title,
			'thumbnail' 	=> $thumbnail[0],
			'3-2-thumbnail' => $mace_gallery_thumbnail[0],
			'full' 			=> $full[0],
		);
	}

	return $images;
}

/**
 * Get gallery counter.
 *
 * @return int
 */
function mace_get_gallery_counter() {
	global $gallery_counter;
	if ( ! $gallery_counter ) {
		$gallery_counter = 1;
	} else {
		$gallery_counter += 1;
	}
	return $gallery_counter;
}
