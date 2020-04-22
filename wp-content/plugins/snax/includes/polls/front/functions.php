<?php
/**
 * Front Functions
 *
 * @package snax
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Load javascripts.
 */
function snax_poll_enqueue_scripts() {
	if ( ! is_singular( snax_get_poll_post_type() ) ) {
		return;
	}

	$url = trailingslashit( snax_get_assets_url() );

	wp_enqueue_script( 'snax-poll', $url . 'js/poll.js', array( 'jquery' ), snax_get_version(), true );

	global $page;

	$config = array(
		'ajax_url'                     => admin_url( 'admin-ajax.php' ),
		'debug'                        => snax_in_debug_mode(),
		'poll_id'                      => get_the_ID(),
		'poll_type'                    => snax_get_poll_type( get_the_ID() ),
		'author_id'                    => get_current_user_id(),
		'all_questions'                => snax_get_poll_questions_count(),
		'questions_answers_arr'        => snax_get_poll_questions_answers(),
		'page'                         => $page,
		'reveal_correct_wrong_answers' => snax_poll_reveal_correct_wrong_answers(),
		'one_question_per_page'        => snax_poll_one_question_per_page(),
		'shuffle_questions'            => snax_poll_shuffle_questions(),
		'questions_per_poll'           => snax_get_questions_per_poll(),
		'shuffle_answers'              => snax_poll_shuffle_answers(),
		'next_question_reload'         => snax_poll_next_question_reload(),
		'share_description'            => snax_get_poll_share_description(),
		'one_vote_per_user'            => snax_poll_vote_limit(),
		'user_votes_expire_time'       => apply_filters( 'snax_poll_user_votes_expire_time', 0 ),
		'i18n'                         => array(
			'votes' => __( 'votes', 'snax' ),
			'k'     => __( 'k', 'snax' ),
			'share' => __( 'Share your vote', 'snax' ),
		),
	);

	$config = apply_filters( 'snax_poll_config', $config );

	wp_localize_script( 'snax-poll', 'snax_poll_config', wp_json_encode( $config ) );
}



/**
 * Load CSS.
 */
function snax_poll_enqueue_styles() {
	if ( ! is_singular( snax_get_poll_post_type() ) ) {
		return;
	}

	$uri = trailingslashit( snax()->css_url );

	wp_enqueue_style( 'snax-single-poll', $uri . 'snax-single-poll.min.css', array( 'snax' ), snax()->version );
	wp_style_add_data( 'snax-single-poll', 'rtl', 'replace' );
	wp_style_add_data( 'snax-single-poll', 'suffix', '.min' );
}



/**
 * Render poll
 *
 * @param string $content		Post content.
 *
 * @return string
 */
function snax_render_poll( $content ) {
    if ( ! apply_filters( 'snax_render_poll', true ) ) {
        return $content;
    }

	global $post;

	$shortcode = '';
	if ( strpos( $content, '[snax_content]' ) > -1 ) {
		$shortcode = '[snax_content]';
		$content = str_replace( '[snax_content]', '', $content );
	}
	if ( is_singular( snax_get_poll_post_type() ) ) {
		ob_start();

		echo '<div class="snax">';
		if ( ! is_user_logged_in() && ! snax_poll_allow_guests_to_play( $post ) ) {
			snax_get_template_part( 'polls/poll-cta' );
		} else {
			$poll_type = get_post_meta( $post->ID, '_snax_poll_type', true );

			// Strip unwanted characters
			$poll_type = preg_replace("/[^A-Za-z0-9_\-]/", "", $poll_type );
			snax_get_template_part( 'polls/' . $poll_type . '/content-poll' );
		}
		echo '</div>';

		$content .= ob_get_clean();
	}
	$content .= $shortcode;

	return $content;
}

/**
 * Generate poll pagination using built-in WP page links
 *
 * @param array    $posts           Array of posts.
 * @param WP_Query $wp_query        WP Query.
 *
 * @return array
 */
function snax_generate_poll_pagination( $posts, $wp_query ) {
    if ( ! apply_filters( 'snax_render_poll', true ) ) {
        return $posts;
    }

	/**
	 * Check if query is an instance of WP_Query.
	 * Some plugins, like BuddyPress may change it.
	 */
	if ( ! ( $wp_query instanceof WP_Query ) ) {
		return $posts;
	}

	// Apply only for the_content on a single post.
	if ( ! ( $wp_query->is_main_query() && $wp_query->is_singular() ) ) {
		return $posts;
	}

	foreach ( $posts as $post ) {

		if ( ! snax_is_poll( $post ) ) {
			continue;
		}

		// We don't need pagination if all questions are displated on a page at once.
		if ( ! snax_poll_one_question_per_page( $post ) ) {
			continue;
		}

		$pages = snax_get_poll_questions_count( $post );

		if ( $pages < 2 ) {
			continue;
		}

		// WP skips <!--nextpage--> quick tag if it's placed at the beggining of a post.
		// So if post content is empty we need to add one extra quick tag as a workaround.
		if ( empty( $post->post_content ) ) {
			$post->post_content .= '<!--nextpage-->';
		}

		// The <!--nextpage--> tag is a divider between two pages. Number of dividers = pages - 1.
		$post->post_content .= str_repeat( '<!--nextpage-->', $pages - 1 );
	}

	return $posts;
}



/**
 * Render poll question featured media
 *
 * @param array       $args   Arguments.
 */
function snax_render_poll_question_featured_media( $args = array() ) {
	global $post;

	$args = wp_parse_args( $args, array(
		'size' => 'post-thumbnail',
	) );

	$poll_type = snax_get_poll_type( $post->post_parent );
	?>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="snax-poll-question-media">
			<?php
			add_filter( 'wp_get_attachment_image_src', 'snax_fix_animated_gif_image', 99, 4 );

			the_post_thumbnail( $args['size'], array( 'class' => 'g1-disable-gif-player' ) );

			remove_filter( 'wp_get_attachment_image_src', 'snax_fix_animated_gif_image', 99, 4 );
			?>
			<?php if( 'binary' === $poll_type ) : ?>
				<div class="snax-poll-anticipation">
					<div></div>
					<div></div>
				</div>
			<?php endif;?>
		</figure>
	<?php endif; ?>
	<?php
}
