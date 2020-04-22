<?php
global $snax_question_id;
$snax_question_id = get_the_ID();

$snax_poll_id = wp_get_post_parent_id( get_the_ID() );
$snax_poll_type = snax_get_poll_type( $snax_poll_id );
$snax_question_class = array(
	'snax-poll-question',
	'snax-poll-question-' . get_the_ID(),
	'snax-poll-question-hidden',
	'snax-poll-question-unanswered',
	'snax-poll-question-title-' . ( snax_get_poll_title_hide() ? 'hide' : 'show' ),
	'snax-poll-question-answer-title-' . ( snax_get_poll_answers_labels_hide() ? 'hide' : 'show' ),
);
?>

<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_question_class ) ); ?>" data-quizzard-question-id="<?php echo absint( get_the_ID() ); ?>">
	<?php if ( 1 !== snax_get_poll_questions_count( $snax_poll_id ) ) : ?>
		<span class="snax-poll-question-xofy">
		<?php
			echo str_replace(
				array(
					'%1$d',
					'%2$d',
				),
				array(
					'<span class="snax-poll-question-xofy-x"></span>',
					'<span class="snax-poll-question-xofy-y"></span>',
				),
				esc_html__( 'Question %1$d of %2$d', 'snax' )
			);
		?>
	</span>
		<span class="snax-poll-question-progress"><span class="snax-poll-question-progress-bar"></span></span>
	<?php endif; ?>
	<?php the_title( '<h2 class="snax-poll-question-title">', '</h2>' ); ?>

	<?php snax_render_poll_question_featured_media(); ?>

	<?php snax_get_template_part('polls/loop-answers' ); ?>

    <?php snax_get_template_part('polls/share-links' ); ?>

    <?php snax_get_template_part('polls/view-results-link' ); ?>

</div><!-- .snax-poll-question -->
