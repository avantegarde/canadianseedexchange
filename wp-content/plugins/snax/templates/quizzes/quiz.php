<?php
/**
 * Quiz template part
 *
 * @package snax 1.11
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>

<?php
$snax_quiz_class = array(
	'quiz',
);
$snax_quiz_class[] = snax_show_start_quiz_button() ? 'snax-quiz-with-start-trigger' : 'snax-quiz-without-start-trigger';
?>

<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_quiz_class ) ); ?>">

	<?php snax_get_template_part( 'quizzes/intro' ); ?>

	<?php snax_get_template_part( 'quizzes/loop-questions' ); ?>

	<?php snax_get_template_part( 'quizzes/pagination' ); ?>

	<div class="snax-quiz-results snax-quiz-results-hidden">
		<?php
		if ( snax_is_quiz_last_page() ) {
			if ( snax_share_to_unlock() ) {
				// User has to fist share to see results so share buttons can be loaded (and hidden) immediately
				// because there is no personalised data inside.
				snax_get_template_part( 'quizzes/locked-result' );
			} else {
				// When quiz ends and results are calculated on server's side and returned, with share buttons, on ajax call.
				// Ajax call doesn't allow us a way to load necessary dependencies so we have to load them here.
				wp_enqueue_script( 'snax-shares' );
			}
		}
		?>
	</div>

	<div class="snax-quiz-actions snax-quiz-actions-hidden">
		<?php if ( snax_show_play_again_button() ) : ?>

			<?php snax_get_template_part( 'quizzes/actions-end' ); ?>

		<?php endif; ?>
	</div>

	<?php if ( snax_one_question_per_page() && snax_is_quiz_last_page() ) : ?>

		<div class="snax-quiz-check-answers snax-quiz-check-answers-hidden">
			<h2><?php esc_html_e( 'Check your answers:', 'snax' ); ?></h2>

		</div>

	<?php endif; ?>

</div><!-- .quiz -->
