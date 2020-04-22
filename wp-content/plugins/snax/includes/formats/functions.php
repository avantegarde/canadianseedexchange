<?php
/**
 * Snax Formats Common Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}



class Snax_Format {
	protected $id;
	protected $labels;
	protected $description;
	protected $position;
	protected $url;
	protected $learn_more_url;


	function __construct( $id, $args) {
		$this->id = $id;

		$this->labels = $args['labels'];
		$this->description = $args['description'];
		$this->position = $args['position'];
		$this->url = $args['url'];
		$this->learn_more_url = $args['learn_more_url'];
	}

	public function get_id() {
		return $this->id;
	}

	public function get_description() {
		return $this->description;
	}

	public function get_url() {
		return $this->url;
	}

	public function get_learn_more_url() {
		return $this->learn_more_url;
	}

	public function get_labels() {
		return $this->labels;
	}

	public function get_mycred_points_for_publishing() {
		$result = 0;

		if ( snax_can_use_plugin( 'mycred/mycred.php' ) ) {
			$hooks = get_option( 'mycred_pref_hooks' );

			if ( is_array( $hooks['active'] ) && in_array( 'snax_format', $hooks['active'] ) ) {
				$result = (int) $hooks['hook_prefs']['snax_format'][ $this->get_id() . '_creds' ];
			}
		}

		return $result;
	}

	public function has_mycred_points_for_publishing() {
		return $this->get_mycred_points_for_publishing() ? true : false;
	}
}

/**
 * Return HTML for media to place inside post content
 *
 * @param string $url           Media url.
 * @param string $source        Optional. Media source.
 * @param string $format        Optional. Media format (audio, video, image). If not set, plain url will be used.
 *
 * @return string
 */
function snax_get_content_media_html( $url, $source = '', $format = '' ) {
	$html = $url;

	if ( ! empty( $source ) ) {
		$html .= "\n\n" . sprintf( '<a href="%s" target="_blank">%s</a>', esc_url_raw( $source ), esc_html__( 'source', 'snax' ) );
	}

	return apply_filters( 'snax_content_media_html', $html, $url, $source, $format );
}

/**
 * Convert element like img, embeds into figure.
 *
 * @param string $content		Input text.
 *
 * @return array				Content and media to assign.
 */
function snax_convert_format_elements( $content ) {
	// Images.
	$img_pattern 		= '/<img[^>]+>/';
	$img_attrs_pattern	= '/(data-snax-id|data-snax-source|alt)="([^"]*)"/i';
	$media_ids 			= array(); // Store media for further assignment.

	// Find images.
	if ( preg_match_all( $img_pattern, $content, $img_matches ) ) {
		foreach ( $img_matches[0] as $img_tag ) {
			$media_id	= '';
			$source 	= '';
			$alt 		= '';

			// Find attributes.
			if ( preg_match_all( $img_attrs_pattern, $img_tag, $img_attrs ) ) {
				$tags = $img_attrs[1];
				$values = $img_attrs[2];

				foreach ( $tags as $index => $tag ) {
					if ( 'data-snax-id' === $tag ) {
						$media_id = $values[ $index ];
					}

					if ( 'data-snax-source' === $tag ) {
						$source = $values[ $index ];
					}

					if ( 'alt' === $tag ) {
						$alt = $values[ $index ];
					}
				}
			}

			// If image is not valid, we will just strip it (replacing with empty string).
			$figure = '';

			if ( $media_id ) {
				$attachment = get_post( $media_id );

				$is_attachment      = $attachment && ( 'attachment' === $attachment->post_type );
				$is_owned_by_user   = $attachment && ( (int) get_current_user_id() === (int) $attachment->post_author );

				$attachment_valid = $is_attachment && $is_owned_by_user;

				// Get only user attachments.
				$attachments = get_posts( array(
					'p' 		=> $media_id,				// Match id?
					'post_type' => 'attachment',			// Is attachment?
					'author' 	=> get_current_user_id(),	// Belongs to user?
				) );

				if ( $attachment_valid ) {
					// Store to use it further, when parent post will be created.
					$media_ids[] = $attachment->ID;

					// Build final markup.
					$img = wp_get_attachment_image( $media_id, 'large' );

					$img = str_replace( 'class="', 'class="aligncenter snax-figure-content ', $img );
					$img = str_replace( ' src=', ' alt="'. $alt .'" src=', $img );

					global $content_width;

					$figure .= '[caption class="snax-figure" align="aligncenter" width="' . intval( $content_width ) . '"]';
					$figure .= $img;

					if ( $source ) {
						$figure .= sprintf( '<a class="snax-figure-source" href="%s" rel="nofollow" target="_blank">%s</a>', esc_url( $source ), esc_url( $source ) );
					}
					$figure .= '[/caption]';
				}
			}

			// Replace image with figure.
			$content = str_replace( $img_tag, $figure, $content );
		}
	}

	return array(
		'content' 	=> $content,		// Converted content.
		'media_ids' => $media_ids,		// Medias that were used in content.
	);
}

/**
 * Return all registered formats
 *
 * @return array
 */
function snax_get_formats( $fields = 'array' ) {
	$format_var = snax_get_url_var( 'format' );

	$formats = array();

	$formats['text'] = array(
		'labels'		=> array(
			'name' 			=> snax_text_get_singular_name( __( 'Story', 'snax' ) ),
			'add_new'		=> snax_text_get_add_new( __( 'Story', 'snax' ) ),
		),
		'description'	=> snax_text_get_description( _x( 'Mix text with images and embeds', 'story format description', 'snax' ) ),
		'position'		=> 10,
		'url'           => add_query_arg( $format_var, 'text' ),
		'learn_more_url'=> '/',
	);

	$formats['image'] = array(
		'labels'		=> array(
			'name' 			=> snax_image_get_singular_name( __( 'Image', 'snax' ) ),
			'add_new'		=> snax_image_get_add_new( __( 'Image', 'snax' ) ),
		),
		'description'	=> snax_image_get_description( _x( 'JPG, PNG or GIF', 'image format description', 'snax' ) ),
		'position'		=> 20,
		'url'           => add_query_arg( $format_var, 'image' ),
		'learn_more_url'=> '/',
	);

	$formats['audio'] = array(
		'labels'		=> array(
			'name' 			=> snax_audio_get_singular_name( __( 'Audio', 'snax' ) ),
			'add_new'		=> snax_audio_get_add_new( __( 'Audio', 'snax' ) ),
		),
		'description'	=>  snax_audio_get_description( _x( 'MP3 or SoundCloud embed, MixCloud, etc.', 'audio format description', 'snax' ) ),
		'position'		=> 20,
		'url'           => add_query_arg( $format_var, 'audio' ),
		'learn_more_url'=> '/',
	);

	$formats['video'] = array(
		'labels'		=> array(
			'name' 			=> snax_video_get_singular_name( __( 'Video', 'snax' ) ),
			'add_new'		=> snax_video_get_add_new( __( 'Video', 'snax' ) ),
		),
		'description'	=>  snax_video_get_description( _x( 'MP4 or YouTube embed, Vimeo, Dailymotion, etc.', 'video format description', 'snax' ) ),
		'position'		=> 20,
		'url'           => add_query_arg( $format_var, 'video' ),
		'learn_more_url'=> '/',
	);

	$formats['gallery'] = array(
		'labels'		=> array(
			'name' 			=> snax_gallery_get_singular_name( __( 'Gallery', 'snax' ) ),
			'add_new'		=> snax_gallery_get_add_new( __( 'Gallery', 'snax' ) ),
		),
		'description'	=> snax_gallery_get_description( _x( 'A collection of images', 'gallery format description', 'snax' ) ),
		'position'		=> 30,
		'url'           => add_query_arg( $format_var, 'gallery' ),
		'learn_more_url'=> '/',
	);

	$formats['embed'] = array(
		'labels'		=> array(
			'name' 			=> snax_embed_get_singular_name( __( 'Embed', 'snax' ) ),
			'add_new'		=> snax_embed_get_add_new( __( 'Embed', 'snax' ) ),
		),
		'description'	=> snax_embed_get_description( _x( 'Facebook post, Twitter status, etc.', 'embed format description', 'snax' ) ),
		'position'		=> 40,
		'url'           => add_query_arg( $format_var, 'embed' ),
		'learn_more_url'=> '/',
	);

	$formats['list'] = array(
		'labels'		=> array(
			'name' 			=> snax_list_get_singular_name( __( 'Open list', 'snax' ) ),
			'add_new'		=> snax_list_get_add_new( __( 'Open List', 'snax' ) ),
		),
		'description'	=> snax_list_get_description( _x( 'Everyone can submit new list items and vote up for the best submission', 'open list format description', 'snax' ) ),
		'position'		=> 50,
		'url'           => add_query_arg( $format_var, 'list' ),
		'learn_more_url'=> '/',
	);

	$formats['ranked_list'] = array(
		'labels'		=> array(
			'name' 			=> snax_ranked_list_get_singular_name( __( 'Ranked list', 'snax' ) ),
			'add_new'		=> snax_ranked_list_get_add_new( __( 'Ranked List', 'snax' ) ),
		),
		'description'	=> snax_ranked_list_get_description( _x( 'Everyone can vote up for the best list item', 'ranked list format description', 'snax' ) ),
		'position'		=> 60,
		'url'           => add_query_arg( array(
			$format_var => 'list',
			'type' 		=> 'ranked',
		) ),
		'learn_more_url'=> '/',
	);

	$formats['classic_list'] = array(
		'labels'		=> array(
			'name' 			=> snax_classic_list_get_singular_name( __( 'Classic list', 'snax' ) ),
			'add_new'		=> snax_classic_list_get_add_new( __( 'Classic List', 'snax' ) ),
		),
		'description'	=> snax_classic_list_get_description( _x( 'A list-based article', 'classic list format description', 'snax' ) ),
		'position'		=> 70,
		'url'           => add_query_arg( array(
			$format_var => 'list',
			'type' 		=> 'classic',
		) ),
		'learn_more_url'=> '/',
	);

	$formats['meme'] = array(
		'labels'		=> array(
			'name' 			=> snax_meme_get_singular_name( __( 'Meme', 'snax' ) ),
			'add_new'		=> snax_meme_get_add_new( __( 'Meme', 'snax' ) ),
		),
		'description'	=> snax_meme_get_description( _x( 'Create a funny pic', 'meme format description', 'snax' ) ),
		'position'		=> 80,
		'url'           => add_query_arg( $format_var, 'meme' ),
		'learn_more_url'=> '/',
	);

	$formats['trivia_quiz'] = array(
		'labels'		=> array(
			'name' 			=> snax_trivia_quiz_get_singular_name( __( 'Trivia quiz', 'snax' ) ),
			'add_new'		=> snax_trivia_quiz_get_add_new( __( 'Trivia Quiz', 'snax' ) ),
		),
		'description'	=> snax_trivia_quiz_get_description( _x( 'What do you know about ...?', 'trivia quiz format description', 'snax' ) ),
		'position'		=> 90,
		'url'           => add_query_arg( $format_var, 'trivia_quiz' ),
		'learn_more_url'=> '/',
	);

	$formats['personality_quiz'] = array(
		'labels'		=> array(
			'name' 			=> snax_personality_quiz_get_singular_name( __( 'Personality quiz', 'snax' ) ),
			'add_new'		=> snax_personality_quiz_get_add_new( __( 'Personality Quiz', 'snax' ) ),
		),
		'description'	=> snax_personality_quiz_get_description( _x( 'What type of person are you?', 'personality quiz format description', 'snax' ) ),
		'position'		=> 100,
		'url'           => add_query_arg( $format_var, 'personality_quiz' ),
		'learn_more_url'=> '/',
	);

	$formats['classic_poll'] = array(
		'labels'		=> array(
			'name' 			=> snax_classic_poll_get_singular_name( __( 'Poll', 'snax' ) ),
			'add_new'		=> snax_classic_poll_get_add_new( __( 'Poll', 'snax' ) ),
		),
		'description'	=> snax_classic_poll_get_description( _x( 'One or multiple questions about a subject or person', 'classic poll format description', 'snax' ) ),
		'position'		=> 110,
		'url'           => add_query_arg( $format_var, 'classic_poll' ),
		'learn_more_url'=> '/',
	);

	$formats['versus_poll'] = array(
		'labels'		=> array(
			'name' 			=> snax_versus_poll_get_singular_name( snax_get_versus_poll_label() ),
			'add_new'		=> snax_versus_poll_get_add_new( snax_get_versus_poll_label() ),
		),
		'description'	=> snax_versus_poll_get_description( _x( 'A poll where each question has two competing answers', 'versus poll format description', 'snax' ) ),
		'position'		=> 120,
		'url'           => add_query_arg( $format_var, 'versus_poll' ),
		'learn_more_url'=> '/',
	);

	$formats['binary_poll'] = array(
		'labels'		=> array(
			'name' 			=> snax_binary_poll_get_singular_name( __( 'Hot or Not', 'snax' ) ),
			'add_new'		=> snax_binary_poll_get_add_new( __( 'Hot or Not', 'snax' ) ),
		),
		'description'	=> snax_binary_poll_get_description( _x( 'A poll where each question has two opposite answers', 'hot ot nor format description', 'snax' ) ),
		'position'		=> 130,
		'url'           => add_query_arg( $format_var, 'binary_poll' ),
		'learn_more_url'=> '/',
	);

	$formats = apply_filters( 'snax_get_formats', $formats );
	$order 	 = snax_get_formats_order();

	// Sort by user defined order.
	if ( ! empty( $order ) ) {
		if ( count( $order ) !== count( $formats ) ) {
			$order = array_unique( array_merge( $order, array_keys( $formats ) ) );
		}

		$sorted = array();

		foreach ( $order as $format_id ) {
			if ( isset( $formats[ $format_id ] ) ) {
				$sorted[ $format_id ] = $formats[ $format_id ];
			}
		}

		$formats = $sorted;
	} else {
		// Sort by position.
		uasort( $formats, 'snax_sort_formats_by_position' );
	}


	if ( 'object' === $fields ) {
		foreach ( $formats as $format_id => $format_args ) {
			$formats[ $format_id ] = new Snax_Format( $format_id, $format_args );
		}
	}

	return $formats;
}

/**
 * Return only active formats
 *
 * @return array
 */
function snax_get_active_formats( $fields = 'array' ) {
	$formats = snax_get_formats( $fields );
	$active_formats_ids = snax_get_active_formats_ids();

	foreach ( $formats as $format_id => $format_args ) {
		$active = in_array( $format_id, $active_formats_ids, true );

		// Format is active. Check if dependencies are fulfilled.
		if ( $active ) {
			switch( $format_id ) {
				case 'image':
				case 'gallery':
				case 'meme':
					$active = snax_is_image_upload_allowed();
					break;

				case 'audio':
					$active = snax_is_audio_upload_allowed();
					break;

				case 'video':
					$active = snax_is_video_upload_allowed();
					break;

				case 'extproduct':
					$active = snax_can_use_plugin( 'woocommerce/woocommerce.php' );
					break;
			}
		}

		if ( ! $active ) {
			unset( $formats[ $format_id ] );
		}
	}

	return $formats;
}

/**
 * Return number of active formats
 *
 * @return int
 */
function snax_get_format_count() {
	return count( snax_get_active_formats() );
}

/**
 * Check whether format is active
 *
 * @param string $format        Format id.
 *
 * @return bool
 */
function snax_is_active_format( $format ) {
	$formats = snax_get_active_formats();

	$is_active = (bool) isset( $formats[ $format ] );

	// List format is active if at least one of its types is active.
	if ( 'list' === $format && ! $is_active ) {
		$is_active = isset( $formats['ranked_list'] ) || isset( $formats['classic_list'] );
	}

	return $is_active;
}

function snax_is_format_disabled( $format ) {
	$is_disabled = false;

	switch( $format ) {
		case 'extproduct':
			$is_disabled = ! snax_can_use_plugin( 'woocommerce/woocommerce.php' );
			break;
	}

	return $is_disabled;
}

/**
 * Callback for uasort.
 *
 * @param array $a             First elemenet to compare.
 * @param array $b             Second elemenet to compare.
 *
 * @return integer
 */
function snax_sort_formats_by_position( $a, $b ) {
	if ( $a['position'] === $b['position'] ) {
		return 0;
	}

	return ( $a['position'] < $b['position']) ? -1 : 1;
}

/**
 * Whether the $post is one of allowed snax formats
 *
 * @param string      $format              Optional. Default is all formats.
 * @param int|WP_Post $post_id             Optional. Post ID or WP_Post object. Default is global `$post`.
 *
 * @return bool
 */
function snax_is_format( $format = null, $post_id = 0 ) {
	$is_format  = false;
	$post       = get_post( $post_id );

	if ( ! empty( $post ) ) {
		$post_format = snax_get_format( $post );
		// Check agains all formats.
		if ( null === $format ) {
			$formats = snax_get_formats();

			$is_format = isset( $formats[ $post_format ] );
			// Check if formats match.
		} else {
			$is_format = $format === $post_format;
		}
	}

	return apply_filters( 'snax_is_format', $is_format, $format, $post );
}

/**
 * Return current post format
 *
 * @param int|WP_Post $post_id             Optional. Post ID or WP_Post object. Default is global `$post`.
 *
 * @return mixed|void
 */
function snax_get_format( $post_id = 0 ) {
	$post = get_post( $post_id );
	$format = null;

	if ( $post ) {
		$format = snax_get_post_format( $post->ID );
	}

	return apply_filters( 'snax_get_format', $format, $post );
}

/**
 * Return generic format name (e.g. personality_quiz to quiz)
 *
 * @param string $format        Snax post format.
 *
 * @return string
 */
function snax_get_generic_format( $format ) {
	$generic = $format;

	switch ( $format ) {
		case 'ranked_list':
		case 'classic_list':
			$generic = 'list';
			break;

		case 'trivia_quiz':
		case 'personality_quiz':
			$generic = 'quiz';
			break;

		case 'classic_poll':
		case 'versus_poll':
		case 'binary_poll':
			$generic = 'poll';
			break;
	}


	return apply_filters( 'snax_generic_format', $generic );
}

/**
 * Return user uploaded image
 *
 * @param string $parent_format     Snax format.
 * @param int    $user_id           User id.
 * @param int    $post_id           Optional. Post id.
 *
 * @return WP_Post                  False if not exists.
 */
function snax_get_format_featured_image( $parent_format, $user_id, $post_id = 0 ) {
	if ( empty( $parent_format ) || empty( $user_id ) ) {
		return false;
	}

	$attachment = false;

	// Get post thumbnail (eg. if it's a draft).
	if ( $post_id ) {
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );

		if ( $post_thumbnail_id ) {
			$attachment = get_post( $post_thumbnail_id );
		}

		// Try to get orphan (post not exists yet).
	} else {
		$attachments = get_posts( array(
			'author' 			=> $user_id,
			'post_type' 		=> 'attachment',
			'meta_key' 			=> '_snax_featured_image_format',
			'meta_value'		=> $parent_format,
			'posts_per_page'	=> 1,
		) );

		if ( ! empty( $attachments ) ) {
			$attachment = $attachments[0];
		}
	}

	return $attachment;
}

/**
 * Make format featured image a regular attachment
 *
 * @param WP_Post $post         Post object or id.
 *
 * @return bool
 */
function snax_reset_format_featured_image( $post ) {
	$post = get_post( $post );

	delete_post_meta( $post->ID, '_snax_featured_image_format' );
}


/**
 * Set snax format to a post.
 *
 * @param int    $post_id  		Post id.
 * @param string $format_slug  	Format slug.
 */
function snax_set_post_format( $post_id, $format_slug ) {
	$format = get_term_by( 'slug', $format_slug, snax_get_snax_format_taxonomy_slug() );
	wp_set_post_terms( $post_id, $format->term_id, snax_get_snax_format_taxonomy_slug(), false );
}

/**
 * Get snax format of a post.
 *
 * @param int $post_id  		Post id.
 *
 * @return string
 */
function snax_get_post_format( $post_id ) {
	$result = wp_get_post_terms( $post_id, snax_get_snax_format_taxonomy_slug() );
	if ( empty( $result ) || ! is_array( $result ) ) {
		return false;
	} else {
		return $result[0]->slug;
	}
}

/**
 * Map legacy (global) option to a single format option
 *
 * @return string
 */
function snax_get_legacy_featured_media_required_setting() {
	// Get legacy global setting.
	$required =  ( 'standard' === apply_filters( 'snax_featured_media_required', get_option( 'snax_featured_media_required', 'none' ) ) );

	$default = $required ? 'required' : 'optional';

	return $default;

}

/**
 * Map legacy (global) option to a single format option
 *
 * @param string $format        Snax format id.
 *
 * @return bool                 Whether to show the media for a format
 */
function snax_get_legacy_show_featured_media_setting( $format ) {
	$show_for_formats = (array) get_option( 'snax_show_featured_images_for_formats', array( 'text', 'trivia_quiz', 'personality_quiz' ) );
	$show_for_formats =  apply_filters( 'snax_show_featured_images_for_formats', $show_for_formats );

	return in_array( $format, $show_for_formats ) ? 'standard' : 'none';
}

/**
 * Check whether the format requires media upload
 *
 * @param $format
 *
 * @return bool
 */
function snax_is_featured_media_field_required( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_featured_media_field', $format );

	$field = 'optional';

	if ( is_callable( $func_name ) ) {
		$field = call_user_func( $func_name );
	}

	return 'required' === $field;
}

/**
 * Check whether the format allows media upload
 *
 * @param $format
 *
 * @return bool
 */
function snax_is_featured_media_field_optional( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_featured_media_field', $format );

	$field = 'optional';

	if ( is_callable( $func_name ) ) {
		$field = call_user_func( $func_name );
	}

	return 'optional' === $field;
}

/**
 * Check whether the format has media upload disabled
 *
 * @param $format
 *
 * @return bool
 */
function snax_is_featured_media_field_disabled( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_featured_media_field', $format );

	$field = 'optional';

	if ( is_callable( $func_name ) ) {
		$field = call_user_func( $func_name );
	}

	return 'disabled' === $field;
}

/**
 * Check whether to show the format featured media on a singe post
 *
 * @param $format
 *
 * @return bool
 */
function snax_show_featured_media_on_single( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_show_featured_media', $format );

	$show = true;

	if ( is_callable( $func_name ) ) {
		$show = call_user_func( $func_name );
	}

	return $show;
}

/**
 * Map legacy (global) option to a single format option
 *
 * @return string
 */
function snax_get_legacy_category_required_setting() {
	$required = ( 'standard' === apply_filters( 'snax_category_required', get_option( 'snax_category_required', 'none' ) ) );

	$default = $required ? 'required' : 'optional';

	return $default;
}

/**
 * Check whether the format requires category
 *
 * @param $format
 *
 * @return bool
 */
function snax_is_category_field_required( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_category_field', $format );

	$field = 'optional';

	if ( is_callable( $func_name ) ) {
		$field = call_user_func( $func_name );
	}

	return 'required' === $field;
}

/**
 * Check whether the format allows category selection
 *
 * @param $format
 *
 * @return bool
 */
function snax_is_category_field_optional( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_category_field', $format );

	$field = 'optional';

	if ( is_callable( $func_name ) ) {
		$field = call_user_func( $func_name );
	}

	return 'optional' === $field;
}

/**
 * Check whether the format has category field disabled
 *
 * @param $format
 *
 * @return bool
 */
function snax_is_category_field_disabled( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_category_field', $format );

	$field = 'optional';

	if ( is_callable( $func_name ) ) {
		$field = call_user_func( $func_name );
	}

	return 'disabled' === $field;
}

/**
 * Map legacy (global) option to a single format option
 *
 * @return string
 */
function snax_get_legacy_category_multi_setting() {
	$allowed = ( 'on' === apply_filters( 'snax_multiple_categories_selection', get_option( 'snax_category_multi', '' ) ) );

	return $allowed ? 'standard' : 'none';
}

/**
 * Check whether the format allows multiple categories selection
 *
 * @param $format
 *
 * @return bool
 */
function snax_is_category_multi_allowed( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_multiple_categories_selection', $format );

	$allowed = false;

	if ( is_callable( $func_name ) ) {
		$allowed = call_user_func( $func_name );
	}

	return $allowed;
}

/**
 * Map legacy (global) option to a single format option
 *
 * @return string
 */
function snax_get_legacy_category_whitelist_setting() {
	return apply_filters( 'snax_category_whitelist', get_option( 'snax_category_whitelist', array( '' => '' ) ) );
}

/**
 * Return list of allowed categories for the format
 *
 * @param string $format        Snax post format.
 *
 * @return array
 */
function snax_get_category_whitelist( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_get_category_whitelist', $format );

	// Allow all by default.
	$list = array( '' => '' );

	if ( is_callable( $func_name ) ) {
		$list = call_user_func( $func_name );
	}

	return $list;
}

/**
 * Map legacy (global) option to a single format option
 *
 * @return string
 */
function snax_get_legacy_category_auto_assign_setting() {
	return (array) apply_filters( 'snax_category_auto_assign', get_option( 'snax_category_auto_assign', array( '' => '' ) ) );
}

/**
 * Return list of categories to auto assign during front end post creation
 *
 * @return array
 */
function snax_get_category_auto_assign( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_get_category_auto_assign', $format );

	// Not assign by default.
	$list = array( '' => '' );

	if ( is_callable( $func_name ) ) {
		$list = call_user_func( $func_name );
	}

	return $list;
}

/**
 * Map legacy (global) option to a single format option
 *
 * @return string
 */
function snax_get_legacy_referrals_setting() {
	$allowed = ( 'standard' === apply_filters( 'snax_allow_snax_authors_to_add_referrals', get_option( 'snax_allow_snax_authors_to_add_referrals', 'standard' ) ) );

	return $allowed ? 'standard' : 'none';
}

/**
 * Check whether the Referral field is allowed for the format
 *
 * @param string $format        Snax post format.
 *
 * @return bool
 */
function snax_allow_snax_authors_to_add_referrals( $format ) {
	$format = snax_get_generic_format( $format );

	$func_name = sprintf( 'snax_%s_allow_snax_authors_to_add_referrals', $format );

	// Allow by default.
	$allowed = true;

	if ( is_callable( $func_name ) ) {
		$allowed = call_user_func( $func_name );
	}

	return $allowed;
}




function snax_render_format_class( $r, $format_id ) {
	$final = array(
		'snax-format',
		'snax-format-' . $format_id,
	);

	$final = array_merge( $final, $r );
	$final = apply_filters( 'snax_get_format_class', $final, $format_id );

	echo 'class="' . implode( ' ', array_map( 'sanitize_html_class', $final ) ) . '"';
}