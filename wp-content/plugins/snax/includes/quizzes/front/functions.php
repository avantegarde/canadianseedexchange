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
function snax_quiz_enqueue_scripts() {
	if ( ! is_singular( snax_get_quiz_post_type() ) ) {
		return;
	}

	$url = trailingslashit( snax_get_assets_url() );

	wp_enqueue_script( 'snax-quiz', $url . 'js/quiz.js', array( 'jquery' ), snax_get_version(), true );

	global $page;

	$config = array(
		'ajax_url'          			=> admin_url( 'admin-ajax.php' ),
		'debug'							=> snax_in_debug_mode(),
		'quiz_id'						=> get_the_ID(),
		'quiz_canonical_url'			=> get_permalink( get_the_ID() ),
		'all_questions'					=> snax_get_questions_count(),
		'questions_answers_arr'			=> snax_get_questions_answers(),
		'page'							=> $page,
		'reveal_correct_wrong_answers'	=> snax_reveal_correct_wrong_answers(),
		'one_question_per_page' 		=> snax_one_question_per_page(),
		'shuffle_questions' 			=> snax_shuffle_questions(),
		'questions_per_quiz'			=> snax_get_questions_per_quiz(),
		'shuffle_answers' 				=> snax_shuffle_answers(),
		'next_question_reload'			=> snax_next_question_reload(),
		'share_to_unlock'				=> snax_share_to_unlock(),
		'share_description'				=> snax_get_share_description(),
	);

	$config = apply_filters( 'snax_quiz_config', $config );

	wp_localize_script( 'snax-quiz', 'snax_quiz_config', wp_json_encode( $config ) );
}



/**
 * Load CSS.
 */
function snax_quiz_enqueue_styles() {
	if ( ! is_singular( snax_get_quiz_post_type() ) ) {
		return;
	}

	$uri = trailingslashit( snax()->css_url );

	wp_enqueue_style( 'snax-single-quiz', $uri . 'snax-single-quiz.min.css', array( 'snax' ), snax()->version );
	wp_style_add_data( 'snax-single-quiz', 'rtl', 'replace' );
	wp_style_add_data( 'snax-single-quiz', 'suffix', '.min' );
}



/**
 * Render quiz
 *
 * @param string $content		Post content.
 *
 * @return string
 */
function snax_render_quiz( $content ) {
    if ( ! apply_filters( 'snax_render_quiz', true ) ) {
        return $content;
    }

	$shortcode = '';
	if ( strpos( $content, '[snax_content]' ) > -1 ) {
		$shortcode = '[snax_content]';
		$content = str_replace( '[snax_content]', '', $content );
	}
	if ( is_singular( snax_get_quiz_post_type() ) ) {
		ob_start();

		echo '<div class="snax">';
		if ( ! is_user_logged_in() && ! snax_quiz_allow_guests_to_play() ) {
			snax_get_template_part( 'quizzes/quiz-cta' );
		} else {
			snax_get_template_part( 'quizzes/quiz' );
		}
		echo '</div>';

		$content .= ob_get_clean();
	}
	$content .= $shortcode;

	return $content;
}

/**
 * Generate quiz pagination using built-in WP page links
 *
 * @param array    $posts           Array of posts.
 * @param WP_Query $wp_query        WP Query.
 *
 * @return array
 */
function snax_generate_quiz_pagination( $posts, $wp_query ) {
    if ( ! apply_filters( 'snax_render_quiz', true ) ) {
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

		if ( ! snax_is_quiz( $post ) ) {
			continue;
		}

		// We don't need pagination if all questions are displated on a page at once.
		if ( ! snax_one_question_per_page( $post ) ) {
			continue;
		}

		$pages = snax_get_questions_count( $post );

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
 * Render quiz question featured media
 *
 * @param array       $args   Arguments.
 */
function snax_render_quiz_question_featured_media( $args = array() ) {
	?>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="snax-quiz-question-media">
			<?php
			add_filter( 'wp_get_attachment_image_src', 'snax_fix_animated_gif_image', 99, 4 );
			the_post_thumbnail( 'post-thumbnail', array( 'class' => 'g1-disable-gif-player' ) );
			remove_filter( 'wp_get_attachment_image_src', 'snax_fix_animated_gif_image', 99, 4 );
			?>
		</figure>
	<?php endif; ?>
	<?php
}
