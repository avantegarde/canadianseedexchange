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
$snax_poll_class = array(
	'poll',
	'poll-binary',
	'snax-poll-without-start-trigger',
	'poll-binary-' . snax_get_poll_setting( 'answers_set' ),
	'poll-reveal-' . snax_get_poll_setting( 'reveal_correct_wrong_answers' ),
	'poll-pagination-' . snax_get_poll_setting( 'one_question_per_page' ),
);
?>

<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_poll_class ) ); ?>">

	<?php snax_get_template_part( 'polls/binary/loop-questions' ); ?>

	<?php snax_get_template_part( 'polls/pagination' ); ?>

</div><!-- .poll -->
