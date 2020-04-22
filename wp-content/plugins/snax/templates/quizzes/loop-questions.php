<?php do_action( 'snax_template_before_quiz_questions_loop' ); ?>

<?php
global $post;
$current_post = $post;
$snax_q_query = snax_get_questions_query();
$snax_questions_classes = array(
	'snax-quiz-questions-wrapper',
);
?>
<?php if ( $snax_q_query->have_posts() ) : ?>
	<?php
	if ( $snax_q_query->post_count === 1 ) {
		$snax_questions_classes[] = 'snax-quiz-single-question';
	}
	?>

	<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_questions_classes ) ); ?>">
		<ul class="snax-quiz-questions-items">

		<?php while ( $snax_q_query->have_posts() ) : $snax_q_query->the_post(); ?>
			<li class="snax-quiz-questions-item">

				<?php
				do_action( 'snax_before_quiz_question', get_post(), $snax_q_query->current_post );

				snax_get_template_part( 'quizzes/content-question' );

				do_action( 'snax_after_quiz_question', get_post(), $snax_q_query->current_post );
				?>

			</li>
		<?php endwhile; ?>
		</ul>
	</div>
<?php endif; ?>

<?php
$post = $current_post;
wp_reset_postdata();
?>

<?php do_action( 'snax_template_after_quiz_questions_loop' );
