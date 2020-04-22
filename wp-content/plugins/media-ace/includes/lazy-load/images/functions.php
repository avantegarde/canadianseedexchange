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

add_filter( 'wp_get_attachment_image_attributes',   'mace_lazy_load_attachment', 10, 3 );
add_filter( 'the_content',                          'mace_lazy_load_content_image' );
add_action( 'wp_head',                              'mace_lazy_load_inline_styles' );
add_filter( 'wp_kses_allowed_html',                 'mace_allow_extra_html_attributes' );
add_filter( 'mace_lazy_load_image',                 'mace_disable_lazy_load_on_feed' );
add_filter( 'get_avatar',                           'mace_lazy_load_avatar', 9999, 6 );


function mace_allow_extra_html_attributes( $allowedposttags ) {
	$allowedposttags['img']['data-src']     = true;
	$allowedposttags['img']['data-expand']  = true;
	$allowedposttags['img']['data-srcset']  = true;
	$allowedposttags['img']['data-sizes']   = true;

	return $allowedposttags;
}

function mace_lazy_load_attachment( $attr, $attachment, $size ) {
	if ( ! apply_filters( 'mace_lazy_load_image', true ) || is_embed() ) {
		return $attr;
	}

	if ( ! apply_filters( 'mace_lazy_load_attachment', true, $attr, $attachment, $size ) ) {
		return $attr;
	}

	$html_class = isset( $attr['class'] ) ? $attr['class'] : '';

	if ( isset( $attr['src'] ) && mace_can_add_lazy_load_class( $html_class ) ) {
		$attr['class']      .= ' ' . mace_get_lazy_load_class();
		$attr['data-src']   =  $attr['src'];

		if ( mace_lazy_load_images_unveilling_effect_enabled() ) {
			$attr['data-expand'] = '600';
		}

		$attr['src'] = mace_get_plugin_url() . 'includes/lazy-load/assets/images/blank.png';

		if ( isset( $attr['srcset'] ) ) {
			$attr['data-srcset'] = $attr['srcset'];
			unset($attr['srcset']);
		}

		if ( isset( $attr['sizes'] ) ) {
			$attr['data-sizes'] = $attr['sizes'];
			unset($attr['sizes']);
		}
	}

	return $attr;
}

function mace_lazy_load_content_image( $content ) {
	if ( ! apply_filters( 'mace_lazy_load_image', true || is_embed() ) ) {
		return $content;
	}

	if ( ! apply_filters( 'mace_lazy_load_content_image', true, $content ) ) {
		return $content;
	}

	// Find img tags.
	if ( preg_match_all('/<img[^>]+>/i', $content, $matches) ) {
		$lazy_class = mace_get_lazy_load_class();

		foreach( $matches[0] as $img_tag ) {

			// Process only if the src attribute exists.
			if ( preg_match('/src=["\']([^"\']+)["\']/i', $img_tag ) ) {
				$new_img_tag = $img_tag;

				// Html class not set.
				$html_class = '';

				// Extract html class.
				if ( preg_match('/class=["\']([^"\']+)["\']/i', $new_img_tag, $class_matches ) ) {
					$html_class = $class_matches[1];
				}

				if ( ! mace_can_add_lazy_load_class( $html_class ) ) {
					continue;
				}

				// Extract width attribute value.
				$width = 1;
				if ( preg_match('/width=["\']([^"\']+)["\']/i', $new_img_tag, $matches ) ) {
					$width = (int) $matches[1];
				}

				// Extract height attribute value.
				$height = 1;
				if ( preg_match('/height=["\']([^"\']+)["\']/i', $new_img_tag, $matches ) ) {
					$height = (int) $matches[1];
				}

				// Thanks to this placeholder, browser will reserve correct space (blank) for future image.
				$placeholder = Mace_Lazy_Load::get_instance()->get_placeholder_src( $width, $height );

				$new_img_tag = str_replace(
					array(
						'src="',
						'src=\'',
						'srcset=',
						'sizes=',
						'class="',
						'class=\'',
					),
					array(
						'src="' . $placeholder . '" data-src="',
						'src=\'' . $placeholder . '\' data-src=\'',
						'data-srcset=',
						'data-sizes=',
						'class="' . $lazy_class . ' ',
						'class=\'' . $lazy_class . ' ',
					),
					$new_img_tag
				);

				// class attribute was not replaced. We need to add it.
				if ( false === strpos( $new_img_tag, 'class=' ) ) {
					$new_img_tag = str_replace( '<img', '<img class="' . $lazy_class . '"', $new_img_tag );
				}

				// Add data-expand attribute if enabled.
				if ( mace_lazy_load_images_unveilling_effect_enabled() ) {
					$new_img_tag = str_replace( '<img', '<img data-expand="600"', $new_img_tag );
				}

				$content = str_replace( $img_tag, $new_img_tag, $content );
			}
		}
	}

	return $content;
}

function mace_get_lazy_load_disable_class() {
	return apply_filters( 'mace_lazy_load_disable_class', 'g1-no-lazyload' );
}

function mace_can_add_lazy_load_class( $html_class ) {
	$lazy_class     = mace_get_lazy_load_class();
	$disable_class  = mace_get_lazy_load_disable_class();

	// Bail if $lazy_class class is already added.
	if ( false !== strpos( $html_class, $lazy_class ) ) {
		return false;
	}

	// Bail if $disable_class class is set.
	if ( false !== strpos( $html_class, $disable_class ) ) {
		return false;
	}

	return apply_filters( 'mace_can_add_lazy_load_class', true, $html_class );
}

function mace_lazy_load_inline_styles() {
	if ( ! mace_lazy_load_images_unveilling_effect_enabled() ) {
		return;
	}
	?>
	<style>
		.lazyload, .lazyloading {
			opacity: 0;
		}
		.lazyloaded {
			opacity: 1;
		}
		.lazyload,
		.lazyloading,
		.lazyloaded {
			transition: opacity 0.175s ease-in-out;
		}

		iframe.lazyloading {
			opacity: 1;
			transition: opacity 0.375s ease-in-out;
			background: #f2f2f2 no-repeat center;
		}
		iframe.lazyloaded {
			opacity: 1;
		}
	</style>
	<?php
}

function mace_lazy_load_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {
	if ( mace_get_lazy_load_images() ) {
		$avatar = mace_lazy_load_content_image( $avatar );
	}

	return $avatar;
}

add_filter( 'bp_core_fetch_avatar', 'mace_bp_core_fetch_avatar', 99, 9 );
function mace_bp_core_fetch_avatar( $avatar, $params, $item_id, $avatar_dir, $html_css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir ) {
	if ( mace_get_lazy_load_images() ) {
		$avatar = mace_lazy_load_content_image( $avatar );
	}

	return $avatar;
}



add_filter( 'mycred_badge_image', 'mace_lazy_load_mycred_badge_image', 10, 3 );
function mace_lazy_load_mycred_badge_image( $html, $image, $badge ) {
	if ( mace_get_lazy_load_images() ) {
		$html = str_replace( '<img ', '<img loading="lazy" ', $html );
	}

	return $html;
}
