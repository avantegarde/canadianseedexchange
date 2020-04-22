<?php
/**
 * Poll settings template part
 *
 * @package snax 1.11
 * @subpackage Forms
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$snax_allow_guests_to_play		    = snax_get_poll_setting( 'allow_guests_to_play' );
$snax_vote_limit 				    = snax_get_poll_setting( 'vote_limit' );
$snax_reveal_correct_wrong_answers 	= snax_get_poll_setting( 'reveal_correct_wrong_answers' );
$snax_one_question_per_page 		= snax_get_poll_setting( 'one_question_per_page' );
$snax_shuffle_questions 			= snax_get_poll_setting( 'shuffle_questions' );
$snax_questions_per_poll 			= snax_get_poll_setting( 'questions_per_poll' );
$snax_shuffle_answers 				= snax_get_poll_setting( 'shuffle_answers' );
?>

<table class="form-table">
	<tbody>
        <!-- Guests can play? -->
        <tr>
            <th>
                <label for="snax_vote_limit">
                    <?php esc_html_e( 'Allow guests to play', 'snax' ); ?>
                </label>
            </th>
            <td>
                <select id="snax_allow_guests_to_play" name="snax_allow_guests_to_play">
                    <option value="standard"<?php selected( $snax_allow_guests_to_play, 'standard' ); ?>><?php esc_html_e( 'yes', 'snax' ); ?></option>
                    <option value="none"<?php selected( $snax_allow_guests_to_play, 'none' ); ?>><?php esc_html_e( 'no', 'snax' ); ?></option>
                </select>
            </td>
        </tr>
        <!-- Vote limit -->
        <tr>
            <th>
                <label for="snax_vote_limit">
                    <?php esc_html_e( 'User can play', 'snax' ); ?>
                </label>
            </th>
            <td>
                <select id="snax_vote_limit" name="snax_vote_limit">
                    <option value="1"<?php selected( $snax_vote_limit, '1' ); ?>><?php esc_html_e( 'just once', 'snax' ); ?></option>
                    <option value="-1"<?php selected( $snax_vote_limit, '-1' ); ?>><?php esc_html_e( 'many times', 'snax' ); ?></option>
                </select>
            </td>
        </tr>
		<!-- Reveal results -->
		<tr>
			<th>
				<label for="snax_reveal_correct_wrong_answers">
					<?php esc_html_e( 'Show results', 'snax' ); ?>
				</label>
			</th>
			<td>
				<select id="snax_reveal_correct_wrong_answers" name="snax_reveal_correct_wrong_answers">
					<option value="immediately"<?php selected( $snax_reveal_correct_wrong_answers, 'immediately' ); ?>><?php esc_html_e( 'right after user answers a question', 'snax' ); ?></option>
					<option value="poll-end"<?php selected( $snax_reveal_correct_wrong_answers, 'poll-end' ); ?>><?php esc_html_e( 'at the end of the poll', 'snax' ); ?></option>
				</select>
			</td>
		</tr>
		<!-- One question per page? -->
		<tr>
			<th>
				<label for="snax_one_question_per_page">
					<?php esc_html_e( 'One question per page?', 'snax' ); ?>
				</label>
			</th>
			<td>
				<select id="snax_one_question_per_page" name="snax_one_question_per_page">
					<option value="standard"<?php selected( $snax_one_question_per_page, 'standard' ); ?>><?php esc_html_e( 'yes', 'snax' ); ?></option>
					<option value="none"<?php selected( $snax_one_question_per_page, 'none' ); ?>><?php esc_html_e( 'no', 'snax' ); ?></option>
				</select>
			</td>
		</tr>

		<!-- Shuffle questions? -->
		<tr>
			<th>
				<label for="snax_shuffle_questions">
					<?php esc_html_e( 'Shuffle questions?', 'snax' ); ?>
				</label>
			</th>
			<td>
				<select id="snax_shuffle_questions" name="snax_shuffle_questions">
					<option value="standard"<?php selected( $snax_shuffle_questions, 'standard' ); ?>><?php esc_html_e( 'yes', 'snax' ); ?></option>
					<option value="none"<?php selected( $snax_shuffle_questions, 'none' ); ?>><?php esc_html_e( 'no', 'snax' ); ?></option>
				</select>
			</td>
		</tr>

		<!-- Questions per poll -->
		<tr>
			<th>
				<label for="snax_questions_per_poll">
					<?php esc_html_e( 'Questions per poll', 'snax' ); ?>
				</label>
			</th>
			<td>
				<input type="number" min="0" id="snax_questions_per_poll" name="snax_questions_per_poll" value="<?php echo esc_attr( $snax_questions_per_poll ); ?>" />
				<p class="description">
					<?php esc_html_e( 'Leave empty to show all available questions.', 'snax' ); ?>
					<?php esc_html_e( 'Works only with the "Shuffle questions" options enabled.', 'snax' ); ?>
				</p>
			</td>
		</tr>


		<!-- Shuffle answers? -->
		<tr>
			<th>
				<label for="snax_shuffle_answers">
					<?php esc_html_e( 'Shuffle answers?', 'snax' ); ?>
				</label>
			</th>
			<td>
				<select id="snax_shuffle_answers" name="snax_shuffle_answers">
					<option value="standard"<?php selected( $snax_shuffle_answers, 'standard' ); ?>><?php esc_html_e( 'yes', 'snax' ); ?></option>
					<option value="none"<?php selected( $snax_shuffle_answers, 'none' ); ?>><?php esc_html_e( 'no', 'snax' ); ?></option>
				</select>
			</td>
		</tr>
	</tbody>
</table>


