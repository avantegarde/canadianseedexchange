<?php
/**
 * Twitter item template part
 *
 * @package g1-socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// HTML class attribute.
$html_classes = array(
	'g1-widget-twitter',
);

?>
<div id="<?php echo esc_attr( $g1_twitter_html_id ); ?>" class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $html_classes ) ); ?>">
	<p>
		<?php
		if ( $g1_twitter_this->is_widget_configured( $g1_twitter_instance ) ) :
			$tweets = $g1_twitter_this->get_tweets( $g1_twitter_instance );
			if ( ! is_wp_error( $tweets ) ) :
				global $g1_socials_twitter;
				$g1_socials_twitter = array(
					'tweets'     => $tweets,
					'max'        => min( count( $tweets ), $g1_twitter_instance['tweets_to_show'] ),
					'updated_at' => date( 'Y-m-d H:i:s', $g1_twitter_this->get_last_update_time() ),
				);
				g1_socials_get_template_part( 'twitter/collection' );
			else :
				echo esc_html( $tweets->get_error_message() );
			endif;
		else :
			printf( esc_html__( 'Please fill the username and %s.', 'g1_socials' ), '<a href="' . esc_url( $g1_twitter_this->get_twitter_settings_url() ) . '" target="_blank">' . esc_html__( 'Twitter access keys and tokens', 'g1_socials' ) . '</a>' );
		endif;
		?>
	</p>
</div>
