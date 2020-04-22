<?php
/**
 * Poll template part
 *
 * @package snax 1.11
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>

<?php
$poll_type = get_post_meta( $post->ID, '_snax_poll_type', true );
$snax_poll_class = array(
	'poll',
	'poll-' . $poll_type,
);
$snax_poll_class[] = 'snax-poll-without-start-trigger';

if ( 'binary' === $poll_type ) {
	$snax_poll_class[] = 'poll-binary-' . snax_get_poll_setting( 'answers_set' );
}
$snax_poll_class[] = 'poll-reveal-' . snax_get_poll_setting( 'reveal_correct_wrong_answers' );
$snax_poll_class[] = 'poll-pagination-' . snax_get_poll_setting( 'one_question_per_page' );
?>

<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_poll_class ) ); ?>">

	<?php snax_get_template_part( 'polls/loop-questions' ); ?>

	<?php snax_get_template_part( 'polls/pagination' ); ?>

</div><!-- .poll -->
