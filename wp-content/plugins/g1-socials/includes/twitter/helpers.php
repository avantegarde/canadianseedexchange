<?php
/**
 * Twitter helpers
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package g1-socials
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Return user's account url
 *
 * @param array $user_arr       User data.
 *
 * @return string
 */
function g1_socials_twitter_get_user_url( $user_arr ) {
	$twitter_url = 'https://twitter.com';

	$user_url = sprintf( '%s/%s/', $twitter_url, $user_arr['screen_name'] );

	return apply_filters( 'g1_socials_twitter_user_url', $user_url, $user_arr );
}

/**
 * Return user's profile image url
 *
 * @param array $user_arr       User data.
 *
 * @return string
 */
function g1_socials_twitter_get_user_profile_image_url( $user_arr ) {
	return is_ssl() ? $user_arr['profile_image_url_https'] : $user_arr['profile_image_url'];
}

/**
 * Return formatted date
 *
 * @param string $date_str      Date string.
 *
 * @return string
 */
function g1_socials_twitter_get_tweet_formatted_date( $date_str ) {
	$timestamp = strtotime( $date_str );

	$date = date( get_option( 'date_format' ), $timestamp );
	$time = date( get_option( 'time_format' ), $timestamp );
	$sep  = $time ? apply_filters( 'g1_socials_date_time_separator', ', ' ) : '';

	return $date . $sep . $time;
}

/**
 * Return formatted datetime
 *
 * @param string $date_str      Date string.
 *
 * @return string
 */
function g1_socials_twitter_get_tweet_formatted_datetime( $date_str ) {
	$timestamp = strtotime( $date_str );

	$date = date( 'Y-m-d', $timestamp );
	$time = date( 'H:i:s', $timestamp );

	return $date . 'T' . $time;
}

/**
 * Render Twitter Reply link
 *
 * @param string $tweet_id     Tweet id.
 */
function g1_socials_twitter_render_reply_link( $tweet_id ) {
	$url = sprintf( apply_filters( 'g1_socials_twitter_tweet_intent_url', 'https://twitter.com/intent/tweet?in_reply_to=%s' ), $tweet_id );
	?>
	<a class="g1-tweet-action g1-tweet-reply" title="<?php esc_html_e( 'Reply', 'g1_socials' ); ?>" onclick="<?php echo esc_attr( g1_socials_twitter_new_window_js( $url ) ); ?>" href="<?php echo esc_url( $url ); ?>">
		<?php esc_html_e( 'Reply', 'g1_socials' ); ?>
	</a>
	<?php
}
/**
 * Render Twitter Retweet link
 *
 * @param string $tweet_id          Tweet id.
 * @param int    $retweet_count     Retweet count.
 */
function g1_socials_twitter_render_retweet_link( $tweet_id, $retweet_count = 0 ) {
	$url = sprintf( apply_filters( 'g1_socials_twitter_retweet_intent_url', 'https://twitter.com/intent/retweet?tweet_id=%s' ), $tweet_id );
	?>
	<a class="g1-tweet-action g1-tweet-retweet" title="<?php esc_html_e( 'Retweet', 'g1_socials' ); ?>" onclick="<?php echo esc_attr( g1_socials_twitter_new_window_js( $url ) ); ?>" href="<?php echo esc_url( $url ); ?>">
		<?php if ( apply_filters( 'g1_socials_twitter_show_retweet_count', $retweet_count > 0, $retweet_count ) ) : ?>
			<span><?php echo esc_html( $retweet_count ); ?></span>
		<?php endif ?>

		<span><?php esc_html_e( 'Retweet', 'g1_socials' ); ?></span>
	</a>
	<?php
}

function g1_socials_twitter_render_like_link( $tweet_id ) {
	$url = sprintf( apply_filters( 'g1_socials_twitter_like_intent_url', 'https://twitter.com/intent/like?tweet_id=%s' ), $tweet_id );
	?>
	<a class="g1-tweet-action g1-tweet-like" title="<?php esc_html_e( 'Like', 'g1_socials' ); ?>" onclick="<?php echo esc_attr( g1_socials_twitter_new_window_js( $url ) ); ?>" href="<?php echo esc_url( $url ); ?>">
		<?php esc_html_e( 'Like', 'g1_socials' ); ?>
	</a>
	<?php
}

/**
 * Return js code to open a new window
 *
 * @param string $url       Window url.
 *
 * @return string
 */
function g1_socials_twitter_new_window_js( $url ) {
	$name   = _x( 'Twitter', 'Twitter Popup Window', 'g1_socials' );
	$width  = 600;
	$height = 350;

	$js = sprintf( "window.open('%s', '%s', 'width=%d,height=%d,left='+(screen.availWidth/2-%d)+',top='+(screen.availHeight/2-%d)+''); return false;", $url, $name, $width, $height, $width / 2, $height / 2 );

	return $js;
}
