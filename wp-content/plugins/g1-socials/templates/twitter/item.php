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

global $g1_socials_twitter_item;

if ( empty( $g1_socials_twitter_item ) ) {
	return;
}

$g1s_tweet    = $g1_socials_twitter_item;
$g1s_user_url = g1_socials_twitter_get_user_url( $g1s_tweet['user'] );
?>

<li class="g1-twitter-item">
	<div class="g1-tweet">
		<p class="g1-tweet-text">
			<?php 
			$content = $g1s_tweet['text'];
			$regex_url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
			$content = preg_replace( $regex_url, '<a href="$0" target="_blank" title="$0">$0</a>', $content );
			echo wp_kses_post( $content ); ?>
		</p>
		<p class="g1-meta g1-tweet-meta">
			<time class="entry-date" datetime="<?php echo esc_attr( g1_socials_twitter_get_tweet_formatted_datetime( $g1s_tweet['created_at'] ) ); ?>">
				<?php echo esc_html( g1_socials_twitter_get_tweet_formatted_date( $g1s_tweet['created_at'] ) ); ?>
			</time>
		</p>
		<p class="g1-tweet-intents">
			<?php g1_socials_twitter_render_reply_link( $g1s_tweet['id_str'] ); ?>
			<?php g1_socials_twitter_render_retweet_link( $g1s_tweet['id_str'], $g1s_tweet['retweet_count'] ); ?>
			<?php g1_socials_twitter_render_like_link( $g1s_tweet['id_str'] ); ?>
		</p>
	</div>
</li>
